@if ($orientation === 'portrait')

    @section('header')
        <style>
            body {
                background-color: rgba(75, 85, 99, 1);
            }

            .page {
                background: white;
                width: 21cm;
                display: block;
                margin: 20px auto;
                display: flex;
                min-height: 29.68cm;
                flex-direction: column;
                padding: {{ $margin ?? '51mm 20mm 45mm 22mm' }};
            }

            .page.landscape {
                background: white;
                min-height: 21cm;
                display: block;
                margin: 20px auto;
                display: flex;
                width: 29.68cm;
                flex-direction: column;
                padding: 20mm 10mm 20mm 10mm;
            }

            .break-inside-avoid {
                page-break-inside: avoid;
            }

            page-break {
                page-break-before: always;
                display: block;
            }

            @media print {
                html {
                    background: transparent;
                }

                body,
                .page {
                    background: transparent;
                    margin: 0;
                    box-shadow: 0;
                    padding: 0;
                    min-height: auto;
                    page-break-after: always;
                    width: auto;
                    padding: 0px;
                }

                .page.landscape {
                    padding: 0px;
                }
            }

            @page {
                size: A4;
                margin: {{ $margin ?? '45mm 20mm 22mm 22mm' }};
            }

            @page :first {
                size: A4;
                margin: {{ $margin ?? '51mm 20mm 45mm 22mm' }};
            }
        </style>
    @endsection
@else
    @section('header')
        <style>
            @page {
                size: A4 landscape;
                margin: {{ $margin ?? '20mm 10mm 20mm 10mm' }};
            }
        </style>
    @endsection
@endif

<div class="page  {{ $orientation }}">
    <main>
        {{ $slot }}
    </main>
</div>
