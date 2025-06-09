<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ArtPubLegal.php';

$Titulo = 'Publicidade Legal - UpLoad Arquivo';
$URL = URL_PRINCIPAL . 'artes/ArtPubLegal.php';

// Instanciar a classe
$UploadController = new UploadController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {

  // $SomaAnual = $ClassifCheckMetas->ConsultaAno($Ano);

} else if (isset($_POST['btn-analitico'])) {

  // $Analitico = $ClassifCheckMetas->ConsultaDia($MesAno);
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-8">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col-sm-4">
              <strong>Empresa</strong>
            </div>
            <div class="col-sm-4">
              <strong>Título</strong>
            </div>
            <div class="col-sm-4">
              <strong>Período</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="Empresa" name="Empresa" maxlength="100">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="Titulo" name="Titulo" maxlength="100">
            </div>
            <div class="col-sm-4">
              <div class="row g-2">
                <div class="col-6">
                  <input type="date" class="form-control form-control-sm" id="DtInicio" name="DtInicio">
                </div>
                <div class="col-6">
                  <input type="date" class="form-control form-control-sm" id="DtFim" name="DtFim">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Arquivo Digital</strong>
            </div>
            <div class="col">
              <strong>Arquivo Impresso</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="file" class="form-control form-control-sm" id="arquivo_digital" name="arquivo_digital">
            </div>
            <div class="col">
              <input type="file" class="form-control form-control-sm" id="arquivo_impresso" name="arquivo_impresso">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-enviar" name="btn-enviar" type="submit" class="btn btn-primary btn-sm">Enviar</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>