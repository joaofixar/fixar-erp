<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Logistics;

use PDO;

/** Read-only access to the partner's SMT071/SMT072 logistics schema. */
final class PdoLoadReportRepository
{
    private PDO $pdo;
    private bool $orderContextAvailable;
    private bool $resourcesAvailable;
    public function __construct(PDO $pdo, bool $orderContextAvailable = false, bool $resourcesAvailable = false) { $this->pdo = $pdo; $this->orderContextAvailable = $orderContextAvailable; $this->resourcesAvailable = $resourcesAvailable; }

    /** @param array<string,mixed> $input @return array<string,mixed> */
    public function summary(int $companyId, array $input): array
    {
        $date = $input['basis'] === 'scheduled_at' ? 'h.SMT071_Data_Previsao' : 'h.SMT071_Data_Criacao';
        $groups = ['status' => ['h.SMT071_Flag_Status', 'status'], 'created_day' => ['DATE(h.SMT071_Data_Criacao)', 'date'], 'scheduled_day' => ['DATE(h.SMT071_Data_Previsao)', 'date']];
        [$expression, $label] = $groups[$input['group']];
        $where = "h.SMT071_C004_Id = :company AND {$date} >= :from AND {$date} < DATE_ADD(:to, INTERVAL 1 DAY)";
        $params = [':company' => $companyId, ':from' => $input['from'], ':to' => $input['to']];
        $total = $this->pdo->prepare("SELECT COUNT(*) AS load_count, COALESCE(SUM(h.SMT071_Valor_Total),0) AS load_total, COALESCE(SUM(h.SMT071_Peso_Total),0) AS load_weight, COALESCE(SUM(h.SMT071_Valor_Fechado),0) AS freight_total FROM SMT071 h WHERE {$where}"); $total->execute($params); $summary = $total->fetch(PDO::FETCH_ASSOC) ?: [];
        $statement = $this->pdo->prepare("SELECT {$expression} AS group_value, COUNT(*) AS load_count, COALESCE(SUM(h.SMT071_Valor_Total),0) AS load_total, COALESCE(SUM(h.SMT071_Peso_Total),0) AS load_weight, COALESCE(SUM(h.SMT071_Valor_Fechado),0) AS freight_total FROM SMT071 h WHERE {$where} GROUP BY {$expression} ORDER BY {$expression}"); $statement->execute($params);
        return ['report' => 'logistics_load_summary', 'period' => ['from' => $input['from'], 'to' => $input['to'], 'basis' => $input['basis']], 'group_by' => $input['group'], 'summary' => $summary, 'rows' => array_map(static function (array $row) use ($label): array { $row[$label] = $row['group_value']; unset($row['group_value']); return $row; }, $statement->fetchAll(PDO::FETCH_ASSOC))];
    }

