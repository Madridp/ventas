FROM php:8.0-apache

# Copiar los archivos del proyecto al directorio de trabajo de Apache
COPY . /var/www/html/

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Asegurarse que index.php sea el archivo por defecto
RUN echo "DirectoryIndex index.php" >> /etc/apache2/apache2.conf
