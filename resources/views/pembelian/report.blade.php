@extends('layout')

@section('content')
<h3>Laporan Pembelian</h3>
<form id="reportForm">
  <input type="date" id="tanggal">
  <input type="text" id="kode_barang" placeholder="Kode Barang">
  <button type="submit">Tampilkan</button>
</form>
<table border="1" width="100%">
  <thead>
    <tr><th>Tanggal</th><th>Kode Barang</th><th>Nama Barang</th><th>Qty</th><th>Total</th></tr>
  </thead>
  <tbody id="reportTable"></tbody>
</table>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("reportForm");
  const tbody = document.getElementById("reportTable");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const token = localStorage.getItem("token");
    const tanggal = document.getElementById("tanggal").value;
    const kode_barang = document.getElementById("kode_barang").value;

    try {
      const url = new URL("/api/report", window.location.origin);
      if (tanggal) url.searchParams.append("tanggal", tanggal);
      if (kode_barang) url.searchParams.append("kode_barang", kode_barang);

      const res = await fetch(url, {
        headers: { Authorization: `Bearer ${token}` }
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.message || "Gagal memuat laporan");

      tbody.innerHTML = "";
      data.data.forEach((r) => {
        const row = `
          <tr>
            <td>${r.tanggal}</td>
            <td>${r.kode_barang}</td>
            <td>${r.nama_barang}</td>
            <td>${r.total_qty}</td>
            <td>${r.total_harga}</td>
          </tr>`;
        tbody.insertAdjacentHTML("beforeend", row);
      });
    } catch (err) {
      alert(err.message);
    }
  });
});
</script>
@endpush
