<?php
namespace hardness;
// Somente ACLs artificiais, sem banco, sessão de cliente ou credenciais.
if (PHP_SAPI !== 'cli') exit;
require dirname(__DIR__).'/access.php';
function checkManager($condition,$message) { if(!$condition) throw new \RuntimeException($message); }
$config=array('acessoDiretorAutomatico'=>false,'indicadores'=>array(array('id'=>'vendedores'),array('id'=>'cotacoes'),array('id'=>'receber')));
$auth=array('usuario'=>77,'perfil'=>10,'root'=>false,'diretor'=>false);
$own=array('indicadores'=>array('vendedores','cotacoes'),'restricoes'=>array(),'gestor'=>true);
$old=array('perfisGestores'=>array(99),'perfis'=>array(3=>array('receber')),'usuarios'=>array(77=>$own,7=>array('indicadores'=>array('receber','vendedores'),'restricoes'=>array('receber'=>array('vendedorId'=>7,'vendedorNome'=>'Teste')),'gestor'=>false),8=>array('indicadores'=>array('receber'),'restricoes'=>array(),'gestor'=>true)));
checkManager(dashboardPersonalizadoPodeGerenciarAcessos($auth,$old),'Usuário gestor pode abrir administração');
checkManager(!dashboardPersonalizadoGerenciaTodosAcessos($auth,$old),'Usuário gestor não recebe administração global');
checkManager(array_column(dashboardPersonalizadoIndicadoresPermitidos($auth,$config,$old),'id')===array('vendedores','cotacoes'),'William continua vendo somente os dois indicadores');
checkManager(array_column(dashboardPersonalizadoIndicadoresGerenciaveis($auth,$config,$old),'id')===array('vendedores','cotacoes'),'Escopo de distribuição é limitado');
$visible=dashboardPersonalizadoAcessosVisiveis($old,dashboardPersonalizadoIndicadoresGerenciaveis($auth,$config,$old));
checkManager($visible['perfis'][3]===array() && $visible['usuarios'][7]['indicadores']===array('vendedores') && !$visible['usuarios'][7]['restricoes'],'Outros acessos não são expostos ao gestor');
$new=$visible;$new['usuarios'][7]=array('indicadores'=>array('cotacoes'),'restricoes'=>array('cotacoes'=>array('vendedorId'=>7,'vendedorNome'=>'Teste')),'gestor'=>true);
$new['usuarios'][77]=array('indicadores'=>array('vendedores'),'restricoes'=>array(),'gestor'=>false);
$new['perfis'][3]=array('cotacoes');$new['perfisGestores']=array(10);$error='';
$result=dashboardPersonalizadoMesclarAcessosGestor($auth,$config,$old,$new,$error);
checkManager($result!==false,$error);
checkManager($result['usuarios'][77]===$own,'Gestor não altera sua própria autoridade');
checkManager($result['usuarios'][7]['indicadores']===array('receber','cotacoes'),'Mantém indicador externo e troca permissões de seu escopo');
checkManager(isset($result['usuarios'][7]['restricoes']['receber'],$result['usuarios'][7]['restricoes']['cotacoes']),'Preserva restrição externa e grava nova restrição');
checkManager(!$result['usuarios'][7]['gestor'] && $result['usuarios'][8]['gestor'],'Não promove usuários nem remove gestor fora de sua autoridade');
checkManager($result['perfisGestores']===$old['perfisGestores'] && $result['perfis'][3]===array('receber','cotacoes'),'Preserva perfil responsável e acessos externos');
$new['usuarios'][7]=array('indicadores'=>array(),'restricoes'=>array());
$result=dashboardPersonalizadoMesclarAcessosGestor($auth,$config,$old,$new,$error);
checkManager($result['usuarios'][7]['indicadores']===array('receber'),'Revogar o último indicador do escopo não apaga os outros');
$new['usuarios'][7]['indicadores']=array('receber');
checkManager(dashboardPersonalizadoMesclarAcessosGestor($auth,$config,$old,$new,$error)===false,'Rejeita indicador externo enviado manualmente');
$new=$visible;$old['usuarios'][77]['restricoes']['vendedores']=array('vendedorId'=>77,'vendedorNome'=>'William');
checkManager(dashboardPersonalizadoRestricaoUsuarioIndicador($auth,$old,'vendedores')['vendedorId']===77,'A flag não ignora restrição de dados');
$new['usuarios'][7]=array('indicadores'=>array('vendedores'),'restricoes'=>array());
checkManager(dashboardPersonalizadoMesclarAcessosGestor($auth,$config,$old,$new,$error)===false,'Não delega visão geral quando só tem visão individual');
$new['usuarios'][7]['restricoes']['vendedores']=array('vendedorId'=>77,'vendedorNome'=>'William');
checkManager(dashboardPersonalizadoMesclarAcessosGestor($auth,$config,$old,$new,$error)!==false,'Pode delegar a mesma visão individual');
$full=$auth;$full['perfil']=99;
checkManager(count(dashboardPersonalizadoIndicadoresPermitidos($full,$config,$old))===3,'Perfil responsável mantém comportamento anterior');
$forged=$new;unset($forged['usuarios'][8]);$forged['usuarios'][7]['gestor']=true;
$fullResult=dashboardPersonalizadoMesclarAcessosGestor($full,$config,$old,$forged,$error);
checkManager(!$fullResult['usuarios'][7]['gestor'] && $fullResult['usuarios'][8]['gestor'],'Somente root muda a flag, inclusive por omissão de registros');
$normal=$auth;$normal['usuario']=7;
checkManager(!dashboardPersonalizadoPodeGerenciarAcessos($normal,$old),'Usuário comum não administra');
$root=$auth;$root['root']=true;
checkManager(dashboardPersonalizadoMesclarAcessosGestor($root,$config,$old,$new,$error)===$new,'Root mantém edição completa');
// Exercita a persistência/normalização da nova flag usando arquivo temporário.
$GLOBALS['aclTestDir']=sys_get_temp_dir().'/dashboard-acl-test-'.uniqid();mkdir($GLOBALS['aclTestDir'],0700);
function dashboardPersonalizadoConfiguracao($empresa,&$errors){return false;}
function dashboardPersonalizadoArquivo($empresa){return $GLOBALS['aclTestDir'].'/config.json';}
checkManager(dashboardPersonalizadoSalvarAcessos(1,$old),'Salvar fixture');
$r=dashboardPersonalizadoLerAcessos(1,array('vendedores'=>true,'cotacoes'=>true,'receber'=>true));
checkManager($r['usuarios'][77]['gestor']===true && $r['usuarios'][7]['gestor']===false,'Flag persiste e legado continua sem gestão');
unlink(dashboardPersonalizadoArquivoAcessos(1));rmdir($GLOBALS['aclTestDir']);
echo "OK: usuário gestor, dois indicadores, escopo de edição, revogação, preservação, restrições, root/perfil e persistência.\n";
