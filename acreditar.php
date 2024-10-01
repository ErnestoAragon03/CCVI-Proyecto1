<?php

try{
    $db = new SQLite3('visa.db');    //Conectar a SQLite

    //Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $num_Tarjeta = $_POST['numero-tarjeta'];
    $cvv = $_POST['cvv'];
    $monto_acreditar = $_POST['monto_acreditar'];

    // Hacemos la consulta de SQL usando las sentencias preparadas

    $stmt = $db -> prepare("SELECT * FROM Tarjetas WHERE num_tarjeta = :num_tarjeta");
    $stmt -> bindValue(':num_tarjeta', $num_Tarjeta, SQLITE3_TEXT);
    $variable = $stmt -> execute();

    $tarjeta = $variable -> fetchArray(SQLITE3_ASSOC);


    if($tarjeta){

        $nuevosaldo = $tarjeta['total'] + $monto_acreditar;
        
        $actualizacionstmt = $db -> prepare("UPDATE Tarjetas SET total = :nuevo_total WHERE num_tarjeta = :num_tarjeta");
        $actualizacionstmt -> bindValue(':nuevo_total', $nuevosaldo, SQLITE3_FLOAT);
        $actualizacionstmt -> bindValue(':num_tarjeta', $num_Tarjeta, SQLITE3_TEXT);

        if($actualizacionstmt -> execute()){

            $pagotransacstmt = $db -> prepare("INSERT INTO transacciones (num_tarjeta, nombre, tipo, monto, fecha)
                                               VALUES (:num_tarjeta, :nombre, 'acreditacion', :monto, CURRENT_TIMESTAMP)");
            $pagotransacstmt -> bindValue(':num_tarjeta', $num_Tarjeta, SQLITE3_TEXT);
            $pagotransacstmt -> bindValue(':nombre', $nombre, SQLITE3_TEXT);
            $pagotransacstmt -> bindValue(':monto', $monto_acreditar, SQLITE3_TEXT);

            if($pagotransacstmt -> execute()){
                echo "El saldo se acredito exitosamente. Su saldo actual es de:" . $nuevosaldo;
            } else {
                echo "Hubo un error en su transacción";
            }

        } else {
            echo "Hubo un error al actualizar su saldo, lo sentimos";
        }
    } else {
        echo "El numero de la tarjeta no existe, vuelva a intentar";
    }
    
} catch (Exception $e){
    echo "Lo sentimos :( hubo un error en la acreditación: ". $e->getMessage();
}
?>