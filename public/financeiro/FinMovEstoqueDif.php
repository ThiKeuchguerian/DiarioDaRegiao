<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinMovEstoque.php';

$Titulo = 'Histórico Movimento Estoque';
$URL = URL_PRINCIPAL . 'financeiro/FinMovEstoqueDif.php';

// Instanciar a classe
$HistoricoMovEst = new MovimentoEstoque();


$DadosDeposito = $HistoricoMovEst->listarDepositos();
// Verifica se a requisição é AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getProdutos') {
  header('Content-Type: application/json; charset=utf-8');

  $codDep   = $_GET['CODDEP'];
  $Produtos = $HistoricoMovEst->listarProdutosPorDeposito($codDep);
  echo json_encode($Produtos);
  exit;
}

if (isset($_POST['btn-buscar'])) {
  $codDep = $_POST['Deposito'];
  $codPro = $_POST['Produto'];
  $dtInicio = '2024-01-01';
  $dtFim = date('Y-m-d');

  if ($codDep !== '0' && $codPro !== '0') {
    $detalheMovimentoitem = $HistoricoMovEst->detalheMovimentoItem($codDep, $codPro, $dtInicio, $dtFim);
    $totalDetalhe = count($detalheMovimentoitem);
  } elseif ($codDep !== '0' && $codPro === '0') {
    $mediaHistorico = $HistoricoMovEst->mediaHistoricoMovimento($codDep, $dtInicio, $dtFim);
    $totalMediaHistorico = COUNT($mediaHistorico);

    $analiticoMediaHistorico = $HistoricoMovEst->mediaAnaliticoMovimento($codDep, $dtInicio, $dtFim);
    $totalAnalitico = count($analiticoMediaHistorico);

    $totalMedia = count($mediaHistorico) + count($analiticoMediaHistorico);

    $produtosComComparacao = [];
    $qtdeDif = 0;
    foreach ($mediaHistorico as $item) {
      $codPro = $item['CodPro'];
      $comparacao = 'OK';
      $detalhes = [];

      foreach ($analiticoMediaHistorico as $itens) {
        if ($itens['CodPro'] === $codPro) {
          $detalhes[] = $itens;
          if (abs($itens['DiferencaPreco'] > ($item['PrecoMedio'] * 0.10))) {
            $comparacao = 'X';
            $qtdeDif++;
          }
        }
      }
      $produtosComComparacao[] = [
        'produto' => $item,
        'comparacao' => $comparacao,
        'detalhes' => $detalhes
      ];
    }
    if ($comparacao === 'X') {
      $rowClass = 'class="bg-danger text-white font-weight-bold"';
    } else {
      $rowClass = '';
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
            <div class="col-2">
              <strong>Deposito</strong>
            </div>
            <div class="col-2">
              <strong>Código Item</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="Deposito" name="Deposito" onchange="getProdutoDeposito(this.value)">
                <option value="0">Todos</option>
                <?php foreach ($DadosDeposito as $key => $item): ?>
                  <option value="<?= $item['CODDEP'] ?>"><?= $item['CODDEP'] . ' - ' . $item['DESDEP'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="Produto" name="Produto">
                <option value="0">Todos</option>
              </select>
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
<?php if (!empty($totalDetalhe)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">
        Qtde. Total de Movimentação: <?= $totalDetalhe ?> ||
        Deposito: <?= $codDep ?> ||
        Período: <?= date('d/m/Y', strtotime($dtInicio)) ?> - <?= date('d/m/Y', strtotime($dtFim)) ?>
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover mb-0" style="border: 1px solid #ccc">
          <thead>
            <tr class="table-primary">
              <th>Família</th>
              <th>Cod. Produto</th>
              <th>Desc. Produto</th>
              <th>U.M.</th>
              <th>Dep.</th>
              <th>Nº. Doc.</th>
              <th>Seq.</th>
              <th>Trans.</th>
              <th>Tipo</th>
              <th>Qtde. Mov.</th>
              <th>Vlr. Mov.</th>
              <th>Qtde. Estoque</th>
              <th>Vlr. Estoque</th>
              <th>Preço Médio</th>
              <th>Dt. Digitada</th>
              <th>Usuário</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($detalheMovimentoitem as $item): ?>
              <tr class="background-color: <?= $item['Tipo'] == 'S' ? '#ffcccc' : ($item['Tipo'] == 'E' ? '#cce5ff' : 'transparent') ?>">
                <td style="text-center"><?= $item['Familia'] ?></td>
                <td style="text-center"><?= $item['codpro'] ?></td>
                <td style="white-space: nowrap;"><?= $item['DescrFis'] ?></td>
                <td style="text-center"><?= $item['UM'] ?></td>
                <td style="text-center"><?= $item['Deposito'] ?></td>
                <td style="text-center"><?= $item['NumDoc'] ?></td>
                <td style="text-center"><?= $item['Seq'] ?></td>
                <td style="text-center"><?= $item['Transacao'] ?></td>
                <td style="text-center"><?= $item['Tipo'] ?></td>
                <td style="text-align: right;"><?= number_format($item['QtdeMovi'], 2, ',', '.') ?></td>
                <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($item['VlrMov'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><?= number_format($item['QtdeEst'], 2, '.', '') ?></td>
                <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($item['VlrEst'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['PreMed'], 2, ',', '.') ?></td>
                <td style="text-center"><?= date('d/m/Y', strtotime($item['DtDigitada'])) ?></td>
                <td><?= $item['Operador'] ?></td>
              </tr>
            <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Exibindo Resultado -->
<?php if (!empty($totalMedia)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">
        Qtde. Itens: <?= $totalMediaHistorico ?> ||
        Deposito: <?= $codDep ?> ||
        Período: <?= date('d/m/Y', strtotime($dtInicio)) ?> - <?= date('d/m/Y', strtotime($dtFim)) ?> ||
        Qtde. Itens com Dif.: <?= $qtdeDif ?>
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr class="table-primary">
              <th scope="col">Cod. Produto</th>
              <th scope="col">Descrição Produto</th>
              <th scope="col">Média Qtde. Movimentada</th>
              <th scope="col">Média Valor Movimentado</th>
              <th scope="col">Média Preço Médio</th>
              <th scope="col">Diferença</th>
            </tr>
          </thead>
          <?php foreach ($produtosComComparacao as $dados): ?>
            <?php $item = $dados['produto']; ?>
            <?php if ($dados['comparacao'] === 'X'): ?>
              <tbody>
                <tr class="summary-row" data-CodPro="<?= $item['CodPro'] ?>" onclick="toggleDetails('<?= $item['CodPro'] ?>')">
                  <th><?= $item['CodPro'] ?></th>
                  <th><?= $item['DescPro'] ?></th>
                  <th style="text-align: right;"><?= number_format($item['QtdeMov'], 2, ',', '.') ?></th>
                  <th style="text-align: right;"><span style="float: left;">R$</span> <?= number_format($item['VlrMov'], 2, ',', '.') ?></th>
                  <th style="text-align: right;"><span style="float: left;">R$</span> <?= number_format($item['PrecoMedio'], 2, ',', '.') ?></th>
                  <th style="text-align: center;"><span class='badge bg-danger text-white font-weight-bold'>*****</span></th>
                </tr>
              </tbody>
              <tr class="detail-row" id="detail-<?= $item['CodPro'] ?>" style="display: none;">
                <td colspan="6">
                  <table class="table table-sm table-bordered mb-0">
                    <thead class="table-primary">
                      <tr>
                        <th>Data Movimento</th>
                        <th>Qtde. Movimentada</th>
                        <th>Valor Movimentado</th>
                        <th>Preço Médio</th>
                        <th>Diferença Preço</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($dados['detalhes'] as $itens): ?>
                        <tr <?= $rowClass ?>>
                          <td><?= date('d/m/Y', strtotime($itens['DtMov'])) ?></td>
                          <td style="text-align: right;"><?= number_format($itens['QtdeMov'], 2, ',', '.') ?></td>
                          <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($itens['VlrMov'], 2, ',', '.') ?></td>
                          <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($itens['PrecoMedio'], 2, ',', '.') ?></td>
                          <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($itens['DiferencaPreco'], 2, ',', '.') ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </td>
              </tr>
              </tbody>
            <?php endif; ?>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>


<!-- Incluindo Java Script -->
<script src="<?= URL_PRINCIPAL ?>js/fin_movestoquedif.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>