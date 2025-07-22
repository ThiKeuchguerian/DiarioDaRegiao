document.addEventListener('DOMContentLoaded', function () {
  const btnAnalitico = document.getElementById('menu-analitico');
  const btnArtes = document.getElementById('menu-artes');
  const btnCirculacao = document.getElementById('menu-circulacao');
  const btnClassificados = document.getElementById('menu-classificados');
  const btnComercial = document.getElementById('menu-comercial');
  const btnContabilidade = document.getElementById('menu-contabilidade');
  const btnDistribuicao = document.getElementById('menu-distribuicao');
  const btnFinanceiro = document.getElementById('menu-financeiro');
  const btnGrafica = document.getElementById('menu-grafica');
  const btnAtex = document.getElementById('menu-atex');
  const btnIntegracoes = document.getElementById('menu-integracoes');
  const btnTi = document.getElementById('menu-ti');

  const btnMobileArtes = document.getElementById('menumobile-artes');
  const btnMobileAnalitico = document.getElementById('menumobile-analitico');
  const btnMobileCirculacao = document.getElementById('menumobile-circulacao');
  const btnMobileClassificados = document.getElementById('menumobile-classificados');
  const btnMobileComercial = document.getElementById('menumobile-comercial');
  const btnMobileContabilidade = document.getElementById('menumobile-contabilidade');
  const btnMobileDistribuicao = document.getElementById('menumobile-distribuicao');
  const btnMobileFinanceiro = document.getElementById('menumobile-financeiro');
  const btnMobileGrafica = document.getElementById('menumobile-grafica');
  const btnMobileAtex = document.getElementById('menumobile-atex');
  const btnMobileIntegracoes = document.getElementById('menumobile-integracoes');
  const btnMobileTi = document.getElementById('menumobile-ti');

  const offcanvasElement = document.getElementById('mobileMenu');
  const offcanvas = new bootstrap.Offcanvas(offcanvasElement);

  // Funções
  function ocultarOutrosConteudos() {
    document.querySelectorAll('.conteudo-pagina').forEach(conteudo => {
      conteudo.classList.add('d-none');
    });
  }

  function exibirConteudo(conteudoId) {
    document.querySelectorAll('.conteudo-pagina').forEach(conteudo => {
      conteudo.classList.add('d-none');
    });
    const conteudo = document.getElementById(conteudoId);
    conteudo.classList.remove('d-none');
  }

  // Event Listeners para o menu lateral
  if (btnAnalitico) {
    btnAnalitico.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-analitico')
    });
  }
  if (btnArtes) {
    btnArtes.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-artes')
    });
  }
  if (btnCirculacao) {
    btnCirculacao.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-circulacao')
    });
  }
  if (btnClassificados) {
    btnClassificados.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-classificados')
    });
  }
  if (btnComercial) {
    btnComercial.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-comercial')
    });
  }
  if (btnContabilidade) {
    btnContabilidade.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-contabilidade')
    });
  }
  if (btnDistribuicao) {
    btnDistribuicao.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-distribuicao')
    });
  }
  if (btnFinanceiro) {
    btnFinanceiro.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-financeiro')
    });
  }
  if (btnGrafica) {
    btnGrafica.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-grafica')
    });
  }
  if (btnAtex) {
    btnAtex.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-atex')
    });
  }
  if (btnIntegracoes) {
    btnIntegracoes.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-integracoes')
    });
  }
  if (btnTi) {
    btnTi.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-ti')
    });
  }

  // Event Listeners para o menu mobile
  btnMobileAnalitico.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-analitico');
    offcanvas.hide();
  });

  btnMobileArtes.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-artes');
    offcanvas.hide();
  });

  btnMobileCirculacao.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-circulacao');
    offcanvas.hide();
  });

  btnMobileClassificados.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-classificados');
    offcanvas.hide();
  });

  btnMobileComercial.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-comercial');
    offcanvas.hide();
  });

  btnMobileContabilidade.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-contabilidade');
    offcanvas.hide();
  });

  btnMobileDistribuicao.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-distribuicao');
    offcanvas.hide();
  });

  btnMobileFinanceiro.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-financeiro');
    offcanvas.hide();
  });

  btnMobileGrafica.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-grafica');
    offcanvas.hide();
  });

  btnMobileAtex.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-atex');
    offcanvas.hide();
  });

  btnMobileIntegracoes.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-integracoes');
    offcanvas.hide();
  });

  btnMobileTi.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-ti');
    offcanvas.hide();
  });
});
// Fim do código 