<?php

class DatabaseConnection
{
  private static $instances = [];

  public static function getConnection($db)
  {
    if (!isset(self::$instances[$db])) {
      switch ($db) {
        case 'dw':
          $dsn = "sqlsrv:Server=10.64.0.11,1433;Database=dw";
          $user = 'dw';
          $pass = 'dw';
          break;

        case 'DrGestor':
          $dsn = "sqlsrv:Server=10.64.0.11,1433;Database=DrGestor";
          $user = 'dw';
          $pass = 'dw';
          break;

        case 'senior':
          $dsn = "sqlsrv:Server=10.64.0.90,1433;Database=sapiens";
          $user = 'sapiens';
          $pass = 'sapiens';
          break;

        case 'seniorTeste':
          $dsn = "sqlsrv:Server=10.64.0.36,1433;Database=sapiens_teste";
          $user = 'sapiens_teste';
          $pass = 'sapiensteste';
          break;

        case 'gestor':
          $dsn = "sqlsrv:Server=10.64.0.11,1433;Database=gestor";
          $user = 'gestor';
          $pass = 'G3s10R_LL';
          break;

        case 'gestorTeste':
          $dsn = "sqlsrv:Server=10.64.0.14,1433;Database=gestor";
          $user = 'gestor';
          $pass = 'gestor';
          break;

        case 'totvs':
          $dsn = "sqlsrv:Server=10.64.0.7\MICROSIGA;Database=PROTHEUS";
          $user = 'sa';
          $pass = 'sa';
          break;

        case 'capt':
          $dsn = "sqlsrv:Server=10.64.0.11,1433;Database=capt";
          $user = 'capt';
          $pass = 'captweb';
          break;

        case 'cadena':
          $dsn = "sqlsrv:Server=10.64.0.25\SQLEXPRESS;Database=Cadena";
          $user = 'capt';
          $pass = 'capt';
          break;

        case 'tecmidia':
          $dsn = "sqlsrv:Server=10.64.0.13,1433;Database=Easyclass";
          $user = 'tecmidia';
          $pass = 'tecsams';
          break;

        case 'webtake':
          $dsn = "sqlsrv:Server=172.16.0.15,1433;Database=WebTake";
          $user = 'tecmidia';
          $pass = 'tecsams';
          break;

        case 'flip':
          $dsn = "mysql:host=168.138.157.203;port=3317;dbname=websquad;charset=utf8";
          $user = 'root';
          $pass = 'P0hRk6aON10f';
          break;
        
        case 'publegal':
          $dsn = "mysql:host=168.75.90.69;port=3312;dbname=db_publicidade_legal;charset=utf8";
          $user = "root";
          $pass = "Mt2g2MCPfA4jnWj6Z3EM";
          break;

        default:
          throw new Exception("Conexão '$db' não definida.");
      }

      try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$instances[$db] = $pdo;
      } catch (PDOException $e) {
        die("Erro ao conectar no banco $db: " . $e->getMessage());
      }
    }

    return self::$instances[$db];
  }
}
