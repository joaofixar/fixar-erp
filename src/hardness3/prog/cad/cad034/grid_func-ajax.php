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
require_once("bibliotecas/classes/CAD034.php");
$CAD034 = new CAD034();
require_once("bibliotecas/classes/FIN009.php");
$FIN009 = new FIN009();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'cad034RecalcularProjecao':
		global $g;
		//$Ano = isset($r_Ano) ? $r_Ano : false;
        $D014_Id = isset($r_D014_Id) ? $r_D014_Id : false;
        
    	$retorno = $FIN009->fin009RecalculaProjecao($D014_Id, null, null, null);

        if ($retorno !== TRUE)
        {
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }else{
	        $resposta['data']="";
	        $resposta['code'] = true;	
	    }			
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
	
	case 'CheckboxFlagAtivo':
		global $g;
		$selected = isset($r_selected) ? $r_selected : false;
		$D014_Id = isset($r_D014_Id) ? $r_D014_Id : false;
		$ret = mysql_query("UPDATE D014 SET D014_Flag_Ativo='{$selected}' WHERE D014_Id='{$D014_Id}'");
		if(!$ret){
			$resposta['code'] = false;
			$resposta['data'] = mysql_error();
		}else{
			$resposta['code'] = true;
		}
	break;
	case 'CheckboxFlagAtivoGrupo':
		global $g;
		$selected = isset($r_selected) ? $r_selected : false;
		$D032_Id = isset($r_D032_Id) ? $r_D032_Id : false;
		$ret = mysql_query("UPDATE D032 SET D032_Flag_Ativo='{$selected}' WHERE D032_Id='{$D032_Id}'");
		if(!$ret){
			$resposta['code'] = false;
			$resposta['data'] = mysql_error();
		}else{
			$resposta['code'] = true;
		}
	break;

}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>