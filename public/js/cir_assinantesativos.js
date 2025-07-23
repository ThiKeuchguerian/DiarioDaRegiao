$(document).ready(function () {
  $("#btn-exportar").click(function () {
    const table = document.getElementById("Resultado");
    debugger;
    TableToExcel.convert(table, {
      name: `AssinantesAtivos.xlsx`,
      sheet: {
        name: 'AssinantesAtivos'
      }
    });
  });
});

document.getElementById('btn-buscar').addEventListener('click', function (e) {
  const dtInicio = document.getElementById('dtInicio').value.trim();
  const dtFim = document.getElementById('dtFim').value.trim();
  const mesCad = document.getElementById('mesCad').value.trim();

  // Se mesCad informado, datas não podem ser informadas
  if (mesCad && (dtInicio || dtFim)) {
    alert('Se "Mês Cadastro" for informado, as datas não podem ser preenchidas.');
    e.preventDefault();
    return;
  }

  // Se dtInicio ou dtFim informado, ambos devem ser preenchidos e mesCad não pode ser informado
  if ((dtInicio || dtFim) && (!dtInicio || !dtFim)) {
    alert('Se uma das datas for informada, a outra também deve ser preenchida.');
    e.preventDefault();
    return;
  }

  if ((dtInicio || dtFim) && mesCad) {
    alert('Se datas forem informadas, "Mês Cadastro" não pode ser preenchido.');
    e.preventDefault();
    return;
  }
});