
export function calcularPromedio(notas) {
  const suma = notas.reduce((acumulador, nota) => acumulador + nota, 0);
  return suma / notas.length;
}

export const VERSION = '1.0.0';


export function estaAprobado(nota, minima = 6) {
  return nota >= minima;
}