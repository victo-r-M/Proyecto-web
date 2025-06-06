document.addEventListener("DOMContentLoaded", function () {
  cargarVentas();

  // Buscar y ordenar
  document.getElementById("buscador").addEventListener("input", cargarVentas);
  document.getElementById("ordenar").addEventListener("change", cargarVentas);
});

function cargarVentas() {
  const buscador = document.getElementById("buscador").value.toLowerCase();
  const ordenar = document.getElementById("ordenar").value;

  fetch("../php/listar_ventas.php")
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector("#tabla-productos tbody");
      tbody.innerHTML = "";

      if (data.error) {
        tbody.innerHTML = `<tr><td colspan="3">${data.error}</td></tr>`;
        return;
      }

      // Filtrar por vendedor o fecha
      let ventasFiltradas = data.filter(venta => 
        venta.Vendedor.toLowerCase().includes(buscador) ||
        venta.FechaVenta.toLowerCase().includes(buscador)
      );

      // Ordenar
      switch (ordenar) {
        case "nombre":
          ventasFiltradas.sort((a, b) => a.Vendedor.localeCompare(b.Vendedor));
          break;
        case "venta_desc":
          ventasFiltradas.sort((a, b) => b.Total - a.Total);
          break;
        case "venta_asc":
          ventasFiltradas.sort((a, b) => a.Total - b.Total);
          break;
        case "fecha":
          ventasFiltradas.sort((a, b) => new Date(b.FechaVenta) - new Date(a.FechaVenta));
          break;
      }

      ventasFiltradas.forEach(venta => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${venta.Vendedor}</td>
          <td>${new Date(venta.FechaVenta).toLocaleString()}</td>
          <td>$${parseFloat(venta.Total).toFixed(2)}</td>
        `;

        fila.addEventListener("click", () => mostrarDetalleVenta(venta.IdVenta));

        tbody.appendChild(fila);
      });
    })
    .catch(err => {
      console.error("Error al cargar ventas:", err);
    });
}

function mostrarDetalleVenta(idVenta) {
  fetch(`../php/listar_detalle_venta.php?idVenta=${idVenta}`)
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        alert("Error al cargar detalle: " + data.error);
        return;
      }

      // Construir tabla detalle dentro del modal
      const modalBody = document.querySelector(".modal-body");
      modalBody.innerHTML = `
        <table class="detalle-venta-table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio Unitario</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            ${data.map(item => `
              <tr>
                <td>${item.Nombre}</td>
                <td>${item.Cantidad}</td>
                <td>$${parseFloat(item.PrecioUnitario).toFixed(2)}</td>
                <td>$${parseFloat(item.Subtotal).toFixed(2)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      `;

      document.getElementById("modal-producto").style.display = "flex";

      // Botón cerrar
      document.getElementById("cerrar-modal").onclick = () => {
        document.getElementById("modal-producto").style.display = "none";
        modalBody.innerHTML = "";
      };

      // Ocultar botones Editar y Eliminar para ventas
      document.getElementById("btn-editar").style.display = "none";
      document.getElementById("btn-eliminar").style.display = "none";
    })
    .catch(err => {
      alert("Error al cargar detalle de venta");
      console.error(err);
    });
}
