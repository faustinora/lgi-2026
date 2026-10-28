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
const contenedorMensaje = document.getElementById('contenedorMensaje');
const tablaProductos = document.getElementById('tablaProductos');
const totalProductos = document.getElementById('totalProductos');

const API_URL = 'api/productos.php';
let temporizadorBusqueda;

async function cargarProductos(termino = '') {
  try {
    const url = termino ? `${API_URL}?q=${encodeURIComponent(termino)}` : API_URL;
    const respuesta = await fetch(url);

    if (!respuesta.ok) throw new Error(`HTTP Error: ${respuesta.status}`);

    const productos = await respuesta.json();
    renderizarTabla(productos);

  } catch (error) {
    console.error('Error al cargar:', error);
    mostrarMensaje('Error al conectar con la base de datos.', 'error');
  }
}

async function guardarProducto(e) {
  e.preventDefault();
  limpiarMensaje();

  const codigo = inputCodigo.value.trim();
  const nombre = inputNombre.value.trim();
  const precio = parseFloat(inputPrecio.value);
  const stock = parseInt(inputStock.value, 10);

  try {
    const respuesta = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ codigo, nombre, precio, stock })
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al guardar');

    mostrarMensaje(datos.message || 'Producto guardado', 'exito');
    formularioCrear.reset();
    cargarProductos(inputBuscador.value.trim());

  } catch (error) {
    mostrarMensaje(error.message, 'error');
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
  limpiarMensaje();

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
    mostrarMensaje(datos.message || 'Producto actualizado', 'exito');
    cargarProductos(inputBuscador.value.trim());

  } catch (error) {
    mostrarMensaje(error.message, 'error');
  }
}

async function eliminarProducto(id, nombre) {
  if (!confirm(`¿Seguro que deseas eliminar el producto: ${nombre}?`)) return;

  limpiarMensaje();

  try {
    const respuesta = await fetch(`${API_URL}?id=${id}`, {
      method: 'DELETE'
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al eliminar');

    mostrarMensaje(datos.message || 'Producto eliminado', 'exito');
    cargarProductos(inputBuscador.value.trim());

  } catch (error) {
    mostrarMensaje(error.message, 'error');
  }
}

function renderizarTabla(productos) {

  tablaProductos.innerHTML = '';

  if (!Array.isArray(productos) || productos.length === 0) {

    tablaProductos.innerHTML = `<tr><td colspan="5" style="text-align: center;">No se encontraron productos.</td></tr>`;

    totalProductos.innerHTML = `<strong>Total:</strong> 0 productos`;

    return;
  }

  productos.forEach((p) => {
    const fila = document.createElement('tr');   
    fila.innerHTML = `
      <td>${escapeHTML(p.codigo)}</td>

      <td>${escapeHTML(p.nombre)}</td>

      <td>$${escapeHTML(p.precio)}</td>

      <td>${escapeHTML(p.stock)}</td>

      <td class="col-acciones">

        <button class="btn-accion" onclick="abrirModalEditar(${p.id}, '${escapeQuote(p.codigo)}', '${escapeQuote(p.nombre)}', '${escapeQuote(p.precio)}', '${escapeQuote(p.stock)}')">Editar</button>

        <button class="btn-accion outline contrast" onclick="eliminarProducto(${p.id}, '${escapeQuote(p.nombre)}')">Eliminar</button>
      </td>
    `;

    tablaProductos.appendChild(fila);

  });

  totalProductos.innerHTML = `<strong>Total:</strong> ${productos.length} producto(s)`;

}

function mostrarMensaje(texto, tipo) {
  const clase = tipo === 'exito' ? 'mensaje-exito' : 'mensaje-error';
  contenedorMensaje.innerHTML = `<div class="mensaje-alerta ${clase}">${escapeHTML(texto)}</div>`;
}

function limpiarMensaje() {
  contenedorMensaje.innerHTML = '';
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