<!-- Modal de Edição -->
<div class="modal fade" id="EditarModal" tabindex="-1" aria-labelledby="EditarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title label-bold" id="EditarModalLabel">Editar Produção</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="Editar" method="post">
        <div class="modal-body">
          <input type="hidden" name="ID">
          <div class="row mb-3">
            <div class="col-3">
              <label for="EditarDataProducao" class="form-label label-bold">Data Produção</label>
              <input type="text" class="form-control" id="EditarDataProducao" name="DataProducao" required>
            </div>
            <div class="col-9">
              <label for="EditarCaderno" class="form-label label-bold">Caderno</label>
              <input type="text" class="form-control" id="EditarCaderno" name="Caderno" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditarPapel" class="form-label label-bold">Papel</label>
              <select class="form-control" id="EditarPapel" name="Papel" required>
                <option value=""></option>
                <?php foreach ($buscaFamilia as $key => $item): ?>
                  <option value="<?= $item['CODFAM'] ?>"><?= $item['CODFAM'] . ' - ' . $item['DESFAM'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <label for="EditarGramatura" class="form-label label-bold">Gramatura</label>
              <select class="form-control" id="EditarGramatura" name="Gramatura" required>
                <option value=""></option>
                <?php foreach ($buscaProduto as $key => $item): ?>
                  <option value="<?= $item['CODPRO'] . ' - ' . $item['DESPRO'] ?>"><?= $item['CODPRO'] . ' - ' . $item['DESPRO'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditarQtdeChapa" class="form-label label-bold">Qtde. Chapa</label>
              <input type="text" class="form-control" id="EditarQtdeChapa" name="QtdeChapa" required>
            </div>
            <div class="col">
              <label for="EditarTrocaBobina" class="form-label label-bold">Troca Bobina</label>
              <select class="form-control" id="EditarTrocaBobina" name="TrocaBobina" required>
                <option value=""></option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
              </select>
            </div>
            <div class="col">
              <label for="EditarQuebraPapel" class="form-label label-bold">Quebra Papel</label>
              <select class="form-control" id="EditarQuebraPapel" name="QuebraPapel" required>
                <option value=""></option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
              </select>
            </div>
            <div class="col">
              <label for="EditarDefeitoChapa" class="form-label label-bold">Defeito Chapa</label>
              <select class="form-control" id="EditarDefeitoChapa" name="DefeitoChapa" required>
                <option value=""></option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
              </select>
            </div>
            <div class="col">
              <label for="EditarMaquina" class="form-label label-bold">Maquina</label>
              <select class="form-control" id="EditarMaquina" name="Maquina" required>
                <option value=""></option>
                <option value="C150">C150</option>
                <option value="Nebiolo">Nebiolo</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditarTiragemLiquida" class="form-label label-bold">Tiragem Líquida</label>
              <input type="text" class="form-control" id="EditarTiragemLiquida" name="TiragemLiquida" required>
            </div>
            <div class="col">
              <label for="EditarTiragemBruta" class="form-label label-bold">Tiragem Bruta</label>
              <input type="text" class="form-control" id="EditarTiragemBruta" name="TiragemBruta" required>
            </div>
            <div class="col">
              <label for="EditarHoraInicio" class="form-label label-bold">Hora de Inicio</label>
              <input type="text" class="form-control" id="EditarHoraInicio" name="HoraInicio" required>
            </div>
            <div class="col">
              <label for="EditarHoraFim" class="form-label label-bold">Hora de Fim</label>
              <input type="text" class="form-control" id="EditarHoraFim" name="HoraFim" required>
            </div>
            <div class="col">
              <label for="EditarNumeroOP" class="form-label label-bold">Numero OP</label>
              <input type="text" class="form-control" id="EditarNumeroOP" name="NumeroOP">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="EditarObs" class="form-label label-bold">Observações</label>
              <textarea class="form-control" id="EditarObs" name="Obs" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger btn-sm" id="BtnExcluirModal" name="BtnExcluirModal">Excluir</button>
          <button type="submit" class="btn btn-primary btn-sm" id="BtnSalvarModal" name="BtnSalvarModal">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Incluir -->
<div class="modal fade" id="IncluirModal" tabindex="-1" aria-labelledby="IncluirModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title label-bold" id="IncluirModalLabel">Incluir Produção</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $URL ?>" id="Incluir" method="post">
        <div class="modal-body">
          <input type="hidden" name="ID">
          <div class="row mb-3">
            <div class="col-3">
              <label for="IncluirDataProducao" class="form-label label-bold">Data Produção</label>
              <input type="date" class="form-control" id="IncluirDataProducao" name="DataProducao" required>
            </div>
            <div class="col-9">
              <label for="IncluirCaderno" class="form-label label-bold">Caderno</label>
              <input type="text" class="form-control" id="IncluirCaderno" name="Caderno" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="IncluirPapel" class="form-label label-bold">Papel</label>
              <select class="form-control" id="IncluirPapel" name="Papel" onchange="fetchProductByFamily(this.value)" required>
                <option value=""></option>
                <?php foreach ($buscaFamilia as $key => $item): ?>
                  <option value="<?= $item['CODFAM'] ?>"><?= $item['CODFAM'] . ' - ' . $item['DESFAM'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <label for="IncluirGramatura" class="form-label label-bold">Gramatura</label>
              <select class="form-control" id="IncluirGramatura" name="Gramatura" required>
                <option value=""></option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="IncluirQtdeChapa" class="form-label label-bold">Qtde. Chapa</label>
              <input type="text" class="form-control" id="IncluirQtdeChapa" name="QtdeChapa" required>
            </div>
            <div class="col">
              <label for="IncluirTrocaBobina" class="form-label label-bold">Troca Bobina</label>
              <select class="form-control" id="IncluirTrocaBobina" name="TrocaBobina" required>
                <option value=""></option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
              </select>
            </div>
            <div class="col">
              <label for="IncluirQuebraPapel" class="form-label label-bold">Quebra Papel</label>
              <select class="form-control" id="IncluirQuebraPapel" name="QuebraPapel" required>
                <option value=""></option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
              </select>
            </div>
            <div class="col">
              <label for="IncluirDefeitoChapa" class="form-label label-bold">Defeito Chapa</label>
              <select class="form-control" id="IncluirDefeitoChapa" name="DefeitoChapa" required>
                <option value=""></option>
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
              </select>
            </div>
            <div class="col">
              <label for="IncluirMaquina" class="form-label label-bold">Maquina</label>
              <select class="form-control" id="IncluirMaquina" name="Maquina" required>
                <option value=""></option>
                <option value="C150">C150</option>
                <option value="Nebiolo">Nebiolo</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="IncluirTiragemLiquida" class="form-label label-bold">Tiragem Líquida</label>
              <input type="text" class="form-control" id="IncluirTiragemLiquida" name="TiragemLiquida" required>
            </div>
            <div class="col">
              <label for="IncluirTiragemBruta" class="form-label label-bold">Tiragem Bruta</label>
              <input type="text" class="form-control" id="IncluirTiragemBruta" name="TiragemBruta" required>
            </div>
            <div class="col">
              <label for="IncluirHoraInicio" class="form-label label-bold">Hora de Inicio</label>
              <input type="time" class="form-control" id="IncluirHoraInicio" name="HoraInicio" required>
            </div>
            <div class="col">
              <label for="IncluirHoraFim" class="form-label label-bold">Hora de Fim</label>
              <input type="time" class="form-control" id="IncluirHoraFim" name="HoraFim" required>
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
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-sm" id="BtnIncluirModal" name="BtnIncluirModal">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>