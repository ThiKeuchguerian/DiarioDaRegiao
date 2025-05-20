// Função para formatar o tempo no formato mm:ss
function formatarTempo(segundos) {
  var minutos = Math.floor(segundos / 60);
  var segRem = segundos % 60;
  minutos = minutos < 10 ? "0" + minutos : minutos;
  segRem = segRem < 10 ? "0" + segRem : segRem;
  return minutos + ":" + segRem;
}

function iniciarBarraTempo() {
  var tempoTotal = 300; // 5 minutos em segundos
  var progressBar = document.getElementById("progressBar");
  var timerText = document.getElementById("timerText");

  var intervalo = setInterval(function() {
    tempoTotal--; // decrementa 1 segundo

    // Atualiza a barra com base no tempo restante
    var porcentagem = (tempoTotal / 300) * 100;
    progressBar.style.width = porcentagem + "%";

    // Atualiza o texto exibido com o tempo formatado
    timerText.textContent = formatarTempo(tempoTotal);

    // Quando o tempo acaba, para o intervalo e recarrega a página
    if (tempoTotal <= 0) {
      clearInterval(intervalo);
      console.log("Tempo esgotado. Recarregando a página...");
      window.location.reload();
      // Se não recarregar imediatamente, tenta novamente em 100ms
      setTimeout(function() {
        window.location.reload();
      }, 100);
    }
  }, 1000);
}

window.onload = iniciarBarraTempo;