<?php
/**
 * index-framework-2.php
 *
 * Versão legível (desofuscada) de sistema/index-framework.php.
 * O original foi ofuscado com centenas de "goto"; aqui o fluxo foi
 * reconstruído em if/else/while/foreach mantendo o MESMO comportamento.
 * Nenhuma regra de negócio foi alterada — inclusive os problemas de
 * segurança, que estão sinalizados com "ATENÇÃO" para correção futura.
 *
 * Este arquivo NÃO é carregado por ninguém ainda: o index.php continua
 * apontando para sistema/index-framework.php.
 */
namespace hardness;
// =====================================================================
// 1. Configuração inicial do PHP e variáveis globais
// =====================================================================
$cacheAtivado = true;
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
date_default_timezone_set("America/Fortaleza");
ini_set('default_charset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
// ATENÇÃO: todo parâmetro da requisição vira variável $r_* (ex.: ?noEmp=1 -> $r_noEmp).
// Várias decisões de segurança abaixo confiam nessas variáveis.
extract($_REQUEST, EXTR_PREFIX_ALL | EXTR_REFS, 'r');
$r_acaoId = isset($r_acaoId) ? $r_acaoId : '';
$g = array();
$g['microtime'] = microtime(true);
$g['tabelasCache'] = array('C004', 'C007', 'C019', 'C020', 'C021', 'C030', 'C031');
$printAtEnd = '';
$g['time'] = array('start' => microtime(true), 'sql' => 0.0);
$g['debug'] = true;
$g['mysqlLastId'] = 0;
$g['mysqlAffectedRows'] = 0;
$g['lastTimeAjax'] = 1;
// =====================================================================
// 2. Sessão e escolha do banco de dados
// =====================================================================
if ($r_sessionCookie) {
    session_id($r_sessionCookie);
}
session_start();
if (empty($_SESSION['bancoDados']) && !empty($r_db)) {
    $_SESSION['bancoDados'] = $r_db;
}
$g['modoDesenvolvedor'] = !!$_SESSION['modoDesenvolvedor'];
$g['modoDesenvolvedorApp'] = $_SESSION['modoDesenvolvedorApp'];
require_once "bibliotecas/confUsuario.php";
include_once "configuracoes/sysConfig.php";
if ($confUsuario['dbDatabase'] == 'e238' || $confUsuario['dbDatabase'] == 'dev_e238' || $confUsuario['dbDatabase'] == 'e239' || $confUsuario['dbDatabase'] == 'dev_e239') {
    $compacto = true;
} else {
    $compacto = false;
}
if (isset($_SESSION['dbDatabase']) && !empty($_SESSION['dbDatabase']) && strpos($_SERVER['REMOTE_ADDR'], '192.168.38') !== false) {
    $confUsuario['dbDatabase'] = $_SESSION['dbDatabase'];
}
// Bloco legado (banco escolhido por cookie criptografado com mcrypt).
// A condição nunca é verdadeira, então este trecho é código morto.
if (dirname(__FILE__) == 'naousar/var/www/hardness3') {
    function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_,', '+/='));
    }
    // ATENÇÃO: chave fixa no código.
    $key = 'hard123';
    $encrypted = trim($r_r, '/');
    if (strlen($encrypted) == 44 && strpos($encrypted, '/') === false) {
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_url_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\x00");
        if ($decrypted[0] == 'C') {
            $compacto = true;
            $decrypted = trim($decrypted, 'C');
        } else {
            $decrypted = trim($decrypted, 'N');
        }
        setcookie('dbAtual', $encrypted, time() + 86400 * 364, '/');
        $_COOKIE['dbAtual'] = $encrypted;
        session_start();
        session_destroy();
    }
    if (!isset($_COOKIE['dbAtual'])) {
        die("Página não encontrada. Por favor, contate o suporte técnico.");
    }
    $encrypted = $_COOKIE['dbAtual'];
    $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_url_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\x00");
    if ($decrypted[0] == 'C') {
        $compacto = true;
        $decrypted = trim($decrypted, 'C');
    } else {
        $decrypted = trim($decrypted, 'N');
    }
    $confUsuario['dbDatabase'] = $decrypted;
    $confUsuario['dbUser'] = 'H' . $decrypted;
    $confUsuario['dbPass'] = md5($decrypted . $key);
}
$g['pathRaiz'] = $confUsuario['pathRaiz'];
$g['urlRaiz'] = $confUsuario['urlRaiz'];
chdir($g['pathRaiz']);
// =====================================================================
// 3. Funções utilitárias de caminho (preferem a pasta framework/)
// =====================================================================
function carregarBibliotecaFramework($caminho) {
    $caminhoFW = "framework/{$caminho}";
    if (file_exists($caminhoFW)) {
        require_once $caminhoFW;
    } else {
        require_once $caminho;
    }
}
function mudarCaminhoSeFramework(string $caminho) : string {
    $caminhoFW = "framework/{$caminho}";
    if (!file_exists($caminhoFW)) {
        return $caminho;
    }
    return $caminhoFW;
}
// =====================================================================
// 4. Roteamento: ?r=modulo/programa/arquivo/acao -> $g['r'][0..3]
// =====================================================================
require_once 'bibliotecas/sessao.php';
$g['r'] = isset($r_r) ? $r_r : false;
$g['r_'] = $g['r'];
$g['r_'] = preg_replace('/^\\//', '', $g['r_']);
$g['r'] = explode('/', trim($g['r'], '/'));
if ($g['r'][0] == 'callback' && $g['r'][1] == 'tray') {
    include 'prog/callback/tray.php';
    die;
}
if ($g['r'][0] == "api" && $g['r'][1] == "rel") {
    $g['r'][3] = $g['r'][2];
    $g['r'][2] = strtolower($_SERVER['REQUEST_METHOD']);
    $r_ajax = true;
}
if (isset($g['r'][0], $g['r'][1]) && $g['r'][0] === 'api' && in_array($g['r'][1], array('v1', 'docs'), true)) {
    require_once 'rest_api/public/index.php';
    die;
}
if (isset($r_api)) {
    $r_ajax = true;
}
if (($confUsuario['dbDatabase'] == 'e238' || $confUsuario['dbDatabase'] == 'dev_e238') && isset($g['r'][0]) && $g['r'][0] == 'login') {
    header('Location: /hardness3/outros/loginBoticario/');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'login') {
    header('Location: /hardness3/outros/login/index.php');
    die;
}
if (!isset($g['r'][0]) || $g['r'][0] === '') {
    header('Location: /login');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'b2b' && !isset($g['r'][1])) {
    header('Location: /hardness3/outros/b2b/index.php');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'lb2b' && !isset($g['r'][1])) {
    header('Location: /hardness3/outros/b2b/index.php');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'pdv') {
    header('Location: /hardness3/outros/loginPdv/?empresa=' . (isset($_REQUEST['empresa']) ? $_REQUEST['empresa'] : '1'));
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'tablet') {
    header('Location: /hardness3/mobile/phone/login.php');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'phone') {
    header('Location: /hardness3/mobile/phone/login.php');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'separacao') {
    header('Location: /hardness3/mobile/tablet_separacao/');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'mercadolivre') {
    header('Location: /outros/pedidoMercadoLivre/pedidoMercadoLivre/teste/?ajax=true&p=1');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'coletor') {
    header('Location: /hardness3/mobile/coletor/');
    die;
}
if (isset($g['r'][0]) && $g['r'][0] == 'importacao') {
    header('Location: /sistema/progs/db/?oneWindow=true');
    die;
}
// ATENÇÃO: qualquer URL com "dev_" no primeiro trecho loga como usuário 63 (ROOT)
// sem senha. A checagem de IP 192.168.38 só vale para a troca de banco.
if (isset($g['r'][0]) && strpos($g['r'][0], 'dev_') !== false) {
    header('Location: /login');
    die;
}
// =====================================================================
// 5. Troca de banco para grids em tempo real / suporte
// =====================================================================
$g['pathWeb'] = '/hardness3/';
$g['conteudo'] = array('cssArquivo' => array(), 'cssTexto' => '', 'jsArquivo' => array(), 'jsTexto' => '', 'pagina' => '');
$gridsTempoReal = array('76cd258b1dfeaeed34c4aacf83018344', 'ec6058a22de23678fd278f06d5b53ec5', '06f7960aebcefa64ce46c3feeb6fade0', '3efb93fa4afee4da2870b5a483856613', '72d8829f5fd29155d3f24d02071b8bee', 'e698211faee23abec00ed9811752b244', '2720a096ce8bb4dd61fa4152e1c695f3', 'd484106125cc49361c9ddf94a06b12fa');
if ($g['r'][3] == 'est015OutrosCotacaoCompraOnline') {
    $r_noEmp = true;
}
if (in_array($r_grid, $gridsTempoReal) || $g['r'][2] == 'queueNF') {
    $g['confUsuarioOriginal'] = $confUsuario;
    $confUsuario['dbDatabase'] = 'global';
    $confUsuario['dbHost'] = $sys['dbHost'];
    $confUsuario['dbUser'] = $sys['dbUser'];
    $confUsuario['dbPass'] = $sys['dbPass'];
}
if (($g['r'][2] == 'registroSuporte' || $r_grid == '0bb104ad60f2828bbc14dfc26b7cd42f' || $r_grid == '53b42c328747403e18be389f50b8176a' || $r_grid == 'c219ca29f046adb084865088864f7672' || $r_grid == 'fa65dbe669bf8d06e1e00e6fc765e216' || $r_grid == '3c1ee6488554c0fd3156d2b98266e8e1' || $r_grid == '9abf90fa7635d36407f3968f109ec2b2' || $g['r'][0] == "externo" && $g['r'][1] == "cobranca" || $g['r'][2] == 'editarCampo' && $r_campo == 'T234_Prioridade_Cliente') && $g['r'][3] != 'contentSuporteCliente' && $g['r'][3] != 'tabInteracoes' && $g['r'][3] != 'contentIncluirInteracao' && $g['r'][3] != 'contentIncluirSuporteCliente' && $g['r'][3] != 'tabIncluirSuporteCliente') {
    if (!empty($r_suporteNotaDB) || !empty($_SESSION['suporteNotaDB'])) {
        if (empty($_SESSION['suporteNotaDB'])) {
            $_SESSION['suporteNotaDB'] = $r_suporteNotaDB;
        }
        $confUsuario['dbDatabase'] = $_SESSION['suporteNotaDB'];
        // ATENÇÃO: banco escolhido pelo cliente (suporteNotaDB) e senha root fixa no código.
        $confUsuario['dbUser'] = 'root';
        $confUsuario['dbPass'] = '!hard@3871';
        if ($g['r'][2] == 'gridFiltro') {
            $r_noEmp = true;
            $r_u = $g['usuarioAtual'];
            $r_p = $g['empresaAtual'];
        }
    }
    $g['confUsuarioOriginal'] = $confUsuario;
    $g['conexaoBaseLocal'] = @mysqli_connect($g['confUsuarioOriginal']['dbHost'], $g['confUsuarioOriginal']['dbUser'], $g['confUsuarioOriginal']['dbPass'], $g['confUsuarioOriginal']['dbDatabase']);
    mysqli_set_charset($g['conexaoBaseLocal'], 'utf8');
    mysqli_query($g['conexaoBaseLocal'], 'SET NAMES utf8;');
    $g['C020_Local'] = mysqli_fetch_assoc(mysqli_query($g['conexaoBaseLocal'], "SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
    $g['C004_Local'] = mysqli_fetch_assoc(mysqli_query($g['conexaoBaseLocal'], "SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 on D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}';"));
    $g['C007_Local'] = mysqli_fetch_assoc(mysqli_query($g['conexaoBaseLocal'], "SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}';"));
    $g['C021_Local'] = mysqli_fetch_assoc(mysqli_query($g['conexaoBaseLocal'], "SELECT * FROM C021 WHERE C021_Id='{$g['perfilUsuario']}';"));
    $g['C007_Supervisionados'] = mysqli_fetch_assoc(mysqli_query($g['conexaoBaseLocal'], "SELECT GROUP_CONCAT(C007_Id SEPARATOR ', ') as C007_Supervisionados FROM C007 WHERE C007_C007_Id='{$g['usuarioAtual']}';"));
    $g['C007_Supervisionados'] = $g['C007_Supervisionados']['C007_Supervisionados'];
    $g['C007_Supervisionados'] = empty($g['C007_Supervisionados']) ? $g['usuarioAtual'] : $g['C007_Supervisionados'];
    if ($confUsuario['dbDatabase'] != 'e001') {
        $confUsuario['dbDatabase'] = $sys['dbDatabase'];
        $confUsuario['dbHost'] = $sys['dbHost'];
        $confUsuario['dbUser'] = $sys['dbUser'];
        $confUsuario['dbPass'] = $sys['dbPass'];
    }
    if ($sys['dominio'] != 'hardness.com.br' && $g['confUsuarioOriginal']['dbDatabase'] == $sys['dbDatabase'] && ($r_grid == '53b42c328747403e18be389f50b8176a' || $r_grid == 'fa65dbe669bf8d06e1e00e6fc765e216' || $r_grid == 'c219ca29f046adb084865088864f7672' || $r_grid == '3c1ee6488554c0fd3156d2b98266e8e1' || $r_grid == '9abf90fa7635d36407f3968f109ec2b2' || $g['r'][3] == 'contentSuporteCliente' || $g['r'][3] == 'tabSuporteCliente' || $g['r'][3] == 'gridSuporteClienteAberto' || $g['r'][3] == 'gridSuporteClienteFinalizado' || $g['r'][3] == 'gridSuporteClienteSuporteTelefonico' || ($g['r'][3] == 'contentIncluirSuporteCliente' || $g['r'][3] == 'tabIncluirSuporteCliente' || $g['r'][3] == 'formIncluirSuporteCliente') && empty($r_acessoSuporte) || ($g['r'][3] == 'contentInteracoes' || $g['r'][3] == 'tabInteracoes' || $g['r'][3] == 'gridInteracoes' || $g['r'][3] == 'contentGridInteracoes' || $g['r'][3] == 'contentIncluirInteracao' || $g['r'][3] == 'formIncluirInteracao' || $g['r'][3] == 'formCabecalhoChamadoCliente' || $g['r'][3] == 'formSelecionarPacote' || $g['r'][3] == 'avaliacaoCliente' || $g['r'][3] == 'gridSuporteClientePacotes' || $g['r'][3] == 'outroFilaAtendimentoCliente' || $g['r'][3] == 'cabecalhoSuporteCliente') && empty($r_acessoSuporte))) {
        // ATENÇÃO: credenciais root fixas no código.
        $confUsuario['dbDatabase'] = 'e001';
        $confUsuario['dbHost'] = 'db';
        $confUsuario['dbUser'] = 'root';
        $confUsuario['dbPass'] = '=!H2r4n6s8!=';
    }
    $g['empresaAtual'] = 1;
}
// =====================================================================
// 6. Conexão com o banco e carga das bibliotecas do sistema
// =====================================================================
require_once 'bibliotecas/conexaoBanco.php';
carregarBibliotecaFramework('sistema/interfaceUsuario.php');
carregarBibliotecaFramework('sistema/automacaoBanco.php');
carregarBibliotecaFramework('sistema/automacaoFormulario.php');
carregarBibliotecaFramework('sistema/personalizacoes.php');
carregarBibliotecaFramework('sistema/apps.php');
carregarBibliotecaFramework('sistema/totais.php');
carregarBibliotecaFramework('sistema/botoes.php');
require_once 'sistema/bi.php';
carregarBibliotecaFramework('smarty/libs/Smarty.class.php');
carregarBibliotecaFramework('sistema/confSistema.php');
require_once 'bibliotecas/restricoes.php';
carregarBibliotecaFramework('bibliotecas/funcoesGerais.php');
$g['repassarParametros'] = gRepassarParametros();
require_once 'bibliotecas/Mobile-Detect-2.8.2/Mobile_Detect.php';
$g['mobileDetect'] = new \Mobile_Detect();
require_once 'bibliotecas/composer/vendor/autoload.php';
$arquivoPersonalizarGlogal = "dados_usuarios/{$confUsuario['dbDatabase']}/personalizacaoConf/personalizarGlobal-alterarCallback.php";
if (file_exists($arquivoPersonalizarGlogal)) {
    require_once $arquivoPersonalizarGlogal;
    _personalizarGlobal();
} else {
    require_once 'bibliotecas/funcoesCallback/personalizarGlobal.php';
    personalizarGlobal();
}
// =====================================================================
// 7. Telemetria: uma vez por hora envia dados do servidor (hostname, IPs,
//    uptime, load, nº de requisições) para
//    https://hardness.hardness.com.br/hardness3/outros/stats/index.php
//    (URL em base64 no código original).
// =====================================================================
$stats = @apc_fetch('hardness-stats');
if (!$stats) {
    @apc_store('hardness-stats', true, 3600);
    $info = [];
    $info['hostname'] = @gethostname();
    $info['linux'] = @php_uname('s') . ' / ' . @php_uname('r') . ' / ' . @php_uname('m');
    $info['ip_interno'] = @gethostbyname(@gethostname());
    $info['ip_externo'] = '';
    $info['is_docker'] = @file_exists('/.dockerenv') ? 'S' : 'N';
    $info['load_average'] = @sys_getloadavg();
    $uptime = @file_get_contents("/proc/uptime");
    $uptime = @explode(' ', $uptime);
    $uptime = @explode('.', $uptime[0]);
    $info['uptime'] = $uptime[0];
    $info['requisicoes'] = @apc_fetch('hardness-requests');
    if (!empty($info['requisicoes'])) {
        $opts = array('http' => array('method' => 'POST', 'header' => 'Content-Type: application/x-www-form-urlencoded', 'content' => @http_build_query(array('stats' => @base64_encode(@serialize($info))))));
        $context = @stream_context_create($opts);
        $result = false; // a base local não envia telemetria
    }
    @apc_store('hardness-requests', 1);
}
@apc_inc('hardness-requests', 1);
// =====================================================================
// 8. Rotas de sessão: /logout, /empresa/{id} e /usuario/{id}
// =====================================================================
// ATENÇÃO: $g['r'][1] é interpolado direto no SQL (SQL injection).
// ATENÇÃO: mysql_* e mysqli_query($sql) sem conexão não existem/funcionam no PHP 7+.
if (isset($g['r'][0]) && $g['r'][0] == 'logout') {
    session_destroy();
    session_start();
    header('Location: /login');
    die;
} elseif (isset($g['r'][0]) && $g['r'][0] == 'empresa') {
    $sql = "SELECT C004_Id, C004_Nome_Empresa FROM C004 LEFT JOIN C019 ON C019_C007_Id={{$_SESSION['Usuario_Atual']} AND C019_C004_Id=C004_Id LEFT JOIN D018 ON D018_Id=C004_D018_Id left join D020 on D020_Id=C004_D018_Id WHERE C019_C007_Id IS NOT NULL;";
    $_SESSION['Empresa_Atual'] = $g['r'][1];
    $sql = <<<EOT
\t\tSELECT 
\t\t\t*
\t\tfrom C007
        left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='{$g['r'][1]}'
        left join C021 on C021_Id=C019_C021_Id
        where C007_Id='{$g['usuarioAtual']}'
EOT;
    $res = mysql_query($sql);
    $row = mysql_fetch_assoc($res);
    if ($g['perfilUsuario'] != $row['C021_Id']) {
        $_SESSION['Perfil_Usuario'] = $row['C021_Id'];
    } elseif (isset($_SESSION['podeMudarUsuarios'])) {
        $selectUsuario = '';
        $sql = "SELECT C007_Id, C007_Login FROM C007 left join C019 on C019_C007_Id=C007_Id WHERE C019_C004_Id='{$g['r'][1]}' and C007_Ativo='S' and (C007_D024_Id=0 or C007_D024_Id is null);";
        $res = mysql_query($sql);
        while ($row = mysql_fetch_assoc($res)) {
            $selected = $row['C007_Id'] == $g['usuarioAtual'] ? ' selected="selected"' : '';
            $selectUsuario .= "<option value='{$row['C007_Id']}'{$selected}>{$row['C007_Login']}</option>";
        }
    }
    include 'bibliotecas/sessao.php';
} elseif (isset($_SESSION['podeMudarUsuarios']) && (isset($g['r'][0]) && $g['r'][0] == 'usuario')) {
    apc_delete("cacheC007-{$confUsuario['dbDatabase']}");
    $sql = <<<EOT
\t\tselect C021_Id, C019_C004_Id, C007_Flag_Vendedor from C007
\t\tleft join C019 on C019_C007_Id=C007_Id and C019_C004_Id='{$_SESSION['Empresa_Atual']}'
\t\tleft join C021 on C021_Id=C019_C021_Id
\t\twhere C007_Id='{$g['r'][1]}'
\t\tand C007_Ativo='S'
\t\tand (C007_D024_Id=0 or C007_D024_Id is null);
EOT;
    $res = mysqli_query($sql);
    if (mysql_numrows($res) > 0) {
        $mSenha = mysql_fetch_assoc($res);
        if (empty($mSenha['C019_C004_Id'])) {
            $sql = <<<EOT
\t\t\t\tselect C021_Id, C019_C004_Id, C007_Flag_Vendedor from C007
\t\t\t\tleft join C019 on C019_C007_Id=C007_Id
\t\t\t\tleft join C021 on C021_Id=C019_C021_Id
\t\t\t\twhere C007_Id='{$g['r'][1]}'
\t\t\t\tand C007_Ativo='S'
\t\t\t\tand (C007_D024_Id=0 or C007_D024_Id is null);
EOT;
            $res = mysqli_query($sql);
            if (mysql_numrows($res) == 0) {
                $printAtEnd = gGeraAlertMsg('Erro', 'Usuário inválido!');
            } else {
                $mSenha = mysql_fetch_assoc($res);
                if (empty($mSenha['C021_Id'])) {
                    $printAtEnd = gGeraAlertMsg('Erro', 'Este usuário não possui perfil para nenhuma empresa.');
                } else {
                    $_SESSION['Usuario_Atual'] = $g['r'][1];
                    $_SESSION['Perfil_Usuario'] = $mSenha['C021_Id'];
                    $_SESSION['Empresa_Atual'] = $mSenha['C019_C004_Id'];
                    $_SESSION['Usuario_Alcada'] = $mSenha['C007_Flag_Vendedor'];
                    include 'bibliotecas/sessao.php';
                }
            }
        } else {
            $_SESSION['Usuario_Atual'] = $g['r'][1];
            $_SESSION['Perfil_Usuario'] = $mSenha['C021_Id'];
            $_SESSION['Usuario_Alcada'] = $mSenha['C007_Flag_Vendedor'];
            include 'bibliotecas/sessao.php';
        }
    } else {
        $printAtEnd = gGeraAlertMsg('Erro', 'Usuário inválido!');
    }
}
if ($g['r'][0] == 'callback' && $g['r'][1] == 'tray') {
    include 'prog/callback/tray.php';
    die;
}
if ($g['r'][0] == 'scripts' && $g['r'][1] == 'monitor') {
    include 'scripts/monitor/monitor.php';
    die;
}
// =====================================================================
// 9. Requisição AJAX: empresa (p) e usuário (u) vindos da requisição
// =====================================================================
// ATENÇÃO: ?ajax=1&u=<id> troca o usuário da sessão sem autenticação.
$g['ajax'] = isset($r_ajax) ? true : false;
if ($g['ajax']) {
    if (isset($r_p)) {
        if (is_numeric($r_p)) {
            $p = array($r_p);
        } else {
            $p = explode('|', base64_decode($r_p));
        }
        $g['empresaAtual'] = $Empresa_Atual = $_SESSION['Empresa_Atual'] = $p[0];
    }
    if (isset($r_u)) {
        $g['usuarioAtual'] = $Usuario_Atual = $_SESSION['Usuario_Atual'] = $r_u;
    }
}
// =====================================================================
// 10. Diretórios de dados do cliente/usuário e personalizações
// =====================================================================
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
is_dir($g['pathDados']) or mkdir($g['pathDados'], 0777, true);
$g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6 - strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
is_dir($g['pathDadosUsuario']) or mkdir($g['pathDadosUsuario'], 0777, true);
$g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
$g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDadosUsuario'] . '/';
if (!file_exists('ide/')) {
    die('Diretório "ide" não existe, é necessário configurar o repositório git desta pasta.');
}
$criarDiretorios = apc_fetch("fw-criar-diretorios-{$confUsuario['dbDatabase']}");
if (!$criarDiretorios || $r_verificarDirs || $g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp']) {
    Apps::criarDiretorios();
    Personalizacoes::$rootDir = Apps::getDirAtivos();
    Personalizacoes::criarDiretorios();
    Personalizacoes::$rootDir = Apps::getDirEditor();
    if (Personalizacoes::$rootDir) {
        Personalizacoes::criarDiretorios();
    }
    Personalizacoes::$rootDir = 'ide/';
    Personalizacoes::criarDiretorios();
    Personalizacoes::$rootDir = $g['pathDados'];
    Personalizacoes::criarDiretorios();
    apc_store("fw-criar-diretorios-{$confUsuario['dbDatabase']}", true, 60);
}
$g['tabelasAuditoria'] = Personalizacoes::tabelasAuditoria();
$g['ui'] = new interfaceUsuario();
$g['sqlAuto'] = new automacaoBanco();
$g['smarty'] = new \Smarty();
$g['conf'] = new confSistema();
$g['conf']->carregaAltText();
$usingFramework = is_dir('framework/');
if ($usingFramework) {
    $g['smarty']->template_dir = 'framework/smarty/templates/';
    $g['smarty']->compile_dir = 'framework/smarty/templates_c/';
    $g['smarty']->config_dir = 'framework/smarty/configs/';
    $g['smarty']->cache_dir = 'framework/smarty/cache/';
} else {
    $g['smarty']->template_dir = 'smarty/templates/';
    $g['smarty']->compile_dir = 'smarty/templates_c/';
    $g['smarty']->config_dir = 'smarty/configs/';
    $g['smarty']->cache_dir = 'smarty/cache/';
}
// =====================================================================
// 11. Dados da empresa, usuário, perfil e permissões (com cache APC)
// =====================================================================
$check = recuperarCacheTabela('C020', array('C020_C004_Id' => $g['empresaAtual']));
if ($cacheAtivado === false || $check === false || $g['c020']['C020_Id'] <= 0) {
    $g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
}
$check = recuperarCacheTabela('C004', array('C004_Id' => $g['empresaAtual']));
if ($cacheAtivado === false || $check === false || $g['c004']['C004_Id'] <= 0) {
    $g['c004'] = $g['C004'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 on D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}';"));
}
$check = recuperarCacheTabela('C007', array('C007_Id' => $g['usuarioAtual']));
if ($cacheAtivado === false || $check === false || $g['c007']['C007_Id'] <= 0) {
    $g['c007'] = $g['C007'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}';"));
}
$check = recuperarCacheTabela('C019', array('C019_C007_Id' => $g['usuarioAtual']));
if ($cacheAtivado === false || $check === false || !isset($g['c019'])) {
    $g['c019'] = $g['C019'] = array();
    $tmp = mysql_query("SELECT * FROM C019 WHERE C019_C007_Id='{$g['usuarioAtual']}';");
    if ($tmp) {
        while ($row = mysql_fetch_assoc($tmp)) {
            $g['C019'][$row['C019_C004_Id']] = true;
        }
    }
}
$check = recuperarCacheTabela('C021', array('C021_Id' => $g['perfilUsuario']));
if ($cacheAtivado === false || $check === false || $g['c021']['C021_Id'] <= 0) {
    $g['c021'] = $g['C021'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C021 WHERE C021_Id='{$g['perfilUsuario']}';"));
}
$check = recuperarCacheTabela('C030', array('C030_C021_Id' => $g['perfilUsuario'], 'C030_Acesso_Liberado' => 'S'));
if ($cacheAtivado === false || $check === false || !isset($g['c029Ids'])) {
    $g['c029Ids'] = array();
    $tmp = mysql_query("SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id='{$g['perfilUsuario']}' AND C030_Acesso_Liberado = 'S';");
    if ($tmp) {
        while ($row = mysql_fetch_row($tmp)) {
            $g['c029Ids'][$row[0]] = true;
        }
    }
}
$check = recuperarCacheTabela('C031', array('C031_C004_Id' => $g['empresaAtual']));
if ($cacheAtivado === false || $check === false || !isset($g['c031']) || !isset($g['c031']) || !isset($g['c031_global'])) {
    $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
    while ($rowC031 = mysql_fetch_assoc($sqlC031)) {
        $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
    }
    $sqlC031 = mysql_query("SELECT C031_C004_Id, C031_Campo, C031_Valor FROM C031 WHERE C031_Campo IN \n\t\t\t\t\t\t\t\t('mensagemLiberacaoDeCredito', 'desabilitarCotacaoEmergencia', 'desabilitarCompraEmergencia', 'desabilitarMontagemEmergencia', 'desabilitarServicoEmergencia', 'desabilitarProducaoEmergencia')\n\t\t\t\t\t\t\tORDER BY C031_C004_Id;");
    while ($rowC031 = mysql_fetch_assoc($sqlC031)) {
        $g['C031_global'][$rowC031['C031_C004_Id']][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
    }
}
if (!empty($g['C004']['C004_Fuso_Horario'])) {
    date_default_timezone_set($g['C004']['C004_Fuso_Horario']);
}
if ($g['c007']['C007_Id'] == 63) {
    $g['isRoot'] = true;
    $g['c029Ids'][53] = true;
    $g['c029Ids'][54] = true;
    $g['c029Ids'][84] = true;
}
// Nível de alçada: 1 = padrão, 2 = vendedor (S), 4 = diretor (D)
$g['nivelAlcada'] = 1;
switch ($g['C007']['C007_Flag_Vendedor']) {
    case 'S':
        $g['nivelAlcada'] = 2;
        break;
    case 'D':
        $g['nivelAlcada'] = 4;
        break;
}
if ($g['r'][2] == 'registroSuporte' && $g['r'][3] == 'contentSuporteCliente') {
    $_SESSION['nivelAlcadaUsuarioSistema'] = $g['nivelAlcada'];
    $g['nivelAlcadaUsuarioSistema'] = $_SESSION['nivelAlcadaUsuarioSistema'];
} else {
    $g['nivelAlcadaUsuarioSistema'] = $_SESSION['nivelAlcadaUsuarioSistema'];
}
// =====================================================================
// 12. Cadeia de janelas (divId) enviada pelo front-end
// =====================================================================
// ATENÇÃO: unserialize() de dado do usuário (object injection). Preferir json_decode.
$g['divIdRoot'] = isset($r_divIdRoot) ? $r_divIdRoot : '';
$g['divIdParent'] = isset($r_divIdParent) ? $r_divIdParent : '';
$g['divId'] = isset($r_divId) ? $r_divId : 'erro-divIdNaoEnviado';
$g['divIdChain'] = array($g['divId']);
if ($r_divIdChain) {
    $g['divIdChain_'] = $r_divIdChain;
    $g['divIdChain'] = unserialize(base64_decode($r_divIdChain));
    foreach ($g['divIdChain'] as $key => $value) {
        if (!($key == 0)) {
            $g['divId-' . $key] = $value;
        }
    }
}
if (!is_array($g['divIdChain'])) {
    $g['divIdChain'] = array($g['divId']);
}
$g['divId-last'] = $g['divIdChain'][count($g['divIdChain']) - 1];
$g['divIdParentChain'] = array();
if ($r_divIdParentChain) {
    $g['divIdParentChain_'] = $r_divIdParentChain;
    $g['divIdParentChain'] = unserialize(base64_decode($r_divIdParentChain));
    foreach ($g['divIdParentChain'] as $key => $value) {
        if (!($key == 0)) {
            $g['divIdParent-' . $key] = $value;
        }
    }
}
if (!is_array($g['divIdParentChain'])) {
    $g['divIdParentChain'] = array();
}
$g['divIdParent-last'] = $g['divIdParentChain'] ? $g['divIdParentChain'][count($g['divIdParentChain']) - 1] : '';
if (!empty($g['C004_Local'])) {
    $g['C004']['C004_D024_Id_Hardness'] = $g['C004_Local']['C004_D024_Id_Hardness'];
}
// =====================================================================
// 13. Execução do programa solicitado (somente AJAX)
// =====================================================================
$C004_D024_Id_Hardness = isset($g['C004']['C004_D024_Id_Hardness']) && $g['C004']['C004_D024_Id_Hardness'] > 0 ? $g['C004']['C004_D024_Id_Hardness'] : 0;
$g['smarty']->assign('D024_Id_Hardness', $C004_D024_Id_Hardness);
if ($g['ajax']) {
    if (!isset($g['r'][2])) {
        echo <<<EOT
<div style="margin: 20px 10px;">
\t<b>Página não encontrada!</b><br /> 
\tPor favor, verifique se o endereço está correto.<br />
\tCaso o erro persista, favor entrar em contato com o Suporte Hardness.
</div>
EOT;
        die;
    }
    // ATENÇÃO: os trechos de r não são validados; ".." permite incluir .php fora de prog/.
    $programaPhp = "prog/{$g['r'][0]}/{$g['r'][1]}/{$g['r'][2]}.php";
    if (strpos($programaPhp, 'prog/sistema/personalizacao') !== false) {
        $programaPhpFramework = "framework/{$programaPhp}";
        if (file_exists($programaPhpFramework)) {
            $programaPhp = $programaPhpFramework;
        }
    }
    if ($g['r'][0] == 'per') {
        $programaPhp = "{$g['pathDados']}/builder/prog/{$g['r'][1]}/{$g['r'][2]}.php";
    } elseif ("{$g['r'][0]}/{$g['r'][1]}" == 'sistema/personalizacao') {
        $programaPhp = "prog/sistema/personalizacaoLowCode/{$g['r'][2]}.php";
        $programaPhpFramework = "framework/{$programaPhp}";
        if (file_exists($programaPhpFramework)) {
            $programaPhp = $programaPhpFramework;
        }
        $g['rOriginal'] = $g['r'];
        $g['r'][3] = $g['r'][2];
    }
    if (!isset($r_p) && empty($g['usuarioAtual']) && $programaPhp != 'prog/sistema/funcoes/login.php' && $programaPhp != 'prog/outros/registroSuporte/registroSuporte.php' && $programaPhp != 'prog/outros/registroSuporteCliente/registroSuporteCliente.php' && $g['r'][0] != "api" && $programaPhp != 'prog/sistema/scripts/atualizacaoBase.php') {
        session_destroy();
        session_start();
        if (!isset($r_api)) {
            echo <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\twindow.location = '/login';
\t\t\t\t} </script></div>
EOT;
            die;
        }
        include_once 'outros/login/index_api.php';
    }
    if ($programaPhp == 'prog/outros/registroSuporte/registroSuporte.php' && $g['r'][2] != 'verificarInteracoes' || $programaPhp != 'prog/sistema/funcoes/login.php' && $programaPhp != 'prog/sistema/funcoes/mensagens.php' && $programaPhp != 'prog/etc/etc006/grid_func-ajax.php' && !stripos($programaPhp, "/api") && $programaPhp != 'prog/sistema/funcoes/chat.php' && $programaPhp != 'prog/sistema/funcoes/util.php' && $programaPhp != 'prog/ven/ven003/form_func-ajax.php' && $programaPhp != 'prog/outros/registroSuporteCliente/registroSuporteCliente.php' && $programaPhp != 'prog/ven/ven012/form_func-ajax.php' && $programaPhp != 'prog/sistema/scripts/atualizacaoBase.php') {
        if (verificaUsuarioAtivo() && empty($r_noEmp)) {
            session_destroy();
            session_start();
            if (!isset($r_api)) {
                echo <<<EOT
\t\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\twindow.location = '/login';
\t\t\t\t\t} </script></div>
EOT;
                die;
            }
            include_once 'outros/login/index_api.php';
        }
        if (!verificaHorarioAcesso() && empty($r_noEmp)) {
            session_destroy();
            session_start();
            if (!isset($r_api)) {
                echo <<<EOT
\t\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\twindow.location = '/login';
\t\t\t\t\t} </script></div>
EOT;
                die;
            }
            include_once 'outros/login/index_api.php';
        }
    }
}
// Sem empresa selecionada: recarrega a tela. Senão executa o programa
// (com eventual personalização "-alterarPrograma.php") e encerra.
if ($g['ajax']) {
    if (!isset($r_noEmp) && empty($g['empresaAtual']) && $programaPhp != 'prog/sistema/funcoes/login.php' && !stripos($programaPhp, "/api") && $programaPhp != 'prog/outros/registroSuporte/registroSuporte.php' && $programaPhp != 'prog/outros/registroSuporteCliente/registroSuporteCliente.php' && $programaPhp != 'prog/sistema/scripts/atualizacaoBase.php') {
        echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t/*
\t\t\t\tfecharJanela('{$g['divId']}');
\t\t\t\t\$(document).ready( function(data) {
\t\t\t\t\tabrirJanela(data, 'telaLogin', '', 'telaLogin', 'Login', 'Efetuar Login no Sistema', '/sistema/funcoes/login/login/', '&tab=login', [550,200], null, 'center');
\t\t\t\t});
\t\t\t\t*/
\t\t\t\twindow.location.reload();
\t\t\t} </script></div>
EOT;
        die;
    }
    if ($programaPhp == 'prog/ven/ven012/grid.php' || $programaPhp == 'prog/ven/ven012/grid_func-ajax.php' || $programaPhp == 'prog/sistema/funcoes/mensagens.php' || $programaPhp == 'prog/etc/etc006/grid_func-ajax.php' || $programaPhp == 'prog/etc/etc006/form.php' || $programaPhp != 'prog/api/api.php' || $programaPhp == 'prog/outros/registroSuporte/registroSuporte.php' || $programaPhp == 'prog/outros/registroSuporteCliente/registroSuporteCliente.php' || $programaPhp == 'prog/etc/etc006/leituraAnexo.php') {
        session_write_close();
    }
    $_SESSION['sb'] = $confUsuario['dbPass'];
    $_SESSION['ub'] = $confUsuario['dbUser'];
    $_SESSION['Banco_Dados'] = $confUsuario['dbDatabase'];
    if (!isset($r_generateHTML)) {
        ob_start();
        $programa = trim($g['r_'], '/');
        $programa = str_replace('/', '-', $programa);
        $programaGlobal = "ide/personalizacaoConf/{$programa}-alterarPrograma.php";
        $programaGlobal_ = Apps::getDirAtivos() . "personalizacaoConf/{$programa}-alterarPrograma.php";
        if (!$g['modoDesenvolvedor'] && file_exists($programaGlobal_)) {
            $programaGlobal = $programaGlobal_;
        }
        $programaGlobal_ = Apps::getDirEditor() . "personalizacaoConf/{$programa}-alterarPrograma.php";
        if ($g['modoDesenvolvedorApp'] && file_exists($programaGlobal_)) {
            $programaGlobal = $programaGlobal_;
        }
        $programaGlobal_ = "{$g['pathDados']}personalizacaoConf/{$programa}-alterarPrograma.php";
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($programaGlobal_)) {
            $programaGlobal = $programaGlobal_;
        }
        $programaEmpresa = "ide/personalizacaoConf/{$g['empresaAtual']}{$programa}-alterarPrograma.php";
        $programaEmpresaGlobal = "{$g['pathDados']}personalizacaoConf/{$g['empresaAtual']}{$programa}-alterarPrograma.php";
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($programaEmpresaGlobal)) {
            $programaEmpresa = $programaEmpresaGlobal;
        }
        if (file_exists($programaEmpresa)) {
            $programaGlobal = $programaEmpresa;
        }
        if (file_exists($programaGlobal)) {
            $tmp = $g['r'][3];
            $g['r'][3] = '';
            include $programaPhp;
            ob_end_clean();
            ob_start();
            $g['r'][3] = $tmp;
            include $programaGlobal;
        } else {
            include $programaPhp;
        }
        $g['time']['end'] = microtime(true);
        $timeTotal = round($g['time']['end'] - $g['time']['start'], 2);
        $timeTotal = $timeTotal == 0 ? 0.01 : $timeTotal;
        $timeSql = round($g['time']['sql'], 2);
        $timePhp = round($timeTotal - $g['time']['sql'], 2);
        $timePhp = round($timePhp * 100 / $timeTotal, 0);
        $timeSql = round($timeSql * 100 / $timeTotal, 0);
        $timeInterval = $timeTotal < 2 ? 'Normal' : 'Lentas';
        if ($timeInterval != 'Normal') {
            $h = fopen("/tmp/logExecTime{$timeInterval}.txt", 'a');
            $timestamp = date('d/m/Y H:i:s');
            fwrite($h, "[{$timestamp}] [{$confUsuario['dbDatabase']}] [{$g['r_']}] {$g['usuarioAtual']} - Tempo ({$timeTotal}) - PHP ({$timePhp}%) SQL ({$timeSql}%)\n");
            fclose($h);
        }
        echo $g['conteudo']['pagina'];
        $output = ob_get_contents();
        ob_end_clean();
        echo $output;
        ob_start();
        $excludeJSFuncs = array('if', 'function', 'dialogConfirm', 'abrirJanela', 'divRefresh', 'return', 'eq', 'encodeURIComponent', 'setTimeout', 'url', 'gridPersonalizacao', 'showInfoJanela', 'acoesGrid', 'datediff', 'gridAudit', 'gridRefresh', 'filtroOrdem', 'gExcluirLinha', 'reloadUI');
        preg_match_all("/(\\.?[a-z]{3,}([a-zA-Z0-9])+)\\([^\\)]+\\)/msuU", utf8_encode($output), $matches);
        if (isset($matches[1])) {
            $funcsJS = array_unique($matches[1]);
            foreach ($funcsJS as $func) {
                if ($func[0] == '.' || in_array($func, $excludeJSFuncs)) {
                    continue;
                }
                if ($func == 'infoCustoVendaProduto' && $g['r']['2'] == 'form_func-ajax') {
                    continue;
                }
                if ($func == 'importarTray') {
                    echo $func;
                }
                if (file_exists("bibliotecas/funcoesJS/{$func}.js")) {
                    echo '<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != \'undefined\') {' . "\n//bibliotecas/funcoesJS/{$func}.js\n";
                    include_once "bibliotecas/funcoesJS/{$func}.js";
                    echo "\n" . '} </script></div>';
                }
            }
        }
        $output .= ob_get_contents();
        apc_inc("{$confUsuario['dbDatabase']}-{$g['usuarioAtual']}-hits");
        apc_inc("{$confUsuario['dbDatabase']}-{$g['usuarioAtual']}-size", strlen(gzcompress($output, 1)));
        die;
    }
    ob_start();
    $programa = trim($g['r_'], '/');
    $programa = str_replace('/', '-', $programa);
    $programaGlobal = "ide/personalizacaoConf/{$programa}-alterarPrograma.php";
    $programaGlobal_ = Apps::getDirAtivos() . "personalizacaoConf/{$programa}-alterarPrograma.php";
    if (!$g['modoDesenvolvedor'] && file_exists($programaGlobal_)) {
        $programaGlobal = $programaGlobal_;
    }
    $programaGlobal_ = Apps::getDirEditor() . "personalizacaoConf/{$programa}-alterarPrograma.php";
    if ($g['modoDesenvolvedorApp'] && file_exists($programaGlobal_)) {
        $programaGlobal = $programaGlobal_;
    }
    $programaGlobal_ = "{$g['pathDados']}personalizacaoConf/{$programa}-alterarPrograma.php";
    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($programaGlobal_)) {
        $programaGlobal = $programaGlobal_;
    }
    $programaEmpresa = "ide/personalizacaoConf/{$g['empresaAtual']}{$programa}-alterarPrograma.php";
    $programaEmpresaGlobal = "{$g['pathDados']}personalizacaoConf/{$g['empresaAtual']}{$programa}-alterarPrograma.php";
    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($programaEmpresaGlobal)) {
        $programaEmpresa = $programaEmpresaGlobal;
    }
    if (file_exists($programaEmpresa)) {
        $programaGlobal = $programaEmpresa;
    }
    if (file_exists($programaGlobal)) {
        $tmp = $g['r'][3];
        $g['r'][3] = '';
        include $programaPhp;
        ob_end_clean();
        ob_start();
        $g['r'][3] = $tmp;
        include $programaGlobal;
    } else {
        include $programaPhp;
    }
    $outputGenerateHTML = ob_get_contents();
    ob_end_clean();
} else {
    $encrypted = trim($r_r, '/');
    if (isset($g['r'][0]) && $g['r'][0] != 'login' && $g['r'][0] != 'api' && $g['r'][0] != 'erp' && $g['r'][0] != 'nfe' && !isset($r_oneWindow) && $g['r'][0] != 'usuario' && $g['r'][0] != 'empresa' && !(strlen($encrypted) == 44 && strpos($encrypted, '/') === false)) {
        echo <<<EOT
\t\t\t<div style="margin: 20px 10px;">
\t\t\t\t<b>Página não encontrada!</b><br />
\t\t\t\tPor favor, verifique se o endereço está correto.<br />
\t\t\t\tCaso o erro persista, favor entrar em contato com o Suporte Hardness.
\t\t\t</div>
EOT;
        die;
    }
}
// =====================================================================
// 14. Página completa (não AJAX): CSS, JS, tema e fontes
// =====================================================================
if (file_exists('static/css/temas/empresa/jqueryui.css')) {
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . 'static/css/temas/empresa/jqueryui.css';
} else {
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . mudarCaminhoSeFramework('static/css/temas/padrao/jqueryui.css');
}
$g['conteudo']['cssArquivo'][] = $g['pathWeb'] . 'static/css/vendor/jquery-ui-timepicker-addon.css';
if (file_exists($sys['temaCSS'])) {
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . $sys['temaCSS'];
}
$g['conteudo']['cssArquivo'][] = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css';
$g['conteudo']['cssArquivo'][] = 'https://fonts.googleapis.com/icon?family=Material+Icons';
$configFonteSistema = array();
if ($g['C007']['C007_Tamanho_Fonte_Sistema'] == '12') {
    $configFonteSistema = array('fonteGeral|font-size' => '12px', 'gridTh|font-size' => '12px', 'gridTh|padding' => '6px', 'gridTd|font-size' => '12px', 'gridTd|padding' => '9px 7px', 'tabsSistema|font-size' => '12px', 'tituloJanelaSistema|font-size' => '12px', 'tituloJanelaSistema|padding' => '.3em 0.5em', 'tabsSistema|height' => '26px', 'tabsSistema|padding' => '.45em 1em', 'dropdown-menu-bootstrap|font-size' => '12px', 'dropdown-menu-bootstrap-li|line-height' => '20px');
} elseif ($g['C007']['C007_Tamanho_Fonte_Sistema'] == '13') {
    $configFonteSistema = array('fonteGeral|font-size' => '13px', 'gridTh|font-size' => '13px', 'gridTh|padding' => '7px', 'gridTd|font-size' => '13px', 'gridTd|padding' => '8px 6px', 'tabsSistema|font-size' => '13px', 'tituloJanelaSistema|font-size' => '13px', 'tituloJanelaSistema|padding' => '.37em 0.5em', 'tabsSistema|height' => '26px', 'tabsSistema|padding' => '.45em 1em', 'dropdown-menu-bootstrap|font-size' => '13px', 'dropdown-menu-bootstrap-li|line-height' => '21px');
}
if (!$g['ajax']) {
    $tmpCss = file_get_contents(mudarCaminhoSeFramework('static/css/principal.css'));
    $tmpIni = $configFonteSistema;
    if (is_array($tmpIni)) {
        foreach ($tmpIni as $varCss => $valueCss) {
            $tmpCss = str_replace('[[' . $varCss . ']]', trim($valueCss), $tmpCss);
        }
    }
    if ($g['modoDesenvolvedor']) {
        $arquivos = array('ide/' . "personalizacaoConf/GLOBAL-cssVars.ini");
    } elseif ($g['modoDesenvolvedorApp']) {
        $arquivos = array('ide/' . "personalizacaoConf/GLOBAL-cssVars.ini", Apps::getDirAtivos() . "personalizacaoConf/GLOBAL-cssVars.ini", Apps::getDirEditor() . "personalizacaoConf/GLOBAL-cssVars.ini");
    } else {
        $arquivos = array('ide/' . "personalizacaoConf/GLOBAL-cssVars.ini", Apps::getDirAtivos() . "personalizacaoConf/GLOBAL-cssVars.ini", $g['pathDados'] . "personalizacaoConf/GLOBAL-cssVars.ini");
    }
    foreach ($arquivos as $tmpIni) {
        if (file_exists($tmpIni)) {
            $tmpIni = file($tmpIni);
            foreach ($tmpIni as $linhaNr => $linha) {
                $linha = trim($linha);
                if (!(preg_match("/^\\/\\/.*/", $linha) || empty($linha))) {
                    $linha = explode('=', $linha);
                    $tmpCss = str_replace('[[' . trim($linha[0]) . ']]', trim($linha[1]), $tmpCss);
                }
            }
        }
    }
    $tmpIni = file('configuracoes/_cssVars.ini');
    foreach ($tmpIni as $linhaNr => $linha) {
        $linha = trim($linha);
        if (!(preg_match("/^\\/\\/.*/", $linha) || empty($linha))) {
            $linha = explode('=', $linha);
            $tmpCss = str_replace('[[' . trim($linha[0]) . ']]', trim($linha[1]), $tmpCss);
        }
    }
    file_put_contents('static/css/temas/tema_' . $confUsuario['dbDatabase'] . '_cache.css', $tmpCss);
}
$g['conteudo']['cssArquivo'][] = $g['pathWeb'] . 'static/css/temas/tema_' . $confUsuario['dbDatabase'] . '_cache.css?load=' . mt_rand();
if (!isset($r_generateHTML)) {
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/jquery-1.6.2.min.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/jquery-ui-1.8.16.custom.min.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/ui/timepicker.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/ui/timepicker-i18n.js';
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . 'static/css/fileuploader.css';
    if ($g['clienteAtualContato'] > 0) {
        $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/sistemaB2B_3.js';
    } else {
        $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . mudarCaminhoSeFramework('static/js/sistema.js') . '?load=' . mt_rand();
        $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/vanilla-picker.min.js';
    }
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/jquery.showLoading.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/jquery.form.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/swfobject.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/fileuploader.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/nicEdit.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/mask.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/json2.min.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/jquery.ui.multidraggable-1.8.8.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/shortcut.js?version=4';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/flotr2/lib/yepnope.js?load=1';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/flotr2/flotr2.min.js?load=1';
    $g['conteudo']['jsArquivo'][] = 'https://kit.fontawesome.com/67c8471bba.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/smt-print.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/print.js';
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . 'bibliotecas/codemirror-3.20/lib/codemirror.css';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'bibliotecas/codemirror-3.20/lib/codemirror.js';
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . 'bibliotecas/codemirror-3.20/theme/monokai.css';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'bibliotecas/codemirror-3.20/mode/xml/xml.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'bibliotecas/codemirror-3.20/mode/javascript/javascript.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'bibliotecas/codemirror-3.20/mode/css/css.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'bibliotecas/codemirror-3.20/mode/clike/clike.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'bibliotecas/codemirror-3.20/mode/php/php.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . mudarCaminhoSeFramework('static/bootstrapErp/js/bootstrap.js');
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . mudarCaminhoSeFramework('static/bootstrapErp/css/bootstrap.css');
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/go.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/jquery.zclip.min.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/html2canvas.js';
    $tema = array_key_exists('C007_Tema', $g['C007']) ? '2021' : 'principal';
    if (!empty($g['C007']['C007_Tema'])) {
        $tema = $g['C007']['C007_Tema'];
    }
    if (isset($r_tema)) {
        $tema = $r_tema;
    }
    $tema = trim($tema);
    if ($tema != 'principal') {
        $tema = 'tema-' . $tema;
        $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . mudarCaminhoSeFramework("static/css/{$tema}.css") . '?load=' . mt_rand();
    }
    $pathPersonalizationThemeByHardness = mudarCaminhoSeFramework("static/css/temas/hardness/{$tema}.css");
    if (file_exists($pathPersonalizationThemeByHardness)) {
        $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . $pathPersonalizationThemeByHardness . '?load=' . mt_rand();
    }
    $pathPersonalizationTheme = Personalizacoes::arquivoCSS($tema);
    if (!empty($pathPersonalizationTheme)) {
        $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . $pathPersonalizationTheme . '?load=' . mt_rand();
    }
    if (!empty($g['C007']['C007_Tema_Layout'])) {
        $temaLayout = $g['C007']['C007_Tema_Layout'];
        $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . mudarCaminhoSeFramework('static/css/layout-' . trim($temaLayout) . '.css') . '?load=' . mt_rand();
    }
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/monaco-editor/0.25.2/min/vs/loader.js';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/monaco-editor/0.25.2/min/vs/editor/editor.main.js';
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . 'static/js/monaco-editor/0.25.2/min/vs/editor/editor.main.css';
    $g['conteudo']['jsArquivo'][] = $g['pathWeb'] . 'static/js/tinymce.min.js';
    $g['conteudo']['cssArquivo'][] = $g['pathWeb'] . 'static/css/iso_bootstrap4.0.0min.css';
}
if (isset($r_oneWindow)) {
    $g['conteudo']['cssTexto'] .= <<<EOT
\t\t#desktop {
\t\t\ttop: auto;
\t\t}
EOT;
}
if ($sys['dominio'] == 'hardness.com.br') {
    $cssExtra .= <<<EOT
\t/**
\t * jQuery showLoading plugin
\t*/
\t.loading-indicator, .loading-indicator-manual {
\t\tbackground: url( '/hardness3/static/img/loading_hardness.gif' );
\t\tbackground-repeat: no-repeat;
\t\tbackground-position: center center;
\t}
EOT;
    if ($tema == '2021' || $tema == 'claro') {
        $cssExtra .= <<<EOT
\t/* Logo do topo */
\t#logo {
\t\tbackground-image: url( '/hardness3/static/img/logo_hardness_2.png' ) !important;
\t}
EOT;
    }
}
if ($sys['dominio'] == 'unityerp.online') {
    $cssExtra .= <<<EOT
\t/**
\t * jQuery showLoading plugin
\t*/
\t.loading-indicator, .loading-indicator-manual {
\t\tbackground: url( '/hardness3/static/img/loading_unityerp.gif' );
\t\tbackground-repeat: no-repeat;
\t\tbackground-position: center center;
\t}
EOT;
}
if ($sys['dominio'] == 'fourmssistemas.com.br') {
    $cssExtra .= <<<EOT
\t/**
\t * jQuery showLoading plugin
\t*/
\t.loading-indicator, .loading-indicator-manual {
\t\tbackground: url( '/hardness3/static/img/loading_fourmssistemas.gif' );
\t\tbackground-repeat: no-repeat;
\t\tbackground-position: center center;
\t}
EOT;
}
// =====================================================================
// 15. Dispositivo (desktop/mobile) e montagem do menu
// =====================================================================
$cssJs = processaCssJs();
$g['smarty']->assign('cssJs', $cssJs);
if (empty($_SESSION['device'])) {
    $isMobileDevice = $g['mobileDetect']->isMobile() || $g['mobileDetect']->isTablet();
    $_SESSION['device'] = $isMobileDevice ? 'mobile' : 'desktop';
}
if ($_SESSION['device'] == 'mobile') {
    $flagPath = "{$g['pathDadosUsuario']}mobile.flag";
    if (file_exists($flagPath)) {
        $testMobileFlag = trim(file_get_contents($flagPath));
        if (strcasecmp($testMobileFlag, 'true') === 0 || $testMobileFlag === '1') {
            $_SESSION['device'] = 'mobile';
        } else {
            $_SESSION['device'] = 'desktop';
        }
    } else {
        $_SESSION['device'] = 'desktop';
    }
}
$g['smarty']->assign('device', $_SESSION['device']);
$g['smarty']->assign('permissaoLojaCliente', gVerificaRoot() || $g['C007']['C007_Flag_Vendedor'] == 'D');
if (false && $g['mobileDetect']->isMobile()) {
    $cssExtra .= <<<EOT
\t\tbody {
\t\t\tfont-size: 16px;
\t\t}
\t\t.ui-widget {
\t\t\tfont-size: 14px;
\t\t}
\t\t.ui-tabs .ui-tabs-nav li a, .ui-tabs.ui-tabs-collapsible .ui-tabs-nav li.ui-tabs-selected a {
\t\t\tfont-size: 15px;
\t\t}
\t\t.ui-tabs .ui-tabs-nav li {
\t\t\theight: 29px;
\t\t}
\t\tinput.whiteBg, .ui-widget-content input.whiteBg, .ui-widget-header input.whiteBg {
\t\t\tfont-size: 15px;
\t\t}
\t\t.btn-group > .btn-mini {
\t\t\tfont-size: 13.5px;
\t\t}
\t\t.btn-group > .btn-mini + .dropdown-toggle {
\t\t\theight: 25px;
\t\t\ttop: -8px;
\t\t}
\t\t.grid, .gridFixo {
\t\t\tfont-size: 15px;
\t\t}
\t\t.grid thead th, .gridFixo thead th, .gridTheadTh {
\t\t\tfont-size: 18px;
\t\t}
\t\tul.menu li a:hover {
\t\t\tfont-size: 16px;
\t\t}
\t\t.btn-group > .btn, .btn-group > .dropdown-menu, .btn-group > .popover {
\t\t\tfont-size: 15px;
\t\t}
\t\t.ui-widget input, .ui-widget select, .ui-widget textarea, .ui-widget button {
\t\t\tfont-size: 16px;
\t\t}
\t\t.ui-datepicker table {
\t\t\tfont-size: 17px;
\t\t}
\t\t.ui-datepicker {
\t\t\twidth: 20em;
\t\t}
\t\t.buttonsBottomTop > span > label > input {
\t\t\theight: 21px;
\t\t}
\t\t.dropdown-menu > li > a {
\t\t\tline-height: 21px;
\t\t}
\t\t.formFormulario input.formInput, .ui-widget-content .formFormulario input.formInput, .ui-widget-header .formFormulario input.formInput {
\t\t\tfont-size: 17px;
\t\t\theight: 24px;
\t\t\tmargin-bottom: 4px;
\t\t}
\t\t.formFormulario select.formInput, .ui-widget-content .formFormulario select.formInput, .ui-widget-header .formFormulario select.formInput {
\t\t\tfont-size: 17px;
\t\t\theight: 24px;
\t\t\tmargin-bottom: 4px;
\t\t}
\t\t.formFormulario textarea.formInput, .ui-widget-content .formFormulario textarea.formInput, .ui-widget-header .formFormulario textarea.formInput {
\t\t\tfont-size: 17px;
\t\t\tmargin-bottom: 4px;
\t\t}
EOT;
}
$g['smarty']->assign('cssExtra', $cssExtra);
if ($g['clienteAtualContato'] > 0) {
    $g['smarty']->assign('menu', $g['smarty']->fetch('menuB2B.tpl'));
} else {
    if ($g['r'][0] == 'nfe') {
        require_once 'bibliotecas/menu_nfe.php';
    } else {
        require_once 'bibliotecas/menu.php';
    }
    if ($g['modoDesenvolvedor']) {
        $dirs = array("ide/personalizacaoConf/");
    } elseif ($g['modoDesenvolvedorApp']) {
        $dirs = array("ide/personalizacaoConf/", Apps::getDirAtivos() . "personalizacaoConf/", Apps::getDirEditor() . "personalizacaoConf/");
    } else {
        $dirs = array("ide/personalizacaoConf/", Apps::getDirAtivos() . "personalizacaoConf/", "{$g['pathDados']}personalizacaoConf/");
    }
    $arquivos = array('GLOBAL-menu.php', $g['empresaAtual'] . 'GLOBAL-menu.php', 'GLOBAL-menuBuilder.php');
    foreach ($arquivos as $arquivo) {
        foreach ($dirs as $dir) {
            if (file_exists($dir . $arquivo)) {
                require_once $dir . $arquivo;
            }
        }
    }
    $menuNormal = $menuEditor = $menuArrayIds = array();
    foreach ($menu as $nivel1 => $nivel1_) {
        foreach ($nivel1_ as $nivel2 => $nivel2_) {
            if (isset($_COOKIE['editarMenu']) && $_COOKIE['editarMenu'] == 'hardness') {
                if (!is_array($nivel2_)) {
                    $menuNormal[$nivel1][$nivel2] = $nivel2_;
                } else {
                    $menuEditor[$nivel1][$nivel2] = (int) isset($g['c029Ids'][key($nivel2_)]);
                    $menuArrayIds[$nivel1][$nivel2] = array(key($nivel2_), $g['perfilUsuario']);
                    isset($menuNormal[$nivel1]) or $menuNormal[$nivel1] = array();
                    $menuNormal[$nivel1][$nivel2] = array_pop($nivel2_);
                }
            } elseif (!is_array($nivel2_)) {
                $menuNormal[$nivel1][$nivel2] = $nivel2_;
            } elseif (isset($g['c029Ids'][key($nivel2_)])) {
                isset($menuNormal[$nivel1]) or $menuNormal[$nivel1] = array();
                $key2 = key($nivel2_);
                $menuNormal[$nivel1][$nivel2] = array_pop($nivel2_);
                if (is_array($menuNormal[$nivel1][$nivel2])) {
                    $menuNormal[$nivel1][$nivel2][1] = "abrirJanelaPermissao = '{$key2}'; " . $menuNormal[$nivel1][$nivel2][1];
                } else {
                    $menuNormal[$nivel1][$nivel2] = "abrirJanelaPermissao = '{$key2}'; " . $menuNormal[$nivel1][$nivel2];
                }
            }
        }
    }
    $g['smarty']->assign('modoDesenvolvedor', $g['modoDesenvolvedor']);
    if ($g['modoDesenvolvedorApp']) {
        $app = Apps::getApp($g['modoDesenvolvedorApp']);
        $g['smarty']->assign('modoDesenvolvedorApp', "{$app['nome']} ({$app['versao']})");
    }
    $g['smarty']->assign('editor', '0');
    if (empty($g['empresaAtual'])) {
        $g['smarty']->assign('menuArray', array('Sistema de Gestão Empresarial' => false));
        if ($compacto) {
            $g['smarty']->assign('menu', $g['smarty']->fetch('menuERP.tpl'));
        } else {
            $g['smarty']->assign('menu', $g['smarty']->fetch('menu.tpl'));
        }
    } else {
        $g['smarty']->assign('menuArray', $menuNormal);
        $g['smarty']->assign('menuArrayPermissao', $menuEditor);
        if (isset($_COOKIE['editarMenu']) && $_COOKIE['editarMenu'] == 'hardness') {
            $menuArrayTotais = $menuArrayTotaisPerfis = array();
            foreach ($menuEditor as $key => $value) {
                $menuArrayTotais[$key] = 0;
                foreach ($value as $key2 => $value2) {
                    $menuArrayTotais[$key] += $value2 ? 1 : 0;
                    $res = mysql_query("SELECT C030_Id FROM C030 WHERE C030_C029_Id = '{$menuArrayIds[$key][$key2][0]}' AND C030_Acesso_Liberado = 'S';");
                    $menuArrayTotaisPerfis[$key][$key2] = mysql_numrows($res);
                }
            }
            $g['smarty']->assign('menuArrayTotais', $menuArrayTotais);
            $g['smarty']->assign('menuArrayTotaisPerfis', $menuArrayTotaisPerfis);
            $g['smarty']->assign('menuArrayIds', $menuArrayIds);
            $g['smarty']->assign('menu', $g['smarty']->fetch('menuEditor.tpl'));
        } elseif ($compacto) {
            $g['smarty']->assign('menu', $g['smarty']->fetch('menuERP.tpl'));
        } else {
            $g['smarty']->assign('menu', $g['smarty']->fetch('menu.tpl'));
        }
    }
}
if (isset($r_T003_Flag_Expedicao)) {
    $menu = <<<EOT
<ul id="barra" class="menubar ui-widget-header ui-corner-bottom">
\t<li class="menuvertical" style="margin-top: 24px;"><span onclick="" style="cursor: pointer; font-weight: bold;">&nbsp;&nbsp;HARDNESS SISTEMAS - EMISSÃO DE NF-E</span></li>
</ul>
EOT;
    $g['smarty']->assign('menu', $menu);
}
// =====================================================================
// 16. Variáveis do cabeçalho: usuário, empresa, seletores de troca
// =====================================================================
$g['smarty']->assign('titulo', !empty($g['c004']['C004_Nome_Abreviado']) ? $g['c004']['C004_Nome_Abreviado'] . ' - Hardness Sistemas' : 'Hardness Sistemas');
$menuextra = isset($g['c004']['C004_Menu_Extra_Html']) ? $g['c004']['C004_Menu_Extra_Html'] : '';
$menuextra = str_replace('[[empresaAtual]]', $g['empresaAtual'], $menuextra);
$g['smarty']->assign('menuextra', $menuextra);
$g['smarty']->assign('oneWindow', isset($r_oneWindow) ? true : false);
$g['smarty']->assign('usuarioAtual', isset($g['usuarioAtual']) && $g['usuarioAtual'] > 0 ? $g['usuarioAtual'] : false);
$g['smarty']->assign('usuarioAtualNome', isset($g['usuarioAtual']) && $g['usuarioAtual'] > 0 ? strtoupper($g['c007']['C007_Login']) : false);
$g['smarty']->assign('usuarioAtualPerfil', isset($g['usuarioAtual']) && $g['usuarioAtual'] > 0 ? $g['c021']['C021_Nome_Perfil'] : false);
$g['smarty']->assign('dbDatabase', $confUsuario['dbDatabase']);
$dbUsuario = mysql_fetch_array(mysql_query("select user()"));
$dbUsuario = $dbUsuario[0];
$g['smarty']->assign('dbUsuario', $dbUsuario);
if (isset($g['clienteAtualContato'])) {
    $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id WHERE D013_Id = '{$g['clienteAtualContato']}'");
    $resD024 = mysql_fetch_array($sqlD024);
    $g['smarty']->assign('clienteAtualNome', $resD024['D024_Nome_Empresa']);
    $g['smarty']->assign('clienteAtual', $g['clienteAtual']);
    $g['smarty']->assign('clienteAtualContato', $g['clienteAtualContato']);
    $g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
}
if (isset($g['usuarioAtual']) && $g['usuarioAtual'] > 0) {
    $g['smarty']->assign('modoDesenvolvedor', $g['modoDesenvolvedor']);
    if ($g['modoDesenvolvedorApp']) {
        $app = Apps::getApp($g['modoDesenvolvedorApp']);
        $g['smarty']->assign('modoDesenvolvedorApp', "{$app['nome']} ({$app['versao']})");
    }
    $sqlC004 = mysql_query("SELECT C004_Nome_Empresa, C004_Nome_Abreviado FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
    $resC004 = mysql_fetch_array($sqlC004);
    $empresaAtualNome = $resC004['C004_Nome_Empresa'];
    $empresaAtualNomeAbreviado = $resC004['C004_Nome_Abreviado'];
    $g['smarty']->assign('empresaAtualNome', $empresaAtualNome);
    $g['smarty']->assign('empresaAtualNomeAbreviado', $empresaAtualNomeAbreviado);
    $g['smarty']->assign('usuarioAtual', $g['usuarioAtual']);
    $selectEmpresa = '';
    $sql = "SELECT C004_Id, C004_Nome_Empresa, C004_Nome_Abreviado FROM C004 LEFT JOIN C019 ON C019_C007_Id={$g['usuarioAtual']} AND C019_C004_Id=C004_Id WHERE C004_Flag_Ativo='S' AND C019_C007_Id IS NOT NULL ORDER BY C004_Ordem_Empresa,C004_Id ASC;";
    $res = mysql_query($sql);
    if (mysql_num_rows($res) == 1) {
        $g['smarty']->assign('selectEmpresa', false);
    } else {
        while ($row = mysql_fetch_assoc($res)) {
            $selected = $row['C004_Id'] == $g['empresaAtual'] ? ' selected="selected"' : '';
            $selectEmpresa .= "<option value='{$row['C004_Id']}'{$selected}>{$row['C004_Nome_Abreviado']}</option>";
        }
        $g['smarty']->assign('selectEmpresa', $selectEmpresa);
    }
    if (isset($g['c029Ids'][105]) || isset($_SESSION['podeMudarUsuarios'])) {
        if (!isset($_SESSION['podeMudarUsuarios'])) {
            $_SESSION['podeMudarUsuarios'] = array($g['usuarioAtual'], $g['C007']['C007_Flag_Vendedor']);
        }
        $selectUsuario = '';
        if ($g['isRoot']) {
            $sql = "SELECT C007_Id, C007_Login, C007_Primeiro_Nome, C021_Nome_Perfil FROM C007 left join C019 on C019_C007_Id=C007_Id LEFT JOIN C021 ON C021_Id = C019_C021_Id WHERE C019_C004_Id='{$g['empresaAtual']}' and C007_Ativo='S' and (C007_D024_Id=0 or C007_D024_Id is null) ORDER BY C007_Login ASC;";
        } elseif ($_SESSION['podeMudarUsuarios'][1] == 'D' && $g['C031']['tipoSistema'] == 'C') {
            $sql = "SELECT C007_Id, C007_Login, C007_Primeiro_Nome, C021_Nome_Perfil FROM C007 left join C019 on C019_C007_Id=C007_Id LEFT JOIN C021 ON C021_Id = C019_C021_Id WHERE C019_C004_Id='{$g['empresaAtual']}' and C007_Ativo='S' and (C007_D024_Id=0 or C007_D024_Id is null) AND C007_Id != 63 ORDER BY C007_Login ASC;";
        } else {
            $sql = "SELECT C007_Id, C007_Login, C007_Primeiro_Nome, C021_Nome_Perfil FROM C007 left join C019 on C019_C007_Id=C007_Id LEFT JOIN C021 ON C021_Id = C019_C021_Id WHERE C019_C004_Id='{$g['empresaAtual']}' and C007_Ativo='S' and (C007_D024_Id=0 or C007_D024_Id is null) and (C007_C007_Id = '{$_SESSION['podeMudarUsuarios'][0]}' OR C007_Id = '{$_SESSION['podeMudarUsuarios'][0]}' AND C007_Id != 63) ORDER BY C007_Login ASC;";
        }
        $res = mysql_query($sql);
        while ($row = mysql_fetch_assoc($res)) {
            $row['C007_Login'] = mb_strtoupper($row['C007_Login']);
            $row['C007_Primeiro_Nome'] = explode(' ', $row['C007_Primeiro_Nome']);
            $row['C007_Primeiro_Nome'] = $row['C007_Primeiro_Nome'][0];
            $selectUsuarioNomes[$row['C007_Id']] = "{$row['C007_Login']}";
            if ($row['C007_Login'] != $row['C007_Primeiro_Nome']) {
                $row['C007_Primeiro_Nome'] = mb_convert_case(mb_strtolower($row['C007_Primeiro_Nome']), MB_CASE_TITLE);
                $selectUsuarioNomes[$row['C007_Id']] .= " ({$row['C007_Primeiro_Nome']})";
            }
        }
        $maiorNome = 0;
        foreach ($selectUsuarioNomes as $nome) {
            if (mb_strlen($nome) > $maiorNome) {
                $maiorNome = mb_strlen($nome);
            }
        }
        $res = mysql_query($sql);
        while ($row = mysql_fetch_assoc($res)) {
            $selected = $row['C007_Id'] == $g['usuarioAtual'] ? ' selected="selected"' : '';
            $row['C021_Nome_Perfil'] = mb_convert_case(mb_strtolower($row['C021_Nome_Perfil']), MB_CASE_TITLE);
            $nome = mb_str_pad($selectUsuarioNomes[$row['C007_Id']], $maiorNome, ' ');
            $nome = str_replace(' ', '&nbsp;', $nome);
            $selectUsuario .= "<option value='{$row['C007_Id']}'{$selected}>{$nome}&nbsp;&nbsp;{$row['C021_Nome_Perfil']}</option>";
        }
        $g['smarty']->assign('selectUsuario', $selectUsuario);
    } else {
        $g['smarty']->assign('selectUsuario', false);
    }
    $g['smarty']->assign('selectUsuarioMensagem', gProcessaSelect(gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', "left join C019 on C019_C007_Id=C007_Id WHERE C019_C004_Id='{$g['empresaAtual']}' and C007_Ativo='S' and (C007_D024_Id=0 or C007_D024_Id is null) and C007_Id != '63' ORDER BY C007_Primeiro_Nome ASC", true)));
}
// Modo "gerar HTML" (exportação de grid / e-mail)
if (isset($r_generateHTML)) {
    if (isset($r_generateEmail)) {
        $outputGenerateHTML = str_replace('span class="item" style="', 'span style="display: block; float: left; margin-right: 5px; margin-top: 1px; ', $outputGenerateHTML);
    }
    $g['smarty']->assign('outputGenerateHTML', $outputGenerateHTML);
    $g['smarty']->display('indexGridHTML.tpl');
    die;
}
$g['smarty']->assign('empresaAtual', $g['empresaAtual']);
if ($g['modoDesenvolvedor']) {
    $personalizacaoConf = file_exists("ide/personalizacaoConf/") ? scandir("ide/personalizacaoConf/") : array();
} elseif ($g['modoDesenvolvedorApp']) {
    $personalizacaoConf = file_exists("ide/personalizacaoConf/") ? scandir("ide/personalizacaoConf/") : array();
    $personalizacaoConf = file_exists(Apps::getDirAtivos() . "personalizacaoConf/") ? scandir(Apps::getDirAtivos() . "personalizacaoConf/") : $personalizacaoConf;
    $personalizacaoConf = file_exists(Apps::getDirEditor() . "personalizacaoConf/") ? scandir(Apps::getDirEditor() . "personalizacaoConf/") : $personalizacaoConf;
} else {
    $personalizacaoConf = file_exists("ide/personalizacaoConf/") ? scandir("ide/personalizacaoConf/") : array();
    $personalizacaoConf = file_exists(Apps::getDirAtivos() . "personalizacaoConf/") ? scandir(Apps::getDirAtivos() . "personalizacaoConf/") : $personalizacaoConf;
    $personalizacaoConf = file_exists("{$g['pathDados']}personalizacaoConf/") ? scandir("{$g['pathDados']}personalizacaoConf/") : $personalizacaoConf;
}
$alertaEmailRecebido = $g['C031']['alertaEmailRecebido'];
$g['smarty']->assign('personalizacaoConf', json_encode($personalizacaoConf));
$g['smarty']->assign('alertaEmailRecebido', json_encode($alertaEmailRecebido));
$menuExtraEmpresas = array();
$res = mysql_query("SELECT C004_Id, C004_Menu_Extra_Html FROM C004;");
if ($res) {
    while ($row = mysql_fetch_assoc($res)) {
        $menuExtraEmpresas[$row['C004_Id']] = str_replace('[[empresaAtual]]', $row['C004_Id'], $row['C004_Menu_Extra_Html']);
    }
}
$g['smarty']->assign('menuExtraEmpresas', json_encode($menuExtraEmpresas));
// =====================================================================
// 17. Teclas de atalho, boletos, certificado digital e avisos
// =====================================================================
$teclasAtalho = $teclasAtalhoF1 = '';
$teclasAtalhoF1 .= "<span style=\"font-size: 1.3em; font-weight: bold;\">F1</span> (Ajuda Teclas de Atalho)<br />";
$res = mysql_query("SELECT * FROM D142 ORDER BY D142_Atalho ASC;");
if ($res) {
    while ($row = mysql_fetch_assoc($res)) {
        $teclasAtalho .= <<<EOT
shortcut.add("{$row['D142_Atalho']}",function() {
\t\$.getJSON('/cad/cad055/ajax/verificarPermissao/?ajax=true&C029_Id=' + encodeURIComponent('{$row['D142_C029_Id']}') + '&callback=?', function(request) {
\t\tif (request.code) {
\t\t\t{$row['D142_Codigo']}
\t\t\t\$('#teclasf1').remove();
\t\t}
\t});
\treturn false;
});
EOT;
        $teclasAtalhoF1 .= "<span style=\"font-size: 1.3em; font-weight: bold;\">{$row['D142_Atalho']}</span> ({$row['D142_Nome']})<br />";
    }
}
$teclasAtalhoF1 .= "<span style=\"font-size: 1.3em; font-weight: bold;\">Esc</span> (Fechar esta Janela)<br />";
$teclasAtalho .= <<<EOT
shortcut.add("F1",function() {
\tif (\$('#teclasf1').size() != 0) { return false; }
\t\$('<div>')
\t\t.attr('id', 'teclasf1')
\t\t.css({
\t\t\t'width': '100%',
\t\t\t'position': 'fixed',
\t\t\t'top': '150px',
\t\t\t'text-align': 'center',
\t\t\t'color': 'white',
\t\t\t'font-weight': 'normal',
\t\t\t'font-size': '18px',
\t\t\t'z-index': '999999999',
\t\t\t'background-color': 'black',
\t\t\t'padding': '15px 0'
\t\t})
\t\t.html('{$teclasAtalhoF1}')
\t\t.appendTo('body');
\tshortcut.add("Esc",function() {
\t\t\tshortcut.remove("Esc");
\t\t\t\$('#teclasf1').remove();
\t\t\treturn false;
\t});
});
EOT;
$g['smarty']->assign('teclasAtalho', $teclasAtalho);
$arquivo = "{$g['pathDados']}personalizacaoTab/janelasIgnorarESC.serialized";
if (file_exists($arquivo)) {
    $janelasIgnorarESC = file_get_contents($arquivo);
    if (empty($janelasIgnorarESC)) {
        $janelasIgnorarESC = '{}';
    }
} else {
    $janelasIgnorarESC = '{}';
}
$g['smarty']->assign('janelasIgnorarESC', $janelasIgnorarESC);
// A base local não consulta boleto do fornecedor.
$versao = file_get_contents('version.txt');
$versaoHtml = '';
$g['smarty']->assign('versao', $versaoHtml);
if ($g['nivelAlcada'] >= 4 || isset($g['c029Ids'][20]) || isset($g['c029Ids'][36])) {
    $alertaVencimentoCertificadoDigital1 = $g['C031']['alertaVencimentoCertificadoDigital1'];
    $alertaVencimentoCertificadoDigital2 = $g['C031']['alertaVencimentoCertificadoDigital2'];
    if ($alertaVencimentoCertificadoDigital1 > 0) {
        $certificadoVencido = false;
        $cert = '';
        $cSql_Empresa = @mysql_query("select * from C004 left join D018 on D018_Id=C004_D018_Id where C004_Id='{$g['empresaAtual']}'");
        $mEmpresa = @mysql_fetch_array($cSql_Empresa);
        if ($g['c020']['C020_NFe_Tipo_Certificado'] == 'A1' and $g['c020']['C020_NFe_Habilitada'] == 'S') {
            require_once 'old/lib/funcoes.php';
            require_once 'prog/ven/ven012/grid_func-callback.php';
            if (!empty($mEmpresa['C004_Certificado_Digital'])) {
                // Certificado armazenado no banco (base64)
                $mkt_Hoje = time();
                $mktVencimentoCertificado = pegaValidadeCertificado(base64_decode($mEmpresa['C004_Certificado_Digital']), $mEmpresa['C004_Senha_Certificados'], 'S');
            } elseif (!empty($mEmpresa['C004_Localizacao_Certificados'])) {
                // Certificado em arquivo .pfx no disco
                $cArquivo_Certificado_Digital = $mEmpresa['C004_Localizacao_Certificados'] . "{$g['empresaAtual']}.pfx";
                if (file_exists($cArquivo_Certificado_Digital)) {
                    $mkt_Hoje = time();
                    $mktVencimentoCertificado = pegaValidadeCertificadoA1($cArquivo_Certificado_Digital, $mEmpresa['C004_Senha_Certificados']);
                }
            }
            $mostraValidadeCertificado = false;
            if (!empty($mktVencimentoCertificado)) {
                $VencimentoCertificado = date("d/m/Y  h:i:s", $mktVencimentoCertificado);
                $diasVencimentoCertificado = $mktVencimentoCertificado - time();
                if ($mktVencimentoCertificado >= $mkt_Hoje) {
                    $diasVencimentoCertificado = floor($diasVencimentoCertificado / 60 / 60 / 24);
                    if ($diasVencimentoCertificado < 1) {
                        $mostraValidadeCertificado = true;
                        $isCertificadoValido = true;
                        $cert = <<<EOT
\t\t\t            \t<span style='font-size:16px;color:white;background-color:#FF0000;padding:2px'>&nbsp<b>VENCIMENTO CERTIFICADO DIGITAL</b>&nbsp<BR></span>
\t\t\t            \t<span style='font-size:8px;'><BR></span>
\t\t\t\t\t\t\t<span style='font-size:14px;'><b>SEU CERTIFICADO DIGITAL VENCE <span style='color:#FF0000;font-size:20px;'><strong>HOJE!</strong></span></b><BR><BR></span>\t\t\t\t\t
\t\t                    <span style='font-size:13px;'><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span>
\t\t\t\t            <script type="text/javascript">
\t\t\t\t\t            var diasVencimentoCertificado = {$diasVencimentoCertificado};
\t\t\t\t\t            dialogConfirm("<span style='color:white;font-size:18px;'<b>VENCIMENTO CERTIFICADO DIGITAL </b></span>", 
\t\t\t\t\t           \t" <span style='font-size:14px;'><b>SEU CERTIFICADO DIGITAL VENCE <span style='color:#FF0000;font-size:20px;'><strong>HOJE!</strong></span></b><BR><BR></span><span style='font-size:14px;'><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span> ");\t\t\t            \t
\t\t\t\t            </script>

EOT;
                    } elseif ($diasVencimentoCertificado == 1) {
                        $mostraValidadeCertificado = true;
                        $isCertificadoValido = true;
                        $cert = <<<EOT
\t\t\t            \t<span style='font-size:16px;color:white;background-color:#FF0000;padding:2px'>&nbsp<b>VENCIMENTO CERTIFICADO DIGITAL</b>&nbsp<BR></span>
\t\t\t            \t<span style='font-size:8px;'><BR></span>
\t\t\t\t\t\t\t<span style='font-size:14px;'><b>SEU CERTIFICADO DIGITAL VENCE <span style='color:#FF0000;font-size:20px;'><strong>AMANHÃ!</strong></span></b><BR><BR></span>\t\t\t\t\t
\t\t                    <span style='font-size:13px;'><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span>
\t\t                    <script type="text/javascript">
\t\t\t\t\t            dialogConfirm("<span style='color:white;font-size:18px;'<b>VENCIMENTO CERTIFICADO DIGITAL </b></span>", 
\t\t\t\t\t            " <span style='font-size:14px;'><b>SEU CERTIFICADO DIGITAL VENCE <span style='color:#FF0000;font-size:20px;'><strong>AMANHÃ!</strong></span></b><BR><BR></span><span style='font-size:14px;'><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span> ");\t\t\t            \t
\t\t\t\t            </script>\t\t            
EOT;
                    } elseif ($diasVencimentoCertificado > 1 && $diasVencimentoCertificado <= $alertaVencimentoCertificadoDigital2) {
                        $mostraValidadeCertificado = true;
                        $isCertificadoValido = true;
                        $cert = <<<EOT
\t\t\t            \t<span style='font-size:16px;color:white;background-color:#FF0000;padding:2px'>&nbsp<b>VENCIMENTO CERTIFICADO DIGITAL</b>&nbsp<BR></span>
\t\t\t            \t<span style='font-size:8px;'><BR></span>
\t\t\t\t\t\t\t<span style='font-size:13px;'><b>Seu certificado digital vence em <span style='color:#FF0000;'>{$diasVencimentoCertificado} dias! </span></b><BR><BR></span>\t\t\t\t\t
\t\t                    <span style='font-size:13px;'><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span>
\t\t\t\t\t\t\t<script type="text/javascript">
\t\t\t\t\t            var diasVencimentoCertificado = {$diasVencimentoCertificado};
\t\t\t\t\t            dialogConfirm("<span style='color:white;font-size:18px;'<b>VENCIMENTO CERTIFICADO DIGITAL </b></span>", 
\t\t\t\t\t            " <span style='font-size:14px;'><b>Seu certificado digital vence em <span style='color:#FF0000;'>{$diasVencimentoCertificado} dias! </span></b><BR><BR><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span> ");\t\t\t            \t
\t\t\t\t            </script>\t\t             
EOT;
                    } elseif ($diasVencimentoCertificado > 1 && $diasVencimentoCertificado <= $alertaVencimentoCertificadoDigital1) {
                        $mostraValidadeCertificado = true;
                        $isCertificadoValido = true;
                        $cert = <<<EOT
\t\t\t            \t<span style='font-size:16px;padding:2px'>&nbsp<b>VENCIMENTO CERTIFICADO DIGITAL</b>&nbsp<BR></span>
\t\t\t            \t<span style='font-size:8px;'><BR></span>
\t\t\t\t\t\t\t<span style='font-size:13px;'><b>Seu certificado digital vence em  <span style='color:#FF0000;'>{$diasVencimentoCertificado} dias! </span></b><BR><BR></span>\t\t\t\t\t
\t\t                    <span style='font-size:13px;'><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span>

EOT;
                    }
                } else {
                    $mostraValidadeCertificado = true;
                    $certificadoVencido = true;
                    $diasVencimentoCertificado = $diasVencimentoCertificado * -1;
                    $diasVencimentoCertificado = floor($diasVencimentoCertificado / 60 / 60 / 24);
                    $cert = <<<EOT
\t\t\t           \t\t<span style='font-size:16px;color:#fff0e6; background-color:#FF0000;'> &nbsp<b> CERTIFICADO DIGITAL VENCIDO </b>&nbsp <BR></span>
\t\t\t           \t\t<span style='font-size:8px;'><BR></span>
\t\t\t           \t\t<span style='font-size:13px;'><b>Seu certificado digital venceu há <span style='color:#FF0000;'>{$diasVencimentoCertificado} dias! </span></b><BR><BR></span>\t\t\t\t\t
\t\t\t\t\t\t\t<span style='font-size:13px;'><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span>\t\t\t\t\t
\t\t\t\t\t\t\t<script type="text/javascript">
\t\t\t\t\t            var diasVencimentoCertificado = {$diasVencimentoCertificado};
\t\t\t\t\t            dialogConfirm("<span style='color:white;font-size:18px;'<b>CERTIFICADO DIGITAL VENCIDO </b></span>", 
\t\t\t\t\t            " <span style='font-size:16px;color:#FF0000'><b>SEU CERTIFICADO DIGITAL VENCEU HÁ <span style='color:#FF0000;'>{$diasVencimentoCertificado} dias! </span></b><BR><BR></span><span style='font-size:14px;'><b>Data de vencimento: <span style='color:#FF0000;'>{$VencimentoCertificado}</span></b></span></span> ");\t\t\t            \t
\t\t\t\t            </script>
EOT;
                }
            }
        }
        if ($mostraValidadeCertificado) {
            $alertaCertificado = <<<EOT
\t\t\t\t\t<div style="border: 1px groove #FF0000; width: 520px; height: 130px; background-color: white; margin: 12px;" >
\t\t\t\t\t\t<img src="/hardness3/static/img/certificadoDigital.jpg" style="width: 95px; height: 95px; float: left; margin: 19px;">
\t\t\t\t\t\t<div style="float: left; margin: 20px 0 14px 33px; font-size: 15px; font-weight: bold;">
\t\t\t\t\t\t\t{$cert}
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
EOT;
        }
    }
}
$g['smarty']->assign('certificado', $alertaCertificado);
$C007 = mysql_query("SELECT C007_Data_Alteracao_Senha FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
$mC007 = mysql_fetch_assoc($C007);
if ($g['usuarioAtual'] != 63 and !empty($mC007['C007_Data_Alteracao_Senha']) and time() > strtotime($mC007['C007_Data_Alteracao_Senha'] . ' +60 days')) {
    $alertaSenhaDesatualizada = <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\tdialogConfirm("<span style='color:red;font-size:18px;'<b>ATENÇÃO!</b></span>", 
\t\t" <span style='font-size:16px'><b>Por medidas de segurança altere sua senha.</b><BR><BR></span><span style='font-size:14px;'><b>Última alteração de senha foi há mais de 60 dias.</span></b></span></span><BR><BR><span style='font-size:14px;'><b>Para alterar sua senha acesse:<br><u>Configuração</u> > <u>Usuário</u></span></b>");
\t} </script></div>
EOT;
    $g['smarty']->assign('senhaDesatualizada', $alertaSenhaDesatualizada);
}
$arquivo = $g['pathDadosUsuario'] . 'tamanhosJanelas.json';
if (file_exists($arquivo)) {
    $tamanhosJanelas = file_get_contents($arquivo);
    if (empty($tamanhosJanelas)) {
        $tamanhosJanelas = '{}';
    }
} else {
    $tamanhosJanelas = '{}';
}
$g['smarty']->assign('tamanhosJanelas', $tamanhosJanelas);
$g['smarty']->assign('logoMenu', $sys['logoMenu']);
$g['smarty']->assign('permissaoSuporte', $g['c029Ids'][229]);
// =====================================================================
// 18. Renderização do template principal
// =====================================================================
if (isset($g['clienteAtual'])) {
    $g['smarty']->display('indexB2B.tpl');
} elseif ($compacto) {
    $g['smarty']->display('indexERP.tpl');
} elseif ($_SESSION['device'] == 'desktop') {
    $g['smarty']->display('index.tpl');
} else {
    $g['smarty']->display('index-mobile.tpl');
}
if (isset($r_p) && !is_numeric($r_p)) {
    $p = explode('|', base64_decode($r_p));
    $g['empresaAtual'] = $Empresa_Atual = $p[0];
}
if (!isset($r_noEmp) && empty($g['empresaAtual'])) {
    echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\twindow.location = '/login';
\t} </script></div>
EOT;
}
echo $printAtEnd;
if (isset($r_oneWindow)) {
    $vars = '';
    foreach ($_REQUEST as $key => $value) {
        if (!($key == 'r')) {
            $vars .= "&{$key}=" . urlencode($value);
        }
    }
    $g['r_'] = '/' . trim($g['r_'], '/') . '/';
    echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\$(document).ready( function(data) {
\t\t\t//abrirJanela(data, 'oneWindow', '', 'oneWindow', 'oneWindow', '', '{$g['r_']}', '&acaoId={$r_acaoId}{$r_T147_Id}{$r_p}{$r_somenteGrid}', 'full');
\t\t\tabrirJanela(data, 'oneWindow', '', 'oneWindow', 'oneWindow', '', '{$g['r_']}', '{$vars}', 'full');
\t\t});
\t} </script></div>
EOT;
}
if (isset($r_T003_Flag_Expedicao)) {
    echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\$(document).ready(function(event) {
\t\t\tabrirJanela(event, 'est002Fila', '', menuId('est002Fila'), 'Enviar NFe para Sefaz', '.: Enviar NFe para Sefaz :.', '/ven/ven012/content/ven012GridFilaDeEnvioNovo/', '&T003_Flag_Expedicao={$r_T003_Flag_Expedicao}', 'full', false, 'topleft', false);
\t\t});
\t} </script></div>
EOT;
}
if ($g['clienteAtualContato'] > 0 && $g['r'][0] == 'erp') {
    if ($g['versaoB2B'] == '1') {
        echo "<script>location.replace('http://{$_SERVER['HTTP_HOST']}/b2b/b2b001/grid/gridB2BPrincipal/?ajax=true');</script>";
    } else {
        echo "<script>location.replace('http://{$_SERVER['HTTP_HOST']}/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true');</script>";
    }
}
// =====================================================================
// Funções auxiliares
// =====================================================================

