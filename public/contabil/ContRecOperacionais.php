<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ContRecOperacionais.php';

$Titulo = 'Receitas Operacionais';
$URL = URL_PRINCIPAL . 'contabil/ContRecOperacionais.php';

// Instanciar a classe
$ContabilRecOperacionais = new ContabilRecOperacionais();

if (isset($_POST['btn-buscar'])) {
  $mesAno = $_POST['MesAno'] ?? date('m/Y');

  $Consultas = $ContabilRecOperacionais->gerarRelatorio($mesAno, []);
  
  $ResultComun = $Consultas['mesComunicacao'];
  $TotalI = count($ResultComun);
  $ResultOutDoc = $Consultas['outrosDocumentos'];
  $TotalD = count($ResultOutDoc);
  $ResultNotaFat = $Consultas['mesNotaFat'];
  list($TotalNF, $TotalVlrBruto) = $ResultNotaFat;
  $ResultadoDiferencaItens = $Consultas['diferencaItens'];
  $TotalDI = count($ResultadoDiferencaItens);
  $ResultadoDiferencaOutrosDoc = $Consultas['diferencaOutrosDoc'];
  $TotalDO = count($ResultadoDiferencaOutrosDoc);
  $ResultadoImportar = $Consultas['notasParaImportar'];
  $TotalIM = count($ResultadoImportar);
  $ResultadoItensNF = $Consultas['itensNotasFiscais'];
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Mes / Ano</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="MesAno" name="MesAno">
                <option value="0">-- Selecione --</option>
                <?php
                $currentMonth = date('m');
                $currentYear = date('Y');

                // Loop pelos 24 meses anteriores
                for ($i = 0; $i < 24; $i++) {
                  $mesAno = date('m/Y', strtotime("-$i month", strtotime("$currentYear-$currentMonth-01")));
                  $selected = ($mesAno == $MesAnoSelecionado) ? 'selected' : '';
                  echo "<option value=\"$mesAno\" $selected>$mesAno</option>";
                }
                ?>
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

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado -->
<?php if (isset($Consultas)) : ?>
  <div class="container">
    <!-- Exibindo Receitas Operacionais -->
    <div class="row gx-1">
      <div class="col-md-6">
        <div class="card shadow-sm h-100">
          <h5 class="card-header bg-primary text-white"> Receitas Operacionais - Comunicação</h5>
          <div class="card-body">
            <table id="Comunicacao" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
              <thead>
                <tr class="table-primary">
                  <th>Tipo Serviço</th>
                  <th>Mes/Ano</th>
                  <th>Vlr. Itens F121CIP</th>
                  <th>Vlr. Itens F660BLA</th>
                  <th>Vlr. Difer.</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ResultComun as $item) : ?>
                  <tr>
                    <td><?= $item['TipoServico'] ?></td>
                    <td><?= $item['MesAno'] ?></td>
                    <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format(($item['VlrVeic'] ?? 0), 2, ',', '.') ?></td>
                    <td style="text-align: right; white-space: nowrap;">
                      <?php
                      // // Função para converter os valores em formato de moeda para float
                      // if (!function_exists('convertCurrencyToFloat')) {
                      //   function convertCurrencyToFloat($value)
                      //   {
                      //     // Remove o "R$ ", os pontos e substitui a vírgula por ponto
                      //     $value = str_replace(['R$', '.', ' '], ['', '', ''], $value);
                      //     return (float) str_replace(',', '.', $value);
                      //   }
                      // }
                      // Aqui assumimos que você vai buscar o VlrOpe correspondente de $ResultOutDoc
                      $v = 0; // Valor padrão se não encontrar
                      foreach ($ResultOutDoc as $outDocItem) {
                        $Diferenca = 0;
                        // Supondo que 'TipoServico' é o campo a ser comparado
                        if ($outDocItem['TipoServico'] === $item['TipoServico']) {
                          $v = $outDocItem['VlrVeic'];
                          break; // Saia do loop assim que encontrar
                        }
                      }
                      // echo htmlspecialchars($v);
                      ?>
                      <span style="float: left;">R$</span><?= number_format(($v ?? 0), 2, ',', '.') ?>
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                      <?php
                      // Convertendo os valores
                      $vlrVeic = ($item['VlrVeic']);
                      $valorV = ($v);

                      // Calculando a diferença
                      $diferenca = $vlrVeic - $valorV;
                      ?>
                      <span style="float: left;">R$</span><?= number_format(($diferenca ?? 0), 2, ',', '.') ?>
                    </td>
                    <td style="text-align: center;">
                      <?php
                      // Comparando os valores e exibindo o resultado
                      if (($diferenca > -1) && ($diferenca < 1)) {
                        echo "<strong style='color:blue;'>OK</strong>";
                      } else {
                        echo "<strong style='color:red;'>X</strong>";
                      }
                      ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm h-100">
          <h5 class="card-header bg-primary text-white">Receitas Operacionais - Notas Faturadas</h5>
          <div class="card-body">
            <table id="Comunicacao" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
              <thead>
                <tr class="table-primary">
                  <th>Tipo Serviço</th>
                  <th>Mes/Ano</th>
                  <th>Vlr. Itens F141CIS</th>
                  <th>Vlr. itens F660CIN</th>
                  <th>Vlr. Difer.</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ResultNotaFat as $item) : ?>
                  <tr>
                    <td><?= $item['TipoServico'] ?></td>
                    <td><?= $item['MesAno'] ?></td>
                    <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format(($item['VlrBruto'] ?? 0), 2, ',', '.') ?></td>
                    <td style="text-align: right; white-space: nowrap;">
                      <?php
                      // // Função para converter os valores em formato de moeda para float
                      // if (!function_exists('convertCurrencyToFloat')) {
                      //   function convertCurrencyToFloat($value)
                      //   {
                      //     // Remove o "R$ ", os pontos e substitui a vírgula por ponto
                      //     $value = str_replace(['R$', '.', ' '], ['', '', ''], $value);
                      //     return (float) str_replace(',', '.', $value);
                      //   }
                      // }
                      // Aqui assumimos que você vai buscar o VlrOpe correspondente de $ResultOutDoc
                      $v = 0; // Valor padrão se não encontrar
                      foreach ($ResultadoItensNF as $outDocItem) {
                        $Diferenca = 0;
                        // Supondo que 'TipoServico' é o campo a ser comparado
                        if ($outDocItem['TipoServico'] === $item['TipoServico']) {
                          $v = $outDocItem['Valor'];
                          break; // Saia do loop assim que encontrar
                        }
                      }
                      // echo htmlspecialchars($v);
                      ?>
                      <span style="float: left;">R$</span><?= number_format(($v ?? 0), 2, ',', '.') ?>
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                      <?php
                      // Convertendo os valores
                      $vlrVeic = ($item['VlrBruto']);
                      $valorV = ($v);

                      // Calculando a diferença
                      $diferenca = $vlrVeic - $valorV;
                      ?>
                      <span style="float: left;">R$</span><?= number_format(($diferenca ?? 0), 2, ',', '.') ?>
                    </td>
                    <td style="text-align: center;">
                      <?php
                      // Comparando os valores e exibindo o resultado
                      if (($diferenca >= 0) && ($diferenca < 1)) {
                        echo "<strong style='color:blue;'>OK</strong>";
                      } else {
                        echo "<strong style='color:red;'>X</strong>";
                      }
                      ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- Espaço entre os resultados -->
    <div class="mb-3"></div>

    <!-- Exibindo Notas a Importar -->
    <?php
    // Divide o array em duas partes
    if (!empty($TotalIM)) {
      $metade = (int) ceil($TotalIM / 2);
      $metade = max(1, $metade);
      $partes = array_chunk($ResultadoImportar, $metade);
      $primeiraParte = $partes[0] ?? [];
      $segundaParte = $partes[1] ?? [];
    }
    ?>
    <?php if (!empty($TotalIM)) : ?>
      <div class="row gx-1">
        <!-- Tabela 1 -->
        <div class="col-md-6">
          <div class="card shadow-sm h-100">
            <h5 class="card-header bg-primary text-white">Notas a serem Importadas | Total: <?= $TotalIM ?></h5>
            <div class="card-body">
              <table class="table table-striped table-hover mb-0" style="border:1px solid #ccc;">
                <thead class="gray-background">
                  <tr class="table-primary">
                    <th>Cod.Cli</th>
                    <th>Serie NF</th>
                    <th>Num. Nota</th>
                    <th>Dt. Emissão</th>
                    <th>Trans. Serviço</th>
                    <th>Vlr. Veiculado</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($primeiraParte as $item): ?>
                    <tr>
                      <td><?= $item['CODCLI'] ?></td>
                      <td style="text-align: center;"><?= $item['CODSNF'] ?></td>
                      <td style="text-align: center;"><?= $item['NUMNFV'] ?></td>
                      <td><?= date('d/m/Y', strtotime($item['DATEMI'])) ?></td>
                      <td style="text-align: center;"><?= $item['TNSSER'] ?></td>
                      <td style="text-align: right; white-space: nowrap"><span style="float: left;">R$</span><?= $item['VLRFIN'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Tabela 2 -->
        <div class="col-md-6">
          <div class="card shadow-sm h-100">
            <h5 class="card-header bg-primary text-white">Notas a serem Importadas (cont.)</h5>
            <div class="card-body">
              <table class="table table-striped table-hover mb-0" style="border:1px solid #ccc;">
                <thead class="gray-background">
                  <tr class="table-primary">
                    <th>Cod.Cli</th>
                    <th>Serie NF</th>
                    <th>Num. Nota</th>
                    <th>Dt. Emissão</th>
                    <th>Trans. Serviço</th>
                    <th>Vlr. Veiculado</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($segundaParte as $item): ?>
                    <tr>
                      <td><?= $item['CODCLI'] ?></td>
                      <td style="text-align: center;"><?= $item['CODSNF'] ?></td>
                      <td style="text-align: center;"><?= $item['NUMNFV'] ?></td>
                      <td><?= date('d/m/Y', strtotime($item['DATEMI'])) ?></td>
                      <td style="text-align: center;"><?= $item['TNSSER'] ?></td>
                      <td style="text-align: right; white-space: nowrap"><span style="float: left;">R$</span><?= $item['VLRFIN'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <!-- Espaço entre os resultados -->
    <div class="mb-3"></div>

    <!-- Exibindo Diferença dos itens -->
    <div class="row gx-1">
      <div class="col-md-6">
        <div class="card shadow-sm h-100">
          <h5 class="card-header bg-primary text-white">Dados dos Itens F121CIP | Total: <?= $TotalDI ?></h5>
          <div class="card-body">
            <table id="Diferenca" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
              <thead>
                <tr class="table-primary">
                  <th>Cod.Cli</th>
                  <th>Pedido</th>
                  <th>Seq.</th>
                  <th>Num. Nota</th>
                  <th>Ped. Cliente </th>
                  <th>Dt. Entrega</th>
                  <th>Tipo Serviço</th>
                  <th>Valor</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ResultadoDiferencaItens as $item1): ?>
                  <?php
                  // Verifica se existe um item correspondente em ResultadoDiferencaOutrosDoc
                  $comparacao = 'X'; // Valor padrão, "X" se não houver correspondência
                  foreach ($ResultadoDiferencaOutrosDoc as $item2) {
                    if (
                      $item1['CODCLI'] === $item2['CODCLI'] &&
                      $item1['DATENT'] === $item2['DATOPE'] &&
                      $item1['NUMNFV'] === $item2['NUMNFV'] &&
                      $item1['TipoServico'] === $item2['TipoServico'] &&
                      ($item1['VlrVeiculado'] === $item2['VLROPE'] ||
                        (abs($item1['VlrVeiculado'] - $item2['VLROPE']) < 0.99 && abs($item1['VlrVeiculado'] - $item2['VLROPE']) >= 0) ||
                        (abs($item2['VLROPE'] - $item1['VlrVeiculado']) < 0.99 && abs($item2['VLROPE'] - $item1['VlrVeiculado']) >= 0)
                      )
                    ) {
                      $comparacao = 'OK'; // Se todos os campos forem iguais, exibe "OK"
                      break; // Encerra o loop ao encontrar correspondência
                    }
                  }
                  ?>
                  <?php if ($comparacao === 'X'): ?>
                    <tr>
                      <td style="text-align: center;"><?= $item1['CODCLI'] ?></td>
                      <td style="text-align: center;"><?= $item1['NUMPED'] ?></td>
                      <td style="text-align: center;"><?= $item1['SEQISP'] ?></td>
                      <td style="text-align: center;"><?= $item1['NUMNFV'] ?></td>
                      <td><?= $item1['PEDCLI'] ?></td>
                      <td style="text-align: center;"><?= date('d/m/Y', strtotime($item1['DATENT'])) ?></td>
                      <td><?= $item1['TipoServico'] ?></td>
                      <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($item1['VlrVeiculado'], 2, ',', '.') ?></td>
                      <td style="text-align: center;"><strong style='color:red;'>X</strong></td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm h-100">
          <h5 class="card-header bg-primary text-white">Dados dos Outros Documentos F660BLA | Total: <?= $TotalDO ?></h5>
          <div class="card-body">
            <table id="Diferenca" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
              <thead>
                <tr class="table-primary">
                  <th>Cod.Cli</th>
                  <th>Doc.</th>
                  <th>Seq.</th>
                  <th>Nota</th>
                  <th>Dt. Operação</th>
                  <th>Tipo Serviço</th>
                  <th>Valor</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ResultadoDiferencaOutrosDoc as $item2): ?>
                  <?php
                  // Verifica se existe um item correspondente em ResultadoDiferencaItens
                  $comparacao = 'X'; // Valor padrão, "X" se não houver correspondência
                  foreach ($ResultadoDiferencaItens as $item1) {
                    if (
                      $item2['CODCLI'] === $item1['CODCLI'] &&
                      $item2['DATOPE'] === $item1['DATENT'] &&
                      $item2['NUMNFV'] === $item1['NUMNFV'] &&
                      // $item2['SEQDOC'] === $item1['SEQISP'] && 
                      $item2['TipoServico'] === $item1['TipoServico'] &&
                      // abs($item2['VLROPE'] - $item1['VlrVeiculado']) < 0.99
                      // ( // Aqui verificamos ambas as condições de diferença
                      //   ($item1['VlrVeiculado'] - $item2['VLROPE'] < 0.99 && $item1['VlrVeiculado'] - $item2['VLROPE'] >= 0) ||
                      //   ($item1['VlrVeiculado'] - $item2['VLROPE'] > 0.99 && $item1['VlrVeiculado'] - $item2['VLROPE'] >= 0)
                      // )
                      ($item2['VLROPE'] === $item1['VlrVeiculado'] ||
                        (abs($item2['VLROPE'] - $item1['VlrVeiculado']) < 0.99 && abs($item2['VLROPE'] - $item1['VlrVeiculado']) >= 0) ||
                        (abs($item1['VlrVeiculado'] - $item2['VLROPE']) < 0.99 && abs($item1['VlrVeiculado'] - $item2['VLROPE']) >= 0)
                      )
                    ) {
                      $comparacao = 'OK'; // Se todos os campos forem iguais, exibe "OK"
                      break; // Encerra o loop ao encontrar correspondência
                    }
                  }
                  ?>
                  <?php if ($comparacao === 'X'): ?>
                    <tr>
                      <td style="text-align: center;"><?= $item2['CODCLI'] ?></td>
                      <td style="text-align: center;"><?= $item2['NUMDOC'] ?></td>
                      <td style="text-align: center;"><?= $item2['SEQDOC'] ?></td>
                      <td style="text-align: center;"><?= $item2['NUMNFV'] ?></td>
                      <td style="text-align: center;"><?= date('d/m/Y', strtotime($item2['DATOPE'])) ?></td>
                      <td><?= $item2['TipoServico'] ?></td>
                      <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($item2['VLROPE'], 2, ',', '.') ?></td>
                      <td style="text-align: center;">
                        <?php
                        // Comparando os valores e exibindo o resultado
                        if ($comparacao === 'OK') {
                          echo "<span style='text-align: center;'><strong style='color:blue;'>OK</strong></span>";
                        } else {
                          echo "<span style='text-align: center;'><strong style='color:red;'>X</strong></span>";
                        }
                        ?>
                      </td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif ?>

<!-- JavaScript -->
<script src="../js/cont_recoperacionais.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>