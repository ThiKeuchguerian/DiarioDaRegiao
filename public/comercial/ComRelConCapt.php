<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ComRelConCapt.php';

$Titulo = 'Relatório Contratos Capt';
$URL = URL_PRINCIPAL . 'comercial/ComRelConCapt.php';

// Instanciar a classe
$ComercialRelContratoCapt = new ComRelatorioContratoCapt();

// Chamando a função ConsultaGrupo
$Grupo = $ComercialRelContratoCapt->ConsultaGrupo();

// Verifica se a requisição é AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getGrupo') {
  $NomeGrupo = $_GET['nomeGrupo'];

  $Grupo = $ComercialRelContratoCapt->ConsultaProduto($NomeGrupo);
  echo json_encode($Grupo);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $DtInicial = str_replace('-', '', $_POST['DtInicial']);
  $DtFinal = str_replace('-', '', $_POST['DtFinal']);
  $Grupo = $_POST['Grupo'];
  $Produto = $_POST['Produto'];
  $Tipo = $_POST['Tipo'];
  $Cliente = $_POST['Cliente'];

  $ConsultaContrato = $ComercialRelContratoCapt->ConsultaContratoCapt($DtInicial, $DtFinal, $Grupo, $Produto, $Tipo, $Cliente);
  $TotalContrato = COUNT($ConsultaContrato);
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-10">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Data Inicial</strong>
            </div>
            <div class="col">
              <strong>Data Final</strong>
            </div>
            <div class="col">
              <strong>Cliente</strong>
            </div>
            <div class="col">
              <strong>Grupo</strong>
            </div>
            <div class="col">
              <strong>Produto</strong>
            </div>
            <div class="col">
              <strong>Tipo</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="DtInicial" name="DtInicial">
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="DtFinal" name="DtFinal">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="Cliente" name="Cliente" maxlength="20">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="Grupo" id="Grupo" onchange="getGrupoProduto(this.value)">
                <option value="">--Selecione--</option>
                <?php foreach ($Grupo as $grupo): ?>
                  <option value="<?= $grupo['nomeGrupo'] ?>"><?= $grupo['nomeGrupo'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="Produto" id="Produto">
                <option value="">--Selecione--</option>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="Tipo" id="Tipo">
                <option value="">--Selecione Tipo-- </opetion>
                <option value="1">01-Normal</opetion>
                <option value="2">02-Bonificação</opetion>
                <option value="3">03-Calhau</opetion>
                <option value="4">04-Compensação</opetion>
                <option value="5">05-Anunc. da Casa</opetion>
                <option value="8">08-Permuta</opetion>
                <option value="9">09-Cortesia</opetion>
                <option value="10">10-Só Fatura</opetion>
                <option value="11">11-Só Veicula</opetion>
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button>
            <button type="submit" id="btn-imprimir" name="Imprimir" class="btn btn-primary btn-sm">Imprimir</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado -->
<?php if (isset($ConsultaContrato)) : ?>
  <?php
  $groupedData = [];
  foreach ($ConsultaContrato as $item) {
    $groupedData[$item['MesAno']][] = $item;
  }
  ?>
  <?php foreach ($groupedData as $mesAno => $items): ?>
    <div class="container d-flex justify-content-center">
      <div class="card shadow-sm">
        <h5 class="card-header bg-primary text-white">
          Período: <?= date('d/m/Y', strtotime($DtInicial)) ?> - <?= date('d/m/Y', strtotime($DtFinal)) ?>
        </h5>
        <div class="card-body">
          <table class="table table-striped table-hover" id="Resultado" name="Resultado">
            <thead>
              <tr class="table-primary">
                <th scope="Col">N.º AP</th>
                <th scope="col">Dt Veic.</th>
                <th scope="col">Produto</th>
                <th scope="col">Título Anúncio</th>
                <th scope="col">Tipo</th>
                <th scope="col">Nome Cliente</th>
                <th scope="col">Vendedor</th>
                <th scope="col">Valor</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td><?= $item['nroContrato'] ?></td>
                  <td><?= $item['DtVeiculacao'] ?></td>
                  <td><?= mb_strimwidth($item['Produto'], 0, 40, '...') ?></td>
                  <td><?= mb_strimwidth($item['tituloAnuncio'], 0, 40, '...') ?></td>
                  <td><?= $item['tipoContrato'] ?></td>
                  <td><?= $item['nomeFantasia'] ?></td>
                  <td><?= $item['nomeReduzido'] ?></td>
                  <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($item['ValorVeiculado'], 2, ',', '.') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tbody>
              <tr class="gray-background">
                <td colspan="2" style="text-align: right;">Qtde. Contratos:</td>
                <td style="text-align: left;"><?= count(array_unique(array_column($items, 'nroContrato'))) ?></td>
                <td style="text-align: right;">Qtde. Veiculações:</td>
                <td style="text-align: right;"><?= COUNT($items) ?></td>
                <td style="text-align: right;">Valor Total:</td>
                <td colspan="2" style="text-align: right;">
                  <span style="float: left;">R$</span>
                  <?php
                  $totalValor = array_sum(array_column($items, 'ValorVeiculado'));
                  echo $totalValor = number_format($totalValor, 2, ',', '.');
                  ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="mb-3"></div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Inclui o JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/comrelconcapt.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>