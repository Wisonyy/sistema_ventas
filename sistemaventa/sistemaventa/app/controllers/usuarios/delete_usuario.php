<?php
include ('../../config.php');
session_start();

$id_usuario = $_POST['id_usuario'];
$id_sesion = $_SESSION['id_usuario'];

if ($id_usuario == $id_sesion) {
    $_SESSION['mensaje'] = "No puedes eliminar tu propio usuario (admin).";
    $_SESSION['icono'] = "error";
} else {
    try {
        $sentencia = $pdo->prepare("DELETE FROM tb_usuarios WHERE id_usuario=:id_usuario");
        $sentencia->bindParam('id_usuario', $id_usuario);
        $sentencia->execute();

        $_SESSION['mensaje'] = "Se eliminó al usuario correctamente";
        $_SESSION['icono'] = "success";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1451) {
            $_SESSION['mensaje'] = "No se puede eliminar este usuario porque tiene registros asociados.";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar usuario.";
        }
        $_SESSION['icono'] = "error";
    }
}

header('Location: '.$URL.'/usuarios/');