// Monta as tags <script>/<link>/<style> a partir de $g['conteudo']
function processaCssJs() {
    global $g;
    $saida = '';
    foreach ($g['conteudo']['jsArquivo'] as $arquivo) {
        $saida .= "<script src=\"{$arquivo}\"></script>\n";
    }
    foreach ($g['conteudo']['cssArquivo'] as $arquivo) {
        $saida .= "<link rel=stylesheet href=\"{$arquivo}\" type=\"text/css\">\n";
    }
    $saida .= "<style type=\"text/css\">{$g['conteudo']['cssTexto']}</style>\n";
    $saida .= "<script type=\"text/javascript\">{$g['conteudo']['jsTexto']}</script>\n";
    return $saida;
}
// Retorna true quando a sessão expirou por inatividade (padrão 2400 s)
function verificaUsuarioAtivo() {
    $t = time();
    $t0 = $_SESSION['Data_Hora_Login'];
    $diff = $t - $t0;
    $tempoDeslogarInatividade = 2400;
    if (isset($_SESSION['tempoDeslogarInatividade']) && $_SESSION['tempoDeslogarInatividade'] > 0) {
        $tempoDeslogarInatividade = $_SESSION['tempoDeslogarInatividade'];
    }
    if ($diff > $tempoDeslogarInatividade || !isset($t0)) {
        return true;
    }
    $_SESSION['Data_Hora_Login'] = time();
}
// Retorna false quando o usuário está fora do horário de acesso permitido
function verificaHorarioAcesso() {
    $horaInicio = $_SESSION['Acesso_Hora_Inicio'];
    $horaFim = $_SESSION['Acesso_Hora_Fim'];
    $horaAtual = date('H:i:s');
    if (!(isset($horaInicio) && isset($horaFim) && $horaInicio != '00:00:00' && $horaFim != '00:00:00')) {
        return true;
    }
    if (!($horaAtual >= $horaInicio && $horaAtual <= $horaFim)) {
        return false;
    }
    $validacaoHoraFim = \DateTime::createFromFormat('H:i:s', $horaFim);
    $validacaoHoraAtual = \DateTime::createFromFormat('H:i:s', $horaAtual);
    $interval = $validacaoHoraFim->diff($validacaoHoraAtual);
    $minutosRestantes = $interval->h * 60 + $interval->i + 1;
    if ($minutosRestantes <= 20 && $minutosRestantes > 10 && !isset($_SESSION['Primeiro_Alerta_Horario_Acesso'])) {
        // Primeiro aviso: faltam entre 11 e 20 minutos
        echo gGeraAlertMsg('Vencimento Acesso', "<font style='font-size:15px;'><span style='color:red;'><b>O sistema será deslogado em {$minutosRestantes} minutos. Acesso liberado até {$horaFim}</b></span></font>");
        $_SESSION['Primeiro_Alerta_Horario_Acesso'] = $minutosRestantes;
    } elseif ($minutosRestantes <= 10 && !isset($_SESSION['Segundo_Alerta_Horario_Acesso'])) {
        // Segundo aviso: faltam 10 minutos ou menos
        echo gGeraAlertMsg('Vencimento Acesso', "<font style='font-size:15px;'><span style='color:red;'><b>O sistema será deslogado em {$minutosRestantes} minutos. Acesso liberado até {$horaFim}</b></span></font>");
        $_SESSION['Segundo_Alerta_Horario_Acesso'] = $minutosRestantes;
    }
    $_SESSION['Acesso_Hora_Inicio'] = $horaInicio;
    $_SESSION['Acesso_Hora_Fim'] = $horaFim;
    return true;
}
// Bloqueia o sistema se houver boleto em atraso (escolhe FW novo ou atual)
function verificaBoleto($Cnpj_Empresa_Usuaria, $Local, $Usuario_Atual, $Gestao) {
    global $sys;
    if (isset($sys["dbHostFwNovo"])) {
        verificaBoletoFwNovo($Cnpj_Empresa_Usuaria, $Local, $Usuario_Atual, $Gestao);
    } else {
        verificaBoletoAtual($Cnpj_Empresa_Usuaria, $Local, $Usuario_Atual, $Gestao);
    }
}
function verificaBoletoFwNovo($Cnpj_Empresa_Usuaria, $Local, $Usuario_Atual, $Gestao) {
    global $g, $confUsuario, $sys;
    $html = "";
    if (strlen($Cnpj_Empresa_Usuaria) == 14) {
        $Cnpj_Empresa_Usuaria = md5($Cnpj_Empresa_Usuaria);
    }
    $servidor = strpos($sys['urlEmpresa'], 'http') === false ? 'http://' . $sys['urlEmpresa'] : $sys['urlEmpresa'];
    $con = @mysqli_connect($sys['dbHostFwNovo'], $sys['dbUserFwNovo'], $sys['dbPassFwNovo'], $sys['dbDatabaseFwNovo']);
    $g['conexaoBanco'] = $con;
    $qPES001 = "SELECT PES001_Id\n \t\t\t\t  FROM PES001\n\t\t\t\t WHERE MD5(PES001_CNPJ) = '{$Cnpj_Empresa_Usuaria}'";
    $PES001 = mysql_query($qPES001);
    if ($erro = mysql_error()) {
        $html .= "Erro select boletos(1):" . $erro;
        return $html;
    }
    $mPES001 = mysql_fetch_assoc($PES001);
    if (!empty($Cnpj_Empresa_Usuaria)) {
        $qBOL001 = "SELECT DATEDIFF(CURRENT_DATE(),BOL001_Data_Vencimento) AS Dias_Vencido,\n\t\t\t\t\t\t   BOL001_Id\n\t\t\t\t\t  FROM BOL001\n\t\t\t\t LEFT JOIN BOL006 ON BOL001_BOL006_Id = BOL006_Id\n\t\t\t\t LEFT JOIN PES001 ON BOL006_PES001_Id = PES001_Id\n\t\t\t\t LEFT JOIN BOL005 ON BOL001_BOL005_Id = BOL005_Id\n\t\t\t\t LEFT JOIN CBC002 ON BOL005_CBC002_Id = CBC002_Id\n\t\t\t\t LEFT JOIN CBC001 ON CBC002_CBC001_Id = CBC001_Id\n\t\t\t\t\t WHERE MD5(PES001_CNPJ) = '{$Cnpj_Empresa_Usuaria}'\n\t\t\t\t\t   AND IFNULL(BOL001_Flag->>'\$.Cancelado', 'N') = 'N'\n\t\t\t\t\t   AND BOL001_Data_Pagamento = '0000-00-00'\n\t\t\t\t  ORDER BY BOL001_Data_Vencimento";
        $BOL001 = mysql_query($qBOL001);
        if ($erro = mysql_error()) {
            $html .= "Erro select boletos(2):" . $erro;
            error_log($html);
            return;
        }
        if (mysql_num_rows($BOL001) > 0) {
            while ($mBOL001 = mysql_fetch_array($BOL001)) {
                $dias = 10;
                if (isset($Gestao)) {
                    $dias = 15;
                }
                if ($mBOL001['Dias_Vencido'] >= $dias) {
                    $a = md5("PES001_Id-{$mPES001["PES001_Id"]}");
                    $urlSuspensao = $servidor . "/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&a={$a}";
                    echo <<<HTML
\t<script>
\t\ttop.location.replace("{$urlSuspensao}");
\t</script>
HTML;
                    die;
                }
            }
        }
    }
    $con = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
    $g['conexaoBanco'] = $con;
}
function verificaBoletoAtual($Cnpj_Empresa_Usuaria, $Local, $Usuario_Atual, $Gestao) {
    global $g, $confUsuario, $sys;
    $html = "";
    if (strlen($Cnpj_Empresa_Usuaria) == 14) {
        $Cnpj_Empresa_Usuaria = md5($Cnpj_Empresa_Usuaria);
    }
    $servidor = strpos($sys['urlEmpresa'], 'http') === false ? 'http://' . $sys['urlEmpresa'] : $sys['urlEmpresa'];
    $con = @mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'], $sys['dbDatabase']);
    $g['conexaoBanco'] = $con;
    $D024 = mysql_fetch_assoc(mysql_query("SELECT D024_Id, D024_Cnpj FROM D024 WHERE md5(D024_Cnpj) = '{$Cnpj_Empresa_Usuaria}'"));
    $D024_Id = $D024['D024_Id'];
    if (!empty($Cnpj_Empresa_Usuaria)) {
        $cClausula = "SELECT DATEDIFF(T117_Data_Vencimento,CURRENT_DATE()) AS Dias_Vencer, \n\t\t\t\t\t\t\t DATEDIFF(CURRENT_DATE(),T117_Data_Vencimento) AS Dias_Vencido\n\t\t\t\t\t\tFROM T117\n\t\t\t\t   LEFT JOIN D024 ON D024_Id=T117_D024_Id\n\t\t\t\t   LEFT JOIN T002 ON T002_T117_Id=T117_Id\n\t\t\t\t   LEFT JOIN D007 ON D007_Id=T117_D007_Id\n\t\t\t\t       WHERE md5(D024_Cnpj)='{$Cnpj_Empresa_Usuaria}'\n\t\t\t\t\t     AND T117_Flag_Cancelado='N'\n\t\t\t\t\t\t AND T002_Data_Recebimento='0000-00-00'\n\t\t\t\t\t\t AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)\n\t\t\t\t    ORDER BY T117_Data_Vencimento";
        $cSql_T117 = mysql_query($cClausula);
        if ($erro = mysql_error()) {
            $html .= "Erro select boletos(1):" . $erro;
            error_log($html);
            return;
        }
        if (mysql_num_rows($cSql_T117) > 0) {
            while ($mT117 = mysql_fetch_array($cSql_T117)) {
                $dias = 10;
                if (isset($Gestao)) {
                    $dias = 15;
                }
                if ($mT117['Dias_Vencido'] >= $dias) {
                    $a = md5("D024_Id-{$D024_Id}");
                    $urlSuspensao = $servidor . "/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&a={$a}";
                    echo <<<HTML
\t<script>
\t\ttop.location.replace("{$urlSuspensao}");
\t</script>
HTML;
                    die;
                }
            }
        }
    }
    $con = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
    $g['conexaoBanco'] = $con;
}
function verificaBoletoParceiro() {
    global $g, $confUsuario, $sys;
    if (empty($sys['dbCNPJ'])) {
        return '';
    }
    $Cnpj_Empresa_Usuaria = md5($sys['dbCNPJ']);
    $servidor = strpos($sys['urlEmpresa'], 'http') === false ? 'http://' . $sys['urlEmpresa'] : $sys['urlEmpresa'];
    $con = @mysqli_connect($sys['dbHostH'], $sys['dbUserH'], $sys['dbPassH'], $sys['dbDatabaseH']);
    $g['conexaoBanco'] = $con;
    $html = "";
    if (!empty($Cnpj_Empresa_Usuaria)) {
        $qBOL001 = "SELECT DATEDIFF(BOL001_Data_Vencimento,CURRENT_DATE()) AS Dias_Vencer,\n\t\t\t\t\t\t   DATEDIFF(CURRENT_DATE(),BOL001_Data_Vencimento) AS Dias_Vencido\n\t\t\t\t\t  FROM BOL001\n\t\t\t\t LEFT JOIN BOL006 ON BOL001_BOL006_Id = BOL006_Id\n\t\t\t\t LEFT JOIN PES001 ON BOL006_PES001_Id = PES001_Id\n\t\t\t\t LEFT JOIN BOL005 ON BOL001_BOL005_Id = BOL005_Id\n\t\t\t\t LEFT JOIN CBC002 ON BOL005_CBC002_Id = CBC002_Id\n\t\t\t\t LEFT JOIN CBC001 ON CBC002_CBC001_Id = CBC001_Id\n\t\t\t\t\t WHERE MD5(PES001_CNPJ) = '{$Cnpj_Empresa_Usuaria}'\n\t\t\t\t\t   AND IFNULL(BOL001_Flag->>'\$.Cancelado', 'N') = 'N'\n\t\t\t\t\t   AND BOL001_Data_Pagamento = '0000-00-00'\n\t\t\t\t  ORDER BY BOL001_Data_Vencimento";
        $BOL001 = mysql_query($qBOL001);
        if ($erro = mysql_error()) {
            $html .= "Erro select boletos(2):" . $erro;
            error_log($html);
            return;
        }
        if (mysql_num_rows($BOL001) > 0) {
            while ($mBOL001 = mysql_fetch_array($BOL001)) {
                $dias = 30;
                if ($mBOL001['Dias_Vencido'] >= $dias) {
                    $urlSuspensao = $servidor . "/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true";
                    echo <<<HTML
\t<script>
\t\ttop.location.replace("{$urlSuspensao}");
\t</script>
HTML;
                    die;
                }
            }
        }
    }
    $con = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
    $g['conexaoBanco'] = $con;
}
