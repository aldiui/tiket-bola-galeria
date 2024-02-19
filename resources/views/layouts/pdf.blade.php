<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        h3 {
            margin: 2;
        }
    </style>
    @stack('style')
</head>
</head>

<body>
    <div align="center">
        <h3>@yield('title') </h3>
    </div>
    <hr style="height:1px;background-color:black;">
    <br>
    @yield('main')


    @stack('scripts')

</body>

</html>
