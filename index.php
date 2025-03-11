<?php
session_start();
require 'config.php'; // Conexión a la base de datos


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT  id,	nombre_usuario,	contraseña_hash, email,	rol, id_empleado FROM usuarios WHERE  nombre_usuario = ?");
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Verificar la contraseña con password_verify
        if (password_verify($password, $row['contraseña_hash'])) {
            $_SESSION['id_usuario'] = $row['id_usuario'];
            $_SESSION['nombre_usuario'] = $row['nombre_usuario'];
            $_SESSION['rol'] = $row['rol'];

            // Redirigir según el rol
            if ($row['rol'] == 'admin') {
                header("Location: admin.php");
            } elseif ($row['rol'] == 'gerente') {
                header("Location: gerente.php");
            } elseif ($row['rol'] == 'empleado') {
                header("Location: empleado.php");
            }
            exit();
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
    $stmt->close();
    $conn->close();
}
$hashed_password = password_hash("1234", PASSWORD_BCRYPT);


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bar Management</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: #f4f4f4; padding: 20px; border-radius: 5px; width: 300px; }
        input, button { width: 100%; margin-bottom: 10px; padding: 8px; }
    </style>
</head>
<body>
<form method="POST" action="index.php">
    <h2>Iniciar Sesión</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <input type="text" name="nombre_usuario" id="nombre_usuario" placeholder="Usuario" required>
    <input type="password" name="password" id="password" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
</form>

</body>
</html>
