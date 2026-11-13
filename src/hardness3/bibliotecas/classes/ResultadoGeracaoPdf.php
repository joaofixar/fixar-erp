<?php
namespace hardness;

/** Valida o arquivo gerado sem confundir um aviso de recurso com sucesso geral. */
class ResultadoGeracaoPdf
{
    /** Somente os cinco modelos do botao Imprimir Etiquetas (novo), pelos filtrados. */
    public static function ehImpressaoDeProdutosFiltrados($modelo, $link)
    {
        $modelos = array(
            '126a2d6df3da44bc15c3bb73b9d70709', // Produto 30x30
            '0b21c4ea16c39ba82439aae2c02b5917', // Produto 100x40
            'b41a5a3de9a2b555b46a30ab8527d358', // Locacao 100x40
            'f8c5fa7021ceb9f5b8da5b6ff6ddfbf9', // Locacao - Caixa 100x40
            'a0c571875fa192f5d410e9ba2910cbfe', // Gondola 100x40
        );
        if (!in_array($modelo, $modelos, true) || !is_string($link)) {
            return false;
        }
        $parametros = array();
        $query = parse_url($link, PHP_URL_QUERY);
        if (is_string($query)) {
            parse_str($query, $parametros);
        }
        return parse_url($link, PHP_URL_PATH) === '/sistema/personalizacao/tela/' . $modelo . '/'
            && isset($parametros['D001_Id'], $parametros['sql'])
            && is_scalar($parametros['D001_Id']) && (string) $parametros['D001_Id'] === '0'
            && is_string($parametros['sql']) && $parametros['sql'] !== ''
            && base64_decode($parametros['sql'], true) !== false;
    }

    /** Produtos ou notas fiscais filtrados, somente nos modelos dos respectivos menus. */
    public static function ehImpressaoFiltradaDeEtiquetas($modelo, $link)
    {
        if (self::ehImpressaoDeProdutosFiltrados($modelo, $link)) {
            return true;
        }
        $modelosNotas = array(
            'c12d5616e0266b451f3b12c611e8a02e', // Despacho 100x100
            'db3dddf3de6b96fcf62ee3db43e3dabf', // Despacho 100x150
            'eb224e3c4fad055f901430800db74133', // DANFE simplificada 105x125
        );
        if (!in_array($modelo, $modelosNotas, true) || !is_string($link)) {
            return false;
        }
        $parametros = array();
        $query = parse_url($link, PHP_URL_QUERY);
        if (is_string($query)) {
            parse_str($query, $parametros);
        }
        return parse_url($link, PHP_URL_PATH) === '/sistema/personalizacao/tela/' . $modelo . '/'
            && isset($parametros['T007_Id'], $parametros['sql'])
            && is_scalar($parametros['T007_Id']) && (string) $parametros['T007_Id'] === '0'
            && is_string($parametros['sql']) && $parametros['sql'] !== ''
            && base64_decode($parametros['sql'], true) !== false;
    }

    public static function avaliar($arquivo, $codigoSaida, array $saida, $modelo, $link, $nomeTela, $permitirAviso = true)
    {
        $falha = array('code' => false, 'aviso' => false);
        clearstatcache(true, $arquivo);
        // Preserva exatamente a verificacao de arquivo do commit de 26/10 nos demais fluxos.
        if (!is_file($arquivo) || filesize($arquivo) < 5 || file_get_contents($arquivo, false, null, 0, 5) !== '%PDF-') {
            return $falha;
        }
        $etiquetasFiltradas = $permitirAviso && self::ehImpressaoFiltradaDeEtiquetas($modelo, $link);
        if ($codigoSaida === 0) {
            return array('code' => !$etiquetasFiltradas || self::arquivoCompleto($arquivo), 'aviso' => false);
        }
        if (!$etiquetasFiltradas || $codigoSaida !== 1 || !self::arquivoCompleto($arquivo)) {
            return $falha;
        }
        $mensagens = implode("\n", $saida);
        if (!preg_match('/\bExit with code 1 due to network error: ContentOperationNotPermittedError\b/', $mensagens)) {
            return $falha;
        }
        preg_match_all('/\b[A-Za-z]+Error\b/', $mensagens, $erros);
        if (array_diff($erros[0], array('ContentOperationNotPermittedError'))) {
            return $falha;
        }
        // Uma recusa da página principal nunca pode ser tratada como aviso de recurso.
        if (preg_match_all('/(?:Failed loading page|Failed to load)\s+(https?:\/\/[^\s,]+)/i', $mensagens, $recursos)) {
            $caminhoPrincipal = parse_url($link, PHP_URL_PATH);
            foreach ($recursos[1] as $recurso) {
                $caminho = parse_url($recurso, PHP_URL_PATH);
                if (is_string($caminho) && substr($caminho, -strlen($caminhoPrincipal)) === $caminhoPrincipal) {
                    return $falha;
                }
            }
        }
        if (!is_string($nomeTela) || $nomeTela === '' || !self::relatorioLegivel($arquivo, 'Relatório - ' . $nomeTela)) {
            return $falha;
        }
        return array('code' => true, 'aviso' => true);
    }

