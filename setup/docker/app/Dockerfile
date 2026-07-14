FROM dunglas/frankenphp:php8.5-alpine

ARG USER=www

# Install basic dependencies
RUN apk update  &&  apk upgrade  &&  apk add --update --no-interactive \
	# Basic packages \
	icu-data-full \
	# cURL \
	#curl \
	# Bash (for installing tools, like Bun) \
	#bash \
	# ZIP Tools \
	zip unzip \
	# 7ZIP Tools \
	#7zip \
	# Sending emails \
	exim \
	# Office Documents (converting to PDF) \
	#libreoffice-writer libreoffice-calc default-jre libreoffice-java-common \
	# PNG Tools \
	oxipng \
	# HEIF/HEIC Tools \
	#libheif libde265 \
	# PDF Tools \
	poppler \
	# Audio Tools \
	sox \
	# Video and Audio Tools \
	#ffmpeg \
	# VIPS tools \
	vips libffi vips-heif vips-jxl vips-poppler \
	# Virtual Framebuffer Display for programs with GUI \
	xvfb xvfb-run \
	# Libraries for running MuseScore \
	#libasound2 libfontconfig1 libglib2.0-0 libjack0 libnss3 \
	# MuseScore Editor \
	musescore \
	# `pg_dump` and `psql` for `artisan db:dump` and `artisan db:restore` commands \
	postgresql-client \
	# Git \
	git

# Install PHP with extensions: for CLI
RUN apk add --no-interactive \
	# Basic packages (always needed) \
	php85-cli php85-bcmath php85-calendar \
	php85-ctype php85-curl php85-dom php85-fileinfo php85-iconv php85-intl \
	php85-mbstring php85-openssl php85-pcntl php85-pdo \
	php85-phar php85-posix php85-session php85-simplexml php85-sockets \
	php85-tokenizer php85-xml php85-xmlreader php85-xmlwriter \
	# For databases and caches \
	php85-pgsql php85-pdo_pgsql \
	#php85-redis \
	# For graphics \
	php85-ffi php85-gd \
	# For SOAP integrations \
	#php85-soap \
	# For ZIP \
	php85-zip

#RUN ln -s /usr/bin/php85 /usr/local/bin/php

# Install PHP extensions: for FrankenPHP
RUN install-php-extensions \
	# Basic packages (always needed) \
	bcmath calendar intl pcntl sockets \
	# For databases and caches \
	pgsql pdo_pgsql \
	#redis \
	# For image processing \
	ffi gd \
	# For SOAP integrations \
	#soap \
	# For ZIP \
	zip

# Install composer
COPY --from=composer/composer:latest-bin /composer /usr/local/bin/composer

# Install NodeJS
#RUN apk add --no-interactive nodejs-current

# Install Bun (instead of NodeJS)
COPY --from=oven/bun:alpine /usr/local/bin/bun /usr/local/bin/bun
RUN ln -s /usr/local/bin/bun /usr/local/bin/bunx

# Clear caches
RUN apk cache clean

# Configure Exim4 to send emails from the container
#RUN sed -i "s/dc_eximconfig_configtype='local'/dc_eximconfig_configtype='internet'/g" \
#	/etc/exim/exim.conf  &&  update-exim4.conf

# Add user for the application
RUN addgroup "$USER"  &&  adduser --disabled-password --ingroup "$USER" "$USER"

# Remove default capability
#RUN setcap -r /usr/local/bin/frankenphp
# Give write access to /data/caddy and /config/caddy
RUN chown -Rf "${USER}:${USER}" /data/caddy  /config/caddy

# Copy application files
#COPY --link . /var/www
#RUN chown "${USER}:${USER}" -R /var/www/storage

# Set current user and working directory
USER "$USER"
WORKDIR /var/www
