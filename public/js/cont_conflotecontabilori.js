$(document).ready(function () {
  $('#btn-buscar').click(function (event) {
    const emp = $('#CodEmp')
    const mesAno = $('#MesAno').val().trim();
    const origem = $('#Origem').val().trim();

    if (emp === '0' || mesAno === '' || origem === '0') {
      alert('Por favor, preencha todos os campos para pesquesa !!!');
      event.preventDefault();
      return false;
    }
  });
});

// Utilizando delegação de eventos caso os elementos sejam carregados dinamicamente
$(document).on('click', '.toggle-summary', function () {
  // Seleciona a tabela mais próxima e alterna todos os elementos com a classe toggle-details
  $(this).closest('table').find('.toggle-details').toggle();
});