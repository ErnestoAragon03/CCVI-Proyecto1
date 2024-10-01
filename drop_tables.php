<?php
//Conectar a SQLite
$db = new SQLite3('visa.db');

$db -> exec("DROP TABLE TARJETAS");

$db -> exec("DROP TABLE TRANSACCIONES");

echo "Tablas eliminadas exitosamente";

$db->close();
?>