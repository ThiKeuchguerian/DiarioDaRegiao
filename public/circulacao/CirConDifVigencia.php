<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirConDifVigencia.php';

$Titulo = 'Contrato Cbo - Vigencia Diferente';
$URL = URL_PRINCIPAL . 'circulacao/CirConDifVigencia.php';

// Instanciar a classe
$contratoCbo = new CirContratoCboVigDif();

if (isset($_POST['btn-buscar'])) {
  $numContrato = $_POST['numContrato'];
  $mesAno = $_POST['MesAno'];
  $codAssinante = '';

  if ($numContrato != '') {
    $consultaAssinante = $contratoCbo->consultaAssinante($numContrato);
    $codAssinante = !empty($consultaAssinante) ? $consultaAssinante[0]['codigoDaPessoa'] : '';
  }

  $consultaContrato = $contratoCbo->consultaContrato($codAssinante, $mesAno);

  $Total = COUNT($consultaContrato);

  $dadosAgrupados = [];
  $contratosProcessados = [];
  if (!empty($consultaContrato)) {
    foreach ($consultaContrato as $item) {
      $codAssinante = $item['codigoDaPessoa'] . '-' . $item['Chave'];
      $dadosAgrupados[$codAssinante][] = $item;
    }
    foreach ($dadosAgrupados as $codAssinante => $conAssinante) {
      $validadeIgual = false;
      if (count($conAssinante) == 2) {
        $contrato1 = $conAssinante[0];
        $contrato2 = $conAssinante[1];
        if (
          $contrato1['dataDeValidadeInicial'] === $contrato2['dataDeValidadeInicial'] &&
          $contrato1['dataDevalidadeFinal'] === $contrato2['dataDevalidadeFinal']
        ) {
          $validadeIgual = true;
        }
      }
      // Define o status a ser exibido conforme a comparação
      $status = $validadeIgual ? '<span class="badge bg-success">OK</span>' : '<span class="badge bg-danger">XX</span>';

      $contratosProcessados[] = [
        'codAssinante' => $codAssinante,
        'contrato'     => $conAssinante,
        'status'       => $status
      ];
    }
  }

  foreach ($contratosProcessados as $grupo) {
    $contratosCliente = $grupo['contrato'];
    $TotalContratos = count(array_unique(array_column($contratosProcessados, 'numeroDoContrato')));
    $TotalClientes = count(array_unique(array_column($contratosProcessados, 'codAssinante')));
    // Conta quantos grupos têm validade igual ou diferente
    static $countValidadeIgual = 0;
    static $countValidadeDiferente = 0;
    if ($grupo['status'] === '<span class="badge bg-success">OK</span>') {
      $countValidadeIgual++;
    } else {
      $countValidadeDiferente++;
    }
  }
} elseif (isset($_POST['btn-corrigir'])) {
  $dados = $_POST;
  $mesAno = '';
  $codAssinante = '';

  $updateContrato = $contratoCbo->updateContrato($dados);

  if ($updateContrato >= 0) {
    $consultaContrato = $contratoCbo->consultaContrato($codAssinante, $mesAno);
    $Total = COUNT($consultaContrato);

    $dadosAgrupados = [];
    $contratosProcessados = [];
    if (!empty($consultaContrato)) {
      foreach ($consultaContrato as $item) {
        $codAssinante = $item['codigoDaPessoa'] . '-' . $item['Chave'];
        $dadosAgrupados[$codAssinante][] = $item;
      }
      foreach ($dadosAgrupados as $codAssinante => $conAssinante) {
        $validadeIgual = false;
        if (count($conAssinante) == 2) {
          $contrato1 = $conAssinante[0];
          $contrato2 = $conAssinante[1];
          if (
            $contrato1['dataDeValidadeInicial'] === $contrato2['dataDeValidadeInicial'] &&
            $contrato1['dataDevalidadeFinal'] === $contrato2['dataDevalidadeFinal']
          ) {
            $validadeIgual = true;
          }
        }
        // Define o status a ser exibido conforme a comparação
        $status = $validadeIgual ? '<span class="badge bg-success">OK</span>' : '<span class="badge bg-danger">XX</span>';
        $contratosProcessados[] = [
          'codAssinante' => $codAssinante,
          'contrato'     => $conAssinante,
          'status'       => $status
        ];
      }
    }
  }

  foreach ($contratosProcessados as $grupo) {
    $contratosCliente = $grupo['contrato'];
    $TotalContratos = count(array_unique(array_column($contratosProcessados, 'numeroDoContrato')));
    $TotalClientes = count(array_unique(array_column($contratosProcessados, 'codAssinante')));
    // Conta quantos grupos têm validade igual ou diferente
    static $countValidadeIgual = 0;
    static $countValidadeDiferente = 0;
    if ($grupo['status'] === '<span class="badge bg-success">OK</span>') {
      $countValidadeIgual++;
    } else {
      $countValidadeDiferente++;
    }
  }
} elseif (isset($_POST['btn-analitico'])) {
  $numContrato = $_POST['numContrato'];
  $mesAno = $_POST['MesAno'];
  $codAssinante = '';

  if ($numContrato != '') {
    $consultaAssinante = $contratoCbo->consultaAssinante($numContrato);
    $codAssinante = !empty($consultaAssinante) ? $consultaAssinante[0]['codigoDaPessoa'] : '';
  }

  $consultaAnalitico = $contratoCbo->consultaAnalitico($codAssinante, $mesAno);

  $Analitico = COUNT($consultaAnalitico);
  $dadosAgrupados = [];
  $contratosProcessados = [];
  if (!empty($consultaAnalitico)) {
    foreach ($consultaAnalitico as $item) {
      $chave = $item['codigoDaPessoa'] . '-' . $item['Chave'];
      $dadosAgrupados[$chave][] = $item;
    }
    foreach ($dadosAgrupados as $chave => $conAssinante) {
      $validadeIgual = false;
      if (count($conAssinante) == 2) {
        $contrato1 = $conAssinante[0];
        $contrato2 = $conAssinante[1];
        if (
          $contrato1['dataDeValidadeInicial'] === $contrato2['dataDeValidadeInicial'] &&
          $contrato1['dataDevalidadeFinal'] === $contrato2['dataDevalidadeFinal']
        ) {
          $validadeIgual = true;
        }
      }
      // Define o status a ser exibido conforme a comparação
      $status = $validadeIgual ? '<span class="badge bg-success">OK</span>' : '<span class="badge bg-danger">XX</span>';

      $contratosProcessados[] = [
        'codAssinante' => $codAssinante,
        'contrato'     => $conAssinante,
        'status'       => $status
      ];
    }
  }
} elseif (isset($_POST['btn-corrigianalitico'])) {
  $dados = $_POST;
  $mesAno = '';
  $codAssinante = $dados['codigoDaPessoa'];

  $updateContrato = $contratoCbo->updateContrato($dados);

  if ($updateContrato >= 0) {
    $consultaAnalitico = $contratoCbo->consultaAnalitico($codAssinante, $mesAno);
    $Analitico = COUNT($consultaAnalitico);

    $dadosAgrupados = [];
    $contratosProcessados = [];
    if (!empty($consultaAnalitico)) {
      foreach ($consultaAnalitico as $item) {
        $chave = $item['Chave'];
        $dadosAgrupados[$chave][] = $item;
      }
      foreach ($dadosAgrupados as $chave => $conAssinante) {
        $validadeIgual = false;
        if (count($conAssinante) == 2) {
          $contrato1 = $conAssinante[0];
          $contrato2 = $conAssinante[1];
          if (
            $contrato1['dataDeValidadeInicial'] === $contrato2['dataDeValidadeInicial'] &&
            $contrato1['dataDevalidadeFinal'] === $contrato2['dataDevalidadeFinal']
          ) {
            $validadeIgual = true;
          }
        }
        // Define o status a ser exibido conforme a comparação
        $status = $validadeIgual ? '<span class="badge bg-success">OK</span>' : '<span class="badge bg-danger">XX</span>';

        $contratosProcessados[] = [
          'codAssinante' => $codAssinante,
          'contrato'     => $conAssinante,
          'status'       => $status
        ];
      }
    }
  }
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Nº.: Contrato</strong>
            </div>
            <div class="col">
              <strong>Mês / Ano</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="numContrato" name="numContrato">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
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

<!-- Exibindo Resultado -->
<?php if (!empty($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">
        Qtde. Clientes <?= $TotalClientes ?> ||
        Qtde. Contrato OK: <?= $countValidadeIgual ?> ||
        Qtde. Contrato XX: <?= $countValidadeDiferente ?>
      </h5>
      <div class="card-body">
        <?php foreach ($contratosProcessados as $grupo) : ?>
          <?php $contratosCliente = $grupo['contrato']; ?>
          <?php $status = $grupo['status']; ?>
          <?php if ($status === '<span class="badge bg-danger">XX</span>'): ?>
            <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
              <thead>
                <tr class="table-primary">
                  <th scope="col">Nº. Contrato</th>
                  <th scope="col">Prod.</th>
                  <th scope="col">Cod. Cliente</th>
                  <th scope="col">Nome Cliente</th>
                  <th scope="col">Dt. Assinatura</th>
                  <th scope="col">Dt. Inicio</th>
                  <th scope="col">Dt. Fim</th>
                  <th scope="col">Plano</th>
                  <th scope="col" style="text-align: center;">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($contratosCliente as $item) : ?>
                  <tr>
                    <td><?= trim($item['numeroDoContrato']) ?></td>
                    <td><?= trim($item['codigoDoProdutoServico']) ?></td>
                    <td><?= trim($item['codigoDaPessoa']) ?></td>
                    <td><?= trim($item['nomeRazaoSocial']) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['dataDaAssinatura'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['dataDeValidadeInicial'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['dataDevalidadeFinal'])) ?></td>
                    <td><?= trim($item['descricaoDoPlanoDePagamento']) ?></td>
                    <td style="text-align: center;"><?= $status ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tbody>
                <?php if ($status === '<span class="badge bg-danger">XX</span>') : ?>
                  <tr>
                    <td colspan="9" style="text-align: right;">
                      <!-- Formulário para enviar via POST os dados para corrigir a data -->
                      <form action="<?= $URL ?>" method="post" id="form" name="form">
                        <input type="hidden" name="MesAno" value="<?= isset($MesAno) ? $MesAno : '' ?>">
                        <!-- Envia os números dos contratos como array -->
                        <?php foreach ($contratosCliente as $contrato): ?>
                          <input type="hidden" name="contrato[]" value="<?= $contrato['numeroDoContrato'] ?>">
                          <input type="hidden" name="produto[]" value="<?= $contrato['codigoDoProdutoServico'] ?>">
                          <input type="hidden" name="dtInicio[]" value="<?= $contrato['dataDeValidadeInicial'] ?>">
                          <input type="hidden" name="dtFinal[]" value="<?= $contrato['dataDevalidadeFinal'] ?>">
                        <?php endforeach; ?>
                        <input type="hidden" id="codigoDaPessoa" name="codigoDaPessoa" value="<?= $contratosCliente[0]['codigoDaPessoa'] ?>">
                        <!-- Botão para submeter o formulário -->
                        <button type="submit" class="btn btn-success btn-sm" id="btn-corrigir" name="btn-corrigir">Corrigir Data</button>
                      </form>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          <?php endif; ?>
          <div class="mb-3"></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Exibindo Resultado -->
<?php if (!empty($Analitico)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">
      </h5>
      <div class="card-body">
        <?php foreach ($contratosProcessados as $grupo) : ?>
          <?php $contratosCliente = $grupo['contrato']; ?>
          <?php $status = $grupo['status']; ?>
          <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
            <thead>
              <tr class="table-primary">
                <th scope="col">Nº. Contrato</th>
                <th class="col">Status</th>
                <th scope="col">Prod.</th>
                <th scope="col">Cod. Cliente</th>
                <th scope="col">Nome Cliente</th>
                <th scope="col">Dt. Assinatura</th>
                <th scope="col">Dt. Inicio</th>
                <th scope="col">Dt. Fim</th>
                <th scope="col">Plano</th>
                <th scope="col" style="text-align: center;">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($contratosCliente as $item) : ?>
                <tr>
                  <td><?= trim($item['numeroDoContrato']) ?></td>
                  <td><?= trim($item['sitContrato']) ?></td>
                  <td><?= trim($item['codigoDoProdutoServico']) ?></td>
                  <td><?= trim($item['codigoDaPessoa']) ?></td>
                  <td><?= trim($item['nomeRazaoSocial']) ?></td>
                  <td><?= date('d/m/Y', strtotime($item['dataDaAssinatura'])) ?></td>
                  <td><?= date('d/m/Y', strtotime($item['dataDeValidadeInicial'])) ?></td>
                  <td><?= date('d/m/Y', strtotime($item['dataDevalidadeFinal'])) ?></td>
                  <td><?= trim($item['descricaoDoPlanoDePagamento']) ?></td>
                  <td style="text-align: center;"><?= $status ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tbody>
              <?php if ($status === '<span class="badge bg-danger">XX</span>') : ?>
                <tr>
                  <td colspan="10" style="text-align: right;">
                    <!-- Formulário para enviar via POST os dados para corrigir a data -->
                    <form action="<?= $URL ?>" method="post" id="form" name="form">
                      <input type="hidden" name="MesAno" value="<?= isset($MesAno) ? $MesAno : '' ?>">
                      <!-- Envia os números dos contratos como array -->
                      <?php foreach ($contratosCliente as $contrato): ?>
                        <input type="hidden" name="contrato[]" value="<?= $contrato['numeroDoContrato'] ?>">
                        <input type="hidden" name="produto[]" value="<?= $contrato['codigoDoProdutoServico'] ?>">
                        <input type="hidden" name="dtInicio[]" value="<?= $contrato['dataDeValidadeInicial'] ?>">
                        <input type="hidden" name="dtFinal[]" value="<?= $contrato['dataDevalidadeFinal'] ?>">
                      <?php endforeach; ?>
                      <input type="hidden" id="codigoDaPessoa" name="codigoDaPessoa" value="<?= $contratosCliente[0]['codigoDaPessoa'] ?>">
                      <!-- Botão para submeter o formulário -->
                      <button type="submit" class="btn btn-success btn-sm" id="btn-corrigianalitico" name="btn-corrigianalitico">Corrigir Data</button>
                    </form>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
          <div class="mb-3"></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/cir_condifvigencia.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>