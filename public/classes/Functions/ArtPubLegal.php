<?php
// require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../DBConnect.php';

// use phpseclib3\Net\SFTP;

class UploadController
{
  private $publegal;

  public function __construct()
  {
    $this->publegal = DatabaseConnection::getConnection('publegal');
  }

  public function consultaPub(array $dados): array
  {
    $nomeEmp  = $dados['Empresa'];
    $titulo   = $dados['Titulo'];
    $dtInicio = $dados['DtInicio'];
    $dtFim    = $dados['DtFim'];

    // depurar($dados, $nomeEmp, $titulo, $dtInicio, $dtFim);
    $sql =
      "SELECT id, status, company, title, digital, printed, created_at AS DtPublicacao, updated_at 
        FROM archives 
      ";

    $where  = [];
    $params = [];

    if ($nomeEmp != '') {
      $where[] = 'company LIKE :nomeEmp';
      $params[':nomeEmp'] = $nomeEmp . '%';
    }
    if ($titulo != '') {
      $where[] = 'title LIKE :titulo';
      $params[':titulo'] = '%' . $titulo . '%';
    }
    if ($dtInicio != '' && $dtFim == '') {
      $where[] = 'created_at = :dtInicio';
      $params[':dtInicio'] = $dtInicio;
    }
    if ($dtInicio != '' && $dtFim != '') {
      $where[] = 'created_at BETWEEN :dtInicio and :dtFim';
      $params = [
        ':dtInicio' => $dtInicio,
        ':dtFim' => $dtFim
      ];
    }

    if (count($where)) {
      $sql .= "\n WHERE " . implode(" AND ", $where);
    }

    $sql .= "\n ORDER BY created_at DESC";

    $stmt = $this->publegal->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function updatePub(array $dados): array
  {
    $id      = $dados['id'];
    $company = $dados['company'];
    $title   = $dados['title'];
    // Converte data de dd/mm/YYYY para YYYY-mm-dd
    $dtPub = $dados['DtPublicacao'];
    if (!empty($dtPub)) {
      $partes = explode('/', $dtPub);
      if (count($partes) === 3) {
        $dtPub = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
      }
    }
    $arqDig  = $dados['arquivo_digital'];
    $arqImp  = $dados['arquivo_impresso'];

    // depurar($id, $company, $title, $dtPub, $arqDig, $arqImp);

    $update = " UPDATE archives ";

    $set    = [];
    $params = [];

    if ($company != '') {
      $set[] = 'company = :company';
      $params[':company'] = $company;
    }
    if ($title != '') {
      $set[] = 'title = :title';
      $params[':title'] = $title;
    }
    if (!empty($dtPub)) {
      $set[] = 'created_at = :dtPub';
      $params[':dtPub'] = $dtPub;
    }
    if ($arqDig != '') {
      $set[] = 'digital = :arqDig';
      $params[':arqDig'] = $arqDig;
    }
    if ($arqImp != '') {
      $set[] = 'printed = :arqImp';
      $params[':arqImp'] = $arqImp;
    }

    if (count($set) > 0) {
      $update .= "\n SET " . implode(", ", $set);
    }

    $where = [];
    $param = [];
    if ($id != '') {
      $where = 'id = :id';
      $param[':id'] = $id;
    }

    $update .= "\n WHERE " . $where;
    depurar($update, $params, $param);
    $stmt = $this->publegal->prepare($update);
    $stmt->execute($params, $param);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function deletePub(array $dados): int
  {
    $id      = $dados['id'];
    $company = $dados['company'];
    $title   = $dados['title'];
    // Converte data de dd/mm/YYYY para YYYY-mm-dd
    $dtPub = $dados['DtPublicacao'];
    if (!empty($dtPub)) {
      $partes = explode('/', $dtPub);
      if (count($partes) === 3) {
        $dtPub = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
      }
    }
    $arqDig  = $dados['arquivo_digital'];
    $arqImp  = $dados['arquivo_impresso'];

    // depurar($id, $company, $title, $dtPub, $arqDig, $arqImp);

    $delete = "DELETE FROM archives";

    $where = [];
    $param = [];
    if ($id != '') {
      $where[] = 'id = :id';
      $param[':id'] = $id;
    }

    $delete .= "\n WHERE " . implode(" ", $where);
    depurar($delete, $param);
    $stmt = $this->publegal->prepare($delete);
    $stmt->execute($param);
  }
}
