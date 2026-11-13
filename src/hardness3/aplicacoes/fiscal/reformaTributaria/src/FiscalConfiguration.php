<?php
declare(strict_types=1);

require_once __DIR__ . '/ReformaTributariaCatalog.php';
require_once REFORMA_TRIBUTARIA_ROOT . '/bibliotecas/classes/ReformaTributaria.php';

class ReformaTributariaFiscalConfiguration
{
    private static function ok(mysqli $db): bool
    {
        $tables = [
            'D005' => 'D005_Id,D005_Classificacao_Fiscal,D005_Observacao,D005_Flag_Ativo,D005_Situacao_Tributaria_CBS_IBS,D005_Classificacao_Tributaria_CBS_IBS',
            'D006' => 'D006_Id,D006_Codigo_CFOP,D006_Descricao,D006_Flag_Inativo,D006_Situacao_Tributaria_CBS_IBS,D006_Classificacao_Tributaria_CBS_IBS,D006_Situacao_Tributaria_CBS_IBS_Regular,D006_Classificacao_Tributaria_CBS_IBS_Regular,D006_Flag_Deb_Crd_CBS,D006_Flag_Deb_Crd_IBS,D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,D006_Flag_Deduz_PIS_Base_IBS_CBS,D006_Flag_Deduz_COFINS_Base_IBS_CBS,D006_Flag_Deduz_ICMS_Base_IBS_CBS,D006_Flag_Deduz_DIFAL_Base_IBS_CBS,D006_Flag_Deduz_FCP_Base_IBS_CBS,D006_Flag_Deduz_FCP_Dest_Base_IBS_CBS',
        ];

        foreach ($tables as $table => $columns) {
            $result = mysqli_query($db, "SHOW COLUMNS FROM `$table`");
            if (!$result) {
                return false;
            }

            $available = [];
            while ($column = mysqli_fetch_assoc($result)) {
                $available[$column['Field']] = true;
            }

            foreach (explode(',', $columns) as $column) {
                if (empty($available[$column])) {
                    return false;
                }
            }
        }

        return true;
    }

    private static function source(string $type): array
    {
        if ($type === 'cfop') {
            return [
                'D006',
                "IFNULL(D006_Flag_Inativo,'N')<>'S'",
                'D006_Id',
                'D006_Codigo_CFOP',
                'D006_Descricao',
                'D006_Situacao_Tributaria_CBS_IBS',
                'D006_Classificacao_Tributaria_CBS_IBS',
                'D006_Flag_Deb_Crd_CBS',
                'D006_Flag_Deb_Crd_IBS',
            ];
        }

        return [
            'D005',
            "IFNULL(D005_Flag_Ativo,'S')<>'N'",
            'D005_Id',
            'D005_Classificacao_Fiscal',
            'D005_Observacao',
            'D005_Situacao_Tributaria_CBS_IBS',
            'D005_Classificacao_Tributaria_CBS_IBS',
            null,
            null,
        ];
    }

