FROM php:8.2-apache

# Paigaldame MySQL toe PHP jaoks
RUN docker-php-ext-install pdo pdo_mysql

# Lubame Apache url-ide ümberkirjutamise (vajadusel)
RUN a2enmod rewrite

# Kopeerime projektifailid konteinerisse
COPY . /var/www/html/

# Määrame õigused Apache kasutajale
RUN chown -W www-data:www-data /var/www/html/
