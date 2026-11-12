<?php
/*
 * Processamento diário de notificações.
 *
 * Configure o cron do servidor para chamar este arquivo diretamente pelo PHP.
 * Exemplo, de segunda a sexta-feira às 06:00:
 * 0 6 * * 1-5 /usr/bin/php /var/www/html/hardness3/scripts/notifications/notificacoesDia.php >> /tmp/notificacoesDia.log 2>&1
 */

namespace hardness;

if (date("D") == "Sun") {
        die("As notificacoes nao rodam no domingo. ".date("Y-m-d H:i"));
} if (date("D") == "Sat") {
    if (floor(date("H"))<5 OR floor(date("H"))>13) {
        die("As notificacoes no sabado rodam entre 07:00 e 13:00. ".date("Y-m-d H:i"));
    }
} else {
    if (floor(date("H"))<5 OR floor(date("H"))>18) {
        die("As notificacoes de segunda a sexta rodam entre 06:00 e 19:00. ".date("Y-m-d H:i"));
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
ini_set('default_charset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
$empresas = scandir('/var/www/sites');

$msg = '';
$dbProcessado = array();
echo "Iniciando processo das notificações".date("Y-m-d H:i")."\n";

foreach($empresas as $index => $empresa){
    if (strpos($empresa, '-confUsuario.php') !== false) {
        include_once('/var/www/sites/'.$empresa);
        if (strpos($msg, $confUsuario['dbDatabase']) === false) {
            $db = $confUsuario['dbDatabase'];
            if (isset($dbProcessado[$db])) {
                continue;
            }

            $pathRaiz = rtrim($confUsuario['pathRaiz'], '/');
            if (empty($pathRaiz)) {
                $pathRaiz = realpath(dirname(__DIR__, 2));
            }
            $pathRaiz .= '/';
            chdir($pathRaiz);
            require_once($pathRaiz . 'sistema/confSistema.php');
            require_once($pathRaiz . 'bibliotecas/funcoesGerais.php');
            require_once($pathRaiz . 'sistema/apps.php');
            require_once($pathRaiz . 'sistema/personalizacoes.php');
            require_once($pathRaiz . 'bibliotecas/restricoes.php');
            $g['conf'] = new confSistema();
            $dbProcessado[$db] = true;

            $g['pathRaiz'] = $pathRaiz;
            $g['urlRaiz'] = $confUsuario['urlRaiz'];
                $con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
                $Error=mysqli_connect_error($con);
                mysql_set_charset('utf8', $con);
                if(empty($Error)) {
                    echo "\nIniciou atualizacao notificações dia:".$confUsuario['dbDatabase'];
                    $g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
                    $g['conexaoBanco'] = $con;
                    require_once('bibliotecas/conexaoBancoAuditoria.php');
                    $g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
                    $g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
                    $g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDadosUsuario'] . '/';
                    $g['pathDados'] = $home . $g['pathDados'];
                    $g['pathDadosUsuario'] = $home . $g['pathDadosUsuario'];
                    $g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
                    $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
                    while($rowC031 = mysql_fetch_assoc($sqlC031)){
                        $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
                        $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
                    }

                    require_once('bibliotecas/classes/NOTIFICACOES.php');
                    $NOTIFICACOES = new NOTIFICACOES();
    				$NOTIFICACOES->atualizarNotificacoesGeralDia();

                } else {
                    $msg .= "Não foi possível acesso este db - {$Error} \n\n";
                }
        }
    }
}

echo $msg;
echo "Finalizando processo das notificações\n";
