document.addEventListener('DOMContentLoaded', function () {

  // Trecho java script VerificaIntCliVendAgen.php
  // Variáveis - Botões
  const BuscarCli = document.getElementById('BuscarCli');
  const BuscarAge = document.getElementById('BuscarAg');
  const BuscarVend = document.getElementById('BuscarVend');

  // Variáveis - Campos
  const CNPJAgen = document.getElementById('CNPJAgen');
  const NomeVend = document.getElementById('NomeVend');

  //Variáveis - Tabelas
  const TipoIntegracao = document.getElementById('TipoIntegracao');
  const ClienteDiv = document.getElementById('Cliente');
  const AgenciaDiv = document.getElementById('Agencia');
  const VendedorDiv = document.getElementById('Vendedor');

  //Tonar os campos obrigatórios quando clicar no botão buscar
  BuscarCli.addEventListener('click', function () {
    CPFCNPJ.setAttribute('required', 'required');
  });
  BuscarAge.addEventListener('click', function () {
    CNPJAgen.setAttribute('required', 'required');
  });
  BuscarVend.addEventListener('click', function () {
    NomeVend.setAttribute('required', 'required');
  });


  // Função para exibir a tabela
  TipoIntegracao.addEventListener('change', function () {
    const selectedValue = TipoIntegracao.value;

    ClienteDiv.style.display = 'none';
    AgenciaDiv.style.display = 'none';
    VendedorDiv.style.display = 'none';

    // Mostrar a tabela correspondente com base na seleção
    if (selectedValue === '1') {
      ClienteDiv.style.display = 'block';
    } else if (selectedValue === '2') {
      VendedorDiv.style.display = 'block';
    } else if (selectedValue === '3') {
      AgenciaDiv.style.display = 'block';
    }
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const editModalEl = document.getElementById('editModal');
  const editForm = document.getElementById('editForm');
  const btnSave = document.getElementById('btnSave');
  // inicializa instância do modal (Bootstrap 5)
  const modal = new bootstrap.Modal(editModalEl);

  // mapa de data-attributes → IDs de campos
  const fieldMap = {
    id: 'editId',
    sistem: 'editSistem',
    codcliente: 'editCodCliente',
    razaosocial: 'editRazaoSocial',
    cpfcnpj: 'editCpfCnpj',
    tipo: 'editTipo', // <select>
    codvendedor: 'editCodVendedor'
  };

  // ao abrir o modal (evento do Bootstrap)
  editModalEl.addEventListener('show.bs.modal', e => {
    const data = e.relatedTarget.dataset;       // dataset com todas as props

    // preenche cada campo conforme o mapa
    for (let key in fieldMap) {
      const el = document.getElementById(fieldMap[key]);
      const val = data[key] || '';

      if (el.tagName === 'select') {
        // percorre as options e marca a que bate com o value (ou com o texto)
        Array.from(el.options).forEach(opt => {
          opt.selected = opt.value === val;       // se value do option for numérico
          // opt.selected = opt.text === val;     // ou, se você receber o texto 'Física'/'Jurídica'
        });
      }
      else {
        el.value = val;
      }
    }
  });

  // no envio do form, dispara AJAX
  // editForm.addEventListener('submit', async e => {
  //   e.preventDefault();
  //   btnSave.disabled = true;

  //   try {
  //     const resp = await fetch(editForm.action, {
  //       method: 'POST',
  //       body: new FormData(editForm)
  //     });
  //     if (!resp.ok) throw new Error(`Status ${resp.status}`);
  //     const text = await resp.text();
  //     // opcional: analisar JSON de resposta:
  //     // const json = await resp.json();

  //     // fecha modal e avisa sucesso (pode usar Toast do Bootstrap)
  //     modal.hide();
  //     alert('Cliente atualizado com sucesso!');
  //     // opcional: recarregar tabela ou linha específica
  //   }
  //   catch (err) {
  //     console.error(err);
  //     alert('Erro ao salvar dados: ' + err.message);
  //   }
  //   finally {
  //     btnSave.disabled = false;
  //   }
  // });
});

$(document).ready(function () {
  // Permitir apenas números no campo editCodCliente
  $('#editCodCliente').on('keypress', function (event) {
    var charCode = event.which ? event.which : event.keyCode;
    if (charCode < 48 || charCode > 57) {
      event.preventDefault();
    }
  });
  // Permitir apenas números no campo editCodVendedor
  $('#editCodVendedor').on('keypress', function (event) {
    var charCode = event.which ? event.which : event.keyCode;
    if (charCode < 48 || charCode > 57) {
      event.preventDefault();
    }
  });
});