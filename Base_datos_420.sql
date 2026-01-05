/*Se crea la base de datos*/
DROP DATABASE IF EXISTS b420chan;
CREATE DATABASE b420chan CHARACTER SET utf8mb4;
USE b420chan;

/* Primero se crea la tabla usuarios que ya han sido registrados */
CREATE TABLE usuario (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(50) NOT NULL,
    num_publicaciones INT DEFAULT 0,
    num_comentarios INT DEFAULT 0,
    tiempo_activo INT DEFAULT 0
);

/* Se crea la tabla publicaciones, para que los usuarios puedan publicar */
CREATE TABLE publicacion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    texto TEXT NOT NULL
);

/* Se crea la tabla de los comentarios de los usuarios registrados */
CREATE TABLE comentario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_publicacion INT NOT NULL,
    fecha_comentario DATETIME DEFAULT CURRENT_TIMESTAMP,
    texto TEXT NOT NULL
);

/*Se añaden las claves foraneas en publicaciones y comentarios */
ALTER TABLE publicacion
ADD CONSTRAINT publicaciones_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON DELETE CASCADE;

ALTER TABLE comentario
ADD CONSTRAINT comentario_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON DELETE CASCADE;

ALTER TABLE comentario
ADD CONSTRAINT comentario_publicacion
FOREIGN KEY (id_publicacion) REFERENCES publicacion(id)
ON DELETE CASCADE;

/* Se añaden los triggers para que se actualicen los comentarios o publicaciones borradas*/
/*Se suma una publicacion*/
DELIMITER $$
CREATE TRIGGER actualizar_num_publicaciones
AFTER INSERT ON publicacion
FOR EACH ROW
BEGIN
    UPDATE usuario
    SET num_publicaciones = num_publicaciones + 1
    WHERE id = NEW.id_usuario;
END $$
DELIMITER ;

/*Se suma publicacion*/
DELIMITER $$
CREATE TRIGGER restar_num_publicaciones
AFTER DELETE ON publicacion
FOR EACH ROW
BEGIN
    UPDATE usuario
    SET num_publicaciones = num_publicaciones - 1
    WHERE id = OLD.id_usuario;
END $$
DELIMITER ;
/*Se suma un comentario*/
DELIMITER $$
CREATE TRIGGER actualizar_num_comentarios
AFTER INSERT ON comentario
FOR EACH ROW
BEGIN
    UPDATE usuario
    SET num_comentarios = num_comentarios + 1
    WHERE id = NEW.id_usuario;
END $$
DELIMITER ;
/*Se borra un comentario */
DELIMITER $$
CREATE TRIGGER restar_num_comentarios
AFTER DELETE ON comentario
FOR EACH ROW
BEGIN
    UPDATE usuario
    SET num_comentarios = num_comentarios - 1
    WHERE id = OLD.id_usuario;
END $$
DELIMITER ;

----------------------------------------
/*PRUEBAS*/
---------------------------------------
/*Se comprueba las tablas creadas*/
SHOW TABLES;
DESCRIBE usuario;
DESCRIBE publicacion;
DESCRIBE comentario;
/* Se añaden usuarios */
INSERT INTO usuario (nombre_usuario, contrasena) VALUES
('usuario1', 'pass1'),
('usuario2', 'pass2'),
('usuario3', 'pass3');
/* Se añaden publicaciones*/
INSERT INTO publicacion (id_usuario, texto) VALUES
(1,'Publicacion de prueba'),
(3,'Publicacion de prueba3');


/*Se añaden comentarios*/
INSERT INTO comentario (id_usuario, id_publicacion, texto) VALUES
(2, 1, 'Comentario del usuario2'),
(1, 1, 'Comentario del usuario1');
/* Se comprueba los usuarios,publicaciones y comentarios añadidos*/
SELECT * FROM usuario;
SELECT * FROM publicacion;
SELECT * FROM comentario;
SELECT id, nombre_usuario, num_publicaciones FROM usuario;
SELECT id, nombre_usuario, num_comentarios FROM usuario;
/*Se comprueba borrar al usuario 3*/
DELETE FROM usuario WHERE id = 3;
/*Se comprueba como afecta borrar una publicacion */
DELETE FROM publicacion WHERE id = 1;
