<?php
use PHPUnit\Framework\TestCase;
use App\Database;

class UsuarioTest extends TestCase {

    private $pdo;

    protected function setUp(): void {
        $this->pdo = Database::connect();
    }

    public function testInsertarUsuarioEnBaseDeDatos() {

        // 1️⃣ Datos de prueba
        $username = "test_user_" . rand(1000, 9999);
        $password = "testpass";

        // 2️⃣ Insertar usuario (como en index.php)
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuario (nombre_usuario, contrasena) VALUES (?, ?)"
        );
        $stmt->execute([$username, $password]);

        // 3️⃣ Comprobar que existe
        $check = $this->pdo->prepare(
            "SELECT nombre_usuario FROM usuario WHERE nombre_usuario = ?"
        );
        $check->execute([$username]);
        $result = $check->fetch();

        // 4️⃣ Verificaciones
        $this->assertNotFalse($result);
        $this->assertEquals($username, $result['nombre_usuario']);
    }
}
