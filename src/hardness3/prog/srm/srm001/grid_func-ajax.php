<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

// Classes
require_once('bibliotecas/classes/EST014.php');
$EST014 = new EST014();
require_once('bibliotecas/classes/EST008.php');
$EST008 = new EST008();

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
		} else {
		    $campo='T189_Flag_Comprar';
			while($linha = mysql_fetch_array($result)){
				$sql = "update T189 set {$campo}='S' where T189_Id='{$linha['T189_Id']}' AND (T189_D009_Id>0 OR T189_D009_Id IS NOT NULL AND T189_D009_Id != '') AND T189_Flag_Tipo != 'R'";
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
		} else {
		    $campo='T189_Flag_Comprar';
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
		$sql="select count(T189_Id) as Total_Comprar from T189 where T189_Flag_Comprar='S'";
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
	
	case 'est014GerarCotacao':
		$sql="select count(T189_Id) as Total_Cotar from T189 where T189_Flag_Cotar='S'";
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
	
	case 'crm001checkGerarOrcamento':
		global $g;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T198_Id = isset($r_T198_Id) ? $r_T198_Id : false;
		
		if($T198_Id == 0){
			$insT198 = <<<EOT
			INSERT INTO T198 (T198_D009_Id, T198_C007_Id)
			VALUES
			('{$D009_Id}',
			'{$g['usuarioAtual']}');
EOT;
			$retorno = mysql_query($insT198);
		}else{
			$delT198 = <<<EOT
			DELETE FROM T198 WHERE T198_Id='{$T198_Id}';
EOT;
			$retorno = mysql_query($delT198);
		}
			
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
		break;
	
	case 'crm001checkDesmarcarTodos':
		$retorno = mysql_query("DELETE FROM T198");
		$erro = mysql_error();
		if ($retorno == true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		}
		break;
	
	case 'crm001checkMarcarTodos':
		global $g;
		$sqlFiltro = (isset($r_sqlFiltro)) ? $r_sqlFiltro : false;
		$sqlFiltroNormal = base64_decode($sqlFiltro);
		$sqlFiltroNormal = str_replace("\\", "", $sqlFiltroNormal);
		
		$retorno = mysql_query($sqlFiltroNormal);
		
		while($linha = mysql_fetch_array($retorno)){
			$T198 = <<<EOT
			INSERT INTO T198 (
				T198_D009_Id,
				T198_C007_Id
			) VALUES (
				'{$linha['D009_Id']}',
				'{$g['usuarioAtual']}'
			);
EOT;
			$retIns = mysql_query($T198);
			$erro = mysql_error();
		}
		if($retIns){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		}
	break;

	case 'crm001DuplicarPedido':
		$T005=mysql_query("SELECT T005_Id from T005 where T005_Flag_Marcado='S'");
		if(mysql_num_rows($T005) <= 0){
			$resposta['code'] = false;
		}else{
			$resposta['code'] = true;
		}
	break;

	case 'crm001VerificaAgruparPedidos':
		$telaEmitirNF = isset($r_telaEmitirNF) ? $r_telaEmitirNF : false;
		$sql = "SELECT T005_D024_Id from T005 where T005_Flag_Marcado='S'";
		if($telaEmitirNF==true){
			$sql .= " AND T005_Flag_Status=3";
		}

		$T005=mysql_query($sql);
		$count=1;
		$retorno=1;
		if(mysql_num_rows($T005) == 0){
			$retorno=2;
		}
		while($mT005=mysql_fetch_array($T005)){
			if($count==1){
				$D024_Id=$mT005['T005_D024_Id'];
			}
			if($D024_Id != $mT005['T005_D024_Id']){
				$retorno=2;
			}
			$count++;
		}
		if($retorno==2){
			$resposta['code'] = false;
		}else{
			$resposta['code'] = true;
		}
	break;

	case 'crm001AgruparPedidos':
		global $g;
		require_once('bibliotecas/classes/VEN002.php');
		$VEN002 = new VEN002();
		$retorno = $VEN002->VEN002AgruparPedidosSelecionados();

		if($retorno===true){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'crm001CheckboxDuplicarPedido':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		$T005=mysql_query("UPDATE T005 SET T005_Flag_Marcado='{$selected}' where T005_Id='{$T005_Id}'");
		if($T005_Id){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
		}
	break;

	case 'crm001CheckboxGerarNF':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		$sqlT005 = mysql_query("SELECT * FROM T005 WHERE T005_Id = '{$T005_Id}'");
		$T005 = mysql_fetch_array($sqlT005);
		if($T005['T005_C007_Gerar_NF'] == $g['usuarioAtual'] || $T005['T005_C007_Gerar_NF'] == '0' || $T005['T005_C007_Gerar_NF'] == '' || empty($T005['T005_C007_Gerar_NF'])){
			if($selected == 'S'){ $usuario = $g['usuarioAtual']; } else { $usuario = 0; }
			mysql_query("UPDATE T005 SET T005_Flag_Gerar_NF='{$selected}', T005_C007_Gerar_NF = '{$usuario}' where T005_Id='{$T005_Id}'");
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
		
	break;

	case 'crm001ClientesProcessar':
		global $g;
		require_once('bibliotecas/classes/VEN001.php');
		$VEN001 = new VEN001();
		$retorno = $VEN001->VEN001ClientesProcessar();

		if($retorno===true){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'abreXMLPedidos':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;		

		$cArquivo = "{$g['pathDados']}/ecommerce/xmlPedido/PEDIDO_{$T005_Id}.xml";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="PEDIDO_' . $T005_Id . '.xml"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte técnico.";
			return false;
		}
		die();
	break;

	case 'crm001SelecionarTodos':
		$extra = isset($r_extra) ? $r_extra : false;
		$sqlFiltroNormal = base64_decode($extra);
		$extra = str_replace("\\", "", $sqlFiltroNormal);
		$extra = gPegarParteWhere($extra);
		mysql_query("UPDATE T005 SET T005_Flag_Marcado = 'S' WHERE {$extra}");
		$resposta['code'] = true;
	break;

	case 'crm001MarcarTodosPedidos':
		$extra = isset($r_extra) ? $r_extra : false;
		$sqlFiltroNormal = base64_decode($extra);
		$extra = str_replace("\\", "", $sqlFiltroNormal);
		$extra = gPegarParteWhere($extra);
		$sql = "UPDATE T005 
		LEFT JOIN D024 AS Cliente ON Cliente.D024_Id=T005_D024_Id
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		LEFT JOIN D021 ON D021_Id=T005_D021_Id
		LEFT JOIN D022 ON D022_Id=T005_D022_Id
		LEFT JOIN D006 ON D006_Id=T005_D006_Id
 		LEFT JOIN D024 AS ClienteNF ON ClienteNF.D024_Id = T005_D024_Id_NFe
		LEFT JOIN C007 AS Vendedor ON Vendedor.C007_ID=T005_C007_Id_Vendedor_Interno
		LEFT JOIN C004 ON C004_Id=T005_C004_Id
		LEFT JOIN D036 ON T005_D036_Id=D036_Id 
		LEFT JOIN T007 ON T007_T005_Id=T005_Id AND T007_FLAG_CANCELADA!='S'
		LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=T005_Liberacao_Credito_Id
		LEFT JOIN C007 AS Separou ON Separou.C007_Id=T005_Usuario_Pecas 
		SET T005_Flag_Gerar_NF = 'S', T005_C007_Gerar_NF = '{$g['usuarioAtual']}' WHERE {$extra} 
		AND T005_C004_Id = '{$g['empresaAtual']}' 
		AND (T005_C007_Gerar_NF = 0 OR T005_C007_Gerar_NF IS NULL)";
		mysql_query($sql);
		$resposta['code'] = true;
	break;

	case 'crm001DesmarcarTodosPedidos':
		$extra = isset($r_extra) ? $r_extra : false;
		$sqlFiltroNormal = base64_decode($extra);
		$extra = str_replace("\\", "", $sqlFiltroNormal);
		$extra = gPegarParteWhere($extra);
		mysql_query("UPDATE T005 SET T005_Flag_Gerar_NF = 'N', T005_C007_Gerar_NF = 0 WHERE T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
		$resposta['code'] = true;
	break;

	case 'crm001VerificaGerarNotaFiscal':
		$resposta['code'] = true;
		$continuar = true;
		
		$res = mysql_query("SELECT T005_D024_Id FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
		if(mysql_num_rows($res) == 0){
			$resposta['data'] = "Nenhum pedido selecionado";
			$resposta['code'] = false;
			$continuar = false;
		}
		
		if ($continuar) {
			// Tira matrizes diferentes
			$matriz = false;
			$res = mysql_query("SELECT T005_D024_Id, T005_D024_Id_Nfe FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND (T005_D024_Id_Nfe IS NULL OR T005_D024_Id_Nfe = '') AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
			while ($row = mysql_fetch_assoc($res)) {
				if ($matriz && $matriz != $row['T005_D024_Id']) { 
					$resposta['data'] = "Pedidos de matrizes diferentes";
					$resposta['code'] = false;
					$continuar = false;
				}
				$matriz = $row['T005_D024_Id'];
			}
		}
		
		// Ou tem matriz ou não tem
		if ($continuar) {
			// Se tiver matriz
			if ($matriz) {
				$res = mysql_query("SELECT T005_D024_Id, T005_D024_Id_Nfe FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND (T005_D024_Id_Nfe IS NOT NULL AND T005_D024_Id_Nfe != '') AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
				while ($row = mysql_fetch_assoc($res)) {
					if ($row['T005_D024_Id_Nfe'] != $matriz) {
						$resposta['data'] = "Empresas diferentes";
						$resposta['code'] = false;
						$continuar = false;
					}
				}
			} else {
				$T005_D024_Id_Nfe = false;
				$res = mysql_query("SELECT T005_D024_Id, T005_D024_Id_Nfe FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
				while ($row = mysql_fetch_assoc($res)) {
					if (!$T005_D024_Id_Nfe) { $T005_D024_Id_Nfe = $row['T005_D024_Id_Nfe']; }
					if ($row['T005_D024_Id_Nfe'] != $T005_D024_Id_Nfe) {
						$resposta['data'] = "Empresas diferentes";
						$resposta['code'] = false;
						$continuar = false;
					}
				}
			}
		}
	break;

	case 'crm001GerarNotaFiscal':
		//Verifica qual pedido vai ser utilizado como cabeçalho
		$sqlT005 = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
		$T005 = array();
		while($res = mysql_fetch_array($sqlT005)){
			$T005[] .= $res['T005_Id'];
		}
		require_once('bibliotecas/classes/VEN012.php');
		$VEN012 = new VEN012();
		$retorno = $VEN012->gerarNotaFiscalPedidos($T005);
		if($retorno !== true){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
		}
	break;

	case 'crm001DesmarcarTodos':
		$extra = isset($r_extra) ? $r_extra : false;
		$sqlFiltroNormal = base64_decode($extra);
		$extra = str_replace("\\", "", $sqlFiltroNormal);
		$extra = gPegarParteWhere($extra);
		mysql_query("UPDATE T005 SET T005_Flag_Marcado = 'N' WHERE {$extra}");
		$resposta['code'] = true;
	break;

	case 'crm001CheckBoxEnviarAgenda':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		mysql_query("UPDATE D024 SET D024_Flag_Enviar_Agenda='{$selected}' WHERE D024_Id='{$D024_Id}'");
		$resposta['code'] = true;
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>