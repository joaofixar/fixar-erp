<?php
// Apenas fixtures locais; sem sessão, credenciais ou conexão com a base do cliente.
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
require_once dirname(__DIR__) . '/agenda-action.php';
require_once dirname(__DIR__) . '/cotacoes-documento.php';
function conferirDoc($condicao, $mensagem) { if (!$condicao) throw new \RuntimeException($mensagem); }
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
class DocumentoStatement {
    public $sql, $valores, $conexao;
    public function bind_param($tipos, &...$valores) { $this->valores=$valores; return true; }
}
function mysqli_prepare($db,$sql) { $s=new DocumentoStatement();$s->conexao=$db;$s->sql=$sql;return $s; }
function mysqli_stmt_execute($s) {
    conferirDoc(strpos($s->sql,'SELECT ')===0,'Integração autoriza somente por leitura');
    $s->conexao->consultas[]=array('sql'=>$s->sql,'valores'=>$s->valores);return true;
}
function mysqli_stmt_get_result($s) { return array_shift($s->conexao->respostas); }
function mysqli_fetch_all($r,$modo) { return $r; }
function mysqli_stmt_close($s) {}
function bancoDoc($respostas=array()) { return (object)array('consultas'=>array(),'respostas'=>$respostas); }
$config=json_decode(file_get_contents(__DIR__.'/fixtures/painel-cotacoes.json'),true);
$indicador=$config['indicadores'][0];
$indicador['_cotacoesRestricao']=array('vendedorId'=>7,'escopo'=>'equipe');
$auth=array('usuario'=>7,'empresa'=>1);$sessao=array('Usuario_Atual'=>7,'Empresa_Atual'=>1);
$entrada=array('documentoTipo'=>'orcamentos','documentoId'=>'123','documentoEmpresa'=>'1','f_dataInicio'=>'2026-10-01','f_dataFim'=>'2026-10-13');
$empresa=array('id'=>1,'cnpj'=>'','nome'=>'Matriz');$documento=array('id'=>123,'numero'=>900,'empresaId'=>1,'excluido'=>0);
$respostas=array(array($empresa),array(array('id'=>7)),array($documento));
foreach(array(array(),array('Usuario_Atual'=>8,'Empresa_Atual'=>1),array('Usuario_Atual'=>7,'Empresa_Atual'=>5)) as $invalida) {
    $db=bancoDoc();conferirDoc(dashboardCotacoesDocumentoContexto($db,$indicador,$auth,$invalida,$entrada,$erro)===false && !$db->consultas,'Sessão divergente não consulta documentos');
}
foreach(array('0','-1','123 OR 1=1',array(123),'99999999999999') as $id) {
    $db=bancoDoc();$invalida=array_merge($entrada,array('documentoId'=>$id));
    conferirDoc(dashboardCotacoesDocumentoContexto($db,$indicador,$auth,$sessao,$invalida,$erro)===false && !$db->consultas,'Rejeitar ID inválido');
}
foreach(array(array('documentoTipo'=>'projecao'),array('documentoTipo'=>array('notas')),array('documentoEmpresa'=>'5'),array('f_dataInicio'=>'2026-10-30')) as $campos) {
    $db=bancoDoc();conferirDoc(dashboardCotacoesDocumentoContexto($db,$indicador,$auth,$sessao,array_merge($entrada,$campos),$erro)===false && !$db->consultas,'Tipo, período ou empresa inválidos não abrem tela nativa');
}
$db=bancoDoc();conferirDoc(dashboardCotacoesDocumentoContexto($db,array(),$auth,$sessao,$entrada,$erro)===false && !$db->consultas,'Outros indicadores não habilitam esta ação');
foreach(array(array(array()),array(array($empresa),array()),array(array($empresa),array(array('id'=>7)),array()),array(array($empresa),array(array('id'=>7)),array(array_merge($documento,array('excluido'=>1)))),array(array($empresa),array(array('id'=>7)),array(array_merge($documento,array('empresaId'=>5))))) as $recusadas) {
    $db=bancoDoc($recusadas);conferirDoc(dashboardCotacoesDocumentoContexto($db,$indicador,$auth,$sessao,$entrada,$erro)===false,'Empresa, permissão, documento ausente/cancelado ou vínculo divergente não abrem o cadastro');
}
foreach(array('orcamentos'=>array('T003',14,'/ven/ven001/content/ven001ContentEditarOrcamento/'),'pedidos'=>array('T005',15,'/ven/ven002/content/ven002content02/'),'notas'=>array('T007',16,'/ven/ven003/content/ven003contentIncluirNotaFiscal/')) as $tipo=>$destino) {
    $db=bancoDoc($respostas);
    $forjada=array_merge($entrada,array('documentoTipo'=>$tipo,'usuario'=>63,'empresa'=>5,'rootSessao'=>1,'f_vendedor'=>999,'url'=>'https://example.invalid/','p'=>5));
    $r=dashboardCotacoesDocumentoContexto($db,$indicador,$auth,$sessao,$forjada,$erro);
    conferirDoc($r!==false,$erro);
    conferirDoc(count($db->consultas)===3,'Somente empresa, permissão e consulta do documento selecionado');
    conferirDoc($r['usuario']===7 && $r['empresaSessao']===1 && $r['documentoId']===123 && $r['numero']===900,'ID interno não é o número fiscal; usa contexto real');
    conferirDoc(parse_url($r['url'],PHP_URL_PATH)===$destino[2],'Rota nativa do tipo correto');
    parse_str(parse_url($r['url'],PHP_URL_QUERY),$q);
    conferirDoc($q===array('oneWindow'=>'true','acaoId'=>'123','tabela'=>$destino[0]),'URL sem troca de empresa/usuário ou destino do navegador');
    conferirDoc($db->consultas[1]['valores']===array(7,0,1,$destino[1]),'Permissão do módulo no perfil da empresa atual');
    $consulta=$db->consultas[2];
    conferirDoc(strpos($consulta['sql'],'d.'.$destino[0].'_Id=?')!==false && substr($consulta['sql'],-7)==='LIMIT 1','Busca por chave do documento, sem varrer o painel completo');
    conferirDoc(in_array(7,$consulta['valores'],true) && !in_array(999,$consulta['valores'],true),'Mantém restrição de vendedor/supervisor');
    conferirDoc(strpos($consulta['sql'],'v.C007_C007_Id =')!==false && strpos($consulta['sql'],'C007_C007_Id_2')===false,'Supervisor 1 aplicado na autorização');
}
echo "OK: abertura de orçamento/pedido/NF, sessão, empresa, permissões do ERP, escopo, ID interno e rotas fixas.\n";
