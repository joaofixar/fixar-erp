<?php
declare(strict_types=1);

/**
 * Read-only translation of the technical inventory and existing company flag
 * into business-facing preparation states. It deliberately creates no fiscal
 * rule, document, registration, calculation or persistent result.
 */
class ReformaTributariaPreparationOverview
{
    public static function forCompany(mysqli $db, int $companyId, array $technical): array
    {
        $foundationReady = self::foundationReady($technical);
        $automaticFilling = self::automaticFillingEnabled($db, $companyId);

        $sales = $foundationReady ? 'Em configuração' : 'Em desenvolvimento';
        $purchases = $foundationReady ? 'Em configuração' : 'Em desenvolvimento';
        $settings = $foundationReady ? 'Em configuração' : 'Em desenvolvimento';

        $nextSteps = [];
        if (!$foundationReady) {
            $nextSteps[] = 'Acompanhe a evolução dos recursos do Hardness antes de iniciar a configuração da empresa.';
        }
        if (!$automaticFilling) {
            $nextSteps[] = 'Revise as configurações fiscais da empresa com a sua equipe fiscal antes de utilizar o preenchimento nos documentos.';
        }
        $nextSteps[] = 'Use o validador da empresa quando estiver disponível para conferir os cadastros prioritários.';

        return [
            'general_status' => $foundationReady ? 'Em configuração' : 'Em desenvolvimento',
            'areas' => [
                ['name' => 'Vendas', 'status' => $sales, 'description' => 'Orçamentos, pedidos e NF-e serão preparados para refletir as configurações fiscais da empresa.'],
                ['name' => 'Compras', 'status' => $purchases, 'description' => 'Cotações, ordens de compra e notas de entrada terão a preparação acompanhada neste painel.'],
                ['name' => 'Configurações fiscais', 'status' => $settings, 'description' => 'Revise CFOP, NCM, CST, classificação tributária e créditos CBS/IBS com a área fiscal.'],
            ],
            'next_steps' => $nextSteps,
        ];
    }

    private static function foundationReady(array $technical): bool
    {
        foreach (array_merge($technical['tables'] ?? [], $technical['fields'] ?? []) as $item) {
            if (($item['status'] ?? '') !== 'present') return false;
        }
        return !empty($technical['tables']) && !empty($technical['fields']);
    }

    private static function automaticFillingEnabled(mysqli $db, int $companyId): bool
    {
        $statement = mysqli_prepare($db, "SELECT C004_Flag_Tipo_Preenchimento_Reforma_Tributaria FROM C004 WHERE C004_Id=? LIMIT 1");
        if (!$statement) return false;
        mysqli_stmt_bind_param($statement, 'i', $companyId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);
        return (string) ($row['C004_Flag_Tipo_Preenchimento_Reforma_Tributaria'] ?? '') === '1';
    }
}
