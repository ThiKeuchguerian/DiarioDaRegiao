<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinConEstoque.php';

$Titulo = 'Consulta Estoque - (Qtde/Preço Medio/Vlr. Est)';
$URL = URL_PRINCIPAL . 'financeiro/FinConEstoque.php';

// Instanciar a classe
$ConsultaEstoque = new ConsultaEstoque();

$DadosDeposito = $ConsultaEstoque->listarDepositos();
// Verifica se a requisição é AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getFamilia') {
  header('Content-Type: application/json; charset=utf-8');
  $codDep  = $_GET['CODDEP'];
  $Familia = $ConsultaEstoque->listarFamlia($codDep);
  echo json_encode($Familia);
  exit;
}

if (isset($_POST['btn-buscar'])) {
  $codDep = $_POST['Deposito'];
  $codFam = $_POST['Familia'];
  $mesAno = $_POST['MesAno'];
  $mesCampos = [
    ['QTDEST1', 'PRMEST1', 'VLREST1'],
    ['QTDEST2', 'PRMEST2', 'VLREST2'],
    ['QTDEST3', 'PRMEST3', 'VLREST3'],
  ];

  $geraMesAno = $ConsultaEstoque->geraMesAno($mesAno);

  // depurar($mesAno, $codDep, $codFam);
  $consultaEstoque = $ConsultaEstoque->consultaEstoque($mesAno, $codDep, $codFam);
  $total = COUNT($consultaEstoque);

  $dadosAgrupados = [];
  $TotalItens = 0;
  foreach ($consultaEstoque as $item) {
    $CodFam = $item['CODFAM'];
    if (!isset($dadosAgrupados[$CodFam])) {
      $dadosAgrupados[$CodFam] = [];
    }
    $dadosAgrupados[$CodFam][] = $item;
    $TotalItens++;
  }

  $valoresTotais = [0, 0, 0];
  foreach ($dadosAgrupados as $CodFam => $CodPro) {
    $VlrTotalEst1 = array_sum(array_column($CodPro, 'VLREST1'));
    $VlrTotalEst2 = array_sum(array_column($CodPro, 'VLREST2'));
    $VlrTotalEst3 = array_sum(array_column($CodPro, 'VLREST3'));
  }
  $totalGeralEst1 = array_sum(array_column($consultaEstoque, 'VLREST1'));
  $totalGeralEst2 = array_sum(array_column($consultaEstoque, 'VLREST2'));
  $totalGeralEst3 = array_sum(array_column($consultaEstoque, 'VLREST3'));
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
              <strong>Deposito</strong>
            </div>
            <div class="col">
              <strong>Código Família</strong>
            </div>
            <div class="col">
              <strong>Mes / Ano</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="Deposito" name="Deposito" onchange="getFamiliaDeposito(this.value)">
                <option value="0">Todos</option>
                <?php foreach ($DadosDeposito as $key => $item): ?>
                  <option value="<?= $item['CODDEP'] ?>"><?= $item['CODDEP'] . ' - ' . $item['DESDEP'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="Familia" name="Familia">
                <option value="0">Todos</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-imprimir" name="btn-imprimir" type="submit" class="btn btn-primary btn-sm">Imprimir</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Incluindo Espaçamento -->
<div class="mb-3"></div>

<!-- Exibindo Resultado -->
<?php if (!empty($total)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 style="text-align: center;" class="card-header bg-primary text-white">
          Soma Geral Estoque
        </h5>
        <table class="table table-striped table-hover mb-0" style="border: 1px solid #ccc; border-collapse: collapse;">
          <thead>
            <tr class="table-primary">
              <th scope="col" style="text-align: center;">Qtde. Total Itens</th>
              <?php foreach ($geraMesAno as $mesAno): ?>
                <th scope="col" style="text-align: center;"><?= htmlspecialchars($mesAno) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th style="text-align: center;"><?= number_format($TotalItens, 0, ',', '.') ?></th>
              <?php if ($totalGeralEst1 < 0): ?>
                <th style="text-align: center;">
                  <span style="color:red; font-weight: bold;">R$</span>
                  <span style="color:red; font-weight: bold;"><?= number_format($totalGeralEst1, 2, ',', '.') ?></span>
                </th>
              <?php else: ?>
                <th style="text-align: center;">
                  <span style="color:blue; font-weight: bold;">R$</span>
                  <span style="color:blue; font-weight: bold;"><?= number_format($totalGeralEst1, 2, ',', '.') ?></span>
                </th>
              <?php endif; ?>
              <?php if ($totalGeralEst2 < 0): ?>
                <th style="text-align: center;">
                  <span style="color:red; font-weight: bold;">R$</span>
                  <span style="color:red; font-weight: bold;"><?= number_format($totalGeralEst2, 2, ',', '.') ?></span>
                </th>
              <?php else: ?>
                <th style="text-align: center;">
                  <span style="color:blue; font-weight: bold;">R$</span>
                  <span style="color:blue; font-weight: bold;"><?= number_format($totalGeralEst2, 2, ',', '.') ?></span>
                </th>
              <?php endif; ?> 
              <?php if ($totalGeralEst3 < 0): ?>
                <th style="text-align: center;">
                  <span style="color:red; font-weight: bold;">R$</span>
                  <span style="color:red; font-weight: bold;"><?= number_format($totalGeralEst3, 2, ',', '.') ?></span>
                </th>
              <?php else: ?>
                <th style="text-align: center;">
                  <span style="color:blue; font-weight: bold;">R$</span>
                  <span style="color:blue; font-weight: bold;"><?= number_format($totalGeralEst3, 2, ',', '.') ?></span>
                </th>
              <?php endif; ?>
            </tr>
          </tbody>
        </table>
        <div class="mb-3"></div>
        <?php foreach ($dadosAgrupados as $CodFam => $CodPro): ?>
          <table class="table table-striped table-hover mb-0" style="border: 1px solid #ccc; border-collapse: collapse;">
            <thead>
              <tr class="table-primary">
                <th scope="col" colspan="2" style="text-align: center; text-transform: uppercase;"><?= $CodPro[0]['DESFAM'] ?></th>
                <?php foreach ($geraMesAno as $mesAno): ?>
                  <th scope="col" colspan="3" style="text-align: center;"><?= htmlspecialchars($mesAno) ?></th>
                <?php endforeach; ?>
                <th scope="col" rowspan="2" style="text-align: center; vertical-align: middle;">Status</th>
              </tr>
              <tr class="table-primary">
                <th scope="col">Cod. Produto</th>
                <th scope="col">Descrição Produto</th>
                <th scope="col" style="text-align: center;">Qtde. Est.</th>
                <th scope="col" style="text-align: center;">Preço Médio</th>
                <th scope="col" style="text-align: center;">Vlr. Estoque</th>
                <th scope="col" style="text-align: center;">Qtde. Est.</th>
                <th scope="col" style="text-align: center;">Preço Médio</th>
                <th scope="col" style="text-align: center;">Vlr. Estoque</th>
                <th scope="col" style="text-align: center;">Qtde. Est.</th>
                <th scope="col" style="text-align: center;">Preço Médio</th>
                <th scope="col" style="text-align: center;">Vlr. Estoque</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($CodPro as $key => $item): ?>
                <tr>
                  <td style="text-align: center;"><?= $item['CODPRO'] ?></td>
                  <td><?= $item['DESPRO'] ?></td>
                  <?php foreach ($mesCampos as $campos): ?>
                    <td style="text-align: right;"><?= number_format($item[$campos[0]], 2, '.', '') ?></td>
                    <td style="text-align: right;">
                      <span style="float: left;">R$</span>
                      <?= $item[$campos[1]] < 0 ? '<span style="color: red; font-weight: bold;">' . number_format($item[$campos[1]], 2, ',', '.') . '</span>' : number_format($item[$campos[1]], 2, ',', '.') ?>
                    </td>
                    <td style="text-align: right;"><span style="float: left;">R$</span>
                      <?= $item[$campos[2]] < 0 ? '<span style="color: red; font-weight: bold;">' . number_format($item[$campos[2]], 2, ',', '.') . '</span>' : number_format($item[$campos[2]], 2, ',', '.') ?>
                    </td>
                  <?php endforeach; ?>
                  <td style="text-align: center;">
                    <?php if (($item['PRMEST1'] - $item['PRMEST2']) > 5): ?>
                      <span style="color: red; font-weight: bold;">XX</span>
                    <?php else: ?>
                      <span style="color: blue; font-weight: bold;">OK</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tbody>
              <tr>
                <th colspan="2" style="text-align: right;"> Valor Total Estoque:</th>
                <?php if (array_sum(array_column($CodPro, 'VLREST1')) < 0): ?>
                  <th colspan="3" style="text-align: right;">
                    <span style="color:red; font-weight: bold;">R$</span>
                    <span style="color:red; font-weight: bold;"><?= number_format(array_sum(array_column($CodPro, 'VLREST1')), 2, ',', '.') ?></span>
                  </th>
                <?php else: ?>
                  <th colspan="3" style="text-align: right;">
                    <span style="color:blue; font-weight: bold;">R$</span>
                    <span style="color:blue; font-weight: bold;"><?= number_format(array_sum(array_column($CodPro, 'VLREST1')), 2, ',', '.') ?></span>
                  </th>
                <?php endif; ?>
                <?php if (array_sum(array_column($CodPro, 'VLREST2')) < 0): ?>
                  <th colspan="3" style="text-align: right;">
                    <span style="color:red; font-weight: bold;">R$</span>
                    <span style="color:red; font-weight: bold;"><?= number_format(array_sum(array_column($CodPro, 'VLREST2')), 2, ',', '.') ?></span>
                  </th>
                <?php else: ?>
                  <th colspan="3" style="text-align: right;">
                    <span style="color:blue; font-weight: bold;">R$</span>
                    <span style="color:blue; font-weight: bold;"><?= number_format(array_sum(array_column($CodPro, 'VLREST2')), 2, ',', '.') ?></span>
                  </th>
                <?php endif; ?>
                <?php if (array_sum(array_column($CodPro, 'VLREST3')) < 0): ?>
                  <th colspan="3" style="text-align: right;">
                    <span style="color:red; font-weight: bold;">R$</span>
                    <span style="color:red; font-weight: bold;"><?= number_format(array_sum(array_column($CodPro, 'VLREST3')), 2, ',', '.') ?></span>
                  </th>
                <?php else: ?>
                  <th colspan="3" style="text-align: right;">
                    <span style="color:blue; font-weight: bold;">R$</span>
                    <span style="color:blue; font-weight: bold;"><?= number_format(array_sum(array_column($CodPro, 'VLREST3')), 2, ',', '.') ?></span>
                  </th>
                <?php endif; ?>
                <th></th>
              </tr>
            </tbody>
          </table>
          <div class="mb-3"></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>




<!-- Incluindo Java Script -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/fin_consultaestoque.js"></script>
<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>