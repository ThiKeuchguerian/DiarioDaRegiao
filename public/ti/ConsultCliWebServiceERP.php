<?php
require_once __DIR__ . '/../config/config.php';
// require_once __DIR__ . '/../classes/Functions/IntegracaoCliVendAg.php';

$Titulo = 'Consulta Cliente WebService Senior';
$URL = URL_PRINCIPAL . 'ti/ConsultCliWebServiceERP.php';

// Instanciar a classe
// $IntegracaoCliVendAg = new IntegracaoCliVendAg();

// Declarando Variáveis;

// URL do WSDL
$wsdl = 'http://10.64.0.89:8080/g5-senior-services/sapiens_Synccom_senior_g5_co_ger_cad_clientes?wsdl';


if (isset($_POST['btn-buscar'])) {
  $nomeCli = $_POST['NomeCli'];
  $cpfCnpj = $_POST['cpfCnpj'];

  $client = new SoapClient($wsdl, [
    'trace' => true,
    'exceptions' => true,
    'cache_wsdl' => WSDL_CACHE_NONE
  ]);

  // Monta o struct clientesConsultarCadastroIn
  $paramsIn = [
    // struct clientesConsultarCadastroInCgcCpf { double cgcCpf; }
    'cgcCpf' => [
      'cgcCpf' => $cpfCnpj
    ],
    // struct clientesConsultarCadastroInCodCli { int codCli; }
    'codCli' => [
      'codCli' => 0    // se você não tiver o código do cliente, passe 0
    ],

    // Demais campos obrigatórios do struct
    'codEmp'               => 2,      // seu código de empresa
    'codFil'               => 1,      // seu código de filial
    'flowInstanceID'       => '',
    'flowName'             => '',
    'identificadorSistema' => 'CADENA',
    'indicePagina'         => 1,
    'limitePagina'         => 1,     // quantos registros por página

    // struct clientesConsultarCadastroInSigUfs { string sigUfs; }
    'sigUfs' => [
      'sigUfs' => ''
    ],

    'sitCli' => '',
    'tipCli' => '',

    // struct clientesConsultarCadastroInTipMer { string tipMer; }
    'tipMer' => [
      'tipMer' => ''
    ],
  ];

  // // imprime todos os tipos que o WSDL define:
  // echo "<pre>";
  // print_r($client->__getTypes());
  // print_r($client->__getFunctions());
  // print_r($client->__getTypes());
  // echo "</pre>";
  // die();

  try {
    // assinatura: ConsultarCadastro(string $user, string $password, int $encryption, clientesConsultarCadastroIn $parameters)
    $response = $client->ConsultarCadastro(
      'cadena',      // user
      'cadena',      // password
      0,              // encryption
      $paramsIn       // parâmetros do tipo clientesConsultarCadastroIn
    );

    // echo '<h3>Resultado:</h3><pre>';
    // print_r($response);
    // echo '</pre>';
  } catch (SoapFault $e) {
    // echo '<h3>Erro:</h3><pre>';
    // print_r($e);
    // echo '</pre>';
  }
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Nome Cliente</strong>
            </div>
            <div class="col">
              <strong>CPF / CNPJ</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="NomeCli" name="NomeCli" maxlength="20">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="cpfCnpj" name="cpfCnpj" maxlength="14">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-analitico" name="btn-analitico" type="submit" class="btn btn-primary btn-sm">Analítico</button>
            <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="mb-3"></div>

<div class="container">
  <div class="card shadow-sm">
    <div class="card-body">

      <?php
      if (isset($response)) {
        echo '<h3>Resultado:</h3><pre>';
        print_r($response);
        echo '</pre>';
      } else {
        echo '<h3>Erro:</h3><pre>';
        print_r($e);
        echo '</pre>';
      }

      ?>
    </div>
  </div>
</div>
<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Incluindo JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/integracao_clivenag.js"></script>

<!-- Incluindo Footer -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>