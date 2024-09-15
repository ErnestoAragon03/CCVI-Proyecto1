<?php
//Conectar a SQLite
$db = new SQLite3('visa.db');

//Crear Tabla  Tarjetas
$db -> exec("CREATE TABLE IF NOT EXISTS Tarjetas (
    num_tarjeta INTEGER PRIMARY KEY,
    nombre VARCHAR(40),
    fecha_venc DATE,
    cvv INTEGER,
    monto_autorizado INTEGER,
    total INTEGER
)");

//Crear Tabla Solicitud
$db -> exec("CREATE TABLE IF NOT EXISTS Solicitud (
    tarjeta INTEGER PRIMARY KEY,
    nombre VARCHAR(40),
    fecha_venc DATE
    num_seguridad INTEGER,
    monto INTEGER,
    autorizacion INTEGER
)");

echo "Base da datos y tablas creadas exitosamente";

$db->close();
?>