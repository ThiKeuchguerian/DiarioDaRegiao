<?php
require_once __DIR__ . '/../config/config.php';

$Titulo = 'Testar Conexão com Banco de Dados';
$URL = URL_PRINCIPAL . 'ti/TesteConexao.php';


// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<div class="content-only">
  <h1>Resultados da Conexão com Banco de Dados</h1>
  <?php
  function testarConexao($dsn, $usuario, $senha)
  {
    try {
      $conn = new PDO($dsn, $usuario, $senha);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo "<p>Conexão com o banco de dados '" . htmlspecialchars($dsn) . "' bem-sucedida com o Usuário: " . htmlspecialchars($usuario) . "!! </p>";
    } catch (PDOException $e) {
      echo "<p>Erro ao conectar com o banco de dados '" . htmlspecialchars($dsn) . "': " . htmlspecialchars($e->getMessage()) . "</p>";
    }
  }

  // Configurações dos bancos de dados
  $configuracoes = [
    ['dsn' => "sqlsrv:Server=10.64.0.11,1433;Database=gestor;", 'usuario' => 'gestor', 'senha' => 'G3s10R_LL'],
    ['dsn' => "sqlsrv:Server=10.64.0.11,1433;Database=gestor;", 'usuario' => 'cmsdiario', 'senha' => 'Diario123!'],
    ['dsn' => "sqlsrv:Server=10.64.0.14,1433;Database=gestor;", 'usuario' => 'gestor', 'senha' => 'gestor'],
    ['dsn' => "sqlsrv:Server=10.64.0.90,1433;Database=sapiens;", 'usuario' => 'sapiens', 'senha' => 'sapiens'],
    ['dsn' => "sqlsrv:Server=10.64.0.36,1433;Database=sapiens_teste;", 'usuario' => 'sapiens_teste', 'senha' => 'sapiensteste'],
    ['dsn' => "sqlsrv:Server=10.64.0.13,1433;Database=EasyClass;", 'usuario' => 'tecmidia', 'senha' => 'tecsams'],
    ['dsn' => "sqlsrv:Server=172.16.0.15,1433;Database=WebTake;", 'usuario' => 'tecmidia', 'senha' => 'tecsams'],
    ['dsn' => "sqlsrv:Server=10.64.0.25\\SQLEXPRESS;Database=cadena;", 'usuario' => 'capt', 'senha' => 'capt'],
    ['dsn' => "sqlsrv:Server=10.64.0.7\\MICROSIGA;Database=PROTHEUS;", 'usuario' => 'totvs', 'senha' => 'totvs']

  ];

  // Testar conexões
  foreach ($configuracoes as $config) {
    testarConexao($config['dsn'], $config['usuario'], $config['senha']);
  }
  ?>
  <a class="btn btn-primary" href="<?= URL_PRINCIPAL ?>"> Voltar </a>
</div>


<?php 

require_once __DIR__ . '/../includes/footer.php';