// 1. Captura de elementos del DOM
const tablaUsuarios = document.getElementById('tablaUsuarios');
const indicadorCarga = document.getElementById('indicadorCarga');
const btnRecargar = document.getElementById('btnRecargar');
const mensajeError = document.getElementById('mensajeError');

async function obtenerUsuarios() {
  
  indicadorCarga.style.display = 'block';
  tablaUsuarios.innerHTML = '';
  mensajeError.textContent = '';
  btnRecargar.disabled = true;

  try {
    const respuesta = await fetch('https://jsonplaceholder.typicode.com/users');

    if (!respuesta.ok) {
      throw new Error(`HTTP Error: status ${respuesta.status}`);
    }

    const usuarios = await respuesta.json();

    usuarios.forEach((usuario) => {
      const fila = document.createElement('tr');
      fila.innerHTML = `
        <td>${usuario.name}</td>
        <td>${usuario.email}</td>
        <td>${usuario.address.city}</td>
      `;
      tablaUsuarios.appendChild(fila);
    });

  } catch (error) {
    console.error('Error al consultar la API:', error);
    mensajeError.textContent = 'Ocurrió un error al cargar la lista de usuarios. Intente nuevamente.';
  } finally {
    
    indicadorCarga.style.display = 'none';
    btnRecargar.disabled = false;
  }
}

btnRecargar.addEventListener('click', obtenerUsuarios);

obtenerUsuarios();