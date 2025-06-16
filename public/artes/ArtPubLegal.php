<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ArtPubLegal.php';

$Titulo = 'Publicidade Legal - UpLoad Arquivo';
$URL = URL_PRINCIPAL . 'artes/ArtPubLegal.php';

// Instanciar a classe
$UploadController = new UploadController();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $consultaPubLegal = $UploadController->consultaPub($dados);
  $Total = COUNT($consultaPubLegal);
} elseif (isset($_POST['btn-enviar'])) {
  $dados = $_POST;
  depurar($dados);
} elseif (isset($_POST['btn-salvar'])) {
  $dados = $_POST;

  $updatePubLegal = $UploadController->updatePub($dados);
} elseif (isset($_POST['btn-apagar'])) {
  $dados = $_POST;

  $deletePubLegal - $UploadController->deletePub($dados);
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

<!-- Exibindo Resultado -->
<?php if (!empty($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          Qtde. Total: <?= $Total ?>||
        </h5>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">ID</th>
              <th scope="col">Empresa</th>
              <th scope="col">Título</th>
              <th scope="col">Dt. Publicação</th>
              <th scope="col">Arquivo Digital</th>
              <th scope="col">Arquivo Impresso</th>
              <th scope="col">Ação</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaPubLegal as $key => $item): ?>
              <tr>
                <td><?= $item['id'] ?></td>
                <td><?= htmlspecialchars($item['company']) ?></td>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td><?= date('d/m/Y', strtotime($item['DtPublicacao'])) ?></td>
                <td><?= $item['digital'] ?></td>
                <td><?= $item['printed'] ?></td>
                <td>
                  <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="<?= $item['id'] ?>"
                    data-company="<?= htmlspecialchars($item['company']) ?>"
                    data-title="<?= htmlspecialchars($item['title']) ?>"
                    data-DtPublicacao="<?= date('d/m/Y', strtotime($item['DtPublicacao'])) ?>">Editar</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>


<!-- Inclui o modal -->
<?php require_once __DIR__ . '/../includes/modals/art_PubLegal.php'; ?>

<!-- Inclui o Java Script -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/art_publegal.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>