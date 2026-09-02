# Sistema de Gestión de Estudiantes

Un sistema CRUD (Crear, Leer, Actualizar, Eliminar) desarrollado en **PHP** y **MySQL** para la gestión básica de alumnos. El proyecto está construido bajo el paradigma de **Programación Orientada a Objetos (POO)** utilizando la extensión `mysqli` con consultas preparadas (*prepared statements*) para garantizar la protección de datos ante inyecciones SQL. La interfaz gráfica se gestiona de forma ligera a través de **Pico.css**.

---

## Tecnologías Utilizadas

* **Lenguaje:** PHP + HTML
* **Estilos:** Pico.css
* **Base de Datos:** MySQL / MariaDB (Administrado vía phpMyAdmin)
* **Entorno de Desarrollo:** Linux / Visual Studio Code
* **Control de Versiones:** Git / GitHub

---

## Estructura del Proyecto

```text
lgi-2026/
└── proyecto/
    ├── config/
    │   └── database.php    # Configuración de conexión POO a MySQL
    ├── crear.php           # Formulario para registrar estudiantes
    ├── editar.php          # Formulario para actualizar estudiantes
    ├── eliminar.php        # Procesamiento de borrado lógico
    ├── index.php           # Listado principal, búsqueda y paginación
    └── README.md           # Documentación del proyecto

## Autor. Algarin Faustino-https://github.com/faustinora/lgi-2026