<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FilaIntegracoes.php';

$Titulo = 'Integrações Grupo Diário da Região';
$URL = URL_PRINCIPAL . 'ti/FilaIntegracoes.php';

// Instanciar a classe
$FilaIntegracoes = new FilaIntegracoes();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $Dados = $_POST;
  // echo '<pre>';
  // var_dump($Dados);
  // die();

  if (!empty($Dados['EditNumPed'])) {
    $UpdatePedidoGrafica = " UPDATE usu_tszp010 SET usu_zp_valped = :VlrPedido, usu_zp_parc1 = :VlrParc WHERE USU_ZP_NUMORI = :NumPed ";

    $Update = $senior->prepare($UpdatePedidoGrafica);
    $Update->bindParam(':VlrPedido', $Dados['EditVlrPedido']);
    $Update->bindParam(':VlrParc', $Dados['EditVlrParc']);
    $Update->bindParam(':NumPed', $Dados['EditNumPed']);
    $Update->execute();

    $UpdateItensPedidoGrafica = "UPDATE usu_tszq010 SET usu_zq_prcven = :VlrPedido, usu_zq_valor = :VlrPedido, usu_zq_prunit = :VlrPedido  WHERE USU_ZQ_NUMORI = :NumPed ";
    $UpdateItensPed = $senior->prepare($UpdateItensPedidoGrafica);
    $UpdateItensPed->bindParam(':VlrPedido', $Dados['EditVlrPedido']);
    $UpdateItensPed->bindParam(':NumPed', $Dados['EditNumPed']);
    $UpdateItensPed->execute();
  } elseif (!empty($Dados['EditFlProcProtheus'])) {
    $Num = $Dados['EditCodPed'];
    $FlProc = $Dados['EditFlProcProtheus'];
    $CodCli = $Dados['EditCodCliProtheus'];

    $DeletePedidoProtheus = "DELETE FROM SZP010 WHERE ZP_NUMORI = :Num AND ZP_FLPROC = :FlProc AND ZP_CLIENTE = :CodCli";
    $DeleteProtheus = $totvs->prepare($DeletePedidoProtheus);
    $DeleteProtheus->bindParam(':Num', $Num);
    $DeleteProtheus->bindParam(':FlProc', $FlProc);
    $DeleteProtheus->bindParam(':CodCli', $CodCli);
    $DeleteProtheus->execute();
    $DeleteProtheus->bindParam(':Num', $Num);
    $DeleteProtheus->bindParam(':FlProc', $FlProc);
    $DeleteProtheus->bindParam(':CodCli', $CodCli);
    $DeleteProtheus->execute();

    $DeleteItensPedidoProtheus = "DELETE FROM SZQ010 WHERE ZQ_NUMORI = :Num";
    $DeleteItensProtheus = $totvs->prepare($DeleteItensPedidoProtheus);
    $DeleteItensProtheus->bindParam(':Num', $Num);
    $DeleteItensProtheus->execute();
    $DeleteItensPedidoProtheus = "DELETE FROM SZQ010 WHERE ZQ_NUMORI = :Num";
    $DeleteItensProtheus = $totvs->prepare($DeleteItensPedidoProtheus);
    $DeleteItensProtheus->bindParam(':Num', $Num);
    $DeleteItensProtheus->execute();
  } elseif (!empty($Dados['EditAPCapt'])) {

    $CpfCnpj = $Dados['EditCpfCnpjCapt'];
    $NumContrato = $Dados['EditAPCapt'];
    $CodCliCapt = $Dados['EditCodCliCapt'];

    $UpdatePedidoCapt = "UPDATE usu_tpedcapt set usu_cpfcnpj = :CpfCnpj WHERE usu_numori = :NumContrato AND usu_cliente = :CodCliCapt";
    // echo '<pre>';
    // var_dump($UpdatePedidoCapt);
    // die();
    $UpdateCapt = $senior->prepare($UpdatePedidoCapt);
    $UpdateCapt->bindParam(':CpfCnpj', $CpfCnpj);
    $UpdateCapt->bindParam(':NumContrato', $NumContrato);
    $UpdateCapt->bindParam(':CodCliCapt', $CodCliCapt);
    $UpdateCapt->execute();
  } elseif (!empty($Dados['EditCodigoCliProtheus'])) {
    $FlProc = $Dados['EditFlProcCliProtheus'];
    $CodCli = $Dados['EditCodigoCliProtheus'];
    $CpfCnpj = $Dados['EditCpfCnpjProtheus'];
    $IE = $Dados['EditIEProtheus'];

    $UpdateClienteProtheus = "UPDATE SZR010 SET ZR_FLPROC = :FlProc, ZR_INSCR = :IE WHERE ZR_COD = :CodCli AND ZR_CGC = :CpfCnpj";
    // echo '<pre>';
    // var_dump($UpdateClienteProtheus);
    // die();
    $UpdateProtheus = $totvs->prepare($UpdateClienteProtheus);
    $UpdateProtheus->bindParam(':FlProc', $FlProc);
    $UpdateProtheus->bindParam(':IE', $IE);
    $UpdateProtheus->bindParam(':CodCli', $CodCli);
    $UpdateProtheus->bindParam(':CpfCnpj', $CpfCnpj);
    $UpdateProtheus->execute();
  }
}

