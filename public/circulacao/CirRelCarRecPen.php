<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirRelCarRecPen.php';

$Titulo = 'Relação Cartão Recorrente - Pendentes';
$URL = URL_PRINCIPAL . 'circulacao/CirRelCarRecPen.php';

// Instanciar a classe
$CirRelCarRecPen = new CirCartaoRecorrentePendente();

if (isset($_POST['btn-buscar'])) {
  $dtInicio = $_POST['dtInicio'];
  $dtFim = $_POST['dtFim'];

  $consultaCarRecPen = $CirRelCarRecPen->consultaCartaoRecorrente($dtInicio, $dtFim);
  $Total = is_array($consultaCarRecPen) ? count($consultaCarRecPen) : 0;
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
              <strong>Data Incial</strong>
            </div>
            <div class="col">
              <strong>Data Final</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtInicio" name="dtInicio">
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtFim" name="dtFim">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button>
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
<?php if (isset($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <h5 class="card-header bg-primary text-white mb-0">
        Qtde. Total: <?= $Total ?><br>
        Período: <?= date('d/m/Y', strtotime($dtInicio)) ?> - <?= date('d/m/Y', strtotime($dtFim)) ?>
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th>N.º Contrato</th>
              <th>Nome Cliente</th>
              <th>N.º Cartão</th>
              <th>Validade Cartão</th>
              <th>Cod. Seg.</th>
              <th>Dt. Vencimento</th>
              <th>Vlr. Parcela</th>
              <th>Descrição Produto</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaCarRecPen as $item): ?>
              <?php if (is_array($item)): ?>
                <tr>
                  <td style="text-align: center;"><?= $item['Contrato'] ?></td>
                  <td><?= $item['NomeCompleto'] ?></td>
                  <td><?= $item['NumCartao'] ?></td>
                  <td style="text-align: center;"><?= date('m/Y', strtotime($item['ValCartao'])) ?></td>
                  <td style="text-align: center;"><?= $item['CodSeg'] ?></td>
                  <td style="text-align: center;"><?= date('d/m/Y', strtotime($item['DtVencParc'])) ?></td>
                  <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['VlrParc'], 2, ',', '.') ?>
                  </td>
                  <td><?= $item['Produto'] ?></td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>
<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/cir_relcarrecpen.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>