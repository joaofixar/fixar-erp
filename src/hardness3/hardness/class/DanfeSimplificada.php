<?php
namespace hardness;

require_once __DIR__ . '/../fpdf/fpdf.php';

/**
 * Impressão adicional da NF-e autorizada, com as divisões da NT 2026.003 v1.00.
 * Não altera o XML autorizado nem o processo de emissão.
 * Sem suplemento no XML, a consulta online v3 é montada com a tabela de URLs da UF.
 */
class DanfeSimplificada
{
    const TITULO = 'DANFE Simplificado Tipo 2';
    const LARGURA = 80;
    const MARGEM = 3;
    const TAMANHO_QR = 30;

    private $xpath;
    private $inf;
    private $protocolo;
    private $suplemento;
    private $pdf;
    private $blocos = array();
    private $dados;
    private $cancelada;

    public function __construct($xml, $cancelada = false)
    {
        if (!class_exists('DOMDocument')) {
            throw new \RuntimeException('A extensão DOM/XML do PHP é necessária para gerar a impressão simplificada.');
        }
        $doc = new \DOMDocument();
        $anterior = libxml_use_internal_errors(true);
        try {
            if (preg_match('/<!DOCTYPE/i', $xml) || !$doc->loadXML($xml, LIBXML_NONET)) {
                throw new \InvalidArgumentException('XML inválido para impressão simplificada.');
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($anterior);
        }
        $this->xpath = new \DOMXPath($doc);
        $this->inf = $this->xpath->query('/*[local-name()="nfeProc"]/*[local-name()="NFe"]/*[local-name()="infNFe"]')->item(0);
        $this->protocolo = $this->xpath->query('/*[local-name()="nfeProc"]/*[local-name()="protNFe"]/*[local-name()="infProt"]')->item(0);
        if (!$this->inf || $this->valor('ide/mod') !== '55' || $this->inf->getAttribute('versao') !== '4.00') {
            throw new \InvalidArgumentException('A impressão simplificada exige XML autorizado de NF-e modelo 55, versão 4.00.');
        }
        if (!$this->protocolo || !in_array($this->valor('cStat', $this->protocolo), array('100', '150'), true)
            || $this->valor('nProt', $this->protocolo) === '') {
            throw new \InvalidArgumentException('O XML não contém autorização de uso da NF-e.');
        }
        $chave = preg_replace('/^NFe/', '', $this->inf->getAttribute('Id'));
        if (!preg_match('/^[0-9A-Z]{44}$/D', $chave) || $chave !== $this->valor('chNFe', $this->protocolo)) {
            throw new \InvalidArgumentException('A chave da NF-e não corresponde ao protocolo de autorização.');
        }
        $this->suplemento = $this->xpath->query('/*[local-name()="nfeProc"]/*[local-name()="NFe"]/*[local-name()="infNFeSupl"]')->item(0);
        $qr = $this->suplemento ? $this->valor('qrCode', $this->suplemento) : '';
        if ($qr !== '' && !preg_match('~^https?://[^\s]+$~iD', $qr)) {
            throw new \InvalidArgumentException('O XML contém um endereço de QR Code inválido.');
        }
        if (!$this->nos('det')->length) {
            throw new \InvalidArgumentException('O XML da NF-e não contém produtos.');
        }
        $ambiente = $this->valor('ide/tpAmb');
        if (!in_array($ambiente, array('1', '2'), true) || $ambiente !== $this->valor('tpAmb', $this->protocolo)) {
            throw new \InvalidArgumentException('O ambiente da NF-e não corresponde ao protocolo de autorização.');
        }
        $urlChave = $this->suplemento ? $this->valor('urlChave', $this->suplemento) : '';
        if ($qr === '' || $urlChave === '') {
            list($urlQr, $urlConsulta) = $this->urlsConsulta($chave, $ambiente);
            if ($qr === '') {
                // Offline exige uma assinatura própria: nunca inventar um QR online para esse XML.
                if ($this->valor('ide/tpEmis') === '9') {
                    throw new \InvalidArgumentException('A NF-e em contingência offline precisa conter o QR Code assinado no XML autorizado.');
                }
                $qr = $urlQr . '?p=' . $chave . '|3|' . $ambiente;
            }
            if ($urlChave === '') { $urlChave = $urlConsulta; }
        }
        $this->cancelada = (bool) $cancelada;
        $this->dados = array(
            'chave' => $chave,
            'numero' => $this->valor('ide/nNF'),
            'serie' => $this->valor('ide/serie'),
            'protocolo' => $this->valor('nProt', $this->protocolo),
            'qrCode' => $qr,
            'urlChave' => $urlChave,
            'ambiente' => $ambiente,
        );
    }

    private function urlsConsulta($chave, $ambiente)
    {
        // MOC, ZX02-20, observação 3: modelo 55 utiliza a mesma URL de QR do modelo 65.
        $ufs = array('11'=>'RO','12'=>'AC','13'=>'AM','14'=>'RR','15'=>'PA','16'=>'AP','17'=>'TO',
            '21'=>'MA','22'=>'PI','23'=>'CE','24'=>'RN','25'=>'PB','26'=>'PE','27'=>'AL','28'=>'SE','29'=>'BA',
            '31'=>'MG','32'=>'ES','33'=>'RJ','35'=>'SP','41'=>'PR','42'=>'SC','43'=>'RS','50'=>'MS','51'=>'MT','52'=>'GO','53'=>'DF');
        $uf = $ufs[substr($chave, 0, 2)] ?? '';
        if ($uf === '' || $uf !== $this->valor('emit/enderEmit/UF') || substr($chave, 0, 2) !== $this->valor('ide/cUF')) {
            throw new \InvalidArgumentException('A UF do emitente não corresponde à chave da NF-e.');
        }
        $pasta = __DIR__ . '/../../bibliotecas/composer/vendor/nfephp-org/sped-nfe/storage/';
        $servicos = new \DOMDocument();
        if (!$servicos->load($pasta . 'wsnfe_4.00_mod65.xml', LIBXML_NONET)) {
            throw new \RuntimeException('Não foi possível carregar os endereços de consulta da SEFAZ.');
        }
        $xpath = new \DOMXPath($servicos);
        $grupo = $ambiente === '1' ? 'producao' : 'homologacao';
        $no = $xpath->query('//UF[sigla="' . $uf . '"]/' . $grupo . '/NfeConsultaQR')->item(0);
        $consultas = json_decode(file_get_contents($pasta . 'uri_consulta_nfce.json'), true);
        $urlQr = $no ? trim($no->textContent) : '';
        $urlConsulta = $consultas[$ambiente][$uf] ?? '';
        if ($urlConsulta !== '' && !preg_match('~^https?://~i', $urlConsulta)) { $urlConsulta = 'http://' . $urlConsulta; }
        if (!preg_match('~^https?://[^\s?]+$~iD', $urlQr) || $urlConsulta === '') {
            throw new \RuntimeException('Endereço de consulta da SEFAZ indisponível para ' . $uf . '.');
        }
        return array($urlQr, $urlConsulta);
    }

    public function dados() { return $this->dados; }

    private function nos($caminho, $contexto = null)
    {
        $partes = array_map(function ($parte) { return '*[local-name()="' . $parte . '"]'; }, explode('/', $caminho));
        return $this->xpath->query('./' . implode('/', $partes), $contexto ?: $this->inf);
    }

    private function valor($caminho, $contexto = null)
    {
        $no = $this->nos($caminho, $contexto)->item(0);
        return $no ? trim($no->textContent) : '';
    }

    /** Preserva casas decimais e números grandes sem converter os valores para float. */
    private function numero($valor, $minimo = 2)
    {
        if (!preg_match('/^(-?)(\d+)(?:\.(\d+))?$/D', $valor, $m)) { return $valor; }
        $inteiro = ltrim($m[2], '0');
        $inteiro = $inteiro === '' ? '0' : $inteiro;
        $decimal = str_pad(rtrim($m[3] ?? '', '0'), $minimo, '0');
        return $m[1] . preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $inteiro) . ($decimal !== '' ? ',' . $decimal : '');
    }

