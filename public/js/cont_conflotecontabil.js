$(document).ready(function () {
  $('#btn-buscar').click(function (event) {
    const emp = $('#CodEmp').val().trim();
    const mesAno = $('#MesAno').val().trim();

    if (emp === '0' && mesAno === '') {
      alert('Por favor, preencha os campos para pesquisa !!!');
      event.preventDefault();
      return false;
    }
    else if (emp === '0' && mesAno !== '') {
      alert('Por favor, selecione a Empresa !!!');
      event.preventDefault();
      return false;
    }
    else if (emp !== '0' && mesAno === '') {
      alert('Por favor, informe o Mes e Ano para pesquisa !!!');
      event.preventDefault();
      return false;
    }
  });
});

$(document).on('click', '.toggle-summary', function () {
  // Seleciona a tabela mais próxima e alterna todos os elementos com a classe toggle-details
  $(this).closest('table').find('.toggle-details').toggle();
});

