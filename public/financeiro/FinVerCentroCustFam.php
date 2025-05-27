<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinVerCentroCustFam.php';

$Titulo = 'Verifica Centro de Custo / Família';
$URL = URL_PRINCIPAL . 'financeiro/FinVerCentroCustFam.php';

// Instanciar a classe
$CentroCustoFam = new CentroCustoFam();

// Busca Descriação e codigo da família para checkbox
$ConsultaFamilia = $CentroCustoFam->consultaFamilia();

if (isset($_POST['btn-buscar'])) {
  $CodFam = $_POST['codFam'];

  $ConsultaPedido = $CentroCustoFam->consultaPedido($CodFam);
  $ConsultaCentroCusto = $CentroCustoFam->consultaCentroCusto($CodFam);

  $Dados = COUNT($ConsultaPedido) + COUNT($ConsultaCentroCusto);

  $countOk = 0;
  $countX = 0;
  foreach ($ConsultaPedido as $item) {
    foreach ($ConsultaCentroCusto as $key) {
      if ($item['codfam'] === $key['codfam'] && $item['codccu'] === $key['codccu']) {
        $countOk++;
      } else {
        $countX++;
        break;
      }
    }
    $status = 'X';
    foreach ($ConsultaCentroCusto as $key) {
      if ($item['codfam'] === $key['codfam'] && $item['codccu'] === $key['codccu']) {
        $status = 'OK';
        break;
      }
    }
  }
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
              <strong>Código Família</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="codFam" name="codFam">
                <option value="">-- Selecione --</option>
                <?php foreach ($ConsultaFamilia as $familia): ?>
                  <option value="<?= $familia['codfam'] ?>"><?= $familia['codfam'] . "-" . $familia['desfam'] ?></option>
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

<!-- Incluindo Espaçamento -->
<div class="mb-3"></div>

<!-- Exibindo Resultado -->
<?php if (isset($Dados)) : ?>
  <!-- Exibindo dados do Cliente -->
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">
        Pedidos -> <?= COUNT($ConsultaPedido) ?> || 
        Qtde. Pedido Corretos: <?= $countOk ?>   || 
        Qtde. Pedido Incorretos: <?= $countX ?>  ||
        Cod. Família: <?= $CodFam ?>
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th>Cod. Pedido</th>
              <th>Cod. NumPed</th>
              <th>Cod. Cli.</th>
              <th>Cliente</th>
              <th>Dt. Emissão</th>
              <th>Dt. Entrega</th>
              <th>Tns. Ser.</th>
              <th>Cod. Fam.</th>
              <th>Cod. Serviço</th>
              <th>Descrição</th>
              <th>Conta Finan.</th>
              <th>Conta Contabil</th>
              <th>Centro Custo</th>
              <th colspan="2" style="width: 1%;">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ConsultaPedido as $item) :
              // Se o status for "OK", pula para o próximo item
              if ($status === 'OK') {
                continue;
              }
            ?>
              <tr>
                <td><?= $item['numped'] ?></td>
                <td><?= $item['pedcli'] ?></td>
                <td><?= $item['codcli'] ?></td>
                <td><?= mb_strimwidth($item['nomcli'], 0, 25, '...') ?></td>
                <td><?= date('d/m/Y', strtotime($item['datemi'])) ?></td>
                <td><?= date('d/m/Y', strtotime($item['datent'])) ?></td>
                <td><?= $item['tnsser'] ?></td>
                <td><?= $item['codfam'] ?></td>
                <td><?= $item['codser'] ?></td>
                <td><?= mb_strimwidth($item['desser'], 0, 15, '...') ?></td>
                <td><?= $item['ctafin'] ?></td>
                <td><?= $item['ctared'] ?></td>
                <td><?= $item['codccu'] ?></td>
                <?php if ($status === 'OK') : ?>
                  <td style="text-align: center;"><span style='color: blue; font-weight: bold;'><?= $status ?></span></td>
                <?php else : ?>
                  <td style="text-align: center; width: 1%;"><span style='color: red; font-weight: bold;'><?= $status ?></span></td>
                  <td style="width: 1%;">
                    <button type='button' class='btn btn-primary btn-sm' id="btn-edit-modal" data-bs-toggle='modal' data-bs-target="#modal" 
                      data-numped="<?= $item['numped'] ?>"
                      data-pedcli="<?= $item['pedcli'] ?>" 
                      data-codcli="<?= $item['codcli'] ?>" 
                      data-ctafin="<?= $item['ctafin'] ?>" 
                      data-ctared="<?= $item['ctared'] ?>" 
                      data-codccu="<?= $item['codccu'] ?>" 
                      data-codfam="<?= $item['codfam'] ?>" 
                      data-codser="<?= $item['codser'] ?>" 
                      data-tnsser="<?= $item['tnsser'] ?>" 
                      data-nomcli="<?= $item['nomcli'] ?>" 
                      data-desser="<?= $item['desser'] ?>">
                      Editar
                    </button>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Incluindo Espaçamento -->
<div class="mb-3"></div>

<!-- Incluindo Modal -->
<?php require_once __DIR__ . '/../includes/modals/fin_VerCentroCustFam.php'; ?>

<!-- Incluindo Java Script -->
<script src="<?= URL_PRINCIPAL ?>js/fin_vercentrocustfam.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>