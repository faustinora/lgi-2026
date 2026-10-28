const formularioCrear = document.getElementById('formularioCrear');
const inputCodigo = document.getElementById('codigo');
const inputNombre = document.getElementById('nombre');
const inputPrecio = document.getElementById('precio');
const inputStock = document.getElementById('stock');

const modalEditar = document.getElementById('modalEditar');
const formularioEditar = document.getElementById('formularioEditar');
const editId = document.getElementById('editId');
const editCodigo = document.getElementById('editCodigo');
const editNombre = document.getElementById('editNombre');
const editPrecio = document.getElementById('editPrecio');
const editStock = document.getElementById('editStock');

const inputBuscador = document.getElementById('inputBuscador');
const tablaProductos = document.getElementById('tablaProductos');
const totalProductos = document.getElementById('totalProductos');


const modalVenta = document.getElementById('modalVenta');
const formularioVenta = document.getElementById('formularioVenta');
const selectProductoVenta = document.getElementById('selectProductoVenta');
const cantidadVenta = document.getElementById('cantidadVenta');
const lblSubtotalVenta = document.getElementById('lblSubtotalVenta');
const tablaVentas = document.getElementById('tablaVentas');
const seccionHistorialVentas = document.getElementById('seccionHistorialVentas');

const API_URL = 'api/productoPrueba.php';
let temporizadorBusqueda;
let listaGlobalProductos = []; 

async function cargarProductos(termino = '') {
  try {
    const url = termino ? `${API_URL}?q=${encodeURIComponent(termino)}` : API_URL;
    const respuesta = await fetch(url);

    if (!respuesta.ok) throw new Error(`HTTP Error: ${respuesta.status}`);

    const productos = await respuesta.json();
    listaGlobalProductos = productos; 
    renderizarTabla(productos);

  } catch (error) {
    console.error('Error al cargar:', error);
    mostrarToast('Error al conectar con la base de datos.', 'error');
  }
}

async function guardarProducto(e) {
  e.preventDefault();

  const codigo = inputCodigo.value.trim();
  const nombre = inputNombre.value.trim();
  const precio = parseFloat(inputPrecio.value);
  const stock = parseInt(inputStock.value, 10);

  if (isNaN(precio) || precio < 0 || isNaN(stock) || stock < 0){
    mostrarToast("Precio y stock deben ser valores positivos", "error");
    return;
  }

  try {
    const respuesta = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ codigo, nombre, precio, stock })
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al guardar');

    mostrarToast(datos.message || 'Producto guardado', 'exito');
    formularioCrear.reset();
    cargarProductos(inputBuscador.value.trim());

  } catch (error) {
    mostrarToast(error.message, 'error');
  }
}

function abrirModalEditar(id, codigo, nombre, precio, stock) {
  editId.value = id;
  editCodigo.value = codigo;
  editNombre.value = nombre;
  editPrecio.value = precio;
  editStock.value = stock;
  modalEditar.showModal();
}

function cerrarModal() {
  modalEditar.close();
}

async function actualizarProducto(e) {
  e.preventDefault();

  const id = editId.value;
  const codigo = editCodigo.value.trim();
  const nombre = editNombre.value.trim();
  const precio = parseFloat(editPrecio.value);
  const stock = parseInt(editStock.value, 10);

  try {
    const respuesta = await fetch(API_URL, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, codigo, nombre, precio, stock })
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al actualizar');

    cerrarModal();
    mostrarToast(datos.message || 'Actualización exitosa', 'exito');
    cargarProductos(inputBuscador.value.trim());

  } catch (error) {
    mostrarToast(error.message, 'error');
  }
}

