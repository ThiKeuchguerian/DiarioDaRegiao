FROM php:7.4-apache

ARG DEBIAN_FRONTEND=noninteractive

ENV TZ="America/Sao_Paulo"

WORKDIR /var/www/html/

COPY ./config/apache /etc/apache2/sites-enabled
COPY ./config/php /usr/local/etc/php/php.ini
COPY ./config/php/disable-opcache.ini /usr/local/etc/php/conf.d/disable-opcache.ini
COPY ./config/ssl /etc/ssl/
# copia o ini para desabilitar totalmente o opcache

# atualizações
RUN apt-get -y update --fix-missing && \
  apt-get upgrade -y && \
  apt-get --no-install-recommends install -y apt-utils vim net-tools tree unixodbc unixodbc-dev ca-certificates ssh libssh2-1 libssh2-1-dev openssh-client pkg-config && \
  apt-get install -y apt-transport-https gnupg wget libcurl4-openssl-dev libedit-dev libsqlite3-dev libssl-dev libxml2-dev zlib1g-dev libpng-dev libmcrypt-dev libjpeg-dev && \
  apt-get install -y freetds-dev freetds-bin freetds-common libdbd-freetds libsybdb5 libqt5sql5-tds libzip-dev zip unzip locales && \
  apt-get install -y software-properties-common less pciutils tcpdump rsyslog ntp curl && \
  rm -rf /var/lib/apt/lists/*
RUN pecl install ssh2

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# extenções PHP para SQL Server
RUN install-php-extensions odbc
RUN install-php-extensions pdo
RUN install-php-extensions pdo_dblib
RUN install-php-extensions pdo_odbc
RUN install-php-extensions pdo_oci
RUN install-php-extensions pdo_sqlsrv
RUN install-php-extensions sqlsrv
RUN install-php-extensions mysqli
RUN install-php-extensions pdo_mysql
RUN install-php-extensions sysvmsg
RUN install-php-extensions sysvsem
RUN install-php-extensions sysvshm
RUN install-php-extensions xmlrpc
RUN install-php-extensions gd
RUN install-php-extensions curl
RUN install-php-extensions zip
RUN install-php-extensions opcache
RUN install-php-extensions calendar
RUN install-php-extensions sodium
RUN install-php-extensions soap
RUN install-php-extensions pdo_firebird

# apache e SSL
RUN a2enmod ssl && a2enmod rewrite
RUN a2enmod rewrite headers mpm_prefork

# cleanup
RUN rm -rf /usr/src/*

# PHP composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalando Drive ODBC 17
RUN curl https://packages.microsoft.com/keys/microsoft.asc | tee /etc/apt/trusted.gpg.d/microsoft.asc
RUN curl https://packages.microsoft.com/config/debian/11/prod.list | tee /etc/apt/sources.list.d/mssql-release.list
RUN apt-get update
RUN ACCEPT_EULA=Y apt-get install -y msodbcsql17
RUN ACCEPT_EULA=Y apt-get install -y mssql-tools
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc
# RUN source ~/.bashrc
RUN apt-get install -y unixodbc-dev
RUN apt-get install -y libgssapi-krb5-2

RUN mkdir /keys

RUN chown -R www-data:www-data /keys \
  && chmod -R 755 /keys

COPY ./scripts/install.sh /srv/install.sh
RUN chmod +x /srv/install.sh
RUN bash /srv/install.sh

RUN service apache2 restart
CMD ["apachectl", "-D", "FOREGROUND"]
