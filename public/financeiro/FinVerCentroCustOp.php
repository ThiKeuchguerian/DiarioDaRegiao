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

  $dados = COUNT($consultaMovimentoEstoque);
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
              <strong></strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
            <div class="col">
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

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Exibindo Resultados -->
<?php if (isset($dados)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="col">Nº.: OP</th>
              <th scope="col">Cod. Fam.</th>
              <th scope="col">Cod. Prod.</th>
              <th scope="col">Descição Produto</th>
              <th scope="col">Tempo Prod.</th>
              <th scope="col">Qtde. Prod.</th>
              <th scope="col">Cod. Comp.</th>
              <th scope="col">Descição Componente</th>
              <th scope="col">Tempo Prod.</th>
              <th scope="col">Qtde. Prod.</th>
              <th scope="col">C. Custo</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>