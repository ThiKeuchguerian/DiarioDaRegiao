<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirListaSemVivaMais.php';

$Titulo = 'Listagem de Assinantes Sem Viva Mais';
$URL = URL_PRINCIPAL . 'circulacao/CirListaSemVivaMais.php';

// Instanciar a classe
$CirListaSemVivaMais = new CirListaSemVivaMais();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $resultado = $CirListaSemVivaMais->ConsultaContratos();
  $QtdeTotal = count($resultado);
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-2">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-body">
          <div class="row justify-content-center text-center">
            <div class="col mb-2">
              <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            </div>
            <div class="col mb-2">
              <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button>
            </div>
            <div class="col mb-2">
              <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
            </div>
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
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">Qtde. Total: <?= $QtdeTotal ?></h5>
        <table class="table table-striped table-hover" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Tipo</th>
              <th scope="col">Contrato</th>
              <th scope="col">NomeAssinante</th>
              <th scope="col">Endereço</th>
              <th scope="col">Municipio</th>
              <th scope="col">Setor</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($resultado as $key => $item): ?>
              <tr>
                <td><?= $item['Tipo'] ?></td>
                <td><?= $item['Contrato'] ?></td>
                <td><?= strlen($item['NomeAssinante']) > 70 ? substr($item['NomeAssinante'], 0, 70) . '...' : $item['NomeAssinante'] ?></td>
                <td><?= $item['Endereco'] ?></td>
                <td style="white-space: nowrap;"><?= $item['Municipio'] ?></td>
                <td style="white-space: nowrap;"><?= $item['Setor'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o resultado e o footer -->
<div class="mb-3"></div>
<!-- JavaScript -->
<script src="../js/cirlistasemvivamais.js"></script>
<!-- Footer -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>