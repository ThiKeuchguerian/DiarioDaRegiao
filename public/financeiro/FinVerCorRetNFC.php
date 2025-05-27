<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinVerCorRetNFC.php';

$Titulo = 'Verifica/Corrigi Retenção de Nota NSC';
$URL = URL_PRINCIPAL . 'financeiro/FinVerCorRetNFC.php';

// Instanciar a classe
$svc = new FinVerificaCorrigeRecencaoNFC();

if (isset($_POST['btn-buscar'])) {
  $codEmp     = (int)$_POST['CodEmp'];
  $numNota    = $_POST['NumNota'];

  // Salvar em sessão
  $_SESSION['CodEmp'] = $codEmp;
  $_SESSION['NumNota'] = $numNota;

  $dadosNFV     = $svc->getNotaBase($codEmp, $numNota);
  $dadosDeveria = $svc->getNotaDeveria($codEmp, $numNota);
  $dadosISV     = $svc->getItensNotaBase($codEmp, $numNota);
  $dadosISVDev  = $svc->getItensNotaDeveria($codEmp, $numNota);
  $codCli       = $dadosNFV[0]['codcli'] ?? null;
  $dadosCli     = $codCli ? $svc->getCliente($codCli) : [];

  $Dados = COUNT($dadosNFV + $dadosISV);
} else if (isset($_POST['btn-corrigir'])) {
  // Usar valores da sessão se não vierem no POST
  $codEmp     = $_SESSION['CodEmp'];
  $numNota    = $_SESSION['NumNota'];
  // $codEmp     = isset($_POST['CodEmp']) ? (int)$_POST['CodEmp'] : ($_SESSION['CodEmp'] ?? 0);
  // $numNota    = isset($_POST['NumNota']) ? $_POST['NumNota'] : ($_SESSION['NumNota'] ?? '');

  $tipoRet    = $_POST['TipoRetencao'];  // 'Todas','IR','CSLL'


  if ($tipoRet === 'Zerar') {
    $svc->zeraRetencao($codEmp, $numNota);
  } else {
    $svc->zeraRetencao($codEmp, $numNota);

    $svc->corrigirRetencao($codEmp, $numNota, $tipoRet);
  }

  $dadosNFV     = $svc->getNotaBase($codEmp, $numNota);
  $dadosDeveria = $svc->getNotaDeveria($codEmp, $numNota);
  $dadosISV     = $svc->getItensNotaBase($codEmp, $numNota);
  $dadosISVDev  = $svc->getItensNotaDeveria($codEmp, $numNota);
  $codCli       = $dadosNFV[0]['codcli'] ?? null;
  $dadosCli     = $codCli ? $svc->getCliente($codCli) : [];

  $Dados = COUNT($dadosNFV + $dadosISV);
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $dados = $_POST;
  $codEmp     = $_SESSION['CodEmp'];
  $numNota    = $_SESSION['NumNota'];

  $dadosCliente = $svc->atualizarCliente($dados);

  $dadosNFV     = $svc->getNotaBase($codEmp, $numNota);
  $dadosDeveria = $svc->getNotaDeveria($codEmp, $numNota);
  $dadosISV     = $svc->getItensNotaBase($codEmp, $numNota);
  $dadosISVDev  = $svc->getItensNotaDeveria($codEmp, $numNota);
  $codCli       = $dadosNFV[0]['codcli'] ?? null;
  $dadosCli     = $codCli ? $svc->getCliente($codCli) : [];

  $Dados = COUNT($dadosNFV + $dadosISV);
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Empresa</strong>
            </div>
            <div class="col">
              <strong>Nº.: Nota</strong>
            </div>
            <div class="col">
              <strong>Tipo Retenção</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="CodEmp" name="CodEmp">
                <option value="0" <?= (isset($codEmp) && $codEmp == 0) ? 'selected' : '' ?>>-- Selecione Empresa --</option>
                <option value="1" <?= (isset($codEmp) && $codEmp == 1) ? 'selected' : '' ?>>1 - Diário da Região</option>
                <option value="2" <?= (isset($codEmp) && $codEmp == 2) ? 'selected' : '' ?>>2 - FM Diário</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="NumNota" name="NumNota" placeholder="Nº Nota Fiscal" maxlength="10" value="<?= isset($numNota) ? htmlspecialchars($numNota) : '' ?>">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="TipoRetencao" id="TipoRetencao">
                <option value="0" <?= (isset($tipoRet) && $tipoRet == '0') ? 'selected' : '' ?>>--Selecione Retenção --</option>
                <option value="Todas" <?= (isset($tipoRet) && $tipoRet == 'Todas') ? 'selected' : '' ?>>ALL 9,45%</option>
                <option value="IR" <?= (isset($tipoRet) && $tipoRet == 'IR') ? 'selected' : '' ?>>IR 4,8%</option>
                <option value="CSLL" <?= (isset($tipoRet) && $tipoRet == 'CSLL') ? 'selected' : '' ?>>CSLL 4.65%</option>
                <option value="Zerar" <?= (isset($tipoRet) && $tipoRet == 'Zerar') ? 'selected' : '' ?>>Zerar</option>
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-corrigir" name="btn-corrigir" type="submit" class="btn btn-success btn-sm">corrigir</button>
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
      <h5 class="card-header bg-primary text-white">Dados Cliente</h5>
      <div class="card-body">
        <table id="dadosCliente" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="col">Cod.Cliente</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">Trib ICMS</th>
              <th scope="col">Trib IPI</th>
              <th scope="col">Trib PIS</th>
              <th scope="col">Trib COFINS</th>
              <th scope="col">Ret. IR</th>
              <th scope="col">Ret. CSLL</th>
              <th scope="col">Ret. PIS</th>
              <th scope="col">Ret. COFINS</th>
              <th scope="col">Outras Ret.</th>
              <th scope="col">Ret. Prod.</th>
              <th scope="col">Ação</td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dadosCli as $key => $item): ?>
              <tr>
                <td><?= $item['codcli'] ?></td>
                <td><?= $item['nomcli'] ?></td>
                <td><?= $item['T_ICMS'] ?></td>
                <td><?= $item['T_IPI'] ?></td>
                <td><?= $item['T_PIS'] ?></td>
                <td><?= $item['T_COFINS'] ?></td>
                <td><?= $item['IR'] ?></td>
                <td><?= $item['CSLL'] ?></td>
                <td><?= $item['PIS'] ?></td>
                <td><?= $item['COFINS'] ?></td>
                <td><?= $item['OutrasRet'] ?></td>
                <td><?= $item['RetPro'] ?></td>
                <td><button class="btn btn-primary btn-edit btn-sm" id="btn-edit-client-modal" data-bs-toggle="modal" data-bs-target="#modal"
                    data-codcli="<?= $item['codcli'] ?>"
                    data-nomcli="<?= $item['nomcli'] ?>"
                    data-TICMS="<?= $item['T_ICMS'] ?>"
                    data-TIPI="<?= $item['T_IPI'] ?>"
                    data-TPIS="<?= $item['T_PIS'] ?>"
                    data-TCOFINS="<?= $item['T_COFINS'] ?>"
                    data-IR="<?= $item['IR'] ?>"
                    data-CSLL="<?= $item['CSLL'] ?>"
                    data-PIS="<?= $item['PIS'] ?>"
                    data-COFINS="<?= $item['COFINS'] ?>"
                    data-OutrasR="<?= $item['OutrasRet'] ?>"
                    data-RetPro="<?= $item['RetPro'] ?>">Editar</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Incluindo Espaçamento -->
  <div class="mb-2"></div>

  <!-- Exibindo dados do Nota -->
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">Dados Nota</h5>
      <div class="card-body">
        <table id="dadosNota" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="col">Situação</th>
              <th scope="Col">Cod. Empresa</th>
              <th scope="col">Num Nota</th>
              <th scope="col">Cod Cliente</th>
              <th scope="col">Vlr Base IR</th>
              <th scope="col">Vlr IR</th>
              <th scope="col">Vlr Base CSLL</th>
              <th scope="col">Vlr CSLL</th>
              <th scope="col">Vlr Base PIS</th>
              <th scope="col">Vlr PIS</th>
              <th scope="col">Vlr Base COFINS</th>
              <th scope="col">Vlr COFINS</th>
              <th scope="col">Vlr Total Retenção</th>
              <th scope="col">Ação</td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dadosNFV as $key => $item): ?>
              <tr>
                <td>Base</td>
                <td><?= $item['codemp'] ?></td>
                <td><?= $item['numnfv'] ?></td>
                <td><?= $item['codcli'] ?></td>
                <td><?= number_format($item['vlrbir'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrirf'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbcl'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrcsl'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbpt'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrpit'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbct'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrcrt'], 6, ',', '.') ?></td>
                <td><?= number_format($item['VlrRetencao'], 6, ',', '.') ?></td>
                <td><button class="btn btn-primary btn-edit btn-sm" id="btn-edit-nota-modal" data-bs-toggle="modal" data-bs-target="#modal"
                    data-codemp="<?= $item['codemp'] ?>"
                    data-numnfv="<?= $item['numnfv'] ?>"
                    data-codcli="<?= $item['codcli'] ?>"
                    data-tipo="Nota"
                    data-vlrbir="<?= number_format($item['vlrbir'], 2, '.', ',') ?>"
                    data-vlrirf="<?= number_format($item['vlrirf'], 2, '.', ',') ?>"
                    data-vlrbcl="<?= number_format($item['vlrbcl'], 2, '.', ',') ?>"
                    data-vlrcsl="<?= number_format($item['vlrcsl'], 2, '.', ',') ?>"
                    data-vlrbpt="<?= number_format($item['vlrbpt'], 2, '.', ',') ?>"
                    data-vlrpit="<?= number_format($item['vlrpit'], 2, '.', ',') ?>"
                    data-vlrbct="<?= number_format($item['vlrbct'], 2, '.', ',') ?>"
                    data-vlrcrt="<?= number_format($item['vlrcrt'], 2, '.', ',') ?>"
                    data-vlrtotal="<?= number_format($item['VlrRetencao'], 2, ',', '.') ?>">Editar</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody>
            <?php foreach ($dadosDeveria as $key => $item): ?>
              <tr>
                <td>Deveria</td>
                <td><?= $item['codemp'] ?></td>
                <td><?= $item['numnfv'] ?></td>
                <td><?= $item['codcli'] ?></td>
                <td><?= number_format($item['vlrbir'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrirf'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbcl'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrcsl'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbpt'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrpit'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbct'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrcrt'], 6, ',', '.') ?></td>
                <td><?= number_format($item['VlrRetencao'], 6, ',', '.') ?></td>
                <td></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Incluindo Espaçamento -->
  <div class="mb-2"></div>

  <!-- Exibindo dados de Itens Nota -->
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">Dados Itens Nota</h5>
      <div class="card-body">
        <table id="dadosItensNota" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="col">Situação</th>
              <th scope="Col">Cod. Empresa</th>
              <th scope="col">Num Nota</th>
              <th scope="col">Seq.</th>
              <th scope="col">Vlr Base Nota</th>
              <th scope="col">Vlr Base IR</th>
              <th scope="col">% IR</th>
              <th scope="col">Vlr IR</th>
              <th scope="col">Vlr Base CSLL</th>
              <th scope="col">% CSLL</th>
              <th scope="col">Vlr CSLL</th>
              <th scope="col">Vlr Base PIS</th>
              <th scope="col">% PIS</th>
              <th scope="col">Vlr PIS</th>
              <th scope="col">Vlr Base COFINS</th>
              <th scope="col">% CONFIS</th>
              <th scope="col">Vlr COFINS</th>
              <th scope="col">Vlr Total Retenção</th>
              <th scope="col">Ação</td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dadosISV as $key => $item): ?>
              <tr>
                <td>Base</td>
                <td><?= $item['codemp'] ?></td>
                <td><?= $item['numnfv'] ?></td>
                <td><?= $item['seqisv'] ?></td>
                <td><?= number_format($item['vlrlse'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrbir'], 2, ',', '.') ?></td>
                <td><?= number_format($item['perirf'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrirf'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbcl'], 2, ',', '.') ?></td>
                <td><?= number_format($item['percsl'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrcsl'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbpt'], 2, ',', '.') ?></td>
                <td><?= number_format($item['perpit'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrpit'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbct'], 2, ',', '.') ?></td>
                <td><?= number_format($item['percrt'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrcrt'], 6, ',', '.') ?></td>
                <td><?= number_format($item['VlrRetencao'], 6, ',', '.') ?></td>
                <td><button class="btn btn-primary btn-edit btn-sm" id="btn-edit-itens-modal" data-bs-toggle="modal" data-bs-target="#modal"
                    data-codemp="<?= $item['codemp'] ?>"
                    data-numnfv="<?= $item['numnfv'] ?>"
                    data-seqisv="<?= $item['seqisv'] ?>"
                    data-tipo="ItensNFC"
                    data-vlrlse="<?= number_format($item['vlrlse'], 2, '.', ',') ?>"
                    data-vlrbir="<?= number_format($item['vlrbir'], 2, '.', ',') ?>"
                    data-perirf="<?= number_format($item['perirf'], 2, '.', ',') ?>"
                    data-vlrirf="<?= number_format($item['vlrirf'], 2, '.', ',') ?>"
                    data-vlrbcl="<?= number_format($item['vlrbcl'], 2, '.', ',') ?>"
                    data-percsl="<?= number_format($item['percsl'], 2, '.', ',') ?>"
                    data-vlrcsl="<?= number_format($item['vlrcsl'], 2, '.', ',') ?>"
                    data-vlrbpt="<?= number_format($item['vlrbpt'], 2, '.', ',') ?>"
                    data-perpit="<?= number_format($item['perpit'], 2, '.', ',') ?>"
                    data-vlrpit="<?= number_format($item['vlrpit'], 2, '.', ',') ?>"
                    data-vlrbct="<?= number_format($item['vlrbct'], 2, '.', ',') ?>"
                    data-percrt="<?= number_format($item['percrt'], 2, '.', ',') ?>"
                    data-vlrcrt="<?= number_format($item['vlrcrt'], 2, '.', ',') ?>"
                    data-vlrtotal="<?= number_format($item['VlrRetencao'], 2, '.', ',') ?>">Editar</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody>
            <?php foreach ($dadosISVDev as $key => $item): ?>
              <tr>
                <td>Deveria</td>
                <td><?= $item['codemp'] ?></td>
                <td><?= $item['numnfv'] ?></td>
                <td><?= $item['seqisv'] ?></td>
                <td><?= number_format($item['vlrlse'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrbir'], 2, ',', '.') ?></td>
                <td><?= number_format($item['perirf'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrirf'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbcl'], 2, ',', '.') ?></td>
                <td><?= number_format($item['percsl'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrcsl'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbpt'], 2, ',', '.') ?></td>
                <td><?= number_format($item['perpit'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrpit'], 6, ',', '.') ?></td>
                <td><?= number_format($item['vlrbct'], 2, ',', '.') ?></td>
                <td><?= number_format($item['percrt'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrcrt'], 6, ',', '.') ?></td>
                <td><?= number_format($item['VlrRetencao'], 6, ',', '.') ?></td>
                <td></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui o Modal para edição de dados -->
<?php require_once __DIR__ . '/../includes/modals/fin_VerCorRetNFC.php'; ?>

<!-- Inclui o JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/fin_vercorretnfc.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>