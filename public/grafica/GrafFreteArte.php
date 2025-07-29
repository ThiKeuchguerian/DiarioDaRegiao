<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/GrafFreteArte.php';

$Titulo = 'Relatório Frete & Artes';
$URL = URL_PRINCIPAL . 'grafica/GrafFreteArte.php';

// Instanciar a classe
$GrafFreteArte = new GraficaFreteArte();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $consulta = $GrafFreteArte->consultaOrcamentos($dados);
  $Total = count($consulta);
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
              <strong>Data Inicial</strong>
            </div>
            <div class="col">
              <strong>Data Final</strong>
            </div>
            <div class="col">
              <strong>Mes / Ano</strong>
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
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-imprimir" name="btn-imprimir" type="submit" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
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
<?php if (!empty($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          <?php if (!empty($dados['dtInicio'])) : ?>
            Qtde. Total: <?= $Total ?> || Peíodo: <?= date("d/m/Y", strtotime($dados['dtInicio'])) ?> - <?= date("d/m/Y", strtotime($dados['dtFim'])) ?>
          <?php elseif (!empty($dados['MesAno'])) : ?>
            Qtde. Total: <?= $Total ?> || Peíodo: Mês <?= ($dados['MesAno']) ?>
          <?php endif; ?>
        </h5>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">MesAno</th>
              <th scope="col">Dt. Emissão</th>
              <th scope="col">Nrº.: Orçamento</th>
              <th scope="col">Nrº.: Proposta</th>
              <th scope="col">Nrº.: Pedido</th>
              <th scope="col">Cliente</th>
              <th scope="col">Vlr. Frete</th>
              <th scope="col">Vlr. Arte</th>
              <th scope="col">Custo Papel</th>
              <th scope="col">Vlr. Venda Liq.</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consulta as $key => $item) : ?>
              <tr>
                <td style="text-align: center;"><?= date("m/Y", strtotime($item['DataEmissao'])) ?></td>
                <td style="text-align: center;"><?= date("d/m/Y", strtotime($item['DataEmissao'])) ?></td>
                <td style="text-align: center;"><?= $item['OrcamentoId'] ?></td>
                <td style="text-align: center;"><?= $item['PropostaId'] ?></td>
                <td style="text-align: center;"><?= $item['NroPedido'] ?></td>
                <td><?= $item['NomeCliente'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['VlrFrete'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['ValorArte'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['PrecoPapel'] * $item['PesoPapelKg'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['ValorVendaLiq'], 2, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>