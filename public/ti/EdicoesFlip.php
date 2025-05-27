<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/EdicoesFlip.php';

$Titulo = 'Datas Disponíveis de Edições do Flip';
$URL = URL_PRINCIPAL . 'ti/EdicoesFlip.php';

// Instanciar a classe
$DatasEdicoesFlip = new EdicoesFlip();

// Obtem o Ano 
$ano = isset($_GET['year']) && is_numeric($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

$buscaProdutos = $DatasEdicoesFlip->buscaProdutos();

if (isset($_POST['btn-buscar'])) {
  $year = $_POST['ano'];
  $idProd = $_POST['produto'];

  // echo "<pre>";
  // var_dump($year, $idProd);
  // die();
  // Obter datas do banco de dados para o ano selecionado
  $datesFromDb = $DatasEdicoesFlip->buscarDatasEdicoes($year, $idProd);
  // echo "<pre>";
  // var_dump($datesFromDb);
  // die();

  //Gerar o HTML para o calendário de 12 meses
  $calendarHTML = $DatasEdicoesFlip->gerarMesesDoAno($year, $datesFromDb);

  $dados = COUNT($datesFromDb);
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
              <strong>Selecione Ano</strong>
            </div>
            <div class="col">
              <strong>Produto</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="number" id="ano" name="ano" class="form-control" value="<?php echo $ano; ?>" min="2000" max="2100" />
            </div>
            <div class="col">
              <select id="produto" name="produto" class="form-select form-select-sm">
                <option value="0"> --Selecione Produto-- </option>
                <?php foreach ($buscaProdutos as $produto): ?>
                  <option value="<?= $produto['id'] ?>"><?= $produto['name'] ?></option>
                <?php endforeach; ?>
              </select>
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

<!-- Exibe o calendário do ano selecionado -->
<?php if (isset($dados)): ?>
  <div class="container h-100">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column p-0 h-100">
        <div class="row row-cols-1 row-cols-md-3 g-4">
          <?php echo $calendarHTML; ?>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui os JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>