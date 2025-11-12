@extends('layout')

@section('content')
<h3>Data Pembelian</h3>
<table border="1" width="100%">
  <thead>
    <tr><th>Tanggal</th><th>Keterangan</th><th>Total</th><th>Detail</th></tr>
  </thead>
  <tbody id="pembelianTable"></tbody>
</table>
@endsection

@push('scripts')
<script src="{{ asset('js/pembelian.js') }}"></script>
@endpush
