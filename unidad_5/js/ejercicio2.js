
function calcularPromedio(notas) {
  const suma = notas.reduce((acumulador, nota) => acumulador + nota, 0);
  return suma / notas.length;
}

function estaAprobado(nota, minima = 6) {
  return nota >= minima;
}

const estudiantes = [
  { nombre: "Lucía", notas: [8, 9, 7] },
  { nombre: "Mateo", notas: [4, 5, 3] },
  { nombre: "Sofia", notas: [6, 7, 8] },
  { nombre: "Tomás", notas: [2, 5, 4] },
  { nombre: "Elena", notas: [9, 10, 8] }
];

const estudiantesConPromedio = estudiantes.map(estudiante => {
  return {
    nombre: estudiante.nombre,
    promedio: calcularPromedio(estudiante.notas)
  };
});

const aprobados = estudiantesConPromedio.filter(estudiante => {
  return estaAprobado(estudiante.promedio);
});

console.log("--- Promedio ---");
console.log(estudiantesConPromedio);

console.log("--- Aprobados ---");
console.log(aprobados);