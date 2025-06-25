document.addEventListener('DOMContentLoaded', function () {
  const btnBuscar = document.getElementById('btn-buscar');
  if (!btnBuscar) return;

  btnBuscar.addEventListener('click', function (event) {
    const codAssinante = document.getElementById('CodAssinante')?.value.trim();
    const numeroContrato = document.getElementById('NumeroContrato')?.value.trim();
    const emailAssinante = document.getElementById('EmailAssinante')?.value.trim();

    if (!codAssinante && !numeroContrato && !emailAssinante) {
      event.preventDefault();
      alert('Por favor, preencha pelo menos um dos campos: CodAssinante, NumeroContrato ou EmailAssinante.');
    }
  });
});
