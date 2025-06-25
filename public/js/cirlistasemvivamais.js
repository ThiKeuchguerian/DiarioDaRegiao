$(document).ready(function () {
  $("#btn-exportar").click(function () {
    event.preventDefault(); // Impede o comportamento padrão de um link

    const table = document.getElementById("Resultado");
    TableToExcel.convert(table, {
      name: `ListagemSemVivaMais.xlsx`,
      sheet: {
          name: 'ListagemSemVivaMais'
      }
    });
  });
});