<?php
// Exibição de Erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('memory_limit', '512M');

// Configurações Variáveis Globais
define('URL_PRINCIPAL', 'http://devsistemas.diariodaregiao.com.br/');
define('SITE_TITLE',    'Grupo Diário da Região');
define('LOGO',          '/config/../img/logo_write.svg');
define('FAVICON',       '/config/../img/favicon.ico');

// Função global para depuração
function depurar($var)
{
  echo '<pre>';
  var_dump($var);  
  echo '</pre>';
  die();
}
