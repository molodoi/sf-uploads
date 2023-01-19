FROM php:8.1-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
  \
  &&  apt-get update \
  &&  apt-get install -y --no-install-recommends \
  libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
  jpegoptim optipng pngquant gifsicle \
  locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip \
  \
  &&  echo "en_US.UTF-8 UTF-8" > /etc/locale.gen  \
  &&  echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
  &&  locale-gen \
  \
  &&  curl -sS https://getcomposer.org/installer | php -- \
  &&  mv composer.phar /usr/local/bin/composer \
  \
  && curl -sL https://deb.nodesource.com/setup_18.x | bash \
  && apt-get install nodejs \
  \
  &&  curl -sS https://get.symfony.com/cli/installer | bash \
  &&  mv /root/.symfony5/bin/symfony /usr/local/bin \
   \ 
  &&  docker-php-ext-configure \
  gd --with-jpeg=/usr/include \
  && docker-php-ext-configure intl \
  &&  docker-php-ext-install \
  pdo pdo_mysql opcache intl zip calendar dom mbstring -j$(nproc) gd xsl \
  \
  &&  pecl install apcu && docker-php-ext-enable apcu

WORKDIR /var/www/

# COPY . /var/www
# RUN useradd -G www-data,root -u $uid -d /home/$user $user
# USER root

# RUN rm -rf /var/www/var/cache/prod \
#     && rm -rf /var/www/var/cache/dev

# RUN chown -R www-data /var/www/
# RUN chmod -fR 777 .

# USER www-data


# RUN chown -R :www-data ./
# RUN chmod -R 777 ./
# RUN addgroup -g ${GID} --system molodoi
# RUN adduser -G molodoi --system -D -s /bin/sh -u ${UID} molodoi
# RUN addgroup molodoi && adduser -D -G molodoi molodoi && chown -R molodoi:molodoi ./
# RUN addgroup -g 1000 -S .  
# RUN adduser -u 1000 -S . -G .
# RUN addgroup -g 1000 -S www-data
# RUN adduser -u 1000 -H -D -S -G www-data www-data

# RUN groupadd -g 1000 www-data
# RUN useradd -u 1000 -ms /bin/bash -g www www

# # Copy existing application directory contents
# COPY . /var/www

# # Copy existing application directory permissions
# COPY --chown=www-data:www-data . /var/www

# # Change current user to www
# USER www-data
# ADD . /var/www
# RUN useradd -G www-data,root -u $UID -d /home/$USER $USER
# USER $USER
# RUN chown -R www-data:www-data /var/www
# RUN chmod -R 755 /var/www/public
# docker exec -ti www_sfuploads bash
# sudo chown -R $USER ./