<?php
/**
 * Reconstrói o fluxo de arquivos PHP ofuscados com goto.
 * Uso: php referencia/deofuscar.php <origem> <destino>
 *
 * A saída é referência de leitura. O sistema legado não a carrega.
 */
declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Uso: php deofuscar.php <origem> <destino>\n");
    exit(1);
}

$srcPath = $argv[1];
$dstPath = $argv[2];
$src = file_get_contents($srcPath);
if ($src === false) {
    fwrite(STDERR, "Não leu {$srcPath}\n");
    exit(1);
}

$tokens = token_get_all($src);
$parser = new Parser($tokens);
$unit = $parser->parseFile();
$base = basename(str_replace('\\', '/', $srcPath));
$out = renderFile($unit, $base, $srcPath);
$dir = dirname($dstPath);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
file_put_contents($dstPath, $out);

$origFns = functionNames($src);
$newFns = functionNames($out);
$missing = array_values(array_diff($origFns, $newFns));
$gotosLeft = preg_match_all('/\bgoto\b/', $out);
fwrite(STDERR, basename($dstPath) . " funções=" . count($newFns) . " goto_restante={$gotosLeft}");
if ($missing) {
    fwrite(STDERR, " FALTANDO=" . implode(',', $missing));
}
fwrite(STDERR, "\n");

function functionNames(string $code): array
{
    preg_match_all('/\bfunction\s+([A-Za-z_\x80-\xff][A-Za-z0-9_\x80-\xff]*)\s*\(/', $code, $m);
    return $m[1];
}

function renderFile(array $unit, string $base, string $srcPath): string
{
    $rel = str_replace('\\', '/', $srcPath);
    $banner = <<<TXT
<?php
/**
 * {$base}
 *
 * Versão legível de {$rel}.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */

TXT;
    $body = '';
    if ($unit['namespace'] !== '') {
        $body .= "namespace {$unit['namespace']};\n\n";
    }
    foreach ($unit['classes'] as $class) {
        $body .= "class {$class['name']}\n{\n";
        foreach ($class['members'] as $member) {
            if ($member['kind'] === 'method') {
                $body .= renderMethod($member);
            } else {
                $body .= '    ' . rtrim($member['text']) . "\n\n";
            }
        }
        $body .= "}\n";
    }
    return $banner . $body;
}

function renderMethod(array $method): string
{
    $sig = $method['signature'];
    $body = structureStmts($method['body'], '        ');
    if ($body !== '' && !str_ends_with($body, "\n")) {
        $body .= "\n";
    }
    return "    {$sig}\n    {\n{$body}    }\n\n";
}

function structureStmts(array $stmts, string $indent): string
{
    $localGoto = false;
    foreach ($stmts as $s) {
        if ($s['kind'] === 'goto' || $s['kind'] === 'label') {
            $localGoto = true;
            break;
        }
    }
    if (!$localGoto) {
        return prettyStmts($stmts, $indent);
    }
    $cfg = buildCfg($stmts);
    $struct = new Structurer($cfg, $indent);
    return $struct->emit();
}

function prettyStmts(array $stmts, string $indent): string
{
    $out = '';
    foreach ($stmts as $s) {
        $out .= prettyStmt($s, $indent);
    }
    return $out;
}

function prettyStmt(array $s, string $indent): string
{
    $inner = $indent . '    ';
    switch ($s['kind']) {
        case 'stmt':
            return formatChunks($s['chunks'], $indent, $inner, true);
        case 'return':
            return formatChunks(array_merge([['tok', [T_RETURN, 'return', 0]]], $s['chunks']), $indent, $inner, true);
        case 'exit':
            return formatChunks(array_merge([['tok', [T_EXIT, $s['word'], 0]]], $s['chunks']), $indent, $inner, true);
        case 'throw':
            return formatChunks(array_merge([['tok', [T_THROW, 'throw', 0]]], $s['chunks']), $indent, $inner, true);
        case 'break':
            return $indent . ($s['arg'] !== '' ? 'break ' . $s['arg'] : 'break') . ";\n";
        case 'continue':
            return $indent . ($s['arg'] !== '' ? 'continue ' . $s['arg'] : 'continue') . ";\n";
        case 'goto':
            return $indent . "goto {$s['target']};\n";
        case 'label':
            return $indent . $s['name'] . ":\n";
        case 'if':
            $out = $indent . 'if (' . $s['cond'] . ") {\n";
            $out .= structureStmts($s['then'], $inner);
            $out .= $indent . '}';
            foreach ($s['elseifs'] as $ei) {
                $out .= ' elseif (' . $ei['cond'] . ") {\n";
                $out .= structureStmts($ei['body'], $inner);
                $out .= $indent . '}';
            }
            if ($s['else'] !== null) {
                $out .= " else {\n";
                $out .= structureStmts($s['else'], $inner);
                $out .= $indent . '}';
            }
            return $out . "\n";
        case 'foreach':
        case 'for':
        case 'while':
            $out = $indent . $s['kind'] . ' (' . $s['header'] . ") {\n";
            $out .= structureStmts($s['body'], $inner);
            return $out . $indent . "}\n";
        case 'do':
            $out = $indent . "do {\n";
            $out .= structureStmts($s['body'], $inner);
            return $out . $indent . '} while (' . $s['cond'] . ");\n";
        case 'switch':
            return renderSwitchLiteral($s, $indent);
        case 'try':
            $out = $indent . "try {\n";
            $out .= structureStmts($s['body'], $inner);
            $out .= $indent . '}';
            foreach ($s['catches'] as $catch) {
                $out .= ' catch (' . $catch['header'] . ") {\n";
                $out .= structureStmts($catch['body'], $inner);
                $out .= $indent . '}';
            }
            if ($s['finally'] !== null) {
                $out .= " finally {\n";
                $out .= structureStmts($s['finally'], $inner);
                $out .= $indent . '}';
            }
            return $out . "\n";
        default:
            return $indent . "/* stmt {$s['kind']} */\n";
    }
}

