<?php
/**
 * Created by PhpStorm.
 * User: Salazar Phocco Yenifer Fiorela
 */
include('../../config.php');
session_start();

$nombre_proveedor = $_GET['nombre_proveedor'] ?? '';
$celular = $_GET['celular'] ?? '';
$telefono = $_GET['telefono'] ?? '';
$empresa = $_GET['empresa'] ?? '';
$email = $_GET['email'] ?? '';
$direccion = $_GET['direccion'] ?? '';
$fechaHora = date('Y-m-d H:i:s');

// Validar dominio de correo
$allowed_domains = ['gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'icloud.com', 'protonmail.com'];
$domain = strtolower(substr(strrchr($email, "@"), 1));

$icono = 'error'; // Por defecto
$mensaje = ''; // Mensaje a mostrar

// Validar datos
if (empty($nombre_proveedor) || empty($email) || empty($telefono) || empty($celular)) {
    $mensaje = "Por favor, complete todos los campos requeridos.";
} elseif (!preg_match('/^\d{9}$/', $telefono) || !preg_match('/^\d{9}$/', $celular)) {
    $mensaje = "El teléfono y el celular deben tener exactamente 9 dígitos.";
} elseif (!in_array($domain, $allowed_domains)) {
    $mensaje = "El dominio del correo no está permitido.";
} else {
    try {
        $sentencia = $pdo->prepare("INSERT INTO tb_proveedores (nombre_proveedor, celular, telefono, empresa, email, direccion, fyh_creacion) VALUES (:nombre_proveedor, :celular, :telefono, :empresa, :email, :direccion, :fyh_creacion)");
        $sentencia->bindParam(':nombre_proveedor', $nombre_proveedor);
        $sentencia->bindParam(':celular', $celular);
        $sentencia->bindParam(':telefono', $telefono);
        $sentencia->bindParam(':empresa', $empresa);
        $sentencia->bindParam(':email', $email);
        $sentencia->bindParam(':direccion', $direccion);
        $sentencia->bindParam(':fyh_creacion', $fechaHora);
        $sentencia->execute();
        $icono = "success";
        $mensaje = "¡Proveedor registrado correctamente!";
        ?>
            <script>
                location.href="<?php echo $URL;?>/proveedores";
            </script>
        <?php
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $mensaje = "El correo '$email' ya está registrado.";
        } else {
            $mensaje = "Error al registrar proveedor: " . $e->getMessage();
             ?>
            <script>
                location.href="<?php echo $URL;?>/proveedores";
            </script>
        <?php
        }
    }
}
?>


<!-- Mostrar SweetAlert -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resultado</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    icon: '<?php echo $icono; ?>',
    title: '<?php echo ($icono === "success") ? "Éxito" : "Error"; ?>',
    text: '<?php echo $mensaje; ?>',
    confirmButtonText: 'Aceptar'
}).then(() => {
    window.location.href = "<?php echo $URL; ?>/proveedores";
});
</script>
</body>
</html>
