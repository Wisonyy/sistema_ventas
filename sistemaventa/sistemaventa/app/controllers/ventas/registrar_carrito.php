<?php
include ('../../config.php');

$nro_venta = $_GET['nro_venta'];
$id_producto = $_GET['id_producto'];
$cantidad = $_GET['cantidad'];
$fechaHora = date("Y-m-d H:i:s"); // Si aún no está definida

// 1. Validar cantidad > 0 y numérica
if (!is_numeric($cantidad) || $cantidad <= 0) {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Cantidad inválida',
            text: 'La cantidad debe ser mayor que cero.',
        }).then(() => {
            location.href = "<?php echo $URL; ?>/ventas/create.php";
        });
    </script>
    <?php
    exit();
}

// 2. Consultar el stock actual del producto
$consulta = $pdo->prepare("SELECT stock FROM tb_almacen WHERE id_producto = :id_producto");
$consulta->bindParam(':id_producto', $id_producto);
$consulta->execute();
$producto = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    // Producto no encontrado
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Producto no encontrado en la base de datos.',
        }).then(() => {
            location.href = "<?php echo $URL; ?>/ventas/create.php";
        });
    </script>
    <?php
    exit();
}

$stock_disponible = $producto['stock'];

// 3. Validar si la cantidad supera el stock
if ($cantidad > $stock_disponible) {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Stock insuficiente',
            text: 'La cantidad solicitada supera el stock disponible (<?php echo $stock_disponible; ?> unidades).',
        }).then(() => {
            location.href = "<?php echo $URL; ?>/ventas/create.php";
        });
    </script>
    <?php
    exit();
}

// 4. Insertar en el carrito si todo es válido
$sentencia = $pdo->prepare("INSERT INTO tb_carrito
       (nro_venta, id_producto, cantidad, fyh_creacion ) 
VALUES (:nro_venta, :id_producto, :cantidad, :fyh_creacion)");

$sentencia->bindParam('nro_venta', $nro_venta);
$sentencia->bindParam('id_producto', $id_producto);
$sentencia->bindParam('cantidad', $cantidad);
$sentencia->bindParam('fyh_creacion', $fechaHora);

if ($sentencia->execute()) {
    ?>
    <script>
        location.href = "<?php echo $URL; ?>/ventas/create.php";
    </script>
    <?php
} else {
    session_start();
    $_SESSION['mensaje'] = "Error: No se pudo registrar en la base de datos";
    $_SESSION['icono'] = "error";
    header('Location: '.$URL.'/ventas/create.php');
}
?>