function renderSwitchLiteral(array $s, string $indent): string
{
    $inner = $indent . '    ';
    $deep = $inner . '    ';
    $out = $indent . 'switch (' . $s['cond'] . ") {\n";
    foreach ($s['cases'] as $case) {
        if ($case['value'] === null) {
            $out .= $inner . "default:\n";
        } else {
            $out .= $inner . 'case ' . $case['value'] . ":\n";
        }
        $body = structureStmts($case['stmts'], $deep);
        $defined = [];
        if (preg_match_all('/^\s*([A-Za-z_][A-Za-z0-9_]*)\s*:/m', $body, $lm)) {
            $defined = array_flip($lm[1]);
        }
        $body = preg_replace_callback(
            '/^(\s*)goto\s+([A-Za-z_][A-Za-z0-9_]*);\s*$/m',
            static function (array $m) use ($defined): string {
                if (isset($defined[$m[2]])) {
                    return $m[0];
                }
                return $m[1] . "break;\n";
            },
            $body
        );
        $out .= $body;
    }
    return $out . $indent . "}\n";
}

function formatChunks(array $chunks, string $indent, string $inner, bool $semi): string
{
    $hasBody = false;
    foreach ($chunks as $c) {
        if ($c[0] === 'body') {
            $hasBody = true;
            break;
        }
    }
    if (!$hasBody) {
        $text = glueChunks($chunks);
        if ($text === '') {
            return '';
        }
        return $indent . $text . ($semi ? ';' : '') . "\n";
    }
    $out = '';
    $line = $indent;
    $started = false;
    $lastPiece = '';
    foreach ($chunks as $c) {
        if ($c[0] === 'body') {
            $out .= rtrim($line) . " {\n";
            $out .= structureStmts($c[1], $inner);
            $line = $indent . '}';
            $started = true;
            $lastPiece = '}';
            continue;
        }
        $piece = $c[0] === 'raw' ? $c[1] : tokText($c[1]);
        if ($c[0] === 'tok' && is_array($c[1]) && $c[1][0] === T_WHITESPACE) {
            continue;
        }
        if ($piece === '') {
            continue;
        }
        if ($lastPiece !== '' && needsSpace($lastPiece, $piece)) {
            $line .= ' ';
        }
        $line .= $piece;
        $started = true;
        $lastPiece = $piece;
    }
    $out .= $line . ($semi ? ';' : '') . "\n";
    return $out;
}

function glueChunks(array $chunks): string
{
    $tokens = [];
    foreach ($chunks as $c) {
        if ($c[0] === 'raw') {
            $tokens[] = $c[1];
        } elseif ($c[0] === 'tok') {
            $tokens[] = tokText($c[1]);
        }
    }
    return gluePieces($tokens);
}

function gluePieces(array $pieces): string
{
    $out = '';
    $last = '';
    foreach ($pieces as $piece) {
        if ($piece === '' || trim($piece) === '') {
            continue;
        }
        if ($last !== '' && needsSpace($last, $piece)) {
            $out .= ' ';
        }
        $out .= $piece;
        $last = $piece;
    }
    return $out;
}

function needsSpace(string $prev, string $next): bool
{
    if ($prev === '' || $next === '') {
        return false;
    }
    if (in_array($next, [';', ',', ')', ']'], true)) {
        return false;
    }
    if (in_array($prev, ['(', '[', '!', '~', '@', '->', '::'], true)) {
        return false;
    }
    if (in_array($next, ['->', '::'], true)) {
        return false;
    }
    $ops = ['=', '==', '===', '!=', '!==', '<>', '=>', '&&', '||', '?', ':', '+', '-', '*', '/', '%', '<', '>', '<=', '>=', '.=', '+=', '-=', '*=', '/=', '%=', '.', 'and', 'or', 'xor'];
    if ($next === '-' && (in_array($prev, $ops, true) || in_array($prev, ['(', ',', '['], true))) {
        return !in_array($prev, ['(', '['], true);
    }
    if (in_array($prev, $ops, true) || in_array($next, $ops, true)) {
        return true;
    }
    $identStart = static fn(string $s): bool => (bool) preg_match('/^[A-Za-z0-9_$\\\\"\']/', $s);
    $identEnd = static fn(string $s): bool => (bool) preg_match('/[A-Za-z0-9_$]$/', $s);
    if ($identEnd($prev) && $identStart($next)) {
        return true;
    }
    if (($prev === ')' || $prev === ']') && $identStart($next)) {
        return true;
    }
    if ($next === '&' && $identEnd($prev)) {
        return true;
    }
    if ($prev === '&') {
        return false;
    }
    if ($prev === ',') {
        return true;
    }
    return false;
}

function tokText($t): string
{
    return is_array($t) ? $t[1] : $t;
}

function tokId($t): ?int
{
    return is_array($t) ? $t[0] : null;
}

