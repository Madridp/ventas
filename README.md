# Sistema de Ventas y Control de Inventario

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-blue.svg)](https://www.mysql.com)
[![Version](https://img.shields.io/badge/Version-1.0.0-green.svg)](https://github.com/your-repo/ventas)
[![Framework](https://img.shields.io/badge/MVC-Custom-orange.svg)]()
[![UI](https://img.shields.io/badge/UI-Bulma%200.9.3-00d1b2.svg)](https://bulma.io)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

Un sistema completo de gestión de ventas y control de inventario desarrollado con PHP, MySQL, siguiendo el patrón MVC, con AJAX para interacciones dinámicas y BULMA para una interfaz moderna y responsive.

## 📈 Características Principales

- **Gestión de Ventas**
  - Registro de ventas con múltiples productos
  - Generación de comprobantes en PDF
  - Historial de transacciones
  - Diferentes métodos de pago (Efectivo, Transferencia, Tarjeta)

- **Control de Inventario**
  - Gestión de productos y categorías
  - Control de stock
  - Alertas de stock bajo
  - Registro de entradas y salidas

- **Gestión de Gastos**
  - Registro de gastos por categoría
  - Control de caja
  - Reportes de gastos

- **Administración de Usuarios**
  - Múltiples niveles de acceso
  - Gestión de permisos
  - Registro de actividades

## 🛠️ Tecnologías Utilizadas

- **Backend**
  - PHP 7.4+
  - MySQL 5.7+
  - Apache/Nginx
  - TCPDF 6.4.4 (Generación de PDF)

- **Frontend**
  - HTML5
  - CSS3
  - JavaScript ES6+
  - AJAX
  - Bulma CSS 0.9.3

- **Arquitectura**
  - Patrón MVC
  - PDO para conexión a base de datos
  - Routing personalizado

## 📋 Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensiones PHP requeridas:
  - PDO
  - MySQL
  - mbstring
  - xml

## 📦 Instalación

1. **Preparación del Proyecto**
   - Clone o descargue este repositorio en su servidor web
   - Asegúrese que la carpeta tenga los permisos correctos (755 para carpetas, 644 para archivos)

2. **Base de Datos**
   - Cree una nueva base de datos MySQL
   - Importe el archivo de base de datos ubicado en `DB/ventas.sql`

3. **Configuración**
   - En la carpeta `config`, edite el archivo `server.php`:
     ```php
     // Configure los datos de conexión a su base de datos
     'HOST' => 'localhost',
     'DB' => 'nombre_base_datos',
     'USER' => 'usuario',
     'PASS' => 'contraseña'
     ```
   - Configure el archivo `config/app.php`:
     ```php
     'APP_NAME' => 'Nombre de su Empresa',
     'APP_URL' => 'http://su-dominio/ventas/'
     ```

## 🔓 Acceso al Sistema

**Credenciales por defecto:**
- Usuario: `Administrador`
- Contraseña: `Administrador`

**Importante:** Por seguridad, cambie la contraseña después del primer inicio de sesión.

## 📊 Uso del Sistema

1. **Panel de Control**
   - Vista general de ventas, inventario y métricas importantes
   - Acceso rápido a funciones principales

2. **Gestión de Ventas**
   - Crear nueva venta: `Ventas -> Nueva Venta`
   - Historial de ventas: `Ventas -> Historial`
   - Reportes: `Ventas -> Reportes`

3. **Inventario**
   - Agregar productos: `Inventario -> Nuevo Producto`
   - Gestionar stock: `Inventario -> Stock`
   - Categorías: `Inventario -> Categorías`

4. **Control de Gastos**
   - Registrar gasto: `Gastos -> Nuevo`
   - Ver reportes: `Gastos -> Reportes`
   - Control de caja: `Gastos -> Caja`

## 🤝 Soporte

Para reportar problemas o solicitar ayuda:
1. Abra un issue en este repositorio
2. Describa detalladamente el problema
3. Incluya capturas de pantalla si es necesario

## 📜 Licencia

Este proyecto está bajo la Licencia MIT - vea el archivo `LICENSE` para más detalles.