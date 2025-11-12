document.addEventListener("DOMContentLoaded", async () => {
  const token = localStorage.getItem("token");
  if (!token) return (location.href = "/");

  let barangList = [];
  let totalHarga = 0;

  try {
    const res = await fetch("/api/barang", {
      headers: { Authorization: `Bearer ${token}` }
    });
    const data = await res.json();
    if (!res.ok) throw new Error("Gagal memuat data barang");
    barangList = data.data;
  } catch (err) {
    alert(err.message);
    return;
  }

  const addBarangBtn = document.getElementById("addBarangBtn");
  const barangRows = document.getElementById("barangRows");
  const totalHargaEl = document.getElementById("totalHarga");

  function hitungTotal() {
    totalHarga = 0;
    document.querySelectorAll(".subtotal").forEach((el) => {
      totalHarga += parseInt(el.textContent || 0);
    });
    totalHargaEl.textContent = totalHarga;
  }

  function createRow() {
    const row = document.createElement("tr");

    const barangSelect = document.createElement("select");
    barangSelect.className = "barang-select";
    barangSelect.innerHTML = `
      <option value="">-- Pilih Barang --</option>
      ${barangList.map(b => `<option value="${b.id}" data-harga="${b.harga}">${b.nama_barang}</option>`).join("")}
    `;

    const hargaTd = document.createElement("td");
    hargaTd.textContent = "0";

    const qtyInput = document.createElement("input");
    qtyInput.type = "number";
    qtyInput.value = 1;
    qtyInput.min = 1;

    const subtotalTd = document.createElement("td");
    subtotalTd.className = "subtotal";
    subtotalTd.textContent = "0";

    const hapusBtn = document.createElement("button");
    hapusBtn.type = "button";
    hapusBtn.textContent = "Hapus";
    hapusBtn.addEventListener("click", () => {
      row.remove();
      hitungTotal();
    });

    const selectTd = document.createElement("td");
    selectTd.appendChild(barangSelect);
    const qtyTd = document.createElement("td");
    qtyTd.appendChild(qtyInput);
    const aksiTd = document.createElement("td");
    aksiTd.appendChild(hapusBtn);

    row.appendChild(selectTd);
    row.appendChild(hargaTd);
    row.appendChild(qtyTd);
    row.appendChild(subtotalTd);
    row.appendChild(aksiTd);

    barangSelect.addEventListener("change", (e) => {
      const harga = e.target.selectedOptions[0].dataset.harga || 0;
      hargaTd.textContent = harga;
      subtotalTd.textContent = harga * qtyInput.value;
      hitungTotal();
    });

    qtyInput.addEventListener("input", () => {
      const harga = barangSelect.selectedOptions[0].dataset.harga || 0;
      subtotalTd.textContent = harga * qtyInput.value;
      hitungTotal();
    });

    barangRows.appendChild(row);
  }

  addBarangBtn.addEventListener("click", createRow);
  createRow(); 

  document.getElementById("pembelianForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const tanggal = document.getElementById("tanggal").value;
    const keterangan = document.getElementById("keterangan").value;

    const details = [];
    document.querySelectorAll("#barangRows tr").forEach((tr) => {
      const select = tr.querySelector(".barang-select");
      const qty = tr.querySelector("input[type='number']").value;
      if (select.value) {
        details.push({
          barang_id: select.value,
          qty: qty,
        });
      }
    });

    if (details.length === 0) {
      alert("Tambahkan minimal 1 barang!");
      return;
    }

    try {
      const res = await fetch("/api/pembelian", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({ tanggal, keterangan, details })
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.message || "Gagal menyimpan pembelian");

      alert("Pembelian berhasil disimpan!");
      location.href = "/pembelian";
    } catch (err) {
      alert(err.message);
    }
  });
});
