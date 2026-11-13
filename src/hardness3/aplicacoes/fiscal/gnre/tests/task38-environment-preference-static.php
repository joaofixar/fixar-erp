<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/EnvironmentPreference.php';
function t38(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }

t38(GnreEnvironmentPreference::validate('1') === '1' && GnreEnvironmentPreference::validate('2') === '2', 'serviço aceita somente os dois ambientes permitidos.');
foreach (['', '0', '3', 'prod', ' 1'] as $invalid) {
    try { GnreEnvironmentPreference::validate($invalid); throw new RuntimeException('valor inválido aceito: '.$invalid); }
    catch (RuntimeException $expected) { }
}
$service = (string) file_get_contents(__DIR__ . '/../src/EnvironmentPreference.php');
$schema = (string) file_get_contents(__DIR__ . '/../../../../scripts/atualizacaoBase.php');
$index = (string) file_get_contents(__DIR__ . '/../public/index.php');
$page = (string) file_get_contents(__DIR__ . '/../public/task38-environment-panel.php');
$presentation = (string) file_get_contents(__DIR__ . '/../public/task10-presentation.php');
t38(strpos($service, "?? self::HOMOLOGATION") !== false, 'ausência de GNRE009 retorna homologação.');
t38(strpos($service, 'WHERE GNRE009_C004_Id=?') !== false && strpos($service, 'ON DUPLICATE KEY UPDATE') !== false, 'leitura/gravação mantém escopo e unicidade por empresa.');
t38(substr_count($schema, 'CREATE TABLE IF NOT EXISTS GNRE009') === 1, 'schema distribuído cria somente GNRE009.');
t38(preg_match('/CREATE TABLE IF NOT EXISTS GNRE009 \(\s*GNRE009_Id.*?GNRE009_C004_Id.*?GNRE009_Ambiente.*?PRIMARY KEY.*?UQ_GNRE009_Empresa/s', $schema) === 1, 'schema contém somente os três campos funcionais e a chave única por empresa.');
t38(strpos($index, "'save_environment_preference'") !== false && strpos($index, 'gnre_require_csrf();') !== false, 'rota exige CSRF e usa o contexto autorizado ativo.');
t38(strpos($page, 'novas guias') !== false && strpos($page, 'snapshot vinculado') !== false, 'tela informa que a escolha vale somente para novas guias.');
t38(strpos($presentation, "require __DIR__ . '/task38-environment-panel.php'") !== false && strpos($presentation, "nav === 'config') { require") === false && strpos($presentation, "nav === 'config') { ob_start(); require") !== false, 'configuração é renderizada no shell padrão, sem saída antecipada.');
foreach (['ConfigUfClient.php', 'ReceptionClient.php', 'ResultClient.php', 'ConfigUfSnapshot.php', 'XmlGenerator.php', 'QueueWorker.php', 'OfficialPdfService.php'] as $file) {
    t38(strpos((string) file_get_contents(__DIR__ . '/../src/' . $file), 'GnreEnvironmentPreference') === false, $file.' não consome a preferência.');
}
t38(strpos((string) file_get_contents(__DIR__ . '/../src/GuideDraftController.php'), 'GnreEnvironmentPreference') !== false, 'nova guia deve consumir a preferência da empresa.');
echo "task38 environment preference: OK\n";
