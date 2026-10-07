
const formulario = document.getElementById('formularioEstudiante');
const inputNombre = document.getElementById('nombre');
const inputApellido = document.getElementById('apellido');
const inputEmail = document.getElementById('email');
const inputBuscador = document.getElementById('inputBuscador');
const contenedorMensaje = document.getElementById('contenedorMensaje');
const tablaEstudiantes = document.getElementById('tablaEstudiantes');
const totalEstudiantes = document.getElementById('totalEstudiantes');
const btnGuardar = document.getElementById('btnGuardar');

const API_URL = 'api/estudiantes.php';
let temporizadorBusqueda;

async function cargarEstudiantes(termino = '') {
  try {
    const url = termino 
      ? `${API_URL}?q=${encodeURIComponent(termino)}` 
      : API_URL;

    const respuesta = await fetch(url);
    if (!respuesta.ok) {
      throw new Error(`HTTP Error: ${respuesta.status}`);
    }

    const estudiantes = await respuesta.json();
    renderizarTabla(estudiantes);

  } catch (error) {
    console.error('Error al obtener estudiantes:', error);
    mostrarMensaje('Error al conectar con la base de datos.', 'error');
  }
}

async function guardarEstudiante(event) {
  event.preventDefault();
  limpiarMensaje();

  const nombre = inputNombre.value.trim();
  const apellido = inputApellido.value.trim();
  const email = inputEmail.value.trim();

  if (!nombre || !apellido || !email) {
    mostrarMensaje('Todos los campos son obligatorios.', 'error');
    return;
  }

  btnGuardar.disabled = true;

  try {
    const respuesta = await fetch(API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ nombre, apellido, email })
    });

    const datos = await respuesta.json();

    if (!respuesta.ok) {
      throw new Error(datos.error || 'Ocurrió un error al guardar.');
    }

    mostrarMensaje(datos.mensaje || 'Estudiante agregado correctamente.', 'exito');


    inputNombre.value = '';
    inputApellido.value = '';
    inputEmail.value = '';


    cargarEstudiantes(inputBuscador.value.trim());

  } catch (error) {
    console.error('Error al guardar:', error);
    mostrarMensaje(error.message, 'error');
  } finally {
    btnGuardar.disabled = false;
  }
}


function renderizarTabla(estudiantes) {
  tablaEstudiantes.innerHTML = '';

  if (!Array.isArray(estudiantes) || estudiantes.length === 0) {
    tablaEstudiantes.innerHTML = `<tr><td colspan="3" style="text-align: center;">No se encontraron estudiantes.</td></tr>`;
    totalEstudiantes.innerHTML = `<strong>Total:</strong> 0 estudiantes`;
    return;
  }

  estudiantes.forEach((estudiante) => {
    const fila = document.createElement('tr');
    fila.innerHTML = `
      <td>${escapeHTML(estudiante.apellido)}</td>
      <td>${escapeHTML(estudiante.nombre)}</td>
      <td>${escapeHTML(estudiante.email)}</td>
    `;
    tablaEstudiantes.appendChild(fila);
  });

  totalEstudiantes.innerHTML = `<strong>Total:</strong> ${estudiantes.length} estudiante(s)`;
}

function mostrarMensaje(texto, tipo) {
  const clase = tipo === 'exito' ? 'mensaje-exito' : 'mensaje-error';
  contenedorMensaje.innerHTML = `<div class="mensaje-alerta ${clase}">${escapeHTML(texto)}</div>`;
}

function limpiarMensaje() {
  contenedorMensaje.innerHTML = '';
}

function escapeHTML(str) {
  return String(str || '')
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}


formulario.addEventListener('submit', guardarEstudiante);

inputBuscador.addEventListener('input', (e) => {
  clearTimeout(temporizadorBusqueda);
  const termino = e.target.value.trim();
  
  temporizadorBusqueda = setTimeout(() => {
    cargarEstudiantes(termino);
  }, 300);
});

cargarEstudiantes();