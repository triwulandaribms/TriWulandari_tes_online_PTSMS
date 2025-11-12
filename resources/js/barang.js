document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    if (!token) return location.href = "/";
  
    try {
      const res = await axios.get("/api/barang", {
        headers: { Authorization: `Bearer ${token}` }
      });
  
      const tbody = document.getElementById("barangTable");
      tbody.innerHTML = "";
  
      res.data.data.forEach(b => {
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
    await axios.delete(`/api/barang/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    location.reload();
  }
  