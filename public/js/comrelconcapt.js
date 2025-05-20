$(document).ready(function () {
  $("#btn-exportar").click(function () {
    const tables = document.querySelectorAll("table[id]");
    const tableArray = Array.from(tables).map(table => {
      const clonedTable = table.cloneNode(true);
      const valueCells = clonedTable.querySelectorAll('td.align-right');
      valueCells.forEach(cell => {
        const span = cell.querySelector('span');
        if (span) {
          span.remove(); // Remove the "R$" span
        }
        cell.innerHTML = cell.innerHTML.trim();
      });
      return clonedTable.outerHTML;
    }).join('');
    const fullTable = document.createElement('table');
    fullTable.innerHTML = tableArray;

    TableToExcel.convert(fullTable, {
      name: `ContratosCapt.xlsx`,
      sheet: {
        name: 'ContratosCapt'
      }
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
  const BuscarButton = document.getElementById('Buscar');
  const DtInicial = document.getElementById('DtInicial');
  const DtFinal = document.getElementById('DtFinal');

  BuscarButton.addEventListener('click', function () {
    // Torna os campos obrigatórios quando o botão "Incluir" é clicado
    DtInicial.setAttribute('required', 'required');
    DtFinal.setAttribute('required', 'required');
  });
});


function getGrupoProduto(NomeGrupo) {
  $.ajax({
    url: '', // A chamada será feita para o mesmo arquivo
    type: 'GET',
    data: {
      action: 'getGrupo',
      nomeGrupo: NomeGrupo
    },
    success: function (data) {
      let Produto = JSON.parse(data);
      $('#Produto').empty(); // Limpa as opções atuais
      $('#Produto').append('<option value="">Todos</option>'); // Adiciona a opção "Todos"
      Produto.forEach(function (Produto) {
        $('#Produto').append('<option value="' + Produto.codProduto + '">' + Produto.Produto + '</option>');
      });
    },
    error: function () {
      alert('Erro ao carregar os Prdutos.');
    }
  });
}