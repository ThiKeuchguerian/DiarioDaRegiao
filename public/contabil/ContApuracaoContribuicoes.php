<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ContRecOperacionais.php';

$Titulo = 'Demonstrativo de Apuração das Contribuições: PIS, COFINS E INSS';
$URL = URL_PRINCIPAL . 'contabil/ContApuracaoContribuicoes.php';

// Instanciar a classe
$ContabilRecOperacionais = new ContabilRecOperacionais();


if (isset($_POST['btn-buscar'])) {
  $mesAno = $_POST['MesAno'];

  $Consultas = $ContabilRecOperacionais->gerarRelatorio($mesAno, ['outrosDocumentos', 'itensNotasFiscais']);

  // Processar dados para exibição
  $outrosDocumentos = isset($Consultas['outrosDocumentos']) && is_array($Consultas['outrosDocumentos']) ? $Consultas['outrosDocumentos'] : [];
  $itensNotasFiscais = isset($Consultas['itensNotasFiscais']) && is_array($Consultas['itensNotasFiscais']) ? $Consultas['itensNotasFiscais'] : [];

  // Tipos de serviço a serem exibidos para itensNotasFiscais
  $tipos = [
    'Encartes 5949S',
    'Impressos de Jornais 5101/6101',
    'Assinaturas de Jornais  5116/5116E',
    'Vendas Avulsos 5101/5113'
  ];

  // Filtrar itensNotasFiscais pelos tipos
  $itensFiltrados = array_filter($itensNotasFiscais, function ($item) use ($tipos) {
    return in_array($item['TipoServico'], $tipos);
  });

  // Somar valores
  $somaVlrVeic = 0;
  foreach ($outrosDocumentos as $item) {
    $somaVlrVeic += floatval($item['VlrVeic']);
  }

  $somaValor = 0;
  foreach ($itensFiltrados as $item) {
    $somaValor += floatval($item['Valor']);
  }

  $total = $somaVlrVeic + $somaValor;
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
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
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
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

<div class="container">
  <div class="card shadow-sm mb-0">
    <div class="card-body mb-0">
      <div class="row mb-0">
        <!-- REGIME CUMULATIVO -->
        <div class="col-md-4 mb-0">
          <div class="border-box">
            <div class="section-title">REGIME CUMULATIVO</div>
            <div class="row">
              <div class="col-8">Alíquota PIS:</span></div>
              <div class="col-4" style="text-align: right; font-weight: bold;">0,65%</div>
            </div>
            <div class="row">
              <div class="col-8">Alíquota COFINS:</div>
              <div class="col-4" style="text-align: right; font-weight: bold;">3,00%</div>
            </div>
            <div class="mb-2"></div>
            <div class="section-title">RECEITAS OPERACIONAIS</div>
            <?php if (!empty($outrosDocumentos)): ?>
              <?php foreach ($outrosDocumentos as $item): ?>
                <div class="row">
                  <div class="col-8"><?= htmlspecialchars($item['TipoServico']) ?></div>
                  <div class="col-4 value"><?= number_format($item['VlrVeic'], 2, ',', '.') ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($itensFiltrados)): ?>
              <?php foreach ($itensFiltrados as $item): ?>
                <div class="row">
                  <div class="col-8"><?= htmlspecialchars($item['TipoServico']) ?></div>
                  <div class="col-4 value"><?= number_format($item['Valor'], 2, ',', '.') ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
            <div class="mb-2"></div>
            <div class="row">
              <div class="col-8">BASE DE CÁLCULO</div>
              <div class="col-4 text-end"><?= number_format($total, 2, ',', '.') ?></div>
            </div>
          </div>

          <div class="border-box">
            <div class="row">
              <div class="col-8">PIS S/ Receitas - Devido</div>
              <div class="col-4 text-end"><span class="value"><?= number_format(($total * 0.0065), 2, ',', '.') ?></span></div>
            </div>
            <div class="row">
              <div class="col-8">PIS Retido de Recebimentos</div>
              <div class="col-4 text-end"><span class="value"></span></div>
            </div>
            <div class="row">
              <div class="col-8">PIS Compensado</div>
              <div class="col-4 text-end"><span class="value">24.895,50</span></div>
            </div>
            <div class="row">
              <div class="col-8">Exclusão - Merc Externo</div>
              <div class="col-4 text-end"><span class="value">373,43</span></div>
            </div>
            <div class="row">
              <div class="col-8"><strong>TOTAL RECEITAS</strong>:</div>
              <div class="col-4 text-end"><span class="value">1.386.183,31</span></div>
            </div>
            <div class="row">
              <div class="col-8"><strong>INSS TOTAL S/ RECEITAS</strong>:</div>
              <div class="col-4 text-end"><span class="value">29.732,84</span></div>
            </div>
          </div>

          <div class="border-box">
            <div class="section-title">DARF's</div>
            <div class="row">
              <div class="col-8">CÓDIGO 8109 PIS</div>
              <div class="col-4 text-end"><span class="value">5.381,68</span></div>
            </div>
            <div class="row">
              <div class="col-8">CÓDIGO 2172 Cofins</div>
              <div class="col-4 text-end"><span class="value">24.838,52</span></div>
            </div>
            <div class="row">
              <div class="col-8">CÓDIGO 2985 Receita Fina</div>
              <div class="col-4 text-end"><span class="value">-</span></div>
            </div>
            <div class="row">
              <div class="col-8">CÓDIGO 2931 INSS</div>
              <div class="col-4 text-end"><span class="value">29.732,84</span></div>
            </div>
            <div class="row">
              <div class="col-8"><strong>INSS SALDO A PAGAR:</strong></div>
              <div class="col-4 text-end"><span class="value">29.732,84</span></div>
            </div>
          </div>
        </div>

        <!-- REGIME NÃO CUMULATIVO -->
        <div class="col-md-4">
          <div class="border-box">
            <div class="section-title">REGIME NÃO CUMULATIVO</div>
            <div class="row">
              <div class="col-8">Alíquota PIS Código 6912:</div>
              <div class="col-4 text-end">1,65%</div>
            </div>
            <div class="row">
              <div class="col-8">Alíquota COFINS Código 5856:</div>
              <div class="col-4 text-end">7,60%</div>
            </div>
            <div class="mb-2"></div>
            <div class="sub-section-title">RECEITAS OPERACIONAIS</div>
            <div class="row">
              <div class="col-8">Impressos Comerciais 54343/54343I</div>
              <div class="col-4 text-end"><span class="value">947.079,64</span></div>
            </div>
            <div class="row">
              <div class="col-8">Impressos Embalagem 5101/5101I</div>
              <div class="col-4 text-end"><span class="value">147.000,00</span></div>
            </div>
            <div class="row">
              <div class="col-8">Comercio 5101</div>
              <div class="col-4 text-end"><span class="value">16.100,00</span></div>
            </div>
            <div class="row">
              <div class="col-8">(-) IPI</div>
              <div class="col-4 text-end"><span class="value">(48.000,00)</span></div>
            </div>
            <div class="row">
              <div class="col-8"><strong>BASE DE CÁLCULO</strong></div>
              <div class="col-4 text-end"><span class="value">1.110.179,64</span></div>
            </div>
            <div class="row">
              <div class="col-8">PIS a Pagar</div>
              <div class="col-4 text-end"><span class="value">18.318,00</span></div>
            </div>
            <div class="row">
              <div class="col-8">COFINS a Pagar</div>
              <div class="col-4 text-end"><span class="value">84.366,37</span></div>
            </div>
          </div>

          <div class="border-box">
            <div class="sub-section-title">RECEITAS NÃO OPERACIONAIS</div>
            <div class="row">
              <div class="col-8">Sucata 5101E</div>
              <div class="col-4 text-end"><span class="value">24.895,50</span></div>
            </div>
            <div class="mb-4"></div>
            <div class="row">
              <div class="col-8">BASE DE CÁLCULO</div>
              <div class="col-4 text-end"><span class="value">24.895,50</span></div>
            </div>
            <div class="mb-4"></div>
            <div class="row">
              <div class="col-8">PIS 3 Pagar</div>
              <div class="col-4 text-end"><span class="value">410,78</span></div>
            </div>
            <div class="row">
              <div class="col-8">COFINS 3 Pagar</div>
              <div class="col-4 text-end"><span class="value">1.892,06</span></div>
            </div>
          </div>

          <div class="border-box">
            <div class="section-title">Receitas Financeiras</div>
            <div class="row">
              <div class="col-8">PIS:</div>
              <div class="col-4 text-end"><span class="value">0,652%</span></div>
            </div>
            <div class="row">
              <div class="col-8">COFINS:</div>
              <div class="col-4 text-end"><span class="value">4,000%</span></div>
            </div>
          </div>
        </div>

        <!-- Legendao e outras informações. -->
        <div class="col-md-4">
          <div class="border-box">
            <div class="section-title">Legendas</div>
            <div class="row">
              <div class="col-4 d-flex align-items-center">Comunicação</div>
              <div class="col-8">
                Consulta item pedido(F121CIP) X <br>
                Consulta de lançamentos(F660BLA)
              </div>
            </div>
            <div class="row">
              <div class="col-4 d-flex align-items-center">Notas Faturadas</div>
              <div class="col-8">
                Consulta item NF de Saídas (F141CIS) X <br>
                Consulta itens de Notas Fiscais (F660CIN)
              </div>
            </div>
          </div>

          <div class="border-box" style="border: 1px solid #ccc;">
            <div class="section-title">Imposto Devido</div>
            <div class="row">
              <div class="col">Receitas</div>
              <div class="col">C.Custo</div>
              <div class="col">Cofins</div>
              <div class="col">Pis</div>
              <div class="col">INSS</div>
              <div class="col">TOTAL</div>
            </div>
            <div class="row">
              <div class="col">Assinaturas</div>
              <div class="col">10204</div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
            <div class="row">
              <div class="col">Bancas</div>
              <div class="col">10205</div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
            <div class="row">
              <div class="col">Publicidade</div>
              <div class="col">10301</div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
            <div class="row">
              <div class="col">Encartes</div>
              <div class="col">10301</div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
            <div class="row">
              <div class="col">Gráficos Jornal</div>
              <div class="col">10307</div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
            <div class="row">
              <div class="col">Gráficos Comercial</div>
              <div class="col">10307</div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
            <div class="row">
              <div class="col">Vida & Arte</div>
              <div class="col">10308</div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
            <div class="row">
              <div class="col">Web</div>
              <div class="col">10802</div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
            <div class="row">
              <div class="col"><strong>TOTAL:</strong></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
              <div class="col"></div>
            </div>
          </div>

          <div class="border-box">
            <div class="section-title">Receitas Financeiras</div>
            <div class="row">
              <div class="col-8">PIS:</div>
              <div class="col-4 text-end"><span class="value">0,652%</span></div>
            </div>
            <div class="row">
              <div class="col-8">COFINS:</div>
              <div class="col-4 text-end"><span class="value">4,000%</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>