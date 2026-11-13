<?php
declare(strict_types=1);

/** Fonte única, fechada e local dos ambientes e contratos SOAP GNRE. */
final class GnreEnvironmentResolver
{
    public const PRODUCTION = '1';
    public const HOMOLOGATION = '2';

    private const ENVIRONMENTS = [
        self::PRODUCTION => ['label' => 'Produção', 'services' => [
            'config_uf' => ['endpoint' => 'https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF', 'namespace' => 'http://www.gnre.pe.gov.br/webservice/GnreConfigUF', 'action' => 'http://www.gnre.pe.gov.br/webservice/GnreConfigUF/consultar', 'version' => '1.00'],
            'reception' => ['endpoint' => 'https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao', 'namespace' => 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao', 'action' => 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao/processar', 'version' => '2.00'],
            'result' => ['endpoint' => 'https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote', 'namespace' => 'http://www.gnre.pe.gov.br/webservice/GnreResultadoLote', 'action' => 'http://www.gnre.pe.gov.br/webservice/GnreResultadoLote/consultar', 'version' => '2.00'],
        ]],
        self::HOMOLOGATION => ['label' => 'Homologação', 'services' => [
            'config_uf' => ['endpoint' => 'https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF', 'namespace' => 'http://www.gnre.pe.gov.br/webservice/GnreConfigUF', 'action' => 'http://www.gnre.pe.gov.br/webservice/GnreConfigUF/consultar', 'version' => '1.00'],
            'reception' => ['endpoint' => 'https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao', 'namespace' => 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao', 'action' => 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao/processar', 'version' => '2.00'],
            'result' => ['endpoint' => 'https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote', 'namespace' => 'http://www.gnre.pe.gov.br/webservice/GnreResultadoLote', 'action' => 'http://www.gnre.pe.gov.br/webservice/GnreResultadoLote/consultar', 'version' => '2.00'],
        ]],
    ];

    public static function validate(string $environment): string
    {
        if (!array_key_exists($environment, self::ENVIRONMENTS)) throw new RuntimeException('Ambiente GNRE inválido. Informe Produção ou Homologação.');
        return $environment;
    }

    public static function label(string $environment): string
    {
        $environment = self::validate($environment);
        return self::ENVIRONMENTS[$environment]['label'];
    }

    /** @return array{endpoint:string,namespace:string,action:string,version:string} */
    public static function service(string $environment, string $service): array
    {
        $environment = self::validate($environment);
        if (!isset(self::ENVIRONMENTS[$environment]['services'][$service])) throw new RuntimeException('Serviço GNRE inválido para resolução de endpoint.');
        return self::ENVIRONMENTS[$environment]['services'][$service];
    }

    /**
     * Validates the immutable environment selected by GNRE007 before an
     * operational client is used. Endpoints remain exclusively allowlisted.
     */
    public static function assertOperationalAllowed(string $environment): string
    {
        return self::validate($environment);
    }
}