    /** @param array<string,mixed> $input @return array<string,mixed> */
    public function listing(int $companyId, array $input): array
    {
        $date = $input['basis'] === 'scheduled_at' ? 'h.SMT071_Data_Previsao' : 'h.SMT071_Data_Criacao';
        $where = "h.SMT071_C004_Id = :company AND {$date} >= :from AND {$date} < DATE_ADD(:to, INTERVAL 1 DAY)";
        $params = [':company' => $companyId, ':from' => $input['from'], ':to' => $input['to']];
        if ($input['status'] !== null && $input['status'] !== '') { $where .= ' AND h.SMT071_Flag_Status = :status'; $params[':status'] = $input['status']; }
        $sql = "SELECT h.SMT071_Id AS load_id, h.SMT071_Descricao AS description, h.SMT071_Data_Criacao AS created_at, h.SMT071_Data_Previsao AS scheduled_at, h.SMT071_Hora_Previsao AS scheduled_time, h.SMT071_Flag_Status AS status, h.SMT071_D109_Id AS driver_id, h.SMT071_D022_Id AS carrier_id, h.SMT071_D170_Id AS vehicle_id, h.SMT071_Valor_Total AS load_total, h.SMT071_Peso_Total AS load_weight, h.SMT071_Valor_Frete_Incluso AS included_freight_total, h.SMT071_Valor_Fechado AS freight_total, h.SMT071_Km_Rodado AS travelled_km, (h.SMT071_Valor_Fechado - h.SMT071_Valor_Frete_Incluso) AS freight_net_total, CASE WHEN h.SMT071_Valor_Total > 0 THEN (h.SMT071_Valor_Fechado / h.SMT071_Valor_Total) * 100 ELSE 0 END AS freight_percent_of_load, CASE WHEN h.SMT071_Peso_Total > 0 THEN h.SMT071_Valor_Fechado / h.SMT071_Peso_Total ELSE 0 END AS freight_per_kg, CASE WHEN h.SMT071_Km_Rodado > 0 THEN h.SMT071_Valor_Fechado / h.SMT071_Km_Rodado ELSE 0 END AS freight_per_km, COUNT(d.SMT072_Id) AS linked_order_count, COALESCE(SUM(d.SMT072_Valor_Pedido),0) AS linked_order_total, COALESCE(SUM(d.SMT072_Peso_Pedido),0) AS linked_order_weight FROM SMT071 h LEFT JOIN SMT072 d ON d.SMT072_SMT071_Id = h.SMT071_Id WHERE {$where} GROUP BY h.SMT071_Id ORDER BY {$date} DESC, h.SMT071_Id DESC LIMIT " . (int)($input['limit'] + 1);
        $statement = $this->pdo->prepare($sql); $statement->execute($params); $rows = $statement->fetchAll(PDO::FETCH_ASSOC); $more = count($rows) > $input['limit']; if ($more) array_pop($rows);
        if ($this->orderContextAvailable && $rows !== []) { $this->appendListingOrderContext($rows, $companyId); }
        if ($this->resourcesAvailable && $rows !== []) { $this->appendResourceContext($rows, $companyId); }
        return ['report' => 'logistics_load_list', 'period' => ['from' => $input['from'], 'to' => $input['to'], 'basis' => $input['basis']], 'filters_applied' => $input['status'] === null || $input['status'] === '' ? [] : ['status' => $input['status']], 'rows' => $rows, 'has_more' => $more];
    }

    /** @return array<string,mixed>|null */
    public function detail(int $companyId, int $loadId): ?array
    {
        $header = $this->pdo->prepare('SELECT SMT071_Id AS load_id, SMT071_Descricao AS description, SMT071_Data_Criacao AS created_at, SMT071_Data_Previsao AS scheduled_at, SMT071_Hora_Previsao AS scheduled_time, SMT071_Flag_Status AS status, SMT071_D109_Id AS driver_id, SMT071_D022_Id AS carrier_id, SMT071_D170_Id AS vehicle_id, SMT071_Valor_Total AS load_total, SMT071_Peso_Total AS load_weight, SMT071_Valor_Frete_Incluso AS included_freight_total, SMT071_Valor_Fechado AS freight_total, SMT071_Km_Rodado AS travelled_km, (SMT071_Valor_Fechado - SMT071_Valor_Frete_Incluso) AS freight_net_total, CASE WHEN SMT071_Valor_Total > 0 THEN (SMT071_Valor_Fechado / SMT071_Valor_Total) * 100 ELSE 0 END AS freight_percent_of_load, CASE WHEN SMT071_Peso_Total > 0 THEN SMT071_Valor_Fechado / SMT071_Peso_Total ELSE 0 END AS freight_per_kg, CASE WHEN SMT071_Km_Rodado > 0 THEN SMT071_Valor_Fechado / SMT071_Km_Rodado ELSE 0 END AS freight_per_km FROM SMT071 WHERE SMT071_C004_Id = :company AND SMT071_Id = :load LIMIT 1');
        $header->execute([':company' => $companyId, ':load' => $loadId]); $result = $header->fetch(PDO::FETCH_ASSOC); if (!is_array($result)) return null;
        $items = $this->pdo->prepare('SELECT SMT072_Id AS link_id, SMT072_T005_Id AS order_id, SMT072_Sequecia_Entrega AS delivery_sequence, SMT072_Valor_Pedido AS order_total, SMT072_Peso_Pedido AS order_weight, SMT072_Guia_St AS tax_guide, SMT072_Contrata_Auxiliar_Descarga AS hires_unloading_helper, SMT072_Valor_Axiliar_Descarga AS unloading_helper_total FROM SMT072 WHERE SMT072_SMT071_Id = :load ORDER BY SMT072_Sequecia_Entrega, SMT072_Id');
        $items->execute([':load' => $loadId]);
        $result['items'] = $items->fetchAll(PDO::FETCH_ASSOC);
        $result['order_context_available'] = $this->orderContextAvailable;
        if ($this->orderContextAvailable && $result['items'] !== []) {
            $this->appendOrderContext($result['items'], $companyId);
        }
        $result['resources_available'] = $this->resourcesAvailable;
        if ($this->resourcesAvailable) { $headers = [$result]; $this->appendResourceContext($headers, $companyId); $result = $headers[0]; }
        return $result;
    }

