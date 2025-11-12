document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    if (!token) return location.href = "/";
  
    try {
      const res = await axios.get("/api/pembelian", {
        headers: { Authorization: `Bearer ${token}` }
      });
      const tbody = document.getElementById("pembelianTable");
      tbody.innerHTML = "";
      res.data.data.forEach(p => {
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
  