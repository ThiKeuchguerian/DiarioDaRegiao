<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinVerCentroCustOp.php';

$Titulo = 'Verifica Centro de Custo / Ordem de Produção';
$URL = URL_PRINCIPAL . 'financeiro/FinVerCentroCustOp.php';

// Instanciar a classe
$CentroCustoOrdermProducao = new CentroCustoOrdermProducao();

if (isset($_POST['btn-buscar'])) {
  $mesAno = $_POST['MesAno'];

  $consultaMovimentoEstoque = $CentroCustoOrdermProducao->movimentoEstoque($mesAno);
  // Pega todos os numdoc dos resultados
  $numDoc = array_unique(array_column($consultaMovimentoEstoque, 'NumDoc'));
  $consultaOrdemProducao = $CentroCustoOrdermProducao->consultaOrdemProducao($numDoc);
  // echo "<pre>";
  // var_dump($consultaMovimentoEstoque[0]);
  // var_dump($consultaOrdemProducao[0]);
  // die();

  $dados = COUNT(array_unique(array_column($consultaOrdemProducao, 'numorp')));

  $agrupandoOp = [];
  foreach ($consultaOrdemProducao as $item) {
    $numorp = $item['numorp'];
    if (!isset($agrupandoOp[$numorp])) {
      $agrupandoOp[$numorp] = [];
    }
    $agrupandoOp[$numorp][] = $item;
  }

  $agrupadoMvto = [];
  foreach ($consultaMovimentoEstoque as $item) {
    $numdoc = $item['NumDoc'];
    if (!isset($agrupadoMvto[$numdoc])) {
      $agrupadoMvto[$numdoc] = [];
    }
    $agrupadoMvto[$numdoc][] = $item;
  }
  // echo "<pre>";
  // var_dump($agrupandoOp);
  // die();
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
              <strong>Mes / Ano</strong>
            </div>
            <div class="col">
              <strong>Nº.: O.P.</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="NumOP" name="NumOP" placeholder="Nº.: O.P.">
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

<!-- Exibindo Resultados -->
<?php if (isset($dados)) : ?>
  <div class="container">
    <div class="card shadow-sm ">
      <h5 class="card-header bg-primary text-white">
        Em <?= $mesAno ?> || Qtde. O.P.: <?= $dados ?>
      </h5>
      <?php foreach ($agrupandoOp as $numorp => $opItens): ?>
        <?php
        // pega as movimentações daquele mesmo número, ou array vazio
        $mvItens = $agrupadoMvto[$numorp] ?? [];
        ?>
        <div class="card-body">
          <h5 class="card-header bg-primary text-white">
            Ordem de Produção: <?= $numorp ?> ||
            Produto: <?= $opItens[0]['codpro'] ?> - <?= $opItens[0]['Produto'] ?> ||
            Qtde. Prod.: <?= number_format($opItens[0]['QtdeProd'], 3, ',', '.') ?> ||
            C.Custo: <?= $opItens[0]['codccu'] ?>
          </h5>
          <div class="row">
            <div class="col-md-6">
              <h6 class="card-header bg-primary text-white">
                Componentes da O.P.
              </h6>
              <table class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
                <thead>
                  <tr class="table-primary">
                    <th scope="col">Cod. Comp.</th>
                    <th scope="col">Descição Componente</th>
                    <th scope="col">Tempo Prod.</th>
                    <th scope="col">Qtde. Comp.</th>
                    <th scope="col">Qtde. Utilizada.</th>
                    <th scope="col">C.Custo OP</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($opItens as $item): ?>
                    <tr>
                      <td><?= $item['codcmp'] ?></td>
                      <td><?= $item['despro'] ?></td>
                      <td style="text-align: right;"><?= number_format($item['tmpprv'], 3, '.', '') ?></td>
                      <td style="text-align: right;"><?= number_format($item['qtdprv'], 3, ',', '.') ?></td>
                      <td style="text-align: right;"><?= number_format($item['qtduti'], 3, ',', '.') ?></td>
                      <td><?= $item['codccu'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <div class="col-md-6">
              <h6 class="card-header bg-primary text-white">
                Movimentação de Estoque
              </h6>
              <table class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
                <thead>
                  <tr class="table-primary">
                    <th scope="col">Cod. Dep.</th>
                    <th scope="col">Cod. Prod.</th>
                    <th scope="col">Descição</th>
                    <!-- <th scope="col">Cod. Tns.</th> -->
                    <th scope="col">Tipo - U.M.</th>
                    <th scope="col">Qtde. Mov.</th>
                    <th scope="col">Vlr. Mov.</th>
                    <th scope="col">C. Custo</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($mvItens as $mv): ?>
                    <?php if ($mv['Tipo'] === 'E') continue; ?>
                    <tr>
                      <td><?= $mv['Deposito'] ?></td>
                      <td><?= $mv['codpro'] ?></td>
                      <td><?= $mv['DescrFis'] ?></td>
                      <!-- <td><?= $mv['Transacao'] ?></td> -->
                      <td><?= $mv['Tipo'] ?> -> <?= $mv['UM'] ?></td>
                      <td style="text-align: right;"><?= number_format($mv['QtdeMovi'], 3, '.', '') ?></td>
                      <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($mv['VlrMov'], 2, ',', '.') ?></td>
                      <td><?= $mv['codccu'] ?></td>
                    </tr>

                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>