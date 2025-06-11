<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirConsultaCliGestor.php';

$Titulo = 'Consulta Cliente Holos+/Gestor';
$URL = URL_PRINCIPAL . 'circulacao/CirConsultaCliGestor.php';

// Instanciar a classe
$ConsultCliGestor = new CirConsultaCliGestor();

if (isset($_POST['btn-buscar'])) {
  $codAssinante = $_POST['CodAssinante'];
  $numContrato = $_POST['NumeroContrato'];
  $emailAssinante = $_POST['EmailAssinante'];

  $consultaAssinante = $ConsultCliGestor->consultaAssinante($codAssinante, $numContrato, $emailAssinante);
  $consultaContrato = $ConsultCliGestor->consultaContrato($codAssinante, $numContrato, $emailAssinante);

  $Total = COUNT($consultaAssinante) + COUNT($consultaContrato);
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
              <strong>Nº.: Contrato</strong>
            </div>
            <div class="col">
              <strong>Cod. Cliente</strong>
            </div>
            <div class="col">
              <strong>E-Mail Cliente</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" name="NumeroContrato" id="NumeroContrato" placeholder="Número do Contrato">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" name="CodAssinante" id="CodAssinante" placeholder="Código do Assinante">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" name="EmailAssinante" id="EmailAssinante" placeholder="E-mail do Assinante">
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
              <th scope="col">Nº. Contrato</th>
              <th scope="col">Cod. Cliente</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">E-Mail</th>
              <th scope="col">Login</th>
              <th scope="col">Senha</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaAssinante as $Assinante): ?>
              <tr>
                <td><?= $Assinante['numeroDoContrato'] ?></td>
                <td><?= $Assinante['codigoDoAssinante'] ?></td>
                <td><?= $Assinante['nomeRazaoSocial'] ?></td>
                <td><?= $Assinante['email'] ?></td>
                <td><?= $Assinante['loginDoUsuarioAssinante'] ?></td>
                <td><?= $Assinante['senhaDoUsuarioAssinante'] ?></td>
                <?php
                $status = 'X';
                foreach ($consultaContrato as $item) {
                  if ($item['numeroDoContrato'] === $Assinante['numeroDoContrato'] && trim($item['email']) === trim($Assinante['email'])) {
                    $status = 'OK';
                    break;
                  }
                }
                ?>
                <?php if ($status === 'OK'): ?>
                  <td style="text-align: center;"><span style='color: blue; font-weight: bold;'><?= $status ?></span></td>
                <?php else: ?>
                  <td style="text-align: center;"><span style='color: red; font-weight: bold;'><?= $status ?></span></td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          Dados Contrato
        </h5>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Nº. Contrato</th>
              <th scope="col">Cod. Cliente</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">E-Mail</th>
              <th scope="col">Situação</th>
              <th scope="col">Tipo Contrato</th>
              <th scope="col">Tipo Assinatura</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaContrato as $item): ?>
              <tr>
                <td><?= $item['numeroDoContrato'] ?></td>
                <td><?= $item['codigoDaPessoa'] ?></td>
                <td><?= $item['NomeAssinante'] ?></td>
                <td><?= $item['email'] ?></td>
                <td><?= $item['situacaoDoContrato'] ?></td>
                <td><?= $item['tipoDeContrato'] ?></td>
                <td><?= $item['TipoDeAssinatura'] ?></td>
                <?php
                $status = 'X';
                foreach ($consultaAssinante as $Assinante) {
                  if ($item['numeroDoContrato'] === $Assinante['numeroDoContrato'] && trim($item['email']) === trim($Assinante['email'])) {
                    $status = 'OK';
                    break;
                  }
                }
                ?>
                <?php if ($status === 'OK'): ?>
                  <td style="text-align: center;"><span style='color: blue; font-weight: bold;'><?= $status ?></span></td>
                <?php else: ?>
                  <td style="text-align: center;"><span style='color: red; font-weight: bold;'><?= $status ?></span></td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/cir_consultacligestor.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>