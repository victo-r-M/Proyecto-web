document.addEventListener("DOMContentLoaded", function () {
  cargarProductos();
});

function cargarProductos() {
  fetch("../php/listar_productos.php")
    .then((res) => res.json())
    .then((data) => {
      const tbody = document.querySelector("#tabla-productos tbody");
      tbody.innerHTML = "";

      if (data.error) {
        tbody.innerHTML = `<tr><td colspan="5">${data.error}</td></tr>`;
        return;
      }

      data.forEach((producto) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td><img src="../img/productos/${producto.imagen}" alt="${producto.nombre}" class="img-miniatura"></td>
          <td>${producto.nombre}</td>
          <td>${producto.descripcion}</td>
          <td>${producto.stock}</td>
          <td>$${producto.precioVenta.toFixed(2)}</td>
        `;
        
        // Evento al hacer clic
        fila.addEventListener("click", () => {
          mostrarModal(producto);
        });

        tbody.appendChild(fila);
      });
    })
    .catch((error) => {
      console.error("Error al cargar productos:", error);
    });
}

// Mostrar datos en el modal
function mostrarModal(producto) {
  document.getElementById("modal-imagen").src = `../img/productos/${producto.imagen}`;
  document.getElementById("modal-nombre").textContent = producto.nombre;
  document.getElementById("modal-descripcion").textContent = producto.descripcion;
  document.getElementById("modal-stock").textContent = producto.stock;
  document.getElementById("modal-stockMinimo").textContent = producto.stockMinimo;
  document.getElementById("modal-precioCompra").textContent = producto.precioCompra.toFixed(2);
  document.getElementById("modal-precioVenta").textContent = producto.precioVenta.toFixed(2);
  document.getElementById("modal-fecha").textContent = producto.fecha;

  document.getElementById("modal-producto").style.display = "flex";

  // Botón cerrar
  document.getElementById("cerrar-modal").onclick = () => {
    document.getElementById("modal-producto").style.display = "none";
  };

  // Botón Editar
  document.getElementById("btn-editar").onclick = () => {
    window.location.href = `producto_nuevo.php?id=${producto.id}`;
  };

  // Botón Eliminar
  document.getElementById("btn-eliminar").onclick = () => {
    if (confirm("¿Seguro que deseas eliminar este producto?")) {
      eliminarProducto(producto.id);
    }
  };
}

// Función eliminar (puedes adaptarla según tu backend)
function eliminarProducto(id) {
  fetch(`../php/eliminar_producto.php?id=${id}`, { method: "DELETE" })
    .then((res) => res.json())
    .then((data) => {
      alert(data.mensaje);
      cargarProductos();
      document.getElementById("modal-producto").style.display = "none";
    })
    .catch((err) => {
      alert("Error al eliminar.");
      console.error(err);
    });
}
