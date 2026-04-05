-- ============================================================
--  SARC - Sistema Avanzado de Rutas Comerciales
--  Base de datos MySQL completa
--  Importar en phpMyAdmin o ejecutar:
--  mysql -u root -p < sarc_database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS sarc_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sarc_db;

-- ── TABLA: usuarios ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  username       VARCHAR(60)  NOT NULL UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  nombre         VARCHAR(120) NOT NULL,
  email          VARCHAR(120) NOT NULL UNIQUE,
  rol            ENUM('admin','supervisor','asesor') NOT NULL DEFAULT 'asesor',
  estado         ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ultimo_acceso  DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLA: asesores ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS asesores (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nombre         VARCHAR(120) NOT NULL,
  email          VARCHAR(120) NOT NULL,
  telefono       VARCHAR(30)  DEFAULT '',
  tipo           VARCHAR(50)  NOT NULL DEFAULT 'junior',
  zona           VARCHAR(80)  DEFAULT '',
  estado         ENUM('activo','inactivo','vacaciones') NOT NULL DEFAULT 'activo',
  notas          TEXT,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLA: clientes ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS clientes (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nombre         VARCHAR(150) NOT NULL,
  nit            VARCHAR(30)  DEFAULT '',
  tipo           VARCHAR(50)  NOT NULL,
  contacto       VARCHAR(120) DEFAULT '',
  email          VARCHAR(120) DEFAULT '',
  telefono       VARCHAR(30)  DEFAULT '',
  direccion      VARCHAR(250) DEFAULT '',
  ciudad         VARCHAR(80)  DEFAULT '',
  zona           VARCHAR(80)  DEFAULT '',
  estado         ENUM('activo','inactivo','prospecto') NOT NULL DEFAULT 'activo',
  notas          TEXT,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLA: rutas ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS rutas (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  asesor_id      INT          NOT NULL,
  fecha          DATE         NOT NULL,
  hora_inicio    VARCHAR(10)  NOT NULL,
  hora_fin       VARCHAR(10)  DEFAULT '',
  zona           VARCHAR(80)  NOT NULL,
  estado         ENUM('planificada','en-progreso','completada','cancelada') NOT NULL DEFAULT 'planificada',
  vehiculo       VARCHAR(30)  DEFAULT '',
  km_inicial     INT          DEFAULT 0,
  km_final       INT          DEFAULT 0,
  clientes_ids   TEXT         COMMENT 'JSON array de IDs',
  observaciones  TEXT,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (asesor_id) REFERENCES asesores(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLA: satisfacciones ───────────────────────────────────
CREATE TABLE IF NOT EXISTS satisfacciones (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  ruta_id        INT      NOT NULL,
  cliente_id     INT      NOT NULL,
  calificacion   TINYINT  NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
  fecha          DATE     NOT NULL,
  comentarios    TEXT,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ruta_id)    REFERENCES rutas(id)    ON DELETE RESTRICT,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
  UNIQUE KEY uq_ruta_cliente (ruta_id, cliente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── DATOS INICIALES ─────────────────────────────────────────
-- 
INSERT INTO clientes (nombre, nit, tipo, contacto, email, telefono, direccion, ciudad, zona, estado, notas) VALUES
('Supermercado Éxito',   '900123456-7', 'mayorista',   'Juan Pérez',  'juan@exito.com',   '+57 601 234 5678', 'Calle 100 #15-20',  'Medellín', 'Centro', 'activo', 'Prefiere visitas martes y jueves'),
('Distribuidora Andina', '900987654-3', 'distribuidor','María López', 'maria@andina.com', '+57 601 876 5432', 'Carrera 50 #80-30', 'Medellín', 'Norte',  'activo', 'Solicita catálogo digital'),
('Farmacia Central',     '800456789-1', 'minorista',   'Luis Torres', 'luis@farm.com',    '+57 601 345 6789', 'Av. El Poblado 22', 'Medellín', 'Sur',    'activo', 'Pago de contado'),
('Empresa XYZ S.A.',     '800111222-5', 'corporativo', 'Sandra Ríos', 'srios@xyz.com',    '+57 601 999 0000', 'Calle 50 #70-10',   'Bogotá',   'Centro', 'activo', '');

-- ── TABLA: visitas_gps (v2.1) ────────────────────────────
CREATE TABLE IF NOT EXISTS visitas_gps (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  ruta_id        INT          NOT NULL,
  cliente_id     INT          NOT NULL,
  hora_entrada   VARCHAR(10)  NOT NULL,
  hora_salida    VARCHAR(10)  DEFAULT '',
  duracion_min   INT          DEFAULT 0,
  lat_entrada    DECIMAL(10,7) DEFAULT NULL,
  lng_entrada    DECIMAL(10,7) DEFAULT NULL,
  lat_salida     DECIMAL(10,7) DEFAULT NULL,
  lng_salida     DECIMAL(10,7) DEFAULT NULL,
  resultado      ENUM('exitosa','sin_contacto','reagendar','negativa') DEFAULT 'exitosa',
  notas          TEXT,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ruta_id)   REFERENCES rutas(id)    ON DELETE CASCADE,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
