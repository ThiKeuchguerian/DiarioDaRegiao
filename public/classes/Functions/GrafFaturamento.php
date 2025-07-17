<?php
require_once __DIR__ . '/../DBConnect.php';

class GraficaFaturamento
{
  private $gdc;

  public function __construct()
  {
    $this->gdc = DatabaseConnection::getConnection('gdc');
  }

  public function consultaFaturamento(array $dados): array
  {
    $dtInicio = $dados['dtInicio'];
    $dtFim = $dados['dtFim'];
    $tipo = $dados['tipo'];
    $cliente = $dados['cliente'];
    $arte = $dados['arte'];
    $faturado = $dados['faturado'];
    // depurar(($dados));
    $where  = [];
    $params = [];

    $sql =
      "SELECT ID, DiaSemana, DataRodagem, Cliente, Arte, Tipo, Formato, 
          Papel, QtdeCor, Tiragem, Valor, Faturado, Obs, NumPedido, NumPedCli
        FROM GraficaFaturamento
      ";

    if (!empty($dtInicio) && !empty($dtFim)) {
      $where[] = "DataRodagem BETWEEN :dtInicio AND :dtFim";
      $params[':dtInicio'] = $dtInicio;
      $params[':dtFim'] = $dtFim;
    }
    if (!empty($dtInicio) && empty($dtFim)) {
      $where[] = "DataRodagem = :dtInicio";
      $params[':dtInicio'] = $dtInicio;
    }

    if (!empty($tipo) && $tipo != 0) {
      $where[] = "Tipo = :tipo";
      $params[':tipo'] = $tipo;
    }
    if (!empty($cliente)) {
      $where[] = "Cliente LIKE :cliente";
      $params[':cliente'] = $cliente . '%';
    }
    if (!empty($arte)) {
      $where[] = "Arte LIKE :arte";
      $params[':arte'] = '%' . $arte . '%';
    }
    if (!empty($faturado)) {
      $where[] = "Faturado = :faturado";
      $params[':faturado'] = $faturado;
    }

    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode(' AND ', $where);
    }

    $sql .= "\n ORDER BY Tipo, Papel, Formato, Cliente";

    $stmt = $this->gdc->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function relatorioFaturamento(array $dados): array
  {
    $dtInicio = $dados['dtInicio'];
    $dtFim = $dados['dtFim'];
    $tipo = $dados['tipo'];
    $cliente = $dados['cliente'];
    $arte = $dados['arte'];
    $faturado = $dados['faturado'];

    $where  = [];
    $params = [];

    $sql =
      "SELECT Gra.ID, Gra.DiaSemana, DataRodagem, Gra.Cliente, Gra.Arte, Gra.Tipo, Gra.Formato, Gra.Papel, 
          Gra.QtdeCor, Gra.Tiragem, Gra.Valor, Gra.Faturado, Gra.Obs, Gra.NumPedido, Gra.NumPedCli,
          ped.codcli, ped.numped, nf.numnfv, ped.pedcli, ped.codcpg, ped.vlrori
        FROM GraficaFaturamento gra
          LEFT OUTER JOIN DR90.sapiens.dbo.e120ped ped WITH (NOLOCK) ON gra.NumPedido = ped.numped
          LEFT OUTER JOIN DR90.sapiens.dbo.e120ipd iped WITH (NOLOCK) ON ped.codemp = iped.codemp AND ped.codfil = iped.codfil AND ped.numped = iped.numped
          LEFT OUTER JOIN DR90.sapiens.dbo.e140ipv inf WITH (NOLOCK) ON ped.codemp = inf.codemp AND ped.codfil = inf.codfil AND ped.numped = inf.numped AND iped.seqipd = inf.seqipd
          LEFT OUTER JOIN DR90.sapiens.dbo.e140nfv nf WITH (NOLOCK) ON inf.codemp = nf.codemp AND inf.codfil = nf.codfil AND inf.numnfv = nf.numnfv AND inf.codsnf = nf.codsnf
      ";

    if (!empty($dtInicio) && !empty($dtFim)) {
      $where[] = "DataRodagem BETWEEN :dtInicio AND :dtFim";
      $params[':dtInicio'] = $dtInicio;
      $params[':dtFim'] = $dtFim;
    }
    if (!empty($dtInicio) && empty($dtFim)) {
      $where[] = "DataRodagem = :dtInicio";
      $params[':dtInicio'] = $dtInicio;
    }

    if (!empty($tipo) && $tipo != 0) {
      $where[] = "Tipo = :tipo";
      $params[':tipo'] = $tipo;
    }
    if (!empty($cliente)) {
      $where[] = "Cliente LIKE :cliente";
      $params[':cliente'] = $cliente . '%';
    }
    if (!empty($arte)) {
      $where[] = "Arte LIKE :arte";
      $params[':arte'] = '%' . $arte . '%';
    }
    if (!empty($faturado)) {
      $where[] = "Faturado = :faturado";
      $params[':faturado'] = $faturado;
    }

    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode(' AND ', $where);
    }
    $sql .= "\n GROUP BY Gra.ID, Gra.DiaSemana, DataRodagem, Gra.Cliente, Gra.Arte, Gra.Tipo, Gra.Formato, 
          Gra.Papel, Gra.QtdeCor, Gra.Tiragem, Gra.Valor, Gra.Faturado, Gra.Obs, Gra.NumPedido, 
          Gra.NumPedCli, ped.codcli, ped.numped, nf.numnfv, ped.pedcli, ped.codcpg, ped.vlrori";
    $sql .= "\n ORDER BY Tipo, DataRodagem, Cliente";

