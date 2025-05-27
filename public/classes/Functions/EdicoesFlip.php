<?php
require_once __DIR__ . '/../DBConnect.php';

class EdicoesFlip
{
  private $flip;

  public function __construct()
  {
    $this->flip = DatabaseConnection::getConnection('flip');
  }

  public function getConnection(): PDO
  {
    return $this->flip;
  }

  function buscaProdutos(): array
  {
    $sql = "SELECT id, name FROM products ORDER BY name";
    $stmt = $this->flip->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function buscarDatasEdicoes(int $year, string $idProd): array
  {
    $sql = "SELECT date(publishedAt), productId FROM editions e WHERE year(e.publishedAt) = :ano AND e.productId = :idProd ORDER BY e.publishedAt";

    // echo "<pre>";
    // var_dump($year, $idProd);
    // var_dump($sql);
    // die();

    $stmt = $this->flip->prepare($sql);
    $stmt->execute(['ano' => $year, 'idProd' => $idProd]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  /**
   * Gera o HTML de um calendário para um mês/ano,
   * recebendo um array flipado das datas marcadas para lookup rápido.
   */
  function gerarCalendario(int $month, int $year, array $markedDates): string
  {
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $daysInMonth     = date('t', $firstDayOfMonth);
    $dayOfWeek       = date('N', $firstDayOfMonth); // 1 (Seg) a 7 (Dom)

    $html  = '<table class="table table-bordered calendar">';
    $html .= '<thead class="table-light"><tr>';
    $dias = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    foreach ($dias as $d) {
      $html .= "<th class=\"text-center\">{$d}</th>";
    }
    $html .= '</tr></thead><tbody><tr>';

    // espaços antes do 1º dia
    $cellCount = 0;
    for ($i = 1; $i < $dayOfWeek; $i++, $cellCount++) {
      $html .= '<td></td>';
    }

    // dias do mês
    for ($d = 1; $d <= $daysInMonth; $d++, $cellCount++) {
      if ($cellCount % 7 === 0 && $cellCount !== 0) {
        $html .= '</tr><tr>';
      }
      $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
      $isMarked = isset($markedDates[$dateStr]);
      $class = $isMarked ? 'table-primary fw-bold' : '';
      $html .= "<td class=\"text-center {$class}\">{$d}</td>";
    }

    // espaços finais
    while ($cellCount % 7 !== 0) {
      $html .= '<td></td>';
      $cellCount++;
    }

    $html .= '</tr></tbody></table>';
    return $html;
  }

  /**
   * Monta os 12 cards com os calendários e retorna todo o bloco HTML.
   */
  function gerarMesesDoAno(int $year, array $markedDates): string
  {
    // flip para lookup
    setlocale(LC_TIME, 'pt_BR.UTF-8'); // para nomes em pt-BR
    $marked = array_flip($markedDates);
    $mesesHTML = '';

    for ($m = 1; $m <= 12; $m++) {
      $nomeMes = ucfirst(strftime('%B', mktime(0, 0, 0, $m, 1, $year)));
      $mesesHTML .= '<div class="col">';
      $mesesHTML .= '<div class="card h-100">';
      $mesesHTML .= "  <div class=\"card-header text-center fw-bold\">{$nomeMes}</div>";
      $mesesHTML .= '  <div class="card-body p-2">';
      $mesesHTML .= $this->gerarCalendario($m, $year, $marked);
      $mesesHTML .= '  </div>';
      $mesesHTML .= '</div>';
      $mesesHTML .= '</div>';
    }
    return $mesesHTML;
  }
}
