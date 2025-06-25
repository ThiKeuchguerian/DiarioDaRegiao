// Preenchimento do Modal
const modal = $("#modal");

const btnEditModal = $('#btn-edit-modal');
$(document).on('click', '#btn-edit-modal', function () {
  $("#modal-title").text("Editar items");

  $('#numped').val($(this).attr("data-numped"));
  $('#pedcli').val($(this).attr("data-pedcli"));
  $('#codcli').val($(this).attr("data-codcli"));
  $('#ctafin').val($(this).attr("data-ctafin"));
  $('#ctared').val($(this).attr("data-ctared"));
  $('#codccu').val($(this).attr("data-codccu"));
  $('#codfam').val($(this).attr("data-codfam"));
  $('#codser').val($(this).attr("data-codser"));
  $('#tnsser').val($(this).attr("data-tnsser"));
  $('#nomcli').val($(this).attr("data-nomcli"));
  $('#desser').val($(this).attr("data-desser"));
});