    private function documento($valor)
    {
        if (strlen($valor) === 14) {
            return substr($valor, 0, 2) . '.' . substr($valor, 2, 3) . '.' . substr($valor, 5, 3) . '/' . substr($valor, 8, 4) . '-' . substr($valor, 12);
        }
        if (strlen($valor) === 11) {
            return substr($valor, 0, 3) . '.' . substr($valor, 3, 3) . '.' . substr($valor, 6, 3) . '-' . substr($valor, 9);
        }
        return $valor;
    }

    private function endereco($grupo)
    {
        $partes = array();
        foreach (array('xLgr', 'nro', 'xCpl', 'xBairro', 'xMun', 'UF', 'CEP') as $tag) {
            $valor = $this->valor($grupo . '/' . $tag);
            if ($valor !== '') { $partes[] = $valor; }
        }
        return implode(', ', $partes);
    }

    private function data($valor)
    {
        if ($valor === '') { return ''; }
        try {
            $emissao = new \DateTimeImmutable($this->valor('ide/dhEmi'));
            return (new \DateTimeImmutable($valor))->setTimezone($emissao->getTimezone())->format('d/m/Y H:i:s');
        } catch (\Exception $e) { return $valor; }
    }

    private function codificar($texto)
    {
        return iconv('UTF-8', 'Windows-1252//TRANSLIT', (string) $texto);
    }

