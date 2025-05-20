
// Trecho para o modal da FilaIntegracoes
const EditModal = $("#EditModal");
$(document).on('click', '#BtnEditPedGrafEas', function () {
  $('#EditNumPed').val($(this).attr("data-NumPed"));
  $('#EditFlProc').val($(this).attr("data-FlProc"));
  $('#EditCodCli').val($(this).attr("data-CodCli"));
  $('#EditTipoCli').val($(this).attr("data-TipoCli"));
  $('#EditDtGera').val($(this).attr("data-DtGera"));
  $('#EditLote').val($(this).attr("data-Lote"));
  $('#EditOrigem').val($(this).attr("data-Origem"));
  $('#EditPedidoS').val($(this).attr("data-PedidoS"));
  $('#EditVlrPedido').val($(this).attr("data-VlrPedido"));
  $('#EditVlrParc').val($(this).attr("data-VlrParc"));
  $('#EditMen').val($(this).attr("data-Men"));
});

const ModalPedidoProtheus = $("#ModalPedidoProtheus");
$(document).on('click', '#BtnEditPedProtheus', function () {
  $('#EditFlProcProtheus').val($(this).attr("data-FPro"));
  $('#EditCodPed').val($(this).attr("data-Num"));
  $('#EditCodCliProtheus').val($(this).attr("data-CodCliente"));
  $('#EditDtEmiProtheus').val($(this).attr("data-DtEmi"));
  $('#EditDtGeraProtheus').val($(this).attr("data-DtGe"));
  $('#EditCodVenProtheus').val($(this).attr("data-CodVen"));
  $('#EditPedidoProtheus').val($(this).attr("data-PedProtheus"));
  $('#EditLoteProtheus').val($(this).attr("data-LoteProtheus"));
  $('#EditIntegracaoProtheus').val($(this).attr("data-PedInt"));
  $('#EditMenProtheus').val($(this).attr("data-Erro"));
});
// Definindo o foco no botão após mostrar o modal
$('#ModalPedidoProtheus').on('shown.bs.modal', function () {
  $('#ExcluirPedidoProtheus').focus(); // Coloca o foco no botão "Excluir"
});

const ModalPedidoCapt = $("#ModalPedidoCapt");
$(document).on('click', '#BtnEditPedCapt', function () {
  $('#EditLotePed').val($(this).attr("data-LoteCapt"));
  $('#EditAPCapt').val($(this).attr("data-NConCapt"));
  $('#EditDtEmiCapt').val($(this).attr("data-DtEmCapt"));
  $('#EditCodCliCapt').val($(this).attr("data-CliCapt"));
  $('#EditCpfCnpjCapt').val($(this).attr("data-CpfCnpjCapt"));
  $('#EditTituloCapt').val($(this).attr("data-TituloCapt"));
  $('#EditCodVenCapt').val($(this).attr("data-VendCapt"));
  $('#EditCodAgCapt').val($(this).attr("data-AgCapt"));
  $('#EditVlrAPCapt').val($(this).attr("data-VlrCapt"));
  $('#EditFlProcCApt').val($(this).attr("data-StatusCapt"));
  $('#EditMenCapt').val($(this).attr("data-MenErroCapt"));
});

const ModalClienteProtheus = $("#ModalClienteProtheus");
$(document).on('click', '#BtnEditClienteProtheus', function () {
  $('#EditFlProcCliProtheus').val($(this).attr("data-FlagPro"));
  $('#EditCodigoCliProtheus').val($(this).attr("data-CodCliPro"));
  $('#EditNomCliProtheus').val($(this).attr("data-NomCli"));
  $('#EditCpfCnpjProtheus').val($(this).attr("data-CpfCnpj"));
  $('#EditIEProtheus').val($(this).attr("data-IE"));
  $('#EditTipoCliProtheus').val($(this).attr("data-TipoReg"));
  $('#EditMenErroProtheus').val($(this).attr("data-MensagemErro"));
});