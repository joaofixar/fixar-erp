<?php
declare(strict_types=1);

/** Ambiente pretendido pela empresa, aplicado somente ao iniciar nova guia. */
final class GnreEnvironmentPreference
{
    public const HOMOLOGATION = '2';
    public const PRODUCTION = '1';

    public static function validate(string $environment): string
    {
        if (!in_array($environment, [self::HOMOLOGATION, self::PRODUCTION], true)) {
            throw new RuntimeException('Ambiente GNRE inválido. Selecione Homologação ou Produção.');
        }
        return $environment;
    }

    /** Sem registro, a única escolha segura é homologação. */
    public function read(mysqli $db, int $companyId): string
    {
        if ($companyId <= 0) throw new InvalidArgumentException('Empresa GNRE inválida.');
        $statement = mysqli_prepare($db, 'SELECT GNRE009_Ambiente FROM GNRE009 WHERE GNRE009_C004_Id=? LIMIT 1');
        if (!$statement) throw new RuntimeException('Não foi possível ler a preferência de ambiente GNRE.');
        mysqli_stmt_bind_param($statement, 'i', $companyId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);
        $environment = (string) ($row['GNRE009_Ambiente'] ?? self::HOMOLOGATION);
        return in_array($environment, [self::HOMOLOGATION, self::PRODUCTION], true) ? $environment : self::HOMOLOGATION;
    }

    /** A chave única assegura uma única preferência por empresa. */
    public function save(mysqli $db, int $companyId, string $environment): void
    {
        if ($companyId <= 0) throw new InvalidArgumentException('Empresa GNRE inválida.');
        $environment = self::validate($environment);
        $statement = mysqli_prepare($db, 'INSERT INTO GNRE009 (GNRE009_C004_Id,GNRE009_Ambiente) VALUES (?,?) ON DUPLICATE KEY UPDATE GNRE009_Ambiente=VALUES(GNRE009_Ambiente)');
        if (!$statement) throw new RuntimeException('Não foi possível preparar a preferência de ambiente GNRE.');
        mysqli_stmt_bind_param($statement, 'is', $companyId, $environment);
        $ok = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
        if (!$ok) throw new RuntimeException('Não foi possível salvar a preferência de ambiente GNRE.');
    }
}
