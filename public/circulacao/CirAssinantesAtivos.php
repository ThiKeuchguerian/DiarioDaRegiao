<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirAssinantesAtivos.php';

$Titulo = 'Relação de Assinantes Ativos';
$URL = URL_PRINCIPAL . 'circulacao/CirAssinantesAtivos.php';

// Instanciar a classe
$AssinantesAtivos = new AssinantesAtivos();

// Chamando a função -> Consulta Produto
$dadosProduto = $AssinantesAtivos->consultaProduto();
$dadosVendedor = $AssinantesAtivos->consultaVendedor();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $consultaContratos = $AssinantesAtivos->consultaContratos($dados);
  $Total = count(array_unique(array_column($consultaContratos, 'CPF_CNPJ')));
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-8">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Dt. Inicial</strong>
            </div>
            <div class="col">
              <strong>Dt. Final</strong>
            </div>
            <div class="col">
              <strong>Protudo</strong>
            </div>
            <div class="col">
              <strong>Combo</strong>
            </div>
            <div class="col">
              <strong>Mês Cad.</strong>
            </div>
            <div class="col">
              <strong>Vendedor</strong>
            </div>
            <div class="col">
              <strong>Tipo</strong>
            </div>
            <div class="col">
              <strong>Tipo Cob.</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center g-2"><!-- Adiciona g-1 para diminuir o espaçamento -->
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtInicio" name="dtInicio">
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtFim" name="dtFim">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="codProduto">
                <option value="0">-- Selecione --</option>
                <?php foreach ($dadosProduto as $key): ?>
                  <option value="<?= $key['CodProd'] ?>" <?= $key['CodProd'] === '0' ? 'selected' : '' ?>><?= $key['DesProd'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="Combo">
                <option value="">-- Selecione --</option>
                <option value="S">Sim</option>
                <option value="N">Não</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="CodVend" required>
                <option value="0">-- Selecione --</option>
                <?php foreach ($dadosVendedor as $key): ?>
                  <option value="<?= $key['CodVend'] ?>" <?= $key['CodVend'] === '0' ? 'selected' : '' ?>><?= $key['NomVend'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="TipoCon">&nbsp;
                <option value=""> </option>
                <option value="I">Novo</option>
                <option value="R">Renovado</option>
              </select>
            </div>
            <div class="col">
              <select class="form-select form-select-sm" name="TipoCob">
                <option value="">-- Selecione --</option>
                <option value="Pago">Pago</option>
                <option value="Cortesia">Cortesia</option>
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

<!-- Exibindo Resultado Buscar -->
<?php if (!empty($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          Qtde. Total: <?= $Total ?>
        </h5>
        <table class="table table-striped table-hover mb-0" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th scope="Col">Contrato</th>
              <th scope="col">Assinante</th>
              <th scope="col">Desc. Plano</th>
              <th scope="col">Tipo Con</th>
              <th scope="col">Mês Cad.</th>
              <th scope="col">Dt Assinatura</th>
              <th scope="col">Validade Inicial</th>
              <th scope="col">Validade Final</th>
              <th scope="col">Desc. Produto</th>
              <th scope="col">Tipo Cob</th>
              <th scope="col">Tipo Assinatura</th>
              <th scope="col">Vendedor</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaContratos as $key => $item) : ?>
              <tr>
                <td><?= $item['numeroDoContrato'] ?></td>
                <td><?= htmlspecialchars($item['nomeRazaoSocial']) ?></td>
                <td><?= htmlspecialchars($item['descricaoDoPlanoDePagamento']) ?></td>
                <td><?= $item['tipoDeContrato'] ?></td>
                <td><?= date('m/Y', strtotime($item['dataDaAssinatura'])) ?></td>
                <td><?= date('d/m/Y', strtotime($item['dataDaAssinatura'])) ?></td>
                <td><?= date('d/m/Y', strtotime($item['dataDeValidadeInicial'])) ?></td>
                <td><?= date('d/m/Y', strtotime($item['dataDeValidadeFinal'])) ?></td>
                <td><?= htmlspecialchars($item['descricaoDoProdutoServico']) ?></td>
                <td><?= htmlspecialchars($item['TipoCob']) ?></td>
                <td><?= htmlspecialchars($item['NatContrato']) ?></td>
                <td><?= htmlspecialchars($item['Vendedor']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui Modal -->
<?php require_once __DIR__ . '/../includes/modals/graf_Faturamento.php'; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/cir_assinantesativos.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>