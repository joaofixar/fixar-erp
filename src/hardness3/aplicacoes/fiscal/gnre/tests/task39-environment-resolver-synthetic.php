<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/EnvironmentResolver.php';
require_once __DIR__ . '/../src/ConfigUfClient.php';
require_once __DIR__ . '/../src/ReceptionClient.php';
require_once __DIR__ . '/../src/ResultClient.php';

function t39(bool $condition, string $message): void { if (!$condition) throw new RuntimeException('Task 39: '.$message); }
function t39rejects(callable $operation, string $message): void {
    try { $operation(); } catch (RuntimeException $e) { return; }
    throw new RuntimeException('Task 39: '.$message);
}

t39(GnreEnvironmentResolver::validate('1') === '1' && GnreEnvironmentResolver::validate('2') === '2', 'resolvedor deve aceitar somente 1 e 2.');
t39(GnreEnvironmentResolver::label('1') === 'Produção' && GnreEnvironmentResolver::label('2') === 'Homologação', 'rótulos públicos incorretos.');
foreach (['1' => 'www.gnre.pe.gov.br', '2' => 'www.testegnre.pe.gov.br'] as $environment => $host) foreach (['config_uf', 'reception', 'result'] as $service) {
    $resolved = GnreEnvironmentResolver::service((string) $environment, $service);
    t39(parse_url($resolved['endpoint'], PHP_URL_HOST) === $host, 'endpoint inesperado para '.$service.' em '.$environment.'.');
}
foreach (['', '0', '3', 'https://externo.invalid'] as $invalid) t39rejects(static function () use ($invalid): void { GnreEnvironmentResolver::validate($invalid); }, 'ambiente inválido deve falhar sem aceitar configuração externa.');
t39rejects(static function (): void { GnreEnvironmentResolver::service('2', 'https://externo.invalid'); }, 'serviço externo não pode ser injetado.');

/* A guarda operacional agora apenas valida o ambiente do snapshot; Produção é válida. */
t39(GnreEnvironmentResolver::assertOperationalAllowed('1') === '1', 'Produção não pode ser bloqueada somente pelo valor 1.');
t39(GnreEnvironmentResolver::assertOperationalAllowed('2') === '2', 'Homologação deve continuar operacional.');

foreach (['ConfigUfClient.php', 'ReceptionClient.php', 'ResultClient.php'] as $file) {
    $source = file_get_contents(__DIR__ . '/../src/' . $file);
    t39(is_string($source) && strpos($source, 'GnreEnvironmentResolver::assertOperationalAllowed') !== false, $file.' deve validar o ambiente imutável antes da chamada.');
    t39(is_string($source) && strpos($source, 'curl_init($service[\'endpoint\'])') !== false, $file.' deve obter endpoint somente do resolvedor.');
}

echo "Task 39 environment resolver synthetic: OK\n";
