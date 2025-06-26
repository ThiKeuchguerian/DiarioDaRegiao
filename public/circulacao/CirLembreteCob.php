<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirLembreteCob.php';

$Titulo = 'Lembrete de Cobrança (Somente E-Mail) - Circulação';
$URL = URL_PRINCIPAL . 'circulacao/CirLembreteCob.php';

// Instanciar a classe
$LembreteCob = new CirLembreteCobranca();

$consultaTipoCob = $LembreteCob->consultaTipoCob();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $consultaTitAbertos = $LembreteCob->consultaTitulosAbertos($dados);
  $Total = count($consultaTitAbertos);

  $dadosAgrupados = [];
  foreach ($consultaTitAbertos as $item) {
    $chaveAgrup = $item['codigoDaPessoa'] . '-' . $item['descricaoTipoCobranca'];
    if (!isset($dadosAgrupados[$chaveAgrup])) {
      $dadosAgrupados[$chaveAgrup] = $item;
    } else {
      $dadosAgrupados[$chaveAgrup]['valorDaParcela'] += $item['valorDaParcela'];
    }
  }
  $TotalCli = count((array_unique((array_column($consultaTitAbertos, 'codigoDaPessoa')))));
  $TotalCliComMail = count(array_unique(array_column(array_filter($consultaTitAbertos, function ($item) {
    return trim($item['email']) != '';
  }), 'codigoDaPessoa')));
  $TotalCliSemMail = count(array_unique(array_column(array_filter($consultaTitAbertos, function ($item) {
    return trim($item['email']) != '';
  }), 'codigoDaPessoa')));
} elseif (isset($_POST['btn-envia'])) {
  $dados = $_POST;
  
  $enviaEmail = $LembreteCob->enviaEmail($dados);
  $TotalEnv = COUNT($enviaEmail);
  $mensagem = implode("\n", $enviaEmail);
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
              <strong>Data Início</strong>
            </div>
            <div class="col">
              <strong>Data Final</strong>
            </div>
            <div class="col">
              <strong>Tipo Cobrança</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtInicio" name="dtInicio">
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtFim" name="dtFim">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="tipoCob" name="tipoCob">
                <option value="">-- Selecione --</option>
                <?php foreach ($consultaTipoCob as $tipoCobranca) : ?>
                  <option value="<?= $tipoCobranca['codigoTipoCobranca'] ?>" <?= isset($_POST['tipoCob']) && $_POST['tipoCob'] == $tipoCobranca['codigoTipoCobranca'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tipoCobranca['descricaoReduzida']) ?>
                  </option>
                <?php endforeach; ?>
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
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          Período: <?= date('d/m/Y', strtotime($dados['dtInicio'])) ?> - <?= date('d/m/Y', strtotime($dados['dtFim'])) ?> ||
          Total Clientes: <?= $TotalCli ?> ||
          Total Clientes C/ E-Mail: <?= $TotalCliComMail ?> ||
          Total Clientes S/ E-Mail: <?= $TotalCliSemMail ?>
        </h5>
        <div class="card-footer d-flex justify-content-end">
          <form action="<?= $URL ?>" method="post">
            <input type="hidden" id="selected_ids" name="selected_ids" required>
            <?php if ($dados['tipoCob'] <> '2') : ?>
              <button type="submit" id="btn-envia" name="btn-envia" class="btn btn-success btn-sm" value=1 onclick="setSelectedIds()">Enviar Somente E-Mail</button>
            <?php elseif ($dados['tipoCob'] == '2') : ?>
              <button type="submit" id="btn-enviapdf" name="btn-envia" class="btn btn-success btn-sm" value=2 onclick="setSelectedIds()">Enviar E-Mail Com Boleto</button>
            <?php endif; ?>
          </form>
        </div>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="col">Nº. Contrato</th>
              <th scope="col">Cod.Cli.</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">E-Mail</th>
              <th scope="col">Vencimento</th>
              <th scope="col">Plano / Tipo</th>
              <th scope="col">Portador</th>
              <th scope="col">Tipo Cob.</th>
              <th scope="col">Valor</th>
              <th scope="col"><input type="checkbox" id="selectAll" name="selectAll" onclick="toggleSelectAll(this, this.closest('table'))"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dadosAgrupados as $item) : ?>
              <?php //depurar($item) ?>
              <tr>
                <td><?= $item['numeroDoContrato'] ?></td>
                <td><?= $item['codigoDaPessoa'] ?></td>
                <td><?= strlen($item['nomeRazaoSocial']) > 50 ? substr($item['nomeRazaoSocial'], 0, 50) . '...' : $item['nomeRazaoSocial'] ?></td>
                <td><?= $item['email'] ?></td>
                <td><?= date('d/m/Y', strtotime($item['dataDoVencimento'])) ?></td>
                <td style="white-space: nowrap;"><?= $item['Plano'] ?> / <?= $item['Tipo'] ?></td>
                <td style="white-space: nowrap;"><?= $item['nomePortador'] ?></td>
                <td style="white-space: nowrap;"><?= $item['descricaoTipoCobranca'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['valorDaParcela'], 2, ',', '.') ?></td>
                <td>
                  <?php if ($item['email'] != '') : ?>
                    <input type="checkbox" name="selected[]" value="<?= htmlspecialchars(json_encode([
                                                                      'Plano' => trim($item['Plano']),
                                                                      'numeroDoContrato' => trim($item['numeroDoContrato']),
                                                                      'codigoDaPessoa' => trim($item['codigoDaPessoa']),
                                                                      'nomeRazaoSocial' => trim($item['nomeRazaoSocial']),
                                                                      'email' => trim($item['email']),
                                                                      'numeroDaParcela' => trim($item['numeroDaParcela']),
                                                                      'numeroDeParcelas' => trim($item['numeroDeParcelas']),
                                                                      'dataDoVencimento' => trim($item['dataDoVencimento']),
                                                                      'numeroBancario' => trim($item['numeroBancario']),
                                                                      // 'numeroDaRemessa' => trim($item['codigoDaRemessa']),
                                                                      'valorDaParcela' => trim($item['valorDaParcela']),
                                                                      'CpfCnpj' => trim($item['CpfCnpj']),
                                                                      // 'Endereco' => urlencode($item['endereco']),
                                                                      // 'Cep' => trim($item['cep']),
                                                                      // 'Cidade' => trim($item['nomeDoMunicipio']),
                                                                      // 'UF' => trim($item['siglaDaUF']),
                                                                      'Descontos' => trim($item['descontos']),
                                                                      'codigoDoPortador' => trim($item['codigoDoPortador'])
                                                                    ])) ?>">
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if (!empty($TotalEnv)): ?>
  <div class="container d-flex justify-content-center filter-fields">
    <div class="col col-sm-8">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-header bg-primary text-white">
            Envio de E-Mail Concluído: <?= $TotalEnv ?> E-Mails Enviados
          </h5>
          <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
            <thead>
              <tr class="table-primary">
                <th scope="col">E-Mail Enviado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($enviaEmail as $key => $value): ?>
                <tr>
                  <td><?= $value ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/cir_lembretecob.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>