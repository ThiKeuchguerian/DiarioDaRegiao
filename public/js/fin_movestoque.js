

$(document).ready(function () {
  $("#btn-exportar").click(function () {
    event.preventDefault(); // Impede o comportamento padrão de um link

    const table = document.getElementById("Resultado");
    TableToExcel.convert(table, {
      name: 'HistoricoMovimentacaoEstoque.xlsx',
      sheet: {
        name: 'HistoricoMovimentacaoEstoque'
      }
    });
  });

  $("#btn-buscar").click(function (event) {
    if ($("#Deposito").val() == "0" || $("#DtInicio").val() == "" || $("#DtFim").val() == "") {
      alert("Por favor, selecione um depósito.");
      event.preventDefault();
    }
  });
});

// Liga/Desliga linhas de detalhe
function toggleDetails(CodPro) {
  document
    .querySelectorAll('.detail-row[data-CodPro="' + CodPro + '"]')
    .forEach(row => row.classList.toggle('hidden'));
}

// Carrega produtos via Ajax
function getProdutoDeposito(codDep) {
  // Se quiser resetar quando CODDEP for 0:
  if (codDep === '0') {
    $('#Produto')
      .empty()
      .append('<option value="0">Todos</option>');
    return;
  }

  $.ajax({
    url: '',              // mesmo arquivo PHP
    type: 'GET',
    dataType: 'json',     // interpreta a resposta como JSON
    data: {
      action: 'getProdutos',
      CODDEP: codDep
    },
    success: function (produtos) {
      // popula o select...
    },
    error: function (xhr, status, erro) {
      console.error("Resposta do servidor:", xhr.responseText);
      alert("Erro ao carregar produtos:\n" + erro);
    }
  });
}

// // Captura o botão de impressão pelo ID
// const btnImprimir = document.getElementById("btn-Imprimir");

// // Adiciona um ouvinte de evento de clique ao botão de impressão
// btnImprimir.addEventListener("click", function () {
//   // Oculta a tabela Filtro antes de imprimir
//   document.querySelector('.filter-fields').style.display = 'none';

//   // Define o formato de impressão para retrato, margens mínimas e fonte menor
//   const style = document.createElement('style');
//   style.innerHTML = '@page { size: portrait; margin: 10mm; } body { font-size: 8px; }';
//   document.head.appendChild(style);

//   // Abre a janela de impressão do navegador
//   window.print();

//   // Restaura a exibição das colunas e linhas após a impressão
//   columnsToHide.forEach(index => {
//     document.querySelectorAll(`td:nth-child(${index}), th:nth-child(${index})`).forEach(cell => {
//       cell.style.display = '';
//     });
//   });

//   rowsToHide.forEach(row => {
//     row.style.display = '';
//   });

//   // Restaura a exibição da tabela Filtro após a impressão
//   document.querySelector('.filter-fields').style.display = 'block';

//   // Remove o estilo de impressão após a impressão
//   document.head.removeChild(style);
// });