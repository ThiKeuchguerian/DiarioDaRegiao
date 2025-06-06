$(document).ready(function () {
  $("#btn-exportar").click(function () {
    const table = document.getElementById("Resultado");
    debugger;
    TableToExcel.convert(table, {
      name: `ComissaoCirculacao.xlsx`,
      sheet: {
        name: 'ComissaoCirculacao'
      }
    });
  });
});
document.addEventListener('DOMContentLoaded', function () {
  const BuscarButton = document.getElementById('btn-buscar');
  const DtInicial = document.getElementById('DtInicial');
  const DtFinal = document.getElementById('DtFinal');

  BuscarButton.addEventListener('click', function () {
    // Torna os campos obrigatórios quando o botão "Incluir" é clicado
    DtInicial.setAttribute('required', 'required');
    DtFinal.setAttribute('required', 'required');
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

    // Esconde todos os accordions e seus botões antes de imprimir
    const accordions = document.querySelectorAll('[id^="Comissao"]');
    accordions.forEach(acc => {
      acc.style.display = "none";
      // Esconde também todos os botões dentro do accordion
      const div = acc.querySelectorAll('accordion-item');
      div.forEach(btn => btn.style.display = "none");
    });

    // Garante que a tabela resultado fique visível
    const resultado = document.getElementById("Resultado");
    if (resultado) resultado.style.display = "table";

    // Imprime mantendo o layout
    window.print();
  });
});