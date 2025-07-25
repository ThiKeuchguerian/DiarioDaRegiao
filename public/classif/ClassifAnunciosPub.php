<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ClassifAnunciosPub.php';

$Titulo = 'Relatório Anuncios Publicados';
$URL = URL_PRINCIPAL . 'classif/ClassifAnunciosPub.php';

// Instanciar a classe
$AnunciosPublicados = new AnunciosPublicados();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $consultaPublicacoes = $AnunciosPublicados->consultaAnuncios($dados);
  $Total = count($consultaPublicacoes);

  // Inicializa a variável de soma
  $somaValorAnuncio = 0;

  // Percorre os resultados e soma os valores
  foreach ($consultaPublicacoes as $item) {
    // Certifica-se de que o valor seja numérico (pode vir como string)
    $somaValorAnuncio += floatval($item['ValorAnuncio']);
  }
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="from" name="from">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Data Publicação</strong>
            </div>
            <div class="col">
              <strong>Mes/Ano Publicação</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtPublic" name="dtPublic">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
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

<!-- Resultado Consulta -->
<?php if (!empty($consultaPublicacoes)): ?>
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body pb-0">
        <h5 class="card-header bg-primary text-white">
          Qtde. Anúncios: <?= $Total ?></span> ||
          <?php if (!empty($dados['dtPublic'])) : ?>
            Data de Publicação: <?= date("d/m/Y", strtotime($dados['dtPublic'])) ?>
          <?php else : ?>
            Mês de Publicação <?= $dados['MesAno'] ?>
          <?php endif; ?> ||
          Vlr. Total: R$ <?= number_format($somaValorAnuncio, 2, ',', '.') ?>
        </h5>
        <table class="table table-striped table-hover">
          <thead>
            <tr class="table-primary">
              <th scope="Col">Núm. Anuncio</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">Dt. Captação</th>
              <th scope="col">Dt. Publicação</th>
              <th scope="col">Seção</th>
              <th scope="col">Título Anúncio</th>
              <th scope="col">Texto Anúncio</th>
              <th class="col">Vlr. Inserção</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaPublicacoes as $itens): //number_format((float)str_replace(',', '.', ($item['VlrPub'] ?? 0)), 2, ',', '.')
            ?>
              <tr>
                <td><?= intval($itens['NumAnuncio']) ?></td>
                <td><?= $itens['nomeCli'] ?></td>
                <td><?= date('d/m/Y', strtotime($itens['DtCapitacao'])) ?></td>
                <td><?= date('d/m/Y', strtotime($itens['DtPublicacao'])) ?></td>
                <td><?= $itens['secao'] ?></td>
                <td title="<?= htmlspecialchars($itens['Titulo']) ?>"><?= htmlspecialchars(mb_strimwidth($itens['Titulo'], 0, 25, '...')) ?></td>
                <td title="<?= htmlspecialchars($itens['Texto']) ?>"><?= htmlspecialchars(mb_strimwidth($itens['Texto'], 0, 25, '...')) ?></td>
                <td style="text-align: right; white-space: nowrap;"><span style="float: left; display: inline-block;">R$ </span><?= number_format($itens['ValorAnuncio'], 2, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui os Scripts -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>