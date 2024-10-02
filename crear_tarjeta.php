<?php
// Conectar a la base de datos
$db_path = './visa.db';

try {
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
    exit;
}

// Verificar si se han enviado los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $num_tarjeta = $_POST['num_tarjeta'];
    $fecha_venc = $_POST['fecha_venc'];
    $cvv = $_POST['cvv'];
    $monto_autorizado = $_POST['monto_autorizado'];
    $monto_total = $_POST['monto_total'];

    // Validar formato de los datos
    if (strlen($num_tarjeta) == 16 && preg_match('/^\d{6}$/', $fecha_venc) && strlen($cvv) == 3) {
        try {
            // Insertar tarjeta en la base de datos
            $stmt = $db->prepare('INSERT INTO tarjetas (num_tarjeta, nombre, fecha_venc, cvv, monto_autorizado, total)
                VALUES (:num_tarjeta, :nombre, :fecha_venc, :cvv, :monto_autorizado, :total)');
            $stmt->bindParam(':num_tarjeta', $num_tarjeta);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':fecha_venc', $fecha_venc);
            $stmt->bindParam(':cvv', $cvv);
            $stmt->bindParam(':monto_autorizado', $monto_autorizado);
            $stmt->bindParam(':total', $monto_total);

            $stmt->execute();
            echo "<div style='font-family: Arial, sans-serif; font-size: 18px; color: green; text-align: center; margin-top: 20px;'>
                        <strong>¡El saldo se acreditó exitosamente!</strong>
                      </div>";
        } catch (PDOException $e) {
            echo "Error al insertar tarjeta: " . $e->getMessage();
        }
    } else {
        echo "Error en los datos: Asegúrate de que el número de tarjeta sea de 16 dígitos, el CVV de 3 dígitos y la fecha de vencimiento esté en formato correcto.";
    }
}
?>

<button onclick="window.location.href='index.html';">Regresar</button>