    $stmt = $this->gdc->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function editarFaturamento(array $dados)
  {
    $ID = $dados['ID'];
    $Cliente = $dados['Cliente'];
    $Arte = $dados['Arte'];
    $Tipo = $dados['Tipo'];
    $Formato = $dados['Formato'];
    $Papel = $dados['Papel'];
    $QtdeCor = $dados['QtdeCor'];
    $Tiragem = $dados['Tiragem'];
    $Valor = floatval($dados['Valor']);
    $Faturado = $dados['Faturado'];
    $Obs = $dados['Obs'];
    $NumPedido = $dados['NumPedido'];
    $NumPedCli = $dados['NumPedCli'];
    
    if ($dados['DataRodagem'] !== '') {
      $dt = DateTime::createFromFormat('d/m/Y', $dados['DataRodagem']);
      $DataRodagem = $dt->format('Y-m-d');
    } else {
      $DataRodagem = '';
    }

    // Gerando Dia da Semana de acordo com a data
    $Dt = DateTime::createFromFormat('Y-m-d', $DataRodagem);
    $DiaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
    $IndiceDia = (int)$Dt->format('w');
    $DiaSemana = $DiaSemana[$IndiceDia];

    if ($ID !== '') {
      $DataLog = date('Y-m-d H:i:s');

      $sqlLog =
        " INSERT INTO GraficaFaturamentoLogs (ID, DiaSemana, DataRodagem, Cliente, Arte, Tipo, Formato, Papel, QtdeCor, Tiragem, Valor, Faturado, Obs, DataLog, Situacao, NumPedido, NumPedCli) 
          VALUES (:ID, :DiaSemana, :DataRodagem, :Cliente, :Arte, :Tipo, :Formato, :Papel, :QtdeCor, :Tiragem, :Valor, :Faturado, :Obs, :DataLog, 'Alterado', :NumPedido, :NumPedCli)
        ";
      $stmt = $this->gdc->prepare($sqlLog);
      $stmt->execute([
        ':ID'          => $ID,
        ':DiaSemana'   => $DiaSemana,
        ':DataRodagem' => $DataRodagem,
        ':Cliente'     => $Cliente,
        ':Arte'        => $Arte,
        ':Tipo'        => $Tipo,
        ':Formato'     => $Formato,
        ':Papel'       => $Papel,
        ':QtdeCor'     => $QtdeCor,
        ':Tiragem'     => $Tiragem,
        ':Valor'       => $Valor,
        ':Faturado'    => $Faturado,
        ':Obs'         => $Obs,
        ':DataLog'     => $DataLog,
        ':NumPedido'   => $NumPedido,
        ':NumPedCli'   => $NumPedCli
      ]);

      $sqlUp =
        "UPDATE GraficaFaturamento 
          SET 
            DiaSemana = :DiaSemana, DataRodagem = :DataRodagem, Cliente = :Cliente, Arte = :Arte, 
            Tipo = :Tipo, Formato = :Formato, Papel = :Papel, QtdeCor = :QtdeCor, Tiragem = :Tiragem, 
            Valor = :Valor, Faturado = :Faturado, NumPedido = :NumPedido, NumPedCli = :NumPedCli, Obs = :Obs
          WHERE ID = :ID
        ";
      $stmt = $this->gdc->prepare($sqlUp);
      $stmt->execute([
        ':ID'          => $ID,
        ':DiaSemana'   => $DiaSemana,
        ':DataRodagem' => $DataRodagem,
        ':Cliente'     => $Cliente,
        ':Arte'        => $Arte,
        ':Tipo'        => $Tipo,
        ':Formato'     => $Formato,
        ':Papel'       => $Papel,
        ':QtdeCor'     => $QtdeCor,
        ':Tiragem'     => $Tiragem,
        ':Valor'       => $Valor,
        ':Faturado'    => $Faturado,
        ':NumPedido'   => $NumPedido,
        ':NumPedCli'   => $NumPedCli,
        ':Obs'         => $Obs
      ]);
    }
  }

