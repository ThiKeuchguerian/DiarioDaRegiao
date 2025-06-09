<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirVendasEquipe.php';

$Titulo = 'Vendas por Equipe';
$URL = URL_PRINCIPAL . 'circulacao/CirVendasEquipe.php';

// Instanciar a classe
$CirVendasEquipe = new CirVendasEquipe();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $MesAno = $_POST['MesAno'];
  $CodProduto = $_POST['CodProduto'];
  // Separa mês e ano – use explode em vez de str_split para evitar problemas
  list($Mes, $Ano) = explode('/', $MesAno);
  // Converte para inteiros
  $Mes = (int)$Mes;
  $Ano = (int)$Ano;

  // Vamos obter os 4 meses: os 3 anteriores + o mês atual.
  // Faremos um loop usando um "offset" que varia de -3 (mais antigo) até 0 (mês atual).
  $MesCads = [];
  for ($offset = -3; $offset <= 0; $offset++) {
    $NovoMes = $Mes + $offset;
    $NovoAno = $Ano;
    // Se o novo mês for menor que 1, significa que passamos para o ano anterior.
    if ($NovoMes < 1) {
      $NovoMes += 12;
      $NovoAno--;
    }
    // Formata com dois dígitos para o mês e quatro para o ano
    $MesCads[] = sprintf("%02d/%04d", $NovoMes, $NovoAno);
  }

  // Agora, de acordo com o exemplo desejado:
  $MesCad1 = $MesCads[0]; // mês mais antigo dos 4 meses
  $MesCad2 = $MesCads[1];
  $MesCad3 = $MesCads[2];
  $MesCad4 = $MesCads[3];

  // $agrupamentos = ['NomeVendedor', 'GrupoEquipe'];
  // $meses = [$MesCad1, $MesCad2, $MesCad3, $MesCad4];
  // echo "<pre>";
  // var_dump($MesAno);
  // var_dump($Mes, $Ano);
  // var_dump($MesCad1, $MesCad2, $MesCad3, $MesCad4);
  // die();
  // $consultaVendas = $CirVendasEquipe->consultaVendas($meses, $CodProduto, $agrupamentos);

  $ConsultaVendasTelevendas = $CirVendasEquipe->ConsultaVendasTelevendas($MesCad1, $MesCad2, $MesCad3, $MesCad4, $CodProduto);
  $ConsultaVendasDepartamento = $CirVendasEquipe->ConsultaVendasDepartamento($MesCad1, $MesCad2, $MesCad3, $MesCad4, $CodProduto);

  $resultado = array_merge($ConsultaVendasTelevendas, $ConsultaVendasDepartamento);
}


// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Mes Cadastro</strong>
            </div>
            <div class="col">
              <strong>Produto</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="CodProduto" name="CodProduto">
                <option value="">--Selecione Produto --</option>
                <option value="0"> Todos </option>
                <option value="1"> Diário da Região </option>
                <option value="3"> Diário da Região Digital </option>
                <option value="13"> Diário da Região + Digital </option>
                <option value="11"> Jornal Viva+ </option>
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
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

