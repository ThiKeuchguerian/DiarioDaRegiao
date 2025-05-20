document.addEventListener('DOMContentLoaded', function () {
  // Variáveis - Botões
  const Buscar = document.getElementById('btn-buscar');

  // Variáveis - Campos
  const MesAno = document.getElementById('MesAno');
  const Empresa = document.getElementById('Empresa');
  const Origem = document.getElementById('Origem'); // Campo a ser validado

  // Torna os campos obrigatórios quando clicar no botão buscar
  Buscar.addEventListener('click', function (event) {
    MesAno.setAttribute('required', 'required');
    Empresa.setAttribute('required', 'required');
    Origem.setAttribute('required', 'required');

    // Expressão regular para verificar se o campo contém apenas letras maiúsculas
    const regexMaiusculas = /^[A-Z]+$/;

    if (!regexMaiusculas.test(Origem.value)) {
      alert('O campo Origem deve conter somente letras maiúsculas.');
      event.preventDefault(); // Impede o envio do formulário
    }
  });
});
// Utilizando delegação de eventos caso os elementos sejam carregados dinamicamente
$(document).on('click', '.toggle-summary', function () {
  // Seleciona a tabela mais próxima e alterna todos os elementos com a classe toggle-details
  $(this).closest('table').find('.toggle-details').toggle();
});