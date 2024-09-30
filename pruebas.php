<?php
// Ruta al archivo de la base de datos SQLite
$db_path = './visa.db';

// Conectar a la base de datos SQLite
try {
    $db = new PDO("sqlite:$db_path");
    // Establecer el modo de error a excepciones
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
    exit;
}
$stmt = $db->prepare('INSERT INTO tarjetas (num_tarjeta, nombre, fecha_venc, cvv, monto_autorizado, total)'
    . 'VALUES (1234567891234567, "Ernesto Aragon", "12/24/31", 123, 500.00, 1000.00)');
$stmt->execute();
?>