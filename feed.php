<body>
    <h1>Feed</h1>
    <p>Welcome, <?php echo "placeholder"; ?>!</p>
    <form action="" method="post">
        <label for="filter_user">Filter by user:</label>
        <select name="filter_user" id="filter_user">
            <option value="">All</option>
        </select>
        <label for="filter_date_from">From date:</label>
        <input type="date" name="filter_date_from" value="">
        <label for="filter_date_to">To date:</label>
        <input type="date" name="filter_date_to" value="">
        <input type="submit" value="Filter">
    </form>
    <form action="" method="post">
        <textarea name="post_text" placeholder="Write your post..." required></textarea><br>
        <input type="submit" name="make_post" value="Make a Post">
    </form>
        <div class="post">
            <h3> User - Date </h3>
            <p>Text</p>
            <p>Likes:</p>
            <form action="like.php" method="post">
                <input type="hidden" name="post_id" value="">
                <input type="hidden" name="from_feed" value="1">
                <input type="submit" value="Like">
            </form>
            <form action="post.php" method="get">
                <input type="hidden" name="id" value="">
                <input type="submit" value="View Post">
            </form>
        </div>
    <br>
    <form action="profile.php" method="get">
        <input type="submit" value="Profile">
    </form>
    <form action="index.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
</body>