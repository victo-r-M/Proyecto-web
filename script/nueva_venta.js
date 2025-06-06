let productosVenta = [];


function buscarProducto() {
  const input = document.getElementById("buscar").value.trim();
  if (input.length === 0) {
    document.getElementById("sugerencias").innerHTML = "";
    return;
  }

  fetch(`../php/buscar_producto.php?term=${encodeURIComponent(input)}`)
    .then(res => res.json())
    .then(data => {
      const contenedor = document.getElementById("sugerencias");
      contenedor.innerHTML = "";
      data.forEach(p => {
        const div = document.createElement("div");
        div.classList.add("sugerencia-item");
        div.innerText = `${p.nombre} - $${parseFloat(p.precio).toFixed(2)}`;
        div.onclick = () => agregarProductoDesdeBD(p);
        contenedor.appendChild(div);
      });
    })
    .catch(err => console.error("Error en la búsqueda:", err));
}

function agregarProductoDesdeBD(producto) {
  const existente = productosVenta.find(p => p.id === producto.id);
  if (existente) {
    existente.cantidad++;
  } else {
    productosVenta.push({ ...producto, cantidad: 1 });
  }
  renderizarVenta();
  document.getElementById("sugerencias").innerHTML = "";
  document.getElementById("buscar").value = "";
}

function renderizarVenta() {
  const contenedor = document.getElementById("lista-venta");
  contenedor.innerHTML = "";
  let total = 0;

  productosVenta.forEach(p => {
    const subtotal = p.precio * p.cantidad;
    total += subtotal;
    contenedor.innerHTML += `
      <div class="producto-venta">
        <img src="../img/productos/${p.imagen}" alt="${p.nombre}" />
        <div class="info">
          <span>${p.nombre}</span>
          <small>$${p.precio.toFixed(2)}</small>
        </div>
        <input type="number" value="${p.cantidad}" min="1" onchange="cambiarCantidad(${p.id}, this.value)" />
        <div class="subtotal">$${subtotal.toFixed(2)}</div>
      </div>`;
  });

  document.getElementById("subtotal").textContent = `$${total.toFixed(2)}`;
  document.getElementById("totalCobrar").textContent = total.toFixed(2);
}

function cambiarCantidad(id, nuevaCantidad) {
  const producto = productosVenta.find(p => p.id === id);
  producto.cantidad = parseInt(nuevaCantidad);
  renderizarVenta();
}

function cobrar() {
    if (productosVenta.length === 0) {
        alert("No hay productos en la venta.");
        return;
    }

    fetch("../php/registrar_venta.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(productosVenta),
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.error) {
            console.error("Error del servidor:", data);
            alert("Error: " + data.error);
            return;
        }

        alert(data.mensaje);
        productosVenta = [];
        renderizarVenta();
    })
    .catch((error) => {
        console.error("Error de red o parsing:", error);
        alert("Hubo un problema al registrar la venta.");
    });
}

