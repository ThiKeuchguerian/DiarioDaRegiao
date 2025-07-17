<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/AnaliticoCirculacao.php';

$Titulo = 'Analítico - Circulação';
$URL = URL_PRINCIPAL . 'analitico/Circulacao.php';

// Instanciar a classe
$AnaliticoCirculacao = new AnaliticoCirculacao();

$consultaDados = $AnaliticoCirculacao->consultaContratos();
// $consultaDados = [
//   [
//     'Tipo' => 'Combo',
//     'Produto' => 3,
//     'DataInicio' => '2025-07-01',
//     'DataFinal' => '2026-07-01',
//     'CpfCnpj' => '12345678901',
//     'NomeRazaoSocial' => 'João da Silva',
//     'Cidade' => 'São José do Rio Preto',
//     'UF' => 'SP',
//     'diaDeNascimento' => 15,
//     'mesDeNascimento' => 5,
//     'anoDeNascimento' => 1980,
//     'sexo' => 'M',
//     'CodSituacao' => 1,
//     'SituacaoContrato' => 'Ativo',
//     'DataAssinatura' => '2025-06-28',
//     'ValorContrato' => 480.00,
//     'ValorParcela' => 40.00,
//     'TipoAssinatura' => 'Mensal',
//     'PlanoPagto' => 'CBO - Combo Digital + Impresso'
//   ],
//   [
//     'Tipo' => 'Impresso',
//     'Produto' => 1,
//     'DataInicio' => '2025-07-01',
//     'DataFinal' => '2026-07-01',
//     'CpfCnpj' => '98765432100',
//     'NomeRazaoSocial' => 'Maria Oliveira',
//     'Cidade' => 'Catanduva',
//     'UF' => 'SP',
//     'diaDeNascimento' => 3,
//     'mesDeNascimento' => 11,
//     'anoDeNascimento' => 1975,
//     'sexo' => 'F',
//     'CodSituacao' => 3,
//     'SituacaoContrato' => 'Suspenso',
//     'DataAssinatura' => '2025-06-30',
//     'ValorContrato' => 360.00,
//     'ValorParcela' => 30.00,
//     'TipoAssinatura' => 'Mensal',
//     'PlanoPagto' => 'Plano Impresso Mensal'
//   ],
//   [
//     'Tipo' => 'Digital',
//     'Produto' => 3,
//     'DataInicio' => '2025-07-05',
//     'DataFinal' => '2026-07-05',
//     'CpfCnpj' => '11223344556',
//     'NomeRazaoSocial' => 'Carlos Lima',
//     'Cidade' => 'Mirassol',
//     'UF' => 'SP',
//     'diaDeNascimento' => 22,
//     'mesDeNascimento' => 9,
//     'anoDeNascimento' => 1990,
//     'sexo' => 'M',
//     'CodSituacao' => 1,
//     'SituacaoContrato' => 'Ativo',
//     'DataAssinatura' => '2025-07-03',
//     'ValorContrato' => 240.00,
//     'ValorParcela' => 20.00,
//     'TipoAssinatura' => 'Mensal',
//     'PlanoPagto' => 'Plano Digital Mensal'
//   ],
// ];
$Total = count(array_unique(array_column($consultaDados, 'CpfCnpj')));

// Inicializar contadores
$homens = 0;
$mulheres = 0;
$juridico = 0;
$impresso = 0;
$digital = 0;
$combo = 0;
$cidades = [];
$faixas = [
  'Até 20 anos' => 0,
  '21 a 30 anos' => 0,
  '31 a 40 anos' => 0,
  '41 a 50 anos' => 0,
  '51 a 60 anos' => 0,
  '61 a 70 anos' => 0,
  'Mais de 70 anos' => 0
];

$clientesContabilizados = []; // ← controle de duplicidade

