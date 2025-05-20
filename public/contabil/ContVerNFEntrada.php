<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ContConsulNfEntrada.php';

$Titulo = 'Verifica Retenção NF Entrada';
$URL = URL_PRINCIPAL . 'contabil/ContVerNFEntrada.php';

// Instanciar a classe
$VerificaNFEntrada = new VerificarNFEntrada();

if (isset($_POST['btn-buscar'])) {
  $codEmp = $_POST['CodEmp'];
  $numNota = $_POST['NumNota'];
  $tipoRet = $_POST['TipoRetencao'];

  $notaBase = $VerificaNFEntrada->consultaNotaBase($codEmp, $numNota);
  $notaDev = $VerificaNFEntrada->consultaNotaDeveria($codEmp, $numNota);

  $Total = COUNT($notaBase);

  if ($Total > 0) {
    //  Extrai todos os CodFor do resultado e elimina duplicatas
    $codForList = array_unique(array_column($notaBase, 'codfor'));

    //Converte o array em uma string "22887,22985,23809"
    $codFor = implode(',', $codForList);

    $dadosFor = $VerificaNFEntrada->consultaFornecedor($codFor);
    // echo "<pre>";
    // var_dump($dadosFor);
    // die();
  }
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
              <strong>Cod. Empresa</strong>
            </div>
            <div class="col">
              <strong>Número Nota</strong>
            </div>
            <div class="col">
              <strong>Tipo Retenção</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="CodEmp" name="CodEmp" required>
                <option value="0">-- Selecione Empresa --</option>
                <option value="1">1 - Diário da Região</option>
                <option value="2">2 - FM Diário</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="NumNota" name="NumNota" maxlength="8" placeholder="Número Nota" require>
            </div>
            <div class="col">
              <select name="TipoRetencao" id="TipoRetencao" class="form-control form-control-sm">
                <option value="0">--Selecione Retenção --</option>
                <option value="Todas">ALL 9,45%</option>
                <option value="IR">IR 4,8%</option>
                <option value="CSLL">CSLL 4.65%</option>
              </select>
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

<!-- Incluindo Espaçamento -->
<div class="mb-3"></div>

<!-- Exibindo Resultado -->
<?php if (isset($dadosFor)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">Dados Fornecedor</h5>
      <div class="card-body">
        <table id="dadosFornecedor" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="col">Cod.For.</th>
              <th scope="col">Nome Fornecedor</th>
              <th scope="col">Rec. IPI</th>
              <th scope="col">Rec. ICMS</th>
              <th scope="col">Rec. PIS</th>
              <th scope="col">Rec. Cofins</th>
              <th scope="col">Trib. ISS</th>
              <th scope="col">Out. Ret.</th>
              <th scope="col">Ret. IRRF</th>
              <th scope="col">Ret./Prod.</th>
              <th scope="col">Trib. IPI</th>
              <th scope="col">Trib. ICMS</td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dadosFor as $key => $item): ?>
              <tr>
                <td><?= $item['codfor'] ?></td>
                <td><?= $item['nomfor'] ?></td>
                <td><?= $item['recipi'] ?></td>
                <td><?= $item['recicm'] ?></td>
                <td><?= $item['recpis'] ?></td>
                <td><?= $item['reccof'] ?></td>
                <td><?= $item['triiss'] ?></td>
                <td><?= $item['retour'] ?></td>
                <td><?= $item['retirf'] ?></td>
                <td><?= $item['retpro'] ?></td>
                <td><?= $item['triipi'] ?></td>
                <td><?= $item['triicm'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Incluindo Espaçamento -->
    <div class="mb-2"></div>

    <!-- Exibindo dados da Nota -->
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">Dados Nota Entra Base</h5>
      <div class="card-body">
        <table id="dadosNota" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="Col">Cod. Empresa</th>
              <th scope="col">Num Nota</th>
              <th scope="col">Cod For</th>
              <th scope="col">Vlr Contábil</th>
              <th scope="col">Per ICMS</th>
              <th scope="col">Vlr Base ICMS</th>
              <th scope="col">Vlr ICMS</th>
              <th scope="col">Vlr Isento IPI</th>
              <th scope="col">Vlr Outros IPI</th>
              <th scope="col">Vlr Base PIS Rec.</th>
              <th scope="col">Per. PIS Rec.</th>
              <th scope="col">Vlr. Ret. PIS</td>
              <th scope="col">Vlr Base Cofins Rec.</th>
              <th scope="col">Per. Cofins Rec.</th>
              <th scope="col">Vlr. Ret. Cofins</td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($notaBase as $key => $item): ?>
              <tr>
                <td><?= $item['codemp'] ?></td>
                <td><?= $item['numnfi'] ?></td>
                <td><?= $item['codfor'] ?></td>
                <td><?= number_format($item['vlrctb'], 2, ',', '.') ?></td>
                <td><?= number_format($item['pericm'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrbic'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlricm'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlriip'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlroip'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrbpr'], 2, ',', '.') ?></td>
                <td><?= number_format($item['perpir'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrpir'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrbcr'], 2, ',', '.') ?></td>
                <td><?= number_format($item['percor'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrcor'], 2, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Incluindo Espaçamento -->
    <div class="mb-2"></div>

    <!-- Exibindo dados da Nota -->
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white">Dados Nota Entrada com Todos Impostos</h5>
      <div class="card-body">
        <table id="dadosNotaDev" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <th scope="Col">Cod. Empresa</th>
              <th scope="col">Num Nota</th>
              <th scope="col">Cod For</th>
              <th scope="col">Vlr Contábil</th>
              <th scope="col">Per ICMS</th>
              <th scope="col">Vlr Base ICMS</th>
              <th scope="col">Vlr ICMS</th>
              <th scope="col">Vlr Isento IPI</th>
              <th scope="col">Vlr Outros IPI</th>
              <th scope="col">Vlr Base PIS Rec.</th>
              <th scope="col">Per. PIS Rec.</th>
              <th scope="col">Vlr. Ret. PIS</td>
              <th scope="col">Vlr Base Cofins Rec.</th>
              <th scope="col">Per. Cofins Rec.</th>
              <th scope="col">Vlr. Ret. Cofins</td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($notaDev as $key => $item): ?>
              <tr>
                <td><?= $item['codemp'] ?></td>
                <td><?= $item['numnfi'] ?></td>
                <td><?= $item['codfor'] ?></td>
                <td><?= number_format($item['vlrctb'], 2, ',', '.') ?></td>
                <td><?= number_format($item['pericm'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrbic'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlricm'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlriip'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlroip'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrbpr'], 2, ',', '.') ?></td>
                <td><?= number_format($item['perpir'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrpir'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrbcr'], 2, ',', '.') ?></td>
                <td><?= number_format($item['percor'], 2, ',', '.') ?></td>
                <td><?= number_format($item['vlrcor'], 2, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui o JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/cont_consultanfentrada.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
