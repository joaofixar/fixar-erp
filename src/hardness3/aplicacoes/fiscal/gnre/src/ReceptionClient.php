<?php
declare(strict_types=1);
require_once __DIR__ . '/TlsPolicy.php';
require_once __DIR__ . '/EnvironmentResolver.php';

/** Recepção GNRE no ambiente imutável do snapshot. Não usa código de NF-e. */
final class GnreReceptionClient
{
    private const NS = 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao';
    private const ACTION = 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao/processar';
    /* Cadeia continua validada; hostname é exceção documentada do Portal. */

    /** @return array{kind:string,http:int,code:string,description:string,receipt:string,receipt_at:string,estimated_ms:string,soap:string,error:string} */
    public function process(array $context, string $loteXml, string $environment): array
    {
        $environment = GnreEnvironmentResolver::assertOperationalAllowed($environment);
        $service = GnreEnvironmentResolver::service($environment, 'reception');
        if (!function_exists('curl_init') || !function_exists('openssl_pkcs12_read') || !class_exists('DOMDocument')) {
            return $this->technical('Recursos PHP para transmissão GNRE não disponíveis.');
        }
        try {
            $cert = $this->companyCertificate((int) $context['company_id']);
            $pem = $this->pemFromPfx($cert['path'], $cert['password']);
        } catch (RuntimeException $e) {
            return $this->technical($e->getMessage());
        }
        try {
            return $this->send($this->envelope($loteXml), $pem, $service);
        } finally {
            @unlink($pem);
        }
    }

    private function companyCertificate(int $companyId): array
    {
        $db = gnre_connect();
        if (!$db) throw new RuntimeException('Base ERP indisponível para localizar o certificado A1.');
        try {
            $s = mysqli_prepare($db, "SELECT C004_Localizacao_Certificados,C004_Senha_Certificados FROM C004 WHERE C004_Id=? AND C004_Flag_Ativo='S' LIMIT 1");
            if (!$s) throw new RuntimeException('Não foi possível localizar o certificado A1 da empresa ativa.');
            mysqli_stmt_bind_param($s, 'i', $companyId); mysqli_stmt_execute($s);
            $r = mysqli_stmt_get_result($s); $row = $r ? mysqli_fetch_assoc($r) : null; mysqli_stmt_close($s);
        } finally { mysqli_close($db); }
        $path = is_array($row) ? rtrim((string) $row['C004_Localizacao_Certificados'], '/\\') . '/' . $companyId . '.pfx' : '';
        if ($path === '' || !is_readable($path)) throw new RuntimeException('Certificado A1 da empresa ativa não está disponível para transmissão GNRE.');
        return ['path' => $path, 'password' => (string) $row['C004_Senha_Certificados']];
    }

    private function pemFromPfx(string $path, string $password): string
    {
        $raw = @file_get_contents($path); $data = [];
        if ($raw === false || !openssl_pkcs12_read($raw, $data, $password) || empty($data['pkey']) || empty($data['cert'])) throw new RuntimeException('Não foi possível abrir o certificado A1 da empresa ativa.');
        $pem = tempnam(sys_get_temp_dir(), 'gnre-tls-');
        $chain = empty($data['extracerts']) ? '' : "\n" . implode("\n", $data['extracerts']);
        if ($pem === false || @file_put_contents($pem, $data['pkey'] . "\n" . $data['cert'] . $chain) === false || !@chmod($pem, 0600)) { if ($pem) @unlink($pem); throw new RuntimeException('Não foi possível preparar o certificado A1 para transmissão GNRE.'); }
        return $pem;
    }

    private function envelope(string $loteXml): string
    {
        $loteXml = (string) preg_replace('/^\s*<\?xml[^>]*\?>\s*/', '', $loteXml);
        return '<?xml version="1.0" encoding="UTF-8"?><soap12:Envelope xmlns:soap12="http://www.w3.org/2003/05/soap-envelope"><soap12:Header><gnreCabecMsg xmlns="' . self::NS . '"><versaoDados>2.00</versaoDados></gnreCabecMsg></soap12:Header><soap12:Body><gnreDadosMsg xmlns="' . self::NS . '">' . $loteXml . '</gnreDadosMsg></soap12:Body></soap12:Envelope>';
    }

