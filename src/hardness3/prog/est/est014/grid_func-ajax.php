<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

// Classes
require_once('bibliotecas/classes/EST014.php');
$EST014 = new EST014();
require_once('bibliotecas/classes/EST008.php');
$EST008 = new EST008();
require_once('bibliotecas/classes/EST006.php');
$EST006 = new EST006();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'est014CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;
		$EST014->updateCheckboxGerarCotar($T189_Id, $selected);
	break;
	
	case 'est014CheckboxComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;
		$EST014->updateCheckboxGerarComprar($T189_Id, $selected);
	break;

	case 'est014CheckboxProduzir':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;
		$EST014->updateCheckboxGerarProducao($T189_Id, $selected);
	break;
	
	case 'est014FlagMarcar':

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
			$campo='T189_Flag_Cotar';
			while($linha = mysql_fetch_array($result)){
				$sql = "update T189 set {$campo}='S' where T189_Id='{$linha['T189_Id']}' AND T189_Flag_Tipo != 'R'";
				mysql_query($sql);
				if(mysql_error()){
					mysql_query("ROLLBACK");
					return mysql_error();
				}
			}
		} else if($r_operacao==2) {
		    $campo='T189_Flag_Comprar';
			while($linha = mysql_fetch_array($result)){
				$sql = "update T189 set {$campo}='S' where T189_Id='{$linha['T189_Id']}' AND (T189_D009_Id>0 OR T189_D009_Id IS NOT NULL AND T189_D009_Id != '') AND T189_Flag_Tipo != 'R'";
				mysql_query($sql);
				if(mysql_error()){
					mysql_query("ROLLBACK");
					return mysql_error();
				}
			}
		} else {
			$campo='T189_Flag_Produzir';
			while($linha = mysql_fetch_array($result)){
				$sql = "update T189 
				LEFT JOIN D009 ON D009_Id=T189_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id
				set {$campo}='S' where T189_Id='{$linha['T189_Id']}' AND (T189_D009_Id>0 OR T189_D009_Id IS NOT NULL AND T189_D009_Id != '') AND T189_Flag_Tipo != 'R' AND D049_Flag_Tipo='MD'";

				mysql_query($sql);
				if(mysql_error()){
					mysql_query("ROLLBACK");
					return mysql_error();
				}
			}
		}
		mysql_query("COMMIT");
	break;
	
	case 'est014FlagDesmarcar':
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
			$campo='T189_Flag_Cotar';
		} else if ($r_operacao==2) {
		    $campo='T189_Flag_Comprar';
		}else{
			$campo='T189_Flag_Produzir';
		}
		while($linha = mysql_fetch_array($result)){
			$sql = "update T189 set {$campo}='N' where T189_Id='{$linha['T189_Id']}'  AND T189_Flag_Tipo != 'R'";
			mysql_query($sql);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return mysql_error();
			}
		}
		mysql_query("COMMIT");
	break;
	
	case 'est014GerarOC':
		$sql="select count(T189_Id) as Total_Comprar from T189 where T189_Flag_Comprar='S' AND T189_C007_Id_Comprar='{$g['usuarioAtual']}'";
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

	case 'est014VerificaGerarProducao':
		$sql="select count(T189_Id) as Total_Produzir from T189 where T189_Flag_Produzir='S'";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);
		
		if ($row['Total_Produzir'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para compra.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma ORDEM DE SERVIÇO dos {$row['Total_Produzir']} itens selecionados?";
		}
	break;
	
	case 'est014GerarCotacao':
		$sql="select count(T189_Id) as Total_Cotar from T189 where T189_Flag_Cotar='S' AND T189_C007_Id_Cotar='{$g['usuarioAtual']}'";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);
		
		$T189 = mysql_query("SELECT * FROM T189 WHERE T189_FLag_Cotar='S'");
		
		
		if ($row['Total_Cotar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para cotar.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma cotação dos {$row['Total_Cotar']} itens selecionados?";
		}
	break;
	
	case 'est014VerificaGerarOC':
		$sql = base64_decode($r_sql);
		$sql = str_replace("\'", "'", $sql);
		$retorno = mysql_query($sql);
		$retorno2 = mysql_query($sql);
		
		echo mysql_error();
		$row = mysql_fetch_array($retorno);
		$D024 = $row['D024_Id'];        
		
		while($row2 = mysql_fetch_array($retorno2)){
			if($D024 != $row2['D024_Id']){
				$resposta['code'] = false;
				$resposta['data'] = "Fornecedores selecionados não são os mesmos";
			} else {
				$resposta['code'] = true;
				$resposta['data'] = "Você tem certeza que deseja gerar uma ordem de compra?";
				$resposta['D024_Id'] = $D024;
			}
		}   
	break;
	
	case 'CheckboxFlagIndisponivel':
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
		$EST008->updateT174_Flag_Indisponivel($T174_Id, $selected);
	break;
	
	case 'CheckboxFlagGerarOC':
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
		$EST008->updateT174_Flag_Gerar_OC($T174_Id, $selected);
	break;
	
	case 'est014aplicarPrecoVenda':
		$T174_Id 		= isset($r_T174_Id) ? $r_T174_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		
		//echo "174: ".$T174_Id."<br />campoEditado: ".$campoEditado."<br />valorAntigo: ".$valorAntigo."<br />NovoValor".$novoValor;
		
		
		$retorno = $EST014->est014AplicarIPV($T174_Id, $campoEditado, $valorAntigo, $novoValor);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est014CotacaoIncluirCabecalho':
		$retorno = $EST008->insereCotacaoItensViaRequisicao();
		
		if($retorno === true){
			$resposta['code']=true;
			$resposta['data']=$T010_Id;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est014OCIncluirCabecalho':
		$retorno = $EST006->insereOCItensViaRequisicao();
		
		if($retorno === true){
			$resposta['code']=true;
			$resposta['data']=$T011_Id;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est014IncluirOrdemServico':
		$T128_Id=0;
		$retorno = $EST008->insereOrdemServico();
		
		if($retorno === true){
			$resposta['code']=true;
			$resposta['data']=$T128_Id;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>