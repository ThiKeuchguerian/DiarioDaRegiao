function toggleDetails(nomeGrupo) {
  var rows = document.querySelectorAll('.detail-row[data-nomeGrupo="' + nomeGrupo + '"]');
  rows.forEach(function(row) {
    row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
  });
}

$(document).ready(function() {
  $("#btn-exportar").click(function() {
    const table = document.getElementById("CheckMetasComercial");
    debugger;
    TableToExcel.convert(table, {
      name: `CheckMetasComercial.xlsx`,
      sheet: {
        name: 'CheckMetasComercial'
      }
    });
  });
  
  $("btn-buscar").click(function() {
    const Buscar = document.getElementById('btn-buscar');
    const Ano = document.getElementById('Ano');
    //Tonar os campos obrigatórios quando clicar no botão buscar
    Buscar.addEventListener('click', function() {
      Ano.setAttribute('required', 'required');
    });
  });
});