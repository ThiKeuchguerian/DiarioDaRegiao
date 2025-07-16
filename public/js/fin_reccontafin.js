$("#btn-buscar").click(function (event) {
  var Ano = $("#ano").val();
  if (!Ano) {
    alert("Por favor, seleciona um Ano.");
    event.preventDefault(); // Impede o envio do formulário
  }
});

document.querySelectorAll('.clickable-row').forEach(row => {
  row.addEventListener('click', function () {
    const target = this.getAttribute('data-target');
    const details = document.querySelector(target);

    // Toggle visibility
    if (details.style.display === "none" || details.style.display === "") {
      details.style.display = "table-row"; // Mostrar a linha com detalhes
    } else {
      details.style.display = "none"; // Ocultar a linha com detalhes
    }
  });
});

$(document).ready(function () {
  $("#Exportar").click(function (event) {
    event.preventDefault(); // Impede o comportamento padrão do botão/link

    // Selecionar a tabela com ID Resultado ou AnaliticoGeral, se estiver visível
    const resultadoTable = document.getElementById("ResultadoDiario");
    const analiticoGeralTable = document.getElementById("AnaliticoGeral");
    let visibleTable = null;

    if (resultadoTable && resultadoTable.style.display !== 'none') {
      visibleTable = resultadoTable;
    } else if (analiticoGeralTable && analiticoGeralTable.style.display !== 'none') {
      visibleTable = analiticoGeralTable;
    }

    if (!visibleTable) {
      alert("Nenhuma tabela visível para exportar.");
      return;
    }

    // Chama a função da biblioteca para converter a tabela em Excel
    TableToExcel.convert(visibleTable, {
      name: 'FaturamentoPorContaFinanceira.xlsx',
      sheet: {
        name: 'Sheet1'
      }
    });
  });


  // Captura o botão de impressão pelo ID
  const btnImprimir = document.getElementById("btn-Imprimir");

  // Adiciona um ouvinte de evento de clique ao botão de impressão
  btnImprimir.addEventListener("click", function () {
    // Oculta a tabela Filtro antes de imprimir
    document.querySelector('.filter-fields').style.display = 'none';

    rowsToHide.forEach(row => {
      row.style.display = '';
    });

    // Restaura a exibição das colunas e linhas após a impressão
    const columnsToHide = []; // Define as colunas a serem ocultadas
    const rowsToHide = []; // Define as linhas a serem ocultadas

    columnsToHide.forEach(index => {
      document.querySelectorAll(`td:nth-child(${index}), th:nth-child(${index})`).forEach(cell => {
        cell.style.display = '';
      });
    });

    rowsToHide.forEach(row => {
      row.style.display = '';
    });

    // Restaura a exibição da tabela Filtro após a impressão
    document.querySelector('.filter-fields').style.display = 'block';

    // Remove o estilo de impressão após a impressão
    document.head.removeChild(style);
  });
});
