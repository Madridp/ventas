CREATE TABLE gastos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    razon VARCHAR(255) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fondo ENUM('Efectivo', 'Transferencia', 'Tarjeta') NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    caja_id INT(11)
);
