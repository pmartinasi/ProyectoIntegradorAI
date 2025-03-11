<?php
session_start();
require_once "config.php";

// Verificar si el usuario ha iniciado sesión y es un empleado
if (!isset($_SESSION["id"]) || $_SESSION["rol"] !== "empleado") {
    header("Location: index.php");
    exit;
}

// Obtener las mesas desde la base de datos
$sql = "SELECT id, estado FROM mesas";
$result = $conn->query($sql);

$mesas = [];
while ($row = $result->fetch_assoc()) {
    $mesas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleado - Gestión de Mesas</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .tabs { display: flex; justify-content: center; margin-bottom: 20px; }
        .tab { padding: 10px 20px; border: 2px solid black; cursor: pointer; }
        .active { background-color: gray; color: white; }
        .content { display: none; }
        #nuevo-pedido { display: block; } /* Muestra por defecto */
        
        .mesas-container { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
        .mesa {
            width: 60px; height: 60px; border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            border: 2px solid gray; cursor: pointer;
            transition: 0.3s;
        }
        .mesa.ocupada { background-color: gray; }
        .mesa.disponible { background-color: green; }
        .mesa.seleccionada { border: 3px solid blue; }
        
        #comenzar-pedido { margin-top: 20px; padding: 10px; font-size: 16px; cursor: pointer; }
    </style>
</head>
<body>

    <!-- Pestañas -->
    <div class="tabs">
        <div class="tab active" onclick="mostrarSeccion('nuevo-pedido')">Nuevo Pedido</div>
        <div class="tab" onclick="mostrarSeccion('control-mesas')">Control Mesas</div>
        <div class="tab" onclick="mostrarSeccion('configuracion')">Configuración</div>
    </div>

    <!-- Contenido de cada pestaña -->
    <div id="nuevo-pedido" class="content">
        <h2>Selecciona una mesa</h2>
        <div class="mesas-container">
            <?php foreach ($mesas as $mesa): ?>
                <div class="mesa <?= $mesa['estado'] == 'ocupada' ? 'ocupada' : 'disponible' ?>" 
                     data-id="<?= $mesa['id'] ?>" 
                     onclick="seleccionarMesa(this)">
                </div>
            <?php endforeach; ?>
        </div>
        <button id="comenzar-pedido" onclick="iniciarPedido()">Comenzar Pedido</button>
    </div>

    <div id="control-mesas" class="content">
        <h2>Control de Mesas</h2>
        <p>Aquí irá la gestión de estado de las mesas.</p>
    </div>

    <div id="configuracion" class="content">
        <h2>Configuración</h2>
        <p>Opciones de configuración del empleado.</p>
    </div>

    <script>
        function mostrarSeccion(id) {
            document.querySelectorAll('.content').forEach(div => div.style.display = 'none');
            document.getElementById(id).style.display = 'block';
            
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
        }

        let mesaSeleccionada = null;

        function seleccionarMesa(elemento) {
            if (elemento.classList.contains('ocupada')) return;

            document.querySelectorAll('.mesa').forEach(mesa => mesa.classList.remove('seleccionada'));
            elemento.classList.add('seleccionada');
            mesaSeleccionada = elemento.getAttribute('data-id');
        }

        function iniciarPedido() {
            if (!mesaSeleccionada) {
                alert("Selecciona una mesa antes de continuar.");
                return;
            }
            window.location.href = `pedido.php?mesa_id=${mesaSeleccionada}`;
        }
    </script>

</body>
</html>
