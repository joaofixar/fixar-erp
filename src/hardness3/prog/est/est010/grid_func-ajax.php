<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/

// Classes
require_once('bibliotecas/classes/EST010.php');
$EST010 = new EST010();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'est010CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$EST010->updateCheckboxGerarCotar($T006_Id, $selected);
	break;
	
	case 'est010CheckboxComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$EST010->updateCheckboxGerarComprar($T006_Id, $selected);
	break;
	
	case 'est010flagCotar':
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		$sql = base64_decode($r_extra);
		$sql = stripslashes($sql);
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$result = mysql_query($sql);
		if(mysql_error()){
			mysql_query("ROLLBACK");
			return mysql_error();
		}
		if($r_operacao==1) {
			$campo='T006_Flag_Cotar';
		} else {
		    $campo='T006_Flag_Comprar';
		}
		while($linha = mysql_fetch_array($result)){
			$sql = "update T006 set {$campo}='S' where T006_Id='{$linha['T006_Id']}'";
			mysql_query($sql);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return mysql_error();
			}
		}
		mysql_query("COMMIT");
		$resposta['code'] = true;
	break;
	case 'est010FlagDesmarcarCotar':
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		$sql = base64_decode($r_extra);
		$sql = stripslashes($sql);
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$result = mysql_query($sql);
		if(mysql_error()){
			mysql_query("ROLLBACK");
			return mysql_error();
		}
		if($r_operacao==1) {
			$campo='T006_Flag_Cotar';
		} else {
		    $campo='T006_Flag_Comprar';
		}
		while($linha = mysql_fetch_array($result)){
			$sql = "update T006 set {$campo}='N' where T006_Id='{$linha['T006_Id']}'";
			mysql_query($sql);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return mysql_error();
			}
		}
		mysql_query("COMMIT");
		$resposta['code'] = true;
	break;
	case 'est010GerarOC':
		$sql=<<<EOT
			SELECT 
				count(T006_Id) as Total_Comprar
			FROM T006 
				LEFT JOIN T005 ON T005_Id=T006_T005_Id
				LEFT JOIN T012 ON T012_T006_Id=T006_Id
				LEFT JOIN T059 ON T059_T006_Id=T006_Id
				LEFT JOIN T129 ON T129_T006_Id=T006_Id
				LEFT JOIN T008 ON T008_T006_Id=T006_Id
				LEFT JOIN T007 ON T007_Id=T008_T007_Id
			WHERE T006_Flag_Comprar='S' 
			AND T005_C004_Id={$g['empresaAtual']} 
			AND T005_Flag_Status!=8 
			AND T005_Flag_Status>0 
			AND T005_Flag_Status!=6 
			AND T005_Flag_Status!=9 
			AND T006_Flag_Tipo_Produto='CP' 
			AND T006_Flag_Status!='3' 
			AND T012_Id IS NULL 
			AND T059_Id IS NULL 
			AND T129_Id IS NULL 
			AND T007_Id IS NULL
EOT;
		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);
		
		if ($row['Total_Comprar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para compra.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma ordem de compra dos {$row['Total_Comprar']} itens selecionados?";
		}
	break;
	
	case 'est010GerarCotacao':
		$sql=<<<EOT
			SELECT 
				count(T006_Id) as Total_Cotar
			FROM T006 
				LEFT JOIN T005 ON T005_Id=T006_T005_Id
				LEFT JOIN T012 ON T012_T006_Id=T006_Id
				LEFT JOIN T059 ON T059_T006_Id=T006_Id
				LEFT JOIN T129 ON T129_T006_Id=T006_Id
				LEFT JOIN T008 ON T008_T006_Id=T006_Id
				LEFT JOIN T007 ON T007_Id=T008_T007_Id
			WHERE T006_Flag_Cotar='S' 
			AND T005_C004_Id={$g['empresaAtual']} 
			AND T005_Flag_Status!=8 
			AND T005_Flag_Status>0 
			AND T005_Flag_Status!=6 
			AND T005_Flag_Status!=9 
			AND T006_Flag_Tipo_Produto='CP' 
			AND T006_Flag_Status!='3' 
			AND T012_Id IS NULL 
			AND T059_Id IS NULL 
			AND T129_Id IS NULL 
			AND T007_Id IS NULL
EOT;
		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);
		
		if ($row['Total_Cotar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para cotar.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma cotação dos {$row['Total_Cotar']} itens selecionados?";
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>