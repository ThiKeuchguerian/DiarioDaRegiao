<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/IntegracaoCliVendAg.php';

$Titulo = 'Verifica Integração Cliente/Vendedor/Agencia';
$URL = URL_PRINCIPAL . 'ti/VerificaIntCliVendAgen.php';

// Instanciar a classe
$IntegracaoCliVendAg = new IntegracaoCliVendAg();

// Declarando Variáveis;
$dadosCapt          = [];
$dadosSenior        = [];
$dadosSeniorIn      = [];
$dadosEasyclass     = [];
$dadosGrafica       = [];
$DadosAgenciaCapt   = [];
$DadosAgenciaSenior = [];
$DadosVendCapt      = [];
$DadosVendSenior    = [];

if (isset($_POST['BuscarCli'])) {
  $cpfCnpj        = $_POST['CPFCNPJ'];

  $dadosGi        = $IntegracaoCliVendAg->ClienteGi($cpfCnpj)               ?: [];
  $dadosCapt      = $IntegracaoCliVendAg->ClienteCapt($cpfCnpj)             ?: [];
  $dadosSenior    = $IntegracaoCliVendAg->ClienteSenior($cpfCnpj)           ?: [];
  $dadosSeniorIn  = $IntegracaoCliVendAg->ClienteSeniorInt($cpfCnpj)        ?: [];
  $dadosEasyclass = $IntegracaoCliVendAg->ClienteEasyClass($cpfCnpj)        ?: [];
  $dadosGrafica   = $IntegracaoCliVendAg->ClienteOrcamentoGrafica($cpfCnpj) ?: [];
  // depurar($cpfCnpj, $dadosGi);
  $Total = count($dadosGi) + count($dadosCapt) + count($dadosSenior) + count($dadosSeniorIn) + count($dadosEasyclass) + count($dadosGrafica);
} elseif (isset($_POST['BuscarAg'])) {
  $cpfCnpj = $_POST['CNPJAgen'];

  $DadosAgenciaCapt   = $IntegracaoCliVendAg->AgenciaCapt($cpfCnpj)   ?: [];
  $DadosAgenciaSenior = $IntegracaoCliVendAg->AgenciaSenior($cpfCnpj) ?: [];
} elseif (isset($_POST['BuscarVend'])) {
  $nome = $_POST['NomeVend'];

  $DadosVendGi     = $IntegracaoCliVendAg->VendedorGi($nome)     ?: [];
  $DadosVendCapt   = $IntegracaoCliVendAg->VendedorCapt($nome)   ?: [];
  $DadosVendSenior = $IntegracaoCliVendAg->VendedorSenior($nome) ?: [];
} elseif (isset($_POST['BtnSalvarCli'])) {
  // Captura os dados corretamente
  $nomeCliente = $_POST['RazaoSocial'];
  $cpfCnpj = $_POST['CpfCnpj'];
  $codCliente = $_POST['CodCliente'];
  $tipoCliente = $_POST['Tipo'];
  $codVendedor = $_POST['CodVendedor'];
  $sistema = $_POST['Sistema'];

  // Debug: Exibir todos os dados POST recebidos
  // echo "<pre>";
  // var_dump($nomeCliente, $cpfCnpj, $codCliente, $tipoCliente, $codVendedor, $sistema);
  // die();

  // faz o update e retorna quantas linhas foram alteradas
  $rowsAffected = $IntegracaoCliVendAg->updateCliente(
    $sistema,
    $nomeCliente,
    $cpfCnpj,
    $codCliente,
    $tipoCliente,
    $codVendedor
  );

  if ($rowsAffected > 0) {
    $_SESSION['msg'] = "<script>
          alert('Update realizado com sucesso!! Foram alterados nº.: ({$rowsAffected} registros)');
          window.location.reload();
        </script>";
  } else {
    $_SESSION['msg'] = "Nenhum registro alterado";
  }
  // redireciona para a mesma página via GET
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;

  // $dadosCapt      = $IntegracaoCliVendAg->ClienteCapt($cpfCnpj)             ?: [];
  // $dadosSenior    = $IntegracaoCliVendAg->ClienteSenior($cpfCnpj)           ?: [];
  // $dadosSeniorIn  = $IntegracaoCliVendAg->ClienteSeniorInt($cpfCnpj)        ?: [];
  // $dadosEasyclass = $IntegracaoCliVendAg->ClienteEasyClass($cpfCnpj)        ?: [];
  // $dadosGrafica   = $IntegracaoCliVendAg->ClienteOrcamentoGrafica($cpfCnpj) ?: [];
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col-12 col-sm-3 mx-auto">
    <div class="card shadow-sm h-100">
      <form method="post" id="formTipoIntegracao" name="formTipoIntegracao">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Tipo de Integração</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row align-items-end">
            <div class="col-md-6">
              <select class="form-select form-select-sm" name="TipoIntegracao" id="TipoIntegracao">
                <option value="0">-- Selecione --</option>
                <option value="1">Cliente</option>
                <option value="2">Vendedor</option>
                <option value="3">Agência</option>
              </select>
            </div>
            <div class="col-md-6">
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

<!-- Filtro Cliente (invisível por padrão) -->
<div class="container" id="Cliente" style="display: none;">
  <div class="row justify-content-center">
    <!-- aqui o mx-auto centraliza a coluna de até 4 cols -->
    <div class="col-12 col-sm-5 mx-auto">
      <div class="card shadow-sm h-100">
        <form method="post">
          <div class="card-header bg-primary text-white">
            <div class="row">
              <div class="col">CPF/CNPJ Cliente</div>
            </div>
          </div>
          <div class="card-body">
            <div class="row justify-content-center">
              <div class="col">
                <input type="text" id="CPFCNPJ" name="CPFCNPJ" maxlength="14" pattern="\d+" class="form-control form-control-sm" placeholder="Digite o CPF ou CNPJ">
              </div>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-end">
            <button id="BuscarCli" name="BuscarCli" type="submit" class="btn btn-primary btn-sm">Buscar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Filtro Vendedor -->
<div class="container" id="Vendedor" style="display: none;">
  <div class="row justify-content-center">
    <!-- aqui o mx-auto centraliza a coluna de até 4 cols -->
    <div class="col-12 col-sm-5 mx-auto">
      <div class="card shadow-sm h-100">
        <form method="post">
          <div class="card-header bg-primary text-white">
            <div class="row">
              <div class="col">Nome Vendedor</div>
            </div>
          </div>
          <div class="card-body">
            <div class="row justify-content-center">
              <div class="col">
                <input type="text" id="NomeVend" name="NomeVend" maxlength="20" class="form-control form-control-sm" placeholder="Digite o nome do vendedor">
              </div>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-end">
            <div class="col text-end">
              <button id="BuscarVend" name="BuscarVend" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Filtro Agencia -->
<div class="container" id="Agencia" style="display: none;">
  <div class="row justify-content-center">
    <!-- aqui o mx-auto centraliza a coluna de até 4 cols -->
    <div class="col-12 col-sm-5 mx-auto">
      <div class="card shadow-sm h-100">
        <form method="post">
          <div class="card-header bg-primary text-white">
            <div class="row">
              <div class="col">CNPJ Agência</div>
            </div>
          </div>
          <div class="card-body">
            <div class="row justify-content-center">
              <div class="col">
                <input type="text" id="CNPJAgen" name="CNPJAgen" maxlength="14" pattern="\d+" class="form-control form-control-sm" placeholder="Digite o CNPJ da agência">
              </div>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-end">
            <div class="col text-end">
              <button id="BuscarAg" name="BuscarAg" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Espaço entre o Filtro e o resultado -->
<div class="mb-3"></div>

<!-- Resultado Cliente -->
<?php if (isset($Total)) : ?>
  <div class="container">
    <div class="row">
      <div class="col">
        <div class="card shadow-sm  h-100">
          <h5 class="card-header bg-primary text-white">Resultado Cliente</h5>
          <div class="card-body table-responsive">
            <table id="Resultado" class="table table-striped full-width-table">
              <thead class="table-light">
                <tr class="table-primary">
                  <th scope="col">Sistema</th>
                  <th scope="Col">ID. Cliente</th>
                  <th scope="Col">Cod. Cliente</th>
                  <th scope="col">Nome Cliente</th>
                  <th scope="col">CPF/CNPJ</th>
                  <th scope="col">Tipo</th>
                  <th scope="col">Cod. Vendedor</th>
                  <th scope="col">Ação</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Garante que tudo é array e mescla
                $todos = array_merge(
                  (array)$dadosCapt,
                  (array)$dadosSenior,
                  (array)$dadosSeniorIn,
                  (array)$dadosEasyclass,
                  (array)$dadosGi,
                  (array)$dadosGrafica
                );
                foreach ($todos as $item):
                ?>
                  <tr>
                    <td><?= htmlspecialchars($item['Sistema'] ?? '—') ?></td>
                    <td style="text-align: center;"><?= ($item['ID'] ?? '—') ?></td>
                    <td style="text-align: center;"><?= ($item['CodCliente'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['NomeCliente'] ?? '—') ?></td>
                    <td><?= ($item['CpfCnpj'] ?? '—') ?></td>
                    <td style="text-align: center;"><?= ($item['Tipo'] ?? '—') ?></td>
                    <td style="text-align: center;"><?= ($item['CodVendedor'] ?? '—') ?></td>
                    <td>
                      <button
                        class="btn btn-sm btn-outline-primary btn-edit"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-sistem="<?= htmlspecialchars($item['Sistema'] ?? '') ?>"
                        data-id="<?= ($item['ID'] ?? '') ?>"
                        data-codcliente="<?= ($item['CodCliente'] ?? '') ?>"
                        data-razaosocial="<?= htmlspecialchars($item['NomeCliente'] ?? '') ?>"
                        data-cpfcnpj="<?= ($item['CpfCnpj'] ?? '') ?>"
                        data-tipo="<?= (($item['Tipo'] === 'Físico') ? '1' : '2') ?>"
                        data-codvendedor="<?= ($item['CodVendedor'] ?? '') ?>">Editar
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Resultado Vendedor -->
<?php if (count($DadosVendCapt) + count($DadosVendSenior) > 0): ?>
  <div class="container">
    <div class="row">
      <div class="col">
        <div class="card shadow-sm  h-100">
          <h5 class="card-header bg-primary text-white">Resultado Vendedor</h5>
          <div class="card-body table-responsive">
            <table id="Resultado" class="table table-striped full-width-table">
              <thead class="table-light">
                <tr class="table-primary">
                  <th>Sistema</th>
                  <th>Id. Vendedor</th>
                  <th>Cod. Vendedor</th>
                  <th>Contato</th>
                  <th>Nome Vendedor</th>
                  <th>CNPJ</th>
                  <th>Situação</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($DadosVendCapt as $item) : ?>
                  <tr>
                    <td>Capt</td>
                    <td><?= $item['idVendedor'] ?></td>
                    <td><?= $item['codVendedor'] ?></td>
                    <td>—</td>
                    <td><?= $item['nome'] ?></td>
                    <td><?= $item['cpf'] ?></td>
                    <td><?= $item['situacao'] ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php foreach ($DadosVendSenior as $item) : ?>
                  <tr>
                    <td>Sapiens</td>
                    <td><?= $item['codrep'] ?></td>
                    <td><?= $item['usu_iderep'] ?></td>
                    <td><?= $item['codcdi'] ?></td>
                    <td><?= $item['nomrep'] ?></td>
                    <td><?= $item['cgccpf'] ?></td>
                    <td><?= $item['sitrep'] ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php foreach ($DadosVendGi as $item) : ?>
                  <tr>
                    <td><?= $item['Sistema'] ?></td>
                    <td><?= $item['ID'] ?></td>
                    <td><?= $item['codrep'] ?></td>
                    <td><?= $item['codcdi'] ?></td>
                    <td><?= $item['nomrep'] ?></td>
                    <td><?= $item['CpfCnpj'] ?></td>
                    <td><?= $item['ativo'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Resultado Agencia -->
<?php if (count($DadosAgenciaCapt) + count($DadosAgenciaSenior) > 0) : ?>
  <div class="containers mt-4 mb-5">
    <div class="row">
      <div class="col">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">Resultado Agência</div>
          <div class="card-body table-responsive">
            <table id="ResultadoAgencia" class="table table-hover table-bordered align-middle">
              <thead class="table-light">
                <tr class="table-primary">
                  <th>Sistema</th>
                  <th>Cod. Agência</th>
                  <th>Nome Agência</th>
                  <th>CNPJ</th>
                  <th>Tipo</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($DadosAgenciaCapt as $item) : ?>
                  <tr>
                    <td>Capt</td>
                    <td><?= $item['codAgencia'] ?></td>
                    <td><?= $item['nome'] ?></td>
                    <td><?= $item['cnpj'] ?></td>
                    <td><?= $item['tipo'] ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php foreach ($DadosAgenciaSenior as $item) : ?>
                  <tr>
                    <td>Senior</td>
                    <td><?= $item['usu_iderep'] ?></td>
                    <td><?= $item['nomrep'] ?></td>
                    <td><?= $item['cgccpf'] ?></td>
                    <td><?= $item['tiprep'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o resultado e o footer -->
<div class="mb-3"></div>

<!-- Incluindo Modals -->
<?php require_once __DIR__ . '/../includes/modals/integracao_clivenag.php'; ?>

<!-- Incluindo JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/integracao_clivenag.js"></script>

<!-- Incluindo Footer -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>