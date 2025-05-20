<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirAnaliseCancEnc.php';

$Titulo = 'Analice de Cancelados/Encerrados';
$URL = URL_PRINCIPAL . 'circulacao/CirAnaliseCancEnc.php';

// Instanciar a classe
$CirAnaliseCancEnc = new CirAnaliseCancEnc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $MesAno = $_POST['MesAno'];
  $codProduto = $_POST['CodProduto'];

  // Separa mês e ano – use explode em vez de str_split para evitar problemas
  list($Mes, $Ano) = explode('/', $_POST['MesAno']);
  $Mes = (int)$Mes;
  $Ano = (int)$Ano;

  // 1) Mês igual do ano passado
  $MesCan = [
    sprintf('%02d/%04d', $Mes, $Ano - 1)
  ];

  // 2) Três meses anteriores + mês atual
  for ($offset = -3; $offset <= 0; $offset++) {
    $novoMes = $Mes + $offset;
    $novoAno = $Ano;
    if ($novoMes < 1) {
      $novoMes += 12;
      $novoAno--;
    }
    $MesCan[] = sprintf('%02d/%04d', $novoMes, $novoAno);
  }

  $Meses     = $MesCan;
  $ListMeses = "'" . implode("', '", $Meses) . "'";
  // echo "<pre>";
  // var_dump($Meses);
  // var_dump($codProduto);
  // die();
  $ConsultaCancEnc = $CirAnaliseCancEnc->ConsultaCancEnc($Meses, $codProduto);

  $resultado = $ConsultaCancEnc;

  // echo "<pre>";
  // var_dump($resultado);
  // die();
} elseif (isset($_POST['btn-analitico'])) {
  $MesAno = $_POST['MesAno'];
  $codProduto = $_POST['CodProduto'];
  // echo "<pre>";
  // var_dump($MesAno);
  // var_dump($codProduto);
  // die();
  $ConsultaAnalitica = $CirAnaliseCancEnc->ConsultaAnalitica($MesAno, $codProduto);

  $resultadoAnalitico = $ConsultaAnalitica;
  $Qtde = COUNT($resultadoAnalitico);
  // echo "<pre>";
  // var_dump($resultadoAnalitico);
  // die();
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="AnaliseCancEnc" name="AnaliseCancEnc">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Mes Cancelamento</strong>
            </div>
            <div class="col">
              <strong>Produto</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="CodProduto">
                <option value="">--Selecione Produto --</option>
                <option value="0"> Todos </option>
                <option value="1"> Diário da Região </option>
                <option value="3"> Diário da Região Digital </option>
                <option value="13"> Diário da Região + Digital </option>
                <option value="11"> Jornal Viva+ </option>
              </select>
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

<!-- Espacamento -->
<div class="mb-3"></div>

<!-- Resultado -->
<?php if (isset($resultado)) : ?>
  <?php
  // Agrupa e soma quantidades por motivo e por mês
  $pivot = [];
  foreach ($resultado as $row) {
    $motivo = $row['MotivoCancelamento'];
    $mes    = $row['MesCanc'];
    $qtde   = (int)$row['Quantidade'];
    if (!isset($pivot[$motivo])) {
      $pivot[$motivo] = array_fill_keys($Meses, 0);
    }
    $pivot[$motivo][$mes] += $qtde;
  }
  ?>
  <div class="container d-flex justify-content-center"">
    <div class=" col col-sm-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <table class="table table-striped table-hover" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Desc. Motivo Cancelamento</th>
              <?php foreach ($Meses as $m): ?>
                <th scope="col" style="text-align: center;"><?= $m ?></th>
              <?php endforeach; ?>
              <th scope="col" style="text-align: center;">Total Geral</th>
            </tr>
          </thead>
          <tbody>
          <tbody>
            <?php foreach ($pivot as $motivo => $dadosMes): ?>
              <?php $totalLinha = array_sum($dadosMes); ?>
              <tr>
                <td><?= $motivo ?></td>
                <?php foreach ($Meses as $m): ?>
                  <td class="text-center"><?= $dadosMes[$m] ?></td>
                <?php endforeach; ?>
                <th class="text-center"><?= $totalLinha ?></th>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody>
            <tr class="table-primary">
              <th scope="row">Total</th>
              <?php
              $totaisPorMes = array_fill_keys($Meses, 0);
              foreach ($pivot as $dadosMes) {
                foreach ($Meses as $m) {
                  $totaisPorMes[$m] += $dadosMes[$m];
                }
              }
              $totalGeral = array_sum($totaisPorMes);
              ?>
              <?php foreach ($Meses as $m): ?>
                <th class="text-center"><?= $totaisPorMes[$m] ?></th>
              <?php endforeach; ?>
              <th class="text-center"><?= $totalGeral ?></th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Resultado -->
<?php if (isset($resultadoAnalitico)) : ?>
  <div class="container d-flex justify-content-center">
    <div class=" col col-sm-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-header bg-primary text-white">Qtde. Contrato: <?= $Qtde ?></h5>
          <table class="table table-striped table-hover" id="Resultado" name="Resultado">
            <thead>
              <tr class="table-primary">
                <th scope="col">Contrato</th>
                <th scope="col">Dt. Assinatura</th>
                <th scope="col">Nome Cliente</th>
                <th scope="col">Tipo Cobrança</th>
                <th scppe="col">Desc. Motivo Cancelamento</th>
                <th scope="col">Tipo Assinatura</th>
                <th scope="col">Mês Canc.</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($resultadoAnalitico as $key => $item) : ?>
                <tr>
                  <td><?= $item['numeroDoContrato'] ?></td>
                  <td><?= date('d/m/Y', strtotime($item['dataDaAssinatura'])) ?></td>
                  <td><?= $item['nomeRazaoSocial'] ?></td>
                  <td><?= $item['descricaoTipoCobranca'] ?></td>
                  <td><?= $item['MotivoCancelamento'] ?></td>
                  <td><?= $item['descricaoTipoDeAssinatura'] ?></td>
                  <td><?= $item['MesCanc'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o resultado e o footer -->
<div class="mb-3"></div>
<!-- JavaScript -->
<script src="../js/maskcampos.js"></script>
<script src="../js/ciranalisecancenc.js"></script>
<!-- Footer -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>