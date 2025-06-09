<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ContConOutrosDoc.php';

$Titulo = 'Consulta Notra Outros Documentos';
$URL = URL_PRINCIPAL . 'contabil/ContConsultaOutrosDoc.php';

// Instanciar a classe
$ContabilConsultaOutrosDoc = new ContabilConsultaOutrosDoc();

if (isset($_POST['btn-buscar'])) {
  $numNF = $_POST['NumNF'];
  $consultas = $ContabilConsultaOutrosDoc->gerarRelatorio($numNF);

  // $Consultas[''];
  // echo "<pre>";
  // var_dump($mesAno);
  // var_dump($numNF);
  // die();
  $Resultado = $consultas['outrosDocumentos'];
  $Total = COUNT($Resultado);

  $ResultadoItens = $consultas['itens'];
  $TotalItens = COUNT($ResultadoItens);

  if ($Total > 0) {
    //  Extrai todos os CodCli do resultado e elimina duplicatas
    $codCliList = array_unique(array_column($Resultado, 'CodCli'));

    //Converte o array em uma string "22887,22985,23809"
    $codCli = implode(',', $codCliList);
    // echo "<pre>";
    // var_dump($codCli);
    // die();
    $dadosCliente = $ContabilConsultaOutrosDoc->consultaCliente($codCli);
    $paramCliente = $ContabilConsultaOutrosDoc->consultaParamCliente($codCli);
  }
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>N.º Nota</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="NumNF" name="NumNF" oninput="this.value = this.value.replace(/[^0-9,]/g, '')">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <!-- <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button> -->
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado -->
<?php if (isset($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">Dados Cliente</h5>
      <div class="card-body">
        <table id="Cliente" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="col">Cod.Cli.</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">Tipo</th>
              <th scope="col">CPF/CNPJ</th>
              <th scope="col">I.E.</th>
              <th scope="col">Endereço</th>
              <th scope="col">Bairro</th>
              <th scope="col">Cidade</th>
              <th scope="col">UF</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dadosCliente as $item): ?>
              <tr>
                <td><?= $item['codcli'] ?></td>
                <td><?= $item['nomcli'] ?></td>
                <td><?= $item['tipcli'] ?></td>
                <td><?= $item['cgccpf'] ?></td>
                <td><?= $item['insest'] ?></td>
                <td><?= $item['Endereco'] ?></td>
                <td><?= $item['baicli'] ?></td>
                <td><?= $item['cidcli'] ?></td>
                <td><?= $item['sigufs'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <br>
        <table id="paramCliente" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="col">Cod.Cli.</th>
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
            </tr>
          </thead>
          <tbody>
            <?php foreach ($paramCliente as $item): ?>
              <tr>
                <td><?= $item['codcli'] ?></td>
                <td><?= $item['T-ICMS'] ?></td>
                <td><?= $item['T-IPI'] ?></td>
                <td><?= $item['T-PIS'] ?></td>
                <td><?= $item['T-COFINS'] ?></td>
                <td><?= $item['IR'] ?></td>
                <td><?= $item['CSLL'] ?></td>
                <td><?= $item['PIS'] ?></td>
                <td><?= $item['COFINS'] ?></td>
                <td><?= $item['OutrasR'] ?></td>
                <td><?= $item['RetPro'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Espaço entre os resultado -->
    <div class="mb-2"></div>

    <!-- Exibindo Resultado Nota -->
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">Nota - Outros Documentos</h5>
      <div class="card-body">
        <table id="OutrosDoc" class="table table-striped table-hover" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th>Entrada</th>
              <th>Cod. Cliente</th>
              <th>Num. Nota</th>
              <th>Num. Documento</th>
              <th>Seq.</th>
              <th>DtVeiculacao</th>
              <th>Cod. Serviço</th>
              <th>Valor</th>
              <th>Lote</th>
              <th>Contabilizado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($Resultado as $item): ?>
              <tr>
                <td><?= $item['Entrada'] ?></td>
                <td><?= $item['CodCli'] ?></td>
                <td><?= $item['Nota'] ?></td>
                <td><?= $item['NumDoc'] ?></td>
                <td><?= $item['Seq'] ?></td>
                <td><?= date('d/m/Y', strtotime($item['DtVeiculacao'])) ?></td>
                <td><?= $item['CodSer'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['Valor'], 2, ',', '.') ?></td>
                <td style="text-align: center;"><?= $item['Lote'] ?></td>
                <td style="text-align: center;"><?= $item['Contabilizado'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Espaço entre os resultado -->
    <div class="mb-2"></div>

    <!-- Exibindo Resultado Itens -->
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">Itens Pedidos</h5>
      <div class="card-body">
        <table id="OutrosDoc" class="table table-striped table-hover" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th>Entrada</th>
              <th>Cod. Cliente</th>
              <th>Num. Nota</th>
              <th>Num. Documento</th>
              <th>Seq.</th>
              <th>DtVeiculacao</th>
              <th>Cod. Serviço</th>
              <th>Valor</th>
              <th>Lote</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ResultadoItens as $item): ?>
              <tr>
                <td><?= $item['Entrada'] ?></td>
                <td><?= $item['CODCLI'] ?></td>
                <td><?= $item['NUMNFV'] ?></td>
                <td><?= $item['NUMPED'] ?></td>
                <td><?= $item['SEQISP'] ?></td>
                <td><?= date('d/m/Y', strtotime($item['DATENT'])) ?></td>
                <td><?= $item['CODSER'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['VlrVeiculado'], 2, ',', '.') ?></td>
                <td><?= $item['PEDCLI'] ?></td>
              </tr>

            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre os resultados -->
<div class="mb-3"></div>

<!-- JavaScript -->
<script src="../js/cont_conoutrosdocs.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>