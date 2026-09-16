export function calcularPromedio(notas) {
    return notas.reduce((a, b) => a + b, 0) / notas.length;
}

export const VERSION = '1.0.0';

export default class Estudiante {
    constructor(nombre, notas) {
        this.nombre = nombre;
        this.notas = notas;
    } 
    getPromedio() {
        return calcularPromedio(this.notas);
    }
}   