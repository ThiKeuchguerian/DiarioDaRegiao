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
function depurar(...$vars)
{
  echo '<pre>';
  foreach ($vars as $var) {
    var_dump($var);
  }
  echo '</pre>';
  die();
}

// Função global para depurar retornando JSON
function depurarJson(...$vars)
{
  header('Content-Type: application/json');
  if (count($vars) === 1) {
    echo json_encode($vars[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  } else {
    echo json_encode($vars, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }
  die(); // Interrompe a execução após a depuração
}
