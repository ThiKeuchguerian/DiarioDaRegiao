$(document).ready(function () {
  $("#Exportar").click(function () {
    const table = document.getElementById("Resultado");
    debugger;
    TableToExcel.convert(table, {
      name: `UsuariosGestor.xlsx`,
      sheet: {
        name: 'UsuariosGestor'
      }
    });
  });
});

// Preenchimento do Modal
function openEditModal(item) {
  // converte "DD/MM/YYYY" → "YYYY-MM-DD"
  let raw = item.dataValidadeSenha;      // ex: "28/04/2025"
  if (/^\d{2}\/\d{2}\/\d{4}$/.test(raw)) {
    const [d, m, y] = raw.split('/');
    raw = `${y}-${m}-${d}`;
  }
  if (item.Status === 'Ativo') {
    Status = '3'
  } else {
    Status = '2'
  }
  console.log(Status);
  $('#Nome').val(item.Nome);
  $('#UserName').val(item.codigoDoUsuario);
  $('#Status').val(Status);
  $('#DtValidadeSenha').val(raw);
  $('#ModalUserGestor').modal('show');
}

$('#editForm').on('submit', function (e) {
  e.preventDefault();
  // Adicione a lógica para salvar as alterações aqui
  $('#ModalUserGestor').modal('hide');
});