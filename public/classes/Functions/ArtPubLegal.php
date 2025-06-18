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
    $nomeEmp  = $dados['Empresa']  ?? '';
    $titulo   = $dados['Titulo']   ?? '';
    $dtInicio = $dados['DtInicio'] ?? '';
    $dtFim    = $dados['DtFim']    ?? '';

    $sql =
      "SELECT id, status, company, title, digital, printed, created_at AS DtPublicacao, updated_at 
        FROM archives 
      ";

    $where  = [];
    $params = [];

    if ($nomeEmp !== '') {
      $where[] = 'company LIKE :nomeEmp';
      $params[':nomeEmp'] = $nomeEmp . '%';
    }
    if ($titulo !== '') {
      $where[] = 'title LIKE :titulo';
      $params[':titulo'] = '%' . $titulo . '%';
    }
    if ($dtInicio !== '' && $dtFim === '') {
      $where[] = 'CAST(created_at AS DATE) = :dtInicio';
      $params[':dtInicio'] = $dtInicio;
    }
    if ($dtInicio !== '' && $dtFim !== '') {
      $where[] = 'CAST(created_at AS DATE) BETWEEN :dtInicio and :dtFim';
      $params[':dtInicio'] = $dtInicio;
      $params[':dtFim'] = $dtFim;
    }

    if (count($where)) {
      $sql .= "\n WHERE " . implode(" AND ", $where);
    }

    $sql .= "\n ORDER BY created_at DESC";

    $stmt = $this->publegal->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertPub(array $dados): array
  {
    $company = $dados['company'] ?? '';
    $title   = $dados['title'] ?? '';
    $arqDig  = $dados['arquivo_digital'] ?? '';
    $arqImp  = $dados['arquivo_impresso'] ?? '';

    $insert = "INSERT INTO archives (status, company, title, digital, printed, created_at, updated_at)
               VALUES (:status, :company, :title, :digital, :printed, :created_at, :updated_at)";

    $params = [
      ':status'     => 1,
      ':company'    => $company,
      ':title'      => $title,
      ':digital'    => $arqDig,
      ':printed'    => $arqImp,
      ':created_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
      ':updated_at' => date('Y-m-d H:i:s')
    ];


    $stmt = $this->publegal->prepare($insert);
    $stmt->execute($params);

    return ['success' => true, 'id' => $this->publegal->lastInsertId()];
  }

  public function updatePub(array $dados): array
  {
    $id      = $dados['id'] ?? '';
    $company = $dados['company'] ?? '';
    $title   = $dados['title'] ?? '';
    $dtPub   = $dados['DtPublicacao'] ?? '';
    if (!empty($dtPub)) {
      $partes = explode('/', $dtPub);
      if (count($partes) === 3) {
        $dtPub = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
      }
    }
    $arqDig  = $dados['arquivo_digital'] ?? '';
    $arqImp  = $dados['arquivo_impresso'] ?? '';

    $update = "UPDATE archives";

    $set    = [];
    $params = [];

    if ($company !== '') {
      $set[] = 'company = :company';
      $params[':company'] = $company;
    }
    if ($title !== '') {
      $set[] = 'title = :title';
      $params[':title'] = $title;
    }
    if (!empty($dtPub)) {
      $set[] = 'created_at = :dtPub';
      $params[':dtPub'] = $dtPub;
    }
    if ($arqDig !== '') {
      $set[] = 'digital = :arqDig';
      $params[':arqDig'] = $arqDig;
    }
    if ($arqImp !== '') {
      $set[] = 'printed = :arqImp';
      $params[':arqImp'] = $arqImp;
    }

    if (count($set) > 0) {
      $update .= "\n SET " . implode(", ", $set);
    } else {
      return ['success' => false, 'message' => 'Nenhum campo para atualizar.'];
    }

    if ($id !== '') {
      $update .= "\n WHERE id = :id";
      $params[':id'] = $id;
    } else {
      return ['success' => false, 'message' => 'ID não informado.'];
    }
    // depurar($params, $update);
    $stmt = $this->publegal->prepare($update);
    $stmt->execute($params);

    return ['success' => true];
  }

  public function deletePub(array $dados): int
  {
    $id = $dados['id'] ?? '';

    if ($id === '') {
      return 0;
    }

    $delete = "DELETE FROM archives WHERE id = :id";
    $param = [':id' => $id];

    $stmt = $this->publegal->prepare($delete);
    $stmt->execute($param);

    return $stmt->rowCount();
  }
}