function buildCfg(array $stmts): array
{
    $n = count($stmts);
    $labels = [];
    foreach ($stmts as $s) {
        if ($s['kind'] === 'label') {
            $labels[$s['name']] = true;
        }
    }
    $isLeader = array_fill(0, max($n, 1), false);
    if ($n > 0) {
        $isLeader[0] = true;
    }
    $i = 0;
    while ($i < $n) {
        $s = $stmts[$i];
        if ($s['kind'] === 'label') {
            $isLeader[$i] = true;
            $i++;
            continue;
        }
        $branch = classifyBranch($s, $labels);
        if ($branch !== null) {
            $i++;
            if (($branch['consumeGoto'] ?? false) && $i < $n && $stmts[$i]['kind'] === 'goto') {
                $i++;
            }
            if ($i < $n) {
                $isLeader[$i] = true;
            }
            continue;
        }
        if (in_array($s['kind'], ['goto', 'return', 'break', 'continue', 'throw', 'exit'], true)) {
            $i++;
            if ($i < $n) {
                $isLeader[$i] = true;
            }
            continue;
        }
        $i++;
    }

    $leaderIdx = [];
    for ($i = 0; $i < $n; $i++) {
        if ($isLeader[$i]) {
            $leaderIdx[] = $i;
        }
    }
    $blocks = [];
    $alias = [];
    $order = [];
    foreach ($leaderIdx as $k => $start) {
        $end = $leaderIdx[$k + 1] ?? $n;
        $labelsHere = [];
        $code = [];
        $term = ['type' => 'end'];
        $j = $start;
        while ($j < $end) {
            $s = $stmts[$j];
            if ($s['kind'] === 'label') {
                $labelsHere[] = $s['name'];
                $j++;
                continue;
            }
            $branch = classifyBranch($s, $labels);
            if ($branch !== null) {
                $j++;
                $false = $branch['false'];
                if ($branch['consumeGoto'] && $j < $end && $stmts[$j]['kind'] === 'goto') {
                    $false = $stmts[$j]['target'];
                    $j++;
                }
                $term = $branch['term'];
                $term['false'] = $false;
                break;
            }
            if ($s['kind'] === 'goto') {
                $term = ['type' => 'goto', 'target' => $s['target']];
                $j++;
                break;
            }
            if (in_array($s['kind'], ['return', 'break', 'continue', 'throw', 'exit'], true)) {
                $term = ['type' => $s['kind'], 'stmt' => $s];
                $j++;
                break;
            }
            $code[] = $s;
            $j++;
        }
        $id = $labelsHere[0] ?? ('@' . $start);
        if ($term['type'] === 'end' && isset($leaderIdx[$k + 1])) {
            $term = ['type' => 'fall', 'target' => null, 'nextIndex' => $k + 1];
        }
        if ($term['type'] === 'branch' && ($term['false'] ?? null) === '@fall') {
            $term['false'] = null;
            $term['falseNext'] = $k + 1;
        }
        $blocks[$id] = ['code' => $code, 'term' => $term, 'labels' => $labelsHere];
        $order[] = $id;
        $alias['@' . $start] = $id;
        foreach ($labelsHere as $name) {
            $alias[$name] = $id;
        }
    }
    foreach ($blocks as $id => &$block) {
        $term = &$block['term'];
        if ($term['type'] === 'fall' && $term['target'] === null) {
            $term['target'] = $order[$term['nextIndex']] ?? null;
        }
        if ($term['type'] === 'branch' && array_key_exists('falseNext', $term) && $term['false'] === null) {
            $term['false'] = $order[$term['falseNext']] ?? null;
        }
        unset($term);
    }
    unset($block);
    return [
        'entry' => $order[0] ?? null,
        'blocks' => $blocks,
        'alias' => $alias,
        'order' => $order,
    ];
}

/**
 * if (cond) { goto T; } [goto F]
 * ou switch cujos cases só desviam para rótulos deste nível.
 */
function classifyBranch(array $s, array $parentLabels): ?array
{
    if ($s['kind'] === 'if' && $s['elseifs'] === []) {
        $true = onlyGoto($s['then']);
        if ($true === null) {
            return null;
        }
        if ($s['else'] !== null) {
            $false = onlyGoto($s['else']);
            if ($false === null) {
                return null;
            }
            return [
                'consumeGoto' => false,
                'term' => ['type' => 'branch', 'cond' => $s['cond'], 'true' => $true, 'false' => $false, 'negated' => false],
                'false' => $false,
            ];
        }
        return [
            'consumeGoto' => true,
            'term' => ['type' => 'branch', 'cond' => $s['cond'], 'true' => $true, 'false' => '@fall', 'negated' => false],
            'false' => '@fall',
        ];
    }
    if ($s['kind'] === 'switch') {
        $arms = [];
        $pending = [];
        foreach ($s['cases'] as $case) {
            $straight = straightToGoto($case['stmts'], $parentLabels);
            if ($straight === null) {
                return null;
            }
            if ($straight['target'] === null) {
                $pending[] = $case['value'];
                continue;
            }
            $values = $pending;
            $values[] = $case['value'];
            $pending = [];
            $arms[] = ['values' => $values, 'code' => $straight['code'], 'target' => $straight['target']];
        }
        if ($pending) {
            return null;
        }
        return [
            'consumeGoto' => true,
            'term' => ['type' => 'switch', 'cond' => $s['cond'], 'arms' => $arms],
            'false' => '@fall',
        ];
    }
    return null;
}

function onlyGoto(array $body): ?string
{
    $goto = null;
    foreach ($body as $s) {
        if ($s['kind'] === 'label' || $s['kind'] === 'empty') {
            continue;
        }
        if ($s['kind'] === 'goto') {
            if ($goto !== null) {
                return null;
            }
            $goto = $s['target'];
            continue;
        }
        return null;
    }
    return $goto;
}

