document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form');
  const nomeAss = document.getElementById('nomeAss');
  const emailAss = document.getElementById('emailAss');
  const btnBuscar = document.getElementById('btn-buscar');

  form.addEventListener('submit', function (e) {
    // Remove espaços em branco antes de validar
    const nomeValue = nomeAss.value.trim();
    const emailValue = emailAss.value.trim();

    if (nomeValue === '' && emailValue === '') {
      e.preventDefault();
      alert('Preencha pelo menos um dos campos: Nome Assinante ou E-mail do Assinante.');
      // Opcional: foco no primeiro campo
      nomeAss.focus();
    }
  });
});