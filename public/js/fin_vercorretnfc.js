document.addEventListener('DOMContentLoaded', function () {
  const BuscarButton = document.getElementById('btn-buscar');
  const CorrigirButton = document.getElementById('btn-corrigir');
  const CodEmpInput = document.getElementById('CodEmp');
  const NumNotaInput = document.getElementById('NumNota');

  BuscarButton.addEventListener('click', function () {
    // Torna os campos obrigatórios quando o botão "Incluir" é clicado
    CodEmpInput.setAttribute('required', 'required');
    NumNotaInput.setAttribute('required', 'required');
  });
  CorrigirButton.addEventListener('click', function () {
    // Torna os campos obrigatórios quando o botão "Excluir" é clicado
    CodEmpInput.setAttribute('required', 'required');
    NumNotaInput.setAttribute('required', 'required');
  });
});

// Validação para aceitar apenas um caractere 'S' ou 'N' em maiúsculas
document.querySelectorAll('.str-input').forEach(function (input) {
  input.addEventListener('input', function () {
    this.value = this.value.replace(/[^sSnN]/g, ''); // Remove caracteres que não sejam 'S' ou 'N'

    // Convertendo para maiúsculas e garantindo que apenas um caractere é aceito
    if (this.value.length > 0) {
      this.value = this.value.charAt(0).toUpperCase(); // Converte para maiúsculo
    }
  });
});

// Preenchimento do Modal
const modal = $("#modal");

const btnEditClientModal = $('#btn-edit-client-modal');
$(document).on('click', '#btn-edit-client-modal', function () {
  $("#modal-title").text("Editar Cliente");
  $("#section-edit-client").show();
  $("#section-edit-nota").hide();
  $("#section-edit-itens").hide();

  $('#CodCli').val($(this).attr("data-codcli"));
  $('#NomeCli').val($(this).attr("data-nomcli"));
  $('#TribICMS').val($(this).attr("data-TICMS"));
  $('#TribIPI').val($(this).attr("data-TIPI"));
  $('#TribPIS').val($(this).attr("data-TPIS"));
  $('#TribCofins').val($(this).attr("data-TCOFINS"));
  $('#RetIR').val($(this).attr("data-IR"));
  $('#RetCSLL').val($(this).attr("data-CSLL"));
  $('#RetPIS').val($(this).attr("data-PIS"));
  $('#RetCofins').val($(this).attr("data-COFINS"));
  $('#OutrasRet').val($(this).attr("data-OutrasR"));
  $('#RetProd').val($(this).attr("data-RetPro"));
});

const btnEditNotaModal = $('#btn-edit-nota-modal');
btnEditNotaModal.click(function () {
  $("#modal-title").text("Editar nota");
  $("#section-edit-client").hide();
  $("#section-edit-nota").show();
  $("#section-edit-itens").hide();

  $('#CodEmpresa').val(btnEditNotaModal.attr("data-codemp"));
  $('#NNota').val(btnEditNotaModal.attr("data-numnfv"));
  $('#NCodCli').val(btnEditNotaModal.attr("data-codcli"));
  $('#Tipo').val(btnEditNotaModal.attr("data-tipo"));
  $('#VlrBIR').val(btnEditNotaModal.attr("data-vlrbir"));
  $('#VlrIR').val(btnEditNotaModal.attr("data-vlrirf"));
  $('#VlrBCSLL').val(btnEditNotaModal.attr("data-vlrbcl"));
  $('#VlrCSLL').val(btnEditNotaModal.attr("data-vlrcsl"));
  $('#VlrBPIS').val(btnEditNotaModal.attr("data-vlrbpt"));
  $('#VlrPIS').val(btnEditNotaModal.attr("data-vlrpit"));
  $('#VlrBCofins').val(btnEditNotaModal.attr("data-vlrbct"));
  $('#VlrCofins').val(btnEditNotaModal.attr("data-vlrcrt"));
  $('#VlrTotal').val(btnEditNotaModal.attr("data-vlrtotal"));
});

const btnEditItensModal = $('#btn-edit-itens-modal');
$(document).on('click', '#btn-edit-itens-modal', function () {
  $("#modal-title").text("Editar items");
  $("#section-edit-client").hide();
  $("#section-edit-nota").hide();
  $("#section-edit-itens").show();

  $('#editCodEmp').val($(this).attr("data-codemp"));
  $('#editNumNota').val($(this).attr("data-numnfv"));
  $('#editTipo').val($(this).attr("data-tipo"));
  $('#editSeq').val($(this).attr("data-seqisv"));
  $('#editVlrBaseNota').val($(this).attr("data-vlrlse"));
  $('#editVlrBaseIR').val($(this).attr("data-vlrbir"));
  $('#editPercIR').val($(this).attr("data-perirf"));
  $('#editVlrIR').val($(this).attr("data-vlrirf"));
  $('#editVlrBaseCSLL').val($(this).attr("data-vlrbcl"));
  $('#editPercCSLL').val($(this).attr("data-percsl"));
  $('#editVlrCSLL').val($(this).attr("data-vlrcsl"));
  $('#editVlrBasePIS').val($(this).attr("data-vlrbpt"));
  $('#editPercPIS').val($(this).attr("data-perpit"));
  $('#editVlrPIS').val($(this).attr("data-vlrpit"));
  $('#editVlrBaseCOFINS').val($(this).attr("data-vlrbct"));
  $('#editPercCOFINS').val($(this).attr("data-percrt"));
  $('#editVlrCOFINS').val($(this).attr("data-vlrcrt"));
  $('#editVlrTotal').val($(this).attr("data-vlrtotal"));
});
