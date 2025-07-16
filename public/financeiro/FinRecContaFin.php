<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinRecContaFin.php';

$Titulo = 'Recebimento - Conta Financeira';
$URL = URL_PRINCIPAL . 'financeiro/FinRecContaFin.php';

// Instanciar a classe
$recebimentoContaFinan = new RecebimentoContaFinan();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;
  $ano = $dados['ano'];
  $codEmp = $dados['codEmp'];

  $consultaRecebimentoContaFinan = $recebimentoContaFinan->consultaFaturamentoRecebido($dados);
  $Total = count($consultaRecebimentoContaFinan);

  // Agrupar os itens por CtaFin:
  $agrupados = [];
  foreach ($consultaRecebimentoContaFinan as $item) {
    $agrupados[$item['ContaFin']][] = $item;
  }

  $totalSoma = array_sum(array_column($consultaRecebimentoContaFinan, 'VLRMOV'));
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
              <strong>Empresa</strong>
            </div>
            <div class="col">
              <strong>Ano</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="codEmp" name="codEmp" placeholder="Conta Reduzida" required>
                <option value="0">-- Selecione Empresa --</option>
                <option value="1">1 - Diário da Região</option>
                <option value="2">2 - FM Diário</option>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="ano" name="ano">
                <option value="0">-- Ano --</option>
                <?php
                $anoAtual = date('Y');
                for ($ano = $anoAtual; $ano >= $anoAtual - 10; $ano--) {
                  $selected = ($ano == $anoSelecionado) ? 'selected' : '';
                  echo "<option value=\"$ano\" $selected>$ano</option>";
                }
                ?>
              </select>
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
  <div class="container d-flex;">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          <?php if ($codEmp == 1) : ?>
            Faturamento Diário da Região - <?= $ano ?>
          <?php elseif ($codEmp == 2) : ?>
            Faturamento FM Diário - <?= $ano ?>
          <?php endif; ?>
        </h5>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th>Conta Financeira</th>
              <?php for ($i = 1; $i <= 12; $i++) : ?>
                <th><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>/<?= $ano ?></th>
              <?php endfor; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($agrupados as $ctaFin => $itens) : ?>
              <tr class="clickable-row" data-target="#empresa1-details-<?= $ctaFin ?>">
                <th><?= $ctaFin ?></th>
                <?php
                $meses = array_fill(1, 12, 0);
                foreach ($itens as $item) {
                  $mesAno = explode('/', $item['MesAno']);
                  $mes = (int)$mesAno[0];
                  $meses[$mes] += $item['VLRMOV'];
                }
                foreach ($meses as $mes => $total) {
                  echo "<td style='text-align: right; white-space: nowrap;'><span style='float: left;'>R$ </span>" . number_format($total, 2, ',', '.') . "</td>";
                }
                ?>
              </tr>
              <tr id="empresa1-details-<?= $ctaFin ?>" class="collapse">
                <td colspan="13">
                  <h6><strong> Sem Permutas </strong></h6>
                  <table class="table table-striped full-width-table" style="border: solid 1px;">
                    <thead>
                      <tr class="table-primary">
                        <th>Descrição Conta Financeira</th>
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                          <th><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>/<?= $ano ?></th>
                        <?php endfor; ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $detalhados = [];
                      foreach ($itens as $item) {
                        $detalhados[$item['DESCTA']][] = $item;
                      }
                      foreach ($detalhados as $contaFin => $detalheitens) : ?>
                        <tr class="clickable-row" data-target="#empresa1-details-<?= $ctaFin ?>-<?= $contaFin ?>">
                          <th>
                            <?= htmlspecialchars($contaFin) ?>
                          </th>
                          <?php
                          $mesesDetalhados = array_fill(1, 12, 0);
                          foreach ($detalheitens as $detalheItem) {
                            if ($detalheItem['CODFPG'] != 13) {
                              $mesAno = explode('/', $detalheItem['MesAno']);
                              $mes = (int)$mesAno[0];
                              $mesesDetalhados[$mes] += $detalheItem['VLRMOV'];
                            }
                          }
                          foreach ($mesesDetalhados as $mes => $total) {
                            echo "<td style='text-align: right; white-space: nowrap;'><span style='float: left;'>R$ </span>" . number_format($total, 2, ',', '.') . "</td>";
                          }
                          ?>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                    <tbody></tbody>
                  </table>
                  <?php $hasPermutas = false;
                  foreach ($itens as $item) {
                    if ($item['CODFPG'] == 13) {
                      $hasPermutas = true;
                      break;
                    }
                  }
                  if ($hasPermutas) : ?>
                    <h6><strong> Permutas </strong></h6>
                    <table class="table table-striped full-width-table" style="border: solid 1px;">
                      <thead>
                        <tr class="table-primary">
                          <th>Descrição Conta Financeira</th>
                          <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <th><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>/<?= $ano ?></th>
                          <?php endfor; ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $detalhados = [];
                        foreach ($itens as $item) {
                          $detalhados[$item['DESCTA']][] = $item;
                        }
                        foreach ($detalhados as $contaFin => $detalheitens) : ?>
                          <tr class="clickable-row" data-target="#details-<?= $ctaFin ?>-<?= $contaFin ?>">
                            <th>
                              <?= htmlspecialchars($contaFin) ?>
                            </th>
                            <?php
                            $mesesDetalhados = array_fill(1, 12, 0);
                            foreach ($detalheitens as $detalheItem) {
                              if ($detalheItem['CODFPG'] == 13) {
                                $mesAno = explode('/', $detalheItem['MesAno']);
                                $mes = (int)$mesAno[0];
                                $mesesDetalhados[$mes] += $detalheItem['VLRMOV'];
                              }
                            }
                            foreach ($mesesDetalhados as $mes => $total) {
                              echo "<td style='text-align: right; white-space: nowrap;'><span style='float: left;'>R$ </span>" . number_format($total, 2, ',', '.') . "</td>";
                            }
                            ?>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      <tbody></tbody>
                    </table>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr class="table-primary">
              <th>Total Geral</th>
              <?php
              $totalMeses = array_fill(1, 12, 0);
              foreach ($agrupados as $itens) {
                foreach ($itens as $item) {
                  $mesAno = explode('/', $item['MesAno']);
                  $mes = (int)$mesAno[0];
                  $totalMeses[$mes] += $item['VLRMOV'];
                }
              }
              ?>
              <?php foreach ($totalMeses as $total) : ?>
                <th style='text-align: right; white-space: nowrap;'><span style='float: left;'>R$ </span><?= number_format($total, 2, ',', '.') ?></th>
              <?php endforeach; ?>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/fin_reccontafin.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>