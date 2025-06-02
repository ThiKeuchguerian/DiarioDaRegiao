<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinVerCentroCustOp.php';

$Titulo = 'Verifica Centro de Custo / Ordem de Produção';
$URL = URL_PRINCIPAL . 'financeiro/FinVerCentroCustOp.php';

// Instanciar a classe
$CentroCustoOrdermProducao = new CentroCustoOrdermProducao();

if (isset($_POST['btn-buscar'])) {
  $mesAno = $_POST['MesAno'];
  $numDoc = $_POST['NumOP'];

  if ($mesAno <> '' && $numDoc == '') {
    // echo "<pre>";
    // var_dump($mesAno, $numDoc);
    // die();
    $buscaNumeroOp = $CentroCustoOrdermProducao->buscaNumeroOP($mesAno);
    // Pega todos os numdoc dos resultados
    $numDoc = array_unique(array_column($buscaNumeroOp, 'numdoc'));

    $consultaMovimentoEstoque = $CentroCustoOrdermProducao->movimentoEstoque($numDoc);
    $consultaOrdemProducao = $CentroCustoOrdermProducao->consultaOrdemProducao($numDoc);
    // echo "<pre>";
    // var_dump($numDoc, $consultaMovimentoEstoque);
    // die();
    $dados = COUNT(array_unique(array_column($consultaOrdemProducao, 'numorp')));
  } else if ($mesAno == '' && $numDoc <> '') {
    $consultaMovimentoEstoque = $CentroCustoOrdermProducao->movimentoEstoque($numDoc);
    $consultaOrdemProducao = $CentroCustoOrdermProducao->consultaOrdemProducao($numDoc);
    // echo "<pre>";
    // var_dump($consultaMovimentoEstoque);
    // die();
    $dados = COUNT(array_unique(array_column($consultaOrdemProducao, 'ChaveAgrup')));
  }


  $agrupandoOp = [];
  foreach ($consultaOrdemProducao as $item) {
    $chave = $item['numorp'] . $item['codori'];
    if (!isset($agrupandoOp[$chave])) {
      $agrupandoOp[$chave] = [];
      $agrupandoOp[$chave][] = $item;
    } else {
      array_push($agrupandoOp[$chave], $item);
    }
  }

  $agrupadoMvto = [];
  foreach ($consultaMovimentoEstoque as $item) {
    $chave = $item['NumDoc'] . $item['oriorp'];
    if (isset($agrupandoOp[$chave])) {
      if (!isset($agrupadoMvto[$chave])) {
        $agrupadoMvto[$chave] = [];
        $agrupadoMvto[$chave] = ['master' => $agrupandoOp[$chave], 'movimento' => []];
        if (!isset($agrupadoMvto[$chave]['movimento'])) {
          $agrupadoMvto[$chave]['movimento'] = [];
          $agrupadoMvto[$chave]['movimento'][] = $item;
        } else {
          array_push($agrupadoMvto[$chave]['movimento'], $item);
        }
      } else {
        array_push($agrupadoMvto[$chave]['movimento'], $item);
      }
    }
  }
  foreach ($agrupadoMvto as $numorp) {
    $status = true;
    $statusOk = 0;
    $statusXX = 0;
    if ($numorp['master'][0]['codccu'] === $numorp['movimento'][0]['codccu']) {
      $status = true;
      $statusOk++;
    } else {
      $status = false;
      $statusXX++;
    }
  }
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
        <?php if ($mesAno <> '') : ?>
          Em <?= $mesAno ?> || Qtde. O.P.: <?= $dados ?> || 
          Qtde. O.P. com C.Custo OK: <?= $statusOk ?> ||
          Qtde. O.P. com C.Custo XX: <?= $statusXX ?>
        <?php else : ?>
          O.P. Nº: <?= $numDoc ?> || Qtde. O.P.: <?= $dados ?>
        <?php endif; ?>
      </h5>
      <?php foreach ($agrupadoMvto as $numorp): ?>
        <?php if (count($numorp) > 0) : ?>
          <div class="card-body">
            <h5 class="card-header bg-primary text-white">
              O.P.: <?= $numorp['master'][0]['numorp'] ?> ||
              Produto: <?= $numorp['master'][0]['codpro'] ?> - <?= $numorp['master'][0]['Produto'] ?> || <br>
              Qtde. Prod.: <?= number_format($numorp['master'][0]['QtdeProd'], 3, ',', '.') ?> ||
              C.Custo OP: <?= $numorp['master'][0]['codccu'] ?> ||
              C.Custo Mov.: <?= $numorp['movimento'][0]['codccu'] ?> ||
              Status:<?php if ($status) : ?>
              <span class="badge bg-success">OK</span>
            <?php else : ?>
              <span class="badge bg-danger">XX</span>
            <?php endif; ?>
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
                      <th scope="col">Dt. Ger.</th>
                      <th scope="col">Tempo Prod.</th>
                      <th scope="col">Qtde. Comp.</th>
                      <th scope="col">Qtde. Ut.</th>
                      <th scope="col">C. Custo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($numorp['master'] as $key): ?>

                      <tr>
                        <td><?= $key['codcmp'] ?></td>
                        <td><?= $key['despro'] ?></td>
                        <td><?= date('d/m/Y', strtotime($key['datger'])) ?></td>
                        <td style="text-align: right;"><?= number_format($key['tmpprv'], 3, '.', '') ?></td>
                        <td style="text-align: right;"><?= number_format($key['qtdprv'], 3, ',', '.') ?></td>
                        <td style="text-align: right;"><?= number_format($key['qtduti'], 3, ',', '.') ?></td>
                        <td><?= $key['codccu'] ?></td>
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
                      <th scope="col">Tipo - U.M.</th>
                      <th scope="col">Qtde. Mov.</th>
                      <th scope="col">Vlr. Mov.</th>
                      <th scope="col">C. Custo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($numorp['movimento'] as $mv):  ?>
                      <?php if ($mv['Tipo'] == 'S') : ?>
                        <tr>
                          <td><?= $mv['Deposito'] ?></td>
                          <td><?= $mv['codpro'] ?></td>
                          <td><?= $mv['DescrFis'] ?></td>
                          <td><?= $mv['Tipo'] ?> -> <?= $mv['UM'] ?></td>
                          <td style="text-align: right;"><?= number_format($mv['QtdeMovi'], 3, '.', '') ?></td>
                          <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($mv['VlrMov'], 2, ',', '.') ?></td>
                          <td><?= $mv['codccu'] ?></td>
                        </tr>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php else: ?>
          <h1> Nenhum resultado encontrado</h1>

        <?php endif; ?>
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