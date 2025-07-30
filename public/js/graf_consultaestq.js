

$(document).ready(function () {
  // Adiciona a máscara ao campo de MesAno
  $('#mesAno').mask('00/0000');

  $("#btn-buscar").click(function (event) {
    var Dep = $("select[name='Deposito']").val();
    var familia = $("select[name='Familia']").val();
    var MesAno = $("input[name='MesAno']").val();
    if (Dep === "0" && MesAno === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor, preencha o campo Deposito e MesAno no formato MM/YYYY.");
    } else if (Dep !== "0" && MesAno === "") {
      event.preventDefault();
      alert("Por favor, preencha o campo MesAno no formato MM/YYYY.");
    }
  });



  // Adiciona o evento de clique ao botão de exportação
  $("#btn-exportar").on("click", function (e) {
    e.preventDefault();
    var htmlContent = '<html><head><meta charset="UTF-8"></head><body>';
    // Supondo que cada tabela a exportar possua a classe "resultado"
    $(".resultado").each(function () {
      htmlContent += $(this).prop("outerHTML") + "<br><br>";
    });
    htmlContent += "</body></html>";

    // Cria um data URI para Excel
    var uri = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(htmlContent);

    // Cria um link temporário com atributo download e simula o clique nele
    var link = document.createElement("a");
    link.href = uri;
    link.style.visibility = "hidden";
    link.download = "exportacao.xls";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });
});


function getFamiliaDeposito(codDep) {
  $.ajax({
    url: '',              // mesmo arquivo PHP
    type: 'GET',
    dataType: 'json',     // interpreta a resposta como JSON
    data: {
      action: 'getFamilia',
      CODDEP: codDep
    },
    success: function (data) {
      $('#Familia').empty(); // Limpa as opções atuais
      $('#Familia').append('<option value="0">Todos</option>'); // Adiciona a opção "Todos"
      data.forEach(function (fam) {
        $('#Familia').append('<option value="' + fam.CODFAM + '">' + fam.CODFAM + ' - ' + fam.DESFAM + '</option>');
      });
    },
    error: function (xhr, status, erro) {
      console.error("Resposta do servidor:", xhr.responseText);
      alert("Erro ao carregar Familias:\n" + erro);
    }
  });
}