foreach ($consultaDados as $linha) {
  $cpfcnpj = trim($linha['CpfCnpj']);
  $cidade = trim($linha['Cidade']);

  // Verifica se já foi contabilizado
  if (in_array($cpfcnpj, $clientesContabilizados)) {
    continue; // pula se já foi contado
  }

  // Marca como contado
  $clientesContabilizados[] = $cpfcnpj;

  // Contagem de sexo
  $sexo = strtoupper($linha['sexo']);
  if ($sexo == 'M') {
    $homens++;
  } elseif ($sexo == 'F') {
    $mulheres++;
  } elseif ($sexo == 'J') {
    $juridico++;
  }

  // Contagem por cidade
  $cidade = $linha['Cidade'];
  if (!isset($cidades[$cidade])) {
    $cidades[$cidade] = 0;
  }
  $cidades[$cidade]++;

  $anoNascimento = (int)$linha['anoDeNascimento'];
  if ($anoNascimento > 0) {
    $dataNascimento = DateTime::createFromFormat('Y-m-d', $anoNascimento . '-01-01');
    $hoje = new DateTime();
    $idade = $hoje->diff($dataNascimento)->y;
  } else {
    $idade = null;
  }

  // Contabilizar na faixa etária correta
  if ($idade <= 20) {
    $faixas['Até 20 anos']++;
  } elseif ($idade <= 30) {
    $faixas['21 a 30 anos']++;
  } elseif ($idade <= 40) {
    $faixas['31 a 40 anos']++;
  } elseif ($idade <= 50) {
    $faixas['41 a 50 anos']++;
  } elseif ($idade <= 60) {
    $faixas['51 a 60 anos']++;
  } elseif ($idade <= 70) {
    $faixas['61 a 70 anos']++;
  } else {
    $faixas['Mais de 70 anos']++;
  }

  // Contagem de Produto
  $tipo = $linha['Tipo'];
  if ($tipo == 'Impresso') {
    $impresso++;
  } elseif ($tipo == 'Digital') {
    $digital++;
  } elseif ($tipo == 'Combo') {
    $combo++;
  }
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<script>
  const cidadesLabels = <?= json_encode(array_keys($cidades)) ?>;
  const cidadesValores = <?= json_encode(array_values($cidades)) ?>;
  const faixasLabels = <?= json_encode(array_keys($faixas)) ?>;
  const faixasValores = <?= json_encode(array_values($faixas)) ?>;
</script>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Exibindo Resultado -->
<?php if (!empty($Total)) : ?>
  <div class="containers">
    <div class="container my-6">
      <div class="row g-4">

        <!-- Total de Clientes -->
        <div class="col-md-3 col-lg-2">
          <div class="card shadow-sm border-0 rounded-3 p-3 bg-light">
            <div class="text-center">
              <h6 class="text-uppercase text-muted mb-1 fw-bold">Total Clientes</h6>
              <h4 class="fw-bold text-black"><?= $Total ?></h4>
            </div>
          </div>
        </div>

        <!-- Homens e Mulheres -->
        <div class="col-md-5 col-lg-4">
          <div class="card shadow-sm border-0 rounded-3 p-3 bg-light">
            <div class="row">
              <div class="col text-center border-end">
                <h6 class="text-muted mb-1 fw-bold">Homens</h6>
                <h5 class="fw-bold text-primary"><?= $homens ?></h5>
              </div>
              <div class="col text-center border-end">
                <h6 class="text-muted mb-1 fw-bold">Mulheres</h6>
                <h5 class="fw-bold text-danger"><?= $mulheres ?></h5>
              </div>
              <div class="col text-center">
                <h6 class="text-muted mb-1 fw-bold">Jurídico</h6>
                <h5 class="fw-bold text-success"><?= $juridico ?></h5>
              </div>
            </div>
          </div>
        </div>

        <!-- Produto -->
        <div class="col-md-5 col-lg-4">
          <div class="card shadow-sm border-0 rounded-3 p-3 bg-light">
            <div class="row">
              <div class="col text-center border-end">
                <h6 class="text-muted mb-1 fw-bold">Impresso</h6>
                <h5 class="fw-bold text-primary"><?= $impresso ?></h5>
              </div>
              <div class="col text-center border-end">
                <h6 class="text-muted mb-1 fw-bold">Digital</h6>
                <h5 class="fw-bold text-danger"><?= $digital ?></h5>
              </div>
              <div class="col text-center">
                <h6 class="text-muted mb-1 fw-bold">Combo</h6>
                <h5 class="fw-bold text-success"><?= $combo ?></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-6">
          <div class="card p-2 h-100">
            <h5 class="mb-2" style="text-align: center;">Clientes por Cidade</h5>
            <div style="overflow-x: auto;">
              <canvas id="graficoCidades" style="height: 1600px;"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card p-2 h-100">
            <h5 class="mb-2" style="text-align: center;">Faixa Etária</h5>
            <div style="overflow-x: auto;">
              <canvas id="graficoFaixaEtaria" style="height: 600px;"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/analitico_circulacao.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>