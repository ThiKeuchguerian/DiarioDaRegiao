<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <link rel="icon" href="<?= FAVICON ?>" type="image/x-icon">
  <title><?= SITE_TITLE ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.15/dist/xlsx.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bs5-toast@1.0.0/dist/bs5-toast.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <style>
    body {
      background: #f5f5f5;
      /* Cor de fundo padrão */
    }

    /* Define a altura padrão da navbar (56px é comum, pode ser ajustado) */
    .navbar.fixed-top {
      height: 65px;
    }

    /* Menu lateral (sidebar) fixo, posicionado abaixo da navbar */
    .sidebar {
      position: fixed;
      top: 65px;
      /* inicia abaixo da navbar */
      left: 0;
      width: 250px;
      height: calc(100vh - 65px);
      /* altura total da tela menos a navbar */
      background: rgb(33, 60, 129);
      /* Corrigido para formato correto */
      color: white;
      padding-top: 20px;
      transition: transform 0.3s ease-in-out;
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        width: 250px;
      }

      .main-content {
        margin-left: 0;
      }
    }

    /* Conteúdo principal: deslocado para a direita, abaixo da navbar */
    .main-content {
      margin-left: 250px;
      margin-top: 65px;
      /* espaçamento abaixo da navbar */
      transition: margin-left 0.3s ease-in-out;
      padding: 20px;
    }

    /* Adicionando estilo para o botão de menu mobile */
    @media (max-width: 768px) {
      .navbar {
        padding: 10px 0;
        /* Ajuste de padding para navbar em mobile */
      }
    }

    @media print {

      /* Se quiser paisagem e controle de margem fina */
      @page {
        size: A4 landscape;
        margin-top: 75px;
        margin: 10mm 10mm 10mm 10mm !important;
        /* top | right | bottom | left */
        /* ou 0 para sem margem, se a impressora/driver permitir */
      }

      /* Zera margens/paddings do html/body */
      html,
      body {
        margin: 5px 5px 5px 5px !important;
        padding: 0 !important;
        width: 100%;
        height: 100%;
        /* força usar toda a página */
      }

      /* Anula o container do Bootstrap */
      .container {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
      }

      /* Faz sua tabela ocupar 100% de largura também */
      table {
        width: 100% !important;
        margin: 0 !important;
        /* page-break-inside: avoid; */
      }

      /* Ajusta células e cabeçalhos */
      th,
      td {
        padding: 2px !important;
        /* reduza conforme precisar */
      }

      /* Caso você tenha um wrapper extra */
      #relatorio,
      .relatorio-wrapper {
        width: 100% !important;
        margin: 0mm !important;
        padding: 0mm !important;
      }

      /* Remove elementos indesejados */
      #header,
      #footer,
      .filter-fields,
      .form,
      .navbar,
      .no-print {
        display: none !important;
      }

      /* Remove os URLs dos links impressos */
      a[href]:after {
        content: none !important;
      }
    }

    li {
      font-size: 13px;
    }

    .content-only {
      margin: 1cm;
      margin-top: 2cm;
    }

    .containers {
      margin-top: 75px;
    }

    .container {
      padding: 0.3rem;
    }

    .card {
      border-radius: 10px;
    }

    th,
    td {
      font-size: 11px;
    }

    table {
      border-collapse: collapse;
    }

    form,
    input,
    select,
    textarea,
    label {
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      font-size: 11px;
      font-weight: bold;
    }

    div {
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      font-size: 11px;
    }

    /* Estilos para barra de tempo */
    #barra-container {
      width: 100%;
      background-color: #ddd;
      border: 1px solid #ccc;
      border-radius: 8px;
      /* bordas arredondadas */
      overflow: hidden;
      /* garante que a barra interna não extrapole */
      position: relative;
    }

    #progressBar {
      height: 15px;
      background-color: rgb(33, 60, 129);
      width: 100%;
      border-radius: 8px;
      /* opcional, pode ser menor que a do container */
      position: relative;
    }

    #timerText {
      position: absolute;
      width: 100%;
      left: 0;
      top: 0;
      text-align: center;
      line-height: 15px;
      color: white;
      font-weight: bold;
    }

    /* Fim do estilo para barra do tempo */
    .hidden {
      display: none;
    }

    /* Estilos para o calendário */
    .calendar {
      height: 1.5rem;
      /* Altura menor */
      width: 1.5rem;
      /* Largura menor */
      vertical-align: middle;
      /* Centralização vertical */
      text-align: center;
      /* Centralização horizontal */
      padding: 0;
      /* Remove espaçamento interno */
      font-size: 0.65rem;
      /* Texto menor, opcional */
      cursor: default;
    }

    /* Estilos utilizados em Demonsrativo de Apuração de Contribuições */
    .section-title {
      background-color: var(--bs-primary);
      color: white;
      padding: 5px;
      font-weight: bold;
    }

    .sub-section-title {
      background-color: var(--bs-primary);
      color: white;
      font-weight: bold;
      padding-left: 11px;
      padding: 3px;
    }

    .bg-yellow {
      background-color: rgb(243, 216, 134);
    }

    .bg-orange {
      background-color: rgb(214, 155, 115);
    }

    .value {
      text-align: right;
    }

    .border-box {
      border: 1px solid #ccc;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 8px;
    }

    canvas {
      max-width: 800px;
    }

    .descricao {
      font-size: 0.65rem;
      color: #aaa;
    }
  </style>
</head>

<body>
  <!-- Navbar fixa no topo (ocupa a linha inteira) -->
  <nav class="navbar fixed-top" style="background-color: rgb(33, 60, 129);">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <!-- Imagem no canto esquerdo -->
      <img src="<?= LOGO ?>" alt="Logo Esquerdo" style="max-width: 4%">

      <!-- Texto central da navbar -->
      <a href="<?= $URL ?>" class="link" style="text-decoration: none; color: white;">
        <span class="navbar-brand mb-0 h1" style="font-family: Lato; color: white; font-weight: bold;"><?= htmlspecialchars($Titulo) ?></span>
      </a>

      <!-- Imagem no canto direito -->
      <img src="<?= LOGO ?>" alt="Logo Direito" style="max-width: 4%" class="d-none d-md-block">

      <!-- Botão para mobile (será exibido somente em telas menores) -->
      <button class="btn btn-outline-light d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
        ☰ Menu
      </button>
    </div>
  </nav>