<?php
namespace hardness;

function validaCampoTipo($array){
	/**
	 * Estes status estão documentados na WIKI para a tabela T189
	 * ao alterar, favor alterar lá para manter atualizado
	 * 
	 * @link http://flycker/wiki/index.php?title=T189#T189_Flag_Tipo
	 */
	if($array['todosCampos']['T189_Flag_Tipo'] == 'C'){
		return "Cotar";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'O'){
		return "Orçamento venda";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'P'){
		return "Comprar";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'E'){
		return "Reposição estoque";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'R'){
		return "<font color='red'>Cancelado</font>";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == 'M'){
		return "Produzir";
	}
}

function validaCampoComprar($array){
	global $g;
	$disabled="";
	$C007_Nome = "";
	$retorno = "<input onchange=\"est014CheckboxComprar(this, '{$array['todosCampos']['T189_Id']}', '{$g['divId']}')\" type='checkbox' id='T189_Flag_Comprar' title='Comprar'";
	if(empty($array['todosCampos']['T189_D009_Id']) || ($array['todosCampos']['T189_D009_Id'] == "") || ($array['todosCampos']['T189_D009_Id'] == null)){
		$disabled = "disabled='disabled'";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == "R"){
		$disabled = "disabled='disabled'";
	} 
	if ($array['todosCampos']['T189_Flag_Comprar']=='S'){
		if($array['todosCampos']['T189_C007_Id_Comprar'] != $g['usuarioAtual']){
			$C007 = mysql_query("SELECT C007_Nome FROM C007 WHERE C007_Id='{$array['todosCampos']['T189_C007_Id_Cotar']}'");
			$mC007 = mysql_fetch_array($C007);
			$disabled = "disabled='disabled'";
			$nome = substr($mC007['C007_Nome'], 0,8);
			$C007_Nome = <<<EOT
			<font color='gray' style='font-size:8px;'>{$nome}</font>
EOT;
		}
		$retorno .="checked='checked'";
	}
	return $retorno .= " {$disabled}/>{$C007_Nome}";
}

function validaCampoProduzir($array){
	global $g;
	$disabled="";
	$C007_Nome = "";
	$retorno = "<input onchange=\"est014CheckboxProduzir(this, '{$array['todosCampos']['T189_Id']}', '{$g['divId']}')\" type='checkbox' id='T189_Flag_Produzir' title='Produzir'";
	if(empty($array['todosCampos']['T189_D009_Id']) || ($array['todosCampos']['T189_D009_Id'] == "") || ($array['todosCampos']['T189_D009_Id'] == null)){
		$disabled = "disabled='disabled'";
	} else if($array['todosCampos']['T189_Flag_Tipo'] == "R"){
		$disabled = "disabled='disabled'";
	} 
	if ($array['todosCampos']['T189_Flag_Produzir']=='S'){
		if($array['todosCampos']['T189_C007_Id_Comprar'] != $g['usuarioAtual']){
			$C007 = mysql_query("SELECT C007_Nome FROM C007 WHERE C007_Id='{$array['todosCampos']['T189_C007_Id_Cotar']}'");
			$mC007 = mysql_fetch_array($C007);
			//$disabled = "disabled='disabled'";
			$nome = substr($mC007['C007_Nome'], 0,8);
			$C007_Nome = <<<EOT
			<font color='gray' style='font-size:8px;'>{$nome}</font>
EOT;
		}
		$retorno .="checked='checked'";
	}
	$D049=mysql_query("SELECT D049_Flag_Tipo FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id WHERE D009_Id='{$array['todosCampos']['T189_D009_Id']}';");
	$mD049=mysql_fetch_array($D049);
	if($mD049['D049_Flag_Tipo'] != 'MD'){
		$disabled = "disabled='disabled'";
	}
	return $retorno .= " {$disabled}/>{$C007_Nome}";
}

function validaCampoIndisponivel($array){
	global $g;	
		$retorno = <<<EOT
		<input type='checkbox' name='T174_Flag_Indisponivel' title='Indisponível' onChange='est014CheckboxFlagIndisponivel(this, "{$array['todosCampos']['T174_Id']}", "{$g['divId']}")'
EOT;
		
		if ($array['todosCampos']['T174_Flag_Indisponivel']=='S'){
			$retorno .= <<<EOT
			checked = 'checked'
EOT;
		}
		$retorno .= <<<EOT
		 />
EOT;
		return $retorno;
}

