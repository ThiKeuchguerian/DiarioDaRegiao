$(document).ready(function () {
  $("#btn-exportar").on("click", function (e) {
    e.preventDefault();
    var htmlContent = '<html><head><meta charset="UTF-8"></head><body>';
    // Supondo que cada tabela a exportar possua a classe "resultado"
    $(".Resultado").each(function () {
      htmlContent += $(this).prop("outerHTML") + "<br><br>";
    });
    htmlContent += "</body></html>";

    // Cria um data URI para Excel
    var uri = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(htmlContent);

    // Cria um link temporário com atributo download e simula o clique nele
    var link = document.createElement("a");
    link.href = uri;
    link.style.visibility = "hidden";
    link.download = "AnunciosWebTake.xls";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Restaura a exibição da tabela Filtro após a impressão
    document.querySelector('.filter-fields').style.display = 'block';
  });
});