    private static function arquivoCompleto($arquivo)
    {
        clearstatcache(true, $arquivo);
        if (!is_file($arquivo) || !is_readable($arquivo) || filesize($arquivo) < 20) {
            return false;
        }
        $handle = @fopen($arquivo, 'rb');
        if (!$handle) {
            return false;
        }
        $cabecalho = fread($handle, 8);
        fseek($handle, max(0, filesize($arquivo) - 2048));
        $final = stream_get_contents($handle, 2048);
        fclose($handle);
        return strpos($cabecalho, '%PDF-') === 0
            && preg_match('/startxref\s+(\d+)\s+%%EOF\s*\z/', $final, $xref) === 1
            && (float) $xref[1] > 0 && (float) $xref[1] < filesize($arquivo);
    }

    private static function relatorioLegivel($arquivo, $tituloEsperado)
    {
        $parser = null;
        // O parser legado usa avisos e error(); converta ambos em recusa controlada.
        set_error_handler(function ($nivel, $mensagem) {
            if ($nivel & (E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_RECOVERABLE_ERROR)) {
                throw new \RuntimeException('Estrutura do PDF inválida.');
            }
            return true;
        });
        try {
            if (!class_exists('fpdi_pdf_parser', false)) {
                require_once dirname(__DIR__) . '/PDFMerger/fpdi/fpdi_pdf_parser.php';
            }
            $erroParser = new ResultadoGeracaoPdfErroParser();
            $parser = new \fpdi_pdf_parser($arquivo, $erroParser);
            if ($parser->getPageCount() < 1 || !isset($parser->xref['trailer'][1]['/Info'])) {
                return false;
            }
            $info = $parser->pdf_resolve_object($parser->c, $parser->xref['trailer'][1]['/Info']);
            if (!isset($info[1][1]['/Title'])) {
                return false;
            }
            $titulo = self::textoPdf($info[1][1]['/Title']);
            return $titulo !== false && trim($titulo) === trim($tituloEsperado);
        } catch (\Exception $erro) {
            return false;
        } catch (\Throwable $erro) {
            return false;
        } finally {
            if ($parser !== null) {
                $parser->closeFile();
            }
            restore_error_handler();
        }
    }

    private static function textoPdf($valor)
    {
        if (!isset($valor[0], $valor[1])) {
            return false;
        }
        if ($valor[0] === PDF_TYPE_HEX) {
            $hex = preg_replace('/\s+/', '', $valor[1]);
            if ($hex === '' || !ctype_xdigit($hex)) {
                return false;
            }
            $texto = pack('H*', strlen($hex) % 2 ? $hex . '0' : $hex);
        } elseif ($valor[0] === PDF_TYPE_STRING) {
            $texto = preg_replace_callback('/\\\\([0-7]{1,3}|\r\n|[\r\n]|.)/s', function ($match) {
                $caractere = $match[1];
                if (preg_match('/\A[0-7]{1,3}\z/', $caractere)) {
                    return chr(octdec($caractere));
                }
                $escapes = array('n' => "\n", 'r' => "\r", 't' => "\t", 'b' => "\x08", 'f' => "\x0c");
                if ($caractere === "\r" || $caractere === "\n" || $caractere === "\r\n") {
                    return '';
                }
                return isset($escapes[$caractere]) ? $escapes[$caractere] : $caractere;
            }, $valor[1]);
        } else {
            return false;
        }
        if (substr($texto, 0, 2) === "\xfe\xff" || substr($texto, 0, 2) === "\xff\xfe") {
            if (!function_exists('iconv')) {
                return false;
            }
            return iconv(substr($texto, 0, 2) === "\xfe\xff" ? 'UTF-16BE' : 'UTF-16LE', 'UTF-8', substr($texto, 2));
        }
        return preg_match('//u', $texto) ? $texto : (function_exists('iconv') ? iconv('Windows-1252', 'UTF-8', $texto) : false);
    }
}

class ResultadoGeracaoPdfErroParser
{
    public $PDFVersion = '1.4';

    public function error($mensagem)
    {
        throw new \RuntimeException('Estrutura do PDF inválida.');
    }
}
