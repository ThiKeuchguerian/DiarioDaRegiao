<?php
require_once __DIR__ . '/../DBConnect.php';

class CirConsultaCode
{
  // Conexões
  private $contaDiario;

  public function __construct()
  {
    $this->contaDiario = DatabaseConnection::getConnection('contaDiario');
  }

  public function consultaCode($dados)
  {
    $nomeAss = $dados['nomeAss'];
    $emailAss = $dados['emailAss'];

    $sql =
      "SELECT c.name, c.email, c.createdAt, c.updatedAt, r.code, r.expiresAt 
        FROM customers c
        INNER JOIN recovery_codes r ON c.id = r.customerId
      ";

    $where = [];
    $params = [];

    if ($nomeAss != '') {
      $where[] = 'name LIKE :nomeAss';
      $params[':nomeAss'] = $nomeAss . '%';
    }
    if ($emailAss != '') {
      $where[] = 'email LIKE :emailAss';
      $params[':emailAss'] = $emailAss . '%';
    }

    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode("\n AND ", $where);
    }

    $stmt = $this->contaDiario->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
