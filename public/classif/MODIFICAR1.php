<?php
require_once __DIR__ . '/../config/config.php';

$Titulo = 'Verifica Integração Cliente/Vendedor/Agencia';
$URL = URL_PRINCIPAL . 'ti/VerificaIntCliVendAgen.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editCodCliente']) && isset($_POST['editRazaoSocial'])) {
  // Debug: Exibir todos os dados POST recebidos
  echo "<pre>";
  var_dump($_POST);
  die();

  // Captura os dados corretamente
  $NomeCliente = $_POST['editRazaoSocial'];
  $CPFCNPJ = $_POST['editCpfCnpj'];
  $CodCli = $_POST['editCodCliente'];
  $TipoCli = $_POST['editTipo'];
  $CodVendedor = $_POST['editCodVendedor'];
  $Sistema = $_POST['editSistem'];

  if ($Sistema === 'Capt') {
    $UpdateCapt = "UPDATE clientes SET codCliente = :CodCli, razaoSocial = :NomeCliente, codVendedor = :CodVendedor WHERE cpfCnpj = :CPFCNPJ";
    $Update = $this->capt->prepare($UpdateCapt);
    $Update->bindParam(':CodCli', $CodCli);
    $Update->bindParam(':NomeCliente', $NomeCliente);
    $Update->bindParam(':CPFCNPJ', $CPFCNPJ);
    $Update->bindParam(':CodVendedor', $CodVendedor);
    $Update->execute();

    $dadosCapt = $IntegracaoCliVendAg->ClienteCapt($CPFCNPJ);
    $dadosSenior = $IntegracaoCliVendAg->ClienteSenior($CPFCNPJ);
    $dadosSeniorIn = $IntegracaoCliVendAg->ClienteSeniorInt($CPFCNPJ);
    $dadosEasyclass = $IntegracaoCliVendAg->ClienteEasyClass($CPFCNPJ);

    // $DadosUpdate = $Update->fetchAll(PDO::FETCH_ASSOC);
  } elseif ($Sistema === 'Sapiens') {
    $UpdateSenior =  "UPDATE sapiens.dbo.e085cli SET idecli = :CodCli, nomcli = :NomeCliente WHERE cgccpf = :CPFCNPJ";
    $Update = $this->sapiens->prepare($UpdateSenior);
    $Update->bindParam(':CodCli', $CodCli);
    $Update->bindParam(':NomeCliente', $NomeCliente);
    $Update->bindParam(':CPFCNPJ', $CPFCNPJ);
    $Update->execute();


    $dadosCapt = $IntegracaoCliVendAg->ClienteCapt($CPFCNPJ);
    $dadosSenior = $IntegracaoCliVendAg->ClienteSenior($CPFCNPJ);
    $dadosSeniorIn = $IntegracaoCliVendAg->ClienteSeniorInt($CPFCNPJ);
    $dadosEasyclass = $IntegracaoCliVendAg->ClienteEasyClass($CPFCNPJ);
  } elseif ($Sistema === 'SapiensIntegracao') {
    $UpdateSeniorInt = "UPDATE usu_tszr010 SET usu_zr_cod = :CodCli, usu_zr_desc = :NomeCliente, usu_zr_codvend = :CodVendedor  WHERE usu_zr_cgc = :CPFCNPJ";
    $Update = $this->senior->prepare($UpdateSeniorInt);
    $Update->bindParam(':CodCli', $CodCli);
    $Update->bindParam(':NomeCliente', $NomeCliente);
    $Update->bindParam(':CPFCNPJ', $CPFCNPJ);
    $Update->bindParam(':CodVendedor', $CodVendedor);
    $Update->execute();
  } elseif ($Sistema === 'EasyClass' && $TipoCli === '1') {
    $UpdateEasyClass = " UPDATE ec_customer SET id_type = 0 WHERE id_value = :CPFCNPJ AND customer_id = :CodCli ";
    $Update = $this->tecmidia->prepare($UpdateEasyClass);
    $Update->bindParam(':CodCli', $CodCli);
    $Update->bindParam(':CPFCNPJ', $CPFCNPJ);
    $Update->execute();

    $dadosCapt = $IntegracaoCliVendAg->ClienteCapt($CPFCNPJ);
    $dadosSenior = $IntegracaoCliVendAg->ClienteSenior($CPFCNPJ);
    $dadosSeniorIn = $IntegracaoCliVendAg->ClienteSeniorInt($CPFCNPJ);
    $dadosEasyclass = $IntegracaoCliVendAg->ClienteEasyClass($CPFCNPJ);
  } elseif ($Sistema === 'EasyClass' && $TipoCli === '2') {
    $UpdateEasyClass = " UPDATE ec_customer SET id_type = '1' WHERE id_value = :CPFCNPJ AND customer_id = :CodCli ";
    $Update = $this->tecmidia->prepare($UpdateEasyClass);
    $Update->bindParam(':CodCli', $CodCli);
    $Update->bindParam(':CPFCNPJ', $CPFCNPJ);
    $Update->execute();
    $Update = $this->tecmidia->prepare($UpdateEasyClass);
    $Update->bindParam(':CodCli', $CodCli);
    $Update->bindParam(':CPFCNPJ', $CPFCNPJ);
    $Update->execute();

    $dadosCapt = $IntegracaoCliVendAg->ClienteCapt($CPFCNPJ);
    $dadosSenior = $IntegracaoCliVendAg->ClienteSenior($CPFCNPJ);
    $dadosSeniorIn = $IntegracaoCliVendAg->ClienteSeniorInt($CPFCNPJ);
    $dadosEasyclass = $IntegracaoCliVendAg->ClienteEasyClass($CPFCNPJ);
  }
}

