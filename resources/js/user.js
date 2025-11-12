document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;

      try {
        const res = await fetch("/api/login", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ username, password })
        });
        const data = await res.json();

        if (!res.ok) throw new Error(data.message || "Login gagal");

        localStorage.setItem("token", data.token);
        alert("Login berhasil");
        location.href = "/barang";
      } catch (err) {
        document.getElementById("message").innerText = err.message;
      }
    });
  }

  if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const username = document.getElementById("username").value;
      const email = document.getElementById("email").value;
      const tanggal_lahir = document.getElementById("tanggal_lahir").value;

      try {
        const res = await fetch("/api/registrasi", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ username, email, tanggal_lahir })
        });
        const data = await res.json();

        if (!res.ok) throw new Error(data.message || "Registrasi gagal");

        alert("Registrasi berhasil");
        location.href = "/";
      } catch (err) {
        document.getElementById("message").innerText = err.message;
      }
    });
  }
});