<!-- Resultado -->
<?php if (isset($resultado)) : ?>
  <?php $Meses = [$MesCad1, $MesCad2, $MesCad3, $MesCad4]; ?>
  <?php $ColunasPorMes = ['Cbo', 'Dig', 'Imp', 'Total', '&nbsp;']; ?>
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <table class="table table-striped table-hover" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Mês</th>
              <th scope="col" colspan="5" style="text-align: center;"> <?php echo $MesCad1; ?> </th>
              <th scope="col" colspan="5" style="text-align: center;"> <?php echo $MesCad2; ?> </th>
              <th scope="col" colspan="5" style="text-align: center;"> <?php echo $MesCad3; ?> </th>
              <th scope="col" colspan="5" style="text-align: center;"> <?php echo $MesCad4; ?> </th>
              <th scope="col" style="text-align: center;">Total Geral</th>
            </tr>
            <tr>
              <th scope="col" class="text-center">Nome Vendedor</th>
              <?php foreach ($Meses as $Mes): ?>
                <?php foreach ($ColunasPorMes as $Titulo): ?>
                  <th scope="col" class="text-center"><?= $Titulo ?></th>
                <?php endforeach; ?>
              <?php endforeach; ?>
              <th scope="col" class="text-center"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ConsultaVendasTelevendas as $item): ?>
              <tr>
                <th><?= $item['NomeVendedor'] ?></th>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                  <td class="text-center"><?= $item["TotalCombo{$i}"] ?></td>
                  <td class="text-center"><?= $item["TotalDigital{$i}"] ?></td>
                  <td class="text-center"><?= $item["TotalImpressa{$i}"] ?></td>
                  <th class="text-center"><?= $item["TotalGeral{$i}"] ?></th>
                  <td class="text-center">&nbsp;</td>
                <?php endfor; ?>
                <th class="text-center"><?= $item['TotalGeral'] ?></th>
              </tr>
            <?php endforeach; ?>
            <?php
            // Antes do primeiro foreach (Vendas Televendas), inicialize os acumuladores:
            $somaComboTV    = array_fill(1, 4, 0);
            $somaDigitalTV  = array_fill(1, 4, 0);
            $somaImpTV      = array_fill(1, 4, 0);
            $somaGeralTV    = array_fill(1, 4, 0);
            $somaTotalGeralTV = 0;

            // Loop de Vendas Televendas
            foreach ($ConsultaVendasTelevendas as $item) {
              for ($i = 1; $i <= 4; $i++) {
                $somaComboTV[$i]   += $item["TotalCombo{$i}"];
                $somaDigitalTV[$i] += $item["TotalDigital{$i}"];
                $somaImpTV[$i]     += $item["TotalImpressa{$i}"];
                $somaGeralTV[$i]   += $item["TotalGeral{$i}"];
              }
              $somaTotalGeralTV += $item['TotalGeral'];
            }
            ?>
            <!-- Linha de totalização para Vendas Televendas -->
            <tr class="table-primary">
              <th class="text-center">Total Dept. Televendas</th>
              <?php for ($i = 1; $i <= 4; $i++): ?>
                <td class="text-center"><?= $somaComboTV[$i]   ?></td>
                <td class="text-center"><?= $somaDigitalTV[$i] ?></td>
                <td class="text-center"><?= $somaImpTV[$i]     ?></td>
                <th class="text-center"><?= $somaGeralTV[$i]   ?></th>
                <td class="text-center">&nbsp;</td>
              <?php endfor; ?>
              <th class="text-center"><?= $somaTotalGeralTV ?></th>
            </tr>
          </tbody>
          <tbody>
            <!-- Linha de separação -->
            <tr class="spacer-row" style="background-color: white; height: 20px;"></tr>
          </tbody>
          <tbody>
            <?php foreach ($ConsultaVendasDepartamento as $key => $item): ?>
              <tr>
                <th><?= $item['GrupoEquipe'] ?></th>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                  <td class="text-center"><?= $item["TotalCombo{$i}"] ?></td>
                  <td class="text-center"><?= $item["TotalDigital{$i}"] ?></td>
                  <td class="text-center"><?= $item["TotalImpressa{$i}"] ?></td>
                  <th class="text-center"><?= $item["TotalGeral{$i}"] ?></th>
                  <td class="text-center">&nbsp;</td>
                <?php endfor; ?>
                <th class="text-center"><?= $item['TotalGeral'] ?></th>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody>
            <?php
            // Antes do foreach de ConsultaVendasDepartamento, inicialize outros acumuladores:
            $somaComboDep    = array_fill(1, 4, 0);
            $somaDigitalDep  = array_fill(1, 4, 0);
            $somaImpDep      = array_fill(1, 4, 0);
            $somaGeralDep    = array_fill(1, 4, 0);
            $somaTotalGeralDep = 0;

            // Loop de Vendas por Departamento
            foreach ($ConsultaVendasDepartamento as $item) {
              for ($i = 1; $i <= 4; $i++) {
                $somaComboDep[$i]   += $item["TotalCombo{$i}"];
                $somaDigitalDep[$i] += $item["TotalDigital{$i}"];
                $somaImpDep[$i]     += $item["TotalImpressa{$i}"];
                $somaGeralDep[$i]   += $item["TotalGeral{$i}"];
              }
              $somaTotalGeralDep += $item['TotalGeral'];
            }
            ?>
            <!-- Linha de totalização para Vendas Departamento -->
            <tr class="table-primary">
              <th class="text-center">Total Departamento</th>
              <?php for ($i = 1; $i <= 4; $i++): ?>
                <td class="text-center"><?= $somaComboDep[$i]   ?></td>
                <td class="text-center"><?= $somaDigitalDep[$i] ?></td>
                <td class="text-center"><?= $somaImpDep[$i]     ?></td>
                <th class="text-center"><?= $somaGeralDep[$i]   ?></th>
                <td class="text-center">&nbsp;</td>
              <?php endfor; ?>
              <th class="text-center"><?= $somaTotalGeralDep ?></th>
            </tr>
          </tbody>
          <tbody>
            <!-- Linha de separação -->
            <tr class="spacer-row" style="background-color: white; height: 20px;"></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o resultado e o footer -->
<div class="mb-3"></div>
<!-- JavaScript -->
<script src="../js/cirvendasequipe.js"></script>
<script src="../js/maskcampos.js"></script>
<!-- Footer -->
<?php require_once __DIR__ . '/../includes/footer.php'; ?>