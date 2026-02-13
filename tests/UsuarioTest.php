<?php
use PHPUnit\Framework\TestCase;
use App\Database;

class UsuarioTest extends TestCase {

    private $pdo;

    protected function setUp(): void {
        $this->pdo = Database::connect();
    }

    public function testInsertarUsuarioEnBaseDeDatos() {

        // Datos de prueba
        $username = "test_user_" . rand(1000, 9999);
        $password = "testpass";

        // Insertar usuario (como en index.php)
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuario (nombre_usuarios, contrasena) VALUES (?, ?)"
        );
        $stmt->execute([$username, $password]);

        // Comprobar que existe
        $check = $this->pdo->prepare(
            "SELECT nombre_usuario FROM usuario WHERE nombre_usuario = ?"
        );
        $check->execute([$username]);
        $result = $check->fetch();

        // Verificaciones
        $this->assertNotFalse($result);
        $this->assertEquals($username, $result['nombre_usuario']);
    }
}
