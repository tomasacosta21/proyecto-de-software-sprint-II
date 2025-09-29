CREATE TABLE rol (
  id_rol INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  descripcion TEXT,
  PRIMARY KEY (id_rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuario (
  id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  apellido VARCHAR(50) NOT NULL,
  nickname VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  rol_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuario_email (email),
  UNIQUE KEY uq_usuario_nick (nickname),
  KEY idx_usuario_rol (rol_id),
  CONSTRAINT fk_usuario_rol 
    FOREIGN KEY (rol_id) REFERENCES rol(id_rol)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
