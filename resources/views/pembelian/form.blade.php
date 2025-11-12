@extends('layout')

@section('content')
<h3>Tambah Pembelian</h3>

<form id="pembelianForm">
  <label>Tanggal:</label>
  <input type="date" id="tanggal" required><br><br>

  <label>Keterangan:</label>
  <input type="text" id="keterangan" placeholder="Keterangan pembelian" required><br><br>

  <h4>Detail Barang</h4>
  <table border="1" width="100%">
    <thead>
      <tr><th>Barang</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th>Aksi</th></tr>
    </thead>
    <tbody id="barangRows"></tbody>
  </table>
  <button type="button" id="addBarangBtn">+ Tambah Barang</button>

  <h4 style="margin-top: 1rem;">Total Harga: <span id="totalHarga">0</span></h4>
  <br>
  <button type="submit">Simpan Pembelian</button>
</form>

<div id="message"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/pembelian-form.js') }}"></script>
@endpush
