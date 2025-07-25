<?php
require_once __DIR__ . '/../DBConnect.php';

class AnunciosPublicados
{
  // Conexões
  private $tecmidia;

  public function __construct()
  {
    $this->tecmidia = DatabaseConnection::getConnection('tecmidia');
  }

  /**
   * Extrai primeiro e último dia no formato YYYYMMDD a partir de 'MM/YYYY'
   */
  public static function obterPrimeiroUltimoDia(string $mesAno): array
  {
    $data = \DateTime::createFromFormat('m/Y', $mesAno);
    if (! $data) {
      throw new \InvalidArgumentException("Mês/Ano inválido: {$mesAno}");
    }
    $primeiro = $data->format('Ym01');               // YYYY-mm-dd
    $ultimo    = $data->modify('last day of this month')->format('Ymd');
    // echo "<pre>";
    // var_dump($primeiro, $ultimo);
    // die();
    return [$primeiro, $ultimo];
  }

  /**
   * @param array   $dados
   * @return array
   */
  public function consultaAnuncios($dados): array
  {
    $dtPublic = $dados['dtPublic'];
    $mesAno = $dados['MesAno'];

    $sql =
      "SELECT
          ad.takedate AS DtCapitacao,
          ins.pubdate AS DtPublicacao,
          ad.ad_number AS NumAnuncio, 
          sou.fullname AS Origem, 
          PT.fullname as TipoPag,
          CASE 
            WHEN cust.id_type = 0 THEN 'F'
            WHEN cust.id_type = 1 THEN 'J'
            WHEN cust.id_type = 7 THEN 'E'
            ELSE ''
          END AS TipoCli,
          cust.id_type,
          cust.id_value AS idCli, 
          cust.fullname AS nomeCli,
          con.nickname AS contato,
          sec.fullname AS secao,
          TituloTexto.Titulo,
          TituloTexto.Texto,
          bhad.fullname as CondFat, ins.charged_price as ValorAnuncio 
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'Bandeira') AS BandeiraCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'TipoOper') AS TipoOperCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'Parcelas') AS ParcelasCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'NumLote') AS NumLoteCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'NumDocto') AS NumDoctoCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'NumAutoriz') AS NumAutorizCartao
        FROM ec_ad ad WITH (NOLOCK)
          INNER JOIN ec_source sou WITH (NOLOCK) ON ad.source_id = sou.source_id
          LEFT JOIN dbo.EC_BillWhen bi WITH (NOLOCK) ON bi.billwhen_id = ad.billwhen_id
          INNER JOIN ec_customer cust WITH (NOLOCK) ON ad.customer_id = cust.customer_id
          LEFT JOIN ec_customer custi WITH (NOLOCK) ON ad.intermediate_id = custi.customer_id
          LEFT JOIN ec_customer custr WITH (NOLOCK) ON ad.representative_id = custr.customer_id
          INNER JOIN ec_section sec WITH (NOLOCK) ON sec.section_id = ad.section_id
          LEFT OUTER JOIN ec_billhow bhad with (nolock) on bhad.billhow_id = ad.billhow_id
          LEFT JOIN ec_adcontact adc WITH (NOLOCK) ON adc.ad_id = ad.ad_id AND adc.adcontact_id IN 
            (
              SELECT TOP 1 adcontact_id FROM ec_adcontact WITH (INDEX(IX_EC_AdContact), NOLOCK)
              WHERE ad_id = ad.ad_id
              ORDER BY ad_id, [level]
            )
          LEFT JOIN ec_contact con WITH (NOLOCK) ON con.contact_id = adc.contact_id
          INNER JOIN EC_PayType PT WITH (NOLOCK) ON PT.paytype_id = AD.paytype_id
          INNER JOIN EC_Insertion ins WITH (NOLOCK) ON ins.ad_id = ad.ad_id -- <-- Este join traz as publicações
          OUTER APPLY (
            SELECT
              MAX(CAST(CASE WHEN t.sequence = 1 THEN t.text END AS VARCHAR(MAX))) AS Titulo,
              MAX(CAST(CASE WHEN t.sequence = 4 THEN t.text END AS VARCHAR(MAX))) AS Texto
            FROM ec_textad tex WITH (NOLOCK)
            INNER JOIN EC_TextAdField t WITH (NOLOCK) ON tex.text_id = t.text_id
            WHERE tex.ad_id = ad.ad_id
            ) AS TituloTexto
          WHERE ad.status_id != 1 AND ad.queue_blocked = 0
      ";

    // Monta dinamenticamente o WHERE e o array de parâmetros
    $where  = [];
    $params = [];

    // Filtro de data
    $where[]  = '';
    $params[] = '';

    if (!empty($dtPublic)) {
      $where[] = 'ins.pubdate = :dtPublic';
      $params[':dtPublic'] = $dtPublic;
    }
    if (!empty($mesAno)) {
      list($dtInicio, $dtFim) = self::obterPrimeiroUltimoDia($mesAno);
      $where[] = '(ins.pubdate >= :dtInicio AND ins.pubdate <= :dtFim)';
      $params[':dtInicio'] = $dtInicio;
      $params[':dtFim'] = $dtFim;
    }

    if (count($where) > 0) {
      $sql .= "\n " . implode(" AND ", $where);
    }
    
    $sql .= "\n ORDER BY ins.pubdate, ad.ad_number";

    // depurar($sql);
    // Prepara e executa
    $stmt = $this->tecmidia->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
