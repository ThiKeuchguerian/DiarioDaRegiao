const ctx = document.getElementById('graficoCidades').getContext('2d');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: cidadesLabels.map((nomeCidade) => nomeCidade.trim()),  // Ex: ['São José do Rio Preto', 'Fernandópolis', ...]
    datasets: [{
      label: 'Clientes',
      data: cidadesValores,  // Ex: [4200, 120, 80, ...]
      backgroundColor: 'rgba(54, 162, 235, 0.6)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1,
      borderRadius: 5,
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      datalabels: {
        anchor: 'end',
        align: 'end',
        clamp: true,
        color: '#000',
        font: {
          weight: 'bold',
          size: 12
        },
      },
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: context => `${context.parsed.x} cliente(s)`
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { precision: 0 }
      }
    }
    // scales: {
    //   x: {
    //     beginAtZero: true,
    //     ticks: {
    //       precision: 0
    //     },
    //     grid: {
    //       drawOnChartArea: false
    //     }
    //   },
    //   y: {
    //     ticks: {
    //       align: 'start', // Deixa o nome da cidade colado na barra
    //       crossAlign: 'near',
    //       font: {
    //         size: 14
    //       }
    //     },
    //     grid: {
    //       drawOnChartArea: false
    //     }
    //   }
    // }
  },
  plugins: [ChartDataLabels]
});

const ctxFaixa = document.getElementById('graficoFaixaEtaria').getContext('2d');

new Chart(ctxFaixa, {
  type: 'bar',
  data: {
    labels: faixasLabels,
    datasets: [{
      label: 'Clientes',
      data: faixasValores,
      backgroundColor: 'rgba(255, 99, 132, 0.6)',
      borderColor: 'rgba(255, 99, 132, 1)',
      borderWidth: 1,
      borderRadius: 5
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      datalabels: {
        anchor: 'end',
        align: 'end',
        clamp: true,
        color: '#000',
        font: {
          weight: 'bold',
          size: 12
        },
      },
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: context => `${context.parsed.y} cliente(s)`
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { precision: 0 }
      }
    }
  },
  plugins: [ChartDataLabels]
});