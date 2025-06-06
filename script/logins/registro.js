document.getElementById("registroForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("php/logins/registro.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert("Cuenta creada con éxito. Ahora puedes iniciar sesión.");
        window.location.href = "index.php";
      } else {
        document.getElementById("errorMsg").innerText = data.error || "Error al registrar";
      }
    })
    .catch(() => {
      document.getElementById("errorMsg").innerText = "Error al registrar";
    });
});
