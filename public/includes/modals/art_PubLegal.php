<!-- Modal de Edição -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar Registro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="EditForm" method="post">
        <div class="modal-body">
          <input type="hidden" name="id" id="edit-id">
          <div class="mb-3">
            <label for="edit-company" class="form-label">Empresa</label>
            <input type="text" class="form-control" id="edit-company" name="company">
          </div>
          <div class="mb-3">
            <label for="edit-title" class="form-label">Título</label>
            <input type="text" class="form-control" id="edit-title" name="title">
          </div>
          <div class="mb-3">
            <label for="edit-DtPublicacao" class="form-label">Data Publicação</label>
            <input type="text" class="form-control" id="edit-DtPublicacao" name="DtPublicacao">
          </div>
          <div class="mb-3">
            <label for="edit-digital" class="form-label">Arquivo Digital</label>
            <input type="file" class="form-control" id="edit-digital" name="arquivo_digital">
          </div>
          <div class="mb-3">
            <label for="edit-impresso" class="form-label">Arquivo Impresso</label>
            <input type="file" class="form-control" id="edit-impresso" name="arquivo_impresso">
          </div>
        </div>
        <div class="modal-footer">
          <!-- <input type="hidden" name="id" value="<?= $item['id'] ?>"> -->
          <button type="button" name="btn-fechar" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
          <button type="submit" name="btn-apagar" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja apagar este registro?')">Apagar</button>
          <button type="submit" name="btn-salvar" class="btn btn-primary btn-sm">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Mensagem -->
<!-- <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Mensagem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php //if ($messageType === 'success'): ?>
          <div class="alert alert-success" role="alert">
            <?= $message ?>
          </div>
        <?php //elseif ($messageType === 'error'): ?>
          <div class="alert alert-danger" role="alert">
            <?= $message ?>
          </div>
        <?php //endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div> -->