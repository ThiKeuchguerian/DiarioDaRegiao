<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirValidadorCPFL.php';

$Titulo = 'Validador CPFL';
$URL = URL_PRINCIPAL . 'circulacao/CirValidadorCPFL.php';

// Instanciar a classe
$ValidadorCpfl = new ValidadorCpfl();

if (isset($_POST['btn-buscar'])) {
  $pendentes = $_POST['Pendentes'];
  $dtInicio  = $_POST['DtInicial'];
  $dtFim     = $_POST['DtFinal'];
  $nomeArquivo = $_FILES['arqcpfl']['name'];
  
  $consultaCpfl = $ValidadorCpfl->consultaCPFL($pendentes, $dtInicio, $dtFim, $nomeArquivo);
  $Total = COUNT($consultaCpfl);
} else if (isset($_POST['btn-processar'])) {
  $dirUp = __DIR__  . '/../uploads/';
  $arqTmp = $_FILES['arqcpfl']['tmp_name'];
  $arqOri = $_FILES['arqcpfl']['name'];
  $nomeArquivo = $arqOri;
  $nomeOriginal = $dirUp . basename($arqOri);

  $consultaArq = $ValidadorCpfl->consultaArquivo($nomeArquivo);
  // depurar($consultaArq);
  if ($consultaArq > 0) {
    echo "<script>alert('O arquivo já processado !!!');</script>";
    // Remove o arquivo da pasta uploads
    unlink($nomeOriginal);
  } elseif (move_uploaded_file($arqTmp, $nomeOriginal)) {
    echo "<script>alert('Upload realizado com sucesso!');</script>";
    $processarArq = $ValidadorCpfl->processaArq($nomeOriginal, $arqOri);
    // Remove o arquivo da pasta uploads
    unlink($nomeOriginal);
  }

  $consultaCpfl = $ValidadorCpfl->consultaCPFL($pendentes = null, $dtInicio = null, $dtFim = null, $nomeArquivo);
  $Total = COUNT($consultaCpfl);
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-8">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" enctype="multipart/form-data" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Somente Títulos Pendentes</strong>
            </div>
            <div class="col">
              <strong>Data Inicio</strong>
            </div>
            <div class="col">
              <strong>Data Final</strong>
            </div>
            <div class="col">
              <strong>Importar Arquivo</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select id="Pendentes" name="Pendentes" class="form-select form-select-sm">
                <option value="0">-- Selecione --</option>
                <option value="1"> Não </options>
                <option value="2"> Sim </options>
              </select>
            </div>
            <div class="col">
              <input type="date" id="DtInicial" name="DtInicial" class="form-control form-control-sm">
            </div>
            <div class="col">
              <input type="date" id="DtFinal" name="DtFinal" class="form-control form-control-sm">
            </div>
            <div class="col">
              <input type="file" class="form-control form-control-sm" id="arqcpfl" name="arqcpfl" accept=".txt">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-processar" name="btn-processar" type="submit" class="btn btn-primary btn-sm">Processar</button>
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
        <h5 class="card-header bg-primary text-white">
          Qtde. Total: <?= $Total ?> || <?= date("d/m/Y", strtotime($dtInicio))  ?> à <?= date("d/m/Y", strtotime($dtFim)) ?>
        </h5>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">UC</th>
              <th scope="col">Contrato</th>
              <th scope="col">Parcela</th>
              <th scope="col">Valor Recebido (CPFL)</th>
              <th scope="col">Valor Parcela (Gestor)</th>
              <th scope="col">Slado Título (Gestor)</th>
              <th scope="col">Dt. Geração</th>
              <th scope="col">Hr. Geração</th>
              <th scope="col">Nome do Arquivo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaCpfl as $key => $item): ?>
              <tr>
                <td style="text-align: center;"><?= $item['UC'] ?></td>
                <td style="text-align: center;"><?= $item['Titulo'] ?></td>
                <td style="text-align: center;"><?= $item['Parcela'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span> <?= number_format($item['valorRecebidoCPFL'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span> <?= number_format($item['ValorDaParcelaGESTOR'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span> <?= number_format($item['SaldoTituloGESTOR'], 2, ',', '.') ?></td>
                <td style="text-align: center;"><?= $item['dtGeracao'] ?></td>
                <td style="text-align: center;"><?= $item['hrGeracao'] ?></td>
                <td><?= $item['nomeDoArquivo'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/cir_validadorcpfl.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>