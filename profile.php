<body>
    <h1>Profile</h1>
    <p>Username: </p>
    <p>Time Active: </p>
    <p>Number of Posts Made: </p>
    <p>Number of Comments Made: </p>
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
    <br>
    <form action="index.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
    <br>
    <form action="feed.php" method="get">
        <input type="submit" value="Back to Feed">
    </form>
</body>