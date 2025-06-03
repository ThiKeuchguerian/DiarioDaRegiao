<?php
// sidebar.php

// Esse array define o “id” que você usará depois no JS,
// o rótulo e, se for abrir em outra janela, um target.
$menuItems = [
  'artes'         => 'Artes',
  'circulacao'    => 'Circulação',
  'classificados' => 'Classificados',
  'comercial'     => 'Comercial',
  'contabilidade' => 'Contabilidade',
  'distribuicao'  => 'Distribuição',
  'financeiro'    => 'Financeiro',
  'grafica'       => 'Gráfica',
  'atex'          => 'Sistemas Atex',
  'ti'            => 'Dept. T.I.',
];

// atributos comuns de link
$linkAttrs = 'class="nav-link align-middle px-1.0" style="color: white;" href="#"';

?>

<!-- desktop -->
<div class="sidebar d-none d-md-flex flex-column">
  <ul class="nav flex-column" id="menu">
    <?php foreach ($menuItems as $key => $label): ?>
      <li class="nav-item">
        <a id="menu-<?= $key ?>" <?= $linkAttrs ?>><?= $label ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<!-- mobile off-canvas -->
<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="mobileMenu" style="background-color: rgb(33, 60, 129);">
  <div class="offcanvas-header">
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="nav flex-column" id="menumobile">
      <?php foreach ($menuItems as $key => $label): ?>
        <li class="nav-item">
          <a id="menumobile-<?= $key ?>" <?= $linkAttrs ?>><?= $label ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>