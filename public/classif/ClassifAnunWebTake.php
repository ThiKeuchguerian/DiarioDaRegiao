<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ClassifAnunWebTake.php';

$Titulo = 'Listagem de Anuncios Feitos - WebTake';
$URL = URL_PRINCIPAL . 'classif/ClassifAnunWebTake.php';

// Instanciar a classe
$ClassifAnunWebTake = new ClassifAnunWebTake();

// Buscando usuários
$ListUser = $ClassifAnunWebTake->ConsultaUsarios();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $MesAno  = $_POST['MesAno'];
  $Usuario = $_POST['Usuario'];

  // echo "<pre>";
  // var_dump($MesAno);
  // var_dump($Usuario);
  // die();

  $resultado = $ClassifAnunWebTake->ConsultaAnunciosWebTake($MesAno, $Usuario);
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="container">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Mes/Ano</strong>
            </div>
            <div class="col">
              <strong>Nome Usuário</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="Usuario" name="Usuario">
                <option value="0">Selecione Usuário</option>
                <?php foreach ($ListUser as $usuario) : ?>
                  <option value="<?= $usuario['fullname'] ?>"><?= $usuario['fullname'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
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

<!-- Resultado Analítico -->
<?php if (isset($resultado)) : ?>
  <?php
  $dadosAgrupados = [];
  $GeralComErro = '0';
  foreach ($resultado as $item) {
    $Users = $item['adtaker_name'];
    $dadosAgrupados[$Users][] = $item;
    $TotalGeralAnuncios = COUNT($resultado);
    if (strpos($item['ErrorMsg'], 'Error:') === 0) {
      $GeralComErro++;
    }
  }
  $GeralSemErro = $TotalGeralAnuncios - $GeralComErro;
  ?>
  <div class="container">
    <div class="card shadow-sm">
    <h5 class="card-header bg-primary text-white">
      Total Anúncios: <?= $TotalGeralAnuncios ?> ||
      Total Publicado: <?= $GeralSemErro ?>  ||
      Total Erros: <?= $GeralComErro ?>
    </h5>
      <div class="card-body">
        <?php foreach ($dadosAgrupados as $Users => $itens) : ?>
          <?php
          $TotalAnuncios = COUNT($itens);
          $SemAnuncios = 0;
          $ComErro = 0;
          foreach ($itens as $item) {
            if (strpos($item['ErrorMsg'], 'Error:') === 0) {
              $ComErro++;
            }
          }
          $SemErro = $TotalAnuncios - $ComErro;
          ?>
          <h5 class="card-header bg-primary text-white">
            Qtde. Anúncios: <?= $TotalAnuncios ?> || Qtde. Publicada: <?= $SemErro ?> || Qtde. Com Erro <?= $ComErro ?>
          </h5>
          <table class="table table-striped table-hover Resultado" id="Resultado" name="Resultado">
            <thead>
              <tr class="table-primary">
                <th>Nº. Contrato</th>
                <th>CPF/CNPJ</th>
                <th>Usuário</th>
                <th>Dt. Criação</th>
                <th>Dt. Publicação</th>
                <th>Tipo</th>
                <th>Seção</th>
                <th>Mensagem</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($itens as $key) : ?>
                <tr>
                  <td><?= $key['contract'] ?></td>
                  <td><?= $key['intermid_value'] ?></td>
                  <td><?= $key['adtaker_name'] ?></td>
                  <td><?= date('d/m/Y', strtotime($key['created_date'])) ?></td>
                  <td><?= date('d/m/Y', strtotime($key['takedate'])) ?></td>
                  <td><?= $key['style_name'] ?></td>
                  <td><?= $key['section_name'] ?></td>
                  <td><?= substr($key['ErrorMsg'], 0, 65) ?><?= strlen($key['ErrorMsg']) > 65 ? '...' : '' ?></td>
                  <td>
                    <?php if (strpos($key['ErrorMsg'], 'Error:') === 0) : ?>
                      <span class="badge bg-danger"> XX </span>
                    <?php else : ?>
                      <span class="badge bg-primary">OK</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tbody>
            </tbody>
          </table>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o resultado e o footer -->
<div class="mb-3"></div>
<!-- JavaScript -->
<script src="../js/maskcampos.js"></script>
<script src="../js/claanunwebtake.js"></script>
<!-- Footer -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>