function straightToGoto(array $body, array $parentLabels): ?array
{
    $code = [];
    $target = null;
    foreach ($body as $s) {
        if ($s['kind'] === 'label') {
            return null;
        }
        if ($s['kind'] === 'goto') {
            if ($target !== null || !isset($parentLabels[$s['target']])) {
                return null;
            }
            $target = $s['target'];
            continue;
        }
        if ($target !== null) {
            return null;
        }
        if (in_array($s['kind'], ['if', 'switch', 'foreach', 'for', 'while', 'try'], true)) {
            return null;
        }
        $code[] = $s;
    }
    return ['code' => $code, 'target' => $target];
}

final class Structurer
{
    private array $blocks;
    private array $alias;
    private ?string $entry;
    private string $indent;
    private array $emitted = [];
    private int $guard = 0;

    public function __construct(array $cfg, string $indent)
    {
        $this->blocks = $cfg['blocks'];
        $this->alias = $cfg['alias'];
        $this->entry = $cfg['entry'];
        $this->indent = $indent;
    }

    public function emit(): string
    {
        if ($this->entry === null) {
            return '';
        }
        return $this->region($this->entry, [], $this->indent);
    }

    private function resolve(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }
        return $this->alias[$name] ?? $name;
    }

    private function block(string $id): ?array
    {
        $id = $this->resolve($id) ?? $id;
        return $this->blocks[$id] ?? null;
    }

    private function succs(string $id): array
    {
        $b = $this->block($id);
        if ($b === null) {
            return [];
        }
        $t = $b['term'];
        if ($t['type'] === 'goto' || $t['type'] === 'fall') {
            $n = $this->resolve($t['target']);
            return $n ? [$n] : [];
        }
        if ($t['type'] === 'branch') {
            $out = [];
            foreach (['true', 'false'] as $side) {
                $n = $this->resolve($t[$side] ?? null);
                if ($n) {
                    $out[] = $n;
                }
            }
            return $out;
        }
        if ($t['type'] === 'switch') {
            $out = [];
            foreach ($t['arms'] as $arm) {
                $n = $this->resolve($arm['target']);
                if ($n) {
                    $out[] = $n;
                }
            }
            if (!empty($t['false'])) {
                $n = $this->resolve($t['false']);
                if ($n) {
                    $out[] = $n;
                }
            }
            return $out;
        }
        return [];
    }

    private function distMap(?string $start, array $blockers): array
    {
        if ($start === null) {
            return [];
        }
        $start = $this->resolve($start);
        $dist = [$start => 0];
        $queue = [[$start, 0]];
        while ($queue) {
            [$n, $d] = array_shift($queue);
            if ($d > 0 && isset($blockers[$n])) {
                continue;
            }
            foreach ($this->succs($n) as $s) {
                if (isset($dist[$s])) {
                    continue;
                }
                if (isset($blockers[$s])) {
                    $dist[$s] = $d + 1;
                    continue;
                }
                $dist[$s] = $d + 1;
                $queue[] = [$s, $d + 1];
            }
        }
        return $dist;
    }

    private function canReach(?string $from, string $target, array $blockers): bool
    {
        $map = $this->distMap($from, $blockers);
        $target = $this->resolve($target) ?? $target;
        return isset($map[$target]);
    }

    private function findJoin(?string $a, ?string $b): ?string
    {
        $a = $this->resolve($a);
        $b = $this->resolve($b);
        if ($a === null) {
            return $b;
        }
        if ($b === null) {
            return $a;
        }
        if ($a === $b) {
            return $a;
        }
        $da = $this->distMap($a, []);
        $db = $this->distMap($b, []);
        $best = null;
        $bestScore = PHP_INT_MAX;
        foreach ($da as $n => $d1) {
            if (!isset($db[$n])) {
                continue;
            }
            $score = $d1 + $db[$n];
            if ($score < $bestScore) {
                $bestScore = $score;
                $best = $n;
            }
        }
        return $best;
    }

    private function findJoinMany(array $nodes): ?string
    {
        $nodes = array_values(array_filter(array_map(fn($n) => $this->resolve($n), $nodes)));
        if (!$nodes) {
            return null;
        }
        $maps = [];
        foreach ($nodes as $n) {
            $maps[] = $this->distMap($n, []);
        }
        $best = null;
        $bestScore = PHP_INT_MAX;
        foreach ($maps[0] as $n => $d) {
            $sum = 0;
            $ok = true;
            foreach ($maps as $map) {
                if (!isset($map[$n])) {
                    $ok = false;
                    break;
                }
                $sum += $map[$n];
            }
            if ($ok && $sum < $bestScore) {
                $bestScore = $sum;
                $best = $n;
            }
        }
        return $best;
    }

    private function region(?string $id, array $stops, string $indent): string
    {
        if ($id === null) {
            return '';
        }
        $id = $this->resolve($id) ?? $id;
        if (isset($stops[$id])) {
            return '';
        }
        if (!isset($this->blocks[$id])) {
            return $indent . "goto {$id};\n";
        }
        if (isset($this->emitted[$id])) {
            return '';
        }
        if (++$this->guard > 20000) {
            return $indent . "/* fluxo interrompido em {$id} */\n";
        }
        $b = $this->blocks[$id];
        $term = $b['term'];
        if ($term['type'] === 'branch') {
            $loop = $this->emitLoop($id, $b, $stops, $indent);
            if ($loop !== null) {
                return $loop;
            }
        }
        $this->emitted[$id] = true;
        $out = $this->renderCode($b['code'], $indent);
        if ($term['type'] === 'goto' || $term['type'] === 'fall') {
            $out .= $this->region($term['target'], $stops, $indent);
            return $out;
        }
        if (in_array($term['type'], ['return', 'break', 'continue', 'throw', 'exit'], true)) {
            $out .= prettyStmt($term['stmt'], $indent);
            return $out;
        }
        if ($term['type'] === 'end') {
            return $out;
        }
        if ($term['type'] === 'branch') {
            $t = $this->resolve($term['true']);
            $f = $this->resolve($term['false']);
            $join = $this->findJoin($t, $f);
            $branchStops = $stops;
            if ($join !== null) {
                $branchStops[$join] = true;
            }
            $then = $this->region($t, $branchStops, $indent . '    ');
            $else = $this->region($f, $branchStops, $indent . '    ');
            if ($t === $join) {
                $out .= $indent . 'if (' . negateCond($term['cond']) . ") {\n";
                $out .= $else;
                $out .= $indent . "}\n";
            } elseif ($f === $join || $else === '') {
                $out .= $indent . 'if (' . $term['cond'] . ") {\n";
                $out .= $then;
                $out .= $indent . "}\n";
            } else {
                $out .= $indent . 'if (' . $term['cond'] . ") {\n";
                $out .= $then;
                $out .= $indent . "} else {\n";
                $out .= $else;
                $out .= $indent . "}\n";
            }
            if ($join !== null) {
                $out .= $this->region($join, $stops, $indent);
            }
            return $out;
        }
        if ($term['type'] === 'switch') {
            return $out . $this->emitSwitch($term, $stops, $indent);
        }
        return $out;
    }

    private function emitLoop(string $id, array $b, array $stops, string $indent): ?string
    {
        $term = $b['term'];
        $t = $this->resolve($term['true']);
        $f = $this->resolve($term['false']);
        $trueLoops = $t !== null && $this->canReach($t, $id, $f ? [$f => true] : []);
        $falseLoops = $f !== null && $this->canReach($f, $id, $t ? [$t => true] : []);
        if ($trueLoops === $falseLoops) {
            return null;
        }
        $this->emitted[$id] = true;
        $exit = $trueLoops ? $f : $t;
        $bodyEntry = $trueLoops ? $t : $f;
        $loopStops = $stops;
        $loopStops[$id] = true;
        if ($exit !== null) {
            $loopStops[$exit] = true;
        }
        $header = $this->renderCode($b['code'], $indent . '    ');
        $out = '';
        if (trim($header) === '') {
            $cond = $trueLoops ? $term['cond'] : negateCond($term['cond']);
            $out .= $indent . 'while (' . $cond . ") {\n";
            $out .= $this->region($bodyEntry, $loopStops, $indent . '    ');
            $out .= $indent . "}\n";
        } else {
            $breakCond = $trueLoops ? negateCond($term['cond']) : $term['cond'];
            $out .= $indent . "while (true) {\n";
            $out .= $header;
            $out .= $indent . '    if (' . $breakCond . ") {\n";
            $out .= $indent . "        break;\n";
            $out .= $indent . "    }\n";
            $out .= $this->region($bodyEntry, $loopStops, $indent . '    ');
            $out .= $indent . "}\n";
        }
        if ($exit !== null) {
            $out .= $this->region($exit, $stops, $indent);
        }
        return $out;
    }

    private function emitSwitch(array $term, array $stops, string $indent): string
    {
        $targets = [];
        foreach ($term['arms'] as $arm) {
            $targets[] = $arm['target'];
        }
        if (!empty($term['false'])) {
            $targets[] = $term['false'];
        }
        $join = $this->findJoinMany($targets);
        $branchStops = $stops;
        if ($join !== null) {
            $branchStops[$join] = true;
        }
        $expr = $term['cond'];
        $out = '';
        $count = count($term['arms']);
        foreach ($term['arms'] as $i => $arm) {
            $tests = [];
            foreach ($arm['values'] as $value) {
                if ($value === null) {
                    continue;
                }
                $tests[] = '(' . $expr . ' == ' . $value . ')';
            }
            $body = $this->renderCode($arm['code'], $indent . '    ');
            $body .= $this->region($arm['target'], $branchStops, $indent . '    ');
            $isElse = $tests === [];
            if ($i === 0 && !$isElse) {
                $out .= $indent . 'if (' . implode(' || ', $tests) . ") {\n";
            } elseif ($isElse) {
                $out .= $indent . "} else {\n";
            } else {
                $out .= $indent . '} elseif (' . implode(' || ', $tests) . ") {\n";
            }
            $out .= $body;
            if ($i === $count - 1) {
                $out .= $indent . "}\n";
            }
        }
        if ($count === 0) {
            return $this->region($term['false'] ?? null, $stops, $indent);
        }
        if ($join !== null) {
            $out .= $this->region($join, $stops, $indent);
        }
        return $out;
    }

    private function renderCode(array $code, string $indent): string
    {
        return prettyStmts($code, $indent);
    }
}

