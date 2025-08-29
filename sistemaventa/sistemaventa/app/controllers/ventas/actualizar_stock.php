<?php
/**
 * Created by PhpStorm.
 * User: Salazar Phocco Yenifer Fiorela
 */

include ('../../config.php');

$id_producto = $_GET['id_producto'];
$stock_calculado = $_GET['stock_calculado'];

$sentencia = $pdo->prepare("UPDATE tb_almacen SET stock=:stock WHERE id_producto=:id_producto");
$sentencia->bindParam('id_producto',$id_producto);
$sentencia->bindParam('stock',$stock_calculado);

if($sentencia->execute()){
   
    echo "se actualiza todo";
   
}else{
    
    echo "error al actualizar";
  
}


