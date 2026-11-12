<?php
namespace hardness;
	/**
	* VEN - VEN001 - Cadastro de Orçamentos
	*
	* Processamento de ações AJAX
	*/
	$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
		case 'processarNCM':
			/*
			$D005 = mysql_query("select * from D005 where D005_Flag_ST='S'");
			while ($mD005 = mysql_fetch_array($D005)){
				mysql_query("update D005 set D005_Flag_ST='{$mD005['D005_Flag_ST']}', D005_Valor_VA='{$mD005['D005_Valor_VA']}' where D005_Classificacao_Fiscal like '{$mD005['D005_Classificacao_Fiscal']}%' and ifnull(D005_Flag_ST,'N')!='S'");
			}
			*/
			$D149 = mysql_query("select D005_Classificacao_Fiscal,D149_Flag_ST,D149_Valor_VA from D149 left join D005 on D005_Id=D149_D005_Id where D149_Flag_ST='S'");
			while ($mD149 = mysql_fetch_array($D149)){
				mysql_query("update D149 left join D005 on D005_Id=D149_D005_Id set D149_Flag_ST='{$mD149['D149_Flag_ST']}', D149_Valor_VA='{$mD149['D149_Valor_VA']}' where D005_Classificacao_Fiscal like '{$mD149['D005_Classificacao_Fiscal']}%' and ifnull(D149_Flag_ST,'N')!='S'");
			}

			$resposta['code'] = true;
			$resposta['data'] = 'OK';
		break;
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>