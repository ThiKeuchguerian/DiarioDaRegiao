$(document).ready(function() {
  // Máscara para MesAno
  $('#MesAno').mask('00/0000');
  
  // Máscara para CPF/CNPJ - aceita apenas números
  $('#cpfcnpj').on('input', function() {
    var val = $(this).val().replace(/\D/g, '');
    $(this).val(val); // força apenas números no campo
    if (val.length <= 11) {
      $(this).mask('000.000.000-00', {reverse: true});
    } else {
      $(this).mask('00.000.000/0000-00', {reverse: true});
    }
  });
});