function negateCond(string $cond): string
{
    $c = trim($cond);
    if ($c !== '' && !str_starts_with($c, '!=') && str_starts_with($c, '!')) {
        $rest = trim(substr($c, 1));
        if (str_starts_with($rest, '(') && str_ends_with($rest, ')') && balancedWrap($rest)) {
            return trim(substr($rest, 1, -1));
        }
        if (preg_match('/^\$[A-Za-z_][A-Za-z0-9_]*$/', $rest)) {
            return $rest;
        }
    }
    if (preg_match('/^\$[A-Za-z_][A-Za-z0-9_]*$/', $c)) {
        return '!' . $c;
    }
    return '!(' . $c . ')';
}

function balancedWrap(string $wrapped): bool
{
    $inner = substr($wrapped, 1, -1);
    $depth = 0;
    $len = strlen($inner);
    for ($i = 0; $i < $len; $i++) {
        $ch = $inner[$i];
        if ($ch === '(') {
            $depth++;
        } elseif ($ch === ')') {
            $depth--;
            if ($depth < 0) {
                return false;
            }
        }
    }
    return $depth === 0;
}

final class Parser
{
    private array $tokens;
    private int $i = 0;
    private int $n;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->n = count($tokens);
    }

    public function parseFile(): array
    {
        $namespace = '';
        $classes = [];
        while ($this->i < $this->n) {
            $this->skip();
            if ($this->i >= $this->n) {
                break;
            }
            $id = tokId($this->tokens[$this->i]);
            if ($id === T_NAMESPACE) {
                $this->i++;
                $this->skip();
                $namespace = '';
                while ($this->i < $this->n && tokText($this->tokens[$this->i]) !== ';' && tokText($this->tokens[$this->i]) !== '{') {
                    $namespace .= tokText($this->tokens[$this->i]);
                    $this->i++;
                }
                $namespace = trim($namespace);
                if ($this->text() === ';') {
                    $this->i++;
                }
                continue;
            }
            if ($id === T_CLASS) {
                $classes[] = $this->parseClass();
                continue;
            }
            $this->i++;
        }
        return ['namespace' => $namespace, 'classes' => $classes];
    }

    private function parseClass(): array
    {
        $this->expectId(T_CLASS);
        $this->skip();
        $name = tokText($this->tokens[$this->i]);
        $this->i++;
        $this->skip();
        if (tokId($this->tokens[$this->i] ?? null) === T_EXTENDS) {
            $name .= ' extends';
            $this->i++;
            while ($this->i < $this->n && $this->text() !== '{') {
                $piece = tokText($this->tokens[$this->i]);
                if (trim($piece) !== '') {
                    $name .= ' ' . trim($piece);
                }
                $this->i++;
            }
        }
        $this->expect('{');
        $members = [];
        while ($this->i < $this->n) {
            $this->skip();
            if ($this->text() === '}') {
                $this->i++;
                break;
            }
            if ($this->looksLikeMethod()) {
                $members[] = $this->parseMethod();
            } else {
                $chunks = $this->readChunksUntilSemi();
                $members[] = ['kind' => 'prop', 'text' => glueChunks($chunks) . ';'];
            }
        }
        return ['name' => trim($name), 'members' => $members];
    }

    private function looksLikeMethod(): bool
    {
        $j = $this->i;
        $paren = 0;
        while ($j < $this->n) {
            $t = $this->tokens[$j];
            $text = tokText($t);
            $id = tokId($t);
            if ($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT) {
                $j++;
                continue;
            }
            if ($text === '(') {
                $paren++;
            } elseif ($text === ')') {
                $paren--;
            } elseif ($text === ';' && $paren === 0) {
                return false;
            } elseif ($text === '{' && $paren === 0) {
                return false;
            } elseif ($id === T_FUNCTION && $paren === 0) {
                return true;
            }
            $j++;
        }
        return false;
    }

    private function parseMethod(): array
    {
        $sig = [];
        $paren = 0;
        while ($this->i < $this->n) {
            $t = $this->tokens[$this->i];
            $text = tokText($t);
            $id = tokId($t);
            if ($text === '(') {
                $paren++;
            } elseif ($text === ')') {
                $paren--;
            }
            if ($text === '{' && $paren === 0) {
                break;
            }
            if (!($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT)) {
                $sig[] = $text;
            }
            $this->i++;
        }
        $this->expect('{');
        $body = $this->parseBody();
        return ['kind' => 'method', 'signature' => gluePieces($sig), 'body' => $body];
    }

    private function parseBody(): array
    {
        $stmts = [];
        while ($this->i < $this->n) {
            $this->skip();
            if ($this->i >= $this->n || $this->text() === '}') {
                break;
            }
            $before = $this->i;
            $stmt = $this->parseStmt();
            if ($stmt === null) {
                break;
            }
            if ($this->i === $before) {
                throw new RuntimeException('Parser parado na linha ' . $this->line());
            }
            if ($stmt['kind'] !== 'empty') {
                $stmts[] = $stmt;
            }
        }
        $this->expect('}');
        return $stmts;
    }

    private function parseStmt(): ?array
    {
        $this->skip();
        if ($this->i >= $this->n || $this->text() === '}') {
            return null;
        }
        $t = $this->tokens[$this->i];
        $id = tokId($t);
        $text = tokText($t);
        if ($this->isLabelStart()) {
            $name = $text;
            $this->i++;
            $this->skip();
            $this->expect(':');
            return ['kind' => 'label', 'name' => $name];
        }
        if ($id === T_GOTO) {
            $this->i++;
            $this->skip();
            $name = tokText($this->tokens[$this->i]);
            $this->i++;
            $this->skip();
            $this->expect(';');
            return ['kind' => 'goto', 'target' => $name];
        }
        if ($id === T_IF) {
            return $this->parseIf();
        }
        if ($id === T_FOREACH || $id === T_FOR || $id === T_WHILE) {
            $kind = $id === T_FOREACH ? 'foreach' : ($id === T_FOR ? 'for' : 'while');
            $this->i++;
            $header = $this->readParen();
            $body = $this->parseBraceOrStmt();
            return ['kind' => $kind, 'header' => $header, 'body' => $body];
        }
        if ($id === T_DO) {
            $this->i++;
            $body = $this->parseBraceOrStmt();
            $this->skip();
            $this->expectId(T_WHILE);
            $cond = $this->readParen();
            $this->skip();
            $this->expect(';');
            return ['kind' => 'do', 'body' => $body, 'cond' => $cond];
        }
        if ($id === T_SWITCH) {
            return $this->parseSwitch();
        }
        if ($id === T_TRY) {
            return $this->parseTry();
        }
        if ($id === T_CASE) {
            $this->i++;
            $value = $this->readUntilColon();
            return ['kind' => 'case', 'value' => trim($value)];
        }
        if ($id === T_DEFAULT) {
            $this->i++;
            $this->skip();
            $this->expect(':');
            return ['kind' => 'default'];
        }
        if ($id === T_BREAK || $id === T_CONTINUE) {
            $word = $id === T_BREAK ? 'break' : 'continue';
            $this->i++;
            $arg = '';
            $this->skip();
            if ($this->text() !== ';') {
                $chunks = $this->readChunksUntilSemi();
                return ['kind' => $word, 'arg' => glueChunks($chunks)];
            }
            $this->expect(';');
            return ['kind' => $word, 'arg' => $arg];
        }
        if ($id === T_RETURN) {
            $this->i++;
            $this->skip();
            if ($this->text() === ';') {
                $this->i++;
                return ['kind' => 'return', 'chunks' => []];
            }
            $chunks = $this->readChunksUntilSemi();
            return ['kind' => 'return', 'chunks' => $chunks];
        }
        if ($id === T_THROW) {
            $this->i++;
            $chunks = $this->readChunksUntilSemi();
            return ['kind' => 'throw', 'chunks' => $chunks];
        }
        if ($id === T_EXIT || $text === 'exit' || $text === 'die') {
            $word = $text === 'die' ? 'die' : 'exit';
            $this->i++;
            $this->skip();
            $chunks = [];
            if ($this->text() === '(') {
                $chunks[] = ['raw', '(' . $this->readParen() . ')'];
                $this->skip();
                $this->expect(';');
            } elseif ($this->text() !== ';') {
                $chunks = $this->readChunksUntilSemi();
            } else {
                $this->expect(';');
            }
            return ['kind' => 'exit', 'word' => $word, 'chunks' => $chunks];
        }
        if ($text === ';') {
            $this->i++;
            return ['kind' => 'empty'];
        }
        $chunks = $this->readChunksUntilSemi();
        return ['kind' => 'stmt', 'chunks' => $chunks];
    }

    private function parseIf(): array
    {
        $this->expectId(T_IF);
        $cond = $this->readParen();
        $then = $this->parseBraceOrStmt();
        $elseifs = [];
        $else = null;
        while (true) {
            $this->skip();
            $id = tokId($this->tokens[$this->i] ?? null);
            if ($id === T_ELSEIF) {
                $this->i++;
                $c = $this->readParen();
                $elseifs[] = ['cond' => $c, 'body' => $this->parseBraceOrStmt()];
                continue;
            }
            if ($id === T_ELSE) {
                $this->i++;
                $else = $this->parseBraceOrStmt();
            }
            break;
        }
        return ['kind' => 'if', 'cond' => $cond, 'then' => $then, 'elseifs' => $elseifs, 'else' => $else];
    }

    private function parseSwitch(): array
    {
        $this->expectId(T_SWITCH);
        $cond = $this->readParen();
        $this->skip();
        $this->expect('{');
        $cases = [];
        $current = null;
        while ($this->i < $this->n) {
            $this->skip();
            if ($this->text() === '}') {
                break;
            }
            $id = tokId($this->tokens[$this->i]);
            if ($id === T_CASE || $id === T_DEFAULT) {
                $stmt = $this->parseStmt();
                if ($current !== null) {
                    $cases[] = $current;
                }
                $current = [
                    'value' => $stmt['kind'] === 'case' ? $stmt['value'] : null,
                    'stmts' => [],
                ];
                continue;
            }
            $stmt = $this->parseStmt();
            if ($stmt === null) {
                break;
            }
            if ($current === null) {
                $current = ['value' => null, 'stmts' => []];
            }
            if ($stmt['kind'] !== 'empty') {
                $current['stmts'][] = $stmt;
            }
        }
        if ($current !== null) {
            $cases[] = $current;
        }
        $this->expect('}');
        return ['kind' => 'switch', 'cond' => $cond, 'cases' => $cases];
    }

    private function parseTry(): array
    {
        $this->expectId(T_TRY);
        $body = $this->parseBrace();
        $catches = [];
        $finally = null;
        while (true) {
            $this->skip();
            $id = tokId($this->tokens[$this->i] ?? null);
            if ($id === T_CATCH) {
                $this->i++;
                $header = $this->readParen();
                $catches[] = ['header' => $header, 'body' => $this->parseBrace()];
                continue;
            }
            if ($id === T_FINALLY) {
                $this->i++;
                $finally = $this->parseBrace();
            }
            break;
        }
        return ['kind' => 'try', 'body' => $body, 'catches' => $catches, 'finally' => $finally];
    }

    private function parseBraceOrStmt(): array
    {
        $this->skip();
        if ($this->text() === '{') {
            return $this->parseBrace();
        }
        $stmt = $this->parseStmt();
        return $stmt === null ? [] : [$stmt];
    }

    private function parseBrace(): array
    {
        $this->expect('{');
        return $this->parseBody();
    }

    private function readChunksUntilSemi(): array
    {
        $chunks = [];
        $paren = 0;
        $bracket = 0;
        while ($this->i < $this->n) {
            $t = $this->tokens[$this->i];
            $id = tokId($t);
            $text = tokText($t);
            if ($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT) {
                $this->i++;
                continue;
            }
            if ($id === T_START_HEREDOC) {
                $chunks[] = ['raw', $this->slurpHeredoc()];
                continue;
            }
            if ($text === '"') {
                $chunks[] = ['raw', $this->slurpDq()];
                continue;
            }
            if ($text === '{' && $bracket === 0 && ($paren === 0 || $this->prevSolid() === ')' || $this->prevSolid() === '$' || $this->prevSolid() === '->')) {
                $prev = $this->prevSolid();
                if ($prev === '$' || $prev === '->') {
                    $chunks[] = ['tok', $t];
                    $this->i++;
                    $depth = 1;
                    while ($this->i < $this->n && $depth > 0) {
                        $t2 = $this->tokens[$this->i];
                        $id2 = tokId($t2);
                        $tx2 = tokText($t2);
                        if ($id2 === T_START_HEREDOC) {
                            $chunks[] = ['raw', $this->slurpHeredoc()];
                            continue;
                        }
                        if ($tx2 === '"') {
                            $chunks[] = ['raw', $this->slurpDq()];
                            continue;
                        }
                        if ($tx2 === '{') {
                            $depth++;
                        } elseif ($tx2 === '}') {
                            $depth--;
                        } elseif ($tx2 === '(') {
                            $paren++;
                        } elseif ($tx2 === ')') {
                            $paren--;
                        } elseif ($tx2 === '[') {
                            $bracket++;
                        } elseif ($tx2 === ']') {
                            $bracket--;
                        }
                        $chunks[] = ['tok', $t2];
                        $this->i++;
                    }
                    continue;
                }
                $this->i++;
                $chunks[] = ['body', $this->parseBody()];
                continue;
            }
            if ($text === ';' && $paren === 0 && $bracket === 0) {
                $this->i++;
                break;
            }
            if ($text === '(') {
                $paren++;
            } elseif ($text === ')') {
                $paren--;
            } elseif ($text === '[') {
                $bracket++;
            } elseif ($text === ']') {
                $bracket--;
            }
            $chunks[] = ['tok', $t];
            $this->i++;
        }
        return $chunks;
    }

    private function readParen(): string
    {
        $this->skip();
        $this->expect('(');
        $depth = 1;
        $pieces = [];
        while ($this->i < $this->n && $depth > 0) {
            $t = $this->tokens[$this->i];
            $text = tokText($t);
            $id = tokId($t);
            if ($id === T_START_HEREDOC) {
                $pieces[] = $this->slurpHeredoc();
                continue;
            }
            if ($text === '"') {
                $pieces[] = $this->slurpDq();
                continue;
            }
            if ($text === '(') {
                $depth++;
            } elseif ($text === ')') {
                $depth--;
                if ($depth === 0) {
                    $this->i++;
                    break;
                }
            }
            if (!($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT)) {
                $pieces[] = $text;
            }
            $this->i++;
        }
        return gluePieces($pieces);
    }

    private function readUntilColon(): string
    {
        $paren = 0;
        $ternary = 0;
        $pieces = [];
        while ($this->i < $this->n) {
            $t = $this->tokens[$this->i];
            $text = tokText($t);
            $id = tokId($t);
            if ($id === T_START_HEREDOC) {
                $pieces[] = $this->slurpHeredoc();
                continue;
            }
            if ($text === '"') {
                $pieces[] = $this->slurpDq();
                continue;
            }
            if ($text === '(') {
                $paren++;
            } elseif ($text === ')') {
                $paren--;
            } elseif ($text === '?' && $paren === 0) {
                $ternary++;
            } elseif ($text === ':' && $paren === 0) {
                if ($ternary > 0) {
                    $ternary--;
                } else {
                    $this->i++;
                    break;
                }
            }
            if (!($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT)) {
                $pieces[] = $text;
            }
            $this->i++;
        }
        return gluePieces($pieces);
    }

    private function slurpHeredoc(): string
    {
        $raw = '';
        while ($this->i < $this->n) {
            $t = $this->tokens[$this->i];
            $raw .= tokText($t);
            $this->i++;
            if (tokId($t) === T_END_HEREDOC) {
                break;
            }
        }
        return $raw;
    }

    private function slurpDq(): string
    {
        $raw = '"';
        $this->i++;
        while ($this->i < $this->n) {
            $t = $this->tokens[$this->i];
            $raw .= tokText($t);
            $this->i++;
            if (tokText($t) === '"') {
                break;
            }
        }
        return $raw;
    }

    private function isLabelStart(): bool
    {
        $id = tokId($this->tokens[$this->i]);
        if ($id !== T_STRING) {
            return false;
        }
        $j = $this->i + 1;
        while ($j < $this->n && tokId($this->tokens[$j]) === T_WHITESPACE) {
            $j++;
        }
        return ($this->tokens[$j] ?? null) === ':';
    }

    private function skip(): void
    {
        while ($this->i < $this->n) {
            $id = tokId($this->tokens[$this->i]);
            if ($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT) {
                $this->i++;
                continue;
            }
            break;
        }
    }

    private function prevSolid(): string
    {
        for ($j = $this->i - 1; $j >= 0; $j--) {
            $id = tokId($this->tokens[$j]);
            if ($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT) {
                continue;
            }
            return tokText($this->tokens[$j]);
        }
        return '';
    }

    private function text(): string
    {
        if ($this->i >= $this->n) {
            return '';
        }
        return tokText($this->tokens[$this->i]);
    }

    private function expect(string $char): void
    {
        $this->skip();
        if ($this->text() !== $char) {
            throw new RuntimeException("Esperava {$char} na linha " . $this->line() . ", achei " . $this->text());
        }
        $this->i++;
    }

    private function expectId(int $id): void
    {
        $this->skip();
        if (tokId($this->tokens[$this->i] ?? null) !== $id) {
            throw new RuntimeException('Token inesperado na linha ' . $this->line());
        }
        $this->i++;
    }

    private function line(): int
    {
        $j = min($this->i, $this->n - 1);
        while ($j > 0 && !is_array($this->tokens[$j])) {
            $j--;
        }
        return is_array($this->tokens[$j]) ? $this->tokens[$j][2] : 0;
    }
}
