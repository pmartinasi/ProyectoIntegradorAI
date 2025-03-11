<?php
session_start();
require_once "config.php"; // Archivo con la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = trim($_POST["password"]);

    if (!empty($usuario) && !empty($password)) {
        $sql = "SELECT id, nombre_usuario, contraseña_hash, rol FROM usuarios WHERE nombre_usuario = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $nombre_usuario, $contraseña_hash, $rol);
                $stmt->fetch();

                if (password_verify($password, $contraseña_hash)) {
                    $_SESSION["id"] = $id;
                    $_SESSION["usuario"] = $nombre_usuario;
                    $_SESSION["rol"] = $rol;

                    // Redirigir según el rol
                    switch ($rol) {
                        case 'admin':
                            header("Location: admin.php");
                            break;
                        case 'gerente':
                            header("Location: gerente.php");
                            break;
                        case 'empleado':
                            header("Location: empleado.php");
                            break;
                        default:
                            session_destroy();
                            die("Rol no reconocido.");
                    }
                    exit;
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "Usuario no encontrado.";
            }
            $stmt->close();
        }
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}
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
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
</form>

</body>
</html>
