#!/bin/bash

##############################
## Autor: Thiago Guimaraes  ##
##############################

#Entrando no diretorio do apache
  cd /var/www/html
  
# Atualizando o sistema
  apt update

# Instalando itens essenciais
  apt install -y software-properties-common vim net-tools less pciutils tcpdump nmap rsyslog ntp
  apt autoremove

# Ajustando Fuso Horário
  timedatectl set-timezone America/Sao_Paulo
  timedatectl set-ntp true

# Ativando o syslog
  systemctl start rsyslog
  systemctl enable rsyslog

# Instalando Composer
  apt install -y php-cli php-mbstring unzip curl
  curl -sS https://getcomposer.org/installer | php
  mv composer.phar /usr/local/bin/composer
  chmod +x /usr/local/bin/composer

  php composer.phar config --global process-timeout 2000
  php composer.phar config --global repos.packagist composer https://packagist.org
  php composer.phar config --global preferred-install dist
  php composer.phar config --global discard-changes true
  php composer.phar config --global autoloader-suffix "myapp"
  php composer.phar config --global minimum-stability stable
  php composer.phar config --global prefer-stable true
  php composer.phar config --global archive format tar
  php composer.phar config --global archive remote-archive true
  php composer.phar config --global archive directory /tmp
  php composer.phar config --global archive format tar
  php composer.phar config --global archive remote-archive true
  php composer.phar config --global archive directory /tmp
  php composer.phar config --global cache-dir /tmp
  php composer.phar config --global cache-files-dir /tmp
  php composer.phar config --global cache-repo-dir /tmp
  php composer.phar config --global cache-vcs-dir /tmp

  composer update
  composer require dompdf/dompdf
  composer require phpmailer/phpmailer
  composer require openboleto/openboleto

