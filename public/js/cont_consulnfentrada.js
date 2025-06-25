$(document).ready(function () {
  ('#btn-buscar').on('click', function (event) {
    var codEmp = ('#CodEmp').val();
    var numNota = ('#NumNota').val();
    
    if (codEmp === '' && numNota === '') {
      alert('Favor informe a Empresa e Número da Nota !!');
      event.preventDefault();
      return false;
    } else if (codEmp !== '' && numNota === '') {
      alert('Favor informe o Número da Nota !!');
      event.preventDefault();
      return false;
    } else if (codEmp === '' && numNota !== '') {
      alert('Favor informe a Empresa !!');
      event.preventDefault();
      return false;
    }
  });
}); 
