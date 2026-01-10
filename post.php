<body>
    <h1>Post</h1>
    <div class="post">
        <h3>User - Date</h3>
        <p>Text</p>
        <p>Likes: </p>
        <form action="like.php" method="post">
            <input type="hidden" name="post_id" value="">
            <input type="hidden" name="from_post" value="1">
            <input type="submit" value="Like">
        </form>
    </div>
    <h2>Comments</h2>
        <div class="comment">
            <h4>User - Date</h4>
            <p>Text</p>
            <p>Likes: </p>
            <form action="like.php" method="post">
                <input type="hidden" name="comment_id" value="">
                <input type="submit" value="Like">
            </form>
        </div>
    <form action="" method="post">
        <textarea name="comment_text" placeholder="Add a comment..." required></textarea><br>
        <input type="submit" name="add_comment" value="Add Comment">
    </form>
    <br>
    <form action="feed.php" method="get">
        <input type="submit" value="Back to Feed">
    </form>
</body>