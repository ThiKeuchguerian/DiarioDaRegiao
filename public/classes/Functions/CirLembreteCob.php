<?php
require_once __DIR__ . '/../DBConnect.php';
require_once __DIR__ . '/../config/composer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

class CirLembreteCobranca
{
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  public function consultaTipoCob(): array
  {
    $sql =
      "SELECT codigoTipoCobranca, descricaoTipoCobranca, descricaoReduzida FROM cadTiposDeDadoParaCobranca 
        WHERE codigoTipoCobranca NOT IN (0,4) ORDER BY descricaoTipoCobranca, descricaoReduzida
      ";

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaTitulosAbertos(array $dados): array
  {
    $dtInicio = $dados['dtInicio'];
    $dtFim    = $dados['dtFim'];
    $tipoCob  = $dados['tipoCob'];

    $sql =
      "SELECT fin.numeroDoContrato, fin.numeroDaParcela, Con.quantidadeDeParcelasDoContrato AS numeroDeParcelas, fin.numeroBancario, TRIM(pessoa.email) AS email,
          fin.numeroLoteRemessa, Con.codigoDaPessoa, nomeRazaoSocial, fin.dataDoVencimento, boleto.emailEnvioBoleto,
          fin.valorDaParcela, pessoa.identMF AS CpfCnpj, fin.portador AS codigoDoPortador,
          CASE 
            WHEN fin.valorDaParcela = fin.saldoValorParcela THEN '0.00'
            WHEN fin.saldoValorParcela = 0 THEN '0.00'
            WHEN fin.saldoValorParcela > 0 AND (fin.valorDaParcela <> fin.saldoValorParcela) THEN fin.valorDaParcela - fin.saldoValorParcela
          END AS descontos, TCob.descricaoReduzida AS descricaoTipoCobranca,
          CONCAT(TRIM(ender.siglaTipoLogradouro), '. ', TRIM(ender.nomeDoLogradouro), ', ', TRIM(ender.numeroDoEndereco), ' - ', 
          TRIM(ender.complementoDoEndereco), ' - ', TRIM(ender.nomeDoBairro)) AS endereco,
          ender.cep, ender.nomeDoMunicipio, ender.siglaDaUF,
          barra.nossoNumero, barra.numeroBancario, barra.codigoDoPortador, barra.codigoDeBarras, barra.linhaDigitavel, barra.codigoDeBarrasAscii, barra.codigoDaRemessa,
          CASE WHEN len(barra.codigoDeBarras) = 44 THEN 'OK' ELSE 'X' END AS Status,
          CASE WHEN Pl.descricaoDoPlanoDePagamento LIKE '%ANU%' THEN 'Anual'
              WHEN Pl.descricaoDoPlanoDePagamento LIKE '%TRI%' THEN 'Trimestral'
              WHEN Pl.descricaoDoPlanoDePagamento LIKE '%SEM%' THEN 'Semestral'
              WHEN Pl.descricaoDoPlanoDePagamento LIKE '%MEN%' THEN 'Mensal' END AS Plano, 
          CASE WHEN Con.tipoDeContrato = 'I' THEN 'Inclusão' WHEN Con.tipoDeContrato = 'R' THEN 'Renovação' END AS Tipo
        FROM assFinanceiroDoContrato fin WITH(NOLOCK)
          INNER JOIN assContratos Con WITH(NOLOCK) ON Con.numeroDoContrato = fin.numeroDoContrato
          INNER JOIN cadPlanoDePagamento Pl WITH (NOLOCK) ON Pl.codigoDoPlanoDePagamento = Con.codigoDoPlanoDePagamento
          INNER JOIN vCadPessoaFisicaJuridica pessoa WITH(NOLOCK) ON pessoa.codigoDaPessoa=Con.codigoDaPessoa
          INNER JOIN assDadosParaCobranca Cob WITH (NOLOCK) ON con.identificadorCobranca = Cob.identificadorCobranca
          INNER JOIN cadTiposDeDadoParaCobranca TCob WITH (NOLOCK) ON Cob.codigoTipoCobranca = TCob.codigoTipoCobranca
        WHERE Con.situacaoDoContrato = 1 AND fin.situacao = 1 AND TCob.codigoTipoCobranca NOT IN (0,4)
      ";

    $where = [];
    $params = [];

    if ($dtInicio == '' && $dtFim == '') {
      $where[] = 'fin.dataDoVencimento = CONVERT(DATE, DATEADD(DAY, 5, GETDATE()))';
    }
    if (!empty($dtInicio) && $dtFim == '') {
      $where[] = 'fin.dataDoVencimento = :dtInicio';
      $params[':dtInicio'] = $dtInicio;
    }
    if (!empty($dtInicio) && !empty($dtFim)) {
      $where[] = 'fin.dataDoVencimento BETWEEN :dtInicio AND :dtFim';
      $params[':dtInicio'] = $dtInicio;
      $params[':dtFim'] = $dtFim;
    }
    if (!empty($tipoCob)) {
      $where[] = 'TCob.codigoTipoCobranca = :tipoCob';
      $params[':tipoCob'] = $tipoCob;
    }

    if ($where > 0) {
      $sql .= "\n AND " . implode("\n AND ", $where);
    }
    $sql  .= "\n ORDER BY Con.codigoDaPessoa, fin.dataDoVencimento, fin.numeroDaParcela ";
    // depurar($sql, $params);
    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function enviaEmail(array $dados)
  {
    $selectDadosArray = json_decode('[' . str_replace('}{', '},{', $dados['selected_ids']) . ']', true);
    $envPdf = $dados['btn-envia'];

    $Assunto = "Lembrete Vencimento Boleto";

    $BoletosPorCliente = [];
    foreach ($selectDadosArray as $boleto) {
      $codCliente = $boleto['codigoDaPessoa']; // deve vir da sua consulta
      if (!isset($BoletosPorCliente[$codCliente])) {
        $BoletosPorCliente[$codCliente] = [];
      }
      $BoletosPorCliente[$codCliente][] = $boleto;
    }

    foreach ($BoletosPorCliente as $codCliente => $boletos) {
      $cliente = $boletos[0]['codigoDaPessoa']; // só tem um por código
      $Email   = $boletos[0]['email'];
      $NomeCli = $boletos[0]['nomeRazaoSocial'];
      $Valor   = $boletos[0]['valorDaParcela'];
      $Plano   = $boletos[0]['Plano'];
      $Parcela = $boletos[0]['numeroDaParcela'] . '/' . $boletos[0]['numeroDeParcelas'];
      $DtVenc  = date('d/m/Y', strtotime($boletos[0]['dataDoVencimento']));
      $diff    = (new DateTime())->diff(new DateTime($boletos[0]['dataDoVencimento']));
      $DiasVencimento = $diff->format('%R%a'); // %R: sinal (+ ou -), %a: total de dias

      // Recupera os dados enviados via POST
      $contrato       = $boletos[0]['numeroDoContrato'] ?? null;
      $dataVencimento = $boletos[0]['dataDoVencimento'] ?? null;
      $valor          = $boletos[0]['valorDaParcela'] ?? null;
      $sequencial     = $boletos[0]['numeroBancario'] ?? null;
      $sacadoNome     = isset($boletos[0]['nomeRazaoSocial']) ? urldecode($boletos[0]['nomeRazaoSocial']) : null;
      $sacadoCpf      = $boletos[0]['CpfCnpj'] ?? null;
      $sacadoEndereco = isset($boletos[0]['Endereco']) ? urldecode($boletos[0]['Endereco']) : null;
      $sacadoCep      = $boletos[0]['Cep'] ?? null;
      $sacadoCidade   = $boletos[0]['Cidade'] ?? null;
      $sacadoUf       = $boletos[0]['UF'] ?? null;
      $descontos      = $boletos[0]['Descontos'] ?? null;
      $numeroDaParcela = $boletos[0]['numeroDaParcela'] ?? null;
      $NumDocumento   = $contrato . '0' . $numeroDaParcela;
      $CodPortador    = $boletos[0]['codigoDoPortador'];

      // Verifica se os dados mínimos foram enviados
      if (!$contrato || !$dataVencimento || !$valor || !$sequencial || !$sacadoNome) {
        die('Dados insuficientes para a geração do boleto.');
      }

      // Dados do cedente (geralmente fixos)
      $cedenteNome     = 'Empresa de Publicidade Rio Preto SA';
      $cedenteCpfCnpj  = '59.963.488/0001-03';
      $cedenteEndereco = 'Av. Joao Batista Vetorasso, 50 - Distrito Industrial';
      $cedenteCep      = '15035-470';
      $cedenteCidade   = 'São José do Rio Preto';
      $cedenteUf       = 'SP';

      // Cria o objeto do sacado com os dados vindos do POST
      $sacado  = new Agente($sacadoNome, $sacadoCpf, $sacadoEndereco, $sacadoCep, $sacadoCidade, $sacadoUf);
      // Cria o objeto do cedente com os dados fixos ou configurados
      $cedente = new Agente($cedenteNome, $cedenteCpfCnpj, $cedenteEndereco, $cedenteCep, $cedenteCidade, $cedenteUf);

      if ($CodPortador === '183351') {
        // Cria o objeto do boleto com os dados obrigatórios e recomendáveis
        $boleto = new Bradesco(
          array(
            // Parâmetros obrigatórios
            'dataVencimento' => new DateTime($dataVencimento),
            'valor' => (float)$valor, // Valor do boleto
            'sequencial' => (int)$sequencial, // Até 11 dígitos
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => 23, // Até 4 dígitos
            'carteira' => 9, // 3, 6 ou 9
            'conta' => 477208, // Até 7 dígitos
            'contaDv' => 3,
            'carteiraDv' => 0,
            'descricaoDemonstrativo' => array( // Até 5
              'Operação referente a Assinatura do Diário da Reigão contrato nº.: ' . $contrato .
                'Plano: ' . $Plano . ' Parcela: ' . $Parcela
            ),
            'instrucoes' => array( // Até 8
              'Após o vencimento mora e 1% de juros ao dia.',
              'Não receber após o vencimento.',
              'Plano: ' . $Plano . ' Parcela: ' . $Parcela,
            ),
            'moeda' => Bradesco::MOEDA_REAL,
            'dataDocumento' => new DateTime(),
            'aceite' => 'Não',
            'especieDoc' => 'DM',
            'numeroDocumento' => $NumDocumento,
            'descontosAbatimentos' => '$descontos',
            // 'quantidade' => $numeroDaParcela,
          )
        );
      } elseif ($CodPortador === '78462') {
        $boleto = new Itau(
          array(
            // Parâmetros obrigatórios
            'dataVencimento' => new DateTime($dataVencimento),
            'valor' => (float)$valor, // Valor do boleto
            'sequencial' => (int)$sequencial, // Até 11 dígitos
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => 45, // Até 4 dígitos
            'carteira' => 109, // 3, 6 ou 9
            'conta' => 8581, // Até 7 dígitos
            'contaDv' => 1,
            'carteiraDv' => 0,
            'descricaoDemonstrativo' => array( // Até 5
              'Operação referente a Assinatura do Diário da Reigão contrato nº.: ' . $contrato,
              'Plano: ' . $Plano . ' Parcela: ' . $Parcela,
            ),
            'instrucoes' => array( // Até 8
              'Após o vencimento mora e 1% de juros ao dia.',
              'Não receber após o vencimento.',
              'Plano: ' . $Plano . ' Parcela: ' . $Parcela,
            ),
            'moeda' => Itau::MOEDA_REAL,
            'aceite' => 'Não',
            // 'especieDoc' => 'DM',
            'numeroDocumento' => $NumDocumento,
            'descontosAbatimentos' => '$descontos',
          )
        );
      }

      // Envio
      $html = $boleto->getOutput();
      $css = '<style>
      @page {
        size: A4 !important;
        margin: 0 2mm !important;
      }
      @media print {
        html, body {
          width: 210mm;
          height: 297mm;
        }
      }
      </style>';
      $nomeArquivo = 'Boleto_' . $contrato . '-' . $numeroDaParcela . '.pdf';
      $html = $css . $html;
      $dompdf = new Dompdf();
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      $caminhoArquivo = __DIR__ . '/../includes/BoletosPdf/' . $nomeArquivo; // você pode alterar o caminho se quiser
      file_put_contents($caminhoArquivo, $dompdf->output());

      // Ajusta Mensagem de acordo com o vencimento
      if ($DiasVencimento > '0') {
        $mensagem = 'Listamos abaixo as parcelas com vencimento para os próximos dias:';
      } elseif ($DiasVencimento < '0') {
        $QtdDias = abs($DiasVencimento);
        $mensagem = 'Listamos abaixo as parcelas vencidas à ' . $QtdDias . ' dias:';
      }

      $TabelaBoletos = '
      <table cellpadding="8" cellspacing="0" style="width: 50%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; border: 1px solid #ddd;">
        <thead>
          <tr style="background-color:rgb(242, 242, 242); text-align: left; color: #333; border-bottom: 2px solid #ccc;">
            <th style="padding: 10px; border: 1px solid #ddd;">Assinatura</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Parcela</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Valor</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Vencimento</th>
          </tr>
        </thead>
        <tbody>
          <tr style="background-color: #fff; border-bottom: 1px solid #eee;">
            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">' . $Plano . '</td>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">' . $Parcela . '</td>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: right; "><span style="float: left;">R$ </span> ' . $Valor . '</td>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: center; ">' . $DtVenc . '</td>
          </tr>
        </tbody>
      </table>';

      $CorpoEmail = '<!doctype html>
        <html>
        <head>
          <meta charset="UTF-8">
          <title>Lembrete de Vencimento</title>
          <style>
            body { font-family: Arial, sans-serif; font-size: 15px; color: #333; }
            .destaque { font-weight: bold; font-size: 16px; }
            .assinatura { margin-top: 20px; font-size: 11px; font-style: italic; }
          </style>
        </head>
        <body>
          <p class="destaque">Prezado(a) <strong>' . htmlspecialchars($NomeCli) . '</strong>,</p>

          <p>Este é um lembrete do <strong>Grupo Diário da Região</strong>.</p><br>

          <p>' . $mensagem . '</p><br>'

        . $TabelaBoletos . '<br>

          <p>Qualquer dúvida ou problema para efetuar o pagamento, entre em contato conosco <strong>respondendo este e-mail ou pelo WhatsApp  <a href="https://wa.me/5517981429276">017-98142-9276</strong></a>
          </p><br>

          <p>Caso o pagamento já tenha sido efetuado, por favor, <strong>desconsidere este aviso</strong>.</p><br>

          <td style="font-family: verdana; font-size: 12px; margin: 0px;">
            Atenciosamente.
          </td></br></br>

          <table>
            <tr>
              <td style="width: 25%;"><img src="cid:logoempresa" alt="Logo"></td>
              <td style="width: 5%; border-right: solid 1px #808080;">&nbsp;</td>
              <td style="width: 5%;">&nbsp;</td>
              <td style="width: 65%;">
                <p style="font-family: verdana; font-size: 12px; margin: 0px; font-weight: bold;" datadetectors="off" x-apple-data-detectors="false">
                <p style="font-family: verdana; font-size: 12px; margin: 0px; font-weight: bold;" datadetectors="off" x-apple-data-detectors="false">Dept. Assinaturas</p>
                <p style="font-family: verdana; font-size: 10px; margin: 0px;"> Grupo Diário da Região</p>
                <p style="font-family: verdana; font-size: 10px; margin: 0px;" datadetectors="off" x-apple-data-detectors="false">Av. Feliciano Sales Cunha, 1515 - Distrito Industrial</p>
                <p style="font-family: verdana; font-size: 10px; margin: 0px;" datadetectors="off" x-apple-data-detectors="false">CEP: 15035-000 - São José do Rio Preto - SP</p>
                <p style="font-family: verdana; font-size: 10px; margin: 0px;" datadetectors="off" x-apple-data-detectors="false">Fone: (17) 2139-2010</p>
                <p style="font-family: verdana; font-size: 10px; margin: 0px;">
                  <a href="http://www.diariodaregiao.com.br/" target="_blank">http://www.diariodaregiao.com.br/</a><br>
                  <a href="http://www.diarioimoveis.com.br/" target="_blank">http://www.diarioimoveis.com.br/</a><br>
                  <a href="http://www.classitudodiariodaregiao.com.br/" target="_blank">http://www.classitudodiariodaregiao.com.br/</a>
                </p>
              </td>
            </tr>
          </table>
        </body>
        </html> ';

      $mail = new PHPMailer(true);
      try {
        // ATIVAR LOG DETALHADO
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'error_log';

        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Host = 'webmail.diariodaregiao.com.br';
        $mail->SMTPAuth = true;
        $mail->addEmbeddedImage('imagens/logo.jpg', 'logoempresa');
        $mail->addCustomHeader("Return-Receipt-To", "cad@diariodaregiao.com.br");
        $mail->addCustomHeader("Disposition-Notification-To", "cad@diariodaregiao.com.br");
        $mail->Username = 'cad@diariodaregiao.com.br';
        $mail->Password = 'cadDiario2016';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Remetente e destinatário
        $mail->setFrom('cad@diariodaregiao.com.br', 'Grupo Diário da Região');
        // $mail->addBCC('ti@diariodaregiao.com.br');
        $mail->addAddress('ti@diariodaregiao.com.br');
        // $mail->addAddress(trim($Email), trim($NomeCli));

        if ($envPdf === '2') {
          // Anexo
          $mail->addAttachment($caminhoArquivo, $nomeArquivo);
        }
        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = $Assunto;
        $mail->Body    = $CorpoEmail;

        $mail->send();
        $msg = "Email enviado com sucesso para $NomeCli <$Email>!";
      } catch (Exception $e) {
        $msg = "Erro ao enviar o email: {$mail->ErrorInfo}";
      }
    }
    unlink($caminhoArquivo);
  }
}
