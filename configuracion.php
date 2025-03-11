<?php
session_start();
require_once "config.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["id"];

// Primero, obtener el id del empleado asociado al usuario
$stmt = $conn->prepare("SELECT id_empleado FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $id_empleado = $row['id_empleado'];
} else {
    die("No se encontró empleado asociado.");
}
$stmt->close();

// Procesar el envío del formulario para actualizar los datos
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre             = $_POST["nombre"];
    $apellido           = $_POST["apellido"];
    $dni                = $_POST["dni"];
    $telefono           = $_POST["telefono"];
    $email              = $_POST["email"];
    $puesto             = $_POST["puesto"];
    $salario            = $_POST["salario"];
    $fecha_contratacion = $_POST["fecha_contratacion"];
    $estado             = $_POST["estado"];

    $stmt = $conn->prepare("UPDATE empleados SET nombre = ?, apellido = ?, dni = ?, telefono = ?, email = ?, puesto = ?, salario = ?, fecha_contratacion = ?, estado = ? WHERE id = ?");
    $stmt->bind_param("sssssdsssi", $nombre, $apellido, $dni, $telefono, $email, $puesto, $salario, $fecha_contratacion, $estado, $id_empleado);
    if ($stmt->execute()) {
        $mensaje = "Datos actualizados correctamente.";
    } else {
        $mensaje = "Error al actualizar los datos.";
    }
    $stmt->close();
}

// Obtener los datos actuales del empleado
$stmt = $conn->prepare("SELECT nombre, apellido, dni, telefono, email, puesto, salario, fecha_contratacion, estado FROM empleados WHERE id = ?");
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$result = $stmt->get_result();
$empleado = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Empleado</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        /* Estilos para la barra de pestañas */
        .tabs {
            display: flex;
            background: #ddd;
            padding: 10px;
        }
        .tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            background: #ccc;
            border: 1px solid #aaa;
        }
        .tab.active {
            background: #666;
            color: white;
            font-weight: bold;
        }
        /* Contenedor del contenido */
        .container {
            padding: 20px;
        }
        form {
            max-width: 500px;
            margin: auto;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        .mensaje {
            text-align: center;
            margin: 10px;
            font-weight: bold;
        }
        button {
            margin-top: 20px;
            padding: 10px 15px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <!-- Barra de pestañas -->
    <div class="tabs">
        <div class="tab" onclick="location.href='pedido.php'">Nuevo Pedido</div>
        <div class="tab" onclick="location.href='control-mesas.php'">Control Mesas</div>
        <div class="tab active">Configuración</div>
    </div>

    <div class="container">
        <h2>Configuración del Empleado</h2>
        <?php if(isset($mensaje)): ?>
            <div class="mensaje"><?= $mensaje ?></div>
        <?php endif; ?>
        <form method="post" action="configuracion.php">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($empleado['nombre']) ?>" required>

            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" value="<?= htmlspecialchars($empleado['apellido']) ?>" required>

            <label for="dni">DNI:</label>
            <input type="text" name="dni" id="dni" value="<?= htmlspecialchars($empleado['dni']) ?>" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($empleado['telefono']) ?>">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($empleado['email']) ?>" required>

            <label for="puesto">Puesto:</label>
            <input type="text" name="puesto" id="puesto" value="<?= htmlspecialchars($empleado['puesto']) ?>" required>

            <label for="salario">Salario:</label>
            <input type="number" step="0.01" name="salario" id="salario" value="<?= htmlspecialchars($empleado['salario']) ?>" required>

            <label for="fecha_contratacion">Fecha de Contratación:</label>
            <input type="date" name="fecha_contratacion" id="fecha_contratacion" value="<?= htmlspecialchars($empleado['fecha_contratacion']) ?>" required>

            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required>
                <option value="activo" <?= $empleado['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $empleado['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>

            <button type="submit">Actualizar Datos</button>
        </form>
    </div>

</body>
</html>
