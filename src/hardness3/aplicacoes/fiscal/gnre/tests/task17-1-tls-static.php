<?php
declare(strict_types=1);

function t171(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

$src = dirname(__DIR__) . '/src/';
$clients = ['ConfigUfClient.php', 'ReceptionClient.php', 'ResultClient.php'];
$pinOption = 'CURLOPT_' . 'PINNEDPUBLICKEY';
$resolver = (string) file_get_contents($src . 'EnvironmentResolver.php');
t171(strpos($resolver, 'https://') !== false, 'O resolvedor central deve manter endpoints HTTPS allowlisted.');

foreach ($clients as $client) {
    $source = (string) file_get_contents($src . $client);
    t171(strpos($source, "https://") === false, $client . ' não deve possuir endpoint HTTPS próprio.');
    t171(strpos($source, $pinOption) === false, $client . ' não pode fixar chave pública do Portal.');
    t171(strpos($source, 'GnreTlsPolicy::curlOptions()') !== false, $client . ' deve usar a política TLS comum.');
}

$policy = (string) file_get_contents($src . 'TlsPolicy.php');
t171(strpos($policy, 'CURLOPT_SSL_VERIFYPEER => true') !== false, 'A política deve validar a cadeia TLS.');
t171(strpos($policy, 'CURLOPT_SSL_VERIFYHOST => 2') !== false, 'A política deve validar o hostname TLS.');
t171(strpos($policy, 'CURL_SSLVERSION_TLSv1_2') !== false, 'A política deve exigir TLS 1.2 ou superior.');
t171(strpos($policy, $pinOption) === false, 'A política TLS não pode conter pin de chave pública.');
t171(strpos($policy, 'Não foi possível conectar com o Portal GNRE com segurança.') !== false, 'A mensagem operacional segura deve estar definida.');

echo "task17.1 tls static: OK\n";