    private function linhas($texto, $largura, $fonte, $negrito)
    {
        $this->pdf->SetFont('Arial', $negrito ? 'B' : '', $fonte);
        $linhas = array();
        foreach (explode("\n", str_replace("\r", '', $this->codificar($texto))) as $paragrafo) {
            $linha = '';
            foreach (explode(' ', $paragrafo) as $palavra) {
                if ($linha !== '' && $this->pdf->GetStringWidth($linha . ' ' . $palavra) > $largura) {
                    $linhas[] = $linha; $linha = '';
                }
                while (strlen($palavra) > 1 && $this->pdf->GetStringWidth($palavra) > $largura) {
                    $n = 1;
                    while ($n < strlen($palavra) && $this->pdf->GetStringWidth(substr($palavra, 0, $n + 1)) <= $largura) { $n++; }
                    $linhas[] = substr($palavra, 0, $n); $palavra = substr($palavra, $n);
                }
                $linha .= ($linha !== '' ? ' ' : '') . $palavra;
            }
            $linhas[] = $linha;
        }
        return $linhas;
    }

    private function texto($texto, $fonte = 8, $negrito = false, $alinhamento = 'L')
    {
        if ($texto === '') { return; }
        foreach ($this->linhas($texto, self::LARGURA - 2 * self::MARGEM - 0.6, $fonte, $negrito) as $linha) {
            $this->blocos[] = array('tipo' => 'texto', 'linhas' => array($linha), 'fonte' => $fonte, 'negrito' => $negrito,
                'alinhamento' => $alinhamento, 'altura' => $fonte * 0.352778 + 0.9);
        }
    }

    private function separador()
    {
        $this->blocos[] = array('tipo' => 'separador', 'altura' => 2.5);
    }

    private function colunas(array $textos, array $larguras, $negrito = false, $fonte = 8, array $alinhamentos = array())
    {
        $linhas = array(); $quantidade = 1;
        foreach ($textos as $i => $texto) {
            $linhas[$i] = $this->linhas($texto, $larguras[$i] - 0.6, $fonte, $negrito);
            $quantidade = max($quantidade, count($linhas[$i]));
        }
        $this->blocos[] = array('tipo' => 'colunas', 'linhas' => $linhas, 'larguras' => $larguras,
            'alinhamentos' => $alinhamentos,
            'fonte' => $fonte, 'negrito' => $negrito, 'alturaLinha' => $fonte * 0.352778 + 0.9,
            'altura' => $quantidade * ($fonte * 0.352778 + 0.9));
    }

    /** Mantém consulta, identificação e QR juntos na mesma folha. */
    private function agrupar($inicio)
    {
        $blocos = array_splice($this->blocos, $inicio);
        $altura = array_sum(array_column($blocos, 'altura'));
        if ($altura <= 276) { $this->blocos[] = array('tipo' => 'grupo', 'blocos' => $blocos, 'altura' => $altura); }
        else { $this->blocos = array_merge($this->blocos, $blocos); }
    }

