<?php
require_once __DIR__ . '/../DBConnect.php';

class CirAjusteParcelasBB
{
  // Conexões
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  private function executeStatement(string $sql, array $params = []): void
  {
    $stmt = $this->gestor->prepare($sql);
    foreach ($params as $key => $value) {
      $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
      $stmt->bindValue($key, $value, $paramType);
    }
    $stmt->execute();
  }

  public function Consulta(array $numCon)
  {
    $sql =
      "SELECT NumeroDaParcela AS NumParcela, NumeroDoContrato AS NumContrato, Situacao, Dt_Sit
        FROM dr_AjusteBB 
      ";
    if (!empty($numCon)) {
      if (count($numCon) === 1) {
        $sql .= "\n WHERE NumeroDoContrato = :numCon";
        $params = [':numCon' => $numCon[0]];
      } else {
        $placeholders = [];
        $params = [];
        foreach ($numCon as $idx => $val) {
          $placeholder = ":numCon$idx";
          $placeholders[] = $placeholder;
          $params[$placeholder] = $val;
        }
        $sql .= "\n WHERE NumeroDoContrato IN (" . implode(',', $placeholders) . ")";
      }
    } else {
      $sql .= "\n WHERE Situacao = 'P'";
      $params = [];
    }

    $stmt = $this->gestor->prepare($sql);
    foreach ($params as $key => $value) {
      $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
      $stmt->bindValue($key, $value, $paramType);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function IncluirParcela(int $numParc, int $numCon)
  {
    $sql =
      "INSERT INTO dr_AjusteBB (NumeroDoContrato, NumeroDaParcela, NumeroDoTituloCliente, Situacao)
       VALUES (:numCon, :numParc, :numTitCli, :situacao)
      ";

    $numTitCli = '';
    $situacao = 'P';

    $params = [
      ':numCon' => $numCon,
      ':numParc' => $numParc,
      ':numTitCli' => $numTitCli,
      ':situacao' => $situacao,
    ];

    $this->executeStatement($sql, $params);
  }

  public function DeletaParcela(int $numParc, int $numCon)
  {
    $sql = "DELETE FROM dr_AjusteBB WHERE NumeroDoContrato = :numCon AND NumeroDaParcela = :numParc AND Situacao = 'P'";

    $params = [
      ':numCon' => $numCon,
      ':numParc' => $numParc,
    ];

    $this->executeStatement($sql, $params);
  }

  public function ProcessaParcelas(string $dtSelecionada)
  {
    $sql = "EXEC gestor.dbo.DR_AjustaTituloBB :dtSelecionada";

    $dtSelecionada = str_replace('-', '', $dtSelecionada);

    $stmt = $this->gestor->prepare($sql);
    $stmt->bindParam(':dtSelecinonada', $dtSelecionada, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
