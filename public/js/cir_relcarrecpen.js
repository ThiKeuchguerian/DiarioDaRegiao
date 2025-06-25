
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
  const btnImprimir = document.getElementById("btn-imprimir");

  btnImprimir.addEventListener("click", function () {
    // Cria ou atualiza o estilo para impressão em paisagem
    let style = document.getElementById('print-landscape-style');
    if (!style) {
      style = document.createElement('style');
      style.id = 'print-landscape-style';
      style.media = 'print';
      style.innerHTML = '@page { size: landscape; }';
      document.head.appendChild(style);
    }

    // Esconde o formulário antes de imprimir
    const form = document.querySelector("form");
    if (form) form.style.display = "none";

    // Garante que a tabela resultado fique visível
    const resultado = document.getElementById("Resultado");
    if (resultado) resultado.style.display = "table";

    // Imprime mantendo o layout
    window.print();
  });
});