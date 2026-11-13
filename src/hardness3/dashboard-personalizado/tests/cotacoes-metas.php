<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/cotacoes-metas.php';
$dir=sys_get_temp_dir().'/cotacoes-metas-'.uniqid();
function dashboardPersonalizadoArquivoAcessos($empresa) { global $dir; return $dir.'/acessos-'.$empresa.'.json'; }
function checkMeta($ok,$message) { if (!$ok) throw new \RuntimeException($message); }
$context=array('mes'=>'2026-10','empresa'=>'all','equipe'=>'all','vendedor'=>'all','representante'=>'all','unidade'=>'reais');
$key=dashboardCotacoesMetaChave($context,$error);
checkMeta($key==='2026-10|all|all|all|all|reais','Contexto canônico de mês/escopo/unidade');
foreach (array(array('mes'=>'2026-13'),array('empresa'=>'../../1'),array('equipe'=>'a|b'),array('unidade'=>'kg')) as $bad) checkMeta(dashboardCotacoesMetaChave(array_merge($context,$bad),$error)===false,'Validar contexto');
$a=dashboardCotacoesMetaArquivo(1,7,'indicador'); $b=dashboardCotacoesMetaArquivo(1,8,'indicador'); $other=dashboardCotacoesMetaArquivo(2,7,'indicador');
checkMeta($a!==$b&&$a!==$other&&$a!==dashboardCotacoesMetaArquivo(1,7,'outro'),'Isolar usuário, empresa e indicador');
try {
    checkMeta(dashboardCotacoesMetaLer($a,$error)===array(),'Meta não definida em arquivo inexistente');
    checkMeta(dashboardCotacoesMetaSalvar($a,$key,5000000,$error)['valor']===5000000.0,'Salvar valor numérico');
    checkMeta(dashboardCotacoesMetaLer($b,$error)===array(),'Outro usuário não recebe meta');
    $next=dashboardCotacoesMetaChave(array_merge($context,array('mes'=>'2026-11')),$error);
    $team=dashboardCotacoesMetaChave(array_merge($context,array('equipe'=>'gian')),$error);
    $tons=dashboardCotacoesMetaChave(array_merge($context,array('unidade'=>'toneladas')),$error);
    dashboardCotacoesMetaSalvar($a,$next,6000000,$error); dashboardCotacoesMetaSalvar($a,$team,1200000,$error); dashboardCotacoesMetaSalvar($a,$tons,12.345,$error);
    checkMeta(count(dashboardCotacoesMetaLer($a,$error))===4,'Meses, equipes e unidades separados');
    foreach (array(-1,0,'500',INF,NAN,0.001) as $bad) checkMeta(dashboardCotacoesMetaSalvar($a,$key,$bad,$error)===false,'Rejeitar valores inválidos e valor arredondado zero');
    dashboardCotacoesMetaSalvar($a,$key,null,$error);
    checkMeta(!isset(dashboardCotacoesMetaLer($a,$error)[$key])&&count(dashboardCotacoesMetaLer($a,$error))===3,'Remover somente a meta do contexto atual');
    file_put_contents($b,'inválido');checkMeta(dashboardCotacoesMetaSalvar($b,$key,10,$error)===false&&file_get_contents($b)==='inválido','Não sobrescrever arquivo inválido');
    checkMeta(dashboardCotacoesMetaOrigemValida('https://erp.example','erp.example:443'),'Porta padrão equivalente');
    checkMeta(!dashboardCotacoesMetaOrigemValida('https://outro.example','erp.example'),'Rejeitar origem externa');
    echo "OK: metas individuais, persistência, meses/escopos/unidades, validação, remoção e origem.\n";
} finally { if(is_dir($dir)) { foreach(glob($dir.'/*') as $file) unlink($file); rmdir($dir); } }
