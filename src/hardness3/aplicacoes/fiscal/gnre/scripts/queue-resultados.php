<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../src/ResultClient.php';
require_once __DIR__ . '/../src/QueueWorker.php';
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("Somente CLI.\n"); }
$limit=isset($argv[1])?(int)$argv[1]:10;
try { $result=(new GnreQueueWorker())->run($limit); echo json_encode($result,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n"; }
catch(Throwable $e) { fwrite(STDERR,'Worker GNRE: '.$e->getMessage()."\n"); exit(1); }
