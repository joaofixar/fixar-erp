<?php
namespace hardness;
require_once __DIR__ . '/../../bibliotecas/classes/LinkCompartilhamentoWhatsApp.php';

$casos = array(
    array('http://localhost/', 'http://localtest.me/hardness3/whatsapp/arquivo.pdf'),
    array('http://localhost:8080/', 'http://localtest.me:8080/hardness3/whatsapp/arquivo.pdf'),
    array('http://LOCALHOST', 'http://localtest.me/hardness3/whatsapp/arquivo.pdf'),
    array('http://[::1]:8080/', 'http://localtest.me:8080/hardness3/whatsapp/arquivo.pdf'),
    array('http://127.0.0.1/', 'http://localtest.me/hardness3/whatsapp/arquivo.pdf'),
    array('http://localtest.me/', 'http://localtest.me/hardness3/whatsapp/arquivo.pdf'),
    array('https://erp.example.com/', 'https://erp.example.com/hardness3/whatsapp/arquivo.pdf'),
    array('http://192.168.1.10:8080/', 'http://192.168.1.10:8080/hardness3/whatsapp/arquivo.pdf'),
    array('https://localhost.example.com/', 'https://localhost.example.com/hardness3/whatsapp/arquivo.pdf'),
);
foreach ($casos as $caso) {
    $url = LinkCompartilhamentoWhatsApp::gerar($caso[0], 'whatsapp/arquivo.pdf');
    if ($url !== $caso[1]) { throw new \RuntimeException('Endereço incorreto: ' . $url); }
}
echo "OK: URLs locais com domínio completo, portas preservadas e endereços de produção intactos.\n";
