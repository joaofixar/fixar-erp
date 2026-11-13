<?php
declare(strict_types=1);
require_once __DIR__ . '/ConfigUfSnapshot.php';
require_once __DIR__ . '/EnvironmentResolver.php';
require_once __DIR__ . '/TlsPolicy.php';

/** Cliente SOAP exclusivo da GNRE. Não reutiliza código ou contratos de NF-e. */
final class GnreConfigUfConsultException extends RuntimeException
{
    public const CERTIFICATE_UNAVAILABLE = 'certificate_unavailable';
    public const PORTAL_ACCESS_DENIED = 'portal_access_denied';
    public const INCONCLUSIVE = 'inconclusive';

    private $publicCategory;

    public function __construct(string $publicCategory, string $message)
    {
        parent::__construct($message);
        $this->publicCategory = $publicCategory;
    }

    public function publicCategory(): string { return $this->publicCategory; }
}

final class GnreConfigUfClient
{
    private const SOAP_ACTION = 'http://www.gnre.pe.gov.br/webservice/GnreConfigUF/consultar';

    public function consult(array $context, string $uf, ?string $revenue, bool $types, string $environment): array
    {
        $environment = GnreEnvironmentResolver::assertOperationalAllowed($environment);
        $service = GnreEnvironmentResolver::service($environment, 'config_uf');
        if (!preg_match('/^[A-Z]{2}$/', $uf)) {
            throw new RuntimeException('UF inválida para consulta GNRE.');
        }
        if ($revenue !== null && !preg_match('/^\d{6}$/', $revenue)) {
            throw new RuntimeException('Receita inválida para consulta GNRE.');
        }
        if (!function_exists('curl_init') || !function_exists('openssl_pkcs12_read')) {
            throw new RuntimeException('O servidor não possui os recursos necessários para a consulta GNRE.');
        }

        $certificate = $this->companyCertificate((int) $context['company_id']);
        $pem = $this->pemFromPfx($certificate['path'], $certificate['password']);
        try {
            $response = $this->send($this->requestXml($uf, $revenue, $types, $environment, $service), $pem, $service);
        } finally {
            @unlink($pem);
        }
        return $this->parseResponse($response, $environment);
    }

    private function companyCertificate(int $companyId): array
    {
        $db = gnre_connect();
        if (!$db) {
            throw new RuntimeException('Base ERP indisponível para localizar o certificado da empresa.');
        }
        try {
            $statement = mysqli_prepare($db, 'SELECT C004_Localizacao_Certificados, C004_Senha_Certificados FROM C004 WHERE C004_Id=? AND C004_Flag_Ativo=\'S\' LIMIT 1');
            if (!$statement) throw new RuntimeException('Não foi possível localizar o certificado da empresa.');
            mysqli_stmt_bind_param($statement, 'i', $companyId);
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            $row = $result ? mysqli_fetch_assoc($result) : null;
            mysqli_stmt_close($statement);
        } finally {
            mysqli_close($db);
        }
        $path = is_array($row) ? rtrim((string) $row['C004_Localizacao_Certificados'], '/\\') . '/' . $companyId . '.pfx' : '';
        if ($path === '' || !is_readable($path)) {
            throw new GnreConfigUfConsultException(GnreConfigUfConsultException::CERTIFICATE_UNAVAILABLE, 'Certificado A1 da empresa ativa não está disponível para a consulta GNRE.');
        }
        return ['path' => $path, 'password' => (string) $row['C004_Senha_Certificados']];
    }

    private function pemFromPfx(string $path, string $password): string
    {
        $contents = @file_get_contents($path);
        $certificate = [];
        if ($contents === false || !openssl_pkcs12_read($contents, $certificate, $password) || empty($certificate['pkey']) || empty($certificate['cert'])) {
            throw new GnreConfigUfConsultException(GnreConfigUfConsultException::CERTIFICATE_UNAVAILABLE, 'Não foi possível abrir o certificado A1 da empresa ativa para a consulta GNRE.');
        }
        $pem = tempnam(sys_get_temp_dir(), 'gnre-tls-');
        if ($pem === false) throw new RuntimeException('Não foi possível preparar o certificado para a consulta GNRE.');
        $chain = !empty($certificate['extracerts']) ? "\n" . implode("\n", $certificate['extracerts']) : '';
        if (@file_put_contents($pem, $certificate['pkey'] . "\n" . $certificate['cert'] . $chain) === false || !@chmod($pem, 0600)) {
            @unlink($pem);
            throw new RuntimeException('Não foi possível preparar o certificado para a consulta GNRE.');
        }
        return $pem;
    }

    private function requestXml(string $uf, ?string $revenue, bool $types, string $environment, array $service): string
    {
        $data = '<TConsultaConfigUf xmlns="http://www.gnre.pe.gov.br"><ambiente>' . $environment . '</ambiente><uf>' . $uf . '</uf>';
        if ($revenue !== null) $data .= '<receita>' . $revenue . '</receita>';
        if ($types) $data .= '<tiposGnre>S</tiposGnre>';
        $data .= '</TConsultaConfigUf>';
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<soap12:Envelope xmlns:soap12="http://www.w3.org/2003/05/soap-envelope"><soap12:Header>'
            . '<gnreCabecMsg xmlns="' . $service['namespace'] . '"><versaoDados>' . $service['version'] . '</versaoDados></gnreCabecMsg>'
            . '</soap12:Header><soap12:Body><gnreDadosMsg xmlns="' . $service['namespace'] . '">' . $data
            . '</gnreDadosMsg></soap12:Body></soap12:Envelope>';
    }

