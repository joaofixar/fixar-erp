<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'closeTab':      
		$tabId = isset($r_tabId) ? $r_tabId : false;
		$sql = mysql_query("SELECT T240_Id FROM T240 WHERE T240_Status != 'F' AND T240_C007_Id = '{$g['usuarioAtual']}' LIMIT $tabId, 1");
		$res = mysql_fetch_array($sql);
		$T240_Id = $res[0];
		if(mysql_num_rows($sql) > 0){
			$sql = "UPDATE T240 SET T240_Status = 'F' WHERE T240_Id = {$T240_Id};";
			mysql_query($sql);
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}  
	break;
	
	case 'newTab':     
		$resposta['data'] = <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'SRM', '/srm/srm001/content/srm001ContentFornecedor/', '&acaoId=' + encodeURIComponent('{$g['divId']}'), [450,58]);				
			}</script></div>
EOT;
		$resposta['code'] = true;
	break;

	case 'orderTabs':      
		$tabs = json_decode($r_order);
		$campoEmpresa = $g['conf']->ini('SRMNomeTab');
		mysql_query("UPDATE T240 SET T240_Ordem = 0 WHERE T240_Status != 'F' AND T240_C007_Id = '{$g['usuarioAtual']}'");
		foreach ($tabs as $ordem => $empresa) {
			mysql_query("UPDATE T240 LEFT JOIN D024 ON D024_Id = T240_D024_Id SET T240_Ordem = {$ordem} WHERE {$campoEmpresa} LIKE '{$empresa}%' AND T240_C007_Id = '{$g['usuarioAtual']}' AND T240_Status != 'F'");
		}
		$resposta['code'] = true;
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>