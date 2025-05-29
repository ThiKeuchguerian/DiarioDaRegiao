<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinMovEstoque.php';

$Titulo = 'Histórico Movimento Estoque';
$URL = URL_PRINCIPAL . 'financeiro/FinMovEstoque.php';

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
            <div class="col-2">
              <strong>Deposito</strong>
            </div>
            <div class="col-2">
              <strong>Código Item</strong>
            </div>
            <div class="col-4">
              <strong>Período</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="Deposito" name="Deposito" onchange="getProdutoDeposito(this.value)" required>
                <option value="0">Todos</option>
                <?php foreach ($DadosDeposito as $key => $item): ?>
                  <option value="<?= $item['CODDEP'] ?>"><?= $item['CODDEP'] . ' - ' . $item['DESDEP'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="Produto" name="Produto" required>
                <option value="0">Todos</option>
              </select>
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="DtInicio" name="DtInicio">
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="DtFim" name="DtFim">
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

<!-- Incluindo Java Script -->
<script src="<?= URL_PRINCIPAL ?>js/fin_movestoque.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>