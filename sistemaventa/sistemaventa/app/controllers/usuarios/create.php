<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../../config.php');

$nombres = $_POST['nombres'] ?? '';
$email = $_POST['email'] ?? '';
$rol = $_POST['rol'] ?? '';
$password_user = $_POST['password_user'] ?? '';
$password_repeat = $_POST['password_repeat'] ?? '';

$allowed_domains = ['gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'icloud.com', 'protonmail.com'];

// Función para redirigir con mensaje de error
function redirectError($message, $url) {
    $_SESSION['mensaje'] = $message;
    $_SESSION['icono'] = 'error';
    header("Location: $url");
    exit();
}

// Validar que ningún campo importante esté vacío
function validateNotEmpty($fields) {
    foreach ($fields as $fieldName => $value) {
        if (trim($value) === '') {
            return "El campo '$fieldName' no puede estar vacío.";
        }
    }
    return null;
}

// Validar email con filtro y dominio permitido
function validateEmail($email, $allowed_domains) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El formato del correo es inválido.";
    }
    $domain = strtolower(substr(strrchr($email, "@"), 1));
    if (!in_array($domain, $allowed_domains)) {
        return "El dominio del correo no está permitido.";
        ?>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Dominio no permitido',
            text: 'El dominio del correo no está permitido.',
            confirmButtonText: 'Aceptar'
        });
        </script>
        <?php
    }
    return null;
}

// Validar que contraseñas coincidan
function validatePasswords($pass1, $pass2) {
    if ($pass1 !== $pass2) {
        return "Las contraseñas no coinciden.";
    }
    if (strlen($pass1) < 6) {
        return "La contraseña debe tener al menos 6 caracteres.";
    }
    return null;
}

// 1. Validar campos vacíos
$error = validateNotEmpty([
    'nombres' => $nombres,
    'email' => $email,
    'contraseña' => $password_user,
    'repetir contraseña' => $password_repeat,
]);
if ($error) redirectError($error, $URL.'/usuarios/create.php');

// 2. Validar email
$error = validateEmail($email, $allowed_domains);
if ($error) redirectError($error, $URL.'/usuarios/create.php');

// 3. Validar contraseñas
$error = validatePasswords($password_user, $password_repeat);
if ($error) redirectError($error, $URL.'/usuarios/create.php');

// 4. Verificar si email existe en tb_usuarios
try {
    $sql = "SELECT 1 FROM tb_usuarios WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch()) {
        redirectError("El correo electrónico '$email' ya está registrado.", $URL.'/usuarios/create.php');
    }
} catch (PDOException $e) {
    error_log("Error DB checking email in usuarios: " . $e->getMessage());
    redirectError("Error al verificar el correo en la base de datos.", $URL.'/usuarios/create.php');
}

// 5. Verificar si email existe en tb_proveedores
try {
    $sql = "SELECT 1 FROM tb_proveedores WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch()) {
        redirectError("El correo '$email' ya está registrado como proveedor.", $URL.'/usuarios/create.php');
    }
} catch (PDOException $e) {
    error_log("Error DB checking email in proveedores: " . $e->getMessage());
    redirectError("Error al verificar el correo en proveedores.", $URL.'/usuarios/create.php');
}

// 6. Insertar usuario
try {
    $password_hash = password_hash($password_user, PASSWORD_DEFAULT);
    $fechaHora = date('Y-m-d H:i:s');

    $sqlInsert = "INSERT INTO tb_usuarios (nombres, email, id_rol, password_user, fyh_creacion)
                  VALUES (:nombres, :email, :rol, :password, :fecha)";
    $stmt = $pdo->prepare($sqlInsert);

    $stmt->bindValue(':nombres', $nombres, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':rol', (int)$rol, PDO::PARAM_INT);
    $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(':fecha', $fechaHora, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "¡Usuario registrado correctamente!";
        $_SESSION['icono'] = "success";
        header('Location: '.$URL.'/usuarios/');
        exit();
    } else {
        redirectError("Error al registrar el usuario, intente de nuevo.", $URL.'/usuarios/create.php');
    }
} catch (PDOException $e) {
    error_log("Error DB inserting user: " . $e->getMessage());
    redirectError("Error en la base de datos al registrar usuario.", $URL.'/usuarios/create.php');
}

?>
