FROM php:8.2-apache

# Paigaldame vajaliku MySQLi laienduse
RUN docker-php-ext-install mysqli

# Lubame Apache url-ide ümberkirjutamise (vajadusel)
RUN a2enmod rewrite

# Määrame töökataloogi
WORKDIR /var/www/html