    public static function records(
        mysqli $db,
        string $type,
        string $filter = 'all',
        string $query = '',
        int $page = 1,
        int $perPage = 50
    ): array
    {
        if (!in_array($type, ['cfop', 'ncm'], true) || !self::ok($db)) {
            return [
                'available' => false,
                'message' => 'Esta conferência ainda não está disponível nesta base.',
            ];
        }

        [$table, $active, $id, $code, $description, $cst, $classification, $cbs, $ibs] = self::source($type);
        $columns = "$id AS id,$code AS code,$description AS description,$cst AS cst,$classification AS classification"
            . ($cbs ? ",$cbs AS cbs,$ibs AS ibs" : '')
            . ($type === 'cfop' ? ',D006_Situacao_Tributaria_CBS_IBS_Regular AS regular_cst,D006_Classificacao_Tributaria_CBS_IBS_Regular AS regular_classification,D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma AS no_add_total,D006_Flag_Deduz_PIS_Base_IBS_CBS AS deduct_pis,D006_Flag_Deduz_COFINS_Base_IBS_CBS AS deduct_cofins,D006_Flag_Deduz_ICMS_Base_IBS_CBS AS deduct_icms,D006_Flag_Deduz_DIFAL_Base_IBS_CBS AS deduct_difal,D006_Flag_Deduz_FCP_Base_IBS_CBS AS deduct_fcp,D006_Flag_Deduz_FCP_Dest_Base_IBS_CBS AS deduct_fcp_dest' : '');
        $sql = "SELECT $columns FROM $table WHERE $active";
        $query = trim($query);

        if ($query !== '') {
            $sql .= " AND ($code LIKE ? OR $description LIKE ?)";
            $statement = mysqli_prepare($db, $sql);
            $value = "%$query%";
            mysqli_stmt_bind_param($statement, 'ss', $value, $value);
        } else {
            $statement = mysqli_prepare($db, $sql);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $records = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $pending = [];
            $details = [];

            foreach ([
                ['Situação tributária CBS/IBS', 'cst'],
                ['Classificação tributária CBS/IBS', 'classification'],
            ] as [$label, $key]) {
                $value = trim((string) $row[$key]);
                $details[] = ['label' => $label, 'value' => $value ?: 'Não informado'];
                if (!$value) {
                    $pending[] = 'Informar ' . strtolower($label);
                }
            }

            if ($cbs) {
                foreach ([
                    ['Débito/crédito CBS', 'cbs'],
                    ['Débito/crédito IBS', 'ibs'],
                ] as [$label, $key]) {
                    $value = $row[$key] === 'S' ? 'Sim' : ($row[$key] === 'N' ? 'Não' : 'Não informado');
                    $details[] = ['label' => $label, 'value' => $value];
                    if ($value === 'Não informado') {
                        $pending[] = 'Informar ' . strtolower($label);
                    }
                }
            }

            if ($type === 'cfop') {
                $yesNo = static fn ($value): string => $value === 'S' ? 'Sim' : 'Não';
                $details[] = ['label' => 'CST CBS/IBS regular', 'value' => trim((string) $row['regular_cst']) ?: 'Não informado'];
                $details[] = ['label' => 'Classificação tributária regular', 'value' => trim((string) $row['regular_classification']) ?: 'Não informado'];
                foreach ([
                    ['Não somar IBS/CBS no total da reforma', 'no_add_total'],
                    ['Deduzir PIS da base IBS/CBS', 'deduct_pis'],
                    ['Deduzir COFINS da base IBS/CBS', 'deduct_cofins'],
                    ['Deduzir ICMS próprio da base IBS/CBS', 'deduct_icms'],
                    ['Deduzir DIFAL da base IBS/CBS', 'deduct_difal'],
                    ['Deduzir FCP próprio da base IBS/CBS', 'deduct_fcp'],
                    ['Deduzir FCP destino da base IBS/CBS', 'deduct_fcp_dest'],
                ] as [$label, $key]) {
                    $details[] = ['label' => $label, 'value' => $yesNo((string) $row[$key])];
                }

                static $reformaTributaria = null;
                static $ratesByClassification = [];
                $reformaTributaria ??= new \hardness\ReformaTributaria();
                $classificationCode = (string) $row['classification'];
                $rates = $ratesByClassification[$classificationCode] ??= $reformaTributaria->getAliquotas($classificationCode);
                foreach ([
                    ['Alíquota CBS', 'aliquotaCbs'], ['Redução CBS', 'percentualReducaoCbs'], ['Diferimento CBS', 'percentualDiferimentoCbs'],
                    ['Alíquota IBS UF', 'aliquotaIbsUf'], ['Redução IBS UF', 'percentualReducaoIbsUf'], ['Diferimento IBS UF', 'percentualDiferimentoIbsUf'],
                    ['Alíquota IBS municipal', 'aliquotaIbsMunicipal'], ['Redução IBS municipal', 'percentualReducaoIbsMunicipal'], ['Diferimento IBS municipal', 'percentualDiferimentoIbsMunicipal'],
                ] as [$label, $key]) {
                    $details[] = ['label' => $label, 'value' => number_format((float) $rates[$key], 2, ',', '.') . '%'];
                }
            }

            $configured = !$pending;
            if ($filter === 'all' || $filter === ($configured ? 'configured' : 'pending')) {
                $records[] = [
                    'id' => (int) $row['id'],
                    'code' => $row['code'],
                    'description' => $row['description'],
                    'cst' => trim((string) $row['cst']) ?: 'Não informado',
                    'classification' => trim((string) $row['classification']) ?: 'Não informado',
                    'regular_cst' => $type === 'cfop' ? (trim((string) $row['regular_cst']) ?: 'Não informado') : '—',
                    'regular_classification' => $type === 'cfop' ? (trim((string) $row['regular_classification']) ?: 'Não informado') : '—',
                    'status' => $configured ? 'Configurado' : 'Não configurado',
                    'filter' => $configured ? 'configured' : 'pending',
                    'summary' => 'CST: ' . ($row['cst'] ?: 'não informado') . ' · Classificação: ' . ($row['classification'] ?: 'não informada'),
                    'pending' => $pending,
                    'details' => $details,
                    'maintenance' => $type === 'cfop'
                        ? 'Fiscal → CFOP → aba Reforma'
                        : 'Fiscal → NCM → Cadastro NCM – Vale para todas empresas → aba Reforma',
                ];
            }
        }

        mysqli_stmt_close($statement);
        $total = count($records);

        $page = max(1, $page);
        $perPage = max(0, $perPage);

        return [
            'available' => true,
            'records' => $perPage === 0 ? $records : array_slice($records, ($page - 1) * $perPage, $perPage),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public static function summary(mysqli $db): array
    {
        if (!self::ok($db)) {
            return [
                'available' => false,
                'message' => 'Esta conferência ainda não está disponível nesta base.',
            ];
        }

        $summary = ['available' => true];
        foreach (['cfop', 'ncm'] as $type) {
            $all = self::records($db, $type, 'all', '', 1)['total'];
            $configured = self::records($db, $type, 'configured', '', 1)['total'];
            $summary[$type] = [
                'total' => $all,
                'configured' => $configured,
                'pending' => $all - $configured,
            ];
        }

        return $summary;
    }
}
