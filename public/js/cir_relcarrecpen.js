
$(document).ready(function () {
  $("#btn-exportar").click(function () {
    const table = document.getElementById("Resultado");

    TableToExcel.convert(table, {
      name: `RelacaoCartaoRecorrente.xlsx`,
      sheet: {
        name: 'RelacaoCartaoRecorrente'
      }
    });
  });
});

window.onbeforeprint = function () {
  document.getElementById('header').style.display = 'block';
  document.getElementById('footer').style.display = 'block';
};

window.onafterprint = function () {
  document.getElementById('header').style.display = 'block';
  document.getElementById('footer').style.display = 'block';
};

document.addEventListener('DOMContentLoaded', function () {
  const BuscarButton = document.getElementById('btn-buscar');
  const DtInicial = document.getElementById('dtInicio');
  const DtFinal = document.getElementById('dtFim');

  BuscarButton.addEventListener('click', function () {
    // Torna os campos obrigatórios quando o botão "Incluir" é clicado
    DtInicial.setAttribute('required', 'required');
    DtFinal.setAttribute('required', 'required');
  });
});

$(document).ready(function () {
  // Captura o botão de impressão pelo ID
  const btnImprimir = document.getElementById("Imprimir");

  // Adiciona um ouvinte de evento de clique ao botão de impressão
  btnImprimir.addEventListener("click", function () {
    // Abre a janela de impressão do navegador
    window.print();
  });
});