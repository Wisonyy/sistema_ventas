<?php
/**
 * Created by PhpStorm.
 * User: Salazar Phocco Yenifer Fiorela
 */

include ('../../config.php');
session_start();

$nombre_cliente = $_POST['nombre_cliente'];
$nit_ci_cliente = $_POST['nit_ci_cliente'];
$celular_cliente = $_POST['celular_cliente'];
$email_cliente = $_POST['email_cliente'];
$fechaHora = date("Y-m-d H:i:s");

// Lista de dominios permitidos
$allowed_domains = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com'];

// Validar formato de email
if (!filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['mensaje'] = "Formato de correo electrónico inválido.";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL;?>/ventas/create.php";
    </script>
    <?php
    exit();
}

// Extraer dominio del email
$email_parts = explode('@', $email_cliente);
$domain = isset($email_parts[1]) ? strtolower($email_parts[1]) : '';

// Validar dominio permitido
if (!in_array($domain, $allowed_domains)) {
    $_SESSION['mensaje'] = "Dominio de correo no permitido. Solo se aceptan: <?php echo implode(', ', $allowed_domains); ?>";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL;?>/ventas/create.php";
    </script>
    <?php
    exit();
}

// Verificar que email y celular no existan en otras tablas
$sql_validacion = "
    SELECT email FROM tb_usuarios WHERE email = :email
    UNION
    SELECT email FROM tb_proveedores WHERE email = :email
    UNION
    SELECT email_cliente AS email FROM tb_clientes WHERE email_cliente = :email
";
$consulta_email = $pdo->prepare($sql_validacion);
$consulta_email->bindParam(':email', $email_cliente);
$consulta_email->execute();

if ($consulta_email->rowCount() > 0) {
    $_SESSION['mensaje'] = "El correo electrónico ya existe en otra tabla.";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL;?>/ventas/create.php";
    </script>
    <?php
    exit();
}

// Validar celular
$sql_validacion_cel = "
    SELECT celular FROM tb_proveedores WHERE celular = :celular
    UNION
    SELECT celular_cliente AS celular FROM tb_clientes WHERE celular_cliente = :celular
    
";
$consulta_cel = $pdo->prepare($sql_validacion_cel);
$consulta_cel->bindParam(':celular', $celular_cliente);
$consulta_cel->execute();

if ($consulta_cel->rowCount() > 0) {
    $_SESSION['mensaje'] = "El número de celular ya existe en otra tabla.";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL;?>/ventas/create.php";
    </script>
    <?php
    exit();
}
// Validar duplicado de NIT/CI
$consulta_nit = $pdo->prepare("SELECT * FROM tb_clientes WHERE nit_ci_cliente = :nit_ci_cliente");
$consulta_nit->bindParam(':nit_ci_cliente', $nit_ci_cliente);
$consulta_nit->execute();
if ($consulta_nit->rowCount() > 0) {
    $_SESSION['mensaje'] = "El NIT/CI ya está registrado.";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL; ?>/ventas/create.php";
    </script>
    <?php
    exit();
}

// Si pasa todas las validaciones, insertar en la base de datos
$sentencia = $pdo->prepare("INSERT INTO tb_clientes
    (nombre_cliente, nit_ci_cliente, celular_cliente, email_cliente, fyh_creacion) 
    VALUES (:nombre_cliente, :nit_ci_cliente, :celular_cliente, :email_cliente, :fyh_creacion)");

$sentencia->bindParam('nombre_cliente', $nombre_cliente);
$sentencia->bindParam('nit_ci_cliente', $nit_ci_cliente);
$sentencia->bindParam('celular_cliente', $celular_cliente);
$sentencia->bindParam('email_cliente', $email_cliente);
$sentencia->bindParam('fyh_creacion', $fechaHora);

if ($sentencia->execute()) {
    ?>
    <script>
        location.href = "<?php echo $URL;?>/ventas/create.php";
    </script>
    <?php
} else {
    $_SESSION['mensaje'] = "Error: no se pudo registrar en la base de datos.";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL;?>/ventas/create.php";
    </script>
    <?php
}
?>
