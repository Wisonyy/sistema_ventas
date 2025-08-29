<?php
/**
 * Created by PhpStorm.
 * User: Salazar Phocco Yenifer Fiorela
 */

include ('../../config.php');

$id_producto = $_POST['id_producto'];

// Iniciar sesión para mostrar mensajes
session_start();

// Verificar si el producto está siendo utilizado en tb_carrito
$sql_check = "SELECT COUNT(*) FROM tb_carrito WHERE id_producto = :id_producto";
$check = $pdo->prepare($sql_check);
$check->execute(['id_producto' => $id_producto]);
$count = $check->fetchColumn();

if ($count > 0) {
    // Si hay registros relacionados, no se puede eliminar
    $_SESSION['mensaje'] = "No se puede eliminar el producto porque está en uso en el carrito.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . $URL . '/almacen/');
    exit();
}

// Si no hay registros relacionados, eliminar el producto
$sentencia = $pdo->prepare("DELETE FROM tb_almacen WHERE id_producto = :id_producto");
$sentencia->bindParam(':id_producto', $id_producto);

if ($sentencia->execute()) {
    $_SESSION['mensaje'] = "Se eliminó el producto de la manera correcta.";
    $_SESSION['icono'] = "success";
    header('Location: ' . $URL . '/almacen/');
} else {
    $_SESSION['mensaje'] = "Error: no se pudo eliminar el producto en la base de datos.";
    $_SESSION['icono'] = "error";
    header('Location: ' . $URL . '/almacen/');
}
?>
