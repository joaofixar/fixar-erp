<?php

declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

use HardnessMcp\Context\ExecutionContext;
use PDO;

/** Persists immutable previews. This class never writes D009 or D009A. */
final class PreparePricesRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    private function query(string $sql, array $params = []): \PDOStatement
    {
        $s = $this->pdo->prepare($sql);
        $s->execute($params);
        return $s;
    }

    public function prepare(array $input, ExecutionContext $context): array
    {
        if (!$context->hasErpActor() || !$context->getResponsibleUserId()) throw new \RuntimeException('actor_required');
        if ($this->pdo->inTransaction()) throw new \RuntimeException('preview_unavailable');
        $hash = hash('sha256', json_encode($input, JSON_THROW_ON_ERROR), true);
        $existing = $this->existing($input, $context);
        if ($existing) return $this->replay($existing, $hash, $context);
        // Atomic history requires transactional tables even on older tenant databases.
        $engines = $this->query("SELECT TABLE_NAME,ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('MCP020','MCP021')")->fetchAll(PDO::FETCH_KEY_PAIR);
        if (count($engines) !== 2 || count(array_filter($engines, static function ($engine) { return strtoupper($engine) === 'INNODB'; })) !== 2) {
            throw new \RuntimeException('preview_unavailable');
        }
        $this->pdo->beginTransaction();
        try {
            $this->query("INSERT INTO MCP020 (MCP020_Public_Id,MCP020_C004_Id,MCP020_MCP001_Id,MCP020_C007_Id,
                MCP020_Chave_Idempotencia,MCP020_Hash_Entrada,MCP020_Origem,MCP020_Data_Criacao,MCP020_Data_Expiracao)
                VALUES (?,?,?,?,?,?,?,UTC_TIMESTAMP(),DATE_ADD(UTC_TIMESTAMP(),INTERVAL 30 MINUTE))",
                [bin2hex(random_bytes(16)), $context->getCompanyId(), $context->getConnectionId(), $context->getResponsibleUserId(),
                 $input['request_key'], $hash, $input['origin']]);
            $id = (int)$this->pdo->lastInsertId();
            $rows = [];
            $codeCounts = array_count_values(array_column($input['items'], 'code'));
            $products = $this->findProducts(array_column($input['items'], 'code'), $context->getCompanyId());
            foreach ($input['items'] as $item) {
                $rows[] = $this->resolve($item, $products[$item['code']] ?? [], $codeCounts[$item['code']] > 1);
            }
            // Distinct input codes must never cause two writes to the same target.
            $targets = [];
            foreach ($rows as $row) if ($row['target'] !== null) $targets[$row['target']] = ($targets[$row['target']] ?? 0) + 1;
            foreach ($rows as &$row) {
                if ($row['target'] !== null && $targets[$row['target']] > 1) {
                    $row['target'] = null;
                    $row['status'] = 'blocked';
                    $row['error'] = 'duplicate_product';
                }
                $this->insertItem($id, $context->getCompanyId(), $row);
            }
            unset($row);
            $persisted = $this->query('SELECT * FROM MCP021 WHERE MCP021_MCP020_Id=? ORDER BY MCP021_Linha', [$id])->fetchAll(PDO::FETCH_ASSOC);
            $previewHash = PricePreviewIntegrity::hash($persisted);
            $this->query("UPDATE MCP020 SET MCP020_Status='prepared',MCP020_Hash_Previa=?,MCP020_Data_Alteracao=UTC_TIMESTAMP() WHERE MCP020_Id=?", [$previewHash, $id]);
            $batch = $this->existing($input, $context);
            $result = $this->output($batch);
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            // A concurrent identical request may have committed while INSERT waited.
            if ($e instanceof \PDOException && (int)($e->errorInfo[1] ?? 0) === 1062) {
                $existing = $this->existing($input, $context);
                if ($existing) return $this->replay($existing, $hash, $context);
            }
            throw $e;
        }
    }

    private function existing(array $input, ExecutionContext $context): ?array
    {
        $r = $this->query('SELECT *, MCP020_Data_Expiracao <= UTC_TIMESTAMP() AS expired FROM MCP020
            WHERE MCP020_C004_Id=? AND MCP020_MCP001_Id=? AND MCP020_Chave_Idempotencia=?',
            [$context->getCompanyId(), $context->getConnectionId(), $input['request_key']])->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    private function replay(array $batch, string $hash, ExecutionContext $context): array
    {
        if (!hash_equals($batch['MCP020_Hash_Entrada'], $hash) || (int)$batch['MCP020_C007_Id'] !== $context->getResponsibleUserId()) {
            throw new \RuntimeException('idempotency_conflict');
        }
        if ($batch['expired']) throw new \RuntimeException('preview_expired');
        if ($batch['MCP020_Status'] !== 'prepared') throw new \RuntimeException('preview_unavailable');
        return $this->output($batch);
    }

    private function findProducts(array $codes, int $company): array
    {
        $result = [];
        foreach (array_chunk(array_values(array_unique($codes)), 250) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $statement = $this->query("SELECT p.D001_Codigo_Produto,d.D009_Id,
                d.D009_Preco_1,d.D009_Preco_2,d.D009_Preco_3,d.D009_Preco_4,
                d.D009_IPT_1,d.D009_IPT_2,d.D009_IPT_3,d.D009_IPT_4,
                a.D009A_Id,a.D009A_Preco_5,a.D009A_IPT_5
                FROM D001 p JOIN D049 v ON v.D049_D001_Id=p.D001_Id
                JOIN D009 d ON d.D009_D049_Id=v.D049_Id
                LEFT JOIN D009A a ON a.D009A_D009_Id=d.D009_Id
                WHERE p.D001_Flag_Ativo='S' AND d.D009_C004_Id=?
                  AND p.D001_Codigo_Produto IN ($placeholders)", array_merge([$company], $chunk));
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $code = $row['D001_Codigo_Produto'];
                // IN uses the database collation; PHP enforces exact code identity.
                if (!in_array($code, $chunk, true)) continue;
                if (count($result[$code] ?? []) < 2) $result[$code][] = $row;
            }
        }
        return $result;
    }

    private function resolve(array $item, array $products, bool $duplicate): array
    {
        $row = ['line'=>$item['line'], 'code'=>$item['code'], 'target'=>null, 'status'=>'blocked',
            'error'=>null, 'snapshot'=>false, 'selected'=>[], 'before'=>[], 'after'=>[]];
        for ($n=1; $n<=5; $n++) {
            $row['selected'][$n] = array_key_exists('price_'.$n, $item['prices']) ? 1 : 0;
            $row['before'][$n] = $row['after'][$n] = null;
        }
        if ($duplicate) { $row['error']='duplicate_code'; return $row; }
        if (array_sum($row['selected']) === 0) { $row['error']='price_selection_required'; return $row; }
        try {
            foreach ($item['prices'] as $key=>$value) $row['after'][(int)substr($key, 6)] = PreparePricesInput::decimal($value);
        } catch (\InvalidArgumentException $e) { $row['error']='invalid_price'; return $row; }
        if (count($products) !== 1) {
            $row['error'] = $products ? 'ambiguous_product' : 'product_not_found';
            return $row;
        }
        $p = $products[0];
        $row['target'] = (int)$p['D009_Id'];
        $row['snapshot'] = true;
        $changed = false;
        for ($n=1; $n<=5; $n++) {
            $prefix = $n===5 ? 'D009A_' : 'D009_';
            $row['before'][$n] = $p[$prefix.'Preco_'.$n];
            if (!$row['selected'][$n]) $row['after'][$n] = $row['before'][$n];
            else {
                if ($n===5 && $p['D009A_Id'] === null) $row['error']='price_5_record_missing';
                if ((float)$p[$prefix.'IPT_'.$n] > 0) $row['error']='automatic_price';
                if ($row['after'][$n] !== $row['before'][$n]) $changed=true;
            }
        }
        if ($row['error'] === null) $row['status'] = $changed ? 'ready' : 'unchanged';
        return $row;
    }

    private function insertItem(int $batch, int $company, array $row): void
    {
        $values = ['MCP021_MCP020_Id'=>$batch, 'MCP021_C004_Id'=>$company, 'MCP021_Linha'=>$row['line'],
            'MCP021_Codigo_Produto'=>$row['code'], 'MCP021_D009_Id'=>$row['target'],
            'MCP021_Status'=>$row['status'], 'MCP021_Codigo_Erro'=>$row['error']];
        for ($n=1; $n<=5; $n++) {
            $values['MCP021_Selecionado_'.$n] = $row['selected'][$n];
            $values['MCP021_Preco_'.$n.'_Antes'] = $row['before'][$n];
            $values['MCP021_Preco_'.$n.'_Depois'] = $row['after'][$n];
        }
        $this->query('INSERT INTO MCP021 ('.implode(',', array_keys($values)).',MCP021_Data_Criacao,MCP021_Data_Snapshot) VALUES ('
            .implode(',', array_fill(0,count($values),'?')).',UTC_TIMESTAMP(),'.($row['snapshot'] ? 'UTC_TIMESTAMP()' : 'NULL').')', array_values($values));
    }

    private function output(array $batch): array
    {
        $result = ['batch_id'=>$batch['MCP020_Public_Id'], 'status'=>$batch['MCP020_Status'],
            'expires_at'=>str_replace(' ', 'T', $batch['MCP020_Data_Expiracao']).'Z',
            'preview_hash'=>bin2hex($batch['MCP020_Hash_Previa']),
            'prices_updated'=>false, 'requires_approval'=>true, 'apply_available'=>true,
            'summary'=>['ready'=>0,'blocked'=>0,'unchanged'=>0], 'items'=>[]];
        $rows = $this->query('SELECT * FROM MCP021 WHERE MCP021_MCP020_Id=? AND MCP021_C004_Id=? ORDER BY MCP021_Linha',
            [$batch['MCP020_Id'], $batch['MCP020_C004_Id']])->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $item = ['line'=>(int)$row['MCP021_Linha'], 'code'=>$row['MCP021_Codigo_Produto'],
                'status'=>$row['MCP021_Status'], 'error'=>$row['MCP021_Codigo_Erro'], 'changes'=>[], 'warnings'=>[]];
            for ($n=1; $n<=5; $n++) if ($row['MCP021_Selecionado_'.$n]) {
                $new = $row['MCP021_Preco_'.$n.'_Depois'];
                $item['changes'][] = ['price'=>$n, 'current'=>$row['MCP021_Preco_'.$n.'_Antes'], 'proposed'=>$new];
                if ($new === '0.0000000000') $item['warnings'][] = 'price_'.$n.'_zero_requires_explicit_approval';
            }
            $item['changes_text'] = implode('; ', array_map(static function ($change) {
                return 'Preço '.$change['price'].': '.($change['current'] ?? 'sem valor').' → '.($change['proposed'] ?? 'inválido/pendente');
            }, $item['changes']));
            $result['summary'][$item['status']] = ($result['summary'][$item['status']] ?? 0) + 1;
            $result['items'][] = $item;
        }
        return $result;
    }
}
