$(document).ready(function () {
  $('#btn-buscar').click(function (event) {
    const ano = $('#Ano').val().trim();
    const mesAno = $('#MesAno').val().trim();

    if (ano === '' && mesAno === '') {
      alert('Por favor, preencha um dos campos Ano ou MesAno.');
      event.preventDefault();
      return false;
    }
    else if (ano === '' && mesAno !== '') {
      alert('Por favor, preencha o campo Ano.');
      event.preventDefault();
      return false;
    }
  });
  $('#btn-analitico').click(function (event) {
    const ano = $('#Ano').val().trim();
    const mesAno = $('#MesAno').val().trim();

    if (ano === '' && mesAno === '') {
      alert('Por favor, preencha um dos campos Ano ou MesAno.');
      event.preventDefault();
      return false;
    }
    else if (ano !== '' && mesAno === '') {
      alert('Por favor, preencha o campo MesAno.');
      event.preventDefault();
      return false;
    }
  });
});