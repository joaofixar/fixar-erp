<?php
namespace hardness;

/** Escolha de impressão; não modifica o XML de uma NF-e autorizada. */
class DanfeModelo
{
    const ATUAL = 'A';
    const REVISAO_LAYOUT_RTC = 'NT2026.010-modelos-2';
    const RTC_VERTICAL = 'V';
    const RTC_HORIZONTAL = 'H';

    public static function opcoes($selecionado = self::RTC_VERTICAL)
    {
        return array(
            array('value' => self::RTC_VERTICAL, 'title' => 'Retrato', 'selected' => $selecionado !== self::RTC_HORIZONTAL),
            array('value' => self::RTC_HORIZONTAL, 'title' => 'Paisagem', 'selected' => $selecionado === self::RTC_HORIZONTAL),
        );
    }

    public static function valido($tipo, $herdar = false)
    {
        return in_array($tipo, $herdar ? array('', 'A', 'V', 'H') : array('A', 'V', 'H'), true);
    }

    public static function ambienteLocal()
    {
        $host = strtolower(trim((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '')));
        if ($host === '::1') { return true; }
        $host = trim(preg_replace('/:\d+$/', '', $host), '[]');
        return in_array($host, array('localhost', '127.0.0.1', '::1'), true);
    }

    private static function pastasPersonalizadas()
    {
        global $confUsuario;
        $raiz = rtrim($confUsuario['pathRaiz'] ?? dirname(__DIR__, 2), '/\\') . '/';
        $pastas = array();
        $usuario = (string) ($confUsuario['dbDatabase'] ?? '');
        if ($usuario !== '' && preg_match('/^[a-zA-Z0-9_-]+$/', $usuario)) {
            $pastas[] = $raiz . 'dados_usuarios/' . $usuario . '/danfe_rtc';
            $pastas[] = $raiz . 'dados_usuarios/' . $usuario;
        }
        $pastas[] = $raiz . 'dados_usuarios/danfe_rtc';
        return $pastas;
    }

    private static function padraoLocal()
    {
        if (!self::ambienteLocal()) { return ''; }
        foreach (self::pastasPersonalizadas() as $pasta) {
            $arquivo = $pasta . '/config_danfe_rtc.php';
            if (!is_file($arquivo)) { continue; }
            $config = require $arquivo;
            if (is_array($config) && !empty($config['ativo']) && self::valido($config['tipoPadrao'] ?? '')) {
                return $config['tipoPadrao'];
            }
        }
        return '';
    }

    public static function resolver(array $nota)
    {
        if ((string) ($nota['T007_Flag_ACP'] ?? '') !== '6' || (string) ($nota['T007_Versao_NFe'] ?? $nota['T007_Versao_Nfe'] ?? '') !== '4.00') {
            return self::ATUAL;
        }
        $usarRTC = ($nota['C004_Flag_Usar_DANFE_RTC'] ?? 'N') === 'S';
        $tipoNota = (string) ($nota['T007A_Tipo_DANFE'] ?? '');
        if ($tipoNota === self::ATUAL) { return self::ATUAL; }
        if ($usarRTC) {
            if (in_array($tipoNota, array(self::RTC_VERTICAL, self::RTC_HORIZONTAL), true)) {
                return $tipoNota;
            }
            $tipoEmpresa = (string) ($nota['C004_Tipo_DANFE'] ?? self::RTC_VERTICAL);
            if (in_array($tipoEmpresa, array(self::RTC_VERTICAL, self::RTC_HORIZONTAL), true)) {
                return $tipoEmpresa;
            }
        }
        // No localhost, a pasta de personalização permite experimentar o RTC
        // sem ativá-lo para a empresa.
        return self::padraoLocal() ?: self::ATUAL;
    }

    public static function criarRTC(array $nota)
    {
        $tipo = self::resolver($nota);
        if ($tipo === self::ATUAL) {
            return null;
        }
        require_once __DIR__ . '/danfe_rt400.php';
        $classe = '\\hardness\\danfe_rt400';
        foreach (self::pastasPersonalizadas() as $pasta) {
            $arquivo = $pasta . '/danfe_rt400.php';
            if (!is_file($arquivo)) { continue; }
            $personalizada = require $arquivo;
            if (!is_string($personalizada) || !is_a($personalizada, '\\hardness\\danfe_rt400', true)) {
                throw new \RuntimeException('A personalização do DANFE RTC deve retornar uma classe compatível com danfe_rt400.');
            }
            $classe = $personalizada;
            break;
        }
        $danfe = new $classe($tipo === self::RTC_HORIZONTAL ? 'L' : 'P');
        $danfe->cancelada = ($nota['T007_Flag_Cancelada'] ?? '') === 'S';
        $danfe->casas_decimais_preco = (int) ($nota['T007_Casas_Decimais_Preco'] ?? 4);
        $danfe->casas_decimais_quantidade = (int) ($nota['T007_Casas_Decimais_Quantidade'] ?? 4);
        return $danfe;
    }

