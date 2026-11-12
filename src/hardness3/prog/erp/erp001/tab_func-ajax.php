<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'closeTab':      
		$tabId = isset($r_tabId) ? $r_tabId : false;
		$sql = mysql_query("SELECT T194_Id FROM T194 WHERE T194_Status != 'F' AND T194_C007_Id = '{$g['usuarioAtual']}' LIMIT $tabId, 1");
		$res = mysql_fetch_array($sql);
		$T194_Id = $res[0];
		if(mysql_num_rows($sql) > 0){
			$sql = "UPDATE T194 SET T194_Status = 'F' WHERE T194_Id = {$T194_Id};";
			mysql_query($sql);
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}  
	break;
	
	case 'newTab':     
		$resposta['data'] = <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'erp', '/erp/erp001/content/erp001ContentCliente/', '&acaoId=' + encodeURIComponent('{$g['divId']}'), [450,58]);				
			}</script></div>
EOT;
			$resposta['code'] = true;
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>