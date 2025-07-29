$(document).ready(function () {
  $("#Exportar").click(function () {
    const table = document.getElementById("GraficaProducao");
    const clonedTable = table.cloneNode(true);

    // Remove a última coluna que contém o botão Editar
    const rows = clonedTable.querySelectorAll('tr');
    rows.forEach(row => {
      const cells = row.querySelectorAll('th, td');
      if (cells.length > 0) {
        row.removeChild(cells[cells.length - 1]);
      }
    });

    TableToExcel.convert(clonedTable, {
      name: `ProducaoGrafica.xlsx`,
      sheet: {
        name: 'ProducaoGrafica'
      }
    });
  });

  // Validação do botão "Buscar"
  $("#btn-buscar").click(function (event) {
    var dtInicio = $("input[name='dtInicio']").val();
    var dtFim = $("input[name='dtFim']").val();
    var caderno = $("input[name='caderno']").val();

    if (dtInicio === "" && dtFim === "" && caderno === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (dtInicio === "" && dtFim !== "" && caderno === "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (dtInicio === "" && dtFim === "" && caderno !== "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    } else if (dtInicio === "" && dtFim !== "" && caderno !== "") {
      event.preventDefault();
      alert("Filtro inválido. Por favor selecione pelo menos Data Inicial");
    }
  });

  // Máscara para os campos do modal Editar de graf_producao.php
  $('#EditarDataProducao').mask('00/00/0000');
  $('#EditarQtdeChapa').mask('00');
  $('#EditarTiragemLiquida').mask('0000000');
  $('#EditarTiragemBruta').mask('0000000');
  $('#EditarHoraInicio').mask('00:00:00');
  $('#EditarHoraFim').mask('00:00:00');
  $('#EditarNumeroOP').mask('000000');
  $('#EditarKilo').mask('0000');

  // Máscara para os campos do modal Incluir de graf_producao.php
  $('#IncluirQtdeChapa').mask('00');
  $('#IncluirTiragemLiquida').mask('0000000');
  $('#IncluirTiragemBruta').mask('0000000');
  $('#IncluirHoraInicio').mask('00:00:00');
  $('#IncluirHoraFim').mask('00:00:00');
  $('#IncluirNumeroOP').mask('000000');
  $('#IncluirKilo').mask('0000');

  // Não permitir Tiragem Bruta menor que Tiragem Liquida ao clicar no BtnIncluirModal
  $('#BtnIncluirModal').click(function (event) {
    var TiragemLiquida = $('#IncluirTiragemLiquida').val();
    var TiragemBruta = $('#IncluirTiragemBruta').val();
    if (parseInt(TiragemBruta) < parseInt(TiragemLiquida)) {
      event.preventDefault();
      alert('Tiragem Bruta não pode ser menor que Tiragem Líquida');
    }
  });
  // // Captura o botão de impressão pelo ID
  // const btnImprimir = document.getElementById("btn-imprimir");

  // // Adiciona um ouvinte de evento de clique ao botão de impressão
  // btnImprimir.addEventListener("click", function () {
  //   // Oculta a tabela Filtro antes de imprimir
  //   document.querySelector('.filter-fields').style.display = 'none';

  //   // Oculta as linhas que contém o <h5>
  //   const rowsToHide = document.querySelectorAll('h5');
  //   rowsToHide.forEach(row => {
  //     row.style.display = 'none';
  //   });

  //   // Oculta a última coluna que contém o botão Editar
  //   const columnsToHide = [19];
  //   columnsToHide.forEach(index => {
  //     document.querySelectorAll(`td:nth-child(${index}), th:nth-child(${index})`).forEach(cell => {
  //       cell.style.display = 'none';
  //     });
  //   });

  //   // Define o formato de impressão para paisagem com margem mínima e aumenta a fonte
  //   const style = document.createElement('style');
  //   style.innerHTML = '@page { size: landscape; } body { font-size: 11px; }';
  //   document.head.appendChild(style);

  //   // Abre a janela de impressão do navegador
  //   window.print();
  // });

  // Não permitir Tiragem Bruta menor que Tiragem Liquida ao clicar no BtnIncluirModal
  $('#BtnIncluirModal').click(function (event) {
    var TiragemLiquida = $('#IncluirTiragemLiquida').val();
    var TiragemBruta = $('#IncluirTiragemBruta').val();
    if (parseInt(TiragemBruta) < parseInt(TiragemLiquida)) {
      event.preventDefault();
      alert('Tiragem Bruta não pode ser menor que Tiragem Líquida');
    }
  });
});

function openEditarModal(item) {
  // Formata a data de Y-m-d para d/m/Y
  const dataOriginal = item.DataProducao;
  let dataFormatada = '';
  if (dataOriginal && dataOriginal.includes('-')) {
    const partes = dataOriginal.split('-');
    dataFormatada = `${partes[2]}/${partes[1]}/${partes[0]}`; // d/m/Y
  }

  // Formata hora (garante sempre no formato HH:mm)
  const formatarHora = (hora) => {
    if (!hora) return '';
    const partes = hora.split(':');
    if (partes.length < 2) return hora;
    const hh = partes[0].padStart(2, '0');
    const mm = partes[1].padStart(2, '0');
    const ss = partes[2].padStart(2, '0');
    return `${hh}:${mm}`;
  };

  const horaInicioFormatada = formatarHora(item.HoraInicio);
  const horaFimFormatada = formatarHora(item.HoraFim);

  $('#EditarModal input[name="ID"]').val(item.ID);
  $('#EditarModal input[name="DataProducao"]').val(dataFormatada);
  $('#EditarModal input[name="Caderno"]').val(item.Caderno);
  $('#EditarModal select[name="Papel"]').val(item.Papel);
  $('#EditarModal select[name="Gramatura"]').val(item.Gramatura);
  $('#EditarModal input[name="QtdeChapa"]').val(item.QtdeChapa);
  $('#EditarModal select[name="TrocaBobina"]').val(item.TrocaBobina);
  $('#EditarModal select[name="QuebraPapel"]').val(item.QuebraPapel);
  $('#EditarModal select[name="DefeitoChapa"]').val(item.DefeitoChapa);
  $('#EditarModal select[name="Maquina"]').val(item.Maquina);
  $('#EditarModal input[name="TiragemLiquida"]').val(item.TiragemLiq);
  $('#EditarModal input[name="TiragemBruta"]').val(item.TiragemBru);
  $('#EditarModal input[name="HoraInicio"]').val(horaInicioFormatada);
  $('#EditarModal input[name="HoraFim"]').val(horaFimFormatada);
  $('#EditarModal input[name="Kilo"]').val(item.Kilo);
  $('#EditarModal input[name="NumeroOP"]').val(item.NumeroOP);
  $('#EditarModal textarea[name="Obs"]').val(item.Obs);
  $('#EditarModal').modal('show');

  let ultimoBotaoClicado = undefined;

  $('#BtnExcluirModal').click(function (event) {
    ultimoBotaoClicado = 'BtnExcluirModal'
  });

  $('#BtnSalvarModal').click(function (event) {
    ultimoBotaoClicado = 'BtnSalvarModal'
  });

  // Ação e excluir do modal
  $('#Editar').on('submit', function (e) {
    e.preventDefault(); // Impede o envio normal e recarregamento da página

    const botaoClicado = `&${ultimoBotaoClicado}=true`

    $.ajax({
      url: $(this).attr('action'), // Arquivo PHP sendo requisitado
      type: 'POST',
      data: $(this).serialize() + botaoClicado,
      success: (res) => {
        try {
          if (res.startsWith('<')) {
            document.body.innerHTML = res  ;
            $('#EditarModal').modal('hide');
            return
          }
          
          eval(res);
        } catch (error) {
          $('#EditarModal').modal('hide');
        }
      }
    })
  });
}

function fetchProductByFamily(codFam) {
  $.ajax({
    url: '', // A chamada será feita para o mesmo arquivo
    type: 'GET',
    dataType: 'json',
    data: {
      action: 'getProdutos',
      codfam: codFam
    },
    success: onSucess,
    error: onError
  });

  function onSucess(produtos) {
    const $sel = $('#IncluirGramatura');
    $sel.empty().append('<option value=""></option>'); // Adiciona a opção vazia
    produtos.forEach(function (produto) {
      $sel.append(
        '<option value="' + produto.CODPRO + ' - ' + produto.DESPRO + '">' +
        produto.CODPRO + ' - ' + produto.DESPRO +
        '</option>'
      );
    });
  }
  function onError(xhr, status, err) {
    // Para debugar, veja no console exatamente o que veio do servidor:
    console.error('XHR status:', xhr.status);
    console.error('responseText:', xhr.responseText);
    alert('Erro ao carregar os produtos. Veja o console para detalhes.');
  }
}