    /** Os valores monetários do XML têm duas casas; soma exata em centavos. */
    private function acrescimos()
    {
        $centavos = 0;
        foreach (array('vFrete', 'vSeg', 'vOutro') as $tag) {
            $valor = $this->valor('total/ICMSTot/' . $tag);
            if (preg_match('/^(\d+)\.(\d{2})$/D', $valor, $m)) { $centavos += (int) $m[1] * 100 + (int) $m[2]; }
        }
        return intdiv($centavos, 100) . '.' . str_pad($centavos % 100, 2, '0', STR_PAD_LEFT);
    }

    private function total($titulo, $valor, $negrito = false)
    {
        if ($valor !== '') { $this->colunas(array($titulo, $this->numero($valor)), array(48, 26), $negrito); }
    }

    private function formaPagamento($codigo, $descricao)
    {
        if ($descricao !== '') { return $descricao; }
        $nomes = array('01' => 'Dinheiro', '02' => 'Cheque', '03' => 'Cartão de crédito', '04' => 'Cartão de débito',
            '05' => 'Crédito em loja', '10' => 'Vale alimentação', '11' => 'Vale refeição', '12' => 'Vale presente',
            '13' => 'Vale combustível', '14' => 'Duplicata mercantil', '15' => 'Boleto bancário',
            '16' => 'Depósito bancário', '17' => 'PIX', '18' => 'Transferência / carteira digital',
            '19' => 'Fidelidade / cashback', '20' => 'PIX estático', '90' => 'Sem pagamento', '99' => 'Outros');
        return $nomes[$codigo] ?? 'Pagamento (código ' . $codigo . ')';
    }

