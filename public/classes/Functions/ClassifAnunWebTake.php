<?php
require_once __DIR__ . '/../DBConnect.php';

class ClassifAnunWebTake
{
  private $webtake;

  public function __construct()
  {
    $this->webtake  = DatabaseConnection::getConnection('webtake') ;
  }

  // Retorna lista de usuários para selecionar
  public function ConsultaUsarios()
  {
    $query = "SELECT U.user_id, S.source_id, U.fullname, users.id_value from EXT_Source S
      INNER JOIN EXT_User U ON U.source_id=S.source_id
      INNER JOIN DR13.easyclass.dbo.EC_User users ON users.user_id=U.user_id
      order by U.fullname";

    //Prepara e executa
    $stmt = $this->webtake->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * @param string      $MesAno    data (ex: '01/2025')
   * @param string      $Usuario   nome de usuário ou 0 para todos
   * @return array
   */
  public function ConsultaAnunciosWebTake(
    string      $MesAno,
    ?string     $Usuario
  ): array {

    $query = "SELECT adtrans_id,process_id,processstatus_id,error_msg,paytype_name,customertrans_id,custacctype_name,
        custacc_name,intermid_type,intermid_value,intermacctype_name,intermacc_name,represenid_type,
        represenid_value,represenacctype_name,represenacc_name,created_date,created_by,takedate,
        source_name,adtaker_name,section_name,style_name,mediatype_name,authorization_code,adbook_name,
        position_name,text_columns,text_depth,ad_number,payment_status,increase,discount,direct_price,
        external_ad_price,billwhen_name,billhow_name,intermrule_name,mediastatus_name,contract,
        syndicate_name,codsyndicate,num_batch,information,session_id,typetable,insertion,timestamp,
        case 
          when error_msg = '' then ''
		      else SUBSTRING(error_msg, 2, len(error_msg) - 1) 
	      end as ErrorMsg
      FROM EXT_AdTrans 
      WHERE processstatus_id = '6' "
    ;

    // Monta dinamicamente o where
    $params = [];

    // Ordenando
    $OrderBy = "ORDER BY created_date";

    // Filtro do MesAno
    if ($MesAno != '') {
      $query .= " AND FORMAT(created_date, 'MM/yyyy') = ? ";
      $params[] = $MesAno;
    }

    // Filtro do Usuario
    if ($Usuario != '0') {
      $query .= " AND adtaker_name = ? ";
      $params[] = $Usuario;
    } 

    // Juntando Tudo
    $sql = $query . $OrderBy;

    // Verifica a query
    // echo "<pre>";
    // var_dump($sql);
    // die();

    // Prepara e executa
    $stmt = $this->webtake->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
