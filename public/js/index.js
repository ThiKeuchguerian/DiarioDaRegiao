document.addEventListener('DOMContentLoaded', function () {
  const btnCirculacao = document.getElementById('menu-circulacao');
  const btnDistribuicao = document.getElementById('menu-distribuicao');
  const btnClassificados = document.getElementById('menu-classificados');
  const btnComercial = document.getElementById('menu-comercial');
  const btnContabilidade = document.getElementById('menu-contabilidade');
  const btnFinanceiro = document.getElementById('menu-financeiro');
  const btnGrafica = document.getElementById('menu-grafica');
  const btnAtex = document.getElementById('menu-atex');
  const btnArtes = document.getElementById('menu-artes');
  const btnTi = document.getElementById('menu-ti');

  const btnMobileCirculacao = document.getElementById('menumobile-circulacao');
  const btnMobileDistribuicao = document.getElementById('menumobile-distribuicao');
  const btnMobileClassificados = document.getElementById('menumobile-classificados');
  const btnMobileComercial = document.getElementById('menumobile-comercial');
  const btnMobileContabilidade = document.getElementById('menumobile-contabilidade');
  const btnMobileFinanceiro = document.getElementById('menumobile-financeiro');
  const btnMobileGrafica = document.getElementById('menumobile-grafica');
  const btnMobileAtex = document.getElementById('menumobile-atex');
  const btnMobileArtes = document.getElementById('menumobile-artes');
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
  if (btnCirculacao) {
    btnCirculacao.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-circulacao')
    });
  }
  if (btnDistribuicao) {
    btnDistribuicao.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-distribuicao')
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
  if (btnContabilidade) {
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
  if (btnArtes) {
    btnArtes.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-artes')
    });
  }
  if (btnTi) {
    btnTi.addEventListener('click', () => {
      ocultarOutrosConteudos();
      exibirConteudo('conteudo-ti')
    });
  }

  // Event Listeners para o menu mobile
  btnMobileCirculacao.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-circulacao');
    offcanvas.hide();
  });

  btnMobileDistribuicao.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-distribuicao');
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

  btnMobileArtes.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-artes');
    offcanvas.hide();
  });

  btnMobileTi.addEventListener('click', () => {
    ocultarOutrosConteudos();
    exibirConteudo('conteudo-ti');
    offcanvas.hide();
  });
});
// Fim do código