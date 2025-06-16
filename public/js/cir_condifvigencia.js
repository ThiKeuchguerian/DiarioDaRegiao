document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form');
  const btnAnalitico = document.getElementById('btn-analitico');
  const btnBuscar = document.getElementById('btn-buscar');
  const numContrato = document.getElementById('numContrato');
  const mesAno = document.getElementById('MesAno');

  form.addEventListener('submit', function (e) {
    // Descobre qual botão foi clicado
    const activeElement = document.activeElement;

    // Validação para o botão Analítico
    if (activeElement === btnAnalitico) {
      if (!numContrato.value.trim() || mesAno.value.trim()) {
        alert('Para Analítico, preencha apenas o campo Nº Contrato e deixe Mês/Ano vazio.');
        e.preventDefault();
        return false;
      }
    }

    // Validação para o botão Buscar
    if (activeElement === btnBuscar) {
      if (!numContrato.value.trim() && !mesAno.value.trim()) {
      alert('Preencha o Nº Contrato ou o Mês/Ano.');
      e.preventDefault();
      return false;
      }
    }
  });
});