    private function send(string $request, string $pem, array $service): array
    {
        $curl = curl_init($service['endpoint']);
        if ($curl === false) return $this->technical('Não foi possível iniciar a transmissão GNRE.');
        curl_setopt_array($curl, GnreTlsPolicy::curlOptions() + [CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>$request, CURLOPT_RETURNTRANSFER=>true, CURLOPT_CONNECTTIMEOUT=>15, CURLOPT_TIMEOUT=>45, CURLOPT_SSLCERT=>$pem, CURLOPT_SSLCERTTYPE=>'PEM', CURLOPT_HTTPHEADER=>['Content-Type: application/soap+xml; charset=utf-8; action="' . self::ACTION . '']]);
        $soap = curl_exec($curl); $error = curl_error($curl); $http = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE); curl_close($curl);
        if (!is_string($soap)) return $this->technical(GnreTlsPolicy::connectionFailure('transmissão', $error)->getMessage(), $http);
        if (strlen($soap) > 2 * 1024 * 1024) return $this->technical('Resposta SOAP excede o limite permitido.', $http, $soap);
        /* O serviço nacional pode devolver o TRetLote válido com HTTP 400/401
           enquanto o lote já foi recebido. O recibo, e não o status HTTP,
           é a evidência que define o acompanhamento. */
        if ($http < 200 || $http >= 300) {
            if ($http === 400 || $http === 401) return $this->parse($soap, $http);
            return $this->technical('HTTP ' . $http . ' sem confirmação de recibo' . ($error === '' ? '.' : ': ' . $error), $http, $soap);
        }
        return $this->parse($soap, $http);
    }

    private function parse(string $soap, int $http): array
    {
        $old = libxml_use_internal_errors(true); $dom = new DOMDocument(); $ok = $dom->loadXML($soap, LIBXML_NONET); libxml_clear_errors(); libxml_use_internal_errors($old);
        if (!$ok) return $this->technical('Resposta SOAP inválida; recibo não confirmado.', $http, $soap);
        $xp = new DOMXPath($dom);
        if ($xp->query('//*[local-name()="Fault"]')->length) return $this->technical('SOAP Fault sem confirmação de recibo.', $http, $soap);
        $ret = $xp->query('//*[local-name()="TRetLote_GNRE"]')->item(0);
        if (!$ret instanceof DOMElement) return $this->technical('Resposta SOAP sem retorno de recepção; recibo não confirmado.', $http, $soap);
        $v = static function (string $q) use ($xp, $ret): string { return trim((string) $xp->evaluate('string(' . $q . ')', $ret)); };
        $code = $v('./*[local-name()="situacaoRecepcao"]/*[local-name()="codigo"]'); $description = $v('./*[local-name()="situacaoRecepcao"]/*[local-name()="descricao"]'); $receipt = $v('./*[local-name()="recibo"]/*[local-name()="numero"]');
        if ($code === '100' && $receipt !== '') return ['kind'=>'received','http'=>$http,'code'=>$code,'description'=>$description,'receipt'=>$receipt,'receipt_at'=>$v('./*[local-name()="recibo"]/*[local-name()="dataHoraRecibo"]'),'estimated_ms'=>$v('./*[local-name()="recibo"]/*[local-name()="tempoEstimadoProc"]'),'soap'=>$soap,'error'=>''];
        if ($code !== '100' && preg_match('/^[1-9][0-9]{2}$/', $code) && $description !== '' && $receipt === '') return $this->rejected($http, $code, $description, $soap);
        return $this->technical('Retorno de recepção sem recibo e sem rejeição confirmada.', $http, $soap);
    }
    private function rejected(int $http, string $code, string $description, string $soap): array { return ['kind'=>'rejected','http'=>$http,'code'=>$code,'description'=>$description,'receipt'=>'','receipt_at'=>'','estimated_ms'=>'','soap'=>$soap,'error'=>'']; }
    private function technical(string $error, int $http = 0, string $soap = ''): array { return ['kind'=>'inconclusive','http'=>$http,'code'=>'','description'=>'','receipt'=>'','receipt_at'=>'','estimated_ms'=>'','soap'=>$soap,'error'=>$error]; }
}
