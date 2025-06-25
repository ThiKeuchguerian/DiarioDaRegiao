$(document).ready(function () {
  // Adiciona a máscara ao campo de Data de Publicação no formato dd/mm/YYYY
  $('#edit-DtPublicacao').mask('00/00/0000');
});

const editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  const id = button.getAttribute('data-id');
  const company = button.getAttribute('data-company');
  const title = button.getAttribute('data-title');
  const DtPublicacao = button.getAttribute('data-DtPublicacao')

  const modalIdInput = editModal.querySelector('#edit-id');
  const modalCompanyInput = editModal.querySelector('#edit-company');
  const modalTitleInput = editModal.querySelector('#edit-title');
  const modalDtPubInput = editModal.querySelector('#edit-DtPublicacao');

  modalIdInput.value = id;
  modalCompanyInput.value = company;
  modalTitleInput.value = title;
  modalDtPubInput.value = DtPublicacao;
});
$('#btn-buscar').on('click', function (e) {
  const empresa = $('#Empresa').val().trim();
  const titulo = $('#Titulo').val().trim();
  const dtInicio = $('#DtInicio').val().trim();
  const dtFim = $('#DtFim').val().trim();

  if (!empresa && !titulo && !dtInicio && !dtFim) {
    e.preventDefault();
    alert('Preencha pelo menos um dos campos: Empresa, Título, Período Inicial ou Período Final.');
  }
});
