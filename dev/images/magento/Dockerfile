ARG base_image=php:7.1-apache
FROM $base_image

# Installing packages required to run Magento.
RUN apt-get update \
 && apt-get install -y libfreetype6-dev libicu-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev libxslt1-dev libsodium-dev sendmail-bin sendmail unzip sudo \
 && rm -rf /var/lib/apt/lists/*

# Building PHP extensions required to run Magento.
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
 && docker-php-ext-install dom gd intl mbstring pdo_mysql xsl zip soap bcmath opcache mcrypt

# Installing composer as a globally available system command.
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php \
 && php -r "unlink('composer-setup.php');" \
 && mv composer.phar /usr/local/bin/composer

# Configure PHP and Apache to run Magento.
ENV PHP_MEMORY_LIMIT 2G
ENV MAGENTO_ROOT /var/www/magento
ADD etc/php.ini /usr/local/etc/php/conf.d/zz-magento.ini
ADD etc/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite \
 && chown -R www-data:www-data /var/www

# Using user www-data to install Magento.
USER www-data

# Setting composer auth to be able to fetch packages from Magento composer repo.
ARG public_key
ARG private_key
ENV COMPOSER_AUTH {\"http-basic\": {\"repo.magento.com\": {\"username\":\"$public_key\", \"password\": \"$private_key\"}}}

# Install prestissimo to speedup install
RUN composer global require hirak/prestissimo

# Prefetch Magento packages.
ARG magento_version=2.3.0
ARG magento_edition=community
RUN composer create-project --repository=https://repo.magento.com/ magento/project-${magento_edition}-edition:$magento_version $MAGENTO_ROOT

# Install sample data if required.
ARG use_sample_data=1
WORKDIR $MAGENTO_ROOT
RUN if [ $use_sample_data -eq 1 ]; then bin/magento sampledata:deploy; fi

# Preconfigure Magento
COPY --chown=www-data env.php app/etc/env.php

# Fix perms in Magento directories. Ensure all command are run as www-data.
ENV HOME /var/www

# Add local repo to work on the extension.
RUN composer config repositories.app-search '{"type": "path", "url": "./app-search-module"}'

# Revert original user (root) to run Apache.
USER root
