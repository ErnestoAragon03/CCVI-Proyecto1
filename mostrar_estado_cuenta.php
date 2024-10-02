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

        if ($transacciones) {
            echo "<h1>Estado de Cuenta de la Tarjeta</h1>";
            echo "<p>Número de Tarjeta: $tarjeta</p>";
            echo "<p>Titular: $titular</p>";

            // Mostrar las transacciones
            echo "<table border='1'>";
            echo "<tr><th>Tipo</th><th>Tienda</th><th>Monto</th><th>Fecha</th></tr>";
            
            foreach ($transacciones as $transaccion) {
                echo "<tr>";
                echo "<td>{$transaccion['tipo']}</td>";
                echo "<td>{$transaccion['tienda']}</td>";
                echo "<td>{$transaccion['monto']}</td>";
                echo "<td>{$transaccion['fecha']}</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No se encontraron transacciones para esta tarjeta y titular.</p>";
        }
    } catch (PDOException $e) {
        echo "Error al realizar la consulta: " . $e->getMessage();
    }
} else {
    echo "Faltan uno o más parámetros en el formulario.";
}
?>

<button onclick="window.location.href='index.html';">Regresar</button>