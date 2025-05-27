<?php

require_once __DIR__ . '/../DBConnect.php';

class FinVerificaCorrigeRecencaoNFC
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('seniorTeste');
  }

  // ------------------------------------------------------------------------
  // SELECTS
  // ------------------------------------------------------------------------
  public function getCliente(string $codCli): array
  {
    $sql = " SELECT codcli, nomcli, triicm AS T_ICMS, triipi AS T_IPI, tricof AS T_COFINS, 
        tripis AS T_PIS, retirf AS IR, retcsl AS CSLL, retcof AS COFINS, retpis AS PIS,
        retour AS OutrasRet, retpro AS RetPro 
      FROM e085cli
      WHERE codcli = :codCli
    ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':codCli' => $codCli]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getNotaBase(int $codEmp, string $numNota): array
  {
    $sql = "SELECT codemp, numnfv, codcli,  
        vlrbir, vlrirf, vlrbcl, vlrcsl,
        vlrbpt, vlrpit, vlrbct, vlrcrt,
        (vlrirf+vlrcsl+vlrpit+vlrcrt) AS VlrRetencao
      FROM e140nfv
      WHERE codemp = :codEmp
        AND numnfv = :numNota
        AND codsnf  = 'NSC'
    ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp'  => $codEmp,
      ':numNota' => $numNota
    ]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getNotaDeveria(int $codEmp, string $numNota): array
  {
    $sql = "SELECT
        codemp, numnfv, codcli,
        vlrbir    = vlrlse,
        vlrirf    = (vlrlse*4.8)/100,
        vlrbcl    = vlrlse,
        vlrcsl    = (vlrlse*1)/100,
        vlrbpt    = vlrlse,
        vlrpit    = (vlrlse*0.65)/100,
        vlrbct    = vlrlse,
        vlrcrt    = (vlrlse*3.00)/100,
        VlrRetencao = 
          ((vlrlse*4.8)/100) +
          ((vlrlse*1)/100) +
          ((vlrlse*0.65)/100) +
          ((vlrlse*3.00)/100)
      FROM e140nfv
      WHERE codemp = :codEmp
        AND numnfv = :numNota
        AND codsnf  = 'NSC'
    ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp'  => $codEmp,
      ':numNota' => $numNota
    ]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getItensNotaBase(int $codEmp, string $numNota): array
  {
    $sql = "SELECT
        codemp, numnfv, seqisv,vlrlse, 
        vlrbir, perirf, vlrirf, 
        vlrbcl, percsl, vlrcsl, 
        vlrbpt, perpit, vlrpit, 
        vlrbct, percrt, vlrcrt,
        (vlrirf+vlrcsl+vlrpit+vlrcrt) AS VlrRetencao
      FROM e140isv
      WHERE codemp = :codEmp
        AND numnfv = :numNota
        AND codsnf  = 'NSC'
    ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp'  => $codEmp,
      ':numNota' => $numNota
    ]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getItensNotaDeveria(int $codEmp, string $numNota): array
  {
    $sql = "SELECT
        codemp, numnfv, seqisv,
        vlrbir    = vlrlse,
        vlrlse,
        perirf    = 4.80,
        vlrirf    = (vlrlse*4.8)/100,
        vlrbcl    = vlrlse,
        percsl    = 1.00,
        vlrcsl    = (vlrlse*1)/100,
        perpit    = 0.65,
        vlrbpt    = vlrlse,
        vlrpit    = (vlrlse*0.65)/100,
        percrt    = 3.00,
        vlrbct    = vlrlse,
        vlrcrt    = (vlrlse*3.00)/100,
        VlrRetencao = 
          ((vlrlse*4.8)/100) +
          ((vlrlse*1)/100) +
          ((vlrlse*0.65)/100) +
          ((vlrlse*3.00)/100)
      FROM e140isv
      WHERE codemp = :codEmp
        AND numnfv = :numNota
        AND codsnf  = 'NSC'
    ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp'  => $codEmp,
      ':numNota' => $numNota
    ]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  // ------------------------------------------------------------------------
  // UPDATES
  // ------------------------------------------------------------------------
  private function updateNFV(string $setClause, int $codEmp, string $numNota): void
  {
    $sql = "UPDATE e140nfv SET {$setClause}
      WHERE codemp = :codEmp
        AND numnfv = :numNota
        AND codsnf  = 'NSC'
    ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp'  => $codEmp,
      ':numNota' => $numNota
    ]);
  }

  private function updateISV(string $setClause, int $codEmp, string $numNota): void
  {
    $sql = "UPDATE e140isv SET {$setClause}
      WHERE codemp = :codEmp
        AND numnfv = :numNota
        AND codsnf  = 'NSC'
    ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp'  => $codEmp,
      ':numNota' => $numNota
    ]);
  }

  /**
   * Corrige as retenções conforme tipo:
   *  - 'Todas'
   *  - 'IR'
   *  - 'CSLL'
   */
  public function corrigirRetencao(int $codEmp, string $numNota, string $tipoRetencao): void
  {
    $mapNFV = [
      'Todas' => "vlrbir=vlrlse, vlrirf=(vlrlse*4.8)/100,
                    vlrbcl=vlrlse, vlrcsl=(vlrlse*1)/100,
                    vlrbpt=vlrlse, vlrpit=(vlrlse*0.65)/100,
                    vlrbct=vlrlse, vlrcrt=(vlrlse*3.00)/100",
      'IR'    => "vlrbir=vlrlse, vlrirf=(vlrlse*4.8)/100",
      'CSLL'  => "vlrbcl=vlrlse, vlrcsl=(vlrlse*1)/100,
                    vlrbpt=vlrlse, vlrpit=(vlrlse*0.65)/100,
                    vlrbct=vlrlse, vlrcrt=(vlrlse*3.00)/100"
    ];
    $mapINFV = [
      'Todas' => "vlrbir=vlrlse, vlrirf=(vlrlse*4.8)/100,
                    vlrbcl=vlrlse, vlrcsl=(vlrlse*1)/100,
                    vlrbpt=vlrlse, vlrpit=(vlrlse*0.65)/100,
                    vlrbct=vlrlse, vlrcrt=(vlrlse*3.00)/100",
      'IR'    => "vlrbir=vlrlse, perirf=4.80, vlrirf=(vlrlse*4.8)/100",

      'CSLL'  => "vlrbcl=vlrlse, percsl='1.00', vlrcsl=(vlrlse*1)/100,
                    vlrbpt=vlrlse, perpit='0.65', vlrpit=(vlrlse*0.65)/100,
                    vlrbct=vlrlse, percrt='3.00', vlrcrt=(vlrlse*3.00)/100"
    ];

    if (isset($mapNFV[$tipoRetencao])) {
      $this->updateNFV($mapNFV[$tipoRetencao], $codEmp, $numNota);
      $this->updateISV($mapINFV[$tipoRetencao], $codEmp, $numNota);
    }
  }

  /**
   * Zera todas as retenções na NF e no ISV
   */
  public function zeraRetencao(int $codEmp, string $numNota): void
  {
    $set0 = "vlrbir=0, vlrirf=0, vlrbcl=0, vlrcsl=0,
             vlrbpt=0, vlrpit=0, vlrbct=0, vlrcrt=0";

    $set1 = "vlrbir=0, perirf=0, vlrirf=0, 
             vlrbcl=0, percsl=0, vlrcsl=0,
             vlrbpt=0, perpit=0, vlrpit=0, 
             vlrbct=0, percrt=0, vlrcrt=0";
    $this->updateNFV($set0, $codEmp, $numNota);
    $this->updateISV($set1, $codEmp, $numNota);
  }

  /**
   * Atualiza Cliente
   * @param array $dados
   */
  public function atualizarCliente(array $dados): void
  {
    if (isset($dados['btn-salvarcli'])) {
      $sql = "UPDATE e085cli SET 
        triicm   = :TribICMS,
        triipi   = :TribIPI,
        tripis   = :TribPIS,
        tricof   = :TribCofins,
        retirf   = :RetIR,
        retcsl   = :RetCSLL,
        retpis   = :RetPIS,
        retcof   = :RetCofins,
        retour   = :OutrasRet,
        retpro   = :RetProd
        WHERE codcli = :CodCli
      ";

      $params = [
        'CodCli'     => $dados['CodCli'],
        'TribICMS'   => $dados['TribICMS'],
        'TribIPI'    => $dados['TribIPI'],
        'TribPIS'    => $dados['TribPIS'],
        'TribCofins' => $dados['TribCofins'],
        'RetIR'      => $dados['RetIR'],
        'RetCSLL'    => $dados['RetCSLL'],
        'RetPIS'     => $dados['RetPIS'],
        'RetCofins'  => $dados['RetCofins'],
        'OutrasRet'  => $dados['OutrasRet'],
        'RetProd'    => $dados['RetProd']
      ];
    } else if (isset($dados['btn-salvarnf'])) {
      $sql  = "UPDATE E140NFV SET 
          vlrbir = :VlrBIR, 
          vlrirf = :VlrIR, 
          vlrbcl = :VlrBCSLL, 
          vlrcsl = :VlrCSLL, 
          vlrbpt = :VlrBPIS, 
          vlrpit = :VlrPIS, 
          vlrbct = :VlrBCofins, 
          vlrcrt = :VlrCofins
        WHERE codemp = :CodEmpresa AND numnfv = :NNota AND codsnf='NSC'
      ";

      $params = [
        'CodEmpresa' => $dados['CodEmpresa'],
        'NNota'      => $dados['NNota'],
        'VlrBIR'     => $dados['VlrBIR'],
        'VlrIR'      => $dados['VlrIR'],
        'VlrBCSLL'   => $dados['VlrBCSLL'],
        'VlrCSLL'    => $dados['VlrCSLL'],
        'VlrBPIS'    => $dados['VlrBPIS'],
        'VlrPIS'     => $dados['VlrPIS'],
        'VlrBCofins' => $dados['VlrBCofins'],
        'VlrCofins'  => $dados['VlrCofins']
      ];
    } else if (isset($dados['btn-salvarinf'])) {
      $sql = "UPDATE E140ISV SET
          vlrlse = :VlrBaseNota, 
          vlrbir = :VlrBaseIR, 
          perirf = :PercIR, 
          vlrirf = :VlrIR, 
          vlrbcl = :VlrBaseCSLL, 
          percsl = :PercCSLL, 
          vlrcsl = :VlrCSLL,
          vlrbpt = :VlrBasePIS, 
          perpit = :PercPIS, 
          vlrpit = :VlrPIS,
          vlrbct = :VlrBaseCOFINS, 
          percrt = :PercCOFINS, 
          vlrcrt = :VlrCOFINS
        WHERE codemp = :CodEmpresa AND numnfv = :NNota AND seqisv = :SeqIsv AND codsnf='NSC'
      ";

      $params = [
        'CodEmpresa'    => $dados['editCodEmp'],
        'NNota'         => $dados['editNumNota'],
        'SeqIsv'        => $dados['editSeq'],
        'VlrBaseNota'   => $dados['editVlrBaseNota'],
        'VlrBaseIR'     => $dados['editVlrBaseIR'],
        'PercIR'        => $dados['editPercIR'],
        'VlrIR'         => $dados['editVlrIR'],
        'VlrBaseCSLL'   => $dados['editVlrBaseCSLL'],
        'PercCSLL'      => $dados['editPercCSLL'],
        'VlrCSLL'       => $dados['editVlrCSLL'],
        'VlrBasePIS'    => $dados['editVlrBasePIS'],
        'PercPIS'       => $dados['editPercPIS'],
        'VlrPIS'        => $dados['editVlrPIS'],
        'VlrBaseCOFINS' => $dados['editVlrBaseCOFINS'],
        'PercCOFINS'    => $dados['editPercCOFINS'],
        'VlrCOFINS'     => $dados['editVlrCOFINS']
      ];
    }

    // echo "<pre>";
    // var_dump($params);
    // var_dump($dados);
    // var_dump($sql);
    // die();

    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
  }
}
