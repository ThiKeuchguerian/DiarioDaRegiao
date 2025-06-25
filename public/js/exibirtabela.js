// ao clicar em uma linha resumo, alterna todos os detalhes até o próximo resumo
document.querySelectorAll('.toggle-summary').forEach(function (row) {
  row.addEventListener('click', function () {
    let next = row.nextElementSibling;
    while (next && !next.classList.contains('toggle-summary')) {
      next.style.display = (next.style.display === 'none' ? '' : 'none');
      next = next.nextElementSibling;
    }
  });
});