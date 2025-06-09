<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/DisListagemEntrega.php';

$Titulo = 'Listagem de Entrega por Setor';
$URL = URL_PRINCIPAL . 'distribuicao/DisListagemEntrega.php';

// Instanciar a classe
$DisListagemEntrega = new DisListagemEntrega();

$ConsultaSetor = $DisListagemEntrega->ConsultaSetor();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $DtSelecionada = $_POST['DtSelecionada'];
  $setorSelecionado = $_POST['setorSelecionado'];

  // echo "<pre>";
  // var_dump($DtSelecionada);
  // var_dump($setorSelecionado);
  // die();
  // Verifica se o campo de data está vazio
  if (empty($DtSelecionada)) {
    $erro = 'Campo de data não pode ser vazio.';
  } else {
    // Formata a data para o formato correto
    $DtSelecionada = date('Y-m-d', strtotime($DtSelecionada));
  }

  $resultado = $DisListagemEntrega->ConsultaListagemEntregra($DtSelecionada, $setorSelecionado);
  $totalQtde = count($resultado);
  if (empty($setorSelecionado)) {
    $SetorSelec = 'Todos';
  } else {
    $SetorSelec = $setorSelecionado;
  }
  // echo "<pre>";
  // var_dump($SetorSelec);
  // die();
  $erro = 'Nenhum dado foi enviado.';
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Data Entrega</strong>
            </div>
            <div class="col">
              <strong>Setor Entrega</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="date" class="form-control form-control-sm" name="DtSelecionada">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="setorSelecionado" required>
                <option value="Todos" disabled selected>Selecione o Setor</option>
                <option value="">Todos</option>
                <?php foreach ($ConsultaSetor as $setor): ?>
                  <option value="<?= htmlspecialchars($setor['Nome']) ?>">
                    <?= htmlspecialchars($setor['Nome']) ?>
                  </option>
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
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">Setor Entregra: <?= $SetorSelec ?> || Qtde. Total: <?= $totalQtde ?></h5>
        <table class="table table-striped table-hover" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Produto</th>
              <th scope="col">Dt Jornal</th>
              <th scope="col">Qtde</th>
              <th scope="col">Contrato</th>
              <th scope="col">Endereço</th>
              <!-- <th scope="col">Número</th> -->
              <th scope="col">Complemento</th>
              <th scope="col">Setor</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($resultado as $key => $item): ?>
              <tr>
                <td><?= $item['Produto'] ?></td>
                <td><?= date('d/m/Y', strtotime($item['DtJornal'])) ?></td>
                <td><?= $item['Qtde'] ?></td>
                <td><?= $item['Contrato'] ?></td>
                <td><?= $item['Endereco'] ?></td>
                <!-- <td><?= $item['Numero'] ?></td> -->
                <td><?= $item['Complemento'] ?></td>
                <td><?= $item['NomeSetor'] ?></td>
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
<script src="../js/cirlistagementregra.js"></script>
<!-- Footer -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>