<?php
require_once __DIR__ . '/../DBConnect.php';

class UploadController
{
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }
}
