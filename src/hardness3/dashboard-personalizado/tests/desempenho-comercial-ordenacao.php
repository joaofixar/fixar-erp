<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/desempenho-comercial.php';
function conferirOrdem($atual, $esperado, $mensagem) {
    if ($atual !== $esperado) throw new \RuntimeException($mensagem);
}
$fontes = array('orcamentos'=>array(), 'pedidos'=>array(), 'notas'=>array());
$cadastros = array();
foreach (array(1=>array(10,9000),2=>array(200,0),3=>array(50,10),4=>array(50,20),5=>array(0,0),6=>array(-1,0)) as $id=>$valores) {
    foreach (array('orcamentos','pedidos','notas') as $indice=>$tipo) $fontes[$tipo][] = array(
        'clienteId'=>'cad:'.$id,'cliente'=>'Cliente '.$id,'responsavelId'=>'7','responsavel'=>'Vendedor',
        'mes'=>'2026-10','valor'=>$indice===2 ? 10000-$id : $valores[$indice], 'quantidade'=>1,'convertidos'=>0);
    $grupo = in_array($id,array(2,3,4),true);
    $cadastros[] = array('clienteId'=>'cad:'.$id,'cliente'=>'Cliente '.$id,'vendedorId'=>'7','vendedor'=>'Vendedor',
        'representanteId'=>$grupo ? '32' : '31','representante'=>$grupo ? 'Z representante' : 'A representante',
        'uf'=>$grupo ? 'SP' : 'PR', 'cidadeId'=>$id===2 ? '2' : '1', 'cidade'=>$id===2 ? 'Z cidade' : 'A cidade');
}
$r = dashboardComercialConsolidar($fontes, array('inicio'=>'2026-10-01','fim'=>'2026-10-14'), '2026-10-14');
conferirOrdem(array_column($r['linhas'],'clienteId'),array('cad:2','cad:4','cad:3','cad:1','cad:5','cad:6'), 'Vendedores: maior orçamento, desempate pedidos, zeros e negativos');
$representantes = dashboardComercialAgruparCarteira(array_reverse($cadastros),$r);
conferirOrdem(array_column($representantes['linhas'],'representanteId'),array('32','31'), 'Representantes: ordenar totais antes de paginar');
conferirOrdem(array_column($representantes['linhas'][0]['clientes'],'clienteId'),array('cad:2','cad:4','cad:3'), 'Expansão: maiores clientes primeiro');
$regioes = dashboardComercialAgruparRegioes(array_reverse($cadastros),$r);
conferirOrdem(array_column($regioes['ufs'],'uf'),array('SP','PR'), 'Brasil: UFs ordenadas por valor');
conferirOrdem(array_column($regioes['ufs'][0]['cidades'],'cidadeId'),array('2','1'), 'UF: cidades ordenadas por valor');
conferirOrdem(array_column($regioes['ufs'][0]['cidades'][1]['clientes'],'clienteId'),array('cad:4','cad:3'), 'Cidade: clientes ordenados por valor');
conferirOrdem($regioes['totais'], array('orcamentos'=>309.0,'pedidos'=>9030.0), 'Ordenação preserva totais');
$empatados = array(
 array('cliente'=>'Z cliente','clienteId'=>'3','totais'=>array('orcamentos'=>0,'pedidos'=>0)),
 array('cliente'=>'A cliente','clienteId'=>'2','totais'=>array('orcamentos'=>0,'pedidos'=>0)),
 array('cliente'=>'A cliente','clienteId'=>'1','totais'=>array('orcamentos'=>0,'pedidos'=>0)));
dashboardComercialOrdenarValores($empatados,'cliente','clienteId');
conferirOrdem(array_column($empatados,'clienteId'),array('1','2','3'), 'Desempate por nome e ID');
echo "OK: ordenação decrescente em Vendedores, Representantes e Regiões, incluindo níveis expandidos e desempates.\n";
