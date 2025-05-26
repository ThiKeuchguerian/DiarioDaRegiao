document.addEventListener('DOMContentLoaded', function () {
  const BuscarButton = document.getElementById('Buscar');
  const CorrigirButton = document.getElementById('Corrigir');
  const ZeraRetButton = document.getElementById('ZeraRet');
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
  ZeraRetButton.addEventListener('click', function () {
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
// const formEditClient = $('#editFormCli');
btnEditClientModal.click(function () {
  $("#modal-title").text("Editar Cliente");
  $("#section-edit-client").show();
  $("#section-edit-nota").hide();
  $("#section-edit-itens").hide();

  $('#CodCli').val(btnEditClientModal.attr("data-codcli"));
  $('#NomeCli').val(btnEditClientModal.attr("data-nomcli"));
  $('#TribICMS').val(btnEditClientModal.attr("data-TICMS"));
  $('#TribIPI').val(btnEditClientModal.attr("data-TIPI"));
  $('#TribPIS').val(btnEditClientModal.attr("data-TPIS"));
  $('#TribCofins').val(btnEditClientModal.attr("data-TCOFINS"));
  $('#RetIR').val(btnEditClientModal.attr("data-IR"));
  $('#RetCSLL').val(btnEditClientModal.attr("data-CSLL"));
  $('#RetPIS').val(btnEditClientModal.attr("data-PIS"));
  $('#RetCofins').val(btnEditClientModal.attr("data-COFINS"));
  $('#OutrasRet').val(btnEditClientModal.attr("data-OutrasR"));
  $('#RetProd').val(btnEditClientModal.attr("data-RetPro"));
});

const btnEditNotaModal = $('#btn-edit-nota-modal');
// const formEditNota = $('#form-edit-nota');
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
const btn = $(this);
// const editFormItens = $('from-edit-item');
btnEditItensModal.click(function () {
  $("#modal-title").text("Editar items");
  $("#section-edit-client").hide();
  $("#section-edit-nota").hide();
  $("#section-edit-itens").show();

  $('#editCodEmp').val(btnEditItensModal.attr("data-codemp"));
  $('#editNumNota').val(btnEditItensModal.attr("data-numnfv"));
  $('#editTipo').val(btnEditItensModal.attr("data-tipo"));
  $('#editSeq').val(btnEditItensModal.attr("data-seqisv"));
  $('#editVlrBaseNota').val(btnEditItensModal.attr("data-vlrlse"));
  $('#editVlrBaseIR').val(btnEditItensModal.attr("data-vlrbir"));
  $('#editPercIR').val(btnEditItensModal.attr("data-perirf"));
  $('#editVlrIR').val(btnEditItensModal.attr("data-vlrirf"));
  $('#editVlrBaseCSLL').val(btnEditItensModal.attr("data-vlrbcl"));
  $('#editPercCSLL').val(btnEditItensModal.attr("data-percsl"));
  $('#editVlrCSLL').val(btnEditItensModal.attr("data-vlrcsl"));
  $('#editVlrBasePIS').val(btnEditItensModal.attr("data-vlrbpt"));
  $('#editPercPIS').val(btnEditItensModal.attr("data-perpit"));
  $('#editVlrPIS').val(btnEditItensModal.attr("data-vlrpit"));
  $('#editVlrBaseCOFINS').val(btnEditItensModal.attr("data-vlrbct"));
  $('#editPercCOFINS').val(btnEditItensModal.attr("data-percrt"));
  $('#editVlrCOFINS').val(btnEditItensModal.attr("data-vlrcrt"));
  $('#editVlrTotal').val(btnEditItensModal.attr("data-vlrtotal"));
});
