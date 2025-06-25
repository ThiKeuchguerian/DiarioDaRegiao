<div class="modal fade" tabindex="-1" id="ModalUserGestor" aria-labelledby="EditModalUserGestor" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditModalUserGestor">Editar Usuário Gestor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="UsuariosGestor.php" method="post">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col">
              <label for="Nome" class="form-label fw-bold">Nome Completo</label>
              <input type="text" style="background-color: #e9ecef;" class="form-control" id="Nome" name="Nome" required readonly>
            </div>
            <div class="col">
              <label for="UserName" class="form-label fw-bold">Usuário</label>
              <input type="text" style="background-color: #e9ecef;" class="form-control" id="UserName" name="UserName" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="Status" class="form-label fw-bold">Situação</label>
              <select class="form-control" name="Status" id="Status" require>
                <option value="1">--Selecione Status--</option>
                <option value="2">Inativo</option>
                <option value="3">Ativo</option>
              </select>
            </div>
            <div class="col">
              <label for="DtValidadeSenha" class="form-label fw-bold">Dt Validade Senha</label>
              <input type="date" class="form-control" id="DtValidadeSenha" name="DtValidadeSenha" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Fechar</button>
            <button type="submit" class="btn btn-primary btn-sm" id="btn-salvar" name="btn-salvar">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>