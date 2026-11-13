<?php
declare(strict_types=1);

/** Política TLS comum aos clientes HTTPS da GNRE. */
final class GnreTlsPolicy
{
    public const OPERATIONAL_ERROR = 'Não foi possível conectar com o Portal GNRE com segurança. Tente novamente mais tarde ou contate o suporte.';

    /** @return array<int, mixed> */
    public static function curlOptions(): array
    {
        return [
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];
    }

    /** Registra o diagnóstico sem devolvê-lo à interface. */
    public static function connectionFailure(string $operation, string $detail = ''): RuntimeException
    {
        error_log('GNRE HTTPS ' . $operation . ' failed' . ($detail === '' ? '' : ': ' . $detail));
        return new RuntimeException(self::OPERATIONAL_ERROR);
    }
}
