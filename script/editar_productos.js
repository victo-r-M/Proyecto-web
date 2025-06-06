document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get("id");

  if (id) {
      document.getElementById("imagen").removeAttribute("required");

    fetch(`../php/obtener_producto.php?id=${id}`)
      .then(response => response.json())
      .then(data => {
        if (data && data.id) {
          document.getElementById("nombre").value = data.nombre;
          document.getElementById("descripcion").value = data.descripcion;
          document.getElementById("precioCompra").value = data.precioCompra;
          document.getElementById("precioVenta").value = data.precioVenta;
          document.getElementById("stock").value = data.stock;
          document.getElementById("stockMinimo").value = data.stockMinimo;
          document.getElementById("upload-text").style.display = "none"; //ocultta el texto de añadir iamgen

          

          // Vista previa de la imagen
          const preview = document.getElementById("preview");
          preview.src = `../img/productos/${data.imagen}`;
          preview.style.display = "block";

          // Opcional: guardar ID para usarlo al guardar
          const form = document.getElementById("producto-form");
          const inputHidden = document.createElement("input");
          inputHidden.type = "hidden";
          inputHidden.name = "id";
          inputHidden.value = id;
          form.appendChild(inputHidden);
        } else {
          alert("Producto no encontrado.");
        }
      })
      .catch(error => {
        console.error("Error al obtener el producto:", error);
      });
  }

  

});
