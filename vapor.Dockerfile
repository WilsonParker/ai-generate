FROM laravelphp/vapor:php82

# Add the `ffmpeg` library...
RUN apk --update add ffmpeg gmp gmp-dev imagemagick libwebp-dev libpng-dev;

RUN pecl install -o -f imagick\
    &&  docker-php-ext-enable imagick

RUN docker-php-ext-configure gd --with-webp --with-freetype --with-jpeg;
RUN docker-php-ext-install gd gmp exif;

COPY ./php.ini /usr/local/etc/php/conf.d/overrides.ini

COPY . /var/task
