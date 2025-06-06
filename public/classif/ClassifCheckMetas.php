<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ClassifCheckMetas.php';

$Titulo = 'Check Metas Classificados';
$URL = URL_PRINCIPAL . 'classif/ClassifCheckMetas.php';

// Instanciar a classe
$ClassifCheckMetas = new ClassifCheckMetas();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $Ano = $_POST['Ano'];
  $MesAno = $_POST['MesAno'];

  if (empty($MesAno)) {
    $MesAno = date('m/Y'); // Pega o mês e ano atual do sistema no formato MM/YYYY
  }
  if (empty($Ano)) {
    $Ano = date('Y'); // Pega o ano atual do sistema
  }
  // Verifica se o campo de data está vazio
  $SomaAnual = $ClassifCheckMetas->ConsultaAno($Ano);
  $Qtde = COUNT($SomaAnual);
  $SomaDia = $ClassifCheckMetas->ConsultaDia($MesAno);
} else if (isset($_POST['btn-analitico'])) {
  $MesAno = $_POST['MesAno'];

  // Separa mês e ano – use explode em vez de str_split para evitar problemas
  list($Mes, $Ano) = explode('/', $_POST['MesAno']);
  $Mes = (int)$Mes;
  $Ano = (int)$Ano;

  // Verifica se o campo de data está vazio
  $Analitico = $ClassifCheckMetas->ConsultaDia($MesAno);
  // $Analitico = $ClassifCheckMetas->ConsultaAno($Ano);
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Ano</strong>
            </div>
            <div class="col">
              <strong>Mes/Ano</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="Ano" name="Ano">
                <option value="0">-- Ano --</option>
                <?php
                $AnoAtual = date('Y');
                for ($ANO = $AnoAtual; $ANO >= $AnoAtual - 10; $ANO--) {
                  $selected = ($ANO == $AnoSelecionado) ? 'selected' : '';
                  echo "<option value=\"$ANO\" $selected>$ANO</option>";
                }
                ?>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
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

<!-- Resultado Analítico -->
<?php if (isset($Analitico)) : ?>
  <?php
  // Agrupando os registro por VendContrato
  $Grupos = [];
  foreach ($Analitico as $key) {
    $Grupos[$key['VendContrato']][] = $key;
  }
  ?>
  <div class="container">
    <?php // Percorre cada grupo, imprimir a linha-resumo e depois os detalhes ocultos
    foreach ($Grupos as $VendContrato => $itens) :
      // Calcula a quantidade e a soma do VlrPub
      $Qtde = COUNT($itens);
      $Soma = 0;
      foreach ($itens as $it) {
        // Limpa formatação se houver "1.234,56" → "1234.56"
        $raw = $it['VlrPub'];
        $Soma += (float)$raw;
      }
      $Soma = number_format($Soma, 2, ',', '.');
    ?>
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-header bg-primary text-white toggle-summary" style="cursor:pointer;">
            <strong><?= htmlspecialchars(($VendContrato)) ?></strong> ||
            Qtde: <?= $Qtde ?> || R$ <?= $Soma ?>
          </h5>
          <table class="table table-striped table-hover toggle-details" style="display:none;">
            <thead>
              <tr>
                <th>Cod. Anuncio</th>
                <th>Nome Cliente</th>
                <th>Data Captação</th>
                <th>Data Publicação</th>
                <th>Mes Publicação</th>
                <th>Origem</th>
                <th>Tipo Cob.</th>
                <th>Vendedor</th>
                <th>Valor</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($itens as $item): ?>
                <tr>
                  <td><?= intval($item['CodAnuncio']) ?></td>
                  <td><?= htmlspecialchars(mb_strimwidth(trim($item['Cliente']), 0, 45, '...')) ?></td>
                  <td><?= date('d/m/Y', strtotime($item['DtCapitacao'])) ?></td>
                  <td><?= date('d/m/Y', strtotime($item['DtPublicacao'])) ?></td>
                  <td><?= $item['MesPub'] ?></td>
                  <td><?= htmlspecialchars(mb_strimwidth(trim($item['Origem']), 0, 15, '...')) ?></td>
                  <td><?= htmlspecialchars(mb_strimwidth(trim($item['Cobranca']), 0, 22, '...')) ?></td>
                  <td><?= htmlspecialchars($item['VendContrato']) ?></td>
                  <td style="text-align: right; white-space: nowrap;">
                    <span style="float: left; display: inline-block;">R$ </span><?= number_format((float)str_replace(',', '.', ($item['VlrPub'] ?? 0)), 2, ',', '.') ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-3"></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Resultado da Consulta Anual -->
<?php if (isset($SomaAnual)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <?php
      $agrupado = [];
      foreach ($SomaAnual as $linha) {
        $agrupado[$linha['VendContrato']][] = $linha;
      }
      $SomaTotal = array_sum(array_column($SomaAnual, 'VlrPub'));
      ?>
      <div class="card-body">
        <h5 class="card-header bg-primary text-white"> Soma Mensal</h5>
        <table class="table table-striped table-hover">
          <thead>
            <tr class="table-primary">
              <th>Vendedor</th>
              <?php for ($i = 1; $i <= 12; $i++) : ?>
                <th style="text-align:center"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>/<?= $Ano ?></th>
              <?php endfor; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($agrupado as $vendedor => $item): ?>
              <tr>
                <th><?= $vendedor ?></th>
                <?php
                $meses = array_fill(1, 12, 0);
                foreach ($item as $linha) {
                  $MesPub = explode('/', $linha['MesPub']);
                  $mes = (int)$MesPub[0];
                  $meses[$mes] += floatval($linha['VlrPub']);
                }
                ?>
                <?php foreach ($meses as $mes): ?>
                  <td style="text-align:right"><span style='float: left;'>R$ </span><?= number_format($mes, 2, ',', '.') ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
            <?php
            // --- CÁLCULO DOS TOTAIS DE CADA MÊS ---
            // Inicializa um vetor com 12 posições (1 a 12) zeradas
            $totaisMes = array_fill(1, 12, 0);

            // Percorre todas as linhas da consulta e acumula por mês
            foreach ($SomaAnual as $linha) {
              // Extrai o mês (antes da “/”) e converte em inteiro
              $mes = (int) explode('/', $linha['MesPub'])[0];
              $totaisMes[$mes] += floatval($linha['VlrPub']);
            }
            ?>
            <!-- LINHA DE TOTAL GERAL -->
            <tr class="table-primary">
              <th>Total Geral</th>
              <?php foreach ($totaisMes as $valor): ?>
                <td style="text-align:right">
                  <span style="float:left">R$ </span><?= number_format($valor, 2, ',', '.') ?>
                </td>
              <?php endforeach; ?>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="mb-3"></div>
  </div>
<?php endif; ?>

<!-- Resultado da Consulta Mes Dia a Dia Por Data de Captacao-->
<?php if (isset($SomaDia)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <?php
      // Supondo que $MesAno venha do POST no formato "MM/YYYY"
      list($mesSel, $anoSel) = explode('/', $MesAno);

      // Agrupa por vendedor apenas as linhas do mês/ano selecionado
      $agrupado = [];
      foreach ($SomaDia as $linha) {
        $dt = strtotime($linha['DtCapitacao']);
        if (date('m/Y', $dt) !== sprintf('%02d/%s', $mesSel, $anoSel)) {
          continue;
        }
        $agrupado[$linha['VendContrato']][] = $linha;
      }

      // Define os intervalos de dias
      $ultimoDia = cal_days_in_month(CAL_GREGORIAN, $mesSel, $anoSel);
      $intervalos = [
        ['start' => 1, 'end' => 15],
        ['start' => 16, 'end' => $ultimoDia]
      ];
      ?>
      <div class="card-body">
        <!-- Linha de totais gerais -->
        <?php
        // Soma geral de todos os dias do mês selecionado
        $somaGeral = 0;
        foreach ($SomaDia as $ln) {
          $dt = strtotime($ln['DtCapitacao']);
          if (date('m/Y', $dt) === sprintf('%02d/%s', $mesSel, $anoSel)) {
            $somaGeral += floatval($ln['VlrPub']);
          }
        }
        $somaGeralFormatada = number_format($somaGeral, 2, ',', '.');
        ?>
        <h5 class="card-header bg-primary text-white text-center">Mês Referencia: <?= $mesSel ?>/<?= $anoSel ?> (Por Dt. Captação )-> Total: R$ <?= $somaGeralFormatada ?></h5>
        <div class="mb-3"></div>
        <!-- Para cada intervalo, monta uma tabela -->
        <?php foreach ($intervalos as $intv): ?>
          <h5 class="card-header bg-primary text-white">
            Dias <?= str_pad($intv['start'], 2, '0', STR_PAD_LEFT) ?> à
            <?= str_pad($intv['end'],   2, '0', STR_PAD_LEFT) ?>
          </h5>
          <table class="table table-bordered table-striped">
            <thead class="table-secondary">
              <tr>
                <th>Vendedor</th>
                <?php for ($d = $intv['start']; $d <= $intv['end']; $d++): ?>
                  <th class="text-center">
                    <?= str_pad($d, 2, '0', STR_PAD_LEFT) ?>
                  </th>
                <?php endfor; ?>
              </tr>
            </thead>
            <tbody>
              <?php
              // Laço por vendedor
              foreach ($agrupado as $vendedor => $linhas):
                // Inicializa soma por dia
                $somasDia = array_fill($intv['start'], $intv['end'] - $intv['start'] + 1, 0);
                // Acumula valores
                foreach ($linhas as $ln) {
                  $dt   = strtotime($ln['DtCapitacao']);
                  $dia  = (int) date('j', $dt);
                  if ($dia >= $intv['start'] && $dia <= $intv['end']) {
                    $somasDia[$dia] += floatval($ln['VlrPub']);
                  }
                }
              ?>
                <tr>
                  <th><?= $vendedor ?></th>
                  <?php for ($d = $intv['start']; $d <= $intv['end']; $d++):
                    $valor = number_format($somasDia[$d], 2, ',', '.');
                  ?>
                    <td style="text-align:right">
                      <span style="float:left">R$ </span><?= $valor ?>
                    </td>
                  <?php endfor; ?>
                </tr>
              <?php endforeach; ?>

              <!-- Linha de totais por dia -->
              <tr class="table-primary">
                <th>Total</th>
                <?php
                // Inicializa totais do intervalo
                $totaisDia = array_fill($intv['start'], $intv['end'] - $intv['start'] + 1, 0);
                // Acumula sobre todas as linhas filtradas
                foreach ($SomaDia as $ln) {
                  $dt  = strtotime($ln['DtCapitacao']);
                  if (date('m/Y', $dt) !== sprintf('%02d/%s', $mesSel, $anoSel)) {
                    continue;
                  }
                  $dia = (int) date('j', $dt);
                  if ($dia >= $intv['start'] && $dia <= $intv['end']) {
                    $totaisDia[$dia] += floatval($ln['VlrPub']);
                  }
                }
                // Exibe
                for ($d = $intv['start']; $d <= $intv['end']; $d++):
                  $val = number_format($totaisDia[$d], 2, ',', '.');
                ?>
                  <td style="text-align:right">
                    <span style="float:left">R$ </span><?= $val ?>
                  </td>
                <?php endfor; ?>
              </tr>
            </tbody>
          </table>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>