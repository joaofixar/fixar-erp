<?php
/** Personalização do localhost: testa leitura de arquivos, sem bootstrap/banco. */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
require_once __DIR__ . '/../../hardness/class/DanfeModelo.php';
$raizProjeto = dirname(__DIR__, 2);
$raiz = sys_get_temp_dir() . '/danfe-rtc-local-' . bin2hex(random_bytes(8));
mkdir($raiz . '/dados_usuarios/danfe_rtc', 0700, true);
mkdir($raiz . '/dados_usuarios/e998', 0700, true);
$arquivos = array(
    $raiz . '/dados_usuarios/danfe_rtc/config_danfe_rtc.php' => "<?php return array('ativo' => true, 'tipoPadrao' => 'V');\n",
    $raiz . '/dados_usuarios/danfe_rtc/danfe_rt400.php' => '<?php require_once ' . var_export($raizProjeto . '/hardness/class/danfe_rt400.php', true) . "; return '\\hardness\\danfe_rt400';\n",
    $raiz . '/dados_usuarios/e998/danfe_rt400.php' => "<?php return require dirname(__DIR__) . '/danfe_rtc/danfe_rt400.php';\n",
);
foreach ($arquivos as $arquivo => $conteudo) { file_put_contents($arquivo, $conteudo); }
register_shutdown_function(function () use ($arquivos, $raiz) {
    foreach ($arquivos as $arquivo => $conteudo) { if (is_file($arquivo)) { unlink($arquivo); } }
    rmdir($raiz . '/dados_usuarios/e998');
    rmdir($raiz . '/dados_usuarios/danfe_rtc');
    rmdir($raiz . '/dados_usuarios');
    rmdir($raiz);
});
$nota = array('T007_Flag_ACP' => '6', 'T007_Versao_NFe' => '4.00');
$confUsuario = array('pathRaiz' => $raiz . '/', 'dbDatabase' => 'e998');
$verificacoes = 0;
function conferirLocal($condicao, $mensagem) {
    global $verificacoes;
    $verificacoes++;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
foreach (array('localhost', 'localhost:8080', '127.0.0.1', '[::1]:8080', '::1') as $host) {
    $_SERVER['HTTP_HOST'] = $host;
    conferirLocal(DanfeModelo::ambienteLocal(), 'Host local não reconhecido: ' . $host);
    conferirLocal(DanfeModelo::resolver($nota) === 'V', 'Pasta local não habilita RTC sem migração');
    conferirLocal(DanfeModelo::resolver($nota + array('C004_Tipo_DANFE' => 'A')) === 'V', 'Modelo local não prevalece sobre padrão atual da empresa');
    conferirLocal(DanfeModelo::resolver($nota + array('C004_Flag_Usar_DANFE_RTC' => 'S', 'C004_Tipo_DANFE' => 'H')) === 'H', 'Orientação da empresa ignorada');
    conferirLocal(DanfeModelo::resolver($nota + array('T007A_Tipo_DANFE' => 'A')) === 'A', 'Exceção atual por nota ignorada');
    conferirLocal(DanfeModelo::resolver($nota + array('C004_Flag_Usar_DANFE_RTC' => 'S', 'T007A_Tipo_DANFE' => 'H')) === 'H', 'Exceção horizontal por nota ignorada');
}
$renderer = DanfeModelo::criarRTC($nota);
conferirLocal($renderer instanceof danfe_rt400 && $renderer->orientacao() === 'P', 'Arquivo em dados_usuarios/e998 não carregado');
conferirLocal(in_array(realpath($raiz . '/dados_usuarios/e998/danfe_rt400.php'), array_map('realpath', get_included_files()), true), 'Personalização específica não lida');
$renderer->xml = file_get_contents(__DIR__ . '/fixtures/danfe-rtc.xml');
conferirLocal(substr($renderer->gerarPDF(), 0, 5) === '%PDF-', 'Personalização local não gera PDF');
// Prova de leitura da pasta compartilhada sem depender de um nome de base local.
unset($confUsuario['dbDatabase']);
$renderer = DanfeModelo::criarRTC($nota + array('C004_Flag_Usar_DANFE_RTC' => 'S', 'T007A_Tipo_DANFE' => 'H'));
conferirLocal($renderer->orientacao() === 'L', 'Pasta compartilhada não cria horizontal');
conferirLocal(in_array(realpath($raiz . '/dados_usuarios/danfe_rtc/danfe_rt400.php'), array_map('realpath', get_included_files()), true), 'Pasta compartilhada não lida');
foreach (array('erp.exemplo.com', 'localhost.exemplo.com', 'localhost.evil:8080') as $host) {
    $_SERVER['HTTP_HOST'] = $host;
    conferirLocal(!DanfeModelo::ambienteLocal(), 'Domínio remoto reconhecido como local');
    conferirLocal(DanfeModelo::resolver($nota) === 'A', 'Preferência de localhost mudou ambiente remoto');
}
$_SERVER['HTTP_HOST'] = 'localhost';
conferirLocal(DanfeModelo::resolver(array('T007_Flag_ACP' => '8', 'T007_Versao_NFe' => '4.00')) === 'A', 'Personalização RTC alterou NFC-e');
conferirLocal(DanfeModelo::resolver(array('T007_Flag_ACP' => '6', 'T007_Versao_NFe' => '3.10')) === 'A', 'Personalização RTC alterou versão 3.10');
echo "OK: {$verificacoes} verificações do carregamento local; PDF gerado sem conexão com banco.\n";
