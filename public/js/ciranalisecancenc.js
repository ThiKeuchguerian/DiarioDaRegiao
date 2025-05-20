$(document).ready(function() {
  $("#btn-exportar").click(function() {
    event.preventDefault(); // Impede o comportamento padrão de um link

    const table = document.getElementById("Resultado");
    TableToExcel.convert(table, {
      name: 'AnaliseCanceladoEncerrado.xlsx',
      sheet: {
        name: 'AnaliseCanceladoEncerrado'
      }
    });
  });
  
  $("#btn-buscar").click(function(event) {
    const mesAno = $("#MesAno").val().trim();
    const codProduto = $("select[name='CodProduto']").val();

    if (!mesAno || !codProduto) {
      event.preventDefault(); // Impede o envio do formulário
      alert("Por favor, preencha os campos 'MesAno' e 'Produto' antes de buscar.");
    }
  });

  $("#btn-analitico").click(function(event) {
    const mesAno = $("#MesAno").val().trim();
    const codProduto = $("select[name='CodProduto']").val();

    if (!mesAno || !codProduto) {
      event.preventDefault(); // Impede o envio do formulário
      alert("Por favor, preencha os campos 'MesAno' e 'Produto' antes de buscar.");
    }
  });
});