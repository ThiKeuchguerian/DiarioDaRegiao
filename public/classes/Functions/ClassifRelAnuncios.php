<?php
require_once __DIR__ . '/../DBConnect.php';

class ClassifRelAnuncios
{
  // Conexões
  private $tecmidia;

  public function __construct()
  {
    $this->tecmidia = DatabaseConnection::getConnection('tecmidia');
  }

  /**
   * @param string      $DtCaptacao  data (ex: '2025-02-01')
   * @param string|null $bandeira    nome da bandeira ou 'Todas'
   * @param int|null    $integracao  1=integrados, 2=não integrado
   * @return array
   */

  public function ConsultaAnuncios(
    string  $DtCaptacao,
    ?string $Bandeira,
    ?int    $Integracao
  ): array {
    $query = "SELECT  ad.takedate AS DataCaptacao, ad.ad_number AS NumAnuncio, sou.fullname AS Origem, PT.fullname as TipoPag,
        case when cust.id_type = 0 then 'CPF' else case when cust.id_type = 1 then 'CNPJ' else case when cust.id_type = 7 then 'ESTRANG' else ' ' end end end as Cliente_TipoId,
        isnull(convert(varchar(18), DBO.[EC_Format_CNPJ_CPF] (cust.id_type,cust.id_value)), '') AS Cliente_Id, 
        isnull(left(cust.fullname, 60),'') as Cliente_Nome, isnull(con.nickname,'') as Contato, isnull(sec.fullname,'') as Secão,
        bhad.fullname as CondFat, ad.charged_price as ValorAnuncio 
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'Bandeira') AS BandeiraCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'TipoOper') AS TipoOperCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'Parcelas') AS ParcelasCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'NumLote') AS NumLoteCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'NumDocto') AS NumDoctoCartao
        , [dbo].[EC_GetCustomTextFieldOfAd](ad.ad_id, 'NumAutoriz') AS NumAutorizCartao
        FROM ec_ad ad with (nolock)  
          inner join ec_source sou with (nolock) on ad.source_id = sou.source_id
          left join dbo.EC_BillWhen bi with (nolock)on bi.billwhen_id = ad.billwhen_id
          inner loop join ec_customer cust with (nolock) on ad.customer_id = cust.customer_id
          left outer loop join ec_customer custi with (nolock) on ad.intermediate_id = custi.customer_id
          left outer loop join ec_customer custr with (nolock) on ad.representative_id = custr.customer_id
          inner loop join ec_section sec with (nolock) on sec.section_id = ad.section_id
          inner loop join ec_textad tex with (nolock) on tex.ad_id = ad.ad_id
          inner loop join ec_style sty with (nolock) on sty.style_id = tex.style_id
          left outer join ec_billhow bhad with (nolock) on bhad.billhow_id = ad.billhow_id
          left outer join ec_adcontact adc with (nolock) on adc.ad_id = ad.ad_id and adc.adcontact_id in (select top 1 adcontact_id from ec_adcontact with (index(IX_EC_AdContact) nolock) where ad_id=ad.ad_id order by ad_id, [level])
          left outer loop join ec_contact con with (nolock) on con.contact_id = adc.contact_id
          inner join EC_PayType PT with (nolock) on PT.paytype_id = AD.paytype_id
      WHERE (ad.status_id != 1 and ad.queue_blocked = 0)
    ";

    // Monta dinamenticamente o WHERE e o array de parâmetros
    $where  = [];
    $params = [];

    // Filtro de data
    $DtInicio = $DtCaptacao;
    $DtFim    = $DtCaptacao;
    $where[]  = "ad.takedate >= :DtInicio";
    $params[':DtInicio'] = $DtInicio;
    if ($DtFim) {
      $where[]         = "ad.takedate <= :DtFim";
      $params['DtFim'] = $DtFim;
    }

    // Filtro da Bandeira
    if ($Bandeira !== null && $Bandeira !== '' && $Bandeira !== 'Todas') {
      $where[]             = "dbo.EC_GetCustomTextFieldOfAd(ad.ad_id,'Bandeira') = :Bandeira";
      $params[':Bandeira'] = $Bandeira;
    } elseif ($Bandeira !== null && $Bandeira !== '' && $Bandeira === 'Todas') {
      $where[]             = "PT.fullname = 'Cartão'";
    }

    // Filtro de Integração
    if ($Integracao === 1) {
      $where[] = "(bhad.fullname not in ('Bonificado','Cortesia','Compensação') OR bhad.fullname IS NULL)";
      $where[] = "sou.fullname <> 'Paginação' ";
    } elseif ($Integracao === 2) {
      $where[] = "(bhad.fullname in ('Bonificado','Cortesia') OR sou.fullname in ('Paginação'))";
    }

    // Ordenando
    $OrderBy  = " order by ad.ad_number, ad.takedate";

    // Juntando tudo
    $sql = $query . "\n AND " . implode("\n AND ", $where);
    $sql = $sql . "\n " . $OrderBy;

    // Verifica a montagem do SQL
    // echo "<pre>";
    // var_dump($sql);
    // die();

    // Prepara e executa
    $stmt = $this->tecmidia->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
