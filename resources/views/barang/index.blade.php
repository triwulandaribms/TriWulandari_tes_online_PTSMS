@extends('layout')

@section('content')
<h3>Daftar Barang</h3>
<a href="/barang/form">Tambah Barang</a>
<table border="1" width="100%">
  <thead>
    <tr><th>Kode</th><th>Nama</th><th>Harga</th><th>Aksi</th></tr>
  </thead>
  <tbody id="barangTable"></tbody>
</table>
@endsection

@push('scripts')
<script src="{{ asset('js/barang.js') }}"></script>
@endpush
