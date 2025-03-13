-- Step 1: Drop foreign key constraints
ALTER TABLE venta DROP FOREIGN KEY venta_ibfk_1;
ALTER TABLE caja DROP FOREIGN KEY caja_ibfk_1;

-- Step 2: Create temporary table with new structure
CREATE TABLE usuario_new (
    usuario_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_nombre VARCHAR(50) NOT NULL,
    usuario_apellido VARCHAR(50) NOT NULL,
    usuario_usuario VARCHAR(20) NOT NULL,
    usuario_email VARCHAR(70),
    usuario_clave VARCHAR(100) NOT NULL,
    usuario_foto VARCHAR(200),
    usuario_cargo VARCHAR(20) NOT NULL,
    caja_id INT NOT NULL,
    PRIMARY KEY (usuario_id)
) ENGINE=InnoDB;

-- Step 3: Copy data from old table to new table
INSERT INTO usuario_new 
SELECT * FROM usuario;

-- Step 4: Drop old table and rename new table
DROP TABLE usuario;
RENAME TABLE usuario_new TO usuario;

-- Step 5: Modify related tables
ALTER TABLE venta MODIFY usuario_id BIGINT UNSIGNED;
ALTER TABLE caja MODIFY usuario_id BIGINT UNSIGNED;

-- Step 6: Recreate foreign key constraints
ALTER TABLE venta ADD CONSTRAINT venta_ibfk_1 
FOREIGN KEY (usuario_id) REFERENCES usuario(usuario_id);

ALTER TABLE caja ADD CONSTRAINT caja_ibfk_1 
FOREIGN KEY (usuario_id) REFERENCES usuario(usuario_id);
