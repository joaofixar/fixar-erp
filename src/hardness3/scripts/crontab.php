<?php
namespace hardness;
/*
Adicionar ao crontab (execução de um único banco):
* * * * * php crontab.php [pathRaiz] [dbHost] [dbUser] [dbPass] [grupoExecucao] (opcional) [dbAlvo] (opcional)
*/

/* Extrai os parâmetros passados para a execução do processo
========================================================================*/
if (count($argv) < 5 || count($argv) > 7) {
    echo "Parâmetros inválidos.\n";
    echo "php crontab.php [pathRaiz] [dbHost] [dbUser] [dbPass] [grupoExecucao] (opcional) [dbAlvo] (opcional)\n";
    die();
}
$pathRaiz = $argv[1];
$dbHost = $argv[2];
$dbUser = $argv[3];
$dbPass = $argv[4];
$grupoExecucao = @$argv[5] ?? ''; // Parâmetro opcional
$dbAlvo = @$argv[6] ?? ''; // Quando preenchido, executa somente este banco
$strGrupoExecucao = empty($grupoExecucao) ? "<VAZIO>" : $grupoExecucao;

/* Verifica se o processo do crontab no linux já não está rodando 
========================================================================*/
/* Esse algoritmo faz o seguinte:
1. Executa um ps aux (que retorna todos os processos que estão sendo executados no linux)
2. O grep -E com o regex faz com que somente os processos crontab.php do ps aux sejem retornados
3. O array_map filtra essas linhas (pois o ps aux retorna vários dados do processo) para puxar somente o grupo do crontab que está sendo
executado
4. Verifica se o grupo atual que o crontab está tentando executar ainda não está em processo de execução, isso
precisa ser feito porque a execução de um crontab pode ser que demore mais de um minuto, e se demorar mais de um
minuto outros processos do crontab podem iniciar gerando uma carga muito pesada no servidor. */
$regex = 'hardness3.*cronta[b]\\.php';
$linhas = array_filter(explode("\n", trim(shell_exec("ps aux | grep -E " . escapeshellarg($regex)))));
$resultados = array_map(function($linha) {
    if (!preg_match('/crontab\.php\s+(.*)$/', $linha, $matches)) {
        return false;
    }
    // Quebra os argumentos respeitando aspas
    $args = preg_split('/\s+(?=(?:[^\']*\'[^\']*\')*[^\']*$)(?=(?:[^"]*"[^"]*")*[^"]*$)/', trim($matches[1]));
    $args = array_map(function($arg) { return trim($arg, "\"'"); }, $args);
    // Pega o 5º argumento somente se não começar com ">"
    $grupo = isset($args[4]) ? $args[4] : '';
    if ($grupo !== '' && substr($grupo, 0, 1) === '>') {
        $grupo = '';
    }
    return $grupo;
}, $linhas);
$resultados = array_filter($resultados, function($grupo) { return $grupo !== false; });

$contagemProcessosGrupos = array_count_values($resultados);
// Verifica se o processo desse crontab já está rodando (colocamos > 2 pq um crontab roda dois processos no linux por algum motivo)
if (($contagemProcessosGrupos[$grupoExecucao] ?? 0) > 2) {
    die("Já existe um processo crontab.php para o grupo {$strGrupoExecucao} em execução ({$contagemProcessosGrupos[$grupoExecucao]}), abortando.\n");
}
echo "INICIO CRONTAB GRUPO: {$strGrupoExecucao}" . (empty($dbAlvo) ? '' : " DB: {$dbAlvo}") . "\n";

