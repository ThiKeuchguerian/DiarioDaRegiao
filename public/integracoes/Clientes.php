<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/IntegracaoClientes.php';

$Titulo = 'Integração de Clientes';
$URL = URL_PRINCIPAL . 'integracoes/Clientes.php';

// Ajustando tempo limete da pagina
set_time_limit(240);

// Instanciar a classe
$IntegracaoClientes = new IntegracaoClientes();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $clienteGi = $IntegracaoClientes->consultaClientesGi($dados);
  $clientesCapt = $IntegracaoClientes->consultaClientesCapt($dados);
  $clientesSenior = $IntegracaoClientes->consultaClientesSenior();

  $cpfSenior = array_column($clientesSenior, 'cgccpf');
  $cpfSeniorMap = array_flip($cpfSenior);

  $clientesNaoEncontratdosCapt = [];
  // Verificação com normalização
  foreach ($clientesCapt as $cliente) {
    $cpfCapt = ($cliente['cpfCnpj']);
    if (!isset($cpfSeniorMap[$cpfCapt])) {
      $clientesNaoEncontratdosCapt[] = $cliente;
    }
  }

  $clientesNaoEncontratdosGi = [];
  // Verificação com normalização
  foreach ($clienteGi as $cliente) {
    $cpfgi = $IntegracaoClientes->limparCpfCnpj($cliente['CGCCPF']);
    if (!isset($cpfSeniorMap[$cpfgi])) {
      $clientesNaoEncontratdosGi[] = $cliente;
    }
  }

  // $clientesNaoEncontratdos = array_merge($clientesNaoEncontratdosCapt, $clientesNaoEncontratdosGi);
  $TotalCapt = count($clientesNaoEncontratdosCapt);
  $TotalGi = count($clientesNaoEncontratdosGi);
  $Total = count($clientesNaoEncontratdosCapt) + count($clientesNaoEncontratdosGi);
} else if (isset($_POST['btn-integracli'])) {
  $dados = $_POST;

  $integraClienteCapt = $IntegracaoClientes->integrarCliente($dados);

  depurar($integraClienteCapt);
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
              <strong>Nome Cliente</strong>
            </div>
            <div class="col">
              <strong>Cpf/Cnpj</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="nomeCli" name="nomeCli">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="cpfCnpj" name="cpfCnpj">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-analitico" name="btn-analitico" type="submit" class="btn btn-primary btn-sm">Analítico</button>
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

<!-- Exibindo Resultado -->
<?php if (!empty($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          Clientes Capt Não Integrados: <?= $TotalCapt ?>||
        </h5>
        <div class="card-footer d-flex justify-content-end">
          <form action="<?= $URL ?>" method="post">
            <input type="hidden" id="selected_ids" name="selected_ids" required>
            <button type="submit" id="btn-integracli" name="btn-integracli" class="btn btn-success btn-sm" value=1 onclick="setSelectedIds()">Integra Clientes</button>
          </form>
        </div>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Código</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">Nome Fantasia/Apelido</th>
              <th scope="col">Cpf/Cnpj</th>
              <th scope="col">I.E.</th>
              <th scope="col">Telefone</th>
              <th scope="col">Celular</th>
              <th scope="col">E-Mail</th>
              <th scope="col">Cod. Ven</th>
              <th scope="col">Dt. Cadastro</th>
              <th scope="col"><input type="checkbox" id="selectAll" name="selectAll" onclick="toggleSelectAll(this, this.closest('table'))"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($clientesNaoEncontratdosCapt as $item) : ?>
              <tr>
                <td><?= $item['codCliente'] ?></td>
                <td><?= $item['razaoSocial'] ?></td>
                <td><?= $item['nomeFantasia'] ?></td>
                <td><?= $item['cpfCnpj'] ?></td>
                <td><?= $item['inscrEstadual'] ?></td>
                <td><?= "(" . $item['telefoneFixoDDD'] . ")" . $item['telefoneFixoNro'] ?></td>
                <td><?= "(" . $item['celularDDD'] . ")" . $item['celularNro'] ?></td>
                <td><?= strlen(trim($item['email'])) > 25 ? substr(trim($item['email']), 0, 25) . '...' : trim($item['email']) ?></td>
                <td><?= trim($item['codVendedor']) ?></td>
                <td><?= date('d/m/Y', strtotime($item['dataCadastro'])) ?></td>
                <td><input type="checkbox" name="selected[]" value='<?= htmlspecialchars(json_encode($item)) ?>'></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          Clientes GI Não Integrados: <?= $TotalGi ?>||
        </h5>
        <div class="card-footer d-flex justify-content-end">
          <form action="<?= $URL ?>" method="post">
            <input type="hidden" id="selected_ids" name="selected_ids" required>
            <button type="submit" id="btn-integracli" name="btn-integracli" class="btn btn-success btn-sm" value=1 onclick="setSelectedIds()">Integra Clientes</button>
          </form>
        </div>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Código</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">Nome Fantasia/Apelido</th>
              <th scope="col">Cpf/Cnpj</th>
              <th scope="col">I.E.</th>
              <th scope="col">Telefone</th>
              <th scope="col">Celular</th>
              <th scope="col">E-Mail</th>
              <th scope="col">Cod. Ven</th>
              <th scope="col">Dt. Cadastro</th>
              <th scope="col"><input type="checkbox" id="selectAll" name="selectAll" onclick="toggleSelectAll(this, this.closest('table'))"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($clientesNaoEncontratdosGi as $item) : //depurar ($item) 
            ?>
              <tr>
                <td><?= $item['CODFAVOREC'] ?></td>
                <td><?= $item['RAZAO'] ?></td>
                <td><?= $item['APELIDO'] ?></td>
                <td><?= $item['CGCCPF'] ?></td>
                <td><?= $item['IERG'] ?></td>
                <td><?= "(" . $item['DDD'] . ")" . $item['FONE1'] ?></td>
                <td><?= $item['FONE2'] ?></td>
                <td><?= strlen(trim($item['EMAIL'])) > 25 ? substr(trim($item['EMAIL']), 0, 25) . '...' : trim($item['EMAIL']) ?></td>
                <td><?= trim($item['CODINT']) ?></td>
                <td><?= date('d/m/Y', strtotime($item['DTI'])) ?></td>
                <td>
                  <?php if (!empty($item['CGCCPF'])) : ?>
                    <input type="checkbox" name="selected[]" value='<?= htmlspecialchars(json_encode($item)) ?>'>
                  <?php else : ?>
                    &ensp;
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/integracao_cliente.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>