    private function send(string $request, string $pem, array $service): string
    {
        $curl = curl_init($service['endpoint']);
        if ($curl === false) throw new RuntimeException('Não foi possível iniciar a consulta GNRE.');
        curl_setopt_array($curl, GnreTlsPolicy::curlOptions() + [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => $request, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15, CURLOPT_TIMEOUT => 45, CURLOPT_SSLCERT => $pem,
            CURLOPT_SSLCERTTYPE => 'PEM',
            CURLOPT_HTTPHEADER => ['Content-Type: application/soap+xml; charset=utf-8; action="' . self::SOAP_ACTION . ''],
        ]);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (is_string($response) && strlen($response) > 2 * 1024 * 1024) {
            throw new GnreConfigUfConsultException(GnreConfigUfConsultException::INCONCLUSIVE, 'A resposta da consulta de configuração excedeu o limite permitido.');
        }
        if (!is_string($response) || $status < 200 || $status >= 300) {
            if (is_string($response)) throw $this->nonSuccessResponseException($status,$response);
            throw new GnreConfigUfConsultException(GnreConfigUfConsultException::INCONCLUSIVE, 'Não foi possível receber uma resposta utilizável da consulta de configuração.');
        }
        return $response;
    }

    private function parseResponse(string $response, string $environment): array
    {
        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument();
        $loaded = $document->loadXML($response, LIBXML_NONET);
        libxml_clear_errors(); libxml_use_internal_errors($previous);
        if (!$loaded) throw new RuntimeException('O Portal GNRE retornou uma resposta SOAP inválida.');
        $reason=$this->soapFaultReason($response);
        if ($reason!==null) $this->throwForSoapFault($reason);
        $xpath = new DOMXPath($document);
        $config = $xpath->query('//*[local-name()="TConfigUf"]')->item(0);
        if (!$config instanceof DOMElement) throw new RuntimeException('O Portal GNRE não retornou a configuração da UF.');
        $value = static function (string $name) use ($xpath, $config): string {
            $node = $xpath->query('./*[local-name()="' . $name . '"]', $config)->item(0);
            return $node ? trim($node->textContent) : '';
        };
        $formatted = $document->saveXML($config);
        $result=['uf' => $value('uf'), 'status_code' => $xpath->evaluate('string(./*[local-name()="situacaoConsulta"]/*[local-name()="codigo"])', $config),
            'status_description' => $xpath->evaluate('string(./*[local-name()="situacaoConsulta"]/*[local-name()="descricao"])', $config),
            'xml' => is_string($formatted) ? $formatted : ''];
        /* Só uma TConfigUf válida no XSD oficial pode seguir para o snapshot. */
        $result['rules'] = GnreConfigUfSnapshot::validate($result['xml'], $environment);
        return $result;
    }

    /** Only a direct authorization refusal carried by a valid Portal SOAP fault is public evidence. */
    private function isExplicitAccessDenied(string $reason): bool
    {
        return (bool) preg_match('/(?:acesso\s+(?:ao\s+)?(?:web\s*service\s+)?(?:negado|recusado|n[aã]o\s+autorizado)|web\s*service\s+(?:n[aã]o\s+autorizado|n[aã]o\s+habilitado)|(?:credenciamento|habilita[cç][aã]o)\s+(?:negad[oa]|recusad[oa]))/iu', $reason);
    }

    /** Non-2xx is never evidence by itself; only a valid SOAP Fault can refine it. */
    private function nonSuccessResponseException(int $status,string $response): GnreConfigUfConsultException
    {
        $reason=$this->soapFaultReason($response);
        if ($reason!==null&&$this->isExplicitAccessDenied($reason)) {
            return new GnreConfigUfConsultException(GnreConfigUfConsultException::PORTAL_ACCESS_DENIED, 'O Portal GNRE recusou o acesso ao Web Service para a consulta de configuração.');
        }
        return new GnreConfigUfConsultException(GnreConfigUfConsultException::INCONCLUSIVE, 'A resposta não permitiu classificar a consulta de configuração com segurança.');
    }

    private function throwForSoapFault(string $reason): void
    {
        if ($this->isExplicitAccessDenied($reason)) {
            throw new GnreConfigUfConsultException(GnreConfigUfConsultException::PORTAL_ACCESS_DENIED, 'O Portal GNRE recusou o acesso ao Web Service para a consulta de configuração.');
        }
        throw new GnreConfigUfConsultException(GnreConfigUfConsultException::INCONCLUSIVE, 'O Portal GNRE retornou uma falha SOAP sem classificação pública segura.');
    }

    /** Returns a reason only for a well-formed SOAP Envelope containing a Body/Fault. */
    private function soapFaultReason(string $response): ?string
    {
        if($response==='') return null;
        $previous=libxml_use_internal_errors(true);$document=new DOMDocument();$loaded=$document->loadXML($response,LIBXML_NONET);libxml_clear_errors();libxml_use_internal_errors($previous);
        $root=$document->documentElement;
        if(!$loaded||!$root||$root->localName!=='Envelope') return null;
        $xpath=new DOMXPath($document);$fault=$xpath->query('/*[local-name()="Envelope"]/*[local-name()="Body"]/*[local-name()="Fault"]')->item(0);
        return $fault instanceof DOMElement?trim($fault->textContent):null;
    }
}
