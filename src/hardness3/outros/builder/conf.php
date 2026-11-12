<?php
namespace hardness;

// Utiliza a mesma sessão do sistema hardness
session_start();
if (!preg_match('/builder\/index\.php$/', $_SERVER['SCRIPT_NAME']) && !preg_match('/builder\/requests\/projeto\.php$/', $_SERVER['SCRIPT_NAME'])) {
    if (empty($_SESSION['Usuario_Atual'])) {
        session_destroy();
        session_start();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            window.location = '/login';
        } </script></div>
EOT;
        die();
    }
    if (empty($_SESSION['projeto'])) {
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            window.location = '/hardness3/outros/builder/index.php';
        } </script></div>
EOT;
        die();
    }
} else {
    if (empty($_SESSION['Usuario_Atual'])) {
        session_destroy();
        session_start();
        header('Location: /login');
        die();
    }
}

require_once(dirname(__FILE__) . "/../../bibliotecas/confUsuario.php");
chdir($confUsuario['pathRaiz']);

function carregarBibliotecaFramework($caminho) {
    $caminhoFW = "framework/{$caminho}";
    if (file_exists($caminhoFW)) {
        require_once($caminhoFW);
    } else {
        require_once($caminho);
    }
}

require_once('bibliotecas/conexaoBanco.php');
include_once("configuracoes/sysConfig.php");
carregarBibliotecaFramework('bibliotecas/funcoesGerais.php');
carregarBibliotecaFramework('sistema/personalizacoes.php');
carregarBibliotecaFramework('sistema/apps.php');
require_once('outros/builder/classes/modelos/Campo.php');
require_once('outros/builder/classes/Util.php');

// Globais
global $g;
$g = array(
    'modoDesenvolvedor' => $_SESSION['modoDesenvolvedor'],
    'modoDesenvolvedorApp' => $_SESSION['modoDesenvolvedorApp']
);
require_once('bibliotecas/sessao.php');
if ($g['modoDesenvolvedorApp']) {
    $g['app'] = Apps::getApp($_SESSION['modoDesenvolvedorApp']);
}

// Dados usuários
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
is_dir($g['pathDados']) or mkdir($g['pathDados'], 0777, true);

$g['pathDadosBuilder'] = $_SESSION['modoDesenvolvedorApp'] ? Apps::getDirEditor() . 'builder' : "{$g['pathDados']}/builder";
is_dir($g['pathDadosBuilder']) or mkdir($g['pathDadosBuilder'], 0777, true);

//$g['pathDadosProg'] = "{$g['pathDadosBuilder']}/prog";
//is_dir($g['pathDadosProg']) or mkdir($g['pathDadosProg'], 0777, true);

$g['pathDadosArquivos'] = "{$g['pathDadosBuilder']}/arquivos";
is_dir($g['pathDadosArquivos']) or mkdir($g['pathDadosArquivos'], 0777, true);

// Projeto
if ($_SESSION['projeto']) {
    $g['projetoPath'] = "{$g['pathDadosBuilder']}/{$_SESSION['projeto']}.json";
    $g['projeto'] = json_decode(file_get_contents($g['projetoPath']), true);
}

if ($_SESSION['modoDesenvolvedor']) {
    Personalizacoes::$rootDir = 'ide/';
} else if ($_SESSION['modoDesenvolvedorApp']) {
    Personalizacoes::$rootDir = Apps::getDirEditor();
}

$g['tabelasAuditoria'] = Personalizacoes::tabelasAuditoria();
