<?php
require_once __DIR__ . '/../DBConnect.php';

class CirVendasEquipe
{
  private $DrGestor;

  public function __construct()
  {
    $this->DrGestor = DatabaseConnection::getConnection('DrGestor');
  }

  /**
   * @param array  $meses       Ex: ['05/2025','06/2025','07/2025','08/2025']
   * @param int    $codProduto  0,1,3,11,13
   * @param string $agrupaEm    'NomeVendedor' ou 'GrupoEquipe'
   */
  public function consultaVendas(array $meses, int $codProduto, array $agrupamentos): array
  {
    // 1) validações mínimas
    if (count($meses) < 1 || count($meses) > 4) {
      throw new InvalidArgumentException("De 1 a 4 meses são permitidos");
    }
    // 2) Mapeia codProduto para a cláusula WHERE:
    $filtroProduto = [
      0  => ' Sr.codigoDoProdutoServico IN (1,3,11)',
      1  => ' Sr.codigoDoProdutoServico = 1',
      3  => ' Sr.codigoDoProdutoServico = 3',
      11 => ' Sr.codigoDoProdutoServico = 11',
      13 => ' Sr.codigoDoProdutoServico IN (1,3)',
    ];
    // pega a cláusula certa (ou o padrão [0] caso o índice não exista)
    $clause = $filtroProduto[$codProduto] ?? $filtroProduto[0];

    // 3) Monta dinamicamente as colunas por mês:
    //    Cada mês gera: TotalComboX, TotalDigitalX, TotalImpressaX, TotalGeralX
    $tipos = [
      'Combo'    => 0.5,
      'Digital'  => 1,
      'Impressa' => 1,
    ];

    // 4) monta as colunas de agregação por mês
    $selectCols = [];
    foreach ($meses as $i => $mes) {
      $idx = $i + 1;
      $ph  = ":mes{$idx}";

      // Combo/Digital/Impressa para o mês
      foreach ($tipos as $tipo => $peso) {
        $alias = "Total{$tipo}{$idx}";
        $selectCols[] = "CAST(
          SUM(
            CASE 
              WHEN NatContrato = " . $this->DrGestor->quote($tipo) . "
              AND (SUBSTRING(MesCad,5,2)+'/'+SUBSTRING(MesCad,1,4)) = {$ph}
              THEN {$peso} ELSE 0
            END
          ) AS INT
        ) AS {$alias}";
      }
      // TotalGeral para o mês
      $cases = [];
      foreach ($tipos as $tipo => $peso) {
        $cases[] = "WHEN NatContrato = " . $this->DrGestor->quote($tipo) . " THEN {$peso}";
      }
      $selectCols[] = "CAST(
        SUM(
          CASE
            {$cases[0]}
            {$cases[1]}
            {$cases[2]}
            ELSE 0
          END
        ) AS INT
      ) AS TotalGeral{$idx}";
    }

    // 5) TotalGeral acumulado
    $inList = implode(',', array_map(fn($i) => ":mes" . ($i + 1), array_keys($meses)));
    $casesAll = [];
    foreach ($tipos as $tipo => $peso) {
      $casesAll[] = "WHEN NatContrato = " . $this->DrGestor->quote($tipo) . " THEN {$peso}";
    }
    $selectCols[] = "CAST(
        SUM(
          CASE
            {$casesAll[0]} AND (SUBSTRING(MesCad,5,2)+'/'+SUBSTRING(MesCad,1,4)) IN ({$inList})
            {$casesAll[1]} AND (SUBSTRING(MesCad,5,2)+'/'+SUBSTRING(MesCad,1,4)) IN ({$inList})
            {$casesAll[2]} AND (SUBSTRING(MesCad,5,2)+'/'+SUBSTRING(MesCad,1,4)) IN ({$inList})
            ELSE 0
          END
        ) AS INT
      ) AS TotalGeral";


    $selectCols = [];
    foreach ($meses as $i => $mes) {
      $idx = $i + 1;
      $ph  = ":mes{$idx}";

      foreach ($tipos as $tipo => $peso) {
        $alias = "Total{$tipo}{$idx}";
        $selectCols[] = "CAST(
          SUM(
            CASE
              WHEN NatContrato = {$this->DrGestor->quote($tipo)}
               AND (SUBSTRING(MesCad,5,2)+'/'+SUBSTRING(MesCad,1,4)) = {$ph}
              THEN {$peso}
              ELSE 0
            END
          ) AS INT
        ) AS {$alias}";
      }

      // 6) monta dinamicamente SELECT e GROUP BY
      $camposGrupo  = $agrupamentos;               // ex: ['NomeVendedor','GrupoEquipe']
      $selGrupo     = implode(', ', $camposGrupo);
      $groupBy      = implode(', ', $camposGrupo);

      $sql = "
        SELECT
          {$selGrupo},
          " . implode(",\n        ", $selectCols) . "
        FROM (
          SELECT
            DrCon.MesCad,
            Eq.NomeVendedor,
            Eq.GrupoEquipe,
            DrCon.NatContrato
          FROM Dr_CadContratos DrCon
          LEFT JOIN gestor.dbo.vCadPessoaFisicaJuridica Pes
            ON Pes.codigoDaPessoa = DrCon.codigoDaPessoaVendedor
          LEFT JOIN Dr_EquipeVendas Eq
            ON Eq.CodVendedor = DrCon.codigoDaPessoaVendedor
          INNER JOIN gestor.dbo.assContratos   Con
            ON DrCon.NumerodoContrato = Con.numeroDoContrato
          INNER JOIN gestor.dbo.cadTipoDeAssinatura Tp
            ON Tp.codigoTipoDeAssinatura = Con.codigoTipoAssinatura
          INNER JOIN gestor.dbo.cadProdutosServicos Sr
            ON Sr.codigoDoProdutoServico = Tp.codigoDoProdutoServico
          WHERE DrCon.TipoCobranca = 'PAGO'
            AND DrCon.tipoDeContrato = 'I'
            AND (SUBSTRING(DrCon.MesCad,5,2)+'/'+SUBSTRING(DrCon.MesCad,1,4)) IN ({$inList})
            AND {$clause}
        ) AS Sub
        GROUP BY {$groupBy}
        ORDER BY {$groupBy};
      ";
    }
    // 7) Prepara a consulta
    $stmt = $this->DrGestor->prepare($sql);
    // 8) Bind dos meses
    foreach ($meses as $i => $mes) {
      $stmt->bindValue(":mes" . ($i + 1), $mes);
    }
    // 9) Executa a consulta
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
