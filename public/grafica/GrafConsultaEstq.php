<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/GrafConsultaEstq.php';

$Titulo = 'Consulta Qtde. Estoque';
$URL = URL_PRINCIPAL . 'grafica/GrafConsultaEstq.php';

// Instanciar a classe
$GrafConstEstq = new GraficaConsultaEstoque();

$consultaDeposito = $GrafConstEstq->consultaDeposito();
// Verifica se a requisição é AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getFamilia') {
  $codDep = $_GET['CODDEP'];
  $Familia = $GrafConstEstq->consultaFamilia($codDep);

  header('Content-Type: application/json');
  echo json_encode($Familia);
  exit; // Para não continuar executando o resto do script
}

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;
  //Gerando os 3 ultimos meses
  list($mes, $ano) = explode('/', $dados['mesAno']);
  $data = DateTime::createFromFormat('Y-m-d', $ano . '-' . $mes . '-01');
  $dados['mesAno3'] = $data->format('m/Y');
  $data->modify('-1 month');
  $dados['mesAno2'] = $data->format('m/Y');
  $data->modify('-1 month');
  $dados['mesAno1'] = $data->format('m/Y');

  $consultaEstoque = $GrafConstEstq->consultaEstoque($dados);
  $Total = count($consultaEstoque);

  if ($Total > 0) {
    $mesAno1 = $dados['mesAno1'];
    $mesAno2 = $dados['mesAno2'];
    $mesAno3 = $dados['mesAno3'];
    $dadosAgrupados = [];
    foreach ($consultaEstoque as $item) {
      $CodFam = $item['CODFAM'];
      if (!isset($dadosAgrupados[$CodFam])) {
        $dadosAgrupados[$CodFam] = [];
      }
      $dadosAgrupados[$CodFam][] = $item;
    }

    // Processa os totais por produto
    $resultado = [];
    // Para cada família, acumula os totais dos produtos e exibe a sua tabela
    foreach ($dadosAgrupados as $CodFam => $itensFamilia) {

      // Cria um array para acumular os totais de cada produto (cada linha única para o produto)
      $produtosTotais = [];
      foreach ($itensFamilia as $item) {

        $CodPro  = $item['CODPRO'];
        $DESPRO  = $item['DESPRO'];
        $MESMOV  = $item['MESMOV'];
        $QTDMOV  = $item['QTDMOV'];
        $ESTEOS  = $item['ESTEOS'];  // "E" para entrada e "S" para saída
        $QtdEst  = $item['QTDEST'];
        $QtdAnt  = $item['QTDANT'];
        
        // Se o produto ainda não foi incluído, cria o registro com totais zerados para cada mês
        if (!isset($produtosTotais[$CodPro])) {
          $produtosTotais[$CodPro] = [
            'DESPRO'  => $DESPRO,
            'mesAno1' => ['QtdEst' => null, 'QtdAnt' => null, 'E' => 0, 'S' => 0],
            'mesAno2' => ['QtdEst' => null, 'QtdAnt' => null, 'E' => 0, 'S' => 0],
            'mesAno3' => ['QtdEst' => null, 'QtdAnt' => null, 'E' => 0, 'S' => 0],
          ];
        }

        // Verifica a qual dos meses o movimento pertence e acumula o total
        if ($MESMOV === $mesAno1) {
          if ($produtosTotais[$CodPro]['mesAno1']['QtdEst'] === null) {
            $produtosTotais[$CodPro]['mesAno1']['QtdEst'] = $QtdEst;
          }
          if ($produtosTotais[$CodPro]['mesAno1']['QtdAnt'] === null) {
            $produtosTotais[$CodPro]['mesAno1']['QtdAnt'] = $QtdAnt;
          }
          if ($ESTEOS === 'E') {
            $produtosTotais[$CodPro]['mesAno1']['E'] += $QTDMOV;
          } elseif ($ESTEOS === 'S') {
            $produtosTotais[$CodPro]['mesAno1']['S'] += $QTDMOV;
          }
        } elseif ($MESMOV === $mesAno2) {
          if ($produtosTotais[$CodPro]['mesAno2']['QtdEst'] === null) {
            $produtosTotais[$CodPro]['mesAno2']['QtdEst'] = $QtdEst;
          }
          if ($produtosTotais[$CodPro]['mesAno2']['QtdAnt'] === null) {
            $produtosTotais[$CodPro]['mesAno2']['QtdAnt'] = $QtdAnt;
          }
          if ($ESTEOS === 'E') {
            $produtosTotais[$CodPro]['mesAno2']['E'] += $QTDMOV;
          } elseif ($ESTEOS === 'S') {
            $produtosTotais[$CodPro]['mesAno2']['S'] += $QTDMOV;
          }
        } elseif ($MESMOV === $mesAno3) {
          if ($produtosTotais[$CodPro]['mesAno3']['QtdEst'] === null) {
            $produtosTotais[$CodPro]['mesAno3']['QtdEst'] = $QtdEst;
          }
          if ($produtosTotais[$CodPro]['mesAno3']['QtdAnt'] === null) {
            $produtosTotais[$CodPro]['mesAno3']['QtdAnt'] = $QtdAnt;
          }
          if ($ESTEOS === 'E') {
            $produtosTotais[$CodPro]['mesAno3']['E'] += $QTDMOV;
          } elseif ($ESTEOS === 'S') {
            $produtosTotais[$CodPro]['mesAno3']['S'] += $QTDMOV;
          }
        }
      }

      $resultado[$CodFam] = [
        'descricao' => $itensFamilia[0]['DESFAM'],
        'produtos' => $produtosTotais,
      ];
    }
  }
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
              <strong>Depósito</strong>
            </div>
            <div class="col">
              <strong>Cod. Família</strong>
            </div>
            <div class="col">
              <strong>Mes / Ano</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="Deposito" name="Deposito" onchange="getFamiliaDeposito(this.value)">
                <option value="0">Todos</option>
                <?php foreach ($consultaDeposito as $key => $item): ?>
                  <option value="<?= $item['CODDEP'] ?>"><?= $item['CODDEP'] . ' - ' . $item['DESDEP'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="Familia" name="Familia">
                <option value="0">Todos</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="mesAno" name="mesAno" placeholder="MM/YYYY">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-imprimir" name="btn-imprimir" type="submit" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
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
        <?php foreach ($resultado as $CodFam => $dados): ?>
          <table class="table table-striped table-hover mb-0 resultado" id="Resultado" name="Resultado" style="border: 1px solid #ccc; border-collapse: collapse; width:100%;">
            <thead>
              <tr class="table-primary">
                <th scope="col" colspan="2" style="text-align: center; text-transform: uppercase;"><?= $dados['descricao'] ?></th>
                <th scope="col" colspan="4" style="text-align: center;"><?= $mesAno1 ?></th>
                <th scope="col" colspan="4" style="text-align: center;"><?= $mesAno2 ?></th>
                <th scope="col" colspan="4" style="text-align: center;"><?= $mesAno3 ?></th>
              </tr>
              <tr class="table-primary">
                <th scope="col">Cod. Produto</th>
                <th scope="col">Descrição Produto</th>
                <?php for ($i = 0; $i < 3; $i++): ?>
                  <th style="text-align: center;">Qtde Inicial</th>
                  <th style="text-align: center;">Qtde Entrada</th>
                  <th style="text-align: center;">Qtde Saída</th>
                  <th style="text-align: center;">Qtde Final</th>
                <?php endfor; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dados['produtos'] as $CodPro => $prod) : ?>
                <tr>
                  <td><?= $CodPro ?></td>
                  <td><?= $prod['DESPRO'] ?></td>
                  <!-- Para mesAno1 -->
                  <?php foreach (['mesAno1', 'mesAno2', 'mesAno3'] as $mes): ?>
                    <td style="border-left: 1px solid #000; text-align:right;">
                      <?= number_format($prod[$mes]['QtdAnt'], 3, ',', '.') ?>
                    </td>
                    <td style="text-align: right; color: blue;">
                      <?= number_format($prod[$mes]['E'], 3, ',', '.') ?>
                    </td>
                    <td style="text-align: right; color: red;">
                      <?= number_format($prod[$mes]['S'], 3, ',', '.') ?>
                    </td>
                    <td style="text-align:right;">
                      <?= number_format(($prod[$mes]['QtdAnt'] + $prod[$mes]['E'] - $prod[$mes]['S']), 3, ',', '.') ?>
                    </td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div class="mb-3"></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/graf_consultaestq.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>