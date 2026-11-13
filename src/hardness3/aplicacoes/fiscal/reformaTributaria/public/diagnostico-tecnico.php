<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../src/TechnicalDiagnostic.php';

$context = reforma_tributaria_require_access();
$diagnostic = null;
$error = '';
$db = reforma_tributaria_connect();
if (!$db) $error = 'Não foi possível consultar o diagnóstico técnico.';
else {
    try { $diagnostic = ReformaTributariaTechnicalDiagnostic::run($db); }
    catch (Throwable $e) { $error = 'Não foi possível executar o diagnóstico técnico.'; }
    mysqli_close($db);
}
function reforma_tributaria_technical_count(array $rows, string $status): int { $count = 0; foreach ($rows as $row) if (($row['status'] ?? '') === $status) $count++; return $count; }
$all = $diagnostic ? array_merge($diagnostic['tables'], $diagnostic['fields'], $diagnostic['routines']) : [];
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Diagnóstico técnico — Reforma Tributária</title><style>body{margin:0;background:#f3f6fb;color:#10284a;font:15px/1.5 Arial,sans-serif}.shell{max-width:880px;margin:auto;padding:28px}.box{background:#fff;border:1px solid #dbe5f0;border-radius:14px;padding:24px;box-shadow:0 5px 16px rgba(24,62,100,.06)}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:20px 0}.card{border:1px solid #dbe5f0;border-radius:10px;padding:15px}.number{font-size:28px;font-weight:bold}.ok{color:#167244}.no{color:#bc3030}.warn{color:#9a6700}.notice{padding:14px;margin:16px 0;background:#fff6df;border:1px solid #edd598;border-radius:10px;color:#735500}a{color:#17659a}@media(max-width:650px){.shell{padding:15px}.grid{grid-template-columns:1fr}}</style></head><body><main class="shell"><div class="box"><h1>Área técnica secundária</h1><p>Resumo interno da estrutura usada como fonte de status do painel. Não cria estruturas, não executa cálculos e não altera dados.</p><?php if ($error): ?><div class="notice"><?= reforma_tributaria_h($error) ?></div><?php elseif ($diagnostic): ?><div class="grid"><div class="card"><small>Itens verificados</small><div class="number"><?= count($all) ?></div></div><div class="card"><small>Disponíveis</small><div class="number ok"><?= reforma_tributaria_technical_count($all, 'present') ?></div></div><div class="card"><small>Com atenção</small><div class="number warn"><?= reforma_tributaria_technical_count($all, 'absent') + reforma_tributaria_technical_count($all, 'unverified') ?></div></div></div><p>Consulta realizada em <?= reforma_tributaria_h($diagnostic['checked_at']) ?>. Os detalhes de infraestrutura permanecem restritos a esta área de apoio e não são exibidos na visão inicial.</p><?php endif; ?><p><a href="<?= reforma_tributaria_h(reforma_tributaria_url()) ?>">Voltar ao painel de preparação</a></p></div></main></body></html>
