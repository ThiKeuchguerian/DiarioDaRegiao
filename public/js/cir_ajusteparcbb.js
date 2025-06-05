document.addEventListener('DOMContentLoaded', function () {
  const incluirButton = document.getElementById('btn-incluir');
  const deletarButton = document.getElementById('btn-excluir');
  const processarButton = document.getElementById('btn-processar');
  const numParcelaInput = document.getElementById('numParc');
  const numContratoInput = document.getElementById('numCon');
  const dtSelecionadaDate = document.getElementById('dtSelecionada');

  incluirButton.addEventListener('click', function () {
    // Torna os campos obrigatórios quando o botão "Incluir" é clicado
    numParcelaInput.setAttribute('required', 'required');
    numContratoInput.setAttribute('required', 'required');
  });
  deletarButton.addEventListener('click', function () {
    // Torna os campos obrigatórios quando o botão "Excluir" é clicado
    numParcelaInput.setAttribute('required', 'required');
    numContratoInput.setAttribute('required', 'required');
  });
  processarButton.addEventListener('click', function () {
    // Torna os campos obrigatórios quando o botão "Excluir" é clicado
    dtSelecionadaDate.setAttribute('required', 'required');
  });
});

document.addEventListener('keydown', function (evt) {
  if (evt.altKey && !evt.shiftKey && !evt.ctrlKey) {
    switch (evt.key.toLowerCase()) {
      case 'i': document.getElementById('btn-incluir').click(); break;
      case 'b': document.getElementById('btn-buscar').click(); break;
      case 'e': document.getElementById('btn-excluir').click(); break;
    }
  }
});