<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Control de Alumnos</title>
</head>
<body>

    <?php
    // Arreglo original de alumnos
    $alumnos = [
        ["Ana Garcia", "Sistemas", [5, 5, 5]],
        ["Juan Perez", "Redes", [6, 8, 7]],
        ["Maria Lopez", "Sistemas", [5, 9, 5]],
        ["Carlos Ruiz", "Redes", [7, 5, 5]]
    ];

    /**
     * Función para calcular el promedio de un arreglo de notas
     * Devuelve el promedio formateado con dos decimales separados por coma
     */
    function calcularPromedio($notas) {
        if (count($notas) === 0) {
            return "0,00";
        }
        $promedio_crudo = array_sum($notas) / count($notas);
        return number_format($promedio_crudo, 2, ',', '');
    }

    /**
     * Función para procesar la lista de alumnos, mostrar sus datos,
     * contar aprobados/desaprobados y determinar el mejor promedio.
     */
    function procesarYContarAlumnos($listaAlumnos) {
        $aprobados = 0;
        $desaprobaron = 0;
        $notaMinimaAprobacion = 6.00;

        // Variables para realizar el seguimiento del mejor alumno
        $mejorPromedio = -1; 
        $mejorAlumnoNombre = "";
        $mejorAlumnoCarrera = "";

        echo "<h3>Listado de Alumnos:</h3>";

        foreach ($listaAlumnos as $alumno) {
            $nombre = $alumno[0];
            $carrera = $alumno[1];
            $notas = $alumno[2];

            // Obtener el promedio formateado en texto (con coma)
            $promedioFormateado = calcularPromedio($notas);

            // Convertir a flotante (con punto) para realizar las evaluaciones matemáticas
            $promedioFlotante = (float)str_replace(',', '.', $promedioFormateado);

            // 1. Evaluar si aprobó o desaprobó
            if ($promedioFlotante >= $notaMinimaAprobacion) {
                $aprobados++;
                $estado = "Aprobado";
            } else {
                $desaprobaron++;
                $estado = "Desaprobado";
            }

            // 2. Evaluar si es el mejor promedio actual
            if ($promedioFlotante > $mejorPromedio) {
                $mejorPromedio = $promedioFlotante;
                $mejorAlumnoNombre = $nombre;
                $mejorAlumnoCarrera = $carrera;
            }

            echo "Nombre: $nombre | Carrera: $carrera | Promedio: $promedioFormateado ($estado)<br>";
        }

        // Volver a formatear el mejor promedio con coma para la presentación visual final
        $mejorPromedioFormateado = number_format($mejorPromedio, 2, ',', '');

        // Mostrar el balance final y el mejor alumno
        echo "<h3>Resumen de Calificaciones:</h3>";
        echo "Alumnos Aprobados: <strong>$aprobados</strong><br>";
        echo "Alumnos Desaprobados: <strong>$desaprobaron</strong><br>";

        echo "<h3> Mejor Promedio:</h3>";
        echo "Nombre: <strong>$mejorAlumnoNombre</strong><br>";
        echo "Carrera: $mejorAlumnoCarrera<br>";
        echo "Promedio: <strong>$mejorPromedioFormateado</strong><br>";
    }

    // Ejecutar la función principal pasando el array de alumnos
    procesarYContarAlumnos($alumnos);
    ?>

</body>
</html>
