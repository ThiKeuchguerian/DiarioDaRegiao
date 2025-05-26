<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <section id="section-edit-client">
        <form action="<?= $URL ?>" method="post">
          <div class="modal-body mb-0">
            <div class="row mb-3">
              <div class="col-4">
                <label for="CodCli" class="form-label fw-bold">Cod. Cliente</label>
                <input type="text" class="form-control" id="CodCli" name="CodCli" style="background-color: #e9ecef;" required readonly>
              </div>
              <div class="col-8">
                <label for="NomeCli" class="form-label fw-bold">Nome Cliente</label>
                <input type="text" class="form-control" id="NomeCli" name="NomeCli" style="background-color: #e9ecef;" required readonly>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="TribICMS" class="form-label fw-bold">Trib. ICMS</label>
                <input type="text" class="form-control str-input" id="TribICMS" name="TribICMS" maxlength="1" required pattern="^[SN]$">
              </div>
              <div class="col">
                <label for="TribIPI" class="form-label fw-bold">Trib. IPI</label>
                <input type="text" class="form-control str-input" id="TribIPI" name="TribIPI" maxlength="1" required pattern="^[SN]$">
              </div>
              <div class="col">
                <label for="TribPIS" class="form-label fw-bold">Trib. PIS</label>
                <input type="text" class="form-control str-input" id="TribPIS" name="TribPIS" maxlength="1" required pattern="^[SN]$">
              </div>
              <div class="col">
                <label for="TribCofins" class="form-label fw-bold">Trib.COFINS</label>
                <input type="text" class="form-control str-input" id="TribCofins" name="TribCofins" maxlength="1" required pattern="^[SN]$">
              </div>
              <div class="col">
                <label for="OutrasRet" class="form-label fw-bold">Outras Ret.</label>
                <input type="text" class="form-control str-input" id="OutrasRet" name="OutrasRet" maxlength="1" required pattern="^[SN]$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="RetIR" class="form-label fw-bold">Ret. IR</label>
                <input type="text" class="form-control str-input" id="RetIR" name="RetIR" maxlength="1" required pattern="^[SN]$">
              </div>
              <div class="col">
                <label for="RetCSLL" class="form-label fw-bold">Ret. CSLL</label>
                <input type="text" class="form-control str-input" id="RetCSLL" name="RetCSLL" maxlength="1" required pattern="^[SN]$">
              </div>
              <div class="col">
                <label for="RetPIS" class="form-label fw-bold">Ret. PIS</label>
                <input type="text" class="form-control str-input" id="RetPIS" name="RetPIS" maxlength="1" required pattern="^[SN]$">
              </div>
              <div class="col">
                <label for="RetCofins" class="form-label fw-bold">Ret.COFINS</label>
                <input type="text" class="form-control str-input" id="RetCofins" name="RetCofins" maxlength="1" required pattern="^[SN]$">
              </div>
              <div class="col">
                <label for="RetProd" class="form-label fw-bold">Ret. Prod.</label>
                <input type="text" class="form-control str-input" id="RetProd" name="RetProd" maxlength="1" required pattern="^[SN]$">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" id="SalvaCli" class="btn btn-primary btn-sm">Salvar</button>
          </div>
        </form>
      </section>

      <section id="section-edit-nota">
        <form id="form-edit-nota" method="post">
          <div class="modal-body mb-0">
            <div class="row mb-3">
              <div class="col">
                <label for="CodEmpresa" class="form-label fw-bold">Cod.Empresa</label>
                <input type="text" class="form-control" id="CodEmpresa" name="CodEmpresa" style="background-color: #e9ecef;" required readonly>
              </div>
              <div class="col">
                <label for="NNota" class="form-label fw-bold">Num. Nota</label>
                <input type="text" class="form-control" id="NNota" name="NNota" style="background-color: #e9ecef;" required readonly>
              </div>
              <div class="col">
                <label for="NCodCli" class="form-label fw-bold">Cod. Cliente</label>
                <input type="text" class="form-control" id="NCodCli" name="NCodCli" style="background-color: #e9ecef;" required readonly>
              </div>
              <div class="col">
                <label for="Tipo" class="form-label fw-bold">Tipo</label>
                <input type="text" class="form-control num-input" id="Tipo" name="Tipo" style="background-color: #e9ecef;" required readonly>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="VlrBIR" class="form-label fw-bold">Vlr Base IR</label>
                <input type="text" class="form-control num-input" id="VlrBIR" name="VlrBIR" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="VlrIR" class="form-label fw-bold">Vlr IR</label>
                <input type="text" class="form-control num-input" id="VlrIR" name="VlrIR" required pattern="^\d+(\.\d{1,2})?$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="VlrBCSLL" class="form-label fw-bold">Vlr Base CSLL</label>
                <input type="text" class="form-control num-input" id="VlrBCSLL" name="VlrBCSLL" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="VlrCSLL" class="form-label fw-bold">Vlr CSLL</label>
                <input type="text" class="form-control num-input" id="VlrCSLL" name="VlrCSLL" required pattern="^\d+(\.\d{1,2})?$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="VlrBPIS" class="form-label fw-bold">Vlr Base PIS</label>
                <input type="text" class="form-control num-input" id="VlrBPIS" name="VlrBPIS" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="VlrPIS" class="form-label fw-bold">Vlr PIS</label>
                <input type="text" class="form-control num-input" id="VlrPIS" name="VlrPIS" required pattern="^\d+(\.\d{1,2})?$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="VlrBCofins" class="form-label fw-bold">Vlr Base COFINS</label>
                <input type="text" class="form-control num-input" id="VlrBCofins" name="VlrBCofins" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="VlrCofins" class="form-label fw-bold">Vlr COFINS</label>
                <input type="text" class="form-control num-input" id="VlrCofins" name="VlrCofins" required pattern="^\d+(\.\d{1,2})?$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="VlrTotal" class="form-label fw-bold">Valor Total Retenção</label>
                <input type="text" class="form-control" id="VlrTotal" name="VlrTotal" required readonly>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" id="SalvaNota" class="btn btn-primary btn-sm">Salvar</button>
            </div>
        </form>
      </section>

      <section id="section-edit-itens">
        <form id="from-edit-itens" method="post">
          <div class="modal-body mb-0">
            <div class="row mb-3">
              <div class="col">
                <label for="editCodEmp" class="form-label fw-bold">Cod.Empresa</label>
                <input type="text" class="form-control" id="editCodEmp" name="editCodEmp" style="background-color: #e9ecef;" required readonly>
              </div>
              <div class="col">
                <label for="editNumNota" class="form-label fw-bold">Num. Nota</label>
                <input type="text" class="form-control" id="editNumNota" name="editNumNota" style="background-color: #e9ecef;" required readonly>
              </div>
              <div class="col">
                <label for="editTipo" class="form-label fw-bold">Tipo</label>
                <input type="text" class="form-control" id="editTipo" name="editTipo" style="background-color: #e9ecef;" required readonly>
              </div>
              <div class="col">
                <label for="editSeq" class="form-label fw-bold">Seq. Item</label>
                <input type="text" class="form-control" id="editSeq" name="editSeq" style="background-color: #e9ecef;" required readonly>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="editVlrBaseNota" class="form-label fw-bold">Vlr Base Nota</label>
                <input type="text" class="form-control num-input" id="editVlrBaseNota" name="editVlrBaseNota" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editVlrBaseIR" class="form-label fw-bold">Vlr Base IR</label>
                <input type="text" class="form-control num-input" id="editVlrBaseIR" name="editVlrBaseIR" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editPercIR" class="form-label fw-bold">Perc. IR</label>
                <input type="text" class="form-control num-input" id="editPercIR" name="editPercIR" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editVlrIR" class="form-label fw-bold">Vlr IR</label>
                <input type="text" class="form-control num-input" id="editVlrIR" name="editVlrIR" required pattern="^\d+(\.\d{1,2})?$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="editVlrBaseCSLL" class="form-label fw-bold">Vlr Base CSLL</label>
                <input type="text" class="form-control num-input" id="editVlrBaseCSLL" name="editVlrBaseCSLL" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editPercCSLL" class="form-label fw-bold">Perc. CSLL</label>
                <input type="text" class="form-control num-input" id="editPercCSLL" name="editPercCSLL" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editVlrCSLL" class="form-label fw-bold">Vlr CSLL</label>
                <input type="text" class="form-control num-input" id="editVlrCSLL" name="editVlrCSLL" required pattern="^\d+(\.\d{1,2})?$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="editVlrBasePIS" class="form-label fw-bold">Vlr Base PIS</label>
                <input type="text" class="form-control num-input" id="editVlrBasePIS" name="editVlrBasePIS" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editPercPIS" class="form-label fw-bold">Perc. PIS</label>
                <input type="text" class="form-control num-input" id="editPercPIS" name="editPercPIS" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editVlrPIS" class="form-label fw-bold">Vlr PIS</label>
                <input type="text" class="form-control num-input" id="editVlrPIS" name="editVlrPIS" required pattern="^\d+(\.\d{1,2})?$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="editVlrBaseCOFINS" class="form-label fw-bold">Vlr Base COFINS</label>
                <input type="text" class="form-control num-input" id="editVlrBaseCOFINS" name="editVlrBaseCOFINS" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editPercCOFINS" class="form-label fw-bold">Perc. COFINS</label>
                <input type="text" class="form-control num-input" id="editPercCOFINS" name="editPercCOFINS" required pattern="^\d+(\.\d{1,2})?$">
              </div>
              <div class="col">
                <label for="editVlrCOFINS" class="form-label fw-bold">Vlr COFINS</label>
                <input type="text" class="form-control num-input" id="editVlrCOFINS" name="editVlrCOFINS" required pattern="^\d+(\.\d{1,2})?$">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label for="editVlrTotal" class="form-label fw-bold">Valor Total Retenção</label>
                <input type="text" class="form-control" id="editVlrTotal" name="editVlrTotal" required readonly>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" id="SalvaItens" class="btn btn-primary btn-sm">Salvar</button>
          </div>
        </form>
      </section>
    </div>
  </div>
</div>