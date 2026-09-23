
import { calcularPromedio, estaAprobado, VERSION }  from './utils-3.js';

// Array estudiantes
const estudiantes = [
  { nombre: "Lorena", notas: [8, 9, 7] },
  { nombre: "Matías", notas: [4, 5, 3] },
  { nombre: "Sonia", notas: [6, 7, 8] },
  { nombre: "Ramón", notas: [2, 5, 4] },
  { nombre: "Carmen", notas: [9, 10, 8] }
];

// Map
const estudiantesConPromedio = estudiantes.map(estudiante => {
  return {
    nombre: estudiante.nombre,
    promedio: calcularPromedio(estudiante.notas)
  };
});

// Filter
const aprobados = estudiantesConPromedio.filter(estudiante => {
  return estaAprobado(estudiante.promedio);
});

// consola
console.log("--- Promedio estudiante ---");
console.log(estudiantesConPromedio);

console.log("---Aprobados ---");
console.log(aprobados);