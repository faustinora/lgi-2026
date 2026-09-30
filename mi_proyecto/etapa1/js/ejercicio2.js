// calcular promedio
function calcularPromedio(notas) {
  const suma = notas.reduce((acumulador, nota) => acumulador + nota, 0);
  return suma / notas.length;
}

// verificar aprobado
function estaAprobado(nota, minima = 6) {
  return nota >= minima;
}

// categorias
function obtenerCalificacion(promedio) {
  if (promedio >= 9) {
    return "Sobresaliente";
  } else if (promedio >= 7.5) {
    return "Muy Bueno";
  } else if (promedio >= 6) {
    return "Bueno";
  } else {
    return "Desaprobado";
  }
}

// vector estudiantes
const estudiantes = [
  { nombre: "Luciana", notas: [8, 9, 7, 6] },
  { nombre: "Mateo", notas: [4, 5, 3, 5] },
  { nombre: "Soledad", notas: [6, 7, 8, 6] },
  { nombre: "Facundo", notas: [2, 5, 4, 5] },
  { nombre: "Ramón", notas: [9, 10, 8, 6] }
];

//uso map()
const estudiantesProcesados = estudiantes.map(estudiantes => {
  const promedioCalculado = calcularPromedio(estudiantes.notas);
  return {
    nombre: estudiantes.nombre,
    promedio: promedioCalculado,
    calificacion: obtenerCalificacion(promedioCalculado)
  };
});

// Filtro aprobados
const aprobados = estudiantesProcesados.filter(estudiantes => 
  estaAprobado(estudiantes.promedio)
);

// Resultados
console.log("--- Resultados estudiantes ---");
console.table(estudiantesProcesados);

console.log(`--- Ver Aprobados (${aprobados.length} de ${estudiantes.length}) ---`);
console.table(aprobados);