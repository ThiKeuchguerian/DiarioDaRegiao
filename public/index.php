<?php
// 1) carrega as configurações
require_once __DIR__ . '/config/config.php';
$URL = URL_PRINCIPAL;
$Titulo = 'Grupo Diário da Região';

// 2) carrega o layout
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// 3) carrega o conteúdo dinâmico
$menuItems = $menuItems ?? []; // segurança
echo '<div class="main-content">';
foreach ($menuItems as $key => $label):
  echo '  <div id="conteudo-' . htmlspecialchars($key) . '" class="container-fluid d-none conteudo-pagina">';
  $file = __DIR__ . '/includes/contents/' . $key . '.php';
  if (file_exists($file)) {
    include $file;
  } else {
    echo "<p>Conteúdo \"$key\" não encontrado.</p>";
  }
  echo '  </div>';
endforeach;
echo '</div>';

// 4) footer
echo '<script src="js/index.js"></script>';
require_once __DIR__ . '/includes/footer.php';
