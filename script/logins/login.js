document.addEventListener("DOMContentLoaded", () => {
  fetch("php/logins/verificar_dueno.php")
    .then(response => response.json())
    .then(data => {
      if (data.existeDueno) {
        document.getElementById("crearCuentaLink").style.display = "none";
      }
    })
    .catch(error => console.error("Error:", error));
});



