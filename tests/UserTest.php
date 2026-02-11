<?php
use PHPUnit\Framework\TestCase;
use App\User;

class UserTest extends TestCase {

    public function testCreateUserWithValidData() {
        $user = new User();
        $this->assertTrue($user->createUser("admin", "1234"));
    }

    public function testCreateUserWithInvalidData() {
        $user = new User();
        $this->assertFalse($user->createUser("", ""));
    }
}
?>