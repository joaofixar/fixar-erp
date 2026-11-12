<?php

declare(strict_types=1);

use HardnessWebhooks\DestinationAuthentication\DestinationAuthenticationService;
use HardnessWebhooks\Security\SecretCipher;
use HardnessWebhooks\Security\HmacSigner;

require_once dirname(__DIR__) . '/autoload.php';

function task21a(bool $condition, string $message): void { if (!$condition) { throw new RuntimeException('Assertion failed: ' . $message); } }
function task21aThrows(callable $callable, string $message): void { try { $callable(); task21a(false, $message); } catch (InvalidArgumentException $exception) { } }

$schema = file_get_contents(dirname(__DIR__, 2) . '/scripts/atualizacaoBase.php');
$service = new DestinationAuthenticationService(new SecretCipher(random_bytes(32)));
$token = 'task21a-bearer.token_123';
$stored = $service->forCreate('B', $token);
task21a($schema !== false && strpos($schema, "WHK001_Destination_Auth_Type ENUM('N','B') NOT NULL DEFAULT 'N'") !== false && strpos($schema, 'WHK001_Destination_Auth_Criptografada LONGTEXT NULL,') !== false, 'canonical WHK001 schema defines destination authentication without a MySQL-5.6-incompatible TEXT default');
task21a(strpos((string) $schema, 'WHK004_WHK007_Id BIGINT UNSIGNED NULL DEFAULT NULL') !== false, 'canonical delivery schema permits absent HMAC');
task21a(strpos((string) $schema, 'ALTER TABLE WHK001') === false && strpos((string) $schema, 'information_schema') === false, 'no permanent WHK001 migration or schema discovery was added');
task21a($stored['type'] === 'B' && $stored['ciphertext'] !== null && strpos($stored['ciphertext'], $token) === false, 'Bearer token is encrypted at rest');
task21a($service->forCreate('N', '') === array('type' => 'N', 'ciphertext' => null), 'none has no ciphertext');
task21a($service->forUpdate('B', '', 'B', $stored['ciphertext']) === $stored, 'empty Bearer edit keeps the configured ciphertext');
task21a($service->forUpdate('N', '', 'B', $stored['ciphertext']) === array('type' => 'N', 'ciphertext' => null), 'none removes ciphertext');
foreach (array('', "bad\r", "bad\n", "bad\x01", str_repeat('a', DestinationAuthenticationService::MAX_TOKEN_LENGTH + 1)) as $invalid) { task21aThrows(static function () use ($service, $invalid): void { $service->forCreate('B', $invalid); }, 'invalid Bearer token is rejected'); }
$headers = HmacSigner::baseHeaders('event', 'delivery', 'type', '1'); task21a(isset($headers['Hardness-Event-Id'], $headers['Hardness-Delivery-Id'], $headers['Content-Type']) && !isset($headers['Hardness-Signature'], $headers['Hardness-Timestamp']), 'unsigned deliveries retain only independent headers');
echo "Task 21A static destination authentication tests passed.\n";

