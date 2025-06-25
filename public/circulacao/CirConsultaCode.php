<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirConsultaCode.php';

$Titulo = 'Consulta Código de Validação Conta Diário';
$URL = URL_PRINCIPAL . 'circulacao/CirConsultaCode.php';

// Instanciar a classe
$ConsultaCode = new CirConsultaCode();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $consultaCode = $ConsultaCode->consultaCode($dados);
  $Total = COUNT($consultaCode);
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Nome Assinante</strong>
            </div>
            <div class="col">
              <strong>E-Mail Cliente</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" name="nomeAss" id="nomeAss" placeholder="Nome do Assinante">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" name="emailAss" id="emailAss" placeholder="E-mail do Assinante">
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

<!-- Exibindo Resultado -->
<?php if (!empty($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          Dados Assinante
        </h5>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Nome Cliente</th>
              <th scope="col">E-Mail</th>
              <th scope="col">Dt. Criação</th>
              <th scope="col">Dt. Atualização</th>
              <th scope="col">Code</th>
              <th scope="col">Validade Code</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaCode as $key => $item): ?>
              <tr>
                <td><?= $item['name'] ?></td>
                <td><?= $item['email'] ?></td>
                <td><?= date('d/m/Y - H:i', strtotime($item['createdAt'])) ?></td>
                <td><?= date('d/m/Y - H:i', strtotime($item['updatedAt'])) ?></td>
                <td><?= $item['code'] ?></td>
                <td><?= date('d/m/Y - H:i', strtotime($item['expiresAt'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/cir_consultacode.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>