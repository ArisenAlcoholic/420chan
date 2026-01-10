<?php
session_start();

## Condicional para volver a index.php en caso de que se borren los datos de sesión. 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "b420chan", 3306, "");

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$error = "";
$success = "";

if (isset($_POST['change_password'])) {
    $current = trim($_POST['current_password']);
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);
    if ($new != $confirm) {
        $error = "New passwords do not match";
    } else {
        $sql = "SELECT contrasena FROM usuario WHERE id = $user_id";
        $res = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($res);
        if ($row['contrasena'] != $current) {
            $error = "Current password is incorrect";
        } else {
            $sql = "UPDATE usuario SET contrasena = '$new' WHERE id = $user_id";
            mysqli_query($con, $sql);
            $success = "Password changed successfully";
        }
    }
}

$sql = "SELECT nombre_usuario, num_publicaciones, num_comentarios, TIMESTAMPDIFF(DAY, fecha_creacion, NOW()) AS days_active FROM usuario WHERE id = $user_id";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>420Chan - Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Profile</h1>
    <p>Username: <?php echo $user['nombre_usuario']; ?></p>
    <p>Time Active: <?php echo $user['days_active']; ?> days</p>
    <p>Number of Posts Made: <?php echo $user['num_publicaciones']; ?></p>
    <p>Number of Comments Made: <?php echo $user['num_comentarios']; ?></p>
    <h2>Change Password</h2>
    <form action="" method="post">
        <label for="current_password">Current Password:</label><br>
        <input type="password" id="current_password" name="current_password" required><br><br>
        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br><br>
        <label for="confirm_password">Confirm New Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" name="change_password" value="Change Password">
    </form>
    <?php if ($error) { echo "<p style=\"color:red\"><i>$error</i></p>"; } ?>
    <?php if ($success) { echo "<p style=\"color:green\"><i>$success</i></p>"; } ?>
    <br>
    <form action="index.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
    <br>
    <form action="feed.php" method="get">
        <input type="submit" value="Back to Feed">
    </form>
</body>
</html>