    /** @param array<int,array<string,mixed>> $items */
    private function appendOrderContext(array &$items, int $companyId): void
    {
        $orderIds = [];
        foreach ($items as &$item) {
            $item['invoices'] = [];
            $item['invoice_count'] = 0;
            $item['invoice_numbers'] = null;
            $item['customer_name'] = null;
            $item['destination_city'] = null;
            $item['destination_state'] = null;
            $item['destination_address'] = null;
            $item['destination_source'] = null;
            $item['destination_region'] = null;
            $item['order_issued_at'] = null;
            $item['order_delivery_at'] = null;
            $item['order_freight_total'] = null;
            $item['item_freight_total'] = null;
            $item['order_status'] = null;
            $item['payment_terms'] = null;
            $orderId = (int)($item['order_id'] ?? 0);
            if ($orderId > 0) { $orderIds[$orderId] = true; }
        }
        unset($item);
        if ($orderIds === []) { return; }
        $placeholders = [];
        $params = [':company' => $companyId];
        foreach (array_keys($orderIds) as $index => $orderId) {
            $placeholder = ':order_' . $index;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $orderId;
        }
        $contextSql = 'SELECT o.T005_Id AS order_id, COALESCE(NULLIF(c.D024_Nome_Fantasia, \'\'), c.D024_Nome_Empresa) AS customer_name, o.T005_Data_Emissao AS order_issued_at, o.T005_Data_Entrega AS order_delivery_at, o.T005_Valor_Frete AS order_freight_total, x.T005A_Valor_Frete_Itens AS item_freight_total, o.T005_Nome_Status AS order_status, o.T005_Prazos_Pagamentos AS payment_terms, CASE WHEN r.D148_Id IS NOT NULL THEN \'redespacho\' WHEN e.D148_Id IS NOT NULL THEN \'endereco_entrega\' ELSE \'cadastro_cliente\' END AS destination_source, COALESCE(rc.D020_Nome_Cidade, ec.D020_Nome_Cidade, cc.D020_Nome_Cidade, c.D024_Cidade) AS destination_city, COALESCE(rs.D018_UF, es.D018_UF, cs.D018_UF, c.D024_Estado) AS destination_state, COALESCE(rr.D084_Regiao, er.D084_Regiao, cr.D084_Regiao) AS destination_region, CASE WHEN r.D148_Id IS NOT NULL THEN CONCAT_WS(\', \', NULLIF(CONCAT_WS(\' \', r.D148_Rua, r.D148_Numero), \'\'), NULLIF(r.D148_Bairro, \'\'), NULLIF(r.D148_Cep, \'\')) WHEN e.D148_Id IS NOT NULL THEN CONCAT_WS(\', \', NULLIF(CONCAT_WS(\' \', e.D148_Rua, e.D148_Numero), \'\'), NULLIF(e.D148_Bairro, \'\'), NULLIF(e.D148_Cep, \'\')) ELSE CONCAT_WS(\', \', NULLIF(CONCAT_WS(\' \', c.D024_Endereco, c.D024_Numero), \'\'), NULLIF(c.D024_Bairro, \'\'), NULLIF(c.D024_Cep, \'\')) END AS destination_address FROM T005 o LEFT JOIN T005A x ON x.T005A_T005_Id = o.T005_Id LEFT JOIN D024 c ON c.D024_Id = o.T005_D024_Id AND c.D024_C004_Id = o.T005_C004_Id LEFT JOIN D148 e ON e.D148_Id = o.T005_D148_Id_Entrega LEFT JOIN D148 r ON r.D148_Id = x.T005A_D148_Id_Redespacho LEFT JOIN D020 cc ON cc.D020_Id = c.D024_D020_Id LEFT JOIN D020 ec ON ec.D020_Id = e.D148_D020_Id LEFT JOIN D020 rc ON rc.D020_Id = r.D148_D020_Id LEFT JOIN D018 cs ON cs.D018_Id = c.D024_D018_Id LEFT JOIN D018 es ON es.D018_Id = e.D148_D018_Id LEFT JOIN D018 rs ON rs.D018_Id = r.D148_D018_Id LEFT JOIN D084 cr ON cr.D084_Id = cc.D020_D084_Id LEFT JOIN D084 er ON er.D084_Id = ec.D020_D084_Id LEFT JOIN D084 rr ON rr.D084_Id = rc.D020_D084_Id WHERE o.T005_C004_Id = :company AND o.T005_Id IN (' . implode(',', $placeholders) . ')';
        $contextStatement = $this->pdo->prepare($contextSql);
        $contextStatement->execute($params);
        $contextByOrder = [];
        foreach ($contextStatement->fetchAll(PDO::FETCH_ASSOC) as $context) { $contextByOrder[(int)$context['order_id']] = $context; }
        $sql = 'SELECT T007_Id AS invoice_id, T007_T005_Id AS order_id, T007_Numero_Nota_Fiscal AS invoice_number, T007_Serie AS invoice_series, T007_Data_Emissao AS issued_at, T007_Valor_Total AS invoice_total, T007_Chave_Acesso_Nfe AS access_key, T007_Flag_Cancelada AS cancelled, T007_Cliente_Razao AS customer_name, T007_Cliente_Cidade AS destination_city, T007_Cliente_Estado AS destination_state, CONCAT_WS(\', \', NULLIF(CONCAT_WS(\' \', T007_Cliente_Endereco, T007_Cliente_Endereco_Numero), \'\'), NULLIF(T007_Cliente_Bairro, \'\'), NULLIF(T007_Cliente_CEP, \'\')) AS destination_address FROM T007 WHERE T007_C004_Id = :company AND T007_T005_Id IN (' . implode(',', $placeholders) . ') ORDER BY T007_T005_Id, T007_Data_Emissao, T007_Id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $invoicesByOrder = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $invoice) {
            $invoicesByOrder[(int)$invoice['order_id']][] = $invoice;
        }
        foreach ($items as &$item) {
            $orderId = (int)($item['order_id'] ?? 0);
            $context = $contextByOrder[$orderId] ?? [];
            $invoices = $invoicesByOrder[$orderId] ?? [];
            $item['invoices'] = $invoices;
            $item['invoice_count'] = count($invoices);
            $item['customer_name'] = $context['customer_name'] ?? null;
            $item['destination_city'] = $context['destination_city'] ?? null;
            $item['destination_state'] = $context['destination_state'] ?? null;
            $item['destination_address'] = $context['destination_address'] ?? null;
            $item['destination_source'] = $context['destination_source'] ?? null;
            $item['destination_region'] = $context['destination_region'] ?? null;
            $item['order_issued_at'] = $context['order_issued_at'] ?? null;
            $item['order_delivery_at'] = $context['order_delivery_at'] ?? null;
            $item['order_freight_total'] = $context['order_freight_total'] ?? null;
            $item['item_freight_total'] = $context['item_freight_total'] ?? null;
            $item['order_status'] = $context['order_status'] ?? null;
            $item['payment_terms'] = $context['payment_terms'] ?? null;
            foreach ($invoices as $invoice) {
                if (trim((string)($invoice['customer_name'] ?? '')) !== '') { $item['customer_name'] = $invoice['customer_name']; }
                foreach (['destination_city', 'destination_state', 'destination_address'] as $field) {
                    if (trim((string)($item[$field] ?? '')) === '' && trim((string)($invoice[$field] ?? '')) !== '') { $item[$field] = $invoice[$field]; }
                }
            }
            $numbers = [];
            foreach ($invoices as $invoice) {
                $number = trim((string)($invoice['invoice_number'] ?? ''));
                $series = trim((string)($invoice['invoice_series'] ?? ''));
                if ($number !== '') { $numbers[] = $series === '' ? $number : $number . ' (série ' . $series . ')'; }
            }
            $item['invoice_numbers'] = $numbers === [] ? null : implode(', ', $numbers);
        }
        unset($item);
    }

    /** @param array<int,array<string,mixed>> $rows */
    private function appendListingOrderContext(array &$rows, int $companyId): void
    {
        $loadIds = [];
        foreach ($rows as &$row) {
            $row['customer_names'] = null;
            $row['destinations'] = null;
            $row['invoice_count'] = 0;
            $row['invoice_numbers'] = null;
            $loadId = (int)($row['load_id'] ?? 0);
            if ($loadId > 0) { $loadIds[$loadId] = true; }
        }
        unset($row);
        if ($loadIds === []) { return; }
        $placeholders = [];
        $params = [':company' => $companyId];
        foreach (array_keys($loadIds) as $index => $loadId) {
            $placeholder = ':load_' . $index;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $loadId;
        }
        $sql = 'SELECT d.SMT072_SMT071_Id AS load_id, d.SMT072_T005_Id AS order_id, COALESCE(NULLIF(c.D024_Nome_Fantasia, \'\'), c.D024_Nome_Empresa) AS order_customer_name, COALESCE(rc.D020_Nome_Cidade, ec.D020_Nome_Cidade, cc.D020_Nome_Cidade, c.D024_Cidade) AS order_city, COALESCE(rs.D018_UF, es.D018_UF, cs.D018_UF, c.D024_Estado) AS order_state, n.T007_Id AS invoice_id, n.T007_Numero_Nota_Fiscal AS invoice_number, n.T007_Serie AS invoice_series, n.T007_Flag_Cancelada AS cancelled, n.T007_Cliente_Razao AS invoice_customer_name, n.T007_Cliente_Cidade AS invoice_city, n.T007_Cliente_Estado AS invoice_state FROM SMT072 d INNER JOIN T005 o ON o.T005_Id = d.SMT072_T005_Id AND o.T005_C004_Id = :company LEFT JOIN T005A x ON x.T005A_T005_Id = o.T005_Id LEFT JOIN D024 c ON c.D024_Id = o.T005_D024_Id AND c.D024_C004_Id = o.T005_C004_Id LEFT JOIN D148 e ON e.D148_Id = o.T005_D148_Id_Entrega LEFT JOIN D148 r ON r.D148_Id = x.T005A_D148_Id_Redespacho LEFT JOIN D020 cc ON cc.D020_Id = c.D024_D020_Id LEFT JOIN D020 ec ON ec.D020_Id = e.D148_D020_Id LEFT JOIN D020 rc ON rc.D020_Id = r.D148_D020_Id LEFT JOIN D018 cs ON cs.D018_Id = c.D024_D018_Id LEFT JOIN D018 es ON es.D018_Id = e.D148_D018_Id LEFT JOIN D018 rs ON rs.D018_Id = r.D148_D018_Id LEFT JOIN T007 n ON n.T007_T005_Id = o.T005_Id AND n.T007_C004_Id = o.T005_C004_Id WHERE d.SMT072_SMT071_Id IN (' . implode(',', $placeholders) . ') ORDER BY d.SMT072_SMT071_Id, d.SMT072_Sequecia_Entrega, n.T007_Id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $contexts = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $loadId = (int)$item['load_id'];
            if (!isset($contexts[$loadId])) { $contexts[$loadId] = ['customers' => [], 'destinations' => [], 'invoices' => []]; }
            $customer = trim((string)($item['invoice_customer_name'] ?? ''));
            if ($customer === '') { $customer = trim((string)($item['order_customer_name'] ?? '')); }
            if ($customer !== '') { $contexts[$loadId]['customers'][$customer] = true; }
            $city = trim((string)($item['order_city'] ?? ''));
            $state = trim((string)($item['order_state'] ?? ''));
            if ($city === '') { $city = trim((string)($item['invoice_city'] ?? '')); }
            if ($state === '') { $state = trim((string)($item['invoice_state'] ?? '')); }
            $destination = $city === '' ? $state : ($state === '' ? $city : $city . '/' . $state);
            if ($destination !== '') { $contexts[$loadId]['destinations'][$destination] = true; }
            $invoiceId = (int)($item['invoice_id'] ?? 0);
            if ($invoiceId > 0) {
                $number = trim((string)($item['invoice_number'] ?? ''));
                $series = trim((string)($item['invoice_series'] ?? ''));
                $label = $number === '' ? 'NF interna ' . $invoiceId : $number . ($series === '' ? '' : ' (série ' . $series . ')');
                if ((string)($item['cancelled'] ?? '') === 'S') { $label .= ' — cancelada'; }
                $contexts[$loadId]['invoices'][$invoiceId] = $label;
            }
        }
        foreach ($rows as &$row) {
            $context = $contexts[(int)($row['load_id'] ?? 0)] ?? ['customers' => [], 'destinations' => [], 'invoices' => []];
            $row['customer_names'] = $this->compactLabels(array_keys($context['customers']));
            $row['destinations'] = $this->compactLabels(array_keys($context['destinations']));
            $row['invoice_count'] = count($context['invoices']);
            $row['invoice_numbers'] = $this->compactLabels(array_values($context['invoices']));
        }
        unset($row);
    }

    /** @param string[] $labels */
    private function compactLabels(array $labels): ?string
    {
        $labels = array_values(array_unique(array_filter($labels, static fn(string $label): bool => $label !== '')));
        if ($labels === []) { return null; }
        $visible = array_slice($labels, 0, 12);
        $text = implode(', ', $visible);
        return count($labels) > count($visible) ? $text . ' (+' . (count($labels) - count($visible)) . ')' : $text;
    }

    /** @param array<int,array<string,mixed>> $rows */
    private function appendResourceContext(array &$rows, int $companyId): void
    {
        $loadIds = [];
        foreach ($rows as &$row) {
            $row['driver_name'] = null;
            $row['carrier_name'] = null;
            $row['vehicle_identification'] = null;
            $row['vehicle_plate'] = null;
            $row['vehicle_capacity_kg'] = null;
            $row['vehicle_capacity_m3'] = null;
            $row['vehicle_body_length_m'] = null;
            $row['photo_count'] = 0;
            $row['latest_photo_at'] = null;
            $loadId = (int)($row['load_id'] ?? 0);
            if ($loadId > 0) { $loadIds[$loadId] = true; }
        }
        unset($row);
        if ($loadIds === []) { return; }
        $placeholders = [];
        $params = [':company' => $companyId];
        foreach (array_keys($loadIds) as $index => $loadId) {
            $placeholder = ':load_' . $index;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $loadId;
        }
        $sql = 'SELECT h.SMT071_Id AS load_id, d.D109_Nome AS driver_name, COALESCE(NULLIF(c.D022_Nome_Fantasia, \'\'), c.D022_Nome_Empresa) AS carrier_name, v.D170_Identificacao_Veiculo AS vehicle_identification, v.D170_Placa AS vehicle_plate, v.D170_Capacidade_Kg AS vehicle_capacity_kg, v.D170_Capacidade_M3 AS vehicle_capacity_m3, va.D170A_Comprimento_Carroceria AS vehicle_body_length_m, COUNT(p.SMT171_Id) AS photo_count, MAX(p.SMT171_Data) AS latest_photo_at FROM SMT071 h LEFT JOIN D109 d ON d.D109_Id = h.SMT071_D109_Id LEFT JOIN D022 c ON c.D022_Id = h.SMT071_D022_Id LEFT JOIN D170 v ON v.D170_Id = h.SMT071_D170_Id LEFT JOIN D170A va ON va.D170A_D170_Id = v.D170_Id LEFT JOIN SMT171 p ON p.SMT171_SMT071_Id = h.SMT071_Id AND p.SMT171_C004_Id = h.SMT071_C004_Id WHERE h.SMT071_C004_Id = :company AND h.SMT071_Id IN (' . implode(',', $placeholders) . ') GROUP BY h.SMT071_Id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $resources = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $resource) { $resources[(int)$resource['load_id']] = $resource; }
        foreach ($rows as &$row) {
            $resource = $resources[(int)($row['load_id'] ?? 0)] ?? [];
            foreach (['driver_name', 'carrier_name', 'vehicle_identification', 'vehicle_plate', 'vehicle_capacity_kg', 'vehicle_capacity_m3', 'vehicle_body_length_m', 'photo_count', 'latest_photo_at'] as $field) {
                if (array_key_exists($field, $resource)) { $row[$field] = $resource[$field]; }
            }
        }
        unset($row);
    }
}