    public static function cacheValido($arquivo, $tipo)
    {
        if (!is_file($arquivo) || filesize($arquivo) === 0) { return false; }
        $modelo = is_file($arquivo . '.modelo') ? trim(file_get_contents($arquivo . '.modelo')) : self::ATUAL;
        return $modelo === $tipo && ($tipo === self::ATUAL || (is_file($arquivo . '.layout-rtc') && trim(file_get_contents($arquivo . '.layout-rtc')) === self::REVISAO_LAYOUT_RTC));
    }

    public static function prepararGeracaoAtual($arquivo)
    {
        // Se o conversor legado falhar, um PDF RTC anterior não pode virar cache do modelo atual.
        if (is_file($arquivo . '.modelo') && trim(file_get_contents($arquivo . '.modelo')) !== self::ATUAL) {
            if (is_file($arquivo) && !unlink($arquivo)) {
                throw new \RuntimeException('Não foi possível substituir o DANFE do modelo anterior.');
            }
            if (is_file($arquivo . '.layout-rtc')) { unlink($arquivo . '.layout-rtc'); }
            if (!unlink($arquivo . '.modelo')) {
                throw new \RuntimeException('Não foi possível atualizar o modelo do DANFE.');
            }
        }
    }

    public static function gravarRTC(array $nota, $xml, $logo, $arquivo)
    {
        $danfe = self::criarRTC($nota);
        if (!$danfe) { return false; }
        $danfe->xml = file_get_contents($xml);
        $danfe->logomarca = $logo;
        $conteudo = $danfe->gerarPDF();
        if (!is_dir(dirname($arquivo)) && !mkdir(dirname($arquivo), 0777, true)) {
            throw new \RuntimeException('Não foi possível criar a pasta do DANFE.');
        }
        $temporario = tempnam(dirname($arquivo), 'rtc-');
        if ($temporario === false) { throw new \RuntimeException('Não foi possível criar o PDF do DANFE RTC.'); }
        try {
            // tempnam cria 0600; mantenha as permissões do arquivo ou o umask do gerador legado.
            $permissoes = is_file($arquivo) ? (fileperms($arquivo) & 0777) : (0666 & ~umask());
            if (file_put_contents($temporario, $conteudo) !== strlen($conteudo) || !chmod($temporario, $permissoes) || !rename($temporario, $arquivo)) {
                throw new \RuntimeException('Não foi possível gravar o PDF do DANFE RTC.');
            }
            if (file_put_contents($arquivo . '.layout-rtc', self::REVISAO_LAYOUT_RTC) === false || file_put_contents($arquivo . '.modelo', self::resolver($nota)) === false) {
                throw new \RuntimeException('Não foi possível registrar o modelo do DANFE RTC.');
            }
        } finally {
            if (is_file($temporario)) { unlink($temporario); }
        }
        return true;
    }

    /** Altera somente a preferência de impressão, inclusive em notas autorizadas. */
    public static function salvarPorNota($id, $empresa, $tipo)
    {
        if (!in_array($tipo, array(self::RTC_VERTICAL, self::RTC_HORIZONTAL), true) || (int) $id <= 0 || (int) $empresa <= 0) {
            return 'Tipo de DANFE ou nota inválida.';
        }
        $id = (int) $id;
        $empresa = (int) $empresa;
        $sql = mysql_query("SELECT T007_Id FROM T007 WHERE T007_Id = '{$id}' AND T007_C004_Id = '{$empresa}' AND T007_Flag_ACP = '6' AND T007_Versao_NFe = '4.00' LIMIT 1");
        if (!$sql || !mysql_fetch_assoc($sql)) {
            return 'NF-e 4.00 não encontrada na empresa atual.';
        }
        $sql = mysql_query("INSERT INTO T007A (T007A_T007_Id, T007A_Tipo_DANFE) VALUES ('{$id}', '{$tipo}') ON DUPLICATE KEY UPDATE T007A_Tipo_DANFE = '{$tipo}'");
        return $sql ? true : 'Não foi possível salvar o tipo de DANFE. Verifique a atualização da base.';
    }
}
