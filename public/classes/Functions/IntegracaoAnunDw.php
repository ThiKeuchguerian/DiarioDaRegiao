<?php
require_once __DIR__ . '/../DBConnect.php';

class IntegracaoAnunDw
{
  private $dw;

  public function __construct()
  {
    $this->dw = DatabaseConnection::getConnection('dw');
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
      $where[]           = "NumeroAp LIKE :codAnuncio";
      $params[':codAnuncio'] = $codAnuncio . '%';

      // 3) Senão, se vier data início e fim, filtramos pelo mês/ano
    } elseif (!empty($MesAno)) {
      // Aqui assumimos que você realmente quer comparar a string MM/yyyy.
      $where[]             = "SUBSTRING(DataVeiculacao,5,2) + '/' + SUBSTRING(DataVeiculacao,1,4) = :MesAno";
      $params[':MesAno'] = $MesAno;
    }

    // 4) Se existe alguma condição, junta ao SQL
    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode("\n   AND ", $where);
    }

    // 5) Ordenação final
    $sql .= "\n ORDER BY NumeroAP";

    // echo "<pre>";
    // var_dump($sql);
    // die();
    // 6) Prepara e executa em um único passo
    $stmt = $this->dw->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
