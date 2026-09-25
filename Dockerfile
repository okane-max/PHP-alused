FROM php:8.2-apache

# Paigaldame vajalikud laiendused andmebaasi jaoks ja tööriistad koodi tõmbamiseks
RUN apt-get update && apt-get install -y \
    wget \
    && docker-php-ext-install mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Määrame töökataloogi
WORKDIR /var/www/html

# Lubame Apache 'rewrite' mooduli (vajadusel ilusamate linkide jaoks)
RUN a2enmod rewrite

# Paljastame pordi 80
EXPOSE 80

CMD ["apache2-foreground"]
