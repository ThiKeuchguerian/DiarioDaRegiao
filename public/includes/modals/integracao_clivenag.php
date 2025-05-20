<!-- Modal Editar Cliente -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel"><strong>Editar Cliente</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="<?= $URL ?>" id="editForm" method="post">
          <input type="hidden" id="editId" name="editId">
          <div class="row mb-3">
            <div class="col">
              <label for="editSistem" class="form-label label-bold"><strong>Sistema</strong></label>
              <input type="text" style="background-color: #e9ecef;" class="form-control" id="editSistem" name="Sistema" readonly>
            </div>
            <div class="col">
              <label for="editCodCliente" class="form-label label-bold"><strong>Cod. Cliente</strong></label>
              <input type="text" class="form-control" id="editCodCliente" name="CodCliente" maxlength="6" required>
            </div>
            <div class="col">
              <label for="editCodVendedor" class="form-label label-bold"><strong>Cod. Vendedor</strong></label>
              <input type="text" class="form-control" id="editCodVendedor" name="CodVendedor" maxlength="5">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="editRazaoSocial" class="form-label label-bold"><strong>Nome / Razão Social</strong></label>
              <input type="text" class="form-control" id="editRazaoSocial" name="RazaoSocial" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="editCpfCnpj" class="form-label label-bold"><strong>CPF/CNPJ</strong></label>
              <input type="text" style="background-color: #e9ecef;" class="form-control" id="editCpfCnpj" name="CpfCnpj" required readonly>
            </div>
            <div class="col">
              <label for="editTipo" class="form-label label-bold"><strong>Tipo</strong></label>
              <select class="form-control" id="editTipo" name="Tipo" required>
                <option value="0">--Selecione Tipo --</option>
                <option value="1">Física</option>
                <option value="2">Jurídica</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary btn-sm" id="BtnSalvarCli" name="BtnSalvarCli">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>