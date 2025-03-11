<?php
session_start();
require_once "config.php";

// Obtener todas las mesas
$sql_mesas = "SELECT * FROM mesas";
$result_mesas = $conn->query($sql_mesas);
$mesas = [];

while ($row = $result_mesas->fetch_assoc()) {
    $mesas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Mesas</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .mesa-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 20px; }
        .mesa { width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; border: 2px solid black; }
        .libre { background-color: lightgreen; }
        .ocupada { background-color: red; color: white; }

        #info-mesa { display: none; position: absolute; bottom: 20px; right: 20px; width: 250px; padding: 10px; border: 2px solid black; background-color: white; text-align: left; }
    </style>
</head>
<body>

    <h2>Mapa de Mesas</h2>
    <div class="mesa-container">
        <?php foreach ($mesas as $mesa): ?>
            <div class="mesa <?= $mesa['estado'] ?>" onclick="mostrarInfoMesa(<?= $mesa['id'] ?>)">
                Mesa <?= $mesa['id'] ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="info-mesa">
        <h3>Información de Mesa</h3>
        <p><strong>Número:</strong> <span id="num-mesa"></span></p>
        <p><strong>Capacidad:</strong> <span id="capacidad"></span></p>
        <p><strong>Estado:</strong> <span id="estado"></span></p>
        <div id="productos-mesa"></div>
    </div>

    <script>
        function mostrarInfoMesa(mesaId) {
            fetch(`obtener_info_mesa.php?mesa_id=${mesaId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("info-mesa").style.display = "block";
                    document.getElementById("num-mesa").textContent = data.numero;
                    document.getElementById("capacidad").textContent = data.capacidad;
                    document.getElementById("estado").textContent = data.estado;

                    let productosHTML = "<h4>Productos pedidos:</h4><ul>";
                    data.productos.forEach(producto => {
                        productosHTML += `<li>${producto.nombre} (x${producto.cantidad})</li>`;
                    });
                    productosHTML += "</ul>";

                    document.getElementById("productos-mesa").innerHTML = data.estado === "ocupada" ? productosHTML : "<p>Sin pedidos.</p>";
                })
                .catch(error => console.error("Error:", error));
        }
    </script>

</body>
</html>
