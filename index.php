<?php
session_start();

## Condicional para volver a index.php en caso de que se borren los datos de sesión. 

$con = mysqli_connect("localhost", "root", "", "b420chan", 3306, "");


if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$error = "";

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

if (isset($_POST['complete_register'])) {
    $user = trim($_POST['user']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);
    if ($password != $confirm) {
        $error = "Passwords do not match";
    } else {
        $sql = "SELECT nombre_usuario FROM usuario WHERE nombre_usuario = '$user'";
        $res = mysqli_query($con, $sql);
        if (mysqli_num_rows($res) > 0) {
            $error = "A user with this username already exists";
        } else {
            $sql = "INSERT INTO usuario (nombre_usuario, contrasena) VALUES ('$user', '$password')";
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
    <?php if (isset($_POST['login']) || isset($_POST['complete_login'])) { ?>
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
    <?php } elseif (isset($_POST['register']) || isset($_POST['complete_register'])) { ?>
        <div class="register-section">
            <h1>Register Yourself</h1>
            <form action="" method="post">
                <label for="user">User</label><br>
                <input type="text" id="user" name="user" required><br><br>
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password" required><br><br>
                <label for="confirm_password">Confirm Password</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>
                <input type="submit" value="Register" name="complete_register">
            </form>
            <form action="" method="post">
                <input type="submit" value="Return" name="return">
            </form>
        </div>
    <?php } else { ?>
        <div class="main-section">
            <h1>Welcome to 420Chan</h1>
            <p>Your go-to place for all things related to cannabis culture.</p>
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