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
  // console.log(codDep);
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
    success: function (data) {
      // console.log(data);

      // let produtos = JSON.parse(data);
      $('#Produto').empty(); // Limpa as opções atuais
      $('#Produto').append('<option value="0">Todos</option>'); // Adiciona a opção "Todos"
      data.forEach(function (produto) {
        $('#Produto').append('<option value="' + produto.CODPRO + '">' + produto.CODPRO + ' - ' + produto.DESPRO + '</option>');
        // console.log("Produto adicionado:", produto);
      });
    },
    error: function (xhr, status, erro) {
      console.error("Resposta do servidor:", xhr.responseText);
      alert("Erro ao carregar produtos:\n" + erro);
    }
  });
}