if (isset($_POST['BuscarCli'])) {
  $CPFCNPJ = $_POST['CPFCNPJ'];

  $dadosCapt = $IntegracaoCliVendAg->ClienteCapt($CPFCNPJ);
  $dadosSenior = $IntegracaoCliVendAg->ClienteSenior($CPFCNPJ);
  $dadosSeniorIn = $IntegracaoCliVendAg->ClienteSeniorInt($CPFCNPJ);
  $dadosEasyclass = $IntegracaoCliVendAg->ClienteEasyClass($CPFCNPJ);
  $dadosGrafica = $IntegracaoCliVendAg->ClienteOrcamentoGrafica($CPFCNPJ);
} elseif (isset($_POST['BuscarAg'])) {
  $CNPJAgen = $_POST['CNPJAgen'];

  $DadosAgenciaCapt = $IntegracaoCliVendAg->AgenciaCapt($CNPJAgen);
  $DadosAgenciaSenior = $IntegracaoCliVendAg->AgenciaSenior($CNPJAgen);
} elseif (isset($_POST['BuscarVend'])) {
  $NomeVend = $_POST['NomeVend'];

  $DadosVendCapt = $IntegracaoCliVendAg->VendedorCapt($NomeVend);
  $DadosVendSenior = $IntegracaoCliVendAg->VendedorSenior($NomeVend);
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers container d-flex justify-content-center">
  <div class="col col-sm-8">
    <div class="card shadow-sm">
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
                <option value="0">-- Selecione Integração --</option>
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

<!-- Filtro Cliente -->
<div class="containerss" id="Cliente" style="display: none;">
  <div class="col col-sm-8">
    <div class="card shadow-sm mb-3">
      <form method="post">
        <div class="card-header bg-primary text-white">
          CPF/CNPJ Cliente
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col">
              <input type="text" id="CPFCNPJ" name="CPFCNPJ" maxlength="14" pattern="\d+" class="form-control form-control-sm" placeholder="Digite o CPF ou CNPJ">
            </div>
            <div class="col-auto">
              <button id="BuscarCli" name="BuscarCli" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Filtro Vendedor -->
<div class="containerss" id="Vendedor" style="display: none;">
  <div class="card shadow-sm mb-3">
    <form method="post">
      <div class="card-header bg-primary text-white">
        Nome Vendedor
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col">
            <input type="text" id="NomeVend" name="NomeVend" maxlength="20" class="form-control form-control-sm" placeholder="Digite o nome do vendedor">
          </div>
          <div class="col-auto">
            <button id="BuscarVend" name="BuscarVend" type="submit" class="btn btn-primary btn-sm">Buscar</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Filtro Agencia -->
<div class="containerss" id="Agencia" style="display: none;">
  <div class="card shadow-sm mb-3">
    <form method="post">
      <div class="card-header bg-primary text-white">
        CNPJ Agência
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col">
            <input type="text" id="CNPJAgen" name="CNPJAgen" maxlength="14" pattern="\d+" class="form-control form-control-sm" placeholder="Digite o CNPJ da agência">
          </div>
          <div class="col-auto">
            <button id="BuscarAg" name="BuscarAg" type="submit" class="btn btn-primary btn-sm">Buscar</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Resultado Cliente -->
<div class="containerss mt-4 mb-5">
  <div class="row">
    <div class="col">
      <?php if ((!empty($dadosCapt)) or (!empty($dadosSenior)) or (!empty($dadosSeniorIn)) or (!empty($dadosEasyclass))) : ?>
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">Resultado Cliente</div>
          <div class="card-body table-responsive">
            <table id="Resultado" class="table table-hover table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Sistema</th>
                  <th>ID. Cliente</th>
                  <th>Cod. Cliente</th>
                  <th>Nome Cliente</th>
                  <th>CPF/CNPJ</th>
                  <th>Tipo</th>
                  <th>Cod. Vendedor</th>
                  <th>Ação</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (array_merge($dadosCapt, $dadosSenior, $dadosSeniorIn, $dadosEasyclass, $dadosGrafica) as $item): ?>
                  <tr>
                    <td><?= $item['Sistema'] ?? '—' ?></td>
                    <td><?= $item['ID'] ?? '—' ?></td>
                    <td><?= $item['CodCliente'] ?? '—' ?></td>
                    <td><?= $item['NomeCliente'] ?? '—' ?></td>
                    <td><?= $item['CpfCnpj'] ?? '—' ?></td>
                    <td><?= $item['Tipo'] ?? '—' ?></td>
                    <td><?= $item['CodVendedor'] ?? '—' ?></td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary btn-edit" data-bs-toggle="modal" data-bs-target="#editModal"
                        data-sistem="<?= $item['Sistema'] ?? '' ?>"
                        data-id="<?= $item['ID'] ?? '' ?>"
                        data-codcliente="<?= $item['CodCliente'] ?? '' ?>"
                        data-razaosocial="<?= $item['NomeCliente'] ?? '' ?>"
                        data-cpfcnpj="<?= $item['CpfCnpj'] ?? '' ?>"
                        data-tipo="<?= $item['Tipo'] ?? '' ?>"
                        data-codvendedor="<?= $item['CodVendedor'] ?? '' ?>">
                        Editar
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Resultado Vendedor -->
<div class="containerss mt-4 mb-5">
  <div class="row">
    <div class="col">
      <?php if ((!empty($DadosVendCapt)) or !empty($DadosVendSenior)) : ?>
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">Resultado Vendedor</div>
          <div class="card-body table-responsive">
            <table id="ResultadoVendedor" class="table table-hover table-bordered align-middle">
              <thead class="table-light">
                <tr>
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
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Resultado Agencia -->
<div class="containerss mt-4 mb-5">
  <div class="row">
    <div class="col">
      <?php if ((!empty($DadosAgenciaCapt)) or (!empty($DadosAgenciaSenior))) : ?>
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">Resultado Agência</div>
          <div class="card-body table-responsive">
            <table id="ResultadoAgencia" class="table table-hover table-bordered align-middle">
              <thead class="table-light">
                <tr>
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
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="<?= URL_PRINCIPAL ?>js/integracao_clivenag.js"></script>

<?php
require_once __DIR__ . '/../includes/footer.php';
