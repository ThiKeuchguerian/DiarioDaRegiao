<?php
require_once __DIR__ . '/../DBConnect.php';

class IntegracaoAnunDw
{
  private $dw;
  private $tots;

  public function __construct()
  {
    $this->dw = DatabaseConnection::getConnection('dw');
    $this->tots = DatabaseConnection::getConnection('totvs');
  }
  /**
   * Extrai primeiro e último dia no formato YYYYMMDD a partir de 'MM/YYYY'
   */
  public static function obterPrimeiroUltimoDia(string $MesAno): array
  {
    $data = \DateTime::createFromFormat('m/Y', $MesAno);
    if (! $data) {
      throw new \InvalidArgumentException("Mês/Ano inválido: {$MesAno}");
    }
    $primeiro = $data->format('Ym01');               // YYYY-mm-dd
    $ultimo    = $data->modify('last day of this month')->format('Ymd');
    // echo "<pre>";
    // var_dump($primeiro, $ultimo);
    // die();
    return [$primeiro, $ultimo];
  }

  /**
   * @param string|null $MesAno       Data no formato MM/yyyy (ex: "05/2025")
   * @param string|null $codAnuncio   Código do anúncio (busca LIKE), ex: "123"
   * @return array                    Resultado do fetchAll(PDO::FETCH_ASSOC)
   */
  function consultaAnunciosDw(
    ?string $MesAno,
    ?string $codAnuncio
  ): array {
    // 1) Monta SELECT base
    $sql    = "SELECT * FROM DM1FT_VeiculacaoAnuncios";
    $where  = [];
    $params = [];

    // 2) Se veio codAnuncio, usamos LIKE e ignoramos data
    if (!empty($codAnuncio)) {
      $where[] = "NumeroAp LIKE :codAnuncio";
      $params[':codAnuncio'] = $codAnuncio . '% ';

      // Se vier data início e fim, filtramos pelo mês/ano
    }
    if (!empty($MesAno)) {
      // Aqui assumimos que você realmente quer comparar a string MM/yyyy.
      $where[] = "SUBSTRING(DataVeiculacao,5,2) + '/' + SUBSTRING(DataVeiculacao,1,4) = :MesAno";
      $params[':MesAno'] = $MesAno;
    }

    // 4) Se existe alguma condição, junta ao SQL
    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode("\n   AND ", $where);
    }

    // 5) Ordenação final
    $sql .= "\n ORDER BY Origem, NumeroAP";

    // echo "<pre>";
    // var_dump($sql);
    // die();
    // 6) Prepara e executa em um único passo
    $stmt = $this->dw->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  function consultaAnunciosProtheus(
    ?string $MesAno,
    ?string $codAnuncio
  ): array {

    if (!empty($MesAno)) {
      // Obtendo primeiro e ultimo dia do mes
      list($primeiroDia, $ultimoDia) = self::obterPrimeiroUltimoDia($MesAno);
    }

    $sql =
      "SELECT Ped.ZP_FLPROC AS FPro, Ped.ZP_NUMORI AS Num, Ped.ZP_CLIENTE AS Cli, 
          Ped.ZP_EMISSAO AS DtEmi, Ped.ZP_DTGERA AS DtGe, Ped.ZP_VEND1 AS CodVen, Ped.D_E_L_E_T_ AS Del, 
          Iped.ZQ_PRODUTO AS CodProd, Iped.ZQ_ENTREG AS DtVeic, Iped.ZQ_VALOR AS Vlr,
          PedInt.C5_NUM AS NumPed, PedInt.C5_LOTE AS LOTE, PedInt.C5_XNUMORI AS PedInt, Ped.ZP_ERRPROC AS Erro
        FROM  SZP010 Ped
        INNER JOIN SZQ010 Iped ON Ped.ZP_FILIAL = Iped.ZQ_FILIAL AND Ped.ZP_CLIENTE = Iped.ZQ_CLI AND Ped.ZP_NUMORI = Iped.ZQ_NUMORI
        LEFT JOIN SC5010 PedInt ON Ped.ZP_FILIAL = PedInt.C5_FILIAL AND Ped.ZP_NUMORI = PedInt.C5_XNUMORI AND Ped.ZP_LOTE = PedInt.C5_LOTE AND Ped.ZP_CLIENTE = PedInt.C5_CLIENTE
    ";

    $where  = [];
    $params = [];

    // Se veio codAnuncio, usamos LIKE e ignoramos data
    if (!empty($codAnuncio)) {
      $where[]           = "Ped.ZP_NUMORI LIKE :codAnuncio";
      $params[':codAnuncio'] = $codAnuncio . '%';

      // Senão, se vier data início e fim, filtramos pelo mês/ano
    }
    if (!empty($MesAno)) {
      // Aqui assumimos que você realmente quer comparar a string MM/yyyy.
      $where[]             = "Iped.ZQ_ENTREG BETWEEN :primeiroDia AND :ultimoDia";
      $params[':primeiroDia'] = $primeiroDia;
      $params[':ultimoDia']   = $ultimoDia;
    }

    // Se existe alguma condição, junta ao SQL
    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode("\n   AND ", $where);
    }

    // 5) Ordenação final
    $sql .= "\n ORDER BY Ped.ZP_NUMORI";

    // echo "<pre>";
    // var_dump($sql);
    // die();

    $stmt = $this->tots->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
