<?php
//Conectar a SQLite
$db = new SQLite3('visa.db');

//Crear Tabla  Tarjetas
$db -> exec("CREATE TABLE IF NOT EXISTS Tarjetas (
    num_tarjeta INTEGER PRIMARY KEY,
    nombre VARCHAR(40),
    fecha_venc DATE,
    cvv INTEGER,
    monto_autorizado REAL,
    total REAL
)");

//Crear Tabla Transacciones
$db -> exec("CREATE TABLE IF NOT EXISTS transacciones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    num_tarjeta INTEGER,
    nombre VARCHAR(40),
    tipo VARCHAR(8),
    tienda VARCHAR(50),
    monto REAL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

echo "Base da datos y tablas creadas exitosamente";

$db->close();
?>