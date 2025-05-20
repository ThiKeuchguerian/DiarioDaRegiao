<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/UsuariosGestor.php';

$Titulo = 'Consulta Usários Gestor';
$URL = URL_PRINCIPAL . 'ti/UsuariosGestor.php';

// Instanciar a classe
$ConsultaUsuariosGestor = new UsuariosGestor();

if (isset($_POST['btn-buscar'])) {
  $UserStatus = $_POST['Status'];
  $NomeUser = $_POST['NomeUser'];
  // echo "<pre>";
  // var_dump($status);
  // die();

  $ConsultaUsuariosGestor = $ConsultaUsuariosGestor->buscarUsuarios($UserStatus, $NomeUser);
  $Qtde = COUNT($ConsultaUsuariosGestor);
} else if (isset($_POST['btn-processar'])) {
  $novaDataValidade = $_POST['DtValidadeSenha'];

  // echo "<pre>";
  // var_dump($novaDataValidade);
  // die();

  $AtualizaDataValidade = $ConsultaUsuariosGestor->atualizarDataValidadeSenha($novaDataValidade);

  $ConsultaUsuariosGestor = $ConsultaUsuariosGestor->buscarUsuarios($UserStatus === '3', $NomeUser);
  $Qtde = COUNT($ConsultaUsuariosGestor);
} else if (isset($_POST['btn-salvar'])) {
  $UserName = $_POST['UserName'];
  $Status = $_POST['Status'];
  $novaDataValidade = $_POST['DtValidadeSenha'];

  // echo "<pre>";
  // var_dump($Data);
  // die();

  $AtualizaUsuario = $ConsultaUsuariosGestor->atualizarDadosUsuario($UserName, $Status, $novaDataValidade);

  $ConsultaUsuariosGestor = $ConsultaUsuariosGestor->buscarUsuarios($UserStatus === '3', $NomeUser);
  $Qtde = COUNT($ConsultaUsuariosGestor);
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="menuFiltro" name="menuFiltro">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Status</strong>
            </div>
            <div class="col">
              <strong>Nome Usuário</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-control form-control-sm" name="Status" id="Status">
                <option value="1">--Selecione Status--</option>
                <option value="2">Inativo</option>
                <option value="3">Ativo</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="NomeUser" name="NomeUser" maxlength="20">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
        <?php if (isset($Qtde)) : ?>
          <!-- Adicionando Espaçamento -->
          <div class="card-footer mb-1"></div>
          <div class="card-header bg-primary text-white">
            <div class="row">
              <div class="col">
                <strong>Nova Data Validade</strong>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row justify-content-center">
              <div class="col">
                <input type="date" class="form-control form-control-sm" name="DtValidadeSenha">
              </div>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-end">
            <div class="col text-end">
              <button id="btn-processar" name="btn-processar" type="submit" class="btn btn-primary btn-sm">Processar</button>
              <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button>
            </div>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </div>
</div>

<!-- Adicionando Espaçamento -->
<div class="mb-3"></div>

<!-- Exibindo Resultado -->
<?php if (isset($Qtde)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <h5 class="card-header bg-primary text-white">
        Qtde. Total Usuários: <?= $Qtde ?>
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover Resultado" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th>Nome Completo</th>
              <th>Nome Usuário</th>
              <th>Situação</th>
              <th>Dt Validade Senha</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ConsultaUsuariosGestor as $key => $item): ?>
              <tr>
                <td><?= $item['Nome'] ?></td>
                <td><?= $item['codigoDoUsuario'] ?></td>
                <td><?= $item['Status'] ?></td>
                <td><?= $item['dataValidadeSenha'] ?></td>
                <td>
                  <button type="button" class="btn btn-primary btn-sm " onclick="openEditModal(<?= htmlspecialchars(json_encode($item)) ?>)">Editar</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Incluindo Modal -->
<?php
require_once __DIR__ . '/../includes/modals/usuarios_gestor.php';
?>

<!-- Incluindo JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/usuariosgestor.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>