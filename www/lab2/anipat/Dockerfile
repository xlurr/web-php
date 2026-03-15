FROM php:8.2-apache

# Для поддержки includes с кириллицей и русских имен файлов
RUN apt-get update && apt-get install -y locales && locale-gen ru_RU.UTF-8

# Включаем mod_rewrite если понадобится
RUN a2enmod rewrite

# Копируем код в контейнер
COPY www/ /var/www/html/

# Ставим нужные права и указываем индексный файл (если понадобится)
RUN chown -R www-data:www-data /var/www/html && \
  chmod -R 755 /var/www/html && \
  echo "DirectoryIndex index.php index.html" >> /etc/apache2/mods-enabled/dir.conf

EXPOSE 80

CMD ["apache2-foreground"]
