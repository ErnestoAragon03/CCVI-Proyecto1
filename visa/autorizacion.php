<?php
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
?>