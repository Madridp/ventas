-- SQL Query to add necessary columns to the gastos table
ALTER TABLE gastos ADD COLUMN fecha DATE;
ALTER TABLE gastos ADD COLUMN hora VARCHAR(15);
ALTER TABLE gastos ADD COLUMN usuario_id INT;
