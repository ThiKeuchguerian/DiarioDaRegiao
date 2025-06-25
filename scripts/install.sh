#!/bin/bash

##############################
## Autor: Thiago Guimaraes  ##
##############################

# Arquivo de log
LOGFILE="/var/www/html/config/install.log"

# Redireciona tudo (stdout e stderr) para o log e também exibe na tela
exec > >(tee -a /var/www/html/config/install.log) 2>&1
echo "🔧 Iniciando instalação - $(date)"

#Entrando no diretorio do apache
cd /var/www/html || exit 1
  
# Atualizando o sistema
echo "📦 Atualizando pacotes do sistema..."
apt update

# Removendo itens desnecessários
echo "📦 Removendo pacotes desnecessários..."
apt autoremove

# Ajustando Fuso Horário
echo "🌎 Ajustando fuso horário..."
timedatectl set-timezone America/Sao_Paulo
timedatectl set-ntp true

# Ativando o syslog
echo "📝 Ativando rsyslog..."
systemctl start rsyslog
systemctl enable rsyslog

# Verificar se a pasta Uploads existe se não criar a pasta
if [ ! -d "/var/www/html/uploads" ]; then
  echo "📂 Criando diretório de uploads..."
  mkdir -p /var/www/html/uploads
fi
chown -R www-data:www-data /var/www/html/uploads

# Instalando Composer
if ! command -v composer &> /dev/null; then
  echo "🎯 Instalando Composer..."
  curl -sS https://getcomposer.org/installer | php
  mv composer.phar /usr/local/bin/composer
  chmod +x /usr/local/bin/composer
else
  echo "🎯 Composer já está instalado."
fi

# Verificar se a pasta existe se não criar a pasta
if [ ! -d "/var/www/html/config/composer" ]; then
  echo "📂 Criando diretório de configuração do Composer..."
  mkdir -p /var/www/html/config/composer
fi

# Criando composer.json local, se não existir
if [ ! -f "/var/www/html/config/composer/composer.json" ]; then
  echo "📄 Criando arquivo composer.json..."
  cat > /var/www/html/config/composer/composer.json <<EOF
{
  "name": "diario/projeto",
  "description": "Sistema interno do jornal",
  "type": "project",
  "license": "proprietary",
  "autoload": {
    "psr-4": {
      "Diario\\\\": "../../includes/"
    }
  },
  "require": {}
}
EOF
else
  echo "ℹ️ composer.json já existe. Pulando criação."
fi

# Instalando dependências
echo "📦 Instalando dependências do Composer..."
composer install --working-dir=config/composer/

# Atualizando autoload
echo "🔄 Atualizando autoload do Composer..."
composer dump-autoload --working-dir=config/composer/

# Instalando os requere do composer
echo "📦 Instalando PHPMailer..."
composer require phpmailer/phpmailer:^6.9 --working-dir=config/composer/ --no-interaction

echo "📦 Instalando DomPDF..."
composer require dompdf/dompdf:^2.0 --working-dir=config/composer/ --no-interaction

echo "📦 Instalando OpenBoleto..."
composer require openboleto/openboleto:^1.1 --working-dir=config/composer/ --no-interaction

echo "📦 Instalando Masterminds Twig..."
composer require masterminds/html5:^2.8 --working-dir=config/composer/ --no-interaction

echo "✅ Instalação finalizada com sucesso! ⏱️ $(date)"