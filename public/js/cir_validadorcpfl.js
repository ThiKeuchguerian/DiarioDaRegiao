$(document).ready(function () {
  $("#btn-buscar").click(function (e) {
    const dtInicio = $("#DtInicial").val();
    const arqcpfl = $("#arqcpfl").val();
    // Permite se: dtInicio preenchido (com ou sem dtFim) OU arqcpfl preenchido
    if (!dtInicio && !arqcpfl) {
      alert("Preencha Data Inicio ou selecione um arquivo.");
      e.preventDefault();
      return false;
    }
    // Se dtInicio estiver preenchido ou arqcpfl estiver preenchido, permite buscar
    if (dtInicio || arqcpfl) {
      // Permite a busca, não faz nada aqui
      return true;
    }
  });
  $("#btn-exportar").click(function () {
    const table = document.getElementById("Resultado");
    debugger;
    TableToExcel.convert(table, {
      name: `ValidadorCPFL.xlsx`,
      sheet: {
        name: 'ValidadorCPFL'
      }
    });
  });
});
