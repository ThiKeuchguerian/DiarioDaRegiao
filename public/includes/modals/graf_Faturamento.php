<!-- Modal de Edição -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel"><strong>Editar Registro</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="Edit" method="post">
        <div class="modal-body">
          <div class="row mb-3">
            <input type="hidden" class="form-control" id="editID" name="ID">
            <div class="col">
              <label for="editDataRodagem" class="form-label label-bold">Data Rodagem</label>
              <input type="text" class="form-control" id="editDataRodagem" name="DataRodagem">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="editCliente" class="form-label label-bold">Cliente</label>
              <input type="text" class="form-control" id="editCliente" name="Cliente">
            </div>
            <div class="col">
              <label for="editArte" class="form-label label-bold">Arte</label>
              <input type="text" class="form-control" id="editArte" name="Arte">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="editTipo" class="form-label label-bold">Tipo</label>
              <select class="form-control" id="editTipo" name="Tipo">
                <option value=""></option>
                <option value="Comercial">Comercial</option>
                <option value="Digital">Digital</option>
                <option value="Editorial">Editorial</option>
                <option value="Embalagem">Embalagem</option>
                <option value="Papel">Papel</option>
                <option value="Terceiro">Terceiro</option>
              </select>
            </div>
            <div class="col">
              <label for="editFormato" class="form-label label-bold">Formato</label>
              <input type="text" class="form-control" id="editFormato" name="Formato">
            </div>
            <div class="col">
              <label for="editPapel" class="form-label label-bold">Papel</label>
              <input type="text" class="form-control" id="editPapel" name="Papel">
            </div>
            <div class="col">
              <label for="editQtdeCor" class="form-label label-bold">Qtde. Cor</label>
              <input type="text" class="form-control" id="editQtdeCor" name="QtdeCor">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="editTiragem" class="form-label label-bold">Tiragem</label>
              <input type="text" class="form-control" id="editTiragem" name="Tiragem">
            </div>
            <div class="col">
              <label for="editValor" class="form-label label-bold">Valor</label>
              <input type="text" class="form-control" id="editValor" name="Valor">
            </div>
            <div class="col">
              <label for="editFaturado" class="form-label label-bold">Faturado</label>
              <select class="form-control" id="editFaturado" name="Faturado">
                <option value=""></option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
              </select>
            </div>
            <div class="col">
              <label for="editNumPedido" class="form-label label-bold">Num. Pedido</label>
              <input type="text" class="form-control" id="editNumPedido" name="NumPedido">
            </div>
            <div class="col">
              <label for="editNumPedCli" class="form-label label-bold">Ped. Cli.</label>
              <input type="text" class="form-control text-uppercase" id="editNumPedCli" name="NumPedCli" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="editObs" class="form-label label-bold">Observações</label>
              <textarea class="form-control" id="editObs" name="Obs" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger btn-sm" id="BtnExcluirModal" name="BtnExcluirModal">Excluir</button>
          <button type="submit" class="btn btn-primary btn-sm" id="BtnSalvarModal" name="BtnSalvarModal">Salvar</button>
        </div>
    </div>
    </form>
  </div>
</div>

<!-- Modal Incluir -->
<div class="modal fade" id="IncluirModal" tabindex="-1" aria-labelledby="IncluirModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="IncluirModalLabel"><strong>Incluir Registro</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="Incluir" method="post">
        <div class="modal-body">
          <input type="hidden" name="ID">
          <div class="row mb-3">
            <div class="col">
              <label for="IncluirDataRodagem" class="form-label label-bold">Data Rodagem</label>
              <input type="date" class="form-control" id="IncluirDataRodagem" name="DataRodagem" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="IncluirCliente" class="form-label label-bold">Cliente</label>
              <input type="text" class="form-control" id="IncluirCliente" name="Cliente">
            </div>
            <div class="col">
              <label for="IncluirArte" class="form-label label-bold">Arte</label>
              <input type="text" class="form-control" id="IncluirArte" name="Arte">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="IncluirTipo" class="form-label label-bold">Tipo</label>
              <select class="form-control" id="editTipo" name="Tipo">
                <option value=""></option>
                <option value="Comercial">Comercial</option>
                <option value="Digital">Digital</option>
                <option value="Editorial">Editorial</option>
                <option value="Embalagem">Embalagem</option>
                <option value="Papel">Papel</option>
                <option value="Terceiro">Terceiro</option>
              </select>
            </div>
            <div class="col">
              <label for="IncluirFormato" class="form-label label-bold">Formato</label>
              <input type="text" class="form-control" id="IncluirFormato" name="Formato">
            </div>
            <div class="col">
              <label for="IncluirPapel" class="form-label label-bold">Papel</label>
              <input type="text" class="form-control" id="IncluirPapel" name="Papel">
            </div>
            <div class="col">
              <label for="IncluirQtdeCor" class="form-label label-bold">Qtde. Cor</label>
              <input type="text" class="form-control" id="IncluirQtdeCor" name="QtdeCor">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="IncluirTiragem" class="form-label label-bold">Tiragem</label>
              <input type="text" class="form-control" id="IncluirTiragem" name="Tiragem">
            </div>
            <div class="col">
              <label for="IncluirValor" class="form-label label-bold">Valor</label>
              <input type="text" class="form-control" id="IncluirValor" name="Valor">
            </div>
            <div class="col">
              <label for="IncluirFaturado" class="form-label label-bold">Faturado</label>
              <select class="form-control" id="IncluirFaturado" name="Faturado">
                <option value=""></option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="IncluirObs" class="form-label label-bold">Observações</label>
              <textarea class="form-control" id="IncluirObs" name="Obs" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="BtnIncluirModal" name="BtnIncluirModal">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>