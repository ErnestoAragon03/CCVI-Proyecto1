<?php
//Conectar a SQLite
$db = new SQLite3('visa.db');

$db -> exec("DROP TABLE TARJETAS");

$db -> exec("DROP TABLE Solicitud");

echo "Tablas eliminadas exitosamente";

$db->close();
?>