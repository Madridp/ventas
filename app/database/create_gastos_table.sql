CREATE TABLE gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razon VARCHAR(255) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fondo ENUM('Efectivo', 'Transferencia', 'Tarjeta') NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
