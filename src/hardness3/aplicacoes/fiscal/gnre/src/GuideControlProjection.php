<?php
declare(strict_types=1);

require_once __DIR__ . '/ResultInterpreter.php';

/**
 * Read model do número oficial da guia. GNRE005 permanece a evidência SOAP;
 * esta classe somente aceita uma autorização individual inequívoca.
 */
final class GnreGuideControlProjection
{
    public static function fromOfficialSoap(string $soap): ?string
    {
        $result = GnreResultInterpreter::interpret($soap);
        $guides = (array)($result['guides'] ?? []);
        if (empty($result['valid']) || count($guides) !== 1) return null;
        $guide = $guides[0];
        if ((string)($guide['code'] ?? '') !== '0') return null;
        $control = trim((string)($guide['control_number'] ?? ''));
        return preg_match('/^[0-9]{1,20}$/D', $control) ? $control : null;
    }

    /** Compatibilidade enquanto o SQL manual desta task não foi aplicado. */
    public static function available(mysqli $db): bool
    {
        $result = mysqli_query($db, "SHOW COLUMNS FROM GNRE001 LIKE 'GNRE001_Numero_Controle'");
        return (bool)($result && mysqli_fetch_row($result));
    }
}
