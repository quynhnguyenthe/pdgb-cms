FROM wyveo/nginx-php-fpm:php80

ARG DOT_ENV
ENV DOT_ENV=$DOT_ENV

RUN apt-get update -y && apt-get install vim curl -y && apt-get install netcat -y && apt-get install php8.0-xml php8.0-redis php8.0-intl   php8.0-zip php8.0-mbstring php8.0-gd php8.0-curl -y \
    build-essential \
    default-mysql-client \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    libzip-dev \
    libonig-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    php8.0-bcmath  \
	&& apt-get purge -y --auto-remove $buildDeps \
    && apt-get clean \
    && apt-get autoremove \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update && \
    apt-get install -y ffmpeg

# Set working directory
COPY docker/nginx/app.conf /etc/nginx/conf.d/default.conf
COPY docker/php/www.conf /etc/php/8.0/fpm/pool.d/www.conf
COPY docker/php/php-fpm.conf /etc/php/8.0/fpm/php-fpm.conf
COPY docker/php/php.ini /etc/php/8.0/fpm/php.ini
#RUN service php8.0-fpm start

# Worker
COPY docker/start.sh /start.sh
RUN chmod 755 /start.sh
WORKDIR /usr/share/nginx/html
COPY . .
#COPY .env.example ./.env
RUN echo "$DOT_ENV" > ./.env
RUN cp ./.env ./.env.production
RUN composer update
RUN chmod -R 777 ./docker
RUN chmod 777 -R ./.git

RUN chown -R nginx:nginx .
RUN find . -type d -exec chmod 755 {} \;
RUN find . -type f -exec chmod 644 {} \;
RUN chmod -R ug+rwx storage public bootstrap

RUN chmod +x /start.sh
CMD ["/start.sh"]
