<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$con = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], "", $_ENV['DB_NAME'], 3306, "");
## Consulta de datos de la publicación y el usuario cuya publicación se ha seleccionado. 

$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'];
$sql = "SELECT p.id, p.texto, p.fecha_publicacion, p.likes, p.imagen_pub, u.nombre_usuario, u.imagen_perfil FROM publicacion p JOIN usuario u ON p.id_usuario = u.id WHERE p.id = $post_id";  
$result = mysqli_query($con, $sql);
$post = mysqli_fetch_assoc($result);

## Consulta de datos de los comentarios de otros usuarios.

$sql_comments = "SELECT c.id, c.texto, c.fecha_comentario, c.likes, u.nombre_usuario, u.imagen_perfil FROM comentario c JOIN usuario u ON c.id_usuario = u.id WHERE c.id_publicacion = $post_id ORDER BY c.fecha_comentario ASC";
$comments_result = mysqli_query($con, $sql_comments);
$comments = [];
while ($row = mysqli_fetch_assoc($comments_result)) {
    $comments[] = $row;
}

## Inserción de un comentario sobre la publicación en la base de datos. 

if (isset($_POST['add_comment'])) {
    $text = trim($_POST['comment_text']);
    if ($text) {
        $sql = "INSERT INTO comentario (id_usuario, id_publicacion, texto) VALUES ($user_id, $post_id, '$text')";
        mysqli_query($con, $sql);
        header("Location: post.php?id=$post_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>420Chan - Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Post</h1>
    <div class="post">
        <h3>
            <?php if ($post['imagen_perfil']) { echo '<img src="' . $post['imagen_perfil'] . '" class="profile-pic">'; } ?>
            <?php echo $post['nombre_usuario']; ?> - <?php echo $post['fecha_publicacion']; ?>
        </h3>
        <p><?php echo $post['texto']; ?></p>
        <?php if ($post['imagen_pub']) { echo '<img src="' . $post['imagen_pub'] . '" style="max-width:100%;">'; } ?>  
        <p>Likes: <?php echo $post['likes']; ?></p>
        <form action="like.php" method="post">
            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
            <input type="hidden" name="from_post" value="1">
            <input type="submit" value="Like">
        </form>
    </div>
    <!-- Mostrar comentarios -->
    <h2>Comments</h2>
    <?php if (empty($comments)) { echo "<p>No comments yet.</p>"; } ?>
    <?php foreach ($comments as $comment) { ?>
        <div class="comment">
            <h4>
                <?php echo $comment['nombre_usuario']; ?> - <?php echo $comment['fecha_comentario']; ?>
                <?php if ($comment['imagen_perfil']) { echo '<img src="' . $comment['imagen_perfil'] . '" class="profile-pic">'; } ?>
            </h4>
            <p><?php echo $comment['texto']; ?></p>
            <p>Likes: <?php echo $comment['likes']; ?></p>
            <form action="like.php" method="post">
                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                <input type="submit" value="Like">
            </form>
        </div>
    <?php } ?>
    <!-- Publicar comentario -->
    <form action="" method="post">
        <textarea name="comment_text" placeholder="Add a comment..." required></textarea><br>
        <input type="submit" name="add_comment" value="Add Comment">
    </form>
    <br>
    <form action="feed.php" method="get">
        <input type="submit" value="Back to Feed">
    </form>
</body>
</html>
