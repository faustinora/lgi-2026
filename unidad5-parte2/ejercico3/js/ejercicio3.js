// Referencias al DOM
const formularioCrear = document.getElementById('formularioCrear');
const inputNombre = document.getElementById('nombre');
const inputApellido = document.getElementById('apellido');
const inputEmail = document.getElementById('email');

const modalEditar = document.getElementById('modalEditar');
const formularioEditar = document.getElementById('formularioEditar');
const editId = document.getElementById('editId');
const editNombre = document.getElementById('editNombre');
const editApellido = document.getElementById('editApellido');
const editEmail = document.getElementById('editEmail');

const inputBuscador = document.getElementById('inputBuscador');
const contenedorMensaje = document.getElementById('contenedorMensaje');
const tablaEstudiantes = document.getElementById('tablaEstudiantes');
const totalEstudiantes = document.getElementById('totalEstudiantes');

const API_URL = 'api/estudiantes.php';
let temporizadorBusqueda;

// 1. OBTENER (GET)
async function cargarEstudiantes(termino = '') {
  try {
    const url = termino ? `${API_URL}?q=${encodeURIComponent(termino)}` : API_URL;
    const respuesta = await fetch(url);

    if (!respuesta.ok) throw new Error(`HTTP Error: ${respuesta.status}`);

    const estudiantes = await respuesta.json();
    renderizarTabla(estudiantes);

  } catch (error) {
    console.error('Error al cargar:', error);
    mostrarMensaje('Error al conectar con la base de datos.', 'error');
  }
}

// 2. CREAR (POST)
async function guardarEstudiante(e) {
  e.preventDefault();
  limpiarMensaje();

  const nombre = inputNombre.value.trim();
  const apellido = inputApellido.value.trim();
  const email = inputEmail.value.trim();

  try {
    const respuesta = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre, apellido, email })
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al guardar');

    mostrarMensaje(datos.mensaje, 'exito');
    formularioCrear.reset();
    cargarEstudiantes(inputBuscador.value.trim());

  } catch (error) {
    mostrarMensaje(error.message, 'error');
  }
}

// 3. EDITAR / ACTUALIZAR (PUT)
function abrirModalEditar(id, nombre, apellido, email) {
  editId.value = id;
  editNombre.value = nombre;
  editApellido.value = apellido;
  editEmail.value = email;
  modalEditar.showModal();
}

function cerrarModal() {
  modalEditar.close();
}

async function actualizarEstudiante(e) {
  e.preventDefault();
  limpiarMensaje();

  const id = editId.value;
  const nombre = editNombre.value.trim();
  const apellido = editApellido.value.trim();
  const email = editEmail.value.trim();

  try {
    const respuesta = await fetch(API_URL, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, nombre, apellido, email })
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al actualizar');

    cerrarModal();
    mostrarMensaje(datos.mensaje, 'exito');
    cargarEstudiantes(inputBuscador.value.trim());

  } catch (error) {
    mostrarMensaje(error.message, 'error');
  }
}

// 4. ELIMINAR (DELETE)
async function eliminarEstudiante(id, nombreCompleto) {
  if (!confirm(`¿Seguro que deseas eliminar a ${nombreCompleto}?`)) return;

  limpiarMensaje();

  try {
    const respuesta = await fetch(`${API_URL}?id=${id}`, {
      method: 'DELETE'
    });

    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'Error al eliminar');

    mostrarMensaje(datos.mensaje, 'exito');
    cargarEstudiantes(inputBuscador.value.trim());

  } catch (error) {
    mostrarMensaje(error.message, 'error');
  }
}

// 5. RENDERIZADO DE TABLA Y EVENTOS EN FILA
function renderizarTabla(estudiantes) {
  tablaEstudiantes.innerHTML = '';

  if (!Array.isArray(estudiantes) || estudiantes.length === 0) {
    tablaEstudiantes.innerHTML = `<tr><td colspan="4" style="text-align: center;">No se encontraron estudiantes.</td></tr>`;
    totalEstudiantes.innerHTML = `<strong>Total:</strong> 0 estudiantes`;
    return;
  }

  estudiantes.forEach((e) => {
    const fila = document.createElement('tr');
    const nombreCompleto = `${escapeHTML(e.nombre)} ${escapeHTML(e.apellido)}`;

    fila.innerHTML = `
      <td>${escapeHTML(e.apellido)}</td>
      <td>${escapeHTML(e.nombre)}</td>
      <td>${escapeHTML(e.email)}</td>
      <td class="col-acciones">
        <button class="btn-accion" onclick="abrirModalEditar(${e.id}, '${escapeQuote(e.nombre)}', '${escapeQuote(e.apellido)}', '${escapeQuote(e.email)}')">Editar</button>
        <button class="btn-accion outline contrast" onclick="eliminarEstudiante(${e.id}, '${escapeQuote(nombreCompleto)}')">Eliminar</button>
      </td>
    `;
    tablaEstudiantes.appendChild(fila);
  });

  totalEstudiantes.innerHTML = `<strong>Total:</strong> ${estudiantes.length} estudiante(s)`;
}

// Auxiliares
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

// Listeners
formularioCrear.addEventListener('submit', guardarEstudiante);
formularioEditar.addEventListener('submit', actualizarEstudiante);

inputBuscador.addEventListener('input', (e) => {
  clearTimeout(temporizadorBusqueda);
  const termino = e.target.value.trim();
  temporizadorBusqueda = setTimeout(() => cargarEstudiantes(termino), 300);
});

// Carga Inicial
cargarEstudiantes();