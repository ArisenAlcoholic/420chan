<?php
session_start();

## Condicional para volver a index.php en caso de que se borren los datos de sesión. 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$con = mysqli_connect("localhost", "root", "", "b420chan", 3306, "");

$user_id = $_SESSION['user_id'];
if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $sql = "SELECT * FROM like_publicacion WHERE id_usuario = $user_id AND id_publicacion = $post_id";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {
        $sql = "DELETE FROM like_publicacion WHERE id_usuario = $user_id AND id_publicacion = $post_id";
    } else {
        $sql = "INSERT INTO like_publicacion (id_usuario, id_publicacion) VALUES ($user_id, $post_id)";
    }
    mysqli_query($con, $sql);
    if (isset($_POST['from_feed'])) {
        header("Location: feed.php");
    } else {
        header("Location: post.php?id=$post_id");
    }
    exit();
} elseif (isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    $sql = "SELECT * FROM like_comentario WHERE id_usuario = $user_id AND id_comentario = $comment_id";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {
        $sql = "DELETE FROM like_comentario WHERE id_usuario = $user_id AND id_comentario = $comment_id";
    } else {
        $sql = "INSERT INTO like_comentario (id_usuario, id_comentario) VALUES ($user_id, $comment_id)";
    }
    mysqli_query($con, $sql);
    $sql = "SELECT id_publicacion FROM comentario WHERE id = $comment_id";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);
    $post_id = $row['id_publicacion'];
    header("Location: post.php?id=$post_id");
    exit();
}
?>