if (isset($_POST['BtnDeletePedProtheus'])) {
  $Num = $_POST['EditCodPed'];
  $FlProc = $_POST['EditFlProcProtheus'];
  $CodCli = $_POST['EditCodCliProtheus'];

  // Itens pedido
  $DeleteItensPedidoProtheus = "DELETE FROM SZQ010 WHERE ZQ_NUMORI = :Num";
  $DeleteItensProtheus = $totvs->prepare($DeleteItensPedidoProtheus);
  $DeleteItensProtheus->bindParam(':Num', $Num);
  $DeleteItensProtheus->execute();

  // Pedido
  $DeletePedidoProtheus = "DELETE FROM SZP010 WHERE ZP_NUMORI = :Num AND ZP_FLPROC = :FlProc AND ZP_CLIENTE = :CodCli";
  $DeleteProtheus = $totvs->prepare($DeletePedidoProtheus);
  $DeleteProtheus->bindParam(':Num', $Num);
  $DeleteProtheus->bindParam(':FlProc', $FlProc);
  $DeleteProtheus->bindParam(':CodCli', $CodCli);
  $DeleteProtheus->execute();
}

$IntegracaoCliente = $FilaIntegracoes->IntegracaoCliente();
$TotalCliente = COUNT($IntegracaoCliente);

$IntegracaoGraficaEasyClass = $FilaIntegracoes->IntegracaoGraficaEasyClass();
$TotalGrafEasy = COUNT($IntegracaoGraficaEasyClass);

$IntegracaoCaptWeb = $FilaIntegracoes->IntegracaoCaptWeb();
$TotalPedCapt = COUNT($IntegracaoCaptWeb);

$IntegracaoAssinaturasGestor = $FilaIntegracoes->IntegracaoAssinaturasGestor();
$TotalAssGertor = COUNT($IntegracaoAssinaturasGestor);

$IntegracaoBancasGestor = $FilaIntegracoes->IntegracaoBancasGestor();
$TotalBancaGestor = COUNT($IntegracaoBancasGestor);

$IntegracaoClienteProdutoProtheus = $FilaIntegracoes->IntegracaoClienteProdutoProtheus();
$TotalProdCliProtheus = COUNT($IntegracaoClienteProdutoProtheus);