  public function incluirFaturamento(array $dados): bool
  {
    $DataRodagem = $dados['DataRodagem'];
    $Cliente = $dados['Cliente'];
    $Arte = $dados['Arte'];
    $Tipo = $dados['Tipo'];
    $Formato = $dados['Formato'];
    $Papel = $dados['Papel'];
    $QtdeCor = $dados['QtdeCor'];
    $Tiragem = $dados['Tiragem'];
    $Valor = floatval($dados['Valor']);
    $Faturado = !empty($dados['Faturado']) ? $dados['Faturado'] : 'Não';
    $Obs = $dados['Obs'];
    $NumPedido = '';
    $NumPedCli = '';

    // Gerando Dia da Semana de acordo com a data
    $Dt = DateTime::createFromFormat('Y-m-d', $DataRodagem);
    $DiaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
    $IndiceDia = (int)$Dt->format('w');
    $DiaSemana = $DiaSemana[$IndiceDia];

    $sqlInsert =
      " INSERT INTO GraficaFaturamento (DiaSemana, DataRodagem, Cliente, Arte, Tipo, Formato, Papel, QtdeCor, Tiragem, Valor, Faturado, Obs, NumPedido, NumPedCli) 
          VALUES (:DiaSemana, :DataRodagem, :Cliente, :Arte, :Tipo, :Formato, :Papel, :QtdeCor, :Tiragem, :Valor, :Faturado, :Obs, :NumPedido, :NumPedCli)
        ";
    $stmt = $this->gdc->prepare($sqlInsert);
    $stmt->execute([
      ':DiaSemana'   => $DiaSemana,
      ':DataRodagem' => $DataRodagem,
      ':Cliente'     => $Cliente,
      ':Arte'        => $Arte,
      ':Tipo'        => $Tipo,
      ':Formato'     => $Formato,
      ':Papel'       => $Papel,
      ':QtdeCor'     => $QtdeCor,
      ':Tiragem'     => $Tiragem,
      ':Valor'       => $Valor,
      ':Faturado'    => $Faturado,
      ':Obs'         => $Obs,
      ':NumPedido'   => $NumPedido,
      ':NumPedCli'   => $NumPedCli
    ]);

    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function duplicarFaturamento(array $dados): bool
  {
    $IDs = $dados['selected_ids'];
    $NovaData = $dados['NovaData'];
    $Faturado = 'Não';

    // Gerando Dia da Semana de acordo com a data
    $Dt = DateTime::createFromFormat('Y-m-d', $NovaData);
    $DiaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
    $IndiceDia = (int)$Dt->format('w');
    $DiaSemana = $DiaSemana[$IndiceDia];

    // Converte a string em um array
    $IDs = explode(',', $IDs);

    foreach ($IDs as $ID) {
      $sql =
        "SELECT DiaSemana = :DiaSemana, DataRodagem = :NovaData, Cliente, Arte, Tipo, Formato, Papel, QtdeCor, Tiragem, Valor, Faturado = :Faturado, Obs, NumPedido, NumPedCli
          FROM GraficaFaturamento
          WHERE ID = :ID
        ";
      $stmt = $this->gdc->prepare($sql);
      $stmt->execute([
        ':ID'          => $ID,
        ':DiaSemana'   => $DiaSemana,
        ':NovaData'    => $NovaData,
        ':Faturado'    => $Faturado
      ]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$row) {
        continue;
      }

      $Fields = implode(', ', array_keys($row));
      $Values = implode(', ', array_fill(0, COUNT($row), '?'));
      $sqlInsert =
        " INSERT INTO GraficaFaturamento ($Fields)
          VALUES ($Values)
        ";
      $stmt = $this->gdc->prepare($sqlInsert);
      $stmt->execute(array_values($row));
    }

    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function alteraDataFaturamento(array $dados): bool
  {
    $IDs = $dados['selected_ids'];
    $NovaData = $dados['NovaData'];

    // Gerando Dia da Semana de acordo com a data
    $Dt = DateTime::createFromFormat('Y-m-d', $NovaData);
    $DiaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
    $IndiceDia = (int)$Dt->format('w');
    $DiaSemana = $DiaSemana[$IndiceDia];

    // Converte a string em um array
    $IDs = explode(',', $IDs);

    $IDs = implode(',', array_map(function ($id) {
      return "'" . trim(($id)) . "'";
    }, $IDs));

    if ($NovaData !== '') {
      $sql =
        "UPDATE GraficaFaturamento
          SET DiaSemana = :DiaSemana, DataRodagem = :NovaData
          WHERE ID IN ($IDs)
        ";
      $stmt = $this->gdc->prepare($sql);
      $stmt->execute([
        ':DiaSemana' => $DiaSemana,
        ':NovaData'  => $NovaData
      ]);
    }

    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function alteraFaturamento(array $dados): array
  {
    // Converte a string em um array
    $IDs = explode(',', $dados['selected_ids']);

    // Remove espaços e cria array associativo de parâmetros
    $params = [];
    $placeholders = [];

    foreach ($IDs as $index => $id) {
      $paramKey = ":id" . $index;
      $placeholders[] = $paramKey;
      $params[$paramKey] = trim($id);
    }

    if ($IDs !== '') {
      $sql =
        "UPDATE GraficaFaturamento
          SET Faturado = 'Sim'
          WHERE ID IN (" . implode(',', $placeholders) . ")
        ";

      $stmt = $this->gdc->prepare($sql);
      $stmt->execute($params);
    }

    $sqlcon =
      "SELECT ID, DiaSemana, DataRodagem, Cliente, Arte, Tipo, Formato, 
        Papel, QtdeCor, Tiragem, Valor, Faturado, Obs, NumPedido, NumPedCli
      FROM GraficaFaturamento 
      WHERE ID IN (" . implode(',', $placeholders) . ")
    ";
    $sqlcon .= "\n ORDER BY Tipo, Papel, Formato, Cliente";

    $stmtcon = $this->gdc->prepare($sqlcon);
    $stmtcon->execute($params);

    return $stmtcon->fetchAll(PDO::FETCH_ASSOC);
  }

    public function excluirFaturamento(array $dados)
  {
    $ID = $dados['ID'];
    $Cliente = $dados['Cliente'];
    $Arte = $dados['Arte'];
    $Tipo = $dados['Tipo'];
    $Formato = $dados['Formato'];
    $Papel = $dados['Papel'];
    $QtdeCor = $dados['QtdeCor'];
    $Tiragem = $dados['Tiragem'];
    $Valor = floatval($dados['Valor']);
    $Faturado = $dados['Faturado'];
    $Obs = $dados['Obs'];
    $NumPedido = $dados['NumPedido'];
    $NumPedCli = $dados['NumPedCli'];
    
    if ($dados['DataRodagem'] !== '') {
      $dt = DateTime::createFromFormat('d/m/Y', $dados['DataRodagem']);
      $DataRodagem = $dt->format('Y-m-d');
    } else {
      $DataRodagem = '';
    }

    // Gerando Dia da Semana de acordo com a data
    $Dt = DateTime::createFromFormat('Y-m-d', $DataRodagem);
    $DiaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
    $IndiceDia = (int)$Dt->format('w');
    $DiaSemana = $DiaSemana[$IndiceDia];

    if ($ID !== '') {
      $DataLog = date('Y-m-d H:i:s');

      $sqlLog =
        " INSERT INTO GraficaFaturamentoLogs (ID, DiaSemana, DataRodagem, Cliente, Arte, Tipo, Formato, Papel, QtdeCor, Tiragem, Valor, Faturado, Obs, DataLog, Situacao, NumPedido, NumPedCli) 
          VALUES (:ID, :DiaSemana, :DataRodagem, :Cliente, :Arte, :Tipo, :Formato, :Papel, :QtdeCor, :Tiragem, :Valor, :Faturado, :Obs, :DataLog, 'Excluído', :NumPedido, :NumPedCli)
        ";
      $stmt = $this->gdc->prepare($sqlLog);
      $stmt->execute([
        ':ID'          => $ID,
        ':DiaSemana'   => $DiaSemana,
        ':DataRodagem' => $DataRodagem,
        ':Cliente'     => $Cliente,
        ':Arte'        => $Arte,
        ':Tipo'        => $Tipo,
        ':Formato'     => $Formato,
        ':Papel'       => $Papel,
        ':QtdeCor'     => $QtdeCor,
        ':Tiragem'     => $Tiragem,
        ':Valor'       => $Valor,
        ':Faturado'    => $Faturado,
        ':Obs'         => $Obs,
        ':DataLog'     => $DataLog,
        ':NumPedido'   => $NumPedido,
        ':NumPedCli'   => $NumPedCli
      ]);

      $sqlDel = " DELETE FROM GraficaFaturamento WHERE ID = :ID ";

      $stmt = $this->gdc->prepare($sqlDel);
      $stmt->execute([
        ':ID' => $ID
      ]);
    }
  }
}
