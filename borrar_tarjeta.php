<?php

try{
    $db = new SQLite3('visa.db');    //Conectar a SQLite

    //Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $num_Tarjeta = $_POST['numero-tarjeta'];
    $cvv = $_POST['cvv'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];

    // Hacemos la consulta de SQL usando las sentencias preparadas

    $stmt = $db -> prepare("SELECT * FROM Tarjetas WHERE num_tarjeta = :num_tarjeta");
    $stmt -> bindValue(':num_tarjeta', $num_Tarjeta, SQLITE3_TEXT);
    $variable = $stmt -> execute();

    $tarjeta = $variable -> fetchArray(SQLITE3_ASSOC);


    if($tarjeta){


        $deletestmt = $db -> prepare("DELETE FROM tarjetas WHERE num_tarjeta = :num_tarjeta 
                                            AND nombre = :nombre
                                            AND cvv = :cvv
                                            AND fecha_venc = :fecha_vencimiento");
        $deletestmt -> bindValue(':num_tarjeta', $num_Tarjeta, SQLITE3_TEXT);
        $deletestmt -> bindValue(':nombre', $nombre, SQLITE3_TEXT);
        $deletestmt -> bindValue(':cvv', $cvv, SQLITE3_TEXT);
        $deletestmt -> bindValue(':fecha_vencimiento', $fecha_vencimiento, SQLITE3_TEXT);
            if($deletestmt -> execute()){
                echo "La tarjeta ha sido eliminada exitosamente.";
            } else {
                echo "Hubo un error en la eliminación, revise los datos";
            }

    }
    else {
        echo "El numero de la tarjeta no existe, vuelva a intentar";
    }
    
} catch (Exception $e){
    echo "Hubo un error: ". $e->getMessage();
}
?>