function validaCampoOC($array){
	global $g;
	//$sql = "SELECT * FROM T011 WHERE T011_T010_Id='{$array['todosCampos']['T010_Id']}' AND T011_D024_Id='{$array['todosCampos']['D024_Id']}'";
	
	$sql = "SELECT * FROM T012 WHERE (T012_T174_Id='{$array['todosCampos']['T174_Id']}' OR T012_T189_Id='{$array['todosCampos']['T189_Id']}')";
	
	$result = mysql_query($sql);
	$total = mysql_num_rows($result);
	$linha = mysql_fetch_array($result);
	$novoIdTelaForm = uniqid();
	if($total > 0){
		$retorno = <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra: ' + {$linha['T012_T011_Id']}, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent('{$linha['T012_T011_Id']}'), [1000,600]);" style="font-weight:bold;">OC {$linha['T012_T011_Id']}</a>
EOT;
	} else {
		$retorno = <<<EOT
		<input type='checkbox' name='T174_Flag_Gerar_OC' title='Gerar ordem de compra' onChange='est014CheckboxGerarOC(this, "{$array['todosCampos']['T174_Id']}", "{$g['divId']}")'
EOT;
		
		if ($array['todosCampos']['T174_Flag_Gerar_OC']=='S'){
			$retorno .= <<<EOT
			checked = 'checked'
EOT;
		}
		$retorno .= <<<EOT
		 />
EOT;
	}
	return $retorno;
}

function validaCampoDtEnvio($array){
	global $g;
	$T173_Id=$array['todosCampos']['T173_Id'];
	$T010_Id=$array['todosCampos']['T010_Id'];
	$T139 = mysql_query("SELECT T139_Data_Hora FROM T139 WHERE T139_T173_Id='{$T173_Id}' AND T139_T010_Id='{$T010_Id}'");
	$mT139 = mysql_fetch_array($T139);
	$mT139['T139_Data_Hora'] = gCorrigeData($mT139['T139_Data_Hora']);
	return $mT139['T139_Data_Hora'];
}

function validaCampoTempo($array){
	global $g;
	$T173_Id=$array['todosCampos']['T173_Id'];
	$T010_Id=$array['todosCampos']['T010_Id'];
	$T174_Id=$array['todosCampos']['T174_Id'];
	$T139 = mysql_query("SELECT T139_Data_Hora FROM T139 WHERE T139_T173_Id='{$T173_Id}' AND T139_T010_Id='{$T010_Id}'");
	$mT139 = mysql_fetch_array($T139);
	
	$T174 = mysql_query("SELECT T174_Hora_Email_Forn FROM T174 WHERE T174_Id='{$T174_Id}'");
	$mT174 = mysql_fetch_array($T174);
	if(empty($mT174['T174_Hora_Email_Forn'])){
		
		$tmpEnviado = strtotime($mT139['T139_Data_Hora']);
		$tmpAtual = time();
		$result = ($tmpAtual - $tmpEnviado);
		
		$hours = floor($result / 3600);
		$minutes = floor(($result / 60) % 60);
		$seconds = $result % 60;
		
		$seconds = str_pad($seconds, 2, "0", STR_PAD_LEFT);
		$minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
		$hours = str_pad($hours, 2, "0", STR_PAD_LEFT);

		$retorno = "$hours:$minutes:$seconds";
	}else{
		$tmpEnviado = strtotime($mT139['T139_Data_Hora']);
		$tmpRetorno = strtotime($mT174['T174_Hora_Email_Forn']);
		$result = ($tmpRetorno - $tmpEnviado);
		
		$hours = floor($result / 3600);
		$minutes = floor(($result / 60) % 60);
		$seconds = $result % 60;
		
		$seconds = str_pad($seconds, 2, "0", STR_PAD_LEFT);
		$minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
		$hours = str_pad($hours, 2, "0", STR_PAD_LEFT);

		$retorno = "$hours:$minutes:$seconds";
	}
	return $retorno;
}
function validaCampoDtRetorno($array){
	global $g;
	$T173_Id=$array['todosCampos']['T173_Id'];
	$T010_Id=$array['todosCampos']['T010_Id'];
	$T174_Id=$array['todosCampos']['T174_Id'];
	$T174 = mysql_query("SELECT T174_Hora_Email_Forn FROM T174 WHERE T174_Id='{$T174_Id}'");
	$mT174 = mysql_fetch_array($T174);
	if(!empty($mT174['T174_Hora_Email_Forn'])){
		$mT174['T174_Hora_Email_Forn'] = gCorrigeData($mT174['T174_Hora_Email_Forn']);
	}else{
		$mT174['T174_Hora_Email_Forn']="00:00:00";
	}
	return $mT174['T174_Hora_Email_Forn'];
}

function validaCampoIconeEmail($array){
	global $g;
	
	if($array['todosCampos']['T174_Flag_Email_Enviado'] == 'S'){
		if(empty($array['todosCampos']['T174_Hora_Email_Forn'])){
			return "<span class=\"ui-icon ui-icon-mail-closed\"></span>";
		}else{
			return "<span class=\"ui-icon ui-icon-check\"></span>";
		}
	}
}

function validaCampoIconeFinalizado($array){
	global $g;
	
	if($array['todosCampos']['T010_Flag_Cotacao_Finalizada'] == 'S'){
		return "<span class=\"ui-icon ui-icon-check\"></span>";
	}
}

?>