$IntegracaoPedidosProtheus = $FilaIntegracoes->IntegracaoPedidosProtheus();
$TotalPedProtheus = COUNT($IntegracaoPedidosProtheus);

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers">
  <form action=<?= $URL ?> method="post" id="FilaIntegracao" name="FilaIntegracao">
    <div class="content-only">
      <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>"> Voltar </a>
    </div>
  </form>
</div>

<!-- Container da barra -->
<div class="container-fluid h-100">
  <div class="card shadow-sm h-100">
    <div class="card-body d-flex flex-column p-0 h-100">
      <div id="barra-container">
        <div id="progressBar">
          <span id="timerText">05:00</span>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Resultado Integração de Cliente -->
<?php if ($TotalCliente !== 0): ?>
  <div class="container-fluid h-100">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column p-0 h-100">
        <h5 class="card-header bg-primary text-white mb-0">Integração de Cliente || Qtde. Total: <?= $TotalCliente ?></h5>
        <div class="flex-grow-1 overflow-auto">
          <table class="table table-striped table-hover mb-0" id="IntegracaoCliente" name="IntegracaoCliente">
            <thead>
              <tr class="gray-background">
                <th>Código</th>
                <th>Cliente</th>
                <th>CpfCnpj</th>
                <th>I.E.</th>
                <th>I.M.</th>
                <th>FlaProc</th>
                <th>Dt.Geração</th>
                <th>Sistema</th>
                <th>Endereço Completo</th>
                <th>Município</th>
                <th>Mensagem Erro Integração</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($IntegracaoCliente as $key => $linha): ?>
                <tr>
                  <td><?= htmlspecialchars($linha['Codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['CpfCnpj'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['IE'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['IM'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['FlaProc'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['DtGeracao'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Sistema'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Endereco'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Municipio'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['ErroIntegra'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado Integração de Pedido Capt -->
<?php if ($TotalPedCapt !== 0): ?>
  <div class="container-fluid h-100">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column p-0 h-100">
        <h5 class="card-header bg-primary text-white mb-0">Integração de Pedido Capt || Qtde. Total: <?= $TotalPedCapt ?></h5>
        <div class="flex-grow-1 overflow-auto">
          <table class="table table-striped table-hover mb-0" id="Integracao" name="Integracao">
            <thead>
              <tr class="gray-background">
                <th>Lote</th>
                <th>Nº Contrato</th>
                <th>Dt. Emissaão</th>
                <th>Cliente</th>
                <th>CPF/CNPJ</th>
                <th>Título AP</th>
                <th>Vendedor</th>
                <th>Agência</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Mensagem</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($IntegracaoCaptWeb as $key => $linha): ?>
                <tr>
                  <td><?= htmlspecialchars($linha['Lote'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['NCon'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['DtEm'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Cli'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['CpfCnpj'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Vend'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Ag'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Vlr'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Status'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['MenErro'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <?php if ($linha['Status'] === 'E' && !empty($linha['NCon'])): ?>
                      <button class="btn btn-primary btn-sm" id="BtnEditPedCapt" data-bs-toggle="modal" data-bs-target="#ModalPedidoCapt"
                        data-LoteCapt="<?= htmlspecialchars($linha['Lote'], ENT_QUOTES, 'UTF-8') ?>"
                        data-NConCapt="<?= htmlspecialchars($linha['NCon'], ENT_QUOTES, 'UTF-8') ?>"
                        data-DtEmCapt="<?= htmlspecialchars($linha['DtEm'], ENT_QUOTES, 'UTF-8') ?>"
                        data-CliCapt="<?= htmlspecialchars($linha['Cli'], ENT_QUOTES, 'UTF-8') ?>"
                        data-CpfCnpjCapt="<?= htmlspecialchars($linha['CpfCnpj'], ENT_QUOTES, 'UTF-8') ?>"
                        data-TituloCapt="<?= htmlspecialchars($linha['Titulo'], ENT_QUOTES, 'UTF-8') ?>"
                        data-VendCapt="<?= htmlspecialchars($linha['Vend'], ENT_QUOTES, 'UTF-8') ?>"
                        data-AgCapt="<?= htmlspecialchars($linha['Ag'], ENT_QUOTES, 'UTF-8') ?>"
                        data-VlrCapt="<?= htmlspecialchars($linha['Vlr'], ENT_QUOTES, 'UTF-8') ?>"
                        data-StatusCapt="<?= htmlspecialchars($linha['Status'], ENT_QUOTES, 'UTF-8') ?>"
                        data-MenErroCapt="<?= htmlspecialchars($linha['MenErro'], ENT_QUOTES, 'UTF-8') ?>">
                        Editar
                      </button>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado Integração de Pedido Capt -->
<?php if ($TotalGrafEasy !== 0): ?>
  <div class="container-fluid h-100">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column p-0 h-100">
        <h5 class="card-header bg-primary text-white mb-0">Integração de Pedido Gráfica/EasyClass || Qtde. Total: <?= $TotalGrafEasy ?></h5>
        <div class="flex-grow-1 overflow-auto">
          <table class="table table-striped table-hover mb-0" id="Integracao" name="Integracao">
            <thead>
              <tr class="gray-background">
                <th>Num. Pedido</th>
                <th>Flac.Proc.</th>
                <th>Cod. Cliente</th>
                <th>Tipo</th>
                <th>Dt Geração</th>
                <th>Lote</th>
                <th>Origem</th>
                <th>Pedido</th>
                <th>Vlr. Pedido</th>
                <th>Vlr. Parcela</th>
                <th>Mensagem Erro Integração</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($IntegracaoGraficaEasyClass as $key => $linha): ?>
                <tr>
                  <td><?= htmlspecialchars($linha['NumPed'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['FlProc'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['CodCli'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['TipoCli'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['DtGera'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Lote'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Origem'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['PedidoS'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['VlrPedido'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['VlrParc'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Men'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <?php if ($linha['FlProc'] === 'E'): ?>
                      <button class="btn btn-primary btn-sm" id="BtnEditPedGrafEas" data-bs-toggle="modal" data-bs-target="#EditModal"
                        data-NumPed="<?= htmlspecialchars($linha['NumPed'], ENT_QUOTES, 'UTF-8') ?>"
                        data-FlProc="<?= htmlspecialchars($linha['FlProc'], ENT_QUOTES, 'UTF-8') ?>"
                        data-CodCli="<?= htmlspecialchars($linha['CodCli'], ENT_QUOTES, 'UTF-8') ?>"
                        data-TipoCli="<?= htmlspecialchars($linha['TipoCli'], ENT_QUOTES, 'UTF-8') ?>"
                        data-DtGera="<?= htmlspecialchars($linha['DtGera'], ENT_QUOTES, 'UTF-8') ?>"
                        data-Lote="<?= htmlspecialchars($linha['Lote'], ENT_QUOTES, 'UTF-8') ?>"
                        data-Origem="<?= htmlspecialchars($linha['Origem'], ENT_QUOTES, 'UTF-8') ?>"
                        data-PedidoS="<?= htmlspecialchars($linha['PedidoS'], ENT_QUOTES, 'UTF-8') ?>"
                        data-VlrPedido="<?= htmlspecialchars($linha['VlrPedido'], ENT_QUOTES, 'UTF-8') ?>"
                        data-VlrParc="<?= htmlspecialchars($linha['VlrParc'], ENT_QUOTES, 'UTF-8') ?>"
                        data-Men="<?= htmlspecialchars($linha['Men'], ENT_QUOTES, 'UTF-8') ?>">
                        Editar
                      </button>
                    <?php else : ?>
                      <span></span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado Integração de Pedido Capt -->
<?php if ($TotalAssGertor !== 0): ?>
  <div class="container-fluid h-100">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column p-0 h-100">
        <h5 class="card-header bg-primary text-white mb-0">Integração de Contrato Assinatura Gestor || Qtde. Total: <?= $TotalAssGertor ?></h5>
        <div class="flex-grow-1 overflow-auto">
          <table class="table table-striped table-hover mb-0" id="Integracao" name="Integracao">
            <thead>
              <tr class="gray-background">
                <th>Num. Contrato</th>
                <th>Cod. Gestor</th>
                <th>Cod. Senior</th>
                <th>Cod.Pro</th>
                <th>Produto</th>
                <th>Operação</th>
                <th>Dt. Gravação</th>
                <th>Tipo Assinatura</th>
                <th>Plano Pagamento</th>
                <th>Situação</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($IntegracaoAssinaturasGestor as $key => $linha): ?>
                <tr>
                  <td><?= htmlspecialchars($linha['NumContrato'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['CodGestor'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['CodERP'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['CodP'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Produto'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Op'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['DtGra'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['TpAss'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['PlPgto'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Sit'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado Integração de Pedido Capt -->
<?php if ($TotalBancaGestor !== 0): ?>
  <div class="container-fluid h-100">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column p-0 h-100">
        <h5 class="card-header bg-primary text-white mb-0">Integração de Contrato Bancas Gestor || Qtde. Total: <?= $TotalBancaGestor ?></h5>
        <div class="flex-grow-1 overflow-auto">
          <table class="table table-striped table-hover mb-0" id="Integracao" name="Integracao">
            <thead>
              <tr class="gray-background">
                <th>Num. Contrato</th>
                <th>ID</th>
                <th>Produto</th>
                <th>Tipo Assinatura</th>
                <th>Situação</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($IntegracaoBancasGestor as $key => $linha): ?>
                <tr>
                  <td><?= htmlspecialchars($linha['NumCon'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['ID'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Produto'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Operacao'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Sit'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado Integração de Pedido Capt -->
<?php if ($TotalProdCliProtheus !== 0): ?>
  <div class="container-fluid h-100">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column p-0 h-100">
        <h5 class="card-header bg-primary text-white mb-0">Integração de Cliente e Produto Protheus || Qtde. Total: <?= $TotalProdCliProtheus ?></h5>
        <div class="flex-grow-1 overflow-auto">
          <table class="table table-striped table-hover mb-0" id="Integracao" name="Integracao">
            <thead>
              <tr class="gray-background">
                <th>Cod. Cli/Pro</th>
                <th>Nome</th>
                <th>CPF/CNPJ</th>
                <th>I.E.</th>
                <th>Tipo</th>
                <th>Flag</th>
                <th>Dt Ger</th>
                <th>Mensagem Erro</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($IntegracaoClienteProdutoProtheus as $key => $linha): ?>
                <tr>
                  <td><?= $linha['CodCli'] ?></td>
                  <td><?= $linha['NomCli'] ?></td>
                  <td><?= $linha['CpfCnpj'] ?></td>
                  <td><?= $linha['IE'] ?></td>
                  <td><?= $linha['TipoReg'] ?></td>
                  <td><?= $linha['FlagPro'] ?></td>
                  <td><?= $linha['DtGer'] ?></td>
                  <td><?= $linha['MensagemErro'] ?></td>
                  <td>
                    <?php if ($linha['FlagPro'] == 'E'): ?>
                      <button type="button" class="btn btn-primary btn-sm" id="BtnEditClienteProtheus" data-bs-toggle="modal" data-bs-target="#ModalClienteProtheus"
                        data-FlagPro="<?= $linha['FlagPro'] ?>"
                        data-CodCliPro="<?= $linha['CodCli'] ?>"
                        data-NomCli="<?= $linha['NomCli'] ?>"
                        data-CpfCnpj="<?= $linha['CpfCnpj'] ?>"
                        data-IE="<?= $linha['IE'] ?>"
                        data-TipoReg="<?= $linha['TipoReg'] ?>"
                        data-MensagemErro="<?= $linha['MensagemErro'] ?>">
                        Editar
                      </button>
                    <?php else : ?>
                      <span></span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado Integração de Pedido Capt -->
<?php if ($TotalPedProtheus !== 0): ?>
  <div class="container-fluid h-100">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column p-0 h-100">
        <h5 class="card-header bg-primary text-white mb-0">Integração de Pedidos Protheus || Qtde. Total: <?= $TotalPedProtheus ?></h5>
        <div class="flex-grow-1 overflow-auto">
          <table class="table table-striped table-hover mb-0" id="Integracao" name="Integracao">
            <thead>
              <tr class="gray-background">
                <th>Flag Proc</th>
                <th>N. Contrato</th>
                <th>Cod. Cliente</th>
                <th>Dt. Emissaão</th>
                <th>Dt. Geração</th>
                <th>Cod. Vendedor</th>
                <th>Cod. Produto</th>
                <th>N. Pedido</th>
                <th>N. Lote</th>
                <th>N. Ped. Int.</th>
                <th>Situação</th>
                <th colspan="2">Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($IntegracaoPedidosProtheus as $key => $linha): ?>
                <tr>
                  <td><?= htmlspecialchars($linha['FPro'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Num'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Cli'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['DtEmi'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['DtGe'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['CodVen'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['CodProd'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['NumPed'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['LOTE'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['PedInt'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($linha['Erro'], ENT_QUOTES, 'UTF-8') ?></td>
                  <?php if ($linha['FPro'] === 'E' or !empty($linha['NumPed'])): ?>
                    <td>
                      <button type="button" class="btn btn-primary btn-sm" id="BtnEditPedProtheus" data-bs-toggle="modal" data-bs-target="#ModalPedidoProtheus"
                        data-FPro="<?= htmlspecialchars($linha['FPro'], ENT_QUOTES, 'UTF-8') ?>"
                        data-Num="<?= htmlspecialchars($linha['Num'], ENT_QUOTES, 'UTF-8') ?>"
                        data-CodCliente="<?= htmlspecialchars($linha['Cli'], ENT_QUOTES, 'UTF-8') ?>"
                        data-DtEmi="<?= htmlspecialchars($linha['DtEmi'], ENT_QUOTES, 'UTF-8') ?>"
                        data-DtGe="<?= htmlspecialchars($linha['DtGe'], ENT_QUOTES, 'UTF-8') ?>"
                        data-CodVen="<?= htmlspecialchars($linha['CodVen'], ENT_QUOTES, 'UTF-8') ?>"
                        data-PedProtheus="<?= htmlspecialchars($linha['NumPed'], ENT_QUOTES, 'UTF-8') ?>"
                        data-LoteProtheus="<?= htmlspecialchars($linha['LOTE'], ENT_QUOTES, 'UTF-8') ?>"
                        data-PedInt="<?= htmlspecialchars($linha['PedInt'], ENT_QUOTES, 'UTF-8') ?>"
                        data-Erro="<?= htmlspecialchars($linha['Erro'], ENT_QUOTES, 'UTF-8') ?>">
                        Editar
                      </button>
                    </td>
                    <td>
                      <form method="post" action="<?= $URL ?>">
                        <input type="hidden" name="EditCodPed" value="<?= htmlspecialchars($linha['Num'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="EditFlProcProtheus" value="<?= htmlspecialchars($linha['FPro'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="EditCodCliProtheus" value="<?= htmlspecialchars($linha['Cli'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-danger btn-sm" id="BtnDeletePedProtheus" name="BtnDeletePedProtheus">Excluir</button>
                      </form>
                    </td>
                  <?php else : ?>
                    <td colspan="2"><span></span></td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui o modal -->
<?php require_once __DIR__ . '/../includes/modals/fila_integracoes.php'; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/filaintegracao.js"></script>

<!-- Inclui o footer da página -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>