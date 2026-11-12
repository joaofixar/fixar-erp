<?php
/*

Esta pagina irá rodar pelo navegador lynx texto.

Copiar para a pasta root/doc do servidor. 

Adicionar no crontab.

Exemplo: lynx -dump http://www.teste.com.br/notificacoes.php

*/

namespace hardness;

if (date("D") == "Sun") {
        die("As notificacoes nao rodam no domingo. ".date("Y-m-d H:i"));
} if (date("D") == "Sat") {
    if (floor(date("H"))<7 OR floor(date("H"))>13) {
        die("As notificacoes no sabado rodam entre 07:00 e 13:00. ".date("Y-m-d H:i"));
    }
} else {
    if (floor(date("H"))<7 OR floor(date("H"))>18) {
        die("As notificacoes de segunda a sexta rodam entre 07:00 e 19:00. ".date("Y-m-d H:i"));
    }
}

session_start();

set_time_limit(0);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
$time_start = microtime(true);
$g = array();
$g['usuarioAtual'] = 63;
$g['empresaAtual'] = 1;
$g['time'] = array();
$g['time']['sql'] = '';
$_SERVER['REMOTE_ADDR'] = '';

ini_set('default_ccleararset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
$empresas = scandir('/var/www/sites');

$msg = '';
$dbProcessado = array();
echo "Iniciando processo das notificações".date("Y-m-d H:i")."\n";
foreach($empresas as $index => $empresa){
    if (strpos($empresa, '-confUsuario.php') !== false) {
        include_once('/var/www/sites/'.$empresa);
        if (strpos($msg, $confUsuario['dbDatabase']) === false) {
            // Não processar o mesmo banco mais de uma vez
            $db = $confUsuario['dbDatabase'];
            if (isset($dbProcessado[$db])){
                continue;
            }
            //
            echo "Processando banco $db ".date("Y-m-d H:i")."\n";
            chmod ($g['pathDados'].'tmp', 0777);
            chmod ($g['pathDados'].'tmp/notificacoes', 0777);
            chdir($confUsuario['pathRaiz']);
            require_once('sistema/confSistema.php');
            require_once('bibliotecas/funcoesGerais.php');
            require_once('sistema/apps.php');
            require_once('sistema/personalizacoes.php');
            require_once('bibliotecas/restricoes.php');
            $g['conf'] = new confSistema();
            $dbProcessado[$db] = true;
            $g['pathRaiz'] = $confUsuario['pathRaiz'];
            $g['urlRaiz'] = $confUsuario['urlRaiz'];
            $con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
            $Error=mysqli_connect_error($con);
            mysql_set_charset('utf8', $con);
            if(empty($Error)) {
                $msg .= "Iniciou atualizacao notificações:".$confUsuario['dbDatabase'];
                $g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
                $g['conexaoBanco'] = $con;
                $g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
                $g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
                $g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDadosUsuario'] . '/';
                $g['pathDados'] = $home . $g['pathDados'];
                $g['pathDadosUsuario'] = $home . $g['pathDadosUsuario'];             
                require_once('bibliotecas/classes/NOTIFICACOES.php');
                $NOTIFICACOES = new NOTIFICACOES();
                $NOTIFICACOES->atualizarNotificacoesGeral();
                chmod ($g['pathDados'].'tmp/notificacoes/cacheN001Notificacoes*.txt', 0777);
            } else {
                $msg .= "Não foi possível acesso este db - {$Error} \n\n";
            }
        }
    }
}

echo $msg;
echo "Finalizando processo das notificações\n";
