
<?php
include ('../../config.php');
/**
 * Created by PhpStorm.
 * User: Salazar Phocco Yenifer Fiorela
 */
$id_proveedor = $_GET['id_proveedor'];
$nombre_proveedor = $_GET['nombre_proveedor'];
$celular = $_GET['celular'];
$telefono = $_GET['telefono'];
$empresa = $_GET['empresa'];
$email = $_GET['email'];
$direccion = $_GET['direccion'];
$fechaHora = date("Y-m-d H:i:s");

// Verificar si el correo ya existe para otro proveedor
$verificarEmail = $pdo->prepare("SELECT COUNT(*) FROM tb_proveedores WHERE email = :email AND id_proveedor != :id_proveedor");
$verificarEmail->bindParam(':email', $email);
$verificarEmail->bindParam(':id_proveedor', $id_proveedor);
$verificarEmail->execute();
$emailExistente = $verificarEmail->fetchColumn();

if ($emailExistente > 0) {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Correo duplicado',
            text: 'El correo ya está registrado con otro proveedor.',
            confirmButtonText: 'Entendido'
        }).then(() => {
            window.location.href = "<?php echo $URL; ?>/proveedores";
        });
    </script>
    <?php
    exit();
}

// Si no hay duplicado, actualizar
$sentencia = $pdo->prepare("UPDATE tb_proveedores
    SET nombre_proveedor=:nombre_proveedor,
        celular=:celular,
        telefono=:telefono,
        empresa=:empresa,
        email=:email,
        direccion=:direccion,
        fyh_actualizacion=:fyh_actualizacion 
    WHERE id_proveedor = :id_proveedor ");

$sentencia->bindParam(':nombre_proveedor',$nombre_proveedor);
$sentencia->bindParam(':celular',$celular);
$sentencia->bindParam(':telefono',$telefono);
$sentencia->bindParam(':empresa',$empresa);
$sentencia->bindParam(':email',$email);
$sentencia->bindParam(':direccion',$direccion);
$sentencia->bindParam(':fyh_actualizacion',$fechaHora);
$sentencia->bindParam(':id_proveedor',$id_proveedor);

if($sentencia->execute()){
    session_start();
    $_SESSION['mensaje'] = "Se actualizó al proveedor correctamente";
    $_SESSION['icono'] = "success";
    ?>
    <script>
        location.href = "<?php echo $URL;?>/proveedores";
    </script>
    <?php
}else{
    session_start();
    $_SESSION['mensaje'] = "Error: no se pudo actualizar en la base de datos";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL;?>/proveedores";
    </script>
    <?php
}
?>