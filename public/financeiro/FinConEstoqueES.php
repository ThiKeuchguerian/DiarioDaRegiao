<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/FinConEstoque.php';

$Titulo = 'Consulta Estoque - (Entrada / Saída)';
$URL = URL_PRINCIPAL . 'financeiro/FinConEstoqueES.php';

// Instanciar a classe
$ConsultaEstoque = new ConsultaEstoque();

$DadosDeposito = $ConsultaEstoque->listarDepositos();
// Verifica se a requisição é AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getFamilia') {
  header('Content-Type: application/json; charset=utf-8');
  $codDep  = $_GET['CODDEP'];
  $Familia = $ConsultaEstoque->listarFamlia($codDep);
  echo json_encode($Familia);
  exit;
}

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $mesAno = $dados['MesAno'];
  // Separa o mês e o ano
  list($mes, $ano) = explode('/', $mesAno);

  // Cria um objeto DateTime a partir do primeiro dia do mês
  $data = DateTime::createFromFormat('Y-m-d', $ano . '-' . $mes . '-01');

  // O primeiro mês é o próprio mês informado
  $mesAno2 = $data->format('m/Y');

  // Subtrai um mês para calcular o mês anterior
  $data->modify('-1 month');
  $mesAno1 = $data->format('m/Y');

  $constultaEstoqueES = $ConsultaEstoque->consultaEstoqueES($dados);
  $total = COUNT($constultaEstoqueES);

  // Primeiro, agrupe os resultados por família.
  $dadosAgrupados = [];
  foreach ($constultaEstoqueES as $item) {
    $CodFam = $item['CODFAM'];
    if (!isset($dadosAgrupados[$CodFam])) {
      $dadosAgrupados[$CodFam] = [];
    }
    $dadosAgrupados[$CodFam][] = $item;
  }

  // Para cada família, processe os itens e acumule os totais.
  foreach ($dadosAgrupados as $CodFam => $itensFamilia) {

    // Cria um array para acumular os totais de cada produto.
    // Agora, para cada mês, vamos armazenar tanto a quantidade quanto o valor.
    // A estrutura ficará assim:
    // 'MesAnoX' => [
    //     'E' => ['qtd' => total, 'vlr' => total],
    //     'S' => ['qtd' => total, 'vlr' => total]
    // ]
    $produtosTotais = [];
    foreach ($itensFamilia as $item) {

      $CodPro  = $item['CODPRO'];
      $DESPRO  = $item['DESPRO'];
      $MESMOV  = $item['MESMOV'];
      $QTDMOV  = $item['QTDMOV'];
      $VLRMOV  = $item['VLRMOV'];
      $ESTEOS  = $item['ESTEOS'];  // "E" para entrada e "S" para saída
      $QtdEst  = $item['QTDEST'];
      $QtdAnt  = $item['QTDANT'];

      // Se o produto ainda não foi incluído, cria o registro com totais zerados.
      if (!isset($produtosTotais[$CodPro])) {
        $produtosTotais[$CodPro] = [
          'DESPRO'  => $DESPRO,
          'MesAno1' => [
            'QtdEst' => null,
            'QtdAnt' => null,
            'E' => ['qtd' => 0, 'vlr' => 0],
            'S' => ['qtd' => 0, 'vlr' => 0]
          ],
          'MesAno2' => [
            'QtdEst' => null,
            'QtdAnt' => null,
            'E' => ['qtd' => 0, 'vlr' => 0],
            'S' => ['qtd' => 0, 'vlr' => 0]
          ],
          'MesAno3' => [
            'QtdEst' => null,
            'QtdAnt' => null,
            'E' => ['qtd' => 0, 'vlr' => 0],
            'S' => ['qtd' => 0, 'vlr' => 0]
          ],
        ];
      }

      // Verifica a qual mês o movimento pertence e acumula os totais conforme o tipo de movimento.
      if ($MESMOV === $mesAno1) {
        if ($produtosTotais[$CodPro]['MesAno1']['QtdEst'] === null) {
          $produtosTotais[$CodPro]['MesAno1']['QtdEst'] = $QtdEst;
        }
        if ($produtosTotais[$CodPro]['MesAno1']['QtdAnt'] === null) {
          $produtosTotais[$CodPro]['MesAno1']['QtdAnt'] = $QtdAnt;
        }
        if ($ESTEOS === 'E') {
          $produtosTotais[$CodPro]['MesAno1']['E']['qtd'] += $QTDMOV;
          $produtosTotais[$CodPro]['MesAno1']['E']['vlr'] += $VLRMOV;
        } elseif ($ESTEOS === 'S') {
          $produtosTotais[$CodPro]['MesAno1']['S']['qtd'] += $QTDMOV;
          $produtosTotais[$CodPro]['MesAno1']['S']['vlr'] += $VLRMOV;
        }
      } elseif ($MESMOV === $mesAno2) {
        if ($produtosTotais[$CodPro]['MesAno2']['QtdEst'] === null) {
          $produtosTotais[$CodPro]['MesAno2']['QtdEst'] = $QtdEst;
        }
        if ($produtosTotais[$CodPro]['MesAno2']['QtdAnt'] === null) {
          $produtosTotais[$CodPro]['MesAno2']['QtdAnt'] = $QtdAnt;
        }
        if ($ESTEOS === 'E') {
          $produtosTotais[$CodPro]['MesAno2']['E']['qtd'] += $QTDMOV;
          $produtosTotais[$CodPro]['MesAno2']['E']['vlr'] += $VLRMOV;
        } elseif ($ESTEOS === 'S') {
          $produtosTotais[$CodPro]['MesAno2']['S']['qtd'] += $QTDMOV;
          $produtosTotais[$CodPro]['MesAno2']['S']['vlr'] += $VLRMOV;
        }
      } elseif ($MESMOV === $MesAno3) {
        if ($produtosTotais[$CodPro]['MesAno3']['QtdEst'] === null) {
          $produtosTotais[$CodPro]['MesAno3']['QtdEst'] = $QtdEst;
        }
        if ($produtosTotais[$CodPro]['MesAno3']['QtdAnt'] === null) {
          $produtosTotais[$CodPro]['MesAno3']['QtdAnt'] = $QtdAnt;
        }
        if ($ESTEOS === 'E') {
          $produtosTotais[$CodPro]['MesAno3']['E']['qtd'] += $QTDMOV;
          $produtosTotais[$CodPro]['MesAno3']['E']['vlr'] += $VLRMOV;
        } elseif ($ESTEOS === 'S') {
          $produtosTotais[$CodPro]['MesAno3']['S']['qtd'] += $QTDMOV;
          $produtosTotais[$CodPro]['MesAno3']['S']['vlr'] += $VLRMOV;
        }
      }
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
              <strong>Deposito</strong>
            </div>
            <div class="col">
              <strong>Código Família</strong>
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
                <?php foreach ($DadosDeposito as $key => $item): ?>
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
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-imprimir" name="btn-imprimir" type="submit" class="btn btn-primary btn-sm">Imprimir</button>
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
<?php if (!empty($total)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <?php foreach ($dadosAgrupados as $CodFam => $itensFamilia) : ?>
      <?php
      // Aqui dentro você faz o mesmo cálculo que já fazia para $produtosTotais
      $produtosTotais = [];
      foreach ($itensFamilia as $item) {
        $CodPro = $item['CODPRO'];
        $DESPRO = $item['DESPRO'];
        $MESMOV = $item['MESMOV'];
        $QTDMOV = $item['QTDMOV'];
        $VLRMOV = $item['VLRMOV'];
        $ESTEOS = $item['ESTEOS'];
        $QtdEst = $item['QTDEST'];
        $QtdAnt = $item['QTDANT'];

        if (!isset($produtosTotais[$CodPro])) {
          $produtosTotais[$CodPro] = [
            'DESPRO' => $DESPRO,
            'MesAno1' => ['QtdEst' => null, 'QtdAnt' => null, 'E' => ['qtd' => 0, 'vlr' => 0], 'S' => ['qtd' => 0, 'vlr' => 0]],
            'MesAno2' => ['QtdEst' => null, 'QtdAnt' => null, 'E' => ['qtd' => 0, 'vlr' => 0], 'S' => ['qtd' => 0, 'vlr' => 0]],
          ];
        }

        if ($MESMOV === $mesAno1) {
          if ($produtosTotais[$CodPro]['MesAno1']['QtdEst'] === null) $produtosTotais[$CodPro]['MesAno1']['QtdEst'] = $QtdEst;
          if ($produtosTotais[$CodPro]['MesAno1']['QtdAnt'] === null) $produtosTotais[$CodPro]['MesAno1']['QtdAnt'] = $QtdAnt;
          $produtosTotais[$CodPro]['MesAno1'][$ESTEOS]['qtd'] += $QTDMOV;
          $produtosTotais[$CodPro]['MesAno1'][$ESTEOS]['vlr'] += $VLRMOV;
        } elseif ($MESMOV === $mesAno2) {
          if ($produtosTotais[$CodPro]['MesAno2']['QtdEst'] === null) $produtosTotais[$CodPro]['MesAno2']['QtdEst'] = $QtdEst;
          if ($produtosTotais[$CodPro]['MesAno2']['QtdAnt'] === null) $produtosTotais[$CodPro]['MesAno2']['QtdAnt'] = $QtdAnt;
          $produtosTotais[$CodPro]['MesAno2'][$ESTEOS]['qtd'] += $QTDMOV;
          $produtosTotais[$CodPro]['MesAno2'][$ESTEOS]['vlr'] += $VLRMOV;
        }
      }
      ?>
      <div class="card-body">
        <table class="table table-striped table-hover mb-0" style="border: 1px solid #ccc; border-collapse: collapse;">
          <thead>
            <tr class="table-primary">
              <th scope="col" colspan="2" style="text-align: center; text-transform: uppercase;"><?= $itensFamilia[0]['DESFAM'] ?></th>
              <th scope="col" colspan="6" style="text-align: center;"><?= $mesAno1 ?></th>
              <th scope="col" colspan="6" style="text-align: center;"><?= $mesAno2 ?></th>
            </tr>
            <tr>
              <th scope="col">Cod. Produto</th>
              <th scope="col">Descrição Produto</th>
              <!-- Cabeçalho para o MesAno1 -->
              <th scope="col" style="text-align: center;">Qtde Inicial</th>
              <th scope="col" style="text-align: center;">Qtde Entrada</th>
              <th scope="col" style="text-align: center;">Valor Entrada</th>
              <th scope="col" style="text-align: center;">Qtde Saída</th>
              <th scope="col" style="text-align: center;">Valor Saída</th>
              <th scope="col" style="text-align: center;">Qtde Final</th>
              <!-- Cabeçalho para o MesAno2 -->
              <th scope="col" style="text-align: center;">Qtde Inicial</th>
              <th scope="col" style="text-align: center;">Qtde Entrada</th>
              <th scope="col" style="text-align: center;">Valor Entrada</th>
              <th scope="col" style="text-align: center;">Qtde Saída</th>
              <th scope="col" style="text-align: center;">Valor Saída</th>
              <th scope="col" style="text-align: center;">Qtde Final</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($produtosTotais  as $CodPro => $prod): ?>
              <tr>
                <td><?= $CodPro ?></td>
                <td><?= $prod['DESPRO'] ?></td>
                <!-- Para MesAno1 -->
                <td style="border-left: 1px solid #000; text-align:center;"><?= number_format($prod['MesAno1']['QtdAnt'], 3, ',', '.') ?></td>
                <td style="text-align: right;"><?= number_format($prod['MesAno1']['E']['qtd'], 3, ',', '.') ?></td>
                <td style="text-align: right; color: blue; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($prod['MesAno1']['E']['vlr'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><?= number_format($prod['MesAno1']['S']['qtd'], 3, ',', '.') ?></td>
                <td style="text-align: right; color: red; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($prod['MesAno1']['S']['vlr'], 2, ',', '.') ?></td>
                <td style="text-align:center;"><?= number_format(($prod['MesAno1']['QtdAnt'] + $prod['MesAno1']['E']['qtd'] - $prod['MesAno1']['S']['qtd']), 3, ',', '.') ?></td>
                <!-- Para MesAno2 -->
                <td style="border-left: 1px solid #000; text-align:center;"><?= number_format($prod['MesAno2']['QtdAnt'], 3, ',', '.') ?></td>
                <td style="text-align: right;"><?= number_format($prod['MesAno2']['E']['qtd'], 3, ',', '.') ?></td>
                <td style="text-align: right; color: blue; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($prod['MesAno2']['E']['vlr'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><?= number_format($prod['MesAno2']['S']['qtd'], 3, ',', '.') ?></td>
                <td style="text-align: right; color: red; white-space: nowrap;"><span style="float: left;">R$</span><?= number_format($prod['MesAno2']['S']['vlr'], 2, ',', '.') ?></td>
                <td style="text-align:center;"><?= number_format(($prod['MesAno2']['QtdAnt'] + $prod['MesAno2']['E']['qtd'] - $prod['MesAno2']['S']['qtd']), 3, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>




<!-- Incluindo Java Script -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/fin_consultaestoque.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>