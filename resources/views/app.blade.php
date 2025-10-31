<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $page ?? config('app.name', 'Laravel') }}</title>

    <style>
        html {
            background-color: oklch(1 0 0);
        }
        html.dark {
            background-color: oklch(0.145 0 0);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <h1>{{ $page }}</h1>
    <p>Selamat datang di aplikasi Laravel tanpa React!</p>
</body>
</html>
