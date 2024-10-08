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
///////////////////////////////URL//////////////////////////////
//Obtener parámetros de la URL
$tarjeta = $_GET['tarjeta'] ?? null;
$nombre = $_GET['nombre'] ?? null;
$fecha_venc = $_GET['fecha_venc'] ?? null;
$num_seguridad = $_GET['num_seguridad'] ?? null;
$monto = $_GET['monto'] ?? null;
$tienda = $_GET['tienda'] ?? null;
$formato = $_GET['formato'] ?? null;
//Confirmar que todos los datos están presentes
if (!$tarjeta || !$nombre || !$fecha_venc || !$num_seguridad || !$monto || !$tienda || ($formato != 'xml' && $formato != 'json')) {
    http_response_code(400); //Devolver error
    echo "Faltan parámetros dentro de la solicitud o el formato es inválido";
    exit();
}
try {
    $stmt = $db->prepare('SELECT * FROM tarjetas 
                        WHERE num_tarjeta = :tarjeta 
                        AND nombre = :nombre 
                        AND fecha_venc = :fecha_venc 
                        AND cvv = :num_seguridad');

    $stmt->bindParam(':tarjeta', $tarjeta);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':fecha_venc', $fecha_venc);
    $stmt->bindParam(':num_seguridad', $num_seguridad);
    
    $stmt->execute();
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        //La tarjeta fue encontrada, continuar el proceso

        // Verificar si el monto es menor que el saldo total y el monto autorizado
        if ($monto <= $resultado['total'] && $monto <= $resultado['monto_autorizado']) {
            // Restar el monto del saldo total
            $nuevo_saldo = $resultado['total'] - $monto;

            // Actualizar el saldo en la base de datos
            $update_stmt = $db->prepare('UPDATE tarjetas SET total = :nuevo_saldo WHERE num_tarjeta = :tarjeta');
            $update_stmt->bindParam(':nuevo_saldo', $nuevo_saldo);
            $update_stmt->bindParam(':tarjeta', $tarjeta);
            $update_stmt->execute();
            
            $status = "APROBADO";
            // Aprobar la transacción

            // Registrar la transacción en la tabla transacciones
            $insert_stmt = $db->prepare('INSERT INTO transacciones (num_tarjeta, nombre, tipo, tienda, monto)' 
                                                . 'VALUES (:tarjeta, :nombre, "consumo", :tienda ,:monto)');
            $insert_stmt->bindParam(':tarjeta', $tarjeta);
            $insert_stmt->bindParam(':nombre', $nombre);
            $insert_stmt->bindParam(':monto', $monto);
            $insert_stmt->bindParam(':tienda', $tienda);
            $insert_stmt->execute();
            $numero = $db->lastInsertId();
            /*
            $stmt = $db->prepare('SELECT id
                              FROM transacciones 
                              WHERE num_tarjeta = :tarjeta 
                              AND nombre = :titular 
                              AND tipo = :tipo,
                              AND tienda = :tienda');
            $insert_stmt->bindParam(':tarjeta', $tarjeta);
            $insert_stmt->bindParam(':nombre', $nombre);
            $insert_stmt->bindParam(':monto', $monto);
            $insert_stmt->bindParam(':tienda', $tienda);
            $insert_stmt->execute();*/
        } else {
            // Rechazar la transacción e indicar el motivo
            if ($monto > $resultado['total']) {
                //echo "<p>Transacción rechazada: Fondos insuficientes.</p>";
            }
            else if ($monto > $resultado['monto_autorizado']) {
                //echo "<p>Transacción rechazada: El monto autorizado es menor a la compra.</p>";
            }
            $status = "DENEGADO";
            $numero = 0;
        }

        //Preparar datos
        $datos = [
            "emisor" => 'visa',
            "tarjeta" => $tarjeta,
            "status"=> $status,
            "numero"=> $numero,
        ];
        /////////////Seleccionar tipo de formato////////////////
        if($formato === 'json') {
        //Formato JSON
        header('Content-Type: application/json');
        echo json_encode($datos);
        } elseif($formato === 'xml') {
        //Formato XML
        header('Content-Type: application/xml');
        $xml = new SimpleXMLElement('<autorizacion/>');
        array_walk_recursive($datos, function($value, $key) use ($xml) {
        $xml->addChild($key, $value);
        });
        echo $xml->asXML();
        } else {
        //Formato inválido
        http_response_code(400); //Devolver error
        echo "Formato inválido";
        }
    }
    else {
        //La tarjeta no fue encontrada
        http_response_code(404); // Not Found
        echo "Tarjeta no encontrada.";
    }
} catch (PDOException $e) {
    echo 'Error al consultar la base de datos: ' .$e->getMessage();
}

?>