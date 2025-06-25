$(document).ready(function () {
  $('#btn-buscar').on('click', function (event) {
    // Supondo que o campo Ano tenha o id "Ano"
    var valorAno = $('#Ano').val();

    // Verifica se o campo está igual a "0" ou 0
    if (valorAno == "0" || parseInt(valorAno) === 0) {
      alert('Favor selecionar um Ano !!');
      event.preventDefault(); // Cancela o evento do botão se estiver em um formulário
      return false;
    }
  });
});

function toggleDetails(vendedor) {
  var rows = document.querySelectorAll('.detail-row[data-vendedor="' + vendedor + '"]');
  rows.forEach(row => {
    row.classList.toggle('hidden');
  });
}