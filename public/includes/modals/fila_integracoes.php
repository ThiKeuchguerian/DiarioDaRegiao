<div class="modal fade" id="EditModal" tabindex="-1" aria-labelledby="EditModalEasyGraf" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditModalEasyGraf">Editar Pedido EasyClass/Grafica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="EditForm" method="post">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col">
              <label for="EditNumPed" class="form-label label-bold">Número Pedido</label>
              <input type="text" class="form-control" id="EditNumPed" name="EditNumPed" required readonly>
            </div>
            <div class="col">
              <label for="EditFlProc" class="form-label label-bold">Status</label>
              <input type="text" class="form-control" id="EditFlProc" name="EditFlProc" required readonly>
            </div>
            <div class="col">
              <label for="EditCodCli" class="form-label label-bold">Cod. Cliente</label>
              <input type="text" class="form-control" id="EditCodCli" name="EditCodCli" required readonly>
            </div>
            <div class="col">
              <label for="EditTipoCli" class="form-label label-bold">Tipo</label>
              <input type="text" class="form-control" id="EditTipoCli" name="EditTipoCli" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditDtGera" class="form-label label-bold">Dt. Geração</label>
              <input type="text" class="form-control" id="EditDtGera" name="EditDtGera" required readonly>
            </div>
            <div class="col">
              <label for="EditLote" class="form-label label-bold">Lote</label>
              <input type="text" class="form-control" id="EditLote" name="EditLote" required readonly>
            </div>
            <div class="col">
              <label for="EditOrigem" class="form-label label-bold">Origem</label>
              <input type="text" class="form-control" id="EditOrigem" name="EditOrigem" required readonly>
            </div>
            <div class="col">
              <label for="EditPedidoS" class="form-label label-bold">Pedido</label>
              <input type="text" class="form-control" id="EditPedidoS" name="EditPedidoS" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditVlrPedido" class="form-label label-bold">Vlr. Pedido</label>
              <input type="text" class="form-control" id="EditVlrPedido" name="EditVlrPedido">
            </div>
            <div class="col">
              <label for="EditVlrParc" class="form-label label-bold">Vlr. Parcela</label>
              <input type="text" class="form-control" id="EditVlrParc" name="EditVlrParc">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditMen" class="form-label label-bold">Mensagem Erro</label>
              <textarea class="form-control" id="EditMen" name="EditMen" rows="5" required readonly></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" id="Salvar" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="ModalPedidoProtheus" tabindex="-1" aria-labelledby="EditModalPedidoProtheus" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditModalPedidoProtheus">Editar Pedido Protheus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="EditFormPedProtheus" method="post">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col">
              <label for="EditFlProcProtheus" class="form-label label-bold">Status</label>
              <input type="text" class="form-control" id="EditFlProcProtheus" name="EditFlProcProtheus" required readonly>
            </div>
            <div class="col">
              <label for="EditCodPed" class="form-label label-bold">Número Pedido</label>
              <input type="text" class="form-control" id="EditCodPed" name="EditCodPed" required readonly>
            </div>
            <div class="col">
              <label for="EditCodCliProtheus" class="form-label label-bold">Cod. Cliente</label>
              <input type="text" class="form-control" id="EditCodCliProtheus" name="EditCodCliProtheus" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditDtEmiProtheus" class="form-label label-bold">Dt. Emissão</label>
              <input type="text" class="form-control" id="EditDtEmiProtheus" name="EditDtEmiProtheus" required readonly>
            </div>
            <div class="col">
              <label for="EditDtGeraProtheus" class="form-label label-bold">Dt. Geração</label>
              <input type="text" class="form-control" id="EditDtGeraProtheus" name="EditDtGeraProtheus" required readonly>
            </div>
            <div class="col">
              <label for="EditCodVenProtheus" class="form-label label-bold">Cod. Vendedor</label>
              <input type="text" class="form-control" id="EditCodVenProtheus" name="EditCodVenProtheus" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditPedidoProtheus" class="form-label label-bold">Pedido Protheus</label>
              <input type="text" class="form-control" id="EditPedidoProtheus" name="EditPedidoProtheus" required readonly>
            </div>
            <div class="col">
              <label for="EditLoteProtheus" class="form-label label-bold">N.º Lote</label>
              <input type="text" class="form-control" id="EditLoteProtheus" name="EditLoteProtheus" required readonly>
            </div>
            <div class="col">
              <label for="EditIntegracaoProtheus" class="form-label label-bold">Cod. Integração</label>
              <input type="text" class="form-control" id="EditIntegracaoProtheus" name="EditIntegracaoProtheus" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditMenProtheus" class="form-label label-bold">Mensagem Erro</label>
              <textarea class="form-control" id="EditMenProtheus" name="EditMenProtheus" rows="5" required readonly></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" id="ExcluirPedidoProtheus" class="btn btn-danger">Excluir</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="ModalPedidoCapt" tabindex="-1" aria-labelledby="EditModalPedidoCapt" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditModalPedidoCapt">Editar Pedido Capt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="EditFormPedCapt" method="post">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col">
              <label for="EditFlProcCApt" class="form-label label-bold">Status</label>
              <input type="text" class="form-control" id="EditFlProcCApt" name="EditFlProcCApt" required readonly>
            </div>
            <div class="col">
              <label for="EditLotePed" class="form-label label-bold">Lote</label>
              <input type="text" class="form-control" id="EditLotePed" name="EditLotePed" required readonly>
            </div>
            <div class="col">
              <label for="EditAPCapt" class="form-label label-bold">N.º: Contrato</label>
              <input type="text" class="form-control" id="EditAPCapt" name="EditAPCapt" required readonly>
            </div>
            <div class="col">
              <label for="EditDtEmiCapt" class="form-label label-bold">Dt. Emissão</label>
              <input type="text" class="form-control" id="EditDtEmiCapt" name="EditDtEmiCapt" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditTituloCapt" class="form-label label-bold">Título AP</label>
              <input type="text" class="form-control" id="EditTituloCapt" name="EditTituloCapt" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditCodCliCapt" class="form-label label-bold">Cod. Cliente</label>
              <input type="text" class="form-control" id="EditCodCliCapt" name="EditCodCliCapt" required readonly>
            </div>
            <div class="col">
              <label for="EditCpfCnpjCapt" class="form-label label-bold">CPF/CNPJ</label>
              <input type="text" class="form-control" id="EditCpfCnpjCapt" name="EditCpfCnpjCapt" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditCodVenCapt" class="form-label label-bold">Cod. Vendedor</label>
              <input type="text" class="form-control" id="EditCodVenCapt" name="EditCodVenCapt" required readonly>
            </div>
            <div class="col">
              <label for="EditCodAgCapt" class="form-label label-bold">Cod. Agencia</label>
              <input type="text" class="form-control" id="EditCodAgCapt" name="EditCodAgCapt" required readonly>
            </div>
            <div class="col">
              <label for="EditVlrAPCapt" class="form-label label-bold">Valor</label>
              <input type="text" class="form-control" id="EditVlrAPCapt" name="EditVlrAPCapt" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditMenCapt" class="form-label label-bold">Mensagem Erro</label>
              <textarea class="form-control" id="EditMenCapt" name="EditMenCapt" rows="5" required readonly></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" id="SalvarApCapt" class="btn btn-danger">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="ModalClienteProtheus" tabindex="-1" aria-labelledby="EditModalClienteProtheus" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditModalClienteProtheus">Editar Cliente Protheus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="EditFormClienteProtheus" method="post">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col">
              <label for="EditFlProcCliProtheus" class="form-label label-bold">Status</label>
              <input type="text" class="form-control" id="EditFlProcCliProtheus" name="EditFlProcCliProtheus" required>
            </div>
            <div class="col">
              <label for="EditTipoCliProtheus" class="form-label label-bold">Tipo</label>
              <input type="text" class="form-control" id="EditTipoCliProtheus" name="EditTipoCliProtheus" required readonly>
            </div>
            <div class="col">
              <label for="EditCodigoCliProtheus" class="form-label label-bold">Cod. Cliente</label>
              <input type="text" class="form-control" id="EditCodigoCliProtheus" name="EditCodigoCliProtheus" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditCpfCnpjProtheus" class="form-label label-bold">CPF/CNPJ</label>
              <input type="text" class="form-control" id="EditCpfCnpjProtheus" name="EditCpfCnpjProtheus" required readonly>
            </div>
            <div class="col">
              <label for="EditIEProtheus" class="form-label label-bold">I.E.</label>
              <input type="text" class="form-control" id="EditIEProtheus" name="EditIEProtheus">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditNomCliProtheus" class="form-label label-bold">Nome</label>
              <input type="text" class="form-control" id="EditNomCliProtheus" name="EditNomCliProtheus" required readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditMenErroProtheus" class="form-label label-bold">Mensagem Erro</label>
              <textarea class="form-control" id="EditMenErroProtheus" name="EditMenErroProtheus" rows="5" required readonly></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" id="SalvarCliProtheus" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>