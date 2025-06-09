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
function toggleDetails(codPro) {
  var row = document.getElementById("detail-" + codPro);
  if (row.style.display === "none" || row.style.display === "") {
    row.style.display = "table-row";
  } else {
    row.style.display = "none";
  }
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

document.addEventListener("DOMContentLoaded", function () {
  const btnImprimir = document.getElementById("btn-imprimir");

  if (btnImprimir) {
    btnImprimir.addEventListener("click", function () {
      // Oculta filtros diretamente com classe CSS
      document.querySelector('.filter-fields')?.classList.add('no-print');

      // Mostra linhas escondidas (por exemplo: .detail-row.hidden)
      const linhasDetalhes = document.querySelectorAll('.detail-row.hidden');
      linhasDetalhes.forEach(row => {
        row.classList.remove('hidden');
      });

      // Cria estilo de impressão temporário
      const style = document.createElement('style');
      // Define o conteúdo CSS que será aplicado
      style.innerHTML = `
        @media print {
          @page {
            size: A4 portrait;
            margin: 10mm;
          }

          body {
            font-size: 8px;
            margin: 0;
            padding: 0;
          }

          .filter-fields,
          .no-print {
            display: none !important;
          }

          table {
            width: 100% !important;
            border-collapse: collapse;
          }

          th, td {
            padding: 4px;
            font-size: 8px;
            word-break: break-word;
          }
        }
      `;

      document.head.appendChild(style);

      /// Após imprimir, restauramos o layout original sem recarregar
      window.onafterprint = function () {
        // Restaura filtro
        if (filtro) filtro.classList.remove('no-print');

        // Restaura as linhas ocultas
        linhasOcultas.forEach(row => row.classList.add('hidden'));

        // Remove estilo temporário
        document.head.removeChild(style);
      };

      // Inicia impressão
      window.print();
    });
  }
});


