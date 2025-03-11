<?php
// Configuración de la base de datos
$servidor = "localhost"; // Cambia si es necesario
$usuario_db = "root"; // Usuario de MySQL
$password_db = ""; // Contraseña de MySQL (deja vacío si no tiene)
$base_datos = "bar_management"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar la codificación de caracteres
$conn->set_charset("utf8mb4");
?>
