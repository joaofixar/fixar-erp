<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * VEN - VEN002 - Pedidos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once("bibliotecas/classes/FIN009.php");
$FIN009 = new FIN009();


$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'fin009RecalcularProjecao':
	    global $g;
        $etapa = $r_etapa;
        $valorTotal = $r_valorTotal;
        $etapas = $r_etapas;

        if($etapa == 1){
        	$Contas = mysql_fetch_array(mysql_query("SELECT COUNT(*)as total from D014"));
            $valorTotal = $Contas['total'];
            $etapas = round($valorTotal/10);
        }

        /** 
        * Funcionamento em etapas
        */
    	$registrosEtapa = floor($valorTotal / $etapas);

    	$limit1 = floor(( $registrosEtapa * $etapa ) - $registrosEtapa);
    	if($etapa >= $etapas){
    		$limit2 = ceil($registrosEtapa + ($valorTotal - ($limit1 + $registrosEtapa)));
    	} else {
    		$limit2 = $registrosEtapa ;
    	}
    	$concluido = 0;

        $resposta['percentual'] = ceil((100/$etapas) * $etapa);
        
    	if($limit2 < $valorTotal){
        	$etapa = $r_etapa + 1;	
        }else{
        	$concluido = 1;
        }
        $resposta['etapa'] = $etapa;
        $resposta['etapas'] = $etapas;
        $resposta['valorTotal'] = $valorTotal;
        $resposta['concluido'] = $concluido;
        // --

        $extraLimit = "LIMIT ". $limit1.",".$limit2;
        
    	$FIN009->fin009RecalculaProjecao(null, null, $extraLimit);

        $resposta['data']="";
        $resposta['code'] = true;
        $resposta['ok'] = 0;
        $resposta['erro'] = 0;
        $resposta['erros'] = "";
	break;
	
	case 'CheckboxFlagConfirmado':
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T018_Id = isset($_REQUEST['T018_Id']) ? $_REQUEST['T018_Id'] : false;
		$ret = mysql_query("UPDATE T018 SET T018_Flag_Confirmado='{$selected}' WHERE T018_Id='{$T018_Id}'");
		if(!$ret){
			$resposta['code'] = false;
			$resposta['data'] = mysql_error();
		}else{
			$resposta['code'] = true;
		}
	break;
	
	case 'fin009AtualizaFlagConfirmado':	
		global $g;
		$T018_Id = isset($r_T018_Id) ? $r_T018_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;
		if($novoValor ==  "0,00"){
			mysql_query("UPDATE T018 SET T018_Flag_Confirmado='N' WHERE T018_Id='{$T018_Id}'");
			$resposta['code'] = true;
		}else{
			mysql_query("UPDATE T018 SET T018_Flag_Confirmado='S' WHERE T018_Id='{$T018_Id}'");
			$resposta['code'] = true;
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>