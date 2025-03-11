<?php
session_start();
require_once "config.php";

// Obtener productos y su cantidad de pedidos anteriores
$sql_productos = "SELECT p.id, p.nombre, COALESCE(SUM(pp.cantidad), 0) AS total_pedidos 
                  FROM productos p 
                  LEFT JOIN pedido_productos pp ON p.id = pp.producto_id 
                  GROUP BY p.id 
                  ORDER BY total_pedidos DESC";
$result_productos = $conn->query($sql_productos);
$productos = [];

while ($row = $result_productos->fetch_assoc()) {
    $productos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 0; padding: 0; }
        .tabs { display: flex; background: #ddd; padding: 10px; }
        .tab { flex: 1; padding: 10px; text-align: center; cursor: pointer; background: #ccc; border: 1px solid #aaa; }
        .tab.active { background: #666; color: white; font-weight: bold; }
        .container { display: flex; justify-content: space-around; margin-top: 20px; padding: 20px; }
        .column { width: 30%; padding: 10px; border: 1px solid black; text-align: left; }
        .lista-productos { max-height: 300px; overflow-y: auto; }
        .producto { display: flex; justify-content: space-between; padding: 5px; }
        .boton { display: block; width: 100%; padding: 10px; margin-top: 10px; background: blue; color: white; font-size: 16px; cursor: pointer; text-align: center; }
    </style>
</head>
<body>

    <!-- Pestañas de navegación -->
    <div class="tabs">
        <div class="tab active">Nuevo Pedido</div>
        <div class="tab" onclick="location.href='control-mesas.php'">Control Mesas</div>
        <div class="tab" onclick="location.href='configuracion.php'">Configuración</div>
    </div>

    <h2>Nuevo Pedido</h2>

    <div class="container">
        <!-- Lista de los productos más pedidos -->
        <div class="column">
            <h3>Más pedidos</h3>
            <div class="lista-productos">
                <?php foreach ($productos as $producto): ?>
                    <div class="producto">
                        <span><?= $producto['nombre'] ?> (<?= $producto['total_pedidos'] ?>)</span>
                        <input type="number" min="0" value="0" data-id="<?= $producto['id'] ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Lista de productos disponibles -->
        <div class="column">
            <h3>Seleccione comanda</h3>
            <div class="lista-productos">
                <?php foreach ($productos as $producto): ?>
                    <div class="producto">
                        <span><?= $producto['nombre'] ?></span>
                        <input type="number" min="0" value="0" data-id="<?= $producto['id'] ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Buscador de productos -->
        <div class="column">
            <h3>Buscar</h3>
            <input type="text" id="buscar" placeholder="Escriba para buscar">
            <div id="resultados" class="lista-productos"></div>
            <button class="boton" onclick="enviarPedido()">Confirmar Pedido</button>
        </div>
    </div>

    <script>
        document.getElementById("buscar").addEventListener("input", function () {
            let filtro = this.value.toLowerCase();
            let resultados = document.getElementById("resultados");
            resultados.innerHTML = "";

            document.querySelectorAll(".producto").forEach(function (producto) {
                let nombre = producto.querySelector("span").textContent.toLowerCase();
                if (nombre.includes(filtro)) {
                    let clon = producto.cloneNode(true);
                    resultados.appendChild(clon);
                }
            });
        });

        function enviarPedido() {
            let pedido = {};
            document.querySelectorAll(".producto input").forEach(input => {
                let cantidad = parseInt(input.value);
                if (cantidad > 0) {
                    let id = input.getAttribute("data-id");
                    pedido[id] = cantidad;
                }
            });

            if (Object.keys(pedido).length === 0) {
                alert("Seleccione al menos un producto.");
                return;
            }

            fetch("guardar_pedido.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(pedido)
            }).then(response => {
                if (response.ok) {
                    window.location.href = "control-mesas.php";
                } else {
                    alert("Error al guardar el pedido.");
                }
            }).catch(error => console.error("Error:", error));
        }
    </script>

</body>
</html>
