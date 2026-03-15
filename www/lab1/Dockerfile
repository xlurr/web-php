FROM php:8.2-apache

# Включаем необходимые PHP-расширения
RUN docker-php-ext-install mysqli && \
  a2enmod rewrite

# Копируем код в контейнер
COPY www/ /var/www/html/

# Устанавливаем права доступа
RUN chown -R www-data:www-data /var/www/html && \
  chmod -R 755 /var/www/html

# Устанавливаем DirectoryIndex (какие файлы по умолчанию открывать)
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/mods-enabled/dir.conf

# Открываем порт 80
EXPOSE 80

# Запускаем Apache
CMD ["apache2-foreground"]
