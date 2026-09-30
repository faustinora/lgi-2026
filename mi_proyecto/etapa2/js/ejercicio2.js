// vector global para mantener la lista
let estudiantes = JSON.parse(localStorage.getItem('estudiantes')) || [];

function guardarEnLocalStorage() {
  localStorage.setItem('estudiantes', JSON.stringify(estudiantes));
}


function calcularPromedio(notas) {
  if (notas.length === 0) return 0;
  const suma = notas.reduce((acumulador, nota) => acumulador + nota, 0);
  return suma / notas.length;
}

function estaAprobado(nota, minima = 6) {
  return nota >= minima;
}

function obtenerCalificacion(promedio) {
  if (promedio >= 9) return "Sobresaliente";
  if (promedio >= 7.5) return "Muy Bueno";
  if (promedio >= 6) return "Bueno";
  return "Desaprobado";
}

// parseo y filtro
function parsearNotas(textoNotas) {
  return textoNotas
    .split(',')
    .map(n => parseFloat(n.trim()))
    .filter(n => !isNaN(n) && n >= 0 && n <= 10);
}

// Referencias al DOM
const formulario = document.getElementById('formularioEstudiante');
const inputNombre = document.getElementById('nombre');
const inputNotas = document.getElementById('notas');
const inputNotaMinima = document.getElementById('notaMinima');
const mensajeError = document.getElementById('mensajeError');
const tablaEstudiantes = document.getElementById('tablaEstudiantes');
const resumenGrupo = document.getElementById('resumenGrupo');
const btnLimpiar = document.getElementById('btnLimpiar');

// Evento al enviar el formulario
formulario.addEventListener('submit', (event) => {
  event.preventDefault();
  mensajeError.textContent = '';

  const nombre = inputNombre.value.trim();
  const notasValidas = parsearNotas(inputNotas.value);

  // Validación: si no hay notas válidas
  if (notasValidas.length === 0) {
    mensajeError.textContent = 'Error: Debe ingresar una nota entre 1 y 10.';
    return;
  }

  // Agregar el nuevo estudiante
  const nuevoEstudiante = {
    id: Date.now(), // para eliminar fácil
    nombre: nombre,
    notas: notasValidas
  };

  estudiantes.push(nuevoEstudiante);

  // Resetear campos
  inputNombre.value = '';
  inputNotas.value = '';

  actualizarInterfaz();
});

// Evento cambiar nota mínima
inputNotaMinima.addEventListener('input', () => {
  actualizarInterfaz();
});

// limpiar toda la lista
btnLimpiar.addEventListener('click', () => {
  estudiantes = [];
  actualizarInterfaz();
});

function actualizarInterfaz() {
  const notaMinima = parseFloat(inputNotaMinima.value) || 6;
  tablaEstudiantes.innerHTML = '';
//muestra mensaje si no hay dato
if (estudiantes.length === 0) {
  tablaEstudiantes.innerHTML = '<tr><td colspan="6" style="text-align: center;">No hay estudiantes ingresados aún.</td></tr>';
  resumenGrupo.innerHTML = '<h3>Resumen del Grupo</h3><p>Total estudiantes: 0 | Aprobados: 0 | Desaprobados: 0 | Promedio General: 0.00</p>';
  return;
}
  let sumaPromediosTotales = 0;
  let contadorAprobados = 0;
  let contadorDesaprobados = 0;

  estudiantes.forEach((estudiante) => {
    const promedio = calcularPromedio(estudiante.notas);
    const aprobado = estaAprobado(promedio, notaMinima);
    const calificacion = obtenerCalificacion(promedio);

    sumaPromediosTotales += promedio;
    if (aprobado) contadorAprobados++;
    else contadorDesaprobados++;

    // Crear fila HTML
    const fila = document.createElement('tr');
    fila.innerHTML = `
      <td>${estudiante.nombre}</td>
      <td>${estudiante.notas.join(', ')}</td>
      <td>${promedio.toFixed(2)}</td>
      <td>${calificacion}</td>
      <td class="${aprobado ? 'aprobado' : 'desaprobado'}">
        ${aprobado ? 'Aprobado' : 'Desaprobado'}
      </td>
      <td>
        <button class="btn-delete" onclick="eliminarEstudiante(${estudiante.id})">✕</button>
      </td>
    `;
    tablaEstudiantes.appendChild(fila);
  });

  // Actualizar Resumen
  const promedioGeneral = sumaPromediosTotales / estudiantes.length;
  resumenGrupo.innerHTML = `
    <h3>Resumen del Grupo</h3>
    <p>
      Total estudiantes: <strong>${estudiantes.length}</strong> | 
      Aprobados: <strong style="color:green;">${contadorAprobados}</strong> | 
      Desaprobados: <strong style="color:red;">${contadorDesaprobados}</strong> | 
      Promedio General: <strong>${promedioGeneral.toFixed(2)}</strong>
    </p>
  `;
}

// eliminar estudiante por ID
window.eliminarEstudiante = function(id) {
  estudiantes = estudiantes.filter(e => e.id !== id);
  actualizarInterfaz();
};