$(document).ready(function () {
  $("#btn-exportar").click(function () {
    const table = document.getElementById("FaturamentoGrafica");
    const clonedTable = table.cloneNode(true);

    // Remove as colunas além da 14ª
    const rows = clonedTable.querySelectorAll('tr');
    rows.forEach(row => {
      const cells = row.querySelectorAll('th, td');
      for (let i = cells.length - 1; i >= 14; i--) {
        row.removeChild(cells[i]);
      }
    });

    // Remove a linha que contém o formulário Altera
    const formRow = clonedTable.querySelector('tr:has(form#Altera)');
    if (formRow) {
      formRow.remove();
    }

    TableToExcel.convert(clonedTable, {
      name: `FaturamentoGrafica.xlsx`,
      sheet: {
        name: 'FaturamentoGrafica'
      }
    });
  });

  // Adicionar evento de clique ao botão Exportar
  $("#btn-exportar").click(function (event) {
    event.preventDefault();
    var tables = document.querySelectorAll('table[id^="FaturamentoGrafica"]');
    tables.forEach(function (table, index) {
      exportTableToExcel(table.id, 'FaturamentoGrafica_' + (index + 1));
    });
  });

  // Validação do botão "Resumo"
  $("#btn-resumo").click(function (event) {
    var DtInicial = $("input[name='dtInicio']").val();
    var DtFinal = $("input[name='dtFim']").val();

    if (DtInicial === "" && DtFinal === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione um período !!!");
    } else if (DtInicial !== "" && DtFinal === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione um peírodo !!!");
    }
  });

  // Validação do botão "Buscar"
  $("#btn-buscar").click(function (event) {
    var DtInicial = $("input[name='dtInicio']").val();
    var DtFinal = $("input[name='dtFim']").val();
    var Cliente = $("input[name='cliente']").val();
    var Arte = $("input[name='arte']").val();
    var Tipo = $("select[name='tipo']").val();
    var Faturado = $("select[name='faturado']").val();

    if (DtInicial === "" && DtFinal === "" && Cliente === "" && Arte === "" && Tipo === "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal !== "" && Cliente === "" && Arte === "" && Tipo === "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal === "" && Cliente !== "" && Arte === "" && Tipo === "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal === "" && Cliente === "" && Arte !== "" && Tipo === "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal === "" && Cliente === "" && Arte === "" && Tipo !== "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal === "" && Cliente === "" && Arte === "" && Tipo === "" && Faturado !== "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    }
  });

  // Validação do botão "Relatório"
  $("#btn-relatorio").click(function (event) {
    var DtInicial = $("input[name='dtInicio']").val();
    var DtFinal = $("input[name='dtFim']").val();
    var Cliente = $("input[name='cliente']").val();
    var Arte = $("input[name='arte']").val();
    var Tipo = $("select[name='tipo']").val();
    var Faturado = $("select[name='faturado']").val();

    if (DtInicial === "" && DtFinal === "" && Cliente === "" && Arte === "" && Tipo === "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal !== "" && Cliente === "" && Arte === "" && Tipo === "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal === "" && Cliente !== "" && Arte === "" && Tipo === "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal === "" && Cliente === "" && Arte !== "" && Tipo === "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal === "" && Cliente === "" && Arte === "" && Tipo !== "" && Faturado === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (DtInicial === "" && DtFinal === "" && Cliente === "" && Arte === "" && Tipo === "" && Faturado !== "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    }
  });
  // Captura o botão de impressão pelo ID
  const btnImprimir = document.getElementById("btn-imprimir");

  // Adiciona um ouvinte de evento de clique ao botão de impressão
  btnImprimir.addEventListener("click", function () {
    // Oculta a tabela Filtro antes de imprimir
    document.querySelector('.filter-fields').style.display = 'none';

    // Oculta as colunas e linhas indesejadas antes de imprimir
    const columnsToHide = [15, 16];
    const rowsToHide = document.querySelectorAll('tr:has(th[colspan="10"]), tr:has(button[name="btn-faturado"]), tr:has(button[name="btn-duplicate"])');

    columnsToHide.forEach(index => {
      document.querySelectorAll(`td:nth-child(${index}), th:nth-child(${index})`).forEach(cell => {
        cell.style.display = 'none';
      });
    });

    rowsToHide.forEach(row => {
      row.style.display = 'none';
    });
    // Define o formato de impressão para paisagem com margem mínima
    const style = document.createElement('style');
    style.innerHTML = '@page { size: landscape; margin: 2.5mm; } body { font-size: 12px; margin: 2.5mm; }';
    document.head.appendChild(style);

    // Abre a janela de impressão do navegador
    window.print();

    // Restaura a exibição das colunas e linhas após a impressão
    columnsToHide.forEach(index => {
      document.querySelectorAll(`td:nth-child(${index}), th:nth-child(${index})`).forEach(cell => {
        cell.style.display = '';
      });
    });

    rowsToHide.forEach(row => {
      row.style.display = '';
    });

    // Restaura a exibição da tabela Filtro após a impressão
    document.querySelector('.filter-fields').style.display = 'block';

    // Remove o estilo de impressão após a impressão
    document.head.removeChild(style);
  });

  // Formata o Campo do Modal editDataRodagem
  $('#editDataRodagem').on('input', function () {
    var input = $(this).val();
    if (/^\d{2}$/.test(input)) {
      $(this).val(input + '/');
    } else if (/^\d{2}\/\d{2}$/.test(input)) {
      $(this).val(input + '/');
    }
  });

  // Permitir apenas números no campo editDataRodagem
  $('#editDataRodagem').on('keypress', function (event) {
    var charCode = event.which ? event.which : event.keyCode;
    if (charCode < 48 || charCode > 57) {
      event.preventDefault();
    }
  });

  // Formata o Campo do Modal editTiragem
  $('#editTiragem').on('input', function () {
    var input = $(this).val();
    input = input.replace(/\D/g, ''); // Remove todos os caracteres não numéricos
    // input = input.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Adiciona ponto a cada milhar
    $(this).val(input);
  });

  // Permitir apenas números no campo editTiragem
  $('#editTiragem').on('keypress', function (event) {
    var charCode = event.which ? event.which : event.keyCode;
    if (charCode < 48 || charCode > 57) {
      event.preventDefault();
    }
  });

  // Permitir apenas números e ponto no campo editValor
  $('#editValor').on('input', function () {
    var input = $(this).val().replace(/[^0-9.-]/g, ''); // Remove todos os caracteres não numéricos exceto ponto e hífen
    if (input.indexOf('-') > 0) {
      input = input.replace(/-/g, ''); // Remove hífen se não estiver no início
    }
    var parts = input.split('.');
    if (parts.length > 2) {
      input = parts[0] + '.' + parts[1].substring(0, 2); // Limita a dois dígitos após o ponto
    } else if (parts.length === 2) {
      parts[1] = parts[1].substring(0, 2); // Limita a dois dígitos após o ponto
      input = parts.join('.');
    }
    $(this).val(input);
  });

  // Permitir apenas números no campo IncluirTiragem
  $('#IncluirTiragem').on('keypress', function (event) {
    var charCode = event.which ? event.which : event.keyCode;
    if (charCode < 48 || charCode > 57) {
      event.preventDefault();
    }
  });

  // Permitir apenas números no campo IncluirValor
  $('#IncluirValor').on('keypress', function (event) {
    var input = $(this).val().replace(/[^0-9.]/g, ''); // Remove todos os caracteres não numéricos exceto ponto
    var parts = input.split('.');
    if (parts.length > 2) {
      input = parts[0] + '.' + parts[1].substring(0, 2); // Limita a dois dígitos após o ponto
    } else if (parts.length === 2) {
      parts[1] = parts[1].substring(0, 2); // Limita a dois dígitos após o ponto
      input = parts.join('.');
    }
    $(this).val(input);
  });

  // Validação do botão Alterar Data
  $("#btn-alterarData").click(function (event) {
    var selected = $("input[name='selected[]']:checked").length;
    var novaData = $("#NovaData").val();
    if (selected === 0 && !novaData) {
      event.preventDefault();
      alert("Por favor, selecione pelo menos um registro e/ou preencha a nova data para alterar!");
    }
  });

  // Validação do botão Faturado
  $("#btn-faturado").click(function (event) {
    var selected = $("input[name='selected[]']:checked").length;
    if (selected === 0) {
      event.preventDefault();
      alert("Selecione pelo menos um registro para faturar!");
    }
  });

  // Validação do botão Duplicar
  $("#btn-duplicate").click(function (event) {
    var selected = $("input[name='selected[]']:checked").length;
    var novaData = $("#NovaData").val();
    if (selected === 0 && !novaData) {
      event.preventDefault();
      alert("Selecione pelo menos um registro ou preencha a nova data para duplicar!");
    }
  });
});

// Exibi tabela secundária
function toggleDetails(Tipo) {
  var rows = document.querySelectorAll('.detail-row[data-tipo="' + Tipo + '"]');
  rows.forEach(row => {
    row.classList.toggle('hidden');
  });
}

// Preenchimento Modal EditModal
function openEditModal(item) {
  $('#editID').val(item.ID);
  $('#editDiaSemana').val(item.DiaSemana);
  // Converte DataRodagem para formato d/m/Y antes de exibir
  if (item.DataRodagem) {
    const dateParts = item.DataRodagem.split('-');
    if (dateParts.length === 3) {
      $('#editDataRodagem').val(`${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`);
    } else {
      $('#editDataRodagem').val(item.DataRodagem);
    }
  } else {
    $('#editDataRodagem').val('');
  }
  $('#editCliente').val(item.Cliente);
  $('#editArte').val(item.Arte);
  $('#editTipo').val(item.Tipo);
  $('#editFormato').val(item.Formato);
  $('#editPapel').val(item.Papel);
  $('#editQtdeCor').val(item.QtdeCor);
  $('#editTiragem').val(item.Tiragem);
  $('#editValor').val(item.Valor);
  $('#editFaturado').val(item.Faturado);
  $('#editObs').val(item.Obs);
  $('#editNumPedido').val(item.NumPedido);
  $('#editNumPedCli').val(item.NumPedCli);
  $('#editModal').modal('show');
}

$('#editForm').on('submit', function (e) {
  e.preventDefault();
  // Adicione a lógica para salvar as alterações aqui
  $('#editModal').modal('hide');
});

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

// Validação do formulário com os Botões Alterar Data / Faturado / Duplicar
function validateForm(event) {
  var selectedIds = document.getElementById('selected_ids').value; // Acha os IDs selecionados (supondo que esteja em algum lugar)
  var novaData = document.getElementById('NovaData').value;
  var checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#selectAll)');
  var atLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

  // Verifica qual botão foi clicado
  var targetButton = event.submitter;

  if (targetButton.id === 'AlterarData') {
    if (!atLeastOneChecked && !novaData) {
      alert('Por favor, selecione pelo menos um checkbox e preencha a nova data.');
      return false; // Impede o envio do formulário
    }
  }

  if (targetButton.id === 'Faturado') {
    if (!atLeastOneChecked) {
      alert('Por favor, selecione pelo menos um checkbox.');
      return false; // Impede o envio do formulário
    }
  }

  if (targetButton.id === 'duplicate') {
    if (!atLeastOneChecked && !novaData) {
      alert('Por favor, selecione pelo menos um checkbox ou preencha a nova data.');
      return false; // Impede o envio do formulário
    }
  }

  return true; // Permite o envio do formulário
}