/* Executa o resto do processo do crontab
========================================================================*/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
set_time_limit(0);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
ini_set('default_charset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
$g = array();

// Iniciando
chdir($pathRaiz);
$confUsuario['pathRaiz'] = $pathRaiz;
function carregarBibliotecaFramework($caminho) {
    $caminhoFW = "framework/{$caminho}";
    if (file_exists($caminhoFW)) {
        require_once($caminhoFW);
    } else {
                require_once($caminho);
    }
}
carregarBibliotecaFramework('sistema/confSistema.php');
carregarBibliotecaFramework('bibliotecas/funcoesGerais.php');
carregarBibliotecaFramework('sistema/apps.php');
require_once('bibliotecas/restricoes.php');
carregarBibliotecaFramework('sistema/personalizacoes.php');
$g['conf'] = new confSistema();


mysqli_set_charset($con,'utf8');
mysqli_query('SET NAMES utf8;');

// Loop DBs
//$dbs = mysql_query("SHOW DATABASES LIKE 'e%';");
//while ($db = mysql_fetch_array($dbs)) {

$processado = '';
$empresas = scandir('/var/www/sites');    

foreach($empresas as $index => $empresa){
    //echo "Entrou no loop /var/www/sites/$empresa \n";
    if (strpos($empresa, '-confUsuario.php') !== false) {
        require('/var/www/sites/'.$empresa);
        // O disparador executa este arquivo em um processo PHP exclusivo por banco.
        // Neste modo não podemos processar os demais bancos, pois classes personalizadas
        // (por exemplo CAD002) permanecem declaradas até o processo encerrar.
        if (!empty($dbAlvo) && $confUsuario['dbDatabase'] !== $dbAlvo) {
            continue;
        }
        if (strpos($processado, $confUsuario['dbDatabase']) === false) {
            //echo "Entrou no loop 3 \n";
            if (strpos($confUsuario['pathRaiz'], '/www/') !== false){
                // Sites no FW novo
                if (strpos($confUsuario['dbDatabase'], '-') !== false) { continue; }
                //
                $processado .= ',' . $confUsuario['dbDatabase'];
                $g['pathRaiz'] = $confUsuario['pathRaiz'];
                $g['urlRaiz'] = $confUsuario['urlRaiz'];
 
                // Conexão
                $con = mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass']);

                $erro= mysqli_connect_error($con);
                if (!empty($erro)) {
                    echo "Erro ao conectar no DB {$confUsuario['dbDatabase']} " . $erro . " \n";
                    continue;
                } else {
                    echo "Processando DB {$confUsuario['dbDatabase']}...\n";
                }

                $g['conexaoBanco'] = $con;
                mysql_query("USE {$confUsuario['dbDatabase']}");
                $g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
                $g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
                // Loop tarefas
                // Modo desenvolvedor é utilizado a tabela D183, no modo normal a C039 - na execução é feito o merge das duas, priorizando a C039
                $sql = <<<EOT
                    SELECT D183_Id, '' as C039_Id FROM D183
                    LEFT JOIN C039 ON C039_Uuid = D183_Uuid
                    WHERE
                        C039_Id IS NULL
                        AND (D183_Desenvolvimento IS NULL OR D183_Desenvolvimento = 0)
                        AND D183_Grupo_Execucao = '{$grupoExecucao}'
                    GROUP BY D183_Uuid
                    UNION
                    SELECT '' as D183_Id, C039_Id FROM C039
                    LEFT JOIN D183 ON C039_Uuid = D183_Uuid
                    WHERE
                        -- Não listar crontabs personalizados de apps que foram desinstalados 
                        (C039_Uuid IS NULL
                        OR (C039_Uuid IS NOT NULL AND D183_Uuid IS NOT NULL))
                        AND C039_Grupo_Execucao = '{$grupoExecucao}'
                EOT;
                $crontabs = mysql_query($sql);
                $erro = mysql_error();
                if (!empty($erro)) {
                    echo "Erro ao consultar processos {$strGrupoExecucao}" . $erro . " \n";
                }
                if (mysql_num_rows($crontabs)<=0){
                    echo "Não existem processos para executar no grupo {$strGrupoExecucao} \n";
                }
                while ($crontab = mysql_fetch_assoc($crontabs)) {
                    $tabela = !empty($crontab['D183_Id']) ? 'D183' : 'C039';
                    echo "Iniciando processo {$confUsuario['dbDatabase']} - {$crontab[$tabela.'_Id']} - {$crontab[$tabela.'_Codigo']}...\n";
                    // Garante que outro processo não 'pegou' para execução ou atualizou dados
                    $crontab = mysql_fetch_assoc(mysql_query("SELECT *, 
                                                                     timediff(now(), {$tabela}_Ultima_Execucao) as tempo, 
                                                                     now() as agora 
                                                                FROM {$tabela} 
                                                               WHERE {$tabela}_Id = '{$crontab[$tabela.'_Id']}'"));
                    $erro = mysql_error();
                    if (!empty($erro)){
                        echo $erro;
                    }
                    if ($crontab[$tabela.'_Flag_Execucao'] == 'S') {
                        if ($crontab["{$tabela}_Crontab"] == "MINUTO"){
                            $tempoMaximo = $crontab["{$tabela}_Crontab_Extra"] * 5;
                            $dataLimite = date("Y-m-d H:i:s",strtotime("{$crontab["{$tabela}_Ultima_Execucao"]} {$tempoMaximo} minutes"));
                            if ($crontab["agora"]<$dataLimite) {
                                echo $crontab["{$tabela}_Nome"] . " -  Processo já está em execução - Agora: {$crontab["agora"]} - Hora limite: {$dataLimite} \n";
                                continue;
                            } 
                        }else if ($crontab["{$tabela}_Crontab"] == "DIA") {
                                // Verificar se o tempo é maior que 24 horas de execução, se sim finaliza, processo está travado
                            if ($crontab["tempo"] > '24:00:00') {
                                echo $crontab["{$tabela}_Nome"] . " - " . $crontab["tempo"] . " - Processo já está em execução a mais de 48:00:00, rodando processo novamente \n";
                                //mysql_query("UPDATE {$tabela} SET {$tabela}_Flag_Execucao = 'N', {$tabela}_Ultima_Execucao = NOW() WHERE {$tabela}_Id = '{$crontab[$tabela.'_Id']}'");
                            } else {
                                echo $crontab["{$tabela}_Nome"] . " - " . $crontab["tempo"] . " - Processo já está em execução a {$crontab['tempo']} \n";
                                continue;
                            }
                        }else if ($crontab["{$tabela}_Crontab"] == "HORA") {
                            $tempoMaximo = $crontab["{$tabela}_Crontab_Extra"] * 4;
                            $dataLimite  = date("Y-m-d H:i:s",strtotime("{$crontab["{$tabela}_Ultima_Execucao"]} {$tempoMaximo} hours"));
                            if ($crontab["agora"] < $dataLimite) {
                                echo $crontab["{$tabela}_Nome"] . " -  Processo já está em execução - Agora: {$crontab["agora"]} - Hora limite: {$dataLimite} \n";
                                continue;
                            } else {
                                echo $crontab["{$tabela}_Nome"] . " - " . $crontab["tempo"] . " - Processo já está em execução a mais de {$tempoMaximo} horas, rodando processo novamente \n";
                                //mysql_query("UPDATE {$tabela} SET {$tabela}_Flag_Execucao = 'N', {$tabela}_Ultima_Execucao = NOW() WHERE {$tabela}_Id = '{$crontab[$tabela.'_Id']}'"); 
                            }
                        
                        } else {
                            echo $crontab["{$tabela}_Nome"] . " - " . $crontab["tempo"] . " - Processo já está em execução \n";
                            continue;
                        }
                    }
                    // Verifica se está apto para executar
                    $ultimaExecucaoBanco = trim((string)$crontab[$tabela.'_Ultima_Execucao']);
                    $possuiUltimaExecucaoValida = !empty($ultimaExecucaoBanco) && $ultimaExecucaoBanco !== '0000-00-00 00:00:00' && $ultimaExecucaoBanco !== '0000-00-00';
                    $ultimaExecucao = $possuiUltimaExecucaoValida ? $ultimaExecucaoBanco : $crontab[$tabela.'_Criacao'];
                    $ultimaExecucao = new \DateTime($ultimaExecucao);
                    $agora = new \DateTime();
                    $diff = $ultimaExecucao->diff($agora);
                    if ($crontab[$tabela.'_Crontab'] == 'DIA') {
                        if (!preg_match('/^[0-9]{1,2}\:[0-9]{2}$/', $crontab[$tabela.'_Crontab_Extra'])) {
                            echo $crontab["{$tabela}_Nome"] . " - Processo em DIA com horario invalido em Crontab_Extra ({$crontab[$tabela.'_Crontab_Extra']})\n";
                            continue;
                        }
                        $horarioExecucaoHoje = new \DateTime(date("Y-m-d {$crontab[$tabela.'_Crontab_Extra']}:00"));
                        if ($possuiUltimaExecucaoValida) {
                            if ($diff->d == 0) {
                                echo $crontab["{$tabela}_Nome"] . " - Processo em DIA ja executado hoje ({$ultimaExecucao->format('Y-m-d H:i:s')}), aguardando proxima janela diaria\n";
                                continue;
                            }
                            if ($agora < $horarioExecucaoHoje) {
                                echo $crontab["{$tabela}_Nome"] . " - Processo em DIA aguardando horario configurado ({$horarioExecucaoHoje->format('Y-m-d H:i:s')})\n";
                                continue;
                            }
                        } else {
                            $dataCriacao = new \DateTime($crontab[$tabela.'_Criacao']);
                            $primeiraExecucao = new \DateTime($dataCriacao->format("Y-m-d {$crontab[$tabela.'_Crontab_Extra']}:00"));
                            if ($dataCriacao > $primeiraExecucao) {
                                $primeiraExecucao->modify('+1 day');
                            }
                            if ($agora < $primeiraExecucao) {
                                echo $crontab["{$tabela}_Nome"] . " - Processo em DIA aguardando primeira execucao em {$primeiraExecucao->format('Y-m-d H:i:s')}\n";
                                continue;
                            }
                        }
                    } else if ($crontab[$tabela.'_Crontab'] == 'HORA') {
                        if (!$crontab[$tabela.'_Crontab_Extra'] || $crontab[$tabela.'_Crontab_Extra'] < 1) {
                            echo $crontab["{$tabela}_Nome"] . " - Processo em HORA sem intervalo valido em Crontab_Extra\n";
                            continue;
                        }
                        if ($diff->h < $crontab[$tabela.'_Crontab_Extra']) {
                            echo $crontab["{$tabela}_Nome"] . " - Processo em HORA aguardando completar intervalo de {$crontab[$tabela.'_Crontab_Extra']} hora(s) desde {$ultimaExecucao->format('Y-m-d H:i:s')}\n";
                            continue;
                        }
                    } else if ($crontab[$tabela.'_Crontab'] == 'MINUTO') {
                        if (!$crontab[$tabela.'_Crontab_Extra'] || $crontab[$tabela.'_Crontab_Extra'] < 1) {
                            echo "Processo em MINUTO mas não foi definido o intervalo em minutos\n";
                            continue;
                        }
                        if ($diff->i < $crontab[$tabela.'_Crontab_Extra']) {
                            echo "Ainda não completou o intervalo de " . $crontab[$tabela.'_Crontab_Extra'] . " para próximo processo (" . $diff->i . ")\n";
                            continue;
                        }
                    }
                    // Executar
                    echo "Executando {$confUsuario['dbDatabase']} - {$crontab[$tabela.'_Id']} - {$crontab[$tabela.'_Codigo']}...\n";
                    mysql_query("UPDATE {$tabela} SET {$tabela}_Flag_Execucao = 'S' WHERE {$tabela}_Id = '{$crontab[$tabela.'_Id']}'");
                    $inicio             = microtime(true);
                    $retorno            = '';
                    $tempoTotalExecucao = '00:00:00';
                    $executouComSucesso = false;
                    $logCrontab         = Personalizacoes::getDirLogsCrontab() . $tabela . '-' . $crontab[$tabela.'_Id'] . '.log';

                    try {
                        $retorno = executarCodigo($crontab[$tabela.'_Codigo'], array(), $crontab[$tabela.'_Codigo_Sql']);
                        $retorno = is_array($retorno) ? print_r($retorno, true) : $retorno;
                        $executouComSucesso = true;
                    } catch (\Throwable $e) {
                        $retorno = "Erro ao executar processo: " . $e->getMessage() . "\n" . $e->getTraceAsString();
                        echo "Erro ao executar {$confUsuario['dbDatabase']} - {$crontab[$tabela.'_Id']} - {$crontab[$tabela.'_Codigo']}: {$e->getMessage()}\n";
                    } finally {
                        $fim               = microtime(true);
                        $tempoExecucao     = ceil($fim - $inicio);
                        $horas             = floor($tempoExecucao / 3600);
                        $minutos           = floor(($tempoExecucao / 60) % 60);
                        $segundos          = $tempoExecucao % 60;
                        $tempoTotalExecucao = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

                        if ($executouComSucesso) {
                            echo("\nTempo total execucao : {$tempoTotalExecucao}\n");

                            if($crontab["{$tabela}_Crontab"] == 'DIA'){
                                //se for dia mantem sempre o horario fixado
                                $ultimaExecucao = date("Y-m-d") ." ". $crontab["{$tabela}_Crontab_Extra"] ;
                            } else {
                                $ultimaExecucao = date("Y-m-d H:i:s");
                            }

                            mysql_query("UPDATE {$tabela} SET {$tabela}_Flag_Execucao = 'N', {$tabela}_Ultima_Execucao = '{$ultimaExecucao}', {$tabela}_Tempo_Execucao = '{$tempoTotalExecucao}' WHERE {$tabela}_Id = '{$crontab[$tabela.'_Id']}'");
                        } else {
                            mysql_query("UPDATE {$tabela} SET {$tabela}_Flag_Execucao = 'N' WHERE {$tabela}_Id = '{$crontab[$tabela.'_Id']}'");
                        }

                        file_put_contents($logCrontab, "Data [{$agora->format('Y-m-d H:i:s')}] DB [{$confUsuario['dbDatabase']}] {$tabela}_Id [{$crontab[$tabela.'_Id']}] Código [{$crontab[$tabela.'_Codigo']}]: " . $retorno . "\n", FILE_APPEND);
                    }
                }
            }
        }
    }
}

echo "FIM CRONTAB GRUPO: {$strGrupoExecucao}\n";
