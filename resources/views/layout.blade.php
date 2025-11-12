<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Laravel AJAX App')</title>
  @vite(['resources/css/style.css', 'resources/js/app.js'])
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
  <div class="container">
    <header>
      <h1>Laravel AJAX App</h1>
      <nav>
        <a href="/products">Produk</a>
        <a href="/categories">Kategori</a>
        <a href="/pembelian/form">Pembelian</a>
      </nav>
    </header>

    @yield('content')
  </div>

  @stack('scripts')
</body>
</html>
