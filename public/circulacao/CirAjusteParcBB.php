<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirAjusteParcBB.php';

$Titulo = 'Ajuste Parcelas - Banco do Brasil';
$URL = URL_PRINCIPAL . 'circulacao/CirAjusteParcBB.php';

// Instanciar a classe
$CirAjusteParcelasBB = new CirAjusteParcelasBB();

if (isset($_POST['btn-buscar'])) {
  $numCon = $_POST['numCon'];

  if ($numCon === '') {
    $numCon = [];
  } else {
    $numCon = [$numCon];
  }

  $consulta = $CirAjusteParcelasBB->Consulta($numCon);
  $dados = count($consulta);
  $status = false;
  if (!empty($consulta)) {
    foreach ($consulta as $item) {
      if (isset($item['Situacao']) && $item['Situacao'] === 'P') {
        $status = true;
        break;
      }
    }
  }
} elseif (isset($_POST['btn-incluir'])) {
  $numParc = $_POST['numParc'];
  $numCon  = $_POST['numCon'];

  $incluir = $CirAjusteParcelasBB->IncluirParcela($numParc, $numCon);

  if (!is_array($numCon)) {
    $numCon = [];
  }
  $consulta = $CirAjusteParcelasBB->Consulta($numCon);
  $dados = count($consulta);
  $status = false;
  if (!empty($consulta)) {
    foreach ($consulta as $item) {
      if (isset($item['Situacao']) && $item['Situacao'] === 'P') {
        $status = true;
        break;
      }
    }
  }
} elseif (isset($_POST['btn-excluir'])) {
  $numParc = $_POST['numParc'];
  $numCon  = $_POST['numCon'];

  $excluir = $CirAjusteParcelasBB->DeletaParcela($numParc, $numCon);

  if (!is_array($numCon)) {
    $numCon = [];
  }
  $consulta = $CirAjusteParcelasBB->Consulta($numCon);
  $dados = count($consulta);
  $status = false;
  if (!empty($consulta)) {
    foreach ($consulta as $item) {
      if (isset($item['Situacao']) && $item['Situacao'] === 'P') {
        $status = true;
        break;
      }
    }
  }
} elseif (isset($_POST['btn-processar'])) {
  $dtSelecionada = $_POST['dtSelecionada'];
  $numCon = [];

  $pegaNumCon = $CirAjusteParcelasBB->Consulta($numCon);
  $numCon = [];
  if (!empty($pegaNumCon)) {
    foreach ($pegaNumCon as $item) {
      if (isset($item['NumContrato'])) {
        $numCon[] = $item['NumContrato'];
      }
    }
  }

  $processa = $CirAjusteParcelasBB->ProcessaParcelas($dtSelecionada);
  $consulta = $CirAjusteParcelasBB->Consulta($numCon);
  $dados = count($consulta);
  $status = false;
  if (!empty($consulta)) {
    foreach ($consulta as $item) {
      if (isset($item['Situacao']) && $item['Situacao'] === 'P') {
        $status = true;
        break;
      }
    }
  }
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Número Parcela</strong>
            </div>
            <div class="col">
              <strong>Número Contrato</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="numParc" name="numParc" maxlength="2" pattern="\d+">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="numCon" name="numCon" maxlength="6" pattern="\d+">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-incluir" name="btn-incluir" type="submit" class="btn btn-primary btn-sm" accesskey="i" title="Alt+I">Inclu<u>i</u>r</button>
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm" accesskey="b" title="Alt+b"><u>B</u>uscar</button>
            <button id="btn-excluir" name="btn-excluir" type="submit" class="btn btn-success btn-sm" accesskey="x" title="Alt+x">E<u>x</u>cluir</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>" accesskey="v" title="Alt+V"><u>V</u>oltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<?php if (isset($dados) && ($dados > 0)) : ?>
  <?php if ($status === true) : ?>
    <div class="container d-flex justify-content-center">
      <div class="col col-sm-4">
        <div class="card shadow-sm">
          <form method="post">
            <div class="card-header bg-primary text-white">
              <div class="row">
                <div class="col"><strong>Nova Data</strong></div>
              </div>
            </div>
            <div class="card-body">
              <div class="row justify-content-center">
                <div class="col">
                  <input type="date" id="dtSelecionada" name="dtSelecionada" class="form-control form-control-sm">
                </div>
              </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
              <button id="btn-processar" name="btn-processar" type="submit" class="btn btn-primary btn-sm">Processar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <!-- Adicionando espaçamento -->
  <div class="mb-2"></div>

  <div class="container d-flex justify-content-center">
    <div class=" col col-sm-8">
      <div class="card shadow-sm">
        <h5 class="card-header bg-primary text-white">Qtde. Contratos: <?= $dados ?></h5>
        <div class="card-body">
          <table class="table table-striped table-hover" id="Resultado" name="Resultado">
            <thead>
              <tr class="table-primary">
                <th>Número da Parcela</th>
                <th>Número do Contrato</th>
                <th>Situação</th>
                <th>Data Processamento</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($consulta as $key => $item): ?>
                <tr>
                  <td><?= $item['NumParcela'] ?></td>
                  <td><?= $item['NumContrato'] ?></td>
                  <td><?= $item['Situacao'] ?></td>
                  <td><?= date('d/m/Y', strtotime($item['Dt_Sit'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Adicionando espaçamento -->
<div class="mb-3"></div>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/cir_ajusteparcbb.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>