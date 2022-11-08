FROM phpdockerio/php80-fpm:latest

WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php-memcached php-imagick php-pear php8.0-dev php8.0-bcmath php8.0-cli php8.0-common php8.0-curl php8.0-fpm php8.0-gd php8.0-imap php8.0-intl php8.0-ldap php8.0-mbstring php8.0-mysql php8.0-opcache php8.0-pgsql php8.0-readline php8.0-soap php8.0-sqlite3 php8.0-tidy php8.0-xml php8.0-xmlrpc php8.0-xsl php8.0-zip --allow-unauthenticated

# Install git
RUN apt-get -y install git

# DEPENDENCIES
RUN apt-get -y install curl wget apt-transport-https ca-certificates gnupg2 apt-utils default-jre mc nano build-essential

# COMPOSER
RUN curl -s https://getcomposer.org/installer | php &&  mv composer.phar /usr/local/bin/composer

#codesniffer
RUN composer global require squizlabs/php_codesniffer

# SQLSRV
RUN apt-get update \
	&& apt-get -y --no-install-recommends install build-essential php-pear php8.0-dev \
	&& curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
	&& curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list \
	&& apt-get update \
	&& ACCEPT_EULA=Y apt-get -y install msodbcsql17 mssql-tools unixodbc-dev \
    && pecl install sqlsrv pdo_sqlsrv  \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    && echo "extension=sqlsrv.so" >> /etc/php/8.0/fpm/conf.d/20-sqlsrv.ini && echo "extension=pdo_sqlsrv.so" >> /etc/php/8.0/fpm/conf.d/30-pdo_sqlsrv.ini \
    && echo "extension=sqlsrv.so" >> /etc/php/8.0/cli/conf.d/20-sqlsrv.ini && echo "extension=pdo_sqlsrv.so" >> /etc/php/8.0/cli/conf.d/30-pdo_sqlsrv.ini

RUN echo 'env[ENVIRONMENT] = dockerserver' >> /etc/php/8.0/fpm/pool.d/www.conf



