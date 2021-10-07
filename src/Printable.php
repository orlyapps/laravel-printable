<?php

namespace Orlyapps\Printable;

trait Printable
{
    public function stationeryPdf()
    {
        return resource_path('pdf/stationery.pdf');
    }

    public function printView()
    {
        return null;
    }

    public function print()
    {
        return new PrintModel($this, $this->stationeryPdf());
    }
}
