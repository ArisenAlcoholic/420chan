/*Se crea la base de datos*/
DROP DATABASE IF EXISTS b420chan;
CREATE DATABASE b420chan CHARACTER SET utf8mb4;
USE b420chan;

/* Primero se crea la tabla usuarios que ya han sido registrados */
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
    texto TEXT NOT NULL,
    likes INT DEFAULT 0
);

/* Se crea la tabla de los comentarios de los usuarios registrados */
CREATE TABLE comentario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_publicacion INT NOT NULL,
    fecha_comentario DATETIME DEFAULT CURRENT_TIMESTAMP,
    texto TEXT NOT NULL,
    likes INT DEFAULT 0
);

/* Se crea la tabla me gusta en publicaciones */
CREATE TABLE like_publicacion (
    id_usuario INT NOT NULL,
    id_publicacion INT NOT NULL,
    PRIMARY KEY (id_usuario, id_publicacion)
);

/* Se crea la tabla me gusta en comentarios */
CREATE TABLE like_comentario (
    id_usuario INT NOT NULL,
    id_comentario INT NOT NULL,
    PRIMARY KEY (id_usuario, id_comentario)
);

/*Se añaden las claves foraneas  */
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

ALTER TABLE like_publicacion
ADD CONSTRAINT like_pub_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON DELETE CASCADE;

ALTER TABLE like_publicacion
ADD CONSTRAINT like_pub_publicacion
FOREIGN KEY (id_publicacion) REFERENCES publicacion(id)
ON DELETE CASCADE;

ALTER TABLE like_comentario
ADD CONSTRAINT like_com_usuario
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
ON DELETE CASCADE;

ALTER TABLE like_comentario
ADD CONSTRAINT like_com_comentario
FOREIGN KEY (id_comentario) REFERENCES comentario(id)
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

/*Se resta publicacion*/
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

/* Se añaden los triggers para que se actualicen los me gusta en publicaciones y comentarios*/
/*Se añade me gusta en publicacion*/
DELIMITER $$
CREATE TRIGGER sumar_like_publicacion
AFTER INSERT ON like_publicacion
FOR EACH ROW
BEGIN
    UPDATE publicacion
    SET likes = likes + 1
    WHERE id = NEW.id_publicacion;
END$$
DELIMITER ;

/*Se resta me gusta en publicacion*/
DELIMITER $$
CREATE TRIGGER restar_like_publicacion
AFTER DELETE ON like_publicacion
FOR EACH ROW
BEGIN
    UPDATE publicacion
    SET likes = likes - 1
    WHERE id = OLD.id_publicacion;

END$$
DELIMITER ;

/*Se añade me gusta en comentario*/
DELIMITER $$
CREATE TRIGGER sumar_like_comentario
AFTER INSERT ON like_comentario
FOR EACH ROW
BEGIN
    UPDATE comentario
    SET likes = likes + 1
    WHERE id = NEW.id_comentario;
END$$
DELIMITER ;

/*Se resta me gusta en comentario*/
DELIMITER $$
CREATE TRIGGER restar_like_comentario
AFTER DELETE ON like_comentario
FOR EACH ROW
BEGIN
    UPDATE comentario
    SET likes = likes - 1
    WHERE id = OLD.id_comentario;
END$$
DELIMITER ;

/* Se crea vista para los me gusta en las publicaciones*/
CREATE VIEW vista_likes_publicacion AS
SELECT
    p.id AS id_publicacion,
    p.texto,
    p.likes,
    lp.id_usuario
FROM publicacion p
LEFT JOIN like_publicacion lp
       ON p.id = lp.id_publicacion;

/* Se crea vista para los me gusta en los comentarios*/
CREATE VIEW vista_likes_comentario AS
SELECT
    c.id AS id_comentario,
    c.id_publicacion,
    c.texto,
    c.likes,
    lc.id_usuario
FROM comentario c
LEFT JOIN like_comentario lc
       ON c.id = lc.id_comentario;

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

/*Se añaden likes*/
INSERT INTO like_publicacion VALUES (1, 1);
INSERT INTO like_comentario VALUES (2, 1);

/* Se comprueba los usuarios,publicaciones,comentarios y likes añadidos*/
SELECT * FROM usuario;
SELECT * FROM publicacion;
SELECT * FROM comentario;
SELECT id, nombre_usuario, num_publicaciones FROM usuario;
SELECT id, nombre_usuario, num_comentarios FROM usuario;

/*Se comprueba borrar el like a la publicacion */
DELETE FROM like_publicacion WHERE id_usuario = 1 AND id_publicacion = 1;

/*Se comprueba borrar al usuario 3*/
DELETE FROM usuario WHERE id = 3;

/*Se comprueba como afecta borrar una publicacion */
DELETE FROM publicacion WHERE id = 1;