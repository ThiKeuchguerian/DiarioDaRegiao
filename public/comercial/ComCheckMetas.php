<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ComCheckMetas.php';

$Titulo = 'Check Metas Comercial - Capt';
$URL = URL_PRINCIPAL . 'comercial/ComCheckMetas.php';

// Instanciar a classe
$ComercialCheckMetas = new ComercialCheckMetas();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $Ano = $_POST['Ano'];

  $CheckMetasGrupo = $ComercialCheckMetas->ConsultaCheckMetasGrupo($Ano);
  $CheckMetasProduto = $ComercialCheckMetas->ConsultaCheckMetasProduto($Ano);
  $CheckMetasSomaTotal = $ComercialCheckMetas->ConsultaCheckMetasSomaTotal($Ano);
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Ano</strong>
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

<!-- Resultado Analítico -->
<?php if (!empty($CheckMetasGrupo) || !empty($CheckMetasProduto) || !empty($CheckMetasSomaTotal)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <table id="CheckMetasComercial" class="table table-striped table-hover toggle-details">
          <thead>
            <tr class="table-primary">
              <th>Grupo Produto</th>
              <th style="text-align: center">01/<?= $Ano ?></th>
              <th style="text-align: center">02/<?= $Ano ?></th>
              <th style="text-align: center">03/<?= $Ano ?></th>
              <th style="text-align: center">04/<?= $Ano ?></th>
              <th style="text-align: center">05/<?= $Ano ?></th>
              <th style="text-align: center">06/<?= $Ano ?></th>
              <th style="text-align: center">07/<?= $Ano ?></th>
              <th style="text-align: center">08/<?= $Ano ?></th>
              <th style="text-align: center">09/<?= $Ano ?></th>
              <th style="text-align: center">10/<?= $Ano ?></th>
              <th style="text-align: center">11/<?= $Ano ?></th>
              <th style="text-align: center">12/<?= $Ano ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($CheckMetasSomaTotal as $key => $Soma) : ?>
              <tr  class="table-primary">
                <th>Total</th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["01/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["02/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["03/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["04/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["05/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["06/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["07/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["08/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["09/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["10/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["11/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
                <th style="text-align: right; white-space: nowrap;"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($Soma["12/{$Ano}"] ?? 0), 2, ',', '.') ?></th>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody>
            <?php foreach ($CheckMetasGrupo as $key => $item) : ?>
              <tr class="summary-row" data-nomeGrupo="<?= ($itens['nomeGrupo']) ?>" onclick="toggleDetails('<?= ($item['nomeGrupo']) ?>')">
                <th><?= $item['nomeGrupo'] ?></th>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["01/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["02/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["03/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["04/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["05/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["06/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["07/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["08/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["09/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["10/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["11/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($item["12/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
              </tr>

              <?php foreach ($CheckMetasProduto as $key => $itens) : ?>
                <?php if ($itens['nomeGrupo'] === $item['nomeGrupo']) : ?>
                  <tr class="detail-row" data-nomeGrupo="<?= htmlspecialchars($itens['nomeGrupo']) ?>" style="display: none;">
                    <th class="align-left"><?= htmlspecialchars($itens['nomeSecao']) ?></th>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["01/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["02/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["03/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["04/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["05/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["06/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["07/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["08/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["09/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["10/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["11/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                    <td class="align-right"><span style="float: left;">R$ </span><?= number_format(htmlspecialchars($itens["12/{$Ano}"] ?? 0), 2, ',', '.') ?></td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="mb-3"></div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Incluindo JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/comcheckmetas.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>