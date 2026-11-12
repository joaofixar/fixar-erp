<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'alteraEstadoEmpresas':
		$C004_D018_Id = isset($r_C004_D018_Id) ? $r_C004_D018_Id : false;
		$resposta['code'] = true;
		$resposta['data']['C004_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$r_C004_D018_Id.' ORDER BY D020_Nome_Cidade'));
	break;

	case 'carregaSeguradoraApolices':
		$C004_D172_Id = isset($r_C004_D172_Id) ? $r_C004_D172_Id : false;	
		$resposta['code'] = true;
		log("D172_Id= ".$C004_D172_Id);	
		$resposta['data']['C004_D173_Id'] = gProcessaSelect(gGeraSelect('D173', 'D173_Id', 'D173_Identificacao', 'WHERE D173_D172_Id='.$C004_D172_Id.' AND D173_Flag_Ativo=\'S\' order by D173_Identificacao'));
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>