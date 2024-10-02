<?php
// Ruta al archivo de la base de datos SQLite
$db_path = './visa.db';

// Conectar a la base de datos SQLite
try {
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
    exit;
}

// Obtener los valores de la URL (número de tarjeta y titular)
$tarjeta = $_GET['tarjeta'] ?? null;
$titular = $_GET['titular'] ?? null;

if ($tarjeta && $titular) {
    // Realizar la consulta para obtener las transacciones de la tarjeta y el titular
    try {
        $stmt = $db->prepare('SELECT tipo, monto, fecha, tienda 
                              FROM transacciones 
                              WHERE num_tarjeta = :tarjeta 
                              AND nombre = :titular 
                              ORDER BY fecha DESC');
        // Asignar los valores a los parámetros
        $stmt->bindParam(':tarjeta', $tarjeta);
        $stmt->bindParam(':titular', $titular);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los resultados
        $transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Estilo CSS integrado
        echo "
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }
            h1 {
                color: #003399;
                text-align: center;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                background-color: #ffffff;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th, td {
                padding: 12px;
                text-align: left;
            }
            th {
                background-color: #003399;
                color: white;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            tr:hover {
                background-color: #e2e2ff;
            }
            .info {
                margin: 20px 0;
                padding: 10px;
                background-color: #e7f3fe;
                border-left: 6px solid #2196F3;
                font-size: 16px;
            }
            .no-transactions {
                text-align: center;
                padding: 20px;
                background-color: #fff3cd;
                border-left: 6px solid #ffeb3b;
            }
            .back-btn {
                display: inline-block;
                padding: 10px 20px;
                margin-top: 20px;
                background-color: #003399;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-size: 16px;
                text-align: center;
            }
            .back-btn:hover {
                background-color: #002080;
            }
            .button-container {
                text-align: center;
                margin-top: 30px;
            }
        </style>";

        if ($transacciones) {
            echo "<h1>Estado de Cuenta de la Tarjeta</h1>";
            echo "<div class='info'><strong>Número de Tarjeta:</strong> $tarjeta</div>";
            echo "<div class='info'><strong>Titular:</strong> $titular</div>";

            // Mostrar las transacciones
            echo "<table>";
            echo "<tr><th>Tipo</th><th>Tienda</th><th>Monto</th><th>Fecha</th></tr>";
            
            foreach ($transacciones as $transaccion) {
                echo "<tr>";
                echo "<td>{$transaccion['tipo']}</td>";
                echo "<td>{$transaccion['tienda']}</td>";
                echo "<td>Q {$transaccion['monto']}</td>";
                echo "<td>{$transaccion['fecha']}</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<div class='no-transactions'><strong>No se encontraron transacciones para esta tarjeta y titular.</strong></div>";
        }

        // Botón de regresar
        echo "
        <div class='button-container'>
            <a href='index.html' class='back-btn'>Regresar</a>
        </div>";
    } catch (PDOException $e) {
        echo "Error al realizar la consulta: " . $e->getMessage();
    }
} else {
    echo "<div class='no-transactions'><strong>Faltan uno o más parámetros en el formulario.</strong></div>";
}
?>