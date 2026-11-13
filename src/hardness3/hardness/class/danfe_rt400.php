<?php
namespace hardness;

require_once __DIR__ . '/../fpdf/code128.php';

/** DANFE modelo 55 / NF-e 4.00, NT 2026.010 v1.00.
 * Valores fiscais vêm exclusivamente do XML. PDF vetorial A4, sem conversor HTML.
 */
class danfe_rt400
{
    public $xml = '';
    public $logomarca = '';
    public $canhoto = true;
    public $cancelada = false;
    public $visualizacao = '';
    public $casas_decimais_preco = 4;
    public $casas_decimais_quantidade = 4;
    private $orientacao;
    private $pdf;
    private $xpath;
    private $inf;
    private $dados;
    private $y;
    private $largura;
    private $limite;
    private $colunas;
    private $perfil;
    private $margemX;
    private $logoArquivo = '';
    private $logoTemporaria = '';

    public function __construct($orientacao = 'P')
    {
        $this->orientacao = $orientacao === 'L' ? 'L' : 'P';
    }

    public function orientacao() { return $this->orientacao; }

    private function nos($caminho, $contexto = null)
    {
        $partes = explode('/', $caminho);
        $expr = './' . implode('/', array_map(function ($p) {
            return $p === '*' ? '*' : '*[local-name()="' . $p . '"]';
        }, $partes));
        return $this->xpath->query($expr, $contexto ?: $this->inf);
    }

    private function valor($caminho, $contexto = null)
    {
        $no = $this->nos($caminho, $contexto)->item(0);
        return $no ? trim($no->textContent) : '';
    }

    private function numero($valor, $casas = 2)
    {
        return $valor === '' ? '' : number_format((float) $valor, max(0, min(10, (int) $casas)), ',', '.');
    }

    private function monetario($caminho, $contexto = null, $casas = 2)
    {
        return $this->numero($this->valor($caminho, $contexto), $casas);
    }

    /** Campos facultativos só ocupam espaço quando há valor fiscal útil no XML. */
    private function temValorUtil(array $caminhos)
    {
        foreach ($caminhos as $caminho) {
            $valor = trim($this->valor($caminho));
            if ($valor === '') { continue; }
            if (!is_numeric($valor) || (float) $valor != 0.0) { return true; }
        }
        return false;
    }

    private function aliquota($grupo, $tag, $item)
    {
        // A existência de gRed, inclusive pAliqEfet=0, prevalece sobre a alíquota nominal.
        $path = $this->nos($grupo . '/gRed', $item)->length ? $grupo . '/gRed/pAliqEfet' : $grupo . '/' . $tag;
        $valor = $this->valor($path, $item);
        return $valor === '' ? '' : $this->numero($valor, 4) . '%';
    }

