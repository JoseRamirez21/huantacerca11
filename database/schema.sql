
-- =====================================================================
-- HUANTA CERCA - Estructura de la base de datos
-- Archivo: database/schema.sql
-- Motor: MySQL 5.7+ / MariaDB 10.4+ (XAMPP)
-- =====================================================================

-- NOTA PARA EL HOSTING: si tu hosting ya creó la base de datos por ti,
-- comenta las dos instrucciones siguientes (CREATE DATABASE y USE),
-- selecciona tu base en phpMyAdmin e importa el resto.
CREATE DATABASE IF NOT EXISTS huanta_cerca
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE huanta_cerca;

-- ---------------------------------------------------------------------
-- Tabla: usuarios
-- Administradores del panel. No existe registro público de cuentas.
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre         VARCHAR(100) NOT NULL,
  correo         VARCHAR(150) NOT NULL,
  password       VARCHAR(255) NOT NULL,            -- hash de password_hash(), nunca texto plano
  rol            ENUM('admin') NOT NULL DEFAULT 'admin',
  estado         TINYINT(1) NOT NULL DEFAULT 1,    -- 1 = activo, 0 = inactivo
  creado_en      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_correo (correo),
  CONSTRAINT chk_usuarios_estado CHECK (estado IN (0, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabla: categorias
-- ---------------------------------------------------------------------
CREATE TABLE categorias (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre         VARCHAR(80) NOT NULL,
  descripcion    VARCHAR(255) NULL,
  estado         TINYINT(1) NOT NULL DEFAULT 1,    -- 1 = activa, 0 = inactiva
  creado_en      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categorias_nombre (nombre),
  CONSTRAINT chk_categorias_estado CHECK (estado IN (0, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabla: negocios
-- Cada negocio pertenece a una categoría.
-- telefono guarda solo los 9 dígitos de un celular peruano (sin +51).
-- imagen queda reservada para una versión futura (no se usa en v1).
-- ---------------------------------------------------------------------
CREATE TABLE negocios (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  categoria_id   INT UNSIGNED NOT NULL,
  nombre         VARCHAR(120) NOT NULL,
  descripcion    VARCHAR(1000) NULL,
  direccion      VARCHAR(200) NULL,
  telefono       VARCHAR(9) NULL,
  horario        VARCHAR(150) NULL,
  imagen         VARCHAR(255) NULL,
  estado         TINYINT(1) NOT NULL DEFAULT 1,    -- 1 = activo, 0 = inactivo
  creado_en      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_negocios_categoria_estado (categoria_id, estado),
  KEY idx_negocios_nombre (nombre),
  CONSTRAINT fk_negocios_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT chk_negocios_estado CHECK (estado IN (0, 1)),
  CONSTRAINT chk_negocios_telefono CHECK (telefono IS NULL OR telefono REGEXP '^9[0-9]{8}$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabla: api_tokens
-- Tokens de sesión de la API. Se guarda el HASH (SHA-256) del token,
-- nunca el token en claro. El token real solo lo conoce quien inició sesión.
-- ---------------------------------------------------------------------
CREATE TABLE api_tokens (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id  INT UNSIGNED NOT NULL,
  token_hash  CHAR(64) NOT NULL,
  expira_en   DATETIME NOT NULL,
  revocado_en DATETIME NULL,
  creado_en   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_api_tokens_hash (token_hash),
  CONSTRAINT fk_api_tokens_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;