<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-weight: bold;" id="modal">Editar Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" method="post">
        <div class="modal-body mb-0">
          <input type="hidden" name="numped">
          <div class="row mb-3">
            <div class="col">
              <label for="numped" class="form-label" style="font-weight: bold;">Cod. Pedido</label>
              <input type="text" class="form-control" id="numped" name="numped" style="background-color: #e9ecef;" readonly>
            </div>
            <div class="col">
              <label for="pedcli" class="form-label" style="font-weight: bold;">Cod. NumPed</label>
              <input type="text" class="form-control" id="pedcli" name="pedcli" style="background-color: #e9ecef;" readonly>
            </div>
            <div class="col">
              <label for="codcli" class="form-label" style="font-weight: bold;">Cod. Cliente</label>
              <input type="text" class="form-control" id="codcli" name="codcli" style="background-color: #e9ecef;" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="ctafin" class="form-label" style="font-weight: bold;">Conta Financeira</label>
              <input type="text" class="form-control" id="ctafin" name="ctafin">
            </div>
            <div class="col">
              <label for="ctared" class="form-label" style="font-weight: bold;">Conta Contábil</label>
              <input type="text" class="form-control" id="ctared" name="ctared">
            </div>
            <div class="col">
              <label for="codccu" class="form-label" style="font-weight: bold;">Centro de Custo</label>
              <input type="text" class="form-control" id="codccu" name="codccu">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="codfam" class="form-label" style="font-weight: bold;">Código da Família</label>
              <input type="text" class="form-control" id="codfam" name="codfam" style="background-color: #e9ecef;" readonly>
            </div>
            <div class="col">
              <label for="codser" class="form-label" style="font-weight: bold;">Código do Serviço</label>
              <input type="text" class="form-control" id="codser" name="codser" style="background-color: #e9ecef;" readonly>
            </div>
            <div class="col">
              <label for="tnsser" class="form-label" style="font-weight: bold;">Transação Serviço</label>
              <input type="text" class="form-control" id="tnsser" name="tnsser" style="background-color: #e9ecef;" readonly>
            </div>
          </div>
          <div class="mb-3">
            <label for="nomcli" class="form-label" style="font-weight: bold;">Cliente</label>
            <input type="text" class="form-control" id="nomcli" name="nomcli" style="background-color: #e9ecef;" readonly>
          </div>
          <div class="mb-3">
            <label for="desser" class="form-label" style="font-weight: bold;">Descrição</label>
            <input type="text" class="form-control" id="desser" name="desser" style="background-color: #e9ecef;" readonly>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="submit" class="btn btn-primary" id="btn-salvar" name="btn-salvar">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>