document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
  
    if (loginForm) {
      loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        try {
          const res = await axios.post("/api/login", {
            username: document.getElementById("username").value,
            password: document.getElementById("password").value
          });
          localStorage.setItem("token", res.data.token);
          alert("Login berhasil");
          location.href = "/barang";
        } catch (err) {
          document.getElementById("message").innerText = err.response?.data?.message || "Login gagal";
        }
      });
    }
  
    if (registerForm) {
      registerForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        try {
          const res = await axios.post("/api/registrasi", {
            username: document.getElementById("username").value,
            email: document.getElementById("email").value,
            tanggal_lahir: document.getElementById("tanggal_lahir").value
          });
          alert("Registrasi berhasil");
          location.href = "/";
        } catch (err) {
          document.getElementById("message").innerText = err.response?.data?.message || "Registrasi gagal";
        }
      });
    }
  });
  