    private function montarBlocos()
    {
        $this->blocos = array();
        $cnpj = $this->valor('emit/CNPJ');
        $this->texto(($cnpj !== '' ? 'CNPJ: ' : 'CPF: ') . $this->documento($cnpj ?: $this->valor('emit/CPF')), 8, false, 'C');
        $this->texto($this->valor('emit/xNome'), 9, true, 'C');
        $this->texto($this->endereco('emit/enderEmit'), 7.5, false, 'C');
        $this->texto(strtoupper(self::TITULO), 9, true, 'C');
        if ($this->cancelada) { $this->texto('NF-e CANCELADA', 11, true, 'C'); }
        $this->separador();
        $larguras = array(14, 22, 7, 5, 14, 12);
        $alinhamentos = array('L', 'L', 'R', 'C', 'R', 'R');
        $this->colunas(array('Código', 'Descrição', 'Qtde', 'Un', 'Vl. unit.', 'Vl. total'), $larguras, true, 7, $alinhamentos);
        foreach ($this->nos('det') as $item) {
            $this->colunas(array($this->valor('prod/cProd', $item), $this->valor('prod/xProd', $item),
                $this->numero($this->valor('prod/qCom', $item), 0), $this->valor('prod/uCom', $item),
                $this->numero($this->valor('prod/vUnCom', $item)), $this->numero($this->valor('prod/vProd', $item))), $larguras, false, 7, $alinhamentos);
            $this->texto($this->valor('infAdProd', $item), 7);
        }
        $this->separador();
        $this->colunas(array('Qtde. total de itens', (string) $this->nos('det')->length), array(55, 19));
        $vProd = $this->valor('total/ICMSTot/vProd');
        $vNF = $this->valor('total/ICMSTot/vNF');
        $this->total('Valor total R$', $vProd);
        $acrescimos = $this->acrescimos();
        $desconto = $this->valor('total/ICMSTot/vDesc');
        if (preg_match('/[1-9]/', $acrescimos)) { $this->total('Acréscimos R$', $acrescimos); }
        if (preg_match('/[1-9]/', $desconto)) { $this->total('Desconto R$', $desconto); }
        if (preg_match('/[1-9]/', $acrescimos . $desconto) || $this->numero($vProd) !== $this->numero($vNF)) {
            $this->total('Valor a pagar R$', $vNF, true);
        }
        $this->separador();
        $this->colunas(array('FORMA DE PAGAMENTO', 'VALOR PAGO R$'), array(48, 26), true);
        foreach ($this->nos('pag/detPag') as $pagamento) {
            $nome = $this->formaPagamento($this->valor('tPag', $pagamento), $this->valor('xPag', $pagamento));
            $this->colunas(array($nome, $this->numero($this->valor('vPag', $pagamento))), array(48, 26));
        }
        $this->total('Troco R$', $this->valor('pag/vTroco') ?: '0.00');
        $tributos = array('total/IBSCBSTot/gCBS/vCBS' => '(+) CBS R$', 'total/IBSCBSTot/gIBS/vIBS' => '(+) IBS R$', 'total/ISTot/vIS' => '(+) IS R$');
        if ($this->nos('total/IBSCBSTot')->length || $this->nos('total/ISTot')->length) { $this->separador(); }
        foreach ($tributos as $caminho => $titulo) {
            $valor = $this->valor($caminho);
            if ($caminho !== 'total/ISTot/vIS' || preg_match('/[1-9]/', $valor)) { $this->total($titulo, $valor); }
        }
        $inicioConsulta = count($this->blocos);
        $this->separador();
        $this->texto('Consulte pela Chave de Acesso em', 7, true, 'C');
        $this->texto($this->dados['urlChave'], 7, false, 'C');
        $this->texto(implode(' ', str_split($this->dados['chave'], 4)), 7, false, 'C');
        $this->separador();
        $cnpj = $this->valor('dest/CNPJ'); $cpf = $this->valor('dest/CPF');
        $documento = $cnpj !== '' ? 'CNPJ: ' . $this->documento($cnpj) : ($cpf !== '' ? 'CPF: ' . $this->documento($cpf) : $this->valor('dest/idEstrangeiro'));
        if ($documento !== '') { $this->texto('CONSUMIDOR ' . $documento, 8, true); }
        $this->texto($this->valor('dest/xNome'), 8);
        $this->texto($this->endereco('dest/enderDest'), 7.5);
        if ($this->nos('entrega')->length) {
            $this->texto('LOCAL DE ENTREGA', 8, true);
            $this->texto($this->endereco('entrega'), 7.5);
        }
        $this->separador();
        $this->texto('NF-e nº ' . $this->dados['numero'] . ' - Série ' . $this->dados['serie'], 9, true, 'C');
        $this->texto('Emissão: ' . $this->data($this->valor('ide/dhEmi')), 8, false, 'C');
        $this->texto('Protocolo de autorização: ' . $this->dados['protocolo'], 8, false, 'C');
        $this->texto('Data de autorização: ' . $this->data($this->valor('dhRecbto', $this->protocolo)), 8, false, 'C');
        $this->blocos[] = array('tipo' => 'qr', 'altura' => self::TAMANHO_QR + 3);
        $this->agrupar($inicioConsulta);
        $this->separador();
        $this->texto($this->valor('infAdic/infAdFisco'), 7);
        if ($this->dados['ambiente'] === '2') {
            $this->texto('EMITIDA EM AMBIENTE DE HOMOLOGAÇÃO - SEM VALOR FISCAL', 8, true, 'C');
        }
        $this->texto($this->valor('xMsg', $this->protocolo), 7);
        $this->separador();
        $this->texto($this->valor('infAdic/infCpl'), 7);
        $tributos = $this->valor('total/ICMSTot/vTotTrib');
        if ($tributos !== '') { $this->texto('Tributos totais incidentes (Lei 12.741/2012): R$ ' . $this->numero($tributos), 7); }
    }

    private function desenharQr($y)
    {
        require_once __DIR__ . '/../../bibliotecas/tcpdf/qrcode.php';
        $qr = new \QRcode($this->dados['qrCode'], 'M');
        $matriz = $qr->getBarcodeArray();
        if (empty($matriz['num_rows'])) { throw new \RuntimeException('Não foi possível gerar o QR Code informado no XML.'); }
        $tam = self::TAMANHO_QR; $x = (self::LARGURA - $tam) / 2; $y += 1.5;
        // Ao menos 10% de margem total e quatro módulos livres em cada lado.
        $margem = max($tam * 0.05, $tam * 4 / ($matriz['num_rows'] + 8));
        $modulo = ($tam - 2 * $margem) / $matriz['num_rows'];
        $this->pdf->SetFillColor(255); $this->pdf->Rect($x, $y, $tam, $tam, 'F');
        $this->pdf->SetFillColor(0);
        foreach ($matriz['bcode'] as $r => $linha) {
            foreach ($linha as $c => $bit) {
                if ($bit) { $this->pdf->Rect($x + $margem + $c * $modulo, $y + $margem + $r * $modulo, $modulo, $modulo, 'F'); }
            }
        }
        $this->pdf->Link($x, $y, $tam, $tam, $this->dados['qrCode']);
    }

