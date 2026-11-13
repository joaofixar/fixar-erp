<?php
declare(strict_types=1);

/**
 * Consulta isolada da configuração da empresa para a Reforma Tributária.
 * Não utiliza Empresa_Atual e não executa nenhuma alteração.
 */
class ReformaTributariaCompanyConfiguration
{
    public static function listForUser(mysqli $db, int $userId): array
    {
        $root = $userId === 63;
        $sql = "SELECT DISTINCT C004.C004_Id AS company_id, C004.C004_Nome_Empresa AS company_name,
                       C004.C004_Nome_Abreviado AS short_name,
                       C004.C004_Cnpj AS cnpj,
                       C004.C004_Codigo_Regime_Tributario AS tax_regime_code,
                       C004.C004_Flag_Tipo_Preenchimento_Reforma_Tributaria AS filling_type
                  FROM C004" . ($root ? '' : ' INNER JOIN C019 ON C004.C004_Id=C019.C019_C004_Id') . "
                 WHERE C004.C004_Flag_Ativo='S'" . ($root ? '' : ' AND C019.C019_C007_Id=?') . "
                 ORDER BY C004.C004_Ordem_Empresa, C004.C004_Id";
        $statement = mysqli_prepare($db, $sql);
        if (!$statement) return [];
        if (!$root) mysqli_stmt_bind_param($statement, 'i', $userId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $companies = [];
        while ($result && ($row = mysqli_fetch_assoc($result))) {
            $row['status'] = self::status($row['filling_type']);
            $row['tax_regime'] = self::taxRegime($row['tax_regime_code'] ?? '');
            $companies[] = $row;
        }
        mysqli_stmt_close($statement);
        return $companies;
    }

    public static function findForUser(mysqli $db, int $userId, int $companyId): ?array
    {
        $root = $userId === 63;
        $sql = "SELECT C004.C004_Id AS company_id,
                       C004.C004_Nome_Empresa AS company_name,
                       C004.C004_Nome_Abreviado AS short_name,
                       C004.C004_Cnpj AS cnpj,
                       C004.C004_Codigo_Regime_Tributario AS tax_regime_code,
                       C004.C004_Flag_Tipo_Preenchimento_Reforma_Tributaria AS filling_type
                  FROM C004" . ($root ? '' : ' INNER JOIN C019 ON C004.C004_Id=C019.C019_C004_Id') . "
                 WHERE C004.C004_Flag_Ativo='S'" . ($root ? '' : ' AND C019.C019_C007_Id=?') . "
                   AND C004.C004_Id=?
                 LIMIT 1";
        $statement = mysqli_prepare($db, $sql);
        if (!$statement) {
            return null;
        }

        if ($root) mysqli_stmt_bind_param($statement, 'i', $companyId);
        else mysqli_stmt_bind_param($statement, 'ii', $userId, $companyId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        if ($row) {
            $row['status'] = self::status($row['filling_type']);
            $row['tax_regime'] = self::taxRegime($row['tax_regime_code'] ?? '');
        }
        return $row ?: null;
    }

    public static function status($value): array
    {
        if ((string) $value === '1') {
            return [
                'title' => 'Preenchimento automático ativado',
                'description' => 'O preenchimento automático das informações da Reforma Tributária está ativado para esta empresa.',
                'class' => 'status--active',
            ];
        }
        if ((string) $value === '0') {
            return [
                'title' => 'Manual/desativado',
                'description' => 'O preenchimento automático das informações da Reforma Tributária está desativado para esta empresa. O preenchimento é manual.',
                'class' => 'status--inactive',
            ];
        }
        return [
            'title' => 'Preenchimento automático não configurado',
            'description' => 'Esta empresa ainda não possui uma definição para o preenchimento automático da Reforma Tributária.',
            'class' => 'status--unconfigured',
        ];
    }

    private static function taxRegime($value): string
    {
        switch ((string) $value) {
            case '1': return 'Simples Nacional';
            case '2': return 'Simples Nacional — excesso do sublimite';
            case '3': return 'Regime Normal';
            default: return 'Não informado';
        }
    }
}
