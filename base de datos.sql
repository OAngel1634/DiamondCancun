CREATE DATABASE IF NOT EXISTS diamond_bright;
USE diamond_bright;

-- Tabla de Usuarios (actualizada para coincidir con BD1)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) UNIQUE,  -- Modificado a VARCHAR(255) y posición ajustada
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255),
    fecha_nacimiento DATE,
    telefono VARCHAR(20),
    contacto_emergencia VARCHAR(255),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME,
    foto_perfil VARCHAR(255),
    verificado BOOLEAN DEFAULT FALSE
);

-- Tabla de Preferencias (mantenida como en BD2 original)
CREATE TABLE preferencias (
    usuario_id INT PRIMARY KEY,
    notificaciones ENUM('Activadas', 'Solo reservas', 'Solo promociones', 'Desactivadas') DEFAULT 'Activadas',
    idioma ENUM('Español', 'English', 'Français', 'Deutsch') DEFAULT 'Español',
    preferencias_gastronomicas VARCHAR(100),
    experiencia_favorita VARCHAR(100),
    genero ENUM('Masculino', 'Femenino', 'Otro', 'Prefiero no decir'),
    facilidades_acceso VARCHAR(255),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de Sesiones (mantenida como en BD2 original)
CREATE TABLE sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    user_agent VARCHAR(255),
    ip_address VARCHAR(45),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME NOT NULL,
    activa TINYINT(1) DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tablas adicionales de BD2 (íntegras)
CREATE TABLE tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio_base DECIMAL(10,2) NOT NULL,
    duracion VARCHAR(50) NOT NULL,
    imagen_principal VARCHAR(255),
    tipo ENUM('Isla Mujeres', 'Snorkel', 'Club Playa', 'MUSA', 'Atardecer') NOT NULL,
    dificultad ENUM('Baja', 'Media', 'Alta'),
    restricciones TEXT,
    incluye TEXT,
    activo TINYINT(1) DEFAULT 1
);

CREATE TABLE galeria_tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    imagen_url VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255),
    orden INT DEFAULT 0,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tour_id INT NOT NULL,
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_tour DATE NOT NULL,
    hora_tour TIME NOT NULL,
    numero_adultos INT NOT NULL,
    numero_ninos INT DEFAULT 0,
    precio_total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada') DEFAULT 'pendiente',
    comentarios TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    metodo ENUM('tarjeta', 'efectivo', 'transferencia', 'paypal') NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'completado', 'rechazado', 'reembolsado') DEFAULT 'pendiente',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    transaccion_id VARCHAR(100),
    detalles TEXT,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
);

CREATE TABLE opiniones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tour_id INT NOT NULL,
    calificacion TINYINT NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
    titulo VARCHAR(100),
    comentario TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    aprobada TINYINT(1) DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('reserva', 'pago', 'sistema', 'promocion') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    leida TINYINT(1) DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    enlace VARCHAR(255),
    icono VARCHAR(50),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE configuracion (
    clave VARCHAR(50) PRIMARY KEY,
    valor TEXT NOT NULL,
    descripcion VARCHAR(255)
);

-- Índices
CREATE INDEX idx_sesiones_token ON sesiones(token);
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_reservas_fecha ON reservas(fecha_tour);
CREATE INDEX idx_tours_tipo ON tours(tipo);

-- Datos iniciales
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('EMAIL_CONTACTO', 'info@diamondbright.com', 'Email principal de contacto'),
('TELEFONO_CONTACTO', '+52 998 123 4567', 'Teléfono de contacto'),
('POLITICA_CANCELACION', '48 horas antes sin costo', 'Política de cancelación'),
('PORCENTAJE_ANTICIPO', '30', 'Porcentaje de anticipo para reservas');

INSERT INTO tours (nombre, descripcion, precio_base, duracion, tipo, dificultad, restricciones, incluye) VALUES
('Tour Isla Mujeres', 'Explora la hermosa Isla Mujeres con nuestro tour premium', 120.00, '8 horas', 'Isla Mujeres', 'Baja', 'Edad mínima: 5 años', 'Barra libre, comida, equipo snorkel'),
('Tour de Snorkel', 'Experiencia de snorkel en el arrecife de coral', 85.00, '4 horas', 'Snorkel', 'Media', 'Saber nadar', 'Equipo profesional, guía, bebidas'),
('Club de Playa Premium', 'Acceso exclusivo a nuestro club de playa con buffet gourmet', 65.00, 'Día completo', 'Club Playa', 'Baja', 'None', 'Buffet, bebidas ilimitadas, hamacas'),
('Tour MUSA', 'Visita al Museo Subacuático de Arte con snorkel o buceo', 90.00, '5 horas', 'MUSA', 'Media', 'Certificado de buceo para opción avanzada', 'Equipo, guía, refrigerios'),
('Tour Atardecer Diamante', 'Paseo al atardecer con cena romántica', 85.50, '4 horas', 'Atardecer', 'Baja', 'Reserva previa', 'Cena, bebidas premium, fotografía profesional');

INSERT INTO galeria_tours (tour_id, imagen_url, descripcion) VALUES
(4, 'https://images.unsplash.com/photo-1544551763-46a013bb70d5', 'Escultura "La Evolución Silenciosa"'),
(4, 'https://images.unsplash.com/photo-1579546929662-711aa81148cf', 'El Hombre en Llamas'),
(4, 'https://images.unsplash.com/photo-1530541930197-ff16ac917b0e', 'El Coleccionista de Sueños'),
(4, 'https://images.unsplash.com/photo-1506929562872-bb421503ef21', 'Jardín de la Esperanza');