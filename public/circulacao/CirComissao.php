<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirComissao.php';

$Titulo = 'Comissão Dept. Circulação';
$URL = URL_PRINCIPAL . 'circulacao/CirComissao.php';

// Instanciar a classe
$CirComissao = new CirComissao();

if (isset($_POST['btn-buscar'])) {
  $dtInicio = $_POST['dtInicio'];
  $dtFim = $_POST['dtFim'];

  $consultaComissao = $CirComissao->consultaComissao($dtInicio, $dtFim);
  $TotalVen = count($consultaComissao);
  // depurar($consultaComissao);

  $agrupaVen = [];
  $QtdeVend  = 0;

  foreach ($consultaComissao as $codVen => $item) {
    $codVen = $item['CodVen'];
    if (!isset($agrupaVen[$codVen])) {
      $agrupaVen[$codVen] = [
        'NomeVen'    => $item['NomeVen'],
        'PerCom'     => $item['PerCom'],
        'QtdeVen'       => 0,
        'QtdeRen'       => 0,
        'QtdeCan'       => 0,
        'TotalVlrVen'   => 0.0,
        'TotalVlrRec'   => 0.0,
        'dadosCon'      => [],
      ];
      $QtdeVend++;
    }
    // Guarda o registro
    $agrupaVen[$codVen]['dadosCon'][] = $item;

    // Incrementa contadores e somas
    if ($item['Tipo'] === 'V') {
      $agrupaVen[$codVen]['QtdeVen'] += (float)$item['Peso'];
      $agrupaVen[$codVen]['TotalVlrVen'] += (float)$item['valorTotal'];
    } elseif ($item['Tipo'] === 'R') {
      $agrupaVen[$codVen]['QtdeRen'] += (float)$item['Peso'];
      $agrupaVen[$codVen]['TotalVlrRec'] += (float)$item['valorTotal'];
    } elseif ($item['Tipo'] === 'C') {
      $agrupaVen[$codVen]['QtdeCan'] += (float)$item['Peso'];
    } 
  }
  uksort($agrupaVen, function ($a, $b) use ($agrupaVen) {
    return strcasecmp($agrupaVen[$a]['NomeVen'], $agrupaVen[$b]['NomeVen']);
  });
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-4">
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
            <button id="btn-imprimir" name="btn-imprimir" type="submit" class="btn btn-primary btn-sm">Imprimir</button>
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
<?php if (isset($TotalVen)) : ?>
  <div class="container d-flex justify-content-center">
    <div class="col col-sm">
      <div class="card shadow-sm">
        <h5 class="card-header bg-primary text-white mb-0">
          Qtde. Total Vendedores: <?= $QtdeVend ?><br>
          Período: <?= date('d/m/Y', strtotime($dtInicio)) ?> - <?= date('d/m/Y', strtotime($dtFim)) ?>
        </h5>
        <div class="card-body">
          <table class="table table-striped table-hover" id="Resultado" name="Resultado">
            <thead>
              <tr class="table-primary">
                <th scope="col">Vendedor</th>
                <th scope="col" style="text-align: center;">Qtde. Vendas</th>
                <th scope="col" style="text-align: center;">Qtde. Canc.</th>
                <th scope="col" style="text-align: center;">Vlr.  Total Vendas</th>
                <th scope="col" style="text-align: center;">Vlr. Total Recebido</th>
                <th scope="col" style="text-align: center;">% Comissão</th>
                <th scope="col" style="text-align: center;">Valor Comissão</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($agrupaVen as $key => $item): ?>
                <tr>
                  <td><?= $item['NomeVen'] ?></td>
                  <td style="text-align: center;"><?= $item['QtdeVen'] ?></td>
                  <td style="text-align: center;"><?= $item['QtdeCan'] ?></td>
                  <td style="text-align: right;"><span style="float: left;">R$</span> <?= number_format($item['TotalVlrVen'], 2, ',', '.') ?></th>
                  <td style="text-align: right;"><span style="float: left;">R$</span> <?= number_format($item['TotalVlrRec'], 2, ',', '.') ?></th>
                  <td style="text-align: center;"><?= $item['PerCom'] ?> %</td>
                  <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format(($item['TotalVlrRec'] * '0.125'), 2, ',', '.') ?></th>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="mb-2"></div>
  <?php foreach ($agrupaVen as $codVen => $itens): ?>
    <div class="container">
      <div class="card shadow-sm ">
        <div class="card-body">
          <div class="accordion" id="Comissao">
            <div class="accordion-item">
              <div class="accordion-header" id=<?= $codVen ?>>
                <button class="accordion-button collapsed bg-primary text-white py-1" style="min-height: 2rem;" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapse<?= $codVen ?>"
                  aria-expanded="false"
                  aria-controls="collapse<?= $codVen ?>">
                  Vendedor: <?= htmlspecialchars($itens['NomeVen']) ?>
                </button>
              </div>
              <div id="collapse<?= $codVen ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $codVen ?>" data-bs-parent="#accordionComissoes">
                <div class="accordion-body p-0">
                  <table class="table table-sm mb-0">
                    <thead>
                      <tr class="table-primary">
                        <th>Cliente</th>
                        <th>Nome Cliente</th>
                        <th>Mº.: Contrato</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Tipo</th>
                        <th>Peso</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($itens['dadosCon'] as $key): ?>
                        <tr>
                          <td><?= $key['CodCli'] ?></td>
                          <td><?= $key['NomeCli'] ?></td>
                          <td><?= $key['NumCon'] ?></td>
                          <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($key['valorTotal'], 2, ',', '.') ?></td>
                          <td style="text-align: center;"><?= $key['Status'] ?></td>
                          <td><?= $key['Tipo'] ?></td>
                          <td><?= number_format($key['Peso'], 2, '.', ',') ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/cir_comissao.js"></script>

<!-- Footer -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>