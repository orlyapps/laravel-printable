<?php

namespace Orlyapps\Printable;

use Gotenberg\Gotenberg;
use Illuminate\Support\Facades\Context;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;
use Wnx\SidecarBrowsershot\BrowsershotLambda;
use Gotenberg\Stream;

class PrintModel
{
    public $user = null;

    public $meta = [];

    public $model = null;

    public $layout = 'default';

    public $watermark = false;

    public $stationery = true;

    public $numberOfPages = true;

    public $stationeryPdf = null;

    public $modelShortName = null;

    /**
     * Enable PDF/A1-b compliance
     */
    public $PDFA = false;

    public function __construct($model, $stationeryPdf)
    {
        $this->stationeryPdf = $stationeryPdf;
        $this->model = $model;
        $reflection = new \ReflectionClass($model);
        $this->modelShortName = \Str::camel($reflection->getShortName());
        $this->user = \Auth::user();

        return $this;
    }

    public function enablePDFaCompliance($PDFA)
    {
        $this->PDFA = $PDFA;

        return $this;
    }

    public function withNumberOfPages($numberOfPages)
    {
        $this->numberOfPages = $numberOfPages;

        return $this;
    }

    public function user($user)
    {
        $this->user = $user;

        return $this;
    }

    public function layout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    public function withWatermark($watermark)
    {
        $this->watermark = $watermark;

        return $this;
    }

    public function withMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function withStationery($stationery)
    {
        $this->stationery = $stationery;

        return $this;
    }

    public function asHTML()
    {
        $view = $this->model->printView() ?? "print.{$this->modelShortName}.layout";
        $templateName = "{$view}-{$this->layout}";

        Context::add('printContext', $this->model->printContext());

        return  (string) view($templateName, array_merge([
            $this->modelShortName => $this->model,
            'model' => $this->model,
            'user' => $this->user,
        ], $this->meta, $this->model->printContext()));
    }

    public function save()
    {
        if (! is_dir(storage_path('printable'))) {
            mkdir(storage_path('printable'));
        }

        $filename = storage_path('printable/'.uniqid(rand(), true).'.pdf');
        $templateString = $this->asHTML();

        $filename = match($this->model->driver()) {
            'gotenberg' => $this->saveWithGotenberg($templateString),
            'lambda' => $this->saveWithLambda($templateString),
            'browsershot' => $this->saveWithBrowsershot($templateString),
            default => throw new \Exception('Invalid print driver specified'),
        };

        $filename = $this->applyStationeryAndWatermark($filename);

        if ($this->PDFA) {
            return $this->convertToPDFa($filename);
        }

        return $filename;
    }

    protected function saveWithGotenberg($templateString)
    {
        $url = config('printable.gotenberg.url');
        $username = config('printable.gotenberg.username');
        $password = config('printable.gotenberg.password');

        $filename =  Gotenberg::save(
            Gotenberg::chromium("{$username}:{$password}@" . trim($url, '/') . '/')
                ->pdf()
                ->margins('0mm', '0mm', '0mm', '0mm')
                ->paperSize('210mm', '297mm')
                ->html(Stream::string('index.html', $templateString)),
            storage_path('printable')
        );
        return storage_path('printable/'.basename($filename));
    }

    protected function saveWithLambda($templateString)
    {
        $shot = BrowsershotLambda::html($templateString);
        return $this->configureBrowsershot($shot);
    }

    protected function saveWithBrowsershot($templateString)
    {
        $shot = Browsershot::html($templateString);
        return $this->configureBrowsershot($shot);
    }

    protected function configureBrowsershot($shot)
    {
        $filename = storage_path('printable/'.uniqid(rand(), true).'.pdf');

        $shot->showBrowserHeaderAndFooter()
            ->hideFooter()
            ->hideHeader()
            ->showBackground()
            ->emulateMedia('print')
            ->paperSize(210, 297, 'mm')
            ->margins(0, 0, 0, 0, 'mm')
            ->setOption('args', '--lang=de-DE')
            ->noSandbox()
            ->waitUntilNetworkIdle(false)
            ->delay(2000)
            ->timeout(60);

        $shot = $this->model->browsershot($shot);

        if ($this->numberOfPages) {
            $shot->headerHtml('<div style="padding-top:15mm;padding-right: 15mm;color: #718096;font-size:12px;text-align:right;width:100%"><span  class="pageNumber"></span> / <span class="totalPages"></span> </div>');
        }

        $shot->savePdf($filename);
        return $filename;
    }

    protected function applyStationeryAndWatermark($sourceFilename)
    {
        $pdf = new Fpdi();
        $pdf->setSourceFile(StreamReader::createByString(file_get_contents($this->stationeryPdf)));

        $coverBackground = $pdf->importPage(1);
        $pageBackground = null;
        try {
            $pageBackground = $pdf->importPage(2);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $pdf->setSourceFile(resource_path('pdf/watermark-preview.pdf'));
        $watermarkPage = $pdf->importPage(1);

        $pageCount = $pdf->setSourceFile($sourceFilename);
        for ($i = 1; $i <= $pageCount; $i++) {
            $pdf->AddPage();
            $template = ($i === 1 || $pageBackground === null) ? $coverBackground : $pageBackground;

            $pageId = $pdf->importPage($i);
            $pdf->useTemplate($pageId);

            if ($this->stationery) {
                $pdf->useTemplate($template);
            }

            if ($this->watermark) {
                $pdf->useTemplate($watermarkPage);
            }

            if ($i !== 1 && optional($this->model)->printTitle) {
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetTextColor(74, 85, 104);
                $pdf->SetXY(20.8, 37);
                $pdf->Write(0, iconv('UTF-8', 'ISO-8859-1//IGNORE', $this->model->printTitle));
            }
        }

        unlink($sourceFilename);
        $filename = storage_path('printable/'.uniqid(rand(), true).'.pdf');
        $pdf->Output('F', $filename);

        return $filename;
    }

    protected function convertToPDFa($filename)
    {
        $outputFile = storage_path('temp/'.uniqid(rand(), true).'.pdf');
        $process = new Process([
            'gt',
            '-dNOSAFER',
            '-dPDFA',
            '-dBATCH',
            '-dNOPAUSE',
            '-sProcessColorModel=DeviceRGB',
            '-sDEVICE=pdfwrite',
            '-dPDFACompatibilityPolicy=1',
            '-sOutputFile='.$outputFile,
            '-sOutputICCProfile='.resource_path('pdf/AdobeRGB1998.icc'),
            '-dPDFACompatibilityPolicy=1',
            $filename,
        ]);

        $process->mustRun();

        return $outputFile;
    }
}
