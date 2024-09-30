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
if (!$tarjeta || !$nombre || !$fecha_venc || !$num_seguridad || !$monto || !$tienda || !$formato) {
    http_response_code(400); //Devolver error
    echo "Faltan parámetros dentro de la solicitud";
    exit();
}
try {
    $stmt = $db->prepare('SELECT * FROM tarjetas 
                        WHERE num_tarjeta = :tarjeta 
                        AND nombre = :nombre 
                        AND fecha_venc = :fecha_venc 
                        AND cvv = :num_seguridad 
                        AND monto_autorizado >= :monto
                        AND total >= :monto');
    $stmt->bindParam(':tarjeta', $tarjeta);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':fecha_venc', $fecha_venc);
    $stmt->bindParam(':num_seguridad', $num_seguridad);
    $stmt->bindParam(':monto', $monto);
    
    $stmt->execute();
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        //La tarjeta fue encontrada, continuar el proceso
        //Preparar datos
        $datos = [
            "tarjeta" => $tarjeta,
            "nombre"=> $nombre,
            "fecha_venc"=> $fecha_venc,
            "num_seguridad"=> $num_seguridad,
            "monto"=> $monto,
            "tienda" => $tienda
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