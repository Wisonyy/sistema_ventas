<?php
/**
 * Created by PhpStorm.
 * User: Salazar Phocco Yenifer Fiorela
 */
include ('../../config.php');

$id_proveedor = $_GET['id_proveedor'];

// Primero verificar si el proveedor tiene compras relacionadas
$sql_check = "SELECT COUNT(*) FROM tb_compras WHERE id_proveedor = :id_proveedor";
$check = $pdo->prepare($sql_check);
$check->execute(['id_proveedor' => $id_proveedor]);
$count = $check->fetchColumn();

session_start();

if ($count > 0) {
    // Si tiene compras, no permitir la eliminación
    $_SESSION['mensaje'] = "No se puede eliminar el proveedor porque tiene compras registradas.";
    $_SESSION['icono'] = "warning";
} else {
    // Si no tiene compras, se puede eliminar
    $sentencia = $pdo->prepare("DELETE FROM tb_proveedores WHERE id_proveedor = :id_proveedor");
    $sentencia->bindParam(':id_proveedor', $id_proveedor);

    if ($sentencia->execute()) {
        $_SESSION['mensaje'] = "Se eliminó al proveedor correctamente.";
        $_SESSION['icono'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error: no se pudo eliminar al proveedor.";
        $_SESSION['icono'] = "error";
    }
}
?>

<script>
    location.href = "<?php echo $URL; ?>/proveedores";
</script>
