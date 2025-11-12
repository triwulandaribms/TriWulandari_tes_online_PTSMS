document.addEventListener("DOMContentLoaded", async () => {
  const token = localStorage.getItem("token");
  if (!token) return (location.href = "/");

  try {
    const res = await fetch("/api/barang", {
      headers: { Authorization: `Bearer ${token}` }
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Gagal memuat barang");

    const tbody = document.getElementById("barangTable");
    tbody.innerHTML = "";

    data.data.forEach((b) => {
      const row = `
        <tr>
          <td>${b.kode_barang}</td>
          <td>${b.nama_barang}</td>
          <td>${b.harga}</td>
          <td>
            <button onclick="editBarang(${b.id})">Edit</button>
            <button onclick="hapusBarang(${b.id})">Hapus</button>
          </td>
        </tr>`;
      tbody.insertAdjacentHTML("beforeend", row);
    });
  } catch (err) {
    console.error(err);
  }
});

async function hapusBarang(id) {
  const token = localStorage.getItem("token");
  if (!confirm("Yakin hapus?")) return;

  try {
    const res = await fetch(`/api/barang/${id}`, {
      method: "DELETE",
      headers: { Authorization: `Bearer ${token}` }
    });

    if (!res.ok) throw new Error("Gagal menghapus barang");

    location.reload();
  } catch (err) {
    alert(err.message);
  }
}
