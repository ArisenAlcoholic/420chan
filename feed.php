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
$filter_user = isset($_POST['filter_user']) ? trim($_POST['filter_user']) : '';
$filter_date_from = isset($_POST['filter_date_from']) ? trim($_POST['filter_date_from']) : '';
$filter_date_to = isset($_POST['filter_date_to']) ? trim($_POST['filter_date_to']) : '';

## Filtro con operadores que concatenan todas las variables. 

$sql = "SELECT p.id, p.texto, p.fecha_publicacion, p.likes, u.nombre_usuario FROM publicacion p JOIN usuario u ON p.id_usuario = u.id";
$where = [];
if ($filter_user) {
    $where[] = "u.nombre_usuario = '$filter_user'";
}
if ($filter_date_from) {
    $where[] = "DATE(p.fecha_publicacion) >= '$filter_date_from'";
}
if ($filter_date_to) {
    $where[] = "DATE(p.fecha_publicacion) <= '$filter_date_to'";
}
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY p.fecha_publicacion DESC";
$result = mysqli_query($con, $sql);
$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}

$sql_users = "SELECT nombre_usuario FROM usuario";
$users_result = mysqli_query($con, $sql_users);
$users = [];
while ($row = mysqli_fetch_assoc($users_result)) {
    $users[] = $row['nombre_usuario'];
}

if (isset($_POST['make_post'])) {
    $text = trim($_POST['post_text']);
    if ($text) {
        $sql = "INSERT INTO publicacion (id_usuario, texto) VALUES ($user_id, '$text')";
        mysqli_query($con, $sql);
        header("Location: feed.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>420Chan - Feed</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Feed</h1>
    <p>Welcome, <?php echo $username; ?>!</p>
    <form action="" method="post">
        <label for="filter_user">Filter by user:</label>
        <select name="filter_user" id="filter_user">
            <option value="">All</option>
            <?php foreach ($users as $u) { ?>
                <option value="<?php echo $u; ?>" <?php if ($filter_user == $u) echo 'selected'; ?>><?php echo $u; ?></option>
            <?php } ?>
        </select>
        <label for="filter_date_from">From date:</label>
        <input type="date" name="filter_date_from" value="<?php echo $filter_date_from; ?>">
        <label for="filter_date_to">To date:</label>
        <input type="date" name="filter_date_to" value="<?php echo $filter_date_to; ?>">
        <input type="submit" value="Filter">
    </form>
    <form action="" method="post">
        <textarea name="post_text" placeholder="Write your post..." required></textarea><br>
        <input type="submit" name="make_post" value="Make a Post">
    </form>
    <?php foreach ($posts as $post) { ?>
        <div class="post">
            <h3><?php echo $post['nombre_usuario']; ?> - <?php echo $post['fecha_publicacion']; ?></h3>
            <p><?php echo $post['texto']; ?></p>
            <p>Likes: <?php echo $post['likes']; ?></p>
            <form action="like.php" method="post">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="from_feed" value="1">
                <input type="submit" value="Like">
            </form>
            <form action="post.php" method="get">
                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                <input type="submit" value="View Post">
            </form>
        </div>
    <?php } ?>
    <br>
    <form action="profile.php" method="get">
        <input type="submit" value="Profile">
    </form>
    <form action="index.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
</body>
</html>