async function eliminarProducto(id, nombre) {
  if (!confirm(`¿Seguro que deseas eliminar el producto: ${nombre}?`)) return;

  try {
    const respuesta = await fetch(`${API_URL}?id=${id}`, {
      method: 'DELETE'
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al eliminar');

    mostrarToast(datos.message || 'Producto eliminado', 'exito');
    cargarProductos(inputBuscador.value.trim());

  } catch (error) {
    mostrarToast(error.message, 'error');
  }
}

function abrirModalVenta() {
  selectProductoVenta.innerHTML = '<option value="">Cargando productos...</option>';
  
  fetch(API_URL)
    .then(res => res.json())
    .then(productos => {
      listaGlobalProductos = productos;
      selectProductoVenta.innerHTML = '<option value="" disabled selected>-- Seleccione un producto --</option>';
      
      productos.forEach(prod => {
        if (parseInt(prod.stock) > 0) {
          const option = document.createElement('option');
          option.value = prod.id;
          option.textContent = `${prod.nombre} (Stock: ${prod.stock} - $${prod.precio})`;
          selectProductoVenta.appendChild(option);
        }
      });
      actualizarSubtotalVenta();
      modalVenta.showModal();
    })
    .catch(() => {
      mostrarToast('Error al cargar los productos para la venta', 'error');
    });
}

function cerrarModalVenta() {
  modalVenta.close();
}

function actualizarSubtotalVenta() {
  const idProd = selectProductoVenta.value;
  const cant = parseInt(cantidadVenta.value, 10) || 0;
  
  const productoEncontrado = listaGlobalProductos.find(p => p.id == idProd);
  if (productoEncontrado) {
    const subtotal = parseFloat(productoEncontrado.precio) * cant;
    lblSubtotalVenta.textContent = `$${subtotal.toFixed(2)}`;
  } else {
    lblSubtotalVenta.textContent = '$0.00';
  }
}

selectProductoVenta.addEventListener('change', actualizarSubtotalVenta);
cantidadVenta.addEventListener('input', actualizarSubtotalVenta);

formularioVenta.addEventListener('submit', async (e) => {
  e.preventDefault();

  const id_producto = selectProductoVenta.value;
  const cantidad = parseInt(cantidadVenta.value, 10);

  if (!id_producto || isNaN(cantidad) || cantidad <= 0) {
    mostrarToast('Seleccione un producto y una cantidad válida', 'error');
    return;
  }

  try {
    const respuesta = await fetch(`${API_URL}?accion=vender`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_producto, cantidad })
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al procesar la venta');

    mostrarToast(datos.message || '¡Venta realizada con éxito!', 'exito');
    cerrarModalVenta();
    formularioVenta.reset();
    cargarProductos(inputBuscador.value.trim());

    if (seccionHistorialVentas.style.display !== 'none') {
      cargarHistorialVentas();
    }

  } catch (error) {
    mostrarToast(error.message, 'error');
  }
});

function toggleSeccionVentas() {
  if (seccionHistorialVentas.style.display === 'none') {
    seccionHistorialVentas.style.display = 'block';
    cargarHistorialVentas();
  } else {
    seccionHistorialVentas.style.display = 'none';
  }
}

async function cargarHistorialVentas() {
  try {
    const respuesta = await fetch(`${API_URL}?accion=listar_ventas`);
    if (!respuesta.ok) throw new Error('Error al obtener el historial');

    const ventas = await respuesta.json();
    tablaVentas.innerHTML = '';

    if (!Array.isArray(ventas) || ventas.length === 0) {
      tablaVentas.innerHTML = `<tr><td colspan="5" style="text-align: center;">No hay ventas registradas.</td></tr>`;
      return;
    }

    ventas.forEach(v => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>#${v.id}</td>
        <td>${escapeHTML(v.nombre_producto)}</td>
        <td>${v.cantidad}</td>
        <td>$${parseFloat(v.total_cobrado).toFixed(2)}</td>
        <td>${v.fecha || 'Reciente'}</td>
      `;
      tablaVentas.appendChild(tr);
    });

  } catch (error) {
    console.error('Error:', error);
    mostrarToast('No se pudo cargar el historial de ventas', 'error');
  }
}

function renderizarTabla(productos) {
  tablaProductos.innerHTML = '';

  if (!Array.isArray(productos) || productos.length === 0) {
    tablaProductos.innerHTML = `<tr><td colspan="5" style="text-align: center;">No se encontraron productos.</td></tr>`;
    totalProductos.innerHTML = `<strong>Total:</strong> 0 productos`;
    
    const spanTotalInventario = document.getElementById('spanTotalInventario');
    if (spanTotalInventario) spanTotalInventario.innerText = '$0.00';
    return;
  }

  let valorTotalInventario = 0;

  productos.forEach((p) => {
    valorTotalInventario += parseFloat(p.precio) * parseInt(p.stock);
    const esStockBajo = parseInt(p.stock) < 10;

    const fila = document.createElement('tr');
    
    if (esStockBajo) {
      fila.style.backgroundColor = '#ffebee';
      fila.style.color = '#c62828';
    }

    fila.innerHTML = `
      <td>${escapeHTML(p.codigo)}</td>
      <td>${escapeHTML(p.nombre)}</td>
      <td>$${parseFloat(p.precio).toFixed(2)}</td>
      <td>
        ${escapeHTML(p.stock)} 
        ${esStockBajo ? '<span style="color: #c62828; font-weight: bold;">(BAJO)</span>' : ''}
      </td>
      <td class="col-acciones">
        <button class="btn-accion" onclick="abrirModalEditar(${p.id}, '${escapeQuote(p.codigo)}', '${escapeQuote(p.nombre)}', ${p.precio}, ${p.stock})">Editar</button>
        <button class="btn-accion outline contrast" onclick="eliminarProducto(${p.id}, '${escapeQuote(p.nombre)}')">Eliminar</button>
      </td>
    `;
    tablaProductos.appendChild(fila);
  });

  totalProductos.innerHTML = `<strong>Total:</strong> ${productos.length} producto(s)`;

  const spanTotalInventario = document.getElementById('spanTotalInventario');
  if (spanTotalInventario) {
    spanTotalInventario.innerText = `$${valorTotalInventario.toFixed(2)}`;
  }
}

function mostrarToast(mensaje, tipo = 'exito') {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast-notificacion ${tipo}`;
  toast.innerText = mensaje;

  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('visible');
  }, 10);

  setTimeout(() => {
    toast.classList.remove('visible');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

function escapeHTML(str) {
  return String(str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function escapeQuote(str) {
  return String(str || '').replace(/'/g, "\\'");
}

formularioCrear.addEventListener('submit', guardarProducto);
formularioEditar.addEventListener('submit', actualizarProducto);

inputBuscador.addEventListener('input', (e) => {
  clearTimeout(temporizadorBusqueda);
  const termino = e.target.value.trim();
  temporizadorBusqueda = setTimeout(() => cargarProductos(termino), 300);
});

cargarProductos();