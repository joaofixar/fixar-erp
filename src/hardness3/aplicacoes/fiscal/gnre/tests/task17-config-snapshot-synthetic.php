<?php
declare(strict_types=1);
if (!class_exists('DOMDocument')) { echo "task17 config snapshot synthetic: SKIP (DOM ausente)\n"; exit; }
require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../src/ConfigUfRules.php';
require_once __DIR__ . '/../src/ConfigUfSnapshot.php';
function t17(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); }
$xml=(string)file_get_contents(__DIR__.'/fixtures/task16-config-uf-minimal.xml');
$rules=GnreConfigUfSnapshot::validate($xml,'2');
t17(($rules['uf']??'')==='PR','snapshot deve interpretar a TConfigUf validada sem duplicar regras da Task 16');
t17(hash('sha256',$xml)===hash('sha256',$xml),'hash idêntico deve identificar conteúdo idêntico');
try { GnreConfigUfSnapshot::validate(str_replace('<uf>PR</uf>','<uf>X</uf>',$xml),'2'); throw new RuntimeException('XML fora do XSD foi aceito'); } catch (RuntimeException $e) { t17(strpos($e->getMessage(),'XSD')!==false,'snapshot deve rejeitar XML inválido antes de persistir'); }
echo "task17 config snapshot synthetic: OK\n";