    private function desenharBloco($bloco, $y)
    {
        if ($bloco['tipo'] === 'grupo') {
            foreach ($bloco['blocos'] as $parte) {
                $this->desenharBloco($parte, $y);
                $y += $parte['altura'];
            }
        } elseif ($bloco['tipo'] === 'qr') { $this->desenharQr($y); }
        elseif ($bloco['tipo'] === 'separador') {
            $this->pdf->SetDrawColor(130); $this->pdf->Line(3, $y + 1.2, 77, $y + 1.2);
        } else {
            $this->pdf->SetFont('Arial', $bloco['negrito'] ? 'B' : '', $bloco['fonte']);
            if ($bloco['tipo'] === 'texto') {
                $this->pdf->SetXY(3, $y);
                $this->pdf->Cell(74, $bloco['altura'], $bloco['linhas'][0], 0, 0, $bloco['alinhamento']);
            } else {
                $x = 3;
                foreach ($bloco['linhas'] as $c => $linhas) {
                    foreach ($linhas as $l => $linha) {
                        $this->pdf->SetXY($x, $y + $l * $bloco['alturaLinha']);
                        $alinhamento = $bloco['alinhamentos'][$c] ?? ($c === 0 ? 'L' : 'R');
                        $this->pdf->Cell($bloco['larguras'][$c], $bloco['alturaLinha'], $linha, 0, 0, $alinhamento);
                    }
                    $x += $bloco['larguras'][$c];
                }
            }
        }
    }

    public function gerarPDF()
    {
        $this->pdf = new \FPDF('P', 'mm', array(self::LARGURA, 297));
        $this->pdf->cMargin = 0.3;
        $this->pdf->SetMargins(self::MARGEM, self::MARGEM, self::MARGEM);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->SetTitle($this->codificar(self::TITULO . ' - ' . $this->dados['numero']));
        $this->pdf->SetLineWidth(0.15);
        $this->montarBlocos();
        $paginas = array(); $pagina = array(); $altura = 0;
        foreach ($this->blocos as $bloco) {
            if ($pagina && $altura + $bloco['altura'] > 286) {
                $paginas[] = array($pagina, $altura); $pagina = array(); $altura = 10;
            }
            $pagina[] = $bloco; $altura += $bloco['altura'];
        }
        if ($pagina) { $paginas[] = array($pagina, $altura); }
        foreach ($paginas as $i => $dadosPagina) {
            list($blocos, $altura) = $dadosPagina;
            $alturaPapel = max(80, $altura + 11);
            $this->pdf->AddPage('P', array(self::LARGURA, $alturaPapel));
            $y = self::MARGEM;
            if ($i > 0) {
                $this->pdf->SetFont('Arial', 'B', 8);
                $this->pdf->SetXY(self::MARGEM, $y);
                $titulo = 'NF-e ' . $this->dados['numero'] . ' / ' . $this->dados['serie'] . ' - CONTINUAÇÃO';
                $this->pdf->Cell(74, 5, $this->codificar($titulo), 0, 0, 'C');
                if ($this->cancelada) {
                    $this->pdf->SetXY(self::MARGEM, $y + 5);
                    $this->pdf->Cell(74, 4, 'NF-e CANCELADA', 0, 0, 'C');
                }
                $y += 10;
            }
            foreach ($blocos as $bloco) {
                $this->desenharBloco($bloco, $y);
                $y += $bloco['altura'];
            }
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->SetXY(3, $alturaPapel - 6);
            $this->pdf->Cell(74, 3, 'Folha ' . ($i + 1) . '/' . count($paginas), 0, 0, 'C');
        }
        return $this->pdf->Output('', 'S');
    }
}
