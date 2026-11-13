<?php
declare(strict_types=1);

/** Read-only inventory. It deliberately does not execute business routines. */
class ReformaTributariaTechnicalDiagnostic
{
    public static function run(mysqli $db): array
    {
        $requirements = self::requirements();
        try {
            $tables = self::tableNames($db);
            $columns = self::columns($db);
            $routines = self::routines($db);
        } catch (Throwable $e) {
            return self::unverified($requirements, $e->getMessage());
        }

        $tableResults = [];
        foreach ($requirements['tables'] as $table => $label) {
            $tableResults[] = self::result($label, isset($tables[$table]), 'Tabela ' . $table);
        }

        $fieldResults = [];
        foreach ($requirements['fields'] as $table => $fields) {
            foreach ($fields as $field) {
                $fieldResults[] = self::result($table . '.' . $field, isset($columns[$table][$field]), 'Campo necessário ao diagnóstico da Reforma');
            }
        }

        $routineResults = [];
        foreach ($requirements['routines'] as $routine => $type) {
            $present = isset($routines[$routine]) && $routines[$routine] === $type;
            $routineResults[] = [
                'name' => $routine,
                'detail' => $type === 'PROCEDURE' ? 'Procedure de totalização' : 'Function de cálculo',
                'status' => $present ? 'present' : 'absent',
                'version' => $present ? 'unverified' : 'not_applicable',
            ];
        }

        return [
            'tables' => $tableResults,
            'fields' => $fieldResults,
            'routines' => $routineResults,
            'checked_at' => date('d/m/Y H:i:s'),
            'read_only' => true,
        ];
    }

    private static function requirements(): array
    {
        $tables = [
            'T001A' => 'Histórico do produto', 'T003A' => 'Orçamento (cabeçalho)', 'T004A' => 'Orçamento (item)',
            'T005A' => 'Pedido (cabeçalho)', 'T006A' => 'Pedido (item)', 'T007A' => 'NF-e saída (cabeçalho)',
            'T008A' => 'NF-e saída (item)', 'T013A' => 'NF-e entrada (cabeçalho)', 'T014A' => 'NF-e entrada (item)',
            'T218A' => 'Cotação (cabeçalho)', 'T219A' => 'Cotação (item)', 'T224A' => 'Ordem de compra (cabeçalho)',
            'T225A' => 'Ordem de compra (item)',
        ];
        $fields = [
            'C004' => ['C004_Flag_Tipo_Preenchimento_Reforma_Tributaria'],
            'D006' => ['D006_Situacao_Tributaria_CBS_IBS', 'D006_Classificacao_Tributaria_CBS_IBS', 'D006_Flag_Deb_Crd_CBS', 'D006_Flag_Deb_Crd_IBS'],
            'T008A' => ['T008A_Situacao_Tributaria_CBS_IBS', 'T008A_Classificacao_Tributaria_CBS_IBS', 'T008A_Valor_Base_CBS_IBS', 'T008A_Valor_CBS', 'T008A_Valor_IBS'],
            'T014A' => ['T014A_Situacao_Tributaria_CBS_IBS', 'T014A_Classificacao_Tributaria_CBS_IBS', 'T014A_Valor_Base_CBS_IBS', 'T014A_Valor_CBS', 'T014A_Valor_IBS'],
            'T001A' => ['T001A_Valor_CBS', 'T001A_Valor_IBS'],
        ];
        foreach (['T004A', 'T006A', 'T219A', 'T225A'] as $table) {
            $fields[$table] = [
                $table . '_Situacao_Tributaria_CBS_IBS', $table . '_Classificacao_Tributaria_CBS_IBS',
                $table . '_Valor_Base_CBS_IBS', $table . '_Valor_CBS', $table . '_Valor_IBS',
            ];
        }

        $routines = [];
        foreach (['T003', 'T004', 'T005', 'T006', 'T007', 'T008'] as $table) {
            $routines[$table . '_Gravar_Totalizacao_4_Reforma'] = 'PROCEDURE';
        }
        foreach (['T218', 'T219', 'T224', 'T225'] as $table) {
            $routines[$table . '_Gravar_Totalizacao' . (in_array($table, ['T218', 'T224'], true) ? '_3' : '') . '_Reforma'] = 'PROCEDURE';
        }
        $routines['T084_Gravar_Totalizacao_Reforma'] = 'PROCEDURE';
        foreach (['T004A', 'T006A', 'T008A', 'T084A', 'T219A', 'T225A'] as $item) {
            foreach (['Aliquota_CBS_Efetiva', 'Aliquota_IBS_UF_Efetiva', 'Aliquota_IBS_Municipal_Efetiva', 'Valor_Base_CBS_IBS', 'Valor_CBS', 'Valor_Diferimento_CBS', 'Valor_Diferimento_IBS_UF', 'Valor_Diferimento_IBS_Municipal', 'Valor_IBS', 'Valor_IBS_UF', 'Valor_IBS_Municipal', 'Valor_Total_Reforma'] as $suffix) {
                $routines['Calcular_' . $item . '_' . $suffix] = 'FUNCTION';
            }
        }
        return compact('tables', 'fields', 'routines');
    }

    private static function tableNames(mysqli $db): array
    {
        $result = mysqli_query($db, 'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE()');
        if (!$result) throw new RuntimeException(mysqli_error($db));
        $out = [];
        while ($row = mysqli_fetch_assoc($result)) $out[$row['TABLE_NAME']] = true;
        return $out;
    }

    private static function columns(mysqli $db): array
    {
        $result = mysqli_query($db, 'SELECT TABLE_NAME,COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()');
        if (!$result) throw new RuntimeException(mysqli_error($db));
        $out = [];
        while ($row = mysqli_fetch_assoc($result)) $out[$row['TABLE_NAME']][$row['COLUMN_NAME']] = true;
        return $out;
    }

    private static function routines(mysqli $db): array
    {
        $result = mysqli_query($db, 'SELECT ROUTINE_NAME,ROUTINE_TYPE FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA=DATABASE()');
        if (!$result) throw new RuntimeException(mysqli_error($db));
        $out = [];
        while ($row = mysqli_fetch_assoc($result)) $out[$row['ROUTINE_NAME']] = $row['ROUTINE_TYPE'];
        return $out;
    }

    private static function result(string $name, bool $present, string $detail): array
    {
        return ['name' => $name, 'detail' => $detail, 'status' => $present ? 'present' : 'absent'];
    }

    private static function unverified(array $requirements, string $reason): array
    {
        $tables = [];
        foreach ($requirements['tables'] as $table => $label) $tables[] = ['name' => $label, 'detail' => $table . ': ' . $reason, 'status' => 'unverified'];
        $fields = [];
        foreach ($requirements['fields'] as $table => $names) foreach ($names as $name) $fields[] = ['name' => $table . '.' . $name, 'detail' => $reason, 'status' => 'unverified'];
        $routines = [];
        foreach ($requirements['routines'] as $name => $type) $routines[] = ['name' => $name, 'detail' => $reason, 'status' => 'unverified', 'version' => 'unverified'];
        return compact('tables', 'fields', 'routines') + ['checked_at' => date('d/m/Y H:i:s'), 'read_only' => true];
    }
}
