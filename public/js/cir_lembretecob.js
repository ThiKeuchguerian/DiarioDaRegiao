// Seleciona todos os checkboxes dentro da tabela atual
function toggleSelectAll(selectAllCheckbox, table) {
  const checkboxes = table.querySelectorAll('input[name="selected[]"]');
  // Marca ou desmarca todos os checkboxes conforme o estado do "Selecionar todos"
  checkboxes.forEach((checkbox) => {
    checkbox.checked = selectAllCheckbox.checked;
  });
  // Atualiza o campo hidden com os IDs selecionados
  setSelectedIds();
}

// Captura os valores dos checkboxes marcados
function setSelectedIds() {
  const selectedIds = Array.from(document.querySelectorAll('input[name="selected[]"]:checked')).map(cb => cb.value);
  document.getElementById('selected_ids').value = selectedIds.join(','); // Define o valor no campo hidden
}
document.querySelectorAll('input[name="selected[]"]').forEach(checkbox => {
  checkbox.addEventListener('change', setSelectedIds);
});

document.getElementById('btn-buscar').addEventListener('click', function (event) {
  const dtInicio = document.getElementById('dtInicio').value;
  const tipoCob = document.getElementById('tipoCob').value;

  if (!dtInicio) {
    alert('A Data Inicial é obrigatória.');
    event.preventDefault(); // Impede o envio do formulário
    return;
  }

  if (tipoCob && !dtInicio) {
    alert('Informe a Data Inicial ao selecionar um Tipo de Cobrança.');
    event.preventDefault(); // Impede o envio do formulário
    return;
  }
});