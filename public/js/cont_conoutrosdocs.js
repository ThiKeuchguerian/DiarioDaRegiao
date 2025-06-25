$(document).ready(function () {
  $('#btn-buscar').on('click', function (event) {
    // Supondo que o campo MesAno tenha o id "MesAno"
    var numNota = $('#NumNota').val();

    // Verifica se o campo está igual a "0" ou 0
    if (numNota == "" || parseInt(numNota) === '') {
      alert('Favor informe o número da nota !!');
      event.preventDefault(); // Cancela o evento do botão se estiver em um formulário
      return false;
    }
  });
});
