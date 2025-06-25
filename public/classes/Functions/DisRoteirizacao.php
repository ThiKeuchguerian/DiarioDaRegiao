<?php
require_once __DIR__ . '/../DBConnect.php';

class DisRoteirizacao
{
  // Conexões
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  public function ConsultaRoteirizacao()
  {
    $query = "SELECT Dom, Seg, Ter, Qua, Qui, Sex, Sab, nomeRazaoSocial, descricaoDoPlanoDePagamento, nomeDoSetorDeEntrega, siglaTipoLogradouro,
      nomeDoLogradouro, numeroDoEndereco, nomeDoBairro, nomeDoMunicipio, siglaDaUF, cep
      FROM gestor.dbo.drEnderecoDeEntrega
    ";
    // echo "<pre>";
    // var_dump($query);
    // die();
    $stmt = $this->gestor->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
