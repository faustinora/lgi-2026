import Estudiante, { calcularPromedio, VERSION } from './utils.js';

const ana = new Estudiante('Ana', [8, 9, 7, 5]);
console.log(ana.getPromedio());
console.log(calcularPromedio([5,6]));
console.log(VERSION);
