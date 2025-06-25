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
    var Dep = $("select[name='Deposito']").val();
    var familia = $("select[name='Familia']").val();
    var mesAno = $("input[name='MesAno']").val();
    if (Dep === "0" && anoMes === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor, preencha o campo Deposito e MesAno ou Só MesAno no formato YYYYMM.");
    } else if (Dep !== "0" && mesAno === "") {
      event.preventDefault();
      alert("Por favor, preencha o campo MesAno no formato MM/YYYY.");
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
function getFamiliaDeposito(codDep) {
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
      action: 'getFamilia',
      CODDEP: codDep
    },
    success: function (data) {
      // console.log(data);

      // let produtos = JSON.parse(data);
      $('#Familia').empty(); // Limpa as opções atuais
      $('#Familia').append('<option value="0">Todos</option>'); // Adiciona a opção "Todos"
      data.forEach(function (produto) {
        $('#Familia').append('<option value="' + produto.CODFAM + '">' + produto.CODFAM + ' - ' + produto.DESFAM + '</option>');
        // console.log("Produto adicionado:", produto);
      });
    },
    error: function (xhr, status, erro) {
      console.error("Resposta do servidor:", xhr.responseText);
      alert("Erro ao carregar Familias:\n" + erro);
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


