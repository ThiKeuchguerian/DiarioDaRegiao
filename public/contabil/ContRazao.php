<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ContRazao.php';

$Titulo = 'Razão Contábil';
$URL = URL_PRINCIPAL . 'contabil/ContRazao.php';

// Instanciar a classe
$ContabilRazao = new ContabilRazao();

if (isset($_POST['btn-buscar'])) {
  $codEmp   = $_POST['CodEmp'];
  $ctaRed   = $_POST['CtaRed'];
  $mesAno   = $_POST['MesAno'];
  $dtInicio = $_POST['DtInicio'];
  $dtFim    = $_POST['DtFim'];

  $ConsultaRazao = $ContabilRazao->consultaRazao($codEmp, $ctaRed, $dtInicio, $dtFim, $mesAno);
  $Total = COUNT($ConsultaRazao);
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-8">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Cód. Empresa:</strong>
            </div>
            <div class="col">
              <strong>Conta Reduzida:</strong>
            </div>
            <div class="col">
              <strong>Data Inicio:</strong>
            </div>
            <div class="col">
              <strong>Data Final:</strong>
            </div>
            <div class="col">
              <strong>Mes/Ano:</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="CodEmp" name="CodEmp" required>
                <option value="0">-- Selecione Empresa --</option>
                <option value="1">1 - Diário da Região</option>
                <option value="2">2 - FM Diário</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="CtaRed" name="CtaRed" placeholder="Conta Reduzida">
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="DtInicio" name="DtInicio">
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="DtFim" name="DtFim">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
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
<?php if (!empty($ConsultaRazao)): ?>
  <?php $dadosgrupados = [];
  foreach ($ConsultaRazao as $item) {
    $dadosgrupados[$item['CODHPD']][] = $item;
  }
  ?>
  <?php foreach ($dadosgrupados as $CODHPD => $grupo): ?>
    <div class="container">
      <div class="card shadow-sm ">
        <h5 class="card-header bg-primary text-white"></h5>
        <div class="card-body table-responsive">
          <table id="Resultado" class="table table-striped full-width-table">
            <thead>
              <tr class="table-primary">
                <th scope="col">Cta. Reduzida</th>
                <th scope="col">Origem</th>
                <th scope="col">Tipo</th>
                <th scope="col">Data Lanc.</th>
                <th scope="col">Histórico</th>
                <th scope="col">Nº. Lote</th>
                <th scope="col">Nº. Lançamento</th>
                <th scope="col">Contra Part.</th>
                <th scope="col">Cta. Débito</th>
                <th scope="col">Cta. Crédito</th>
                <th scope="col">Vlr. Débito</th>
                <th scope="col">Vlr. Crédito</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($grupo as $item): ?>
                <tr>
                  <td><?= $item['CTARED'] ?></td>
                  <td><?= $item['ORILCT'] ?></td>
                  <td><?= $item['TIPLCT'] ?></td>
                  <td><?= date('d/m/Y', strtotime($item['DATLCT'])) ?></td>
                  <td><?= $item['CPLLCT'] ?></td>
                  <td><?= number_format($item['NUMLOT'], 0, '', '.') ?></td>
                  <td><?= '000' . substr($item['NUMLCT'], 0, 1) . '/' . $item['NUMLCT'] ?></td>
                  <td>
                    <?php if ($item['CTARED'] === $item['CTACRE']): ?>
                      <?= $item['CTADEB'] ?>
                    <?php elseif ($item['CTARED'] === $item['CTADEB']): ?>
                      <?= $item['CTACRE'] ?>
                    <?php endif; ?>
                  </td>
                  <td style="text-align: right; ">
                    <?php if ($item['CTADEB'] > 0 && ($item['CTARED'] === $item['CTADEB'])): ?>
                      <?= $item['CTADEB'] ?>
                    <?php endif; ?>
                  </td>
                  <td style="text-align: right;">
                    <?php if ($item['CTACRE'] > 0 && ($item['CTARED'] === $item['CTACRE'])): ?>
                      <?= $item['CTACRE'] ?>
                    <?php endif; ?>
                  </td>
                  <td style="text-align: right; ">
                    <?php if ($item['CTADEB'] > 0 && ($item['CTARED'] === $item['CTADEB'])): ?>
                      <span style="float: left; color: red;">R$</span><span style="color: red;"><?= number_format($item['VLRLCT'], 2, ',', '.') ?></span>
                    <?php endif; ?>
                  </td>
                  <td style="text-align: right; color: blue;">
                    <?php if ($item['CTACRE'] > 0 && ($item['CTARED'] === $item['CTACRE'])): ?>
                      <span style="float: left; color: blue;">R$</span><span style="color: blue;"><?= number_format($item['VLRLCT'], 2, ',', '.') ?>
                      <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tbody>
              <tr>
                <?php
                $totalDebito = 0;
                $saldo = 0;
                foreach ($grupo as $item) {
                  if ($item['CTADEB'] > 0) {
                    $totalDebito += $item['VLRLCT'];
                  }
                }
                $totalCredito = 0;
                foreach ($grupo as $item) {
                  if ($item['CTACRE'] > 0) {
                    $totalCredito += $item['VLRLCT'];
                  }
                }
                $saldo = $totalCredito - $totalDebito;
                ?>
                <th colspan="9" style="text-align: right;"><strong>Saldo:</strong></th>
                <th style="text-align: right; white-space: nowrap;">
                  <span style="text-align: left;">R$</span><?= number_format($saldo, 2, ',', '.') ?>
                </th>
                <th style="text-align: right; color: red; white-space: nowrap;">
                  <span style="float: left; color: red;">R$</span><?= number_format($totalDebito, 2, ',', '.') ?>
                </th>
                <th style="text-align: right; color: blue; white-space: nowrap;">
                  <span style="float: left; color: blue;">R$</span><?= number_format($totalCredito, 2, ',', '.') ?>
                </th>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Espaço entre o menu e o resultado -->
    <div class="mb-2"></div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Inclui o JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';