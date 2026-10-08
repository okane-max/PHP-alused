FROM php:8.2-apache

# Paigaldame andmebaasi laienduse ja wget tööriista failide tõmbamiseks
RUN apt-get update && apt-get install -y \
    wget \
    && docker-php-ext-install mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Liigume veebiserveri juurkausta
WORKDIR /var/www/html
# Määrame failiõigused õigeks Apache kasutajale
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
