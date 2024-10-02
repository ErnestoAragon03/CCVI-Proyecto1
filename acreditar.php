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

            $pagotransacstmt = $db -> prepare("INSERT INTO transacciones (num_tarjeta, nombre, tipo, tienda, monto, fecha)
                                               VALUES (:num_tarjeta, :nombre, 'pagos', 'ingreso', :monto, CURRENT_TIMESTAMP)");
            $pagotransacstmt -> bindValue(':num_tarjeta', $num_Tarjeta, SQLITE3_TEXT);
            $pagotransacstmt -> bindValue(':nombre', $nombre, SQLITE3_TEXT);
            $pagotransacstmt -> bindValue(':monto', $monto_acreditar, SQLITE3_TEXT);

            if($pagotransacstmt -> execute()){
                echo "<div style='font-family: Arial, sans-serif; font-size: 18px; color: green; text-align: center; margin-top: 20px;'>
                        <strong>¡El saldo se acreditó exitosamente!</strong><br>
                        <p>Su saldo actual es de: <span style='font-size: 20px; font-weight: bold;'>" . $nuevosaldo . "</span></p>
                      </div>";
            } else {
                echo "<div style='font-family: Arial, sans-serif; font-size: 18px; color: red; text-align: center; margin-top: 20px;'>
                        <strong>Hubo un error en su transacción</strong>
                      </div>";
            }

        } else {
            echo "<div style='font-family: Arial, sans-serif; font-size: 18px; color: red; text-align: center; margin-top: 20px;'>
                    <strong>Hubo un error al actualizar su saldo, lo sentimos</strong>
                  </div>";
        }
    } else {
        echo "<div style='font-family: Arial, sans-serif; font-size: 18px; color: red; text-align: center; margin-top: 20px;'>
                <strong>El número de la tarjeta no existe, vuelva a intentar</strong>
              </div>";
    }
    
} catch (Exception $e){
    echo "<div style='font-family: Arial, sans-serif; font-size: 18px; color: red; text-align: center; margin-top: 20px;'>
            <strong>Lo sentimos :( hubo un error en la acreditación: </strong><br>" . $e->getMessage() . "
          </div>";
}
?>


<div class="acredit-form">
    <form action="acreditar.html">
        <button type="submit" class="generar-btn">Regresar</button>
    </form>
</div>