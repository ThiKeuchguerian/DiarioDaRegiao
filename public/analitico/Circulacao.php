<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/AnaliticoCirculacao.php';

$Titulo = 'Analítico - Circulação';
$URL = URL_PRINCIPAL . 'analitico/Circulacao.php';

// Instanciar a classe
$AnaliticoCirculacao = new AnaliticoCirculacao();

if (isset($_POST['btn-geral'])) {
  $consultaDados = $AnaliticoCirculacao->consultaContratos();
  $consultaContaDiario = $AnaliticoCirculacao->consultaContaDiario();
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
  $geral = count(array_unique(array_column($consultaDados, 'CpfCnpj')));

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

  $TotalContaDiario = count($consultaContaDiario);
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

    if ($sexo != 'J') {
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

  $tipo = 0;
  $assDigital = 0;
  $assHolos = 0;
  $tipoAss = 0;
  $assAnual = 0;
  $assMensal = 0;
  foreach ($consultaContaDiario as $item) {
    // depurar($item);
    $tipo = ($item['type']);
    if ($tipo == 'paid_pass') {
      $assDigital++;
    } elseif ($tipo == 'free_pass') {
      $assHolos++;
    }
    if ($tipo == 'paid_pass') {
      $tipoAss = ($item['name']);
      if ($tipoAss == 'Plano Anual / Digital') {
        $assAnual++;
      } elseif ($tipoAss == 'Plano Mensal / Digital') {
        $assMensal++;
      }
    }
  }
} else if (isset($_POST['btn-analitico'])) {
  $consultaDados = $AnaliticoCirculacao->consultaContratos();
  $TotalCliHolos = count($consultaDados);
  $TotalClientes = count(array_unique(array_column($consultaDados, 'CpfCnpj')));

  $consultaContaDiario = $AnaliticoCirculacao->consultaContaDiario();
  $TotalCliContaD = count($consultaContaDiario);
  $TotalClientesCD = count(array_filter($consultaContaDiario, function ($item) {
    return isset($item['type']) && $item['type'] === 'paid_pass';
  }));

  $Total = count($consultaDados) + count($consultaContaDiario);
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

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-2">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="Analitico" name="Analitico">
        <div class="card-header bg-primary text-white">
        </div>
        <div class="card-footer d-flex justify-content-center">
          <div class="col text-center">
            <button id="btn-geral" name="btn-geral" type="submit" class="btn btn-primary btn-sm">Geral</button>
            <button id="btn-analitico" name="btn-analitico" type="submit" class="btn btn-primary btn-sm">Analítico</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Exibindo Resultado Geral -->
<?php if (!empty($geral)) : ?>
  <div class="container my-6">
    <div class="row g-4 justify-content-center text-center">
      <!-- Total de Clientes -->
      <div class="col-md-3 col-lg-2">
        <div class="card shadow-sm border-0 rounded-3 p-3 bg-light">
          <div class="text-center">
            <h6 class="text-uppercase text-muted mb-1 fw-bold">Total Geral</h6>
            <h4 class="fw-bold text-black"><?= ($geral + $assDigital) ?></h4>
          </div>
        </div>
      </div>
      <!-- Total de Clientes -->
      <div class="col-md-3 col-lg-2">
        <div class="card shadow-sm border-0 rounded-3 p-3 bg-light">
          <div class="text-center">
            <h6 class="text-uppercase text-muted mb-1 fw-bold">Total Clientes</h6>
            <h4 class="fw-bold text-black"><?= $geral ?></h4>
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
      <br>
      <!-- Conta Diario -->
      <!-- Total de Clientes -->
      <div class="col-md-3 col-lg-2">
        <div class="card shadow-sm border-0 rounded-3 p-3 bg-light">
          <div class="text-center">
            <h6 class="text-uppercase text-muted mb-1 fw-bold">Total Clientes</h6>
            <h4 class="fw-bold text-black"><?= $TotalContaDiario ?></h4>
          </div>
        </div>
      </div>

      <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 p-3 bg-light">
          <div class="row">
            <div class="col text-center border-end">
              <h6 class="text-muted mb-1 fw-bold">Conta Diário</h6>
              <h5 class="fw-bold text-primary"><?= $assDigital ?></h5>
            </div>
            <div class="col text-center">
              <h6 class="text-muted mb-1 fw-bold">Holos</h6>
              <h5 class="fw-bold text-success"><?= $assHolos ?></h5>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 p-3 bg-light">
          <div class="row">
            <div class="col text-center border-end">
              <h6 class="text-muted mb-1 fw-bold">Plano Anual</h6>
              <h5 class="fw-bold text-primary"><?= $assAnual ?></h5>
            </div>
            <div class="col text-center">
              <h6 class="text-muted mb-1 fw-bold">Plano Mensal</h6>
              <h5 class="fw-bold text-success"><?= $assMensal ?></h5>
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
<?php endif; ?>

<!-- Exibindo Resultado Analítico -->
<?php if (!empty($Total)) : ?>
  <div class="container mt-4">
    <div class="accordion" id="accordionContaDiario">
      <div class="accordion-item border rounded shadow-sm mb-3">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button collapsed bg-primary text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            <i class="bi bi-card-list me-2"></i>
            Relação Clientes Conta Diário ||
            Qtde. Total: <?= $TotalCliContaD ?> ||
            Total Clientes: <?= $TotalClientesCD ?>
          </button>
          </h5>
          <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accodionAssinantes">
            <div class="accordion-body">
              <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
                <thead>
                  <tr class="table-primary">
                    <th scope="col">Cliente</th>
                    <th scope="col">CpfCnpj</th>
                    <th scope="col">E-Mail</th>
                    <th scope="col">Tipo Assinatura</th>
                    <th scope="col">Dt. Assinatura</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Situação</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($consultaContaDiario as $key => $item): ?>
                    <tr>
                      <td><?= htmlspecialchars($item['Cliente']) ?></td>
                      <td></td>
                      <td><?= htmlspecialchars($item['email']) ?></td>
                      <td><?= htmlspecialchars($item['name']) ?></td>
                      <td><?= date('d/m/Y', strtotime($item['createdAt'])) ?></td>
                      <td><?= $item['type'] ?></td>
                      <td><?= $item['status'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
      </div>

      <div class="accordion-item border rounded shadow-sm mb-3">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button collapsed bg-primary text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseTwo">
            <i class="bi bi-card-list me-2"></i>
            Relação Clientes Holos ||
            Qtde. Total: <?= $TotalCliHolos ?> ||
            Total Clientes: <?= $TotalClientes ?>
          </button>
          </h5>
          <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accodionAssinantes">
            <div class="accordion-body">
              <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
                <thead>
                  <tr class="table-primary">
                    <th scope="col">Contrato</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">CpfCnpj</th>
                    <th scope="col">Tipo Assinatura</th>
                    <th scope="col">Plano Pgto.</th>
                    <th scope="col">Dt. Assinatura</th>
                    <th scope="col">Vigencia</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Cidade</th>
                    <th scope="col">Dt. Nascimento</th>
                    <th scope="col">Sexo</th>
                    <th scope="col">Situação</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($consultaDados as $key => $item): ?>
                    <tr>
                      <td><?= $item['numeroDoContrato'] ?></td>
                      <td><?= htmlspecialchars($item['NomeRazaoSocial']) ?></td>
                      <td><?= $item['CpfCnpj'] ?></td>
                      <td><?= $item['TipoAssinatura'] ?></td>
                      <td><?= $item['PlanoPagto'] ?></td>
                      <td><?= date('d/m/Y', strtotime($item['DataAssinatura'])) ?></td>
                      <td><?= date('d/m/Y', strtotime($item['DataInicio'])) . ' - ' . date('d/m/Y', strtotime($item['DataFinal'])) ?></td>
                      <td><?= $item['Tipo'] ?></td>
                      <td><?= $item['Cidade'] ?></td>
                      <td style="text-align: center;">
                        <?= str_pad($item['diaDeNascimento'], 2, '0', STR_PAD_LEFT) . '/' . str_pad($item['mesDeNascimento'], 2, '0', STR_PAD_LEFT) . '/' . $item['anoDeNascimento'] ?>
                      </td>
                      <td style="text-align: center;"><?= $item['sexo'] ?></td>
                      <td style="text-align: center;"><?= $item['SituacaoContrato'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
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