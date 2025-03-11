<?php
session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data)) {
        echo "Error: No hay datos de pedido.";
        exit;
    }

    $mesa_id = rand(1, 10); // Simulación: Se asigna una mesa aleatoria (puedes cambiar esto)
    $fecha_pedido = date("Y-m-d H:i:s");
    $estado = "ocupada";

    // Insertar el pedido en la base de datos
    $sql_pedido = "INSERT INTO pedidos (mesa_id, fecha_pedido, estado) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_pedido);
    $stmt->bind_param("iss", $mesa_id, $fecha_pedido, $estado);
    $stmt->execute();
    $pedido_id = $stmt->insert_id;
    $stmt->close();

    // Insertar los productos pedidos
    $sql_producto = "INSERT INTO pedido_productos (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_producto);

    foreach ($data as $producto_id => $cantidad) {
        $stmt->bind_param("iii", $pedido_id, $producto_id, $cantidad);
        $stmt->execute();
    }
    $stmt->close();

    // Marcar la mesa como ocupada
    $sql_update_mesa = "UPDATE mesas SET estado = 'ocupada' WHERE id = ?";
    $stmt = $conn->prepare($sql_update_mesa);
    $stmt->bind_param("i", $mesa_id);
    $stmt->execute();
    $stmt->close();

    // Redirigir al control de mesas
    header("Location: control-mesas.php");
    exit;
} else {
    echo "Acceso no válido.";
}
?>
