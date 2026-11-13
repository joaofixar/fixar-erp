<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'alteraEstado':
		$D024_D018_Id = isset($r_D024_D018_Id) ? $r_D024_D018_Id : false;
		$resposta['code'] = true;
		if(!$D024_D018_Id){
			$resposta['data']['D024_D020_Id'] = "<option value=''>...</option>";
		} else {
			$resposta['data']['D024_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$r_D024_D018_Id.' ORDER BY D020_Nome_Cidade'));
		}
	break;	
	
	case 'alteraEstadoCobranca':
		$D024_D018_Cobranca = isset($r_D024_D018_Cobranca) ? $r_D024_D018_Cobranca : false;
		$resposta['code'] = true;
		$resposta['data']['D024_D020_Cobranca'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$r_D024_D018_Cobranca.' ORDER BY D020_Nome_Cidade'));
	break;	
	
	case 'alteraEstadoEntrega':
		$D024_D018_Entrega = isset($r_D024_D018_Entrega) ? $r_D024_D018_Entrega : false;
		$resposta['code'] = true;
		$resposta['data']['D024_D020_Entrega'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$r_D024_D018_Entrega.' ORDER BY D020_Nome_Cidade'));
	break;

	case 'juntarProcuraClientes':
		$sql = "SELECT * FROM D024 WHERE D024_Id = '{$r_D024_Id}'";
		$res = mysql_query($sql);
		$resposta['code'] = true;
		$resposta['data'] = mysql_fetch_assoc($res);
	break;

	case 'alteraSetor':
		$D001_C008_Id = isset($r_D001_C008_Id) ? $r_D001_C008_Id : false;
		$resposta['code'] = true;
		$resposta['data']['D001_D060_Id'] = gProcessaSelect(gGeraSelect('D060', 'D060_Id', 'D060_Id, D060_Descricao_Grupo', 'WHERE D060_C008_Id = '.$r_D001_C008_Id.' ORDER BY D060_Id, D060_Descricao_Grupo', true));
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>