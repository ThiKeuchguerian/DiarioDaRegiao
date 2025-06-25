$(document).ready(function () {
  $('#btn-buscar').click(function (event) {
    const mesAno = $('#MesAno').val().trim();
    const codProduto = $('#CodProduto').val().trim();

    if (mesAno === '' && codProduto === '') {
      alert('Por favor, preencha os campos MesAno e Produto.');
      event.preventDefault();
      return false;
    }
    else if (mesAno !== '' && codProduto === '') {
      alert('Por favor, preencha o campo Produto.');
      event.preventDefault();
      return false;
    }
    else if (mesAno === '' && codProduto !== '') {
      alert('Por favor, preencha o campo MesAno.');
      event.preventDefault();
      return false;
    }
  });
  $("#btn-exportar").click(function () {
    event.preventDefault(); // Impede o comportamento padrão de um link

    const table = document.getElementById("Resultado");
    TableToExcel.convert(table, {
      name: 'VendasPorEquipe.xlsx',
      sheet: {
        name: 'VendasPorEquipe'
      }
    });
  });
});