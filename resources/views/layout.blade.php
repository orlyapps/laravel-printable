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


    @yield('header')


</head>

<body class="text-black font-sans text-base">
    <div id="app">
        @yield('content')
    </div>
</body>


</html>
