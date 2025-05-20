<?php

require_once __DIR__ . '/../DBConnect.php';

class VerificarNFEntrada
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  /**
   * Retorna os dados do fornecedor
   */
  public function consultaFornecedor(string $codFor): array
  {
    $sql = 
      " SELECT codfor, nomfor, recipi, recicm, recpis, reccof, triiss, retour, retirf, retpro, triipi, triicm
        FROM e095for
        WHERE codfor = :codfor
      ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':codfor' => $codFor]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Busca a nota base em E660INC
   */
  public function consultaNotaBase(int $codEmp, string $numNota): array
  {
    $sql = 
      " SELECT codemp, numnfi, codfor, vlrctb, pericm, vlrbic, vlricm,
          vlroic, vlriip, vlroip, perpir, vlrbpr, vlrpir, percor, vlrbcr, vlrcor,
          (vlrirf + vlrcsl + vlrpit + vlrcrt) AS VlrRetencao
        FROM E660INC 
        WHERE numnfi = :numNota AND codemp = :codEmp
      ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':numNota' => $numNota,
      ':codEmp'  => $codEmp
    ]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Calcula a “nota deveria” aplicando as alíquotas fixas
   */
  public function consultaNotaDeveria(int $codEmp, string $numNota): array
  {
    $sql = 
      "SELECT codemp, numnfi, codfor, vlrctb, pericm, vlrbic, vlricm,
        vlroic, vlriip, vlroip, 1.6500   AS perpir,
        (vlrctb - vlricm)                      AS vlrbpr,
        ((vlrctb-vlricm)*1.65)/100            AS vlrpir,
        7.6000  AS percor,
        (vlrctb - vlricm)                     AS vlrbcr,
        ((vlrctb-vlricm)*7.6)/100             AS vlrcor
      FROM E660INC
      WHERE numnfi = :numNota
        AND codemp = :codEmp
      ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':numNota' => $numNota,
      ':codEmp'  => $codEmp
    ]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Executa os updates de acordo com o botão pressionado
   */
  public function corrigirRetencoes(array $post): void
  {
    $codEmp  = (int)$post['CodEmp'];
    $numNota = $post['NumNota'];
    $tipo    = $post['TipoRetencao'] ?? '';

    // mapeia o SET da nota fiscal
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
    if (isset($mapNFV[$tipo])) {
      $sql = "
            UPDATE e140nfv
               SET {$mapNFV[$tipo]}
             WHERE numnfv = :numNota
               AND codemp = :codEmp
               AND codsnf = 'NSC'
          ";
      $stmt = $this->senior->prepare($sql);
      $stmt->execute([
        ':numNota' => $numNota,
        ':codEmp' => $codEmp
      ]);
    }

    // mesmo para E140ISV (substitua campos p/ ISV conforme NFV)
    $mapISV = [
      'Todas' => "vlrbir=vlrlse, perirf=4.80, vlrirf=(vlrlse*4.8)/100,
                      vlrbcl=vlrlse, percsl=1.00, vlrcsl=(vlrlse*1)/100,
                      vlrbpt=vlrlse, perpit=0.65, vlrpit=(vlrlse*0.65)/100,
                      vlrbct=vlrlse, percrt=3.00, vlrcrt=(vlrlse*3.00)/100",
      'IR'    => "vlrbir=vlrlse, perirf=4.80, vlrirf=(vlrlse*4.8)/100",
      'CSLL'  => "vlrbcl=vlrlse, percsl=1.00, vlrcsl=(vlrlse*1)/100,
                      vlrbpt=vlrlse, perpit=0.65, vlrpit=(vlrlse*0.65)/100,
                      vlrbct=vlrlse, percrt=3.00, vlrcrt=(vlrlse*3.00)/100"
    ];
    if (isset($mapISV[$tipo])) {
      $sql = "
            UPDATE e140isv
               SET {$mapISV[$tipo]}
             WHERE numnfv = :numNota
               AND codemp = :codEmp
               AND codsnf = 'NSC'
          ";
      $stmt = $this->senior->prepare($sql);
      $stmt->execute([
        ':numNota' => $numNota,
        ':codEmp' => $codEmp
      ]);
    }
  }
}
