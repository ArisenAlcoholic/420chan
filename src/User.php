<?php
namespace App;

class User {
    public function createUser(string $username, string $password): bool {
        if (empty($username) || empty($password)) {
            return false;
        }
        return true;
    }
}
?>