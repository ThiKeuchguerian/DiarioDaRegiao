<?php
require_once __DIR__ . '/../config/config.php';
// require_once __DIR__ . '/../classes/Functions/ALTERAR.php';

$Titulo = 'Demonstrativo de Apuração das Contribuições: PIS, COFINS E INSS';
$URL = URL_PRINCIPAL . 'contabil/ContApuracaoContribuicoes.php';

// Instanciar a classe
// $ClassifCheckMetas = new ClassifCheckMetas();

if (isset($_POST['btn-buscar'])) {
  $mesAno = $_POST['MesAno'];
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
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
            <div class="row">
              <div class="col-8">Noticiários</div>
              <div class="col-4 value">6.652,08</div>
            </div>
            <div class="row">
              <div class="col-8">Classificados CM</div>
              <div class="col-4 value">1.432,15</div>
            </div>
            <div class="row">
              <div class="col-8">Classificados Imóveis</div>
              <div class="col-4 value">312,00</div>
            </div>
            <div class="row">
              <div class="col-8">Classificados Locação</div>
              <div class="col-4 value">212,00</div>
            </div>
            <div class="row">
              <div class="col-8">Suplementos 54343</div>
              <div class="col-4 value">315.00</div>
            </div>
            <div class="row">
              <div class="col-8">Direito Motor</div>
              <div class="col-4 value">214,00</div>
            </div>
            <div class="row">
              <div class="col-8">Diário Web</div>
              <div class="col-4 value">217,00</div>
            </div>
            <div class="row">
              <div class="col-8">Publicidades Rádios</div>
              <div class="col-4 value">210,00</div>
            </div>
            <div class="row">
              <div class="col-8">Publicidades Redes Sociais</div>
              <div class="col-4 value">350,00</div>
            </div>
            <div class="row">
              <div class="col-8">Assinaturas de Jornais 5101/5101E</div>
              <div class="col-4 value">317.242,62</div>
            </div>
            <div class="row">
              <div class="col-8">Assinaturas de Jornais 5116/5116E</div>
              <div class="col-4 value">473.462,20</div>
            </div>
            <div class="row">
              <div class="col-8">Receitas Merc Externo</div>
              <div class="col-4 value">7.626,90</div>
            </div>
            <div class="row">
              <div class="col-8"><strong>BASE DE CÁLCULO</strong></div>
              <div class="col-4 value">851.197,48</div>
            </div>
            <div class="row mt-2">
              <div class="col-8">PIS a Pagar Código 8109:</div>
              <div class="col-4 value">5.381,68</div>
            </div>
            <div class="row">
              <div class="col-8">COFINS a Pagar Código 21:</div>
              <div class="col-4 value">24.838,52</div>
            </div>
          </div>

          <div class="border-box">
            <div class="section-title">INSS S/T RECEITA BRUTA</div>
            <div class="row">
              <div class="col-8">RECEITA OPERACIONAL BRUTA:</div>
              <div class="col-4 text-end"><span class="value">1.361.283,81</span></div>
            </div>
            <div class="row">
              <div class="col-8">1,5% INSS S/T RECEITA BRUTA:</div>
              <div class="col-4 text-end"><span class="value">20.419,31</span></div>
            </div>
            <div class="row">
              <div class="col-8">RECEITA NÃO OPERACIONAL:</div>
              <div class="col-4 text-end"><span class="value">24.895,50</span></div>
            </div>
            <div class="row">
              <div class="col-8">1,5% INSS S/ OUTRAS RECEITAS:</div>
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
              <div class="col-8">Alíquota PIS:</div>
              <div class="col-4 text-end">1,65%</div>
            </div>
            <div class="row">
              <div class="col-8">Alíquota COFINS:</div>
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