    private function data($valor, $hora = false)
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}:\d{2}:\d{2}))?/', $valor, $m)) {
            return $valor;
        }
        return $hora ? ($m[4] ?? '') : "$m[3]/$m[2]/$m[1]";
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

    private function doc($grupo)
    {
        return $this->documento($this->valor($grupo . '/CNPJ') ?: $this->valor($grupo . '/CPF'));
    }

    private function endereco($grupo)
    {
        return implode(', ', array_filter(array($this->valor($grupo . '/xLgr'), $this->valor($grupo . '/nro'), $this->valor($grupo . '/xCpl')), 'strlen'));
    }

    /** Também utilizado pelos testes sem carregar bootstrap ou conexão com banco. */
    public function lerXML()
    {
        if (!class_exists('DOMDocument')) {
            throw new \RuntimeException('A extensão DOM/XML do PHP é necessária para gerar o DANFE RTC.');
        }
        $doc = new \DOMDocument();
        $anterior = libxml_use_internal_errors(true);
        try {
            if (strpos($this->xml, '<!DOCTYPE') !== false || !$doc->loadXML($this->xml, LIBXML_NONET)) {
                throw new \InvalidArgumentException('XML inválido para impressão do DANFE RTC.');
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($anterior);
        }
        $this->xpath = new \DOMXPath($doc);
        $this->inf = $this->xpath->query('/*[local-name()="NFe"]/*[local-name()="infNFe"] | /*[local-name()="nfeProc"]/*[local-name()="NFe"]/*[local-name()="infNFe"]')->item(0);
        if (!$this->inf || $this->valor('ide/mod') !== '55' || $this->inf->getAttribute('versao') !== '4.00') {
            throw new \InvalidArgumentException('O DANFE RTC exige XML de NF-e modelo 55, versão 4.00.');
        }
        $protocolo = $this->xpath->query('/*[local-name()="nfeProc"]/*[local-name()="protNFe"]/*[local-name()="infProt"]')->item(0);
        $supl = $this->xpath->query('//*[local-name()="NFe"]/*[local-name()="infNFeSupl"]')->item(0);
        $itens = array();
        foreach ($this->nos('det') as $det) {
            $icms = $this->nos('imposto/ICMS/*', $det)->item(0);
            $ipi = $this->nos('imposto/IPI/IPITrib', $det)->item(0);
            $rtc = 'imposto/IBSCBS/gIBSCBS';
            $descr = $this->valor('prod/cProd', $det) . ' - ' . $this->valor('prod/xProd', $det);
            $info = $this->valor('infAdProd', $det);
            if ($info !== '') { $descr .= "\n" . $info; }
            $descricaoModelo = $descr;
            $descr .= "\nNCM: " . $this->valor('prod/NCM', $det);
            foreach (array('prod/CEST' => 'CEST', 'imposto/IBSCBS/cClassTrib' => 'cClassTrib', 'imposto/IBSCBS/CST' => 'CST IBS/CBS', 'prod/nFCI' => 'FCI', 'prod/xPed' => 'Pedido', 'prod/nItemPed' => 'Item pedido') as $path => $label) {
                $valor = $this->valor($path, $det);
                if ($valor !== '') {
                    $descr .= "\n" . $label . ': ' . $valor;
                    if (!in_array($label, array('cClassTrib', 'CST IBS/CBS'), true)) { $descricaoModelo .= "\n" . $label . ': ' . $valor; }
                }
            }
            if ($this->valor('prod/uTrib', $det) !== '' && ($this->valor('prod/uTrib', $det) !== $this->valor('prod/uCom', $det) || $this->valor('prod/qTrib', $det) !== $this->valor('prod/qCom', $det))) {
                $tributaria = "Trib.: " . $this->monetario('prod/qTrib', $det, $this->casas_decimais_quantidade) . ' ' . $this->valor('prod/uTrib', $det) . ' x ' . $this->monetario('prod/vUnTrib', $det, $this->casas_decimais_preco);
                $descr .= "\n" . $tributaria;
                $descricaoModelo .= "\n" . $tributaria;
            }
            $cst = $icms ? ($this->valor('CST', $icms) ?: $this->valor('CSOSN', $icms)) : '';
            // CST 00 é uma string válida; a origem não integra a classificação IBS/CBS.
            $cst = $icms ? $this->valor('orig', $icms) . $cst : '';
            $itens[] = array(
                'numero' => $det->getAttribute('nItem'),
                'descricao' => $descr,
                'descricaoModelo' => $descricaoModelo,
                'ncm' => $this->valor('prod/NCM', $det),
                'classificacao' => $this->valor('imposto/IBSCBS/cClassTrib', $det),
                'cst' => $cst,
                'cfop' => $this->valor('prod/CFOP', $det),
                'quantidade' => $this->monetario('prod/qCom', $det, $this->casas_decimais_quantidade),
                'unidade' => $this->valor('prod/uCom', $det),
                'unitario' => $this->monetario('prod/vUnCom', $det, $this->casas_decimais_preco),
                'total' => $this->numero($this->valor('vItem', $det) !== '' ? $this->valor('vItem', $det) : $this->valor('prod/vProd', $det)),
                'bases' => array('ICMS' => $icms ? $this->monetario('vBC', $icms) : '', 'IBS/CBS' => $this->monetario($rtc . '/vBC', $det), 'IS' => $this->monetario('imposto/IS/vBCIS', $det), 'IPI' => $ipi ? $this->monetario('vBC', $ipi) : ''),
                'aliquotas' => array('ICMS' => $icms ? $this->porcentagem($this->valor('pICMS', $icms)) : '', 'CBS' => $this->aliquota($rtc . '/gCBS', 'pCBS', $det), 'IBS UF' => $this->aliquota($rtc . '/gIBSUF', 'pIBSUF', $det), 'IBS MUN' => $this->aliquota($rtc . '/gIBSMun', 'pIBSMun', $det), 'IPI' => $ipi ? $this->porcentagem($this->valor('pIPI', $ipi)) : '', 'IS' => $this->porcentagem($this->valor('imposto/IS/pIS', $det))),
                'tributos' => array('ICMS' => $icms ? $this->monetario('vICMS', $icms) : '', 'CBS' => $this->monetario($rtc . '/gCBS/vCBS', $det), 'IBS UF' => $this->monetario($rtc . '/gIBSUF/vIBSUF', $det), 'IBS MUN' => $this->monetario($rtc . '/gIBSMun/vIBSMun', $det), 'IPI' => $ipi ? $this->monetario('vIPI', $ipi) : '', 'IS' => $this->monetario('imposto/IS/vIS', $det))
            );
        }
        if (!$itens) { throw new \InvalidArgumentException('XML sem itens para impressão do DANFE RTC.'); }
        $this->dados = array(
            'chave' => preg_replace('/^NFe/', '', $this->inf->getAttribute('Id')),
            'numero' => $this->valor('ide/nNF'), 'serie' => $this->valor('ide/serie'),
            'protocolo' => $protocolo ? $this->valor('nProt', $protocolo) : '',
            'autorizacao' => $protocolo ? $this->valor('dhRecbto', $protocolo) : '',
            'status' => $protocolo ? $this->valor('cStat', $protocolo) : '',
            'qrCode' => $supl ? $this->valor('qrCode', $supl) : '',
            'itens' => $itens,
            'total' => $this->numero($this->valor('total/vNFTot') !== '' ? $this->valor('total/vNFTot') : $this->valor('total/ICMSTot/vNF')),
            'crt' => $this->valor('emit/CRT')
        );
        return $this->dados;
    }

    private function porcentagem($valor)
    {
        return $valor === '' ? '' : $this->numero($valor, 4) . '%';
    }

    private function texto($texto)
    {
        return iconv('UTF-8', 'Windows-1252//TRANSLIT', (string) $texto);
    }

    /** Quebra pelo tamanho real da fonte, incluindo palavras/códigos sem espaços. */
    private function linhas($texto, $largura, $fonte = 7)
    {
        $this->pdf->SetFont('Arial', '', $fonte);
        $linhas = array();
        foreach (explode("\n", str_replace("\r", '', $this->texto($texto))) as $paragrafo) {
            $linha = '';
            foreach (explode(' ', $paragrafo) as $palavra) {
                if ($linha !== '' && $this->pdf->GetStringWidth($linha . ' ' . $palavra) > $largura) {
                    $linhas[] = $linha;
                    $linha = '';
                }
                while ($this->pdf->GetStringWidth($palavra) > $largura && strlen($palavra) > 1) {
                    $n = 1;
                    while ($n < strlen($palavra) && $this->pdf->GetStringWidth(substr($palavra, 0, $n + 1)) <= $largura) { $n++; }
                    $linhas[] = substr($palavra, 0, $n);
                    $palavra = substr($palavra, $n);
                }
                $linha .= ($linha === '' ? '' : ' ') . $palavra;
            }
            $linhas[] = $linha;
        }
        return $linhas;
    }

    private function imprimirLinhas($x, $y, $largura, array $linhas, $fonte = 7, $altura = 2.8, $negrito = false, $alinhamento = 'L')
    {
        $margem = $this->pdf->cMargin;
        $this->pdf->cMargin = 0.5;
        $this->pdf->SetFont('Arial', $negrito ? 'B' : '', $fonte);
        foreach ($linhas as $linha) {
            $this->pdf->SetXY($x, $y);
            $this->pdf->Cell($largura, $altura, $linha, 0, 0, $alinhamento);
            $y += $altura;
        }
        $this->pdf->cMargin = $margem;
    }

    /** Coordenadas (pt) medidas nas páginas 8 e 9 da NT fornecida.
     * Cada lista contém as divisões reais do quadro, da esquerda para a direita.
     */
    private function perfilModelo()
    {
        if ($this->orientacao === 'P') {
            return array(
                'x' => 28.5, 'direita' => 581.0, 'y' => 21.25, 'cabecalhoY' => 74.5,
                'cabecalhoAltura' => 66, 'identificacaoAltura' => 22, 'crtAltura' => 22.5,
                'campoAltura' => 17.5, 'duplicataAltura' => 21.5, 'produtoMinimo' => 25.5,
                'produtoColunasAltura' => 13.5, 'adicionaisTitulo' => 15, 'adicionaisCorpo' => 61,
                'cabecalho' => array(28.5, 201, 327, 581),
                'natureza' => array(28.5, 327, 581),
                'inscricoes' => array(28.5, 201, 398.5, 581),
                'crt' => array(28.5, 297.75, 581),
                'destNome' => array(28.5, 338, 479, 581),
                'destEndereco' => array(28.5, 282.5, 415, 479, 581),
                'destMunicipio' => array(28.5, 194, 293.5, 338, 479, 581),
                'duplicatas' => array(28.5, 100.75, 159.25, 202.75, 289.75, 340.75, 391.75, 471.75, 529.75, 581),
                'produtosTotais' => array(28.5, 152, 261, 371, 472, 581),
                'icmsTotais' => array(28.5, 152, 261, 370, 472, 581),
                'icmsOpcionais' => array(28.5, 166.25, 289.75, 427.75, 581),
                'rtcTotais' => array(28.5, 177, 312, 450, 581),
                'rtcMono' => array(28.5, 166.25, 262.25, 442.75, 581),
                'issqn' => array(28.5, 183.25, 315.75, 448.25, 581),
                'transporte' => array(28.5, 194.25, 271.25, 348.75, 426.25, 470.25, 581),
                'transporteEndereco' => array(28.5, 249.25, 381.75, 426.25, 581),
                'volumes' => array(28.5, 116.75, 227.25, 315.75, 415.25, 497.75, 581),
                'produtos' => array(28.5, 145.5, 180, 221.5, 270, 318.5, 394.5, 470.5, 581),
                'adicionais' => array(28.5, 276, 461.5, 467.25, 581),
                'espacos' => array('destinatario' => 6.5, 'cobranca' => 3.25, 'totalProdutos' => 4.25, 'icms' => 5.5, 'rtc' => 3.75, 'issqn' => 3.75, 'transporte' => 3.75, 'produtos' => 3.75, 'adicionais' => 3.5),
                'titulos' => array('destinatario' => 8.5, 'cobranca' => 9.5, 'totalProdutos' => 9.5, 'icms' => 10, 'rtc' => 10, 'issqn' => 9.75, 'transporte' => 10, 'produtos' => 10),
            );
        }
        return array(
            'x' => 21.5, 'direita' => 766.5, 'y' => 9, 'cabecalhoY' => 9,
            'cabecalhoAltura' => 58, 'identificacaoAltura' => 14.5, 'crtAltura' => 14.5,
            'campoAltura' => 14.5, 'duplicataAltura' => 20.25, 'produtoMinimo' => 26,
            'produtoColunasAltura' => 12.5, 'adicionaisTitulo' => 14, 'adicionaisCorpo' => 75,
            'cabecalho' => array(21.5, 274.5, 424, 766.5),
            'natureza' => array(21.5, 424, 766.5),
            'inscricoes' => array(21.5, 274.5, 520.5, 766.5),
            'crt' => array(21.5, 384.5, 766.5),
            'destNome' => array(21.5, 439, 629.5, 766.5),
            'destEndereco' => array(21.5, 364, 543, 629.5, 766.5),
            'destMunicipio' => array(21.5, 245, 379, 439, 629.5, 766.5),
            'duplicatas' => array(21.5, 119.25, 197.75, 256.25, 374.25, 442.75, 511.5, 619.25, 697.75, 766.5),
            'produtosTotais' => array(21.5, 188, 335, 483.5, 619.5, 766.5),
            'icmsTotais' => array(21.5, 188, 335, 482, 619.5, 766.5),
            'icmsOpcionais' => array(21.5, 207.5, 374.25, 560.25, 766.5),
            'rtcTotais' => array(21.5, 212.5, 425.5, 588.5, 766.5),
            'rtcMono' => array(21.5, 207.75, 393.75, 580.25, 766.5),
            'issqn' => array(21.5, 230.25, 409.25, 588.25, 766.5),
            'transporte' => array(21.5, 244.75, 349.25, 453.75, 557.75, 617.25, 766.5),
            'transporteEndereco' => array(21.5, 319.25, 498.25, 557.75, 766.5),
            'volumes' => array(21.5, 140.75, 289.75, 408.75, 542.75, 654.75, 766.5),
            'produtos' => array(21.5, 262.5, 312, 368.5, 425.5, 475, 553, 645, 766.5),
            'adicionais' => array(21.5, 381, 655.5, 661.75, 766.5),
            'espacos' => array_fill_keys(array('destinatario', 'cobranca', 'totalProdutos', 'icms', 'rtc', 'issqn', 'transporte', 'produtos', 'adicionais'), 0),
            'titulos' => array('destinatario' => 8.5, 'cobranca' => 8.25, 'totalProdutos' => 8.75, 'icms' => 8, 'rtc' => 8.5, 'issqn' => 9.25, 'transporte' => 7.75, 'produtos' => 7.75),
        );
    }

    private function mm($pt) { return $pt * 25.4 / 72; }

    private function divisoes($nome)
    {
        return array_map(function ($pt) { return $this->mm($pt); }, $this->perfil[$nome]);
    }

    private function quadro($x, $y, $w, $h, $opcional = false, $titulo = false)
    {
        $this->pdf->SetFillColor(...($opcional ? array(203, 220, 239) : ($titulo ? array(238, 238, 238) : array(255, 255, 255))));
        $this->pdf->_out($opcional ? '[1.2 0.7] 0 d' : '[] 0 d');
        $this->pdf->Rect($x, $y, $w, $h, 'DF');
        $this->pdf->_out('[] 0 d');
    }

    private function garantirEspaco($altura)
    {
        if ($this->y + $altura > $this->limite) { $this->novaPagina(false); }
    }

    private function titulo($texto, $nome, $opcional = false)
    {
        $espaco = $this->mm($this->perfil['espacos'][$nome]);
        $altura = $this->mm($this->perfil['titulos'][$nome]);
        $this->garantirEspaco($espaco + $altura + $this->mm($this->perfil['campoAltura']));
        $this->y += $espaco;
        $this->quadro($this->margemX, $this->y, $this->largura, $altura, $opcional, true);
        $this->pdf->SetTextColor(0);
        $this->imprimirLinhas($this->margemX + 0.6, $this->y + 0.15, $this->largura - 1.2, array($this->texto($texto)), 6.5, $altura - 0.3, true);
        $this->y += $altura;
    }

    /** Campo = legenda, conteúdo, alinhamento. As divisões vêm do modelo, não do número de campos preenchidos. */
    private function campos(array $campos, $divisoes, $opcional = false, $alturaPt = null)
    {
        $xs = is_array($divisoes) ? $divisoes : $this->divisoes($divisoes);
        $altura = $this->mm($alturaPt ?? $this->perfil['campoAltura']);
        $fonte = $this->orientacao === 'P' ? 6 : 6.5;
        $labelFonte = 5.3;
        $labelsAltura = 1.95;
        $valoresAltura = 2.25;
        $preparados = array();
        foreach ($campos as $i => $campo) {
            $w = $xs[$i + 1] - $xs[$i];
            $labels = $this->linhas($campo[0], $w - 1.2, $labelFonte);
            $valores = $this->linhas($campo[1], $w - 1.2, $fonte);
            $altura = max($altura, count($labels) * $labelsAltura + count($valores) * $valoresAltura + 0.5);
            $preparados[] = array($xs[$i], $w, $labels, $valores, $campo[2] ?? 'L', $campo[3] ?? $opcional);
        }
        $this->garantirEspaco($altura);
        foreach ($preparados as $c) {
            $this->quadro($c[0], $this->y, $c[1], $altura, $c[5]);
            $this->pdf->SetTextColor(120);
            $this->imprimirLinhas($c[0] + 0.5, $this->y + 0.15, $c[1] - 1, $c[2], $labelFonte, $labelsAltura);
            $this->pdf->SetTextColor(0);
            $this->imprimirLinhas($c[0] + 0.5, $this->y + 0.35 + count($c[2]) * $labelsAltura, $c[1] - 1, $c[3], $fonte, $valoresAltura, false, $c[4]);
        }
        $this->y += $altura;
    }

    private function numeroNota()
    {
        return implode('.', str_split(str_pad($this->dados['numero'], 9, '0', STR_PAD_LEFT), 3));
    }

    private function rotacionado($x, $y, $texto, $fonte = 6, $negrito = false)
    {
        $this->pdf->SetFont('Arial', $negrito ? 'B' : '', $fonte);
        $this->pdf->_out(sprintf('q 0 1 -1 0 %.3F %.3F cm', $x * $this->pdf->k, ($this->pdf->h - $y) * $this->pdf->k));
        $this->pdf->_out('BT ' . $this->pdf->TextColor . ' /F' . $this->pdf->CurrentFont['i'] . ' ' . $fonte . ' Tf 0 0 Td (' . $this->pdf->_escape($this->texto($texto)) . ') Tj ET Q');
    }

    private function canhoto()
    {
        if (!$this->canhoto) { return; }
        $recebemos = 'RECEBEMOS DE ' . $this->valor('emit/xNome') . ' OS PRODUTOS/SERVIÇOS CONSTANTES DA NOTA FISCAL ELETRÔNICA INDICADA AO LADO';
        if ($this->orientacao === 'P') {
            $x = $this->margemX; $y = $this->mm(21.25);
            $w = $this->mm(486.5 - 28.5); $h = $this->mm(46);
            $this->quadro($x, $y, $w, $h, true);
            $this->pdf->SetTextColor(0);
            $this->imprimirLinhas($x + 0.5, $y + 2, $w - 1, $this->linhas($recebemos, $w - 1, 5.4), 5.4, 2.1, true);
            $linha = $this->mm(45.5); $dataX = $this->mm(166.5);
            $this->pdf->Line($x, $linha, $x + $w, $linha);
            $this->pdf->Line($dataX, $linha, $dataX, $y + $h);
            $this->pdf->SetTextColor(120);
            $this->imprimirLinhas($x + 0.5, $linha + 0.2, $dataX - $x - 1, array('DATA DE RECEBIMENTO'), 5.3, 2);
            $this->imprimirLinhas($dataX + 0.5, $linha + 0.2, $x + $w - $dataX - 1, array($this->texto('IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR')), 5.3, 2);
            $this->quadro($x + $w, $y, $this->largura - $w, $h, true);
            $this->pdf->SetTextColor(0);
            $this->imprimirLinhas($x + $w, $y + 4, $this->largura - $w, array('NF-e', $this->texto('Nº ' . $this->numeroNota()), $this->texto('SÉRIE ' . str_pad($this->dados['serie'], 3, '0', STR_PAD_LEFT))), 6.5, 3, true, 'C');
            $this->y = $this->mm($this->perfil['cabecalhoY']);
        } else {
            $x = $this->mm(773.25); $y = $this->mm(9); $w = $this->mm(828.5 - 773.25); $h = $this->mm(548.5);
            $this->quadro($x, $y, $w, $h, true);
            $this->pdf->Line($x, $this->mm(480), $x + $w, $this->mm(480));
            $this->pdf->Line($this->mm(801.25), $y, $this->mm(801.25), $this->mm(480));
            $this->pdf->SetTextColor(0);
            $this->pdf->SetFont('Arial', 'B', 6);
            $fonte = min(6, 6 * $this->mm(455) / max(1, $this->pdf->GetStringWidth($this->texto($recebemos))));
            $this->rotacionado($x + 5, $this->mm(474), $recebemos, $fonte, true);
            $this->pdf->SetTextColor(120);
            $this->rotacionado($x + 13, $this->mm(474), 'DATA DE RECEBIMENTO', 6);
            $this->rotacionado($x + 13, $this->mm(196), 'IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR', 6);
            $this->pdf->SetTextColor(0);
            $this->rotacionado($x + 7, $this->mm(546), 'NF-e', 8, true);
            $this->rotacionado($x + 11, $this->mm(546), 'Nº ' . $this->numeroNota(), 6);
            $this->rotacionado($x + 15, $this->mm(546), 'SÉRIE ' . str_pad($this->dados['serie'], 3, '0', STR_PAD_LEFT), 6);
        }
    }

    private function novaPagina($primeira)
    {
        $this->pdf->AddPage($this->orientacao);
        $this->y = $this->mm($this->perfil['y']);
        if ($primeira) { $this->canhoto(); }
        $xs = $this->divisoes('cabecalho'); $y = $this->y;
        $h = $this->mm($this->perfil['cabecalhoAltura']);
        $endereco = $this->endereco('emit/enderEmit') . "\n" . $this->valor('emit/enderEmit/xBairro') . "\n" . $this->valor('emit/enderEmit/xMun') . '/' . $this->valor('emit/enderEmit/UF') . ' - CEP ' . $this->valor('emit/enderEmit/CEP') . "\nFone: " . $this->valor('emit/enderEmit/fone');
        $logoW = $this->logoArquivo ? 16 : 0;
        $nome = $this->linhas($this->valor('emit/xNome'), $xs[1] - $xs[0] - $logoW - 2, 8);
        $linhas = $this->linhas($endereco, $xs[1] - $xs[0] - $logoW - 2, 6);
        $h = max($h, (count($nome) + count($linhas)) * 2.3 + 2);
        $this->quadro($xs[0], $y, $xs[1] - $xs[0], $h);
        if ($this->logoArquivo) {
            $tam = getimagesize($this->logoArquivo);
            $largura = min(14, 18 * $tam[0] / $tam[1]);
            $this->pdf->Image($this->logoArquivo, $xs[0] + 1, $y + 2, $largura, $largura * $tam[1] / $tam[0], $tam[2] === IMAGETYPE_JPEG ? 'JPG' : 'PNG');
        }
        $this->pdf->SetTextColor(0);
        $textoY = $y + max(1, ($h - (count($nome) + count($linhas)) * 2.3) / 2);
        $this->imprimirLinhas($xs[0] + $logoW + 0.5, $textoY, $xs[1] - $xs[0] - $logoW - 1, $nome, 8, 2.3, true, 'C');
        $this->imprimirLinhas($xs[0] + $logoW + 0.5, $textoY + count($nome) * 2.3, $xs[1] - $xs[0] - $logoW - 1, $linhas, 6, 2.3, false, 'C');
        $w = $xs[2] - $xs[1];
        $this->quadro($xs[1], $y, $w, $h);
        $this->imprimirLinhas($xs[1], $y + 0.5, $w, array('DANFE'), 10, 4, true, 'C');
        $this->pdf->SetTextColor(120);
        $this->imprimirLinhas($xs[1], $y + 4.5, $w, array($this->texto('DOCUMENTO AUXILIAR DA NOTA FISCAL ELETRÔNICA')), 4.3, 2, false, 'C');
        $this->pdf->SetTextColor(0);
        $boxY = $this->orientacao === 'P' ? 9 : 8;
        $boxH = $this->orientacao === 'P' ? 5 : 3.5;
        $this->pdf->Rect($xs[1] + ($w - 5) / 2, $y + $boxY, 5, $boxH);
        $this->imprimirLinhas($xs[1] + ($w - 5) / 2, $y + $boxY + 0.2, 5, array($this->valor('ide/tpNF')), 7, $boxH - 0.4, true, 'C');
        $this->imprimirLinhas($xs[1], $y + $h - 7.8, $w, array($this->texto('0 - ENTRADA   1 - SAÍDA'), $this->texto('Nº ' . $this->numeroNota()), $this->texto('SÉRIE ' . str_pad($this->dados['serie'], 3, '0', STR_PAD_LEFT) . '   FOLHA ' . $this->pdf->PageNo() . '/{nb}')), 5.5, 2.4, false, 'C');
        $w = $xs[3] - $xs[2];
        $this->quadro($xs[2], $y, $w, $h);
        $barraH = $h * 0.36; $chaveH = $h * 0.36;
        $this->pdf->Line($xs[2], $y + $barraH, $xs[3], $y + $barraH);
        $this->pdf->Line($xs[2], $y + $barraH + $chaveH, $xs[3], $y + $barraH + $chaveH);
        if (preg_match('/^[0-9A-Z]{44}$/', $this->dados['chave'])) {
            $this->pdf->SetFillColor(0);
            $this->pdf->Code128($xs[2] + 5, $y + 0.5, $this->dados['chave'], $w - 10, $barraH - 1);
        }
        $this->pdf->SetTextColor(120);
        $this->imprimirLinhas($xs[2] + 0.5, $y + $barraH + 0.4, $w - 1, array('CHAVE DE ACESSO'), 5.3, 2);
        $this->pdf->SetTextColor(0);
        $this->imprimirLinhas($xs[2], $y + $barraH + 4.5, $w, array(trim(chunk_split($this->dados['chave'], 4, ' '))), 6, 2.5, true, 'C');
        $this->pdf->SetTextColor(120);
        $this->imprimirLinhas($xs[2] + 0.5, $y + $barraH + $chaveH + 1, $w - 1, $this->linhas('Consulte a autenticidade no portal nacional da NF-e www.nfe.fazenda.gov.br/portal ou no site da SEFAZ autorizadora.', $w - 1.2, 4.6), 4.6, 1.8, false, 'C');
        $this->y += $h;
        $proto = $this->dados['protocolo'];
        if ($proto !== '') { $proto .= ' - ' . $this->data($this->dados['autorizacao']) . ' ' . $this->data($this->dados['autorizacao'], true); }
        $this->campos(array(array('NATUREZA DA OPERAÇÃO', $this->valor('ide/natOp')), array('PROTOCOLO DE AUTORIZAÇÃO DE USO', $proto, 'R')), 'natureza', false, $this->perfil['identificacaoAltura']);
        if ($primeira) {
            $this->campos(array(array('INSCRIÇÃO ESTADUAL', $this->valor('emit/IE')), array('INSCRIÇÃO ESTADUAL DO SUBSTITUTO TRIBUTÁRIO', $this->valor('emit/IEST')), array('CNPJ / CPF', $this->doc('emit'), 'R')), 'inscricoes', false, $this->perfil['identificacaoAltura']);
            $regimes = array('1' => 'SIMPLES NACIONAL', '2' => 'SIMPLES NACIONAL - EXCESSO DE SUBLIMITE', '3' => 'REGIME NORMAL', '4' => 'SIMPLES NACIONAL - MEI');
            $crt = $this->dados['crt'];
            // O campo reservado é facultativo e permanece vazio: sua tag ainda não foi publicada.
            $this->campos(array(array('CÓDIGO DO REGIME TRIBUTÁRIO', $crt . (isset($regimes[$crt]) ? ' - ' . $regimes[$crt] : '')), array('TIPO DE REGIME DE APURAÇÃO DO IBS E DA CBS', '', 'L', true)), 'crt', false, $this->perfil['crtAltura']);
        }
        $avisos = array();
        if ($this->cancelada || in_array($this->dados['status'], array('101', '151', '155'), true)) { $avisos[] = 'NF-e CANCELADA'; }
        if (in_array($this->dados['status'], array('110', '301', '302'), true)) { $avisos[] = 'USO DENEGADO'; }
        if ($this->valor('ide/tpAmb') === '2') { $avisos[] = 'SEM VALOR FISCAL - AMBIENTE DE HOMOLOGAÇÃO'; }
        if ($this->visualizacao === 'S' || $this->dados['protocolo'] === '') { $avisos[] = 'PRÉ-VISUALIZAÇÃO - NF-e SEM AUTORIZAÇÃO DE USO'; }
        if ($this->valor('ide/tpEmis') !== '1') { $avisos[] = 'EMISSÃO EM CONTINGÊNCIA - TIPO ' . $this->valor('ide/tpEmis'); }
        $this->pdf->SetTextColor(0);
        foreach ($avisos as $aviso) {
            $this->imprimirLinhas($this->margemX, $this->y, $this->largura, array($this->texto($aviso)), 7, 3, true, 'C');
            $this->y += 3;
        }
    }

    private function destinatario()
    {
        $this->titulo('DESTINATÁRIO / REMETENTE', 'destinatario');
        $emissao = $this->valor('ide/dhEmi') ?: $this->valor('ide/dEmi');
        $saida = $this->valor('ide/dhSaiEnt') ?: $this->valor('ide/dSaiEnt');
        $this->campos(array(array('NOME / RAZÃO SOCIAL', $this->valor('dest/xNome')), array('CNPJ / CPF', $this->doc('dest') ?: $this->valor('dest/idEstrangeiro')), array('DATA DE EMISSÃO', $this->data($emissao))), 'destNome');
        $this->campos(array(array('ENDEREÇO', $this->endereco('dest/enderDest')), array('BAIRRO / DISTRITO', $this->valor('dest/enderDest/xBairro')), array('CEP', $this->valor('dest/enderDest/CEP')), array('DATA DE ENTRADA / SAÍDA', $this->data($saida))), 'destEndereco');
        $this->campos(array(array('MUNICÍPIO', $this->valor('dest/enderDest/xMun')), array('FONE / FAX', $this->valor('dest/enderDest/fone')), array('UF', $this->valor('dest/enderDest/UF')), array('INSCRIÇÃO ESTADUAL', $this->valor('dest/IE')), array('HORA DE SAÍDA', $this->data($saida, true) ?: $this->valor('ide/hSaiEnt'))), 'destMunicipio');
    }

    private function cobranca()
    {
        if (!$this->nos('cobr/fat')->length && !$this->nos('cobr/dup')->length) { return; }
        $this->titulo('FATURA / DUPLICATAS', 'cobranca', true);
        $parcelas = array();
        foreach ($this->nos('cobr/dup') as $dup) { $parcelas[] = array($this->valor('nDup', $dup), $this->data($this->valor('dVenc', $dup)), $this->monetario('vDup', $dup)); }
        if (!$parcelas) { $parcelas[] = array($this->valor('cobr/fat/nFat'), '', $this->monetario('cobr/fat/vLiq')); }
        foreach (array_chunk($parcelas, 3) as $linha) {
            while (count($linha) < 3) { $linha[] = array('', '', ''); }
            $campos = array();
            foreach ($linha as $parcela) {
                $campos[] = array('FATURA/DUPLICATA', $parcela[0]);
                $campos[] = array('VENCIMENTO', $parcela[1]);
                $campos[] = array('VALOR', $parcela[2]);
            }
            $this->campos($campos, 'duplicatas', true, $this->perfil['duplicataAltura']);
        }
    }

    private function totais()
    {
        $icms = 'total/ICMSTot/';
        $this->titulo('TOTAL DOS PRODUTOS E TOTAL DA NOTA', 'totalProdutos');
        $this->campos(array(array('VALOR TOTAL DOS PRODUTOS', $this->monetario($icms . 'vProd')), array('VALOR DO FRETE', $this->monetario($icms . 'vFrete')), array('VALOR DO SEGURO', $this->monetario($icms . 'vSeg')), array('DESCONTO', $this->monetario($icms . 'vDesc')), array('OUTRAS DESPESAS ACESSÓRIAS', $this->monetario($icms . 'vOutro'))), 'produtosTotais');
        $this->campos(array(array('VALOR TOTAL DA NOTA', $this->dados['total'])), array($this->margemX, $this->margemX + $this->largura));
        $this->titulo('TOTAL DO ICMS / IPI', 'icms');
        $this->campos(array(array('BASE DE CÁLCULO DO ICMS', $this->monetario($icms . 'vBC')), array('VALOR DO ICMS', $this->monetario($icms . 'vICMS')), array('BASE DE CÁLCULO DO ICMS ST', $this->monetario($icms . 'vBCST')), array('VALOR DO ICMS ST', $this->monetario($icms . 'vST')), array('VALOR DO IPI', $this->monetario($icms . 'vIPI'))), 'icmsTotais');
        $this->opcionais($icms, array('vFCP' => 'VALOR DO FCP', 'vFCPST' => 'VALOR DO FCP RETIDO POR ST', 'vICMSUFDest' => 'VALOR DO DIFAL NA UF DE DESTINO', 'vFCPUFDest' => 'VALOR DO FCP NA UF DE DESTINO'), 'icmsOpcionais');
        $this->opcionais($icms, array('qBCMono' => 'BC DO ICMS MONOFÁSICO', 'vICMSMono' => 'VALOR DO ICMS MONOFÁSICO', 'qBCMonoReten' => 'BC DO ICMS MONOFÁSICO POR RETENÇÃO', 'vICMSMonoReten' => 'VALOR DO ICMS MONOFÁSICO POR RETENÇÃO'), 'icmsOpcionais');
        $rtc = 'total/IBSCBSTot/';
        $this->titulo('TOTAL DO IBS / CBS / IS', 'rtc');
        $this->campos(array(array('VALOR DA CBS', $this->monetario($rtc . 'gCBS/vCBS')), array('VALOR DO IBS UF', $this->monetario($rtc . 'gIBS/gIBSUF/vIBSUF')), array('VALOR DO IBS MUNICÍPIO', $this->monetario($rtc . 'gIBS/gIBSMun/vIBSMun')), array('VALOR DO IMPOSTO SELETIVO', $this->monetario('total/ISTot/vIS'))), 'rtcTotais');
        $this->opcionais($rtc . 'gMono/', array('vIBSMono' => 'VALOR DO IBS MONOFÁSICO', 'vCBSMono' => 'VALOR DA CBS MONOFÁSICA', 'vIBSMonoReten' => 'VALOR DO IBS MONOFÁSICO POR RETENÇÃO', 'vCBSMonoReten' => 'VALOR DA CBS MONOFÁSICA POR RETENÇÃO'), 'rtcMono');
        if ($this->temValorUtil(array('total/ISSQNtot/vServ', 'total/ISSQNtot/vBC', 'total/ISSQNtot/vISS'))) {
            $this->titulo('CÁLCULO DO ISSQN', 'issqn', true);
            $this->campos(array(array('INSCRIÇÃO MUNICIPAL', $this->valor('emit/IM')), array('VALOR TOTAL DOS SERVIÇOS', $this->monetario('total/ISSQNtot/vServ')), array('BASE DE CÁLCULO DO ISSQN', $this->monetario('total/ISSQNtot/vBC')), array('VALOR DO ISSQN', $this->monetario('total/ISSQNtot/vISS'))), 'issqn', true, $this->orientacao === 'P' ? 16 : 14.75);
        }
    }

    private function opcionais($prefixo, array $mapa, $divisoes)
    {
        if (!$this->temValorUtil(array_map(function ($tag) use ($prefixo) { return $prefixo . $tag; }, array_keys($mapa)))) { return; }
        $campos = array();
        foreach ($mapa as $tag => $label) { $campos[] = array($label, $this->monetario($prefixo . $tag, null, strpos($tag, 'qBC') === 0 ? 4 : 2)); }
        $this->campos($campos, $divisoes, true);
    }

    private function transporte()
    {
        $modo = $this->valor('transp/modFrete');
        if (($modo === '' || $modo === '9')
            && !$this->nos('transp/transporta')->length && !$this->nos('transp/veicTransp')->length
            && !$this->nos('transp/vol')->length && !$this->nos('transp/reboque')->length) { return; }
        $this->titulo('TRANSPORTADOR / VOLUMES TRANSPORTADOS', 'transporte', true);
        $modos = array('0' => 'Remetente', '1' => 'Destinatário', '2' => 'Terceiros', '3' => 'Próprio / Remetente', '4' => 'Próprio / Destinatário', '9' => 'Sem ocorrência de transporte');
        $altura = $this->orientacao === 'P' ? 17.75 : 14.5;
        $this->campos(array(array('RAZÃO SOCIAL', $this->valor('transp/transporta/xNome')), array('FRETE POR CONTA DE', $modo . (isset($modos[$modo]) ? ' - ' . $modos[$modo] : '')), array('CÓDIGO ANTT', $this->valor('transp/veicTransp/RNTC')), array('PLACA DO VEÍCULO', $this->valor('transp/veicTransp/placa')), array('UF', $this->valor('transp/veicTransp/UF')), array('CNPJ / CPF', $this->doc('transp/transporta'))), 'transporte', true, $altura);
        $this->campos(array(array('ENDEREÇO', $this->valor('transp/transporta/xEnder')), array('MUNICÍPIO', $this->valor('transp/transporta/xMun')), array('UF', $this->valor('transp/transporta/UF')), array('INSCRIÇÃO ESTADUAL', $this->valor('transp/transporta/IE'))), 'transporteEndereco', true, $altura);
        $volumes = iterator_to_array($this->nos('transp/vol'));
        if (!$volumes) { $volumes = array(null); }
        foreach ($volumes as $vol) {
            $this->campos(array(array('QUANTIDADE', $vol ? $this->valor('qVol', $vol) : ''), array('ESPÉCIE', $vol ? $this->valor('esp', $vol) : ''), array('MARCA', $vol ? $this->valor('marca', $vol) : ''), array('NUMERAÇÃO', $vol ? $this->valor('nVol', $vol) : ''), array('PESO BRUTO', $vol ? $this->monetario('pesoB', $vol, 3) : ''), array('PESO LÍQUIDO', $vol ? $this->monetario('pesoL', $vol, 3) : '')), 'volumes', true, $altura);
        }
    }

    private function cabecalhoItens()
    {
        $this->titulo('DADOS DOS PRODUTOS / SERVIÇOS', 'produtos');
        $labels = array('DESCRIÇÃO DO PRODUTO / SERVIÇO', "CST /\nCFOP", 'QTD / UN', 'VLR UNIT', 'VLR TOTAL', 'BASES DE CÁLCULO', 'ALÍQUOTAS', 'VALOR DOS TRIBUTOS');
        $x = $this->margemX; $h = $this->mm($this->perfil['produtoColunasAltura']);
        foreach ($labels as $i => $label) {
            $this->quadro($x, $this->y, $this->colunas[$i], $h, false, true);
            $this->pdf->SetTextColor(0);
            $this->imprimirLinhas($x + 0.3, $this->y + 0.2, $this->colunas[$i] - 0.6, $this->linhas($label, $this->colunas[$i] - 1, 5.2), 5.2, 1.8, true, 'C');
            $x += $this->colunas[$i];
        }
        $this->y += $h;
    }

    private function numeroModelo($texto)
    {
        if (substr($texto, -1) !== '%') { return $texto; }
        $numero = substr($texto, 0, -1);
        while (substr($numero, -1) === '0' && strlen(substr(strrchr($numero, ','), 1)) > 2) { $numero = substr($numero, 0, -1); }
        return $numero . '%';
    }

    private function lider($label, $valor, $w, $fonte)
    {
        $valor = $this->numeroModelo($valor);
        $this->pdf->SetFont('Arial', '', $fonte);
        $texto = $label . '..' . $valor;
        while ($this->pdf->GetStringWidth($this->texto($texto . '.')) < $w - 1.8) { $texto = $label . '.' . substr($texto, strlen($label)); }
        return $texto;
    }

    /** Exatamente três linhas: ICMS/CBS; IBS UF/IPI; IBS Município/IS. */
    private function linhasTributos(array $valores, $w, $font)
    {
        $linhas = array();
        foreach (array(array('ICMS', 'CBS'), array('IBS UF', 'IPI'), array('IBS MUN', 'IS')) as $par) {
            $linha = $par[0] . '..' . $this->numeroModelo($valores[$par[0]]) . ' / ' . $par[1] . '..' . $this->numeroModelo($valores[$par[1]]);
            $fonte = $font;
            $this->pdf->SetFont('Arial', '', $fonte);
            while ($this->pdf->GetStringWidth($this->texto($linha)) > $w - 1.8 && $fonte > 4.5) { $fonte -= 0.1; $this->pdf->SetFont('Arial', '', $fonte); }
            $linhas[] = array($this->texto($linha), $fonte);
        }
        return $linhas;
    }

    private function produtos()
    {
        $cabecalho = true; $font = $this->orientacao === 'P' ? 5.5 : 6;
        $linhaH = $this->mm($this->orientacao === 'P' ? 5.7 : 6.3);
        foreach ($this->dados['itens'] as $item) {
            $meta = '[NCM ' . $item['ncm'] . ']';
            if ($item['classificacao'] !== '') { $meta .= ' [cClassTrib ' . $item['classificacao'] . ']'; }
            $descricao = $this->linhas($item['descricaoModelo'], $this->colunas[0] - 1, $font);
            $metadata = $this->linhas($meta, $this->colunas[0] - 1, $font - 0.4);
            $simples = array('CST...' . $item['cst'] . "\nCFOP.." . $item['cfop'], $item['quantidade'] . "\n[" . $item['unidade'] . ']', $item['unitario'], $item['total']);
            $listas = array($descricao);
            foreach ($simples as $i => $valor) { $listas[] = $this->linhas($valor, $this->colunas[$i + 1] - 1, $font); }
            $bases = array();
            foreach ($item['bases'] as $tag => $valor) { $bases[] = $this->texto($this->lider($tag === 'IBS/CBS' ? 'IBS / CBS' : $tag, $valor, $this->colunas[5], $font)); }
            $listas[] = $bases;
            $max = max(array_map('count', $listas));
            $altura = max($this->mm($this->perfil['produtoMinimo']), max($max, count($descricao) + count($metadata) + 1) * $linhaH + 0.8);
            $headerH = $cabecalho ? $this->mm($this->perfil['produtoColunasAltura'] + $this->perfil['titulos']['produtos'] + $this->perfil['espacos']['produtos']) : 0;
            if ($this->y + $altura + $headerH > $this->limite && $altura < $this->limite - 55) { $this->novaPagina(false); $cabecalho = true; }
            if ($cabecalho) { $this->cabecalhoItens(); $cabecalho = false; }
            $offset = 0;
            do {
                $disponivel = $this->limite - $this->y;
                $necessario = max($max - $offset, count($descricao) - $offset + count($metadata) + 1) * $linhaH + 0.8;
                $h = max($this->mm($this->perfil['produtoMinimo']), min($necessario, $disponivel));
                if ($h > $disponivel || $disponivel < $linhaH * (count($metadata) + 2)) { $this->novaPagina(false); $this->cabecalhoItens(); continue; }
                $n = max(1, (int) floor(($h - 0.8) / $linhaH));
                $final = $offset + $n >= $max;
                if ($final && count($descricao) - $offset + count($metadata) + 1 > $n) { $final = false; $n -= count($metadata) + 1; }
                $x = $this->margemX;
                for ($i = 0; $i < 8; $i++) {
                    $w = $this->colunas[$i]; $this->quadro($x, $this->y, $w, $h);
                    $this->pdf->SetTextColor(0);
                    if ($i < 6) { $this->imprimirLinhas($x + 0.4, $this->y + 0.4, $w - 0.8, array_slice($listas[$i], $offset, $n), $font, $linhaH, false, in_array($i, array(2, 3, 4)) ? 'R' : 'L'); }
                    elseif ($offset === 0) {
                        foreach ($this->linhasTributos($i === 6 ? $item['aliquotas'] : $item['tributos'], $w, $font) as $r => $linha) {
                            $this->imprimirLinhas($x + 0.4, $this->y + 0.4 + $r * $linhaH, $w - 0.8, array($linha[0]), $linha[1], $linhaH);
                        }
                    }
                    if ($i === 0 && $final) {
                        $this->pdf->SetTextColor(120);
                        $this->imprimirLinhas($x + 0.4, $this->y + $h - count($metadata) * $linhaH - 0.4, $w - 0.8, $metadata, $font - 0.4, $linhaH, false, 'R');
                    }
                    $x += $w;
                }
                $this->y += $h; $offset += $n;
                if (!$final) { $this->novaPagina(false); $this->cabecalhoItens(); }
            } while (!$final);
        }
    }

    private function adicionais()
    {
        $xs = $this->divisoes('adicionais');
        $temQrCode = $this->dados['qrCode'] !== '';
        $direita = $temQrCode ? $xs[2] : $xs[4];
        $head = $this->mm($this->perfil['adicionaisTitulo']); $body = $this->mm($this->perfil['adicionaisCorpo']);
        $cpl = $this->valor('infAdic/infCpl');
        $trib = $this->monetario('total/ICMSTot/vTotTrib');
        if ($trib !== '') { $cpl .= "\nVALOR APROXIMADO DOS TRIBUTOS: R$ " . $trib; }
        foreach (array('retirada' => 'LOCAL DE RETIRADA', 'entrega' => 'LOCAL DE ENTREGA') as $grupo => $label) {
            if ($this->nos($grupo)->length) { $cpl .= "\n" . $label . ': ' . $this->endereco($grupo) . ' - ' . $this->valor($grupo . '/xMun') . '/' . $this->valor($grupo . '/UF') . ' ' . $this->doc($grupo); }
        }
        foreach ($this->nos('transp/reboque') as $reboque) { $cpl .= "\nREBOQUE: " . $this->valor('placa', $reboque) . '/' . $this->valor('UF', $reboque) . ' RNTC ' . $this->valor('RNTC', $reboque); }
        $fonte = 5.5; $lh = 2.1;
        $ls = $this->linhas($cpl, $xs[1] - $xs[0] - 1.2, $fonte);
        $fisco = $this->linhas($this->valor('infAdic/infAdFisco'), $direita - $xs[1] - 1.2, $fonte);
        $max = max(count($ls), count($fisco)); $offset = 0;
        do {
            $gap = $this->mm($this->perfil['espacos']['adicionais']);
            $this->garantirEspaco($gap + $head + $body);
            $this->y += $gap;
            $n = min($max - $offset, max(1, (int) floor(($this->limite - $this->y - $head - 3.5) / $lh)));
            $h = max($body, $n * $lh + 3.5);
            $this->quadro($xs[0], $this->y, $direita - $xs[0], $head, false, true);
            $this->pdf->SetTextColor(0);
            $this->imprimirLinhas($xs[0] + 0.5, $this->y + 0.3, $direita - $xs[0] - 1, array('DADOS ADICIONAIS'), 6.5, 3, true);
            $this->quadro($xs[0], $this->y + $head, $xs[1] - $xs[0], $h);
            $this->quadro($xs[1], $this->y + $head, $direita - $xs[1], $h);
            $this->pdf->SetTextColor(120);
            $this->imprimirLinhas($xs[0] + 0.5, $this->y + $head + 0.3, $xs[1] - $xs[0] - 1, array($this->texto('INFORMAÇÕES COMPLEMENTARES')), 4.8, 1.8);
            $this->imprimirLinhas($xs[1] + 0.5, $this->y + $head + 0.3, $direita - $xs[1] - 1, array('RESERVADO AO FISCO'), 4.8, 1.8);
            $this->pdf->SetTextColor(0);
            $this->imprimirLinhas($xs[0] + 0.5, $this->y + $head + 2.5, $xs[1] - $xs[0] - 1, array_slice($ls, $offset, $n), $fonte, $lh);
            $this->imprimirLinhas($xs[1] + 0.5, $this->y + $head + 2.5, $xs[2] - $xs[1] - 1, array_slice($fisco, $offset, $n), $fonte, $lh);
            if ($temQrCode) { $this->qrCode($xs[3], $this->y, $xs[4] - $xs[3], $head, $h, $offset === 0); }
            $this->y += $head + $h; $offset += $n;
            if ($offset < $max) { $this->novaPagina(false); }
        } while ($offset < $max);
    }

    private function qrCode($x, $y, $w, $head, $h, $preencher)
    {
        $this->quadro($x, $y, $w, $head, true, true);
        $this->quadro($x, $y + $head, $w, $h, true);
        $this->pdf->SetTextColor(0);
        $this->imprimirLinhas($x, $y + 0.3, $w, array('QR CODE'), 6.5, 3, true, 'C');
        // Sem ZX02 no XML, conserva o quadro da referência sem inventar URL ou QR.
        if (!$preencher || $this->dados['qrCode'] === '') { return; }
        require_once __DIR__ . '/../../bibliotecas/tcpdf/qrcode.php';
        $qr = new \QRcode($this->dados['qrCode'], 'M'); $matrix = $qr->getBarcodeArray();
        if (empty($matrix['num_rows'])) { throw new \RuntimeException('Não foi possível gerar o QR Code informado no XML.'); }
        $tam = min($w - 3, $h - 2);
        $s = $tam / ($matrix['num_rows'] + 8);
        $xx = $x + ($w - $tam) / 2; $yy = $y + $head + 1;
        $this->pdf->SetFillColor(255); $this->pdf->Rect($xx, $yy, $tam, $tam, 'F');
        $this->pdf->SetFillColor(0);
        foreach ($matrix['bcode'] as $r => $linha) {
            foreach ($linha as $c => $bit) { if ($bit) { $this->pdf->Rect($xx + ($c + 4) * $s, $yy + ($r + 4) * $s, $s, $s, 'F'); } }
        }
    }

    private function prepararLogo()
    {
        $this->logoArquivo = '';
        $this->logoTemporaria = '';
        if (!$this->logomarca || !is_file($this->logomarca)) { return; }
        $tam = @getimagesize($this->logomarca);
        if (!$tam) { return; }
        if ($tam[2] === IMAGETYPE_JPEG) { $this->logoArquivo = $this->logomarca; return; }
        if ($tam[2] !== IMAGETYPE_PNG) { return; }
        $cabecalho = file_get_contents($this->logomarca, false, null, 0, 29);
        if (strlen($cabecalho) < 29) { return; }
        // O FPDF 1.6 não aceita PNG com canal alfa ou entrelaçado.
        if (ord($cabecalho[24]) <= 8 && !in_array(ord($cabecalho[25]), array(4, 6), true) && ord($cabecalho[28]) === 0) {
            $this->logoArquivo = $this->logomarca;
            return;
        }
        if (!function_exists('imagecreatefrompng')) { return; }
        $origem = @imagecreatefrompng($this->logomarca);
        if (!$origem) { return; }
        $destino = imagecreatetruecolor($tam[0], $tam[1]);
        imagefill($destino, 0, 0, imagecolorallocate($destino, 255, 255, 255));
        imagecopy($destino, $origem, 0, 0, 0, 0, $tam[0], $tam[1]);
        $tmp = tempnam(sys_get_temp_dir(), 'danfe-rtc-logo-');
        try {
            if ($tmp !== false && imagejpeg($destino, $tmp, 95)) {
                $this->logoTemporaria = $tmp;
                $this->logoArquivo = $tmp;
            } elseif ($tmp !== false) { unlink($tmp); }
        } finally {
            imagedestroy($origem);
            imagedestroy($destino);
        }
    }

    public function gerarPDF()
    {
        $this->lerXML();
        $this->prepararLogo();
        try {
            return $this->montarPDF();
        } finally {
            if ($this->logoTemporaria && is_file($this->logoTemporaria)) { unlink($this->logoTemporaria); }
        }
    }

    private function montarPDF()
    {
        // O FPDF legado deste sistema redefine o nome A4 com largura de 655 pt.
        // Dimensões explícitas mantêm o papel normativo de 210 x 297 mm.
        $this->pdf = new \PDF_Code128($this->orientacao, 'mm', array(210, 297));
        $this->pdf->SetAutoPageBreak(false);
        $this->perfil = $this->perfilModelo();
        $this->margemX = $this->mm($this->perfil['x']);
        $this->pdf->SetMargins($this->margemX, $this->mm($this->perfil['y']), 5);
        $this->pdf->SetLineWidth(0.15);
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle($this->texto('DANFE RTC - NF-e ' . $this->dados['numero']));
        $this->largura = $this->mm($this->perfil['direita'] - $this->perfil['x']);
        $this->limite = $this->orientacao === 'L' ? 204 : 291;
        $divisoes = $this->divisoes('produtos');
        $this->colunas = array();
        for ($i = 0; $i < 8; $i++) { $this->colunas[] = $divisoes[$i + 1] - $divisoes[$i]; }
        $this->novaPagina(true);
        $this->destinatario();
        $this->cobranca();
        $this->totais();
        $this->transporte();
        $this->produtos();
        $this->adicionais();
        return $this->pdf->Output('', 'S');
    }
}
