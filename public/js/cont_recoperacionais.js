$(document).ready(function () {
  $('#btn-buscar').on('click', function (event) {
    // Supondo que o campo MesAno tenha o id "MesAno"
    var valorMesAno = $('#MesAno').val();

    // Verifica se o campo está igual a "0" ou 0
    if (valorMesAno == "0" || parseInt(valorMesAno) === 0) {
      alert('Favor selecionar um Mes/Ano !!');
      event.preventDefault(); // Cancela o evento do botão se estiver em um formulário
      return false;
    }
  });
});