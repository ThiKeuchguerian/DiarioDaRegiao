document.addEventListener('DOMContentLoaded', function () {
  // Exportar para Excel via TableToExcel
  $("#Exportar").click(function () {
    const table = document.getElementById("Comissao");
    TableToExcel.convert(table, {
      name: `ComissaoGrafica.xlsx`,
      sheet: {
        name: 'ComissaoGrafica'
      }
    });
  });

  // Exibe os elementos de cabeçalho e rodapé antes e depois da impressão
  window.onbeforeprint = function () {
    document.getElementById('header').style.display = 'block';
    document.getElementById('footer').style.display = 'block';
  };

  // window.onafterprint = function () {
  //   document.getElementById('header').style.display = 'block';
  //   document.getElementById('footer').style.display = 'block';
  // };

  // Torna os campos DtInicial e DtFinal obrigatórios ao clicar no botão Buscar
  const BuscarButton = document.getElementById('btn-buscar');
  const DtInicial = document.getElementById('DtInicial');
  const DtFinal = document.getElementById('DtFinal');
  // CodVend não é manipulado aqui, mas caso precise pode ser utilizado 

  BuscarButton.addEventListener('click', function () {
    DtInicial.setAttribute('required', 'required');
    DtFinal.setAttribute('required', 'required');
  });

  window.onafterprint = function () {
    // Dá um tempo curto para garantir que a impressão foi finalizada
    setTimeout(function () {
      window.location.reload();
    }, 30);
  };

  // Evento para o botão de imprimir
  const btnImprimir = document.getElementById("btn-imprimir");
  btnImprimir.addEventListener("click", function () {
    window.print();
  });

}); 