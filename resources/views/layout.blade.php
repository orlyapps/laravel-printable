<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <base href="{{ config('app.url') }}">

    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        {!! file_get_contents(config('printable.tailwindConfig')) !!}
    </script>
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
            padding: 50mm 22mm 40mm 22mm;
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

    </style>

    @yield('header')


</head>

<body class="text-black font-sans text-base">
    <div id="app">
        @yield('content')
    </div>
</body>


</html>
