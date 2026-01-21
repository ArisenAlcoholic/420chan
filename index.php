<?php
session_start();

## Condicional para volver a index.php en caso de que se borren los datos de sesión. 

$con = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], "", $_ENV['DB_NAME'], 3306, "");


if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$error = "";

## Condicional de inicio de sesión. 

if (isset($_POST['complete_login'])) {
    $user = trim($_POST['user']); 
    $password = trim($_POST['password']);
    $sql = "SELECT id, nombre_usuario FROM usuario WHERE nombre_usuario = '$user' AND contrasena = '$password'";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['nombre_usuario'];
        header("Location: feed.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}

## Condicional de registro.

if (isset($_POST['complete_register'])) {
    $user = trim($_POST['user']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);
    $profile_pic = NULL;  // Variable para introducir imágenes
    if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] == 0) {  
        $filename = time() . '_' . basename($_FILES['imagen_perfil']['name']);  // Prefijado con fecha para la subida de la imagen y evitar duplicados
        $target = 'img/' . $filename;
        if (move_uploaded_file($_FILES['imagen_perfil']['tmp_name'], $target)) {
            $profile_pic = $target;
        }
    }
## Comprobador de contraseñas. 

    if ($password != $confirm) {
        $error = "Passwords do not match";
    } else {
        $sql = "SELECT nombre_usuario FROM usuario WHERE nombre_usuario = '$user'";
        $res = mysqli_query($con, $sql);
        if (mysqli_num_rows($res) > 0) { // Comprueba que no haya usuarios registrados con ese mismo nombre. 
            $error = "A user with this username already exists";
        } else {
            $sql = "INSERT INTO usuario (nombre_usuario, contrasena, imagen_perfil) VALUES ('$user', '$password', " . ($profile_pic ? "'$profile_pic'" : "NULL") . ")";  // Inserción de nuevo usuario en la tabla de usuario. 
            mysqli_query($con, $sql);
            $id = mysqli_insert_id($con);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $user;
            header("Location: feed.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>420Chan</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Pantalla de inicio de sesión -->
    <?php if (isset($_POST['login']) || isset($_POST['complete_login'])) { ?> <!--Ambas condiciones para que no saque al usuario de la pantalla de inicio de sesión en caso de que haya introducido credenciales incorrectas -->
        <div class="login-section">
            <h1>Login to 420Chan</h1>
            <form action="" method="post">
                <label for="user">User:</label><br>
                <input type="text" id="user" name="user" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                <input type="submit" value="Login" name="complete_login">
            </form>
            <form action="" method="post">
                <input type="submit" value="Return" name="return">
            </form>
        </div>
    <!-- Pantalla de registro -->
    <?php } elseif (isset($_POST['register']) || isset($_POST['complete_register'])) { ?> <!--Ambas condiciones para que no saque al usuario de la pantalla de registro en caso de que haya introducido credenciales incorrectas -->
        <div class="register-section">
            <h1>Register Yourself</h1>
            <form action="" method="post" enctype="multipart/form-data">  
                <input type="text" id="user" name="user" required><br><br>
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password" required><br><br>
                <label for="confirm_password">Confirm Password</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>
                <input type="file" name="imagen_perfil" accept="image/*"><br><br>  <!-- Entrada de imagen -->
                <input type="submit" value="Register" name="complete_register">
            </form>
            <form action="" method="post">
                <input type="submit" value="Return" name="return">
            </form>
        </div>
    <?php } else { ?>
    <!-- Pantalla de página de inicio por defecto -->
        <div class="main-section">
            <h1>Welcome to 420Chan</h1>
            <p class="welcome">A forum for those nostalgic about ye olde Internet.</p>
            <br>
            <div class="register">
                <form action="" method="post">
                    <input type="submit" name="register" value="Register">
                </form>
            </div>
            <br>
            <div class="login">
                <form action="" method="post">
                    <input type="submit" name="login" value="Login">
                </form>
            </div>
        </div>
    <?php } ?>
    <!-- Mensaje de error sale aquí -->
    <?php if ($error) { echo "<p style=\"color:red\"><i>$error</i></p>"; } ?>
</body>

</html>
