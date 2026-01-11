<?php
session_start();

## Condicional para volver a index.php en caso de que se borren los datos de sesión. 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "b420chan", 3306, "");

$user_id = $_SESSION['user_id']; //Valores de la sesión del login de index.php
$username = $_SESSION['username'];  
$filter_user = isset($_POST['filter_user']) ? trim($_POST['filter_user']) : '';
$filter_date_from = isset($_POST['filter_date_from']) ? trim($_POST['filter_date_from']) : '';
$filter_date_to = isset($_POST['filter_date_to']) ? trim($_POST['filter_date_to']) : '';

## Filtro con operadores que concatenan todas las variables. 
## Permite crear consultas dinámicas en función de los dataos por medio de los cuales ha filtrado el usuario. 

$sql = "SELECT p.id, p.texto, p.fecha_publicacion, p.likes, p.imagen_pub, u.nombre_usuario, u.imagen_perfil FROM publicacion p JOIN usuario u ON p.id_usuario = u.id";  //Consulta para la sesión de usuario y publicación de posts bajo el usuario de la sesión. 
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
$sql .= " ORDER BY p.fecha_publicacion DESC"; //Añade las condiciones de $where a la consulta SQL.
$result = mysqli_query($con, $sql);
$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}

## Consulta para cada usuario que haya hecho una publicación. 

$sql_users = "SELECT nombre_usuario FROM usuario";
$users_result = mysqli_query($con, $sql_users);
$users = [];
while ($row = mysqli_fetch_assoc($users_result)) {
    $users[] = $row['nombre_usuario'];
}

## Condiciones para realizar la publicación. 

if (isset($_POST['make_post'])) {
    $text = trim($_POST['post_text']);
    $image_path = NULL;  // NEW: Default to NULL
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {  // Añadido handler de imágenes. 
        $filename = time() . '_' . basename($_FILES['post_image']['name']);  // Prefijado con fecha para la subida de la imagen y evitar duplicados
        $target = 'img/' . $filename;
        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target)) {
            $image_path = $target;
        }
    }
    if ($text) {
        $sql = "INSERT INTO publicacion (id_usuario, texto, imagen_pub) VALUES ($user_id, '$text', " . ($image_path ? "'$image_path'" : "NULL") . ")";  //Inserta la ruta de la imagen a la base de datos. 
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
    <p class="welcome">Welcome, <?php echo $username; ?>!</p>
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
    <form action="" method="post" enctype="multipart/form-data">  
        <textarea name="post_text" placeholder="Write your post..." required></textarea><br>
        <input type="file" name="post_image" accept="image/*"><br>  
        <input type="submit" name="make_post" value="Make a Post">
    </form>
    <?php foreach ($posts as $post) { ?>
        <div class="post">
            <h3>
                <?php if ($post['imagen_perfil']) { echo '<img src="' . $post['imagen_perfil'] . '" class="profile-pic">'; } ?> <!-- Muestra la imagen de perfil (si la hay)-->
                <?php echo $post['nombre_usuario']; ?> - <?php echo $post['fecha_publicacion']; ?>
            </h3>
        
                <p><?php echo $post['texto']; ?></p>
            <?php if ($post['imagen_pub']) { echo '<img src="' . $post['imagen_pub'] . '" style="max-width:100%;">'; } ?>  
            <p>Likes: <?php echo $post['likes']; ?></p>
            <!-- Quizás haya una mejor manera de contabilizar likes que actualizando toda la página, buscar alternativa?(AJAX ) -->
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