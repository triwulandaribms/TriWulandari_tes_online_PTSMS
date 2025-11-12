document.addEventListener("DOMContentLoaded", async () => {
  const token = localStorage.getItem("token");
  if (!token) return (location.href = "/");

  try {
    const res = await fetch("/api/pembelian", {
      headers: { Authorization: `Bearer ${token}` }
    });
    const data = await res.json();

    if (!res.ok) throw new Error(data.message || "Gagal memuat pembelian");

    const tbody = document.getElementById("pembelianTable");
    tbody.innerHTML = "";

    data.data.forEach((p) => {
      const row = `
        <tr>
          <td>${p.tanggal}</td>
          <td>${p.keterangan}</td>
          <td>${p.total_harga}</td>
          <td>${p.details.map(d => `${d.barang.nama_barang} (${d.qty})`).join(", ")}</td>
        </tr>`;
      tbody.insertAdjacentHTML("beforeend", row);
    });
  } catch (err) {
    console.error(err);
  }
});
