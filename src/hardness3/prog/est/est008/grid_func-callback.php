<?php
namespace hardness;

/**
* Weiberlan
* 24/02/2012
*/
  function campoFornecedor($array){
    // $sql="SELECT GROUP_CONCAT(D024_Nome_Empresa SEPARATOR '<br />') as D024_Nome_Empresa FROM T173 LEFT JOIN D024 ON T173_D024_Id=D024_Id WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
    // $resultado=mysql_query($sql);
    // $row=mysql_fetch_array($resultado);
    // return $row['D024_Nome_Empresa'];
    
    $sql = "SELECT D024_Id,T173_Flag_Alcada_Status,D024_Nome_Empresa,T173_Id FROM T173 LEFT JOIN D024 on D024_Id=T173_D024_Id WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
    $result = mysql_query($sql);
    $retorno = "";
    $totalForn=0;
    while($linha = mysql_fetch_array($result)){
        $sql2 = "SELECT T174_Valor_Total FROM T174 WHERE T174_T173_Id='{$linha['T173_Id']}'";
        $result2 = mysql_query($sql2);
        while($linhaT174 = mysql_fetch_array($result2)){
            $totalForn = $totalForn + $linhaT174['T174_Valor_Total'];
        }
        $totalForn = gCorrigeNumero($totalForn);
        if($linha['T173_Flag_Alcada_Status'] == 'K'){
            $alcada = "OK<br />";
        }elseif ($linha['T173_Flag_Alcada_Status'] == 'A'){
            $alcada = "<font color=\"green\">Aprovado</font><br />";        
        }elseif ($linha['T173_Flag_Alcada_Status'] == 'N'){
            $alcada = "<font color=\"red\">Negado - ".$linha['T173_Motivo_Nao_Liberado']."</font><br />";
        }elseif ($linha['T173_Flag_Alcada_Status'] == 'L'){
            $alcada = "<font color=\"blue\">Em Análise</font><br />";    
        }
        $retorno .=  "{$linha['D024_Nome_Empresa']} / {$alcada}";
    }
    return $retorno;
}

function campoCodFornecedor($array){
    // $sql="SELECT GROUP_CONCAT(D024_Nome_Empresa SEPARATOR '<br />') as D024_Nome_Empresa FROM T173 LEFT JOIN D024 ON T173_D024_Id=D024_Id WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
    // $resultado=mysql_query($sql);
    // $row=mysql_fetch_array($resultado);
    // return $row['D024_Nome_Empresa'];
    
    $sql = "SELECT D024_Id FROM T173 LEFT JOIN D024 on D024_Id=T173_D024_Id WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
    $result = mysql_query($sql);
    $retorno = "";
    $totalForn=0;
    while($linha = mysql_fetch_array($result)){
        $retorno .=  "{$linha['D024_Id']} <br />";
    }
    return $retorno;
}

function validaCampoTotalPorFornecedor($array){
	$sql2 = "SELECT T174_Valor_Total,T174_Quantidade FROM T174 WHERE T174_T173_Id='{$array['todosCampos']['T173_Id']}'";
	$result2 = mysql_query($sql2);
	$totalForn=0;
	while($linhaT174 = mysql_fetch_array($result2)){
		$totalForn += ($linhaT174['T174_Valor_Total']);
	}
	$totalForn = gCorrigeNumero($totalForn);
	return $totalForn;
}

function validaCampoTotalPorFornecedorMaisQunt($array){
	$sql2 = "SELECT T174_Valor_Total,T174_Quantidade FROM T174 WHERE T174_T173_Id='{$array['todosCampos']['T173_Id']}'";
	$result2 = mysql_query($sql2);
	$totalForn=0;
	while($linhaT174 = mysql_fetch_array($result2)){
		$totalForn += ($linhaT174['T174_Valor_Total'] * $linhaT174['T174_Quantidade']);
	}
	$totalForn = gCorrigeNumero($totalForn);
	return $totalForn;
}
function validaCampoTotalPorFornecedorCotado($array){
	$sql2 = "SELECT T174_Valor_Total FROM T174 WHERE T174_T173_Id='{$array['todosCampos']['T173_Id']}' AND T174_Flag_Gerar_OC='1'";
	$result2 = mysql_query($sql2);
	$totalForn=0;
	while($linhaT174 = mysql_fetch_array($result2)){
		$totalForn = $totalForn + $linhaT174['T174_Valor_Total'];
	}
	$totalForn = gCorrigeNumero($totalForn);
	return $totalForn;
}
function campoValorCotacao($array){
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$retorno = "";
	$totalForn=0;
	while($linha = mysql_fetch_array($result)){
		$sql2 = "SELECT SUM(T174_Valor_Total) as total FROM T174 WHERE T174_T173_Id='{$linha['T173_Id']}'";
		$result2 = mysql_query($sql2);
		$linhaT174 = mysql_fetch_array($result2);
		//echo 'totalForn='.$totalForn.'<br />';
		$totalForn = gCorrigeNumero($linhaT174['total']);
		$retorno .= $totalForn."<br />";
	}
	
	return $retorno;
}

function campoValorCotadoTotal($array){
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$retorno = "";
	$totalForn=0;
	while($linha = mysql_fetch_array($result)){
		$sql2 = "SELECT (SUM(T174_Valor_Custo_Unitario*T174_Quantidade)) as total FROM T174 WHERE T174_T173_Id='{$linha['T173_Id']}'";
		$result2 = mysql_query($sql2);
		$linhaT174 = mysql_fetch_array($result2);
		//echo 'totalForn='.$totalForn.'<br />';
		$totalForn = gCorrigeNumero($linhaT174['total']);
		$retorno .= $totalForn."<br />";
	}
	
	return $retorno;
}

function campoValorCotadoFornecedor($array){
	$sql = mysql_query("SELECT SUM(T174_Valor_Custo_Unitario*T174_Quantidade) AS Cotado  FROM T174 WHERE T174_T173_Id='{$array['todosCampos']['T173_Id']}'");
	$result = mysql_fetch_array($sql);	
	$totalForn = gCorrigeNumero($result['Cotado']);
	return $totalForn;
}

function validaCampoTotalConfirmadoMaisQuant($array){
	$sql2 = "SELECT T174_Valor_Total,T174_Quantidade FROM T174 WHERE T174_T173_Id='{$array['todosCampos']['T173_Id']}' AND T174_Flag_Gerar_OC='1'";
	$result2 = mysql_query($sql2);
	$totalForn=0;
	while($linhaT174 = mysql_fetch_array($result2)){
		$totalForn += ($linhaT174['T174_Valor_Total'] * $linhaT174['T174_Quantidade']);
	}
	$totalForn = gCorrigeNumero($totalForn);
	return $totalForn;
}

function campoValorConfirmado($array){
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$retorno = "";
	$totalForn=0;
	while($linha = mysql_fetch_array($result)){
		$sql2 = "SELECT SUM(T174_Valor_Total) as total FROM T174 WHERE T174_T173_Id='{$linha['T173_Id']}' AND T174_Flag_Gerar_OC='1'";
		$result2 = mysql_query($sql2);
		$linhaT174 = mysql_fetch_array($result2);
		//echo 'totalForn='.$totalForn.'<br />';		
		$totalForn = gCorrigeNumero($linhaT174['total']);
		$retorno .= $totalForn."<br />";
	}
	
	return $retorno;
	
	// $sql="SELECT T173_Valor_Confirmado FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	// $resultado=mysql_query($sql);
	// $retorno='';
	// while($row=mysql_fetch_array($resultado)){
		// $retorno .= gCorrigeNumero($row['T173_Valor_Confirmado']) . "<br />";
	// }
	// return $retorno;
}

//Função que define a classe CSS para cada linha do GRID
function colunaStatus($array) {
   if ($array['todosCampos']['Status'] == "PENDENTE") {
   		return "<font color='red'>PENDENTE</font>";
   }else{
   		return $array['todosCampos']['Status'];
   }
}

function colunaStatusNovo($array){
	if ($array['todosCampos']['T010_Flag_Cotacao_Finalizada'] == "S"){
		return "<font color='blue'><b>Conluído</b></font>";
	} else {
		return "<font color='red'>Pendente</font>";
	}
}

function retornaStatusAlcada($array){
	global $g;
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	$result = mysql_query($sql);
	$retorno = "";	
	while($linha = mysql_fetch_array($result)){
		if($linha['T173_Flag_Alcada_Status'] == 'K'){
			$retorno .= "OK<br />";
		}elseif ($linha['T173_Flag_Alcada_Status'] == 'A'){
			$retorno .= "<font color=\"green\">Aprovado</font><br />";		
		}elseif ($linha['T173_Flag_Alcada_Status'] == 'N'){
			$retorno .= "<font color=\"red\">Negado - ".$linha['T173_Motivo_Nao_Liberado']."</font><br />";
		}elseif ($linha['T173_Flag_Alcada_Status'] == 'L'){
			$retorno .= "<font color=\"blue\">Em Análise</font><br />";	
		}
	}
	return $retorno;
}

function retornaStatusAlcadaNovo($array){
	$sql = "SELECT T173_Flag_Alcada_Status, T173_Valor_Liberou_Alcada FROM T173 WHERE T173_Id='{$array['todosCampos']['T173_Id']}'";
	$result = mysql_query($sql);
	$linha = mysql_fetch_assoc($result);
	$valor = gCorrigeNumero($linha['T173_Valor_Liberou_Alcada']);
	if($linha['T173_Flag_Alcada_Status'] == 'K'){
		return "OK<br />";
	}elseif ($linha['T173_Flag_Alcada_Status'] == 'A'){
		return "<font color=\"green\"><b>Aprovado</b></font> Valor: ".$valor."<br />";		
	}elseif ($linha['T173_Flag_Alcada_Status'] == 'N'){
		return "<font color=\"red\">Negado - ".$linha['T173_Motivo_Nao_Liberado']."</font><br />";
	}elseif ($linha['T173_Flag_Alcada_Status'] == 'L'){
		return "<font color=\"blue\">Em Análise</font><br />";	
	}
}

function campoPernc($array) {
	$T173_Id = $array['todosCampos']['T173_Id'];
	$sql = "SELECT T174_Valor_Custo_Unitario,
			D009_Id,
			T010_Data,
			(select T014_Valor_Custo_Unitario from T014 left join T013 on T013_Id=T014_T013_Id where T014_D009_Id=D009_Id and T013_Data_Emissao < T010_Data and T014_Valor_Custo_Unitario > 0 and T013_Numero_Nota_Fiscal not like 'A%' order by T014_Id desc limit 1) as Preco_Fob
			FROM T174
			LEFT JOIN T010 ON T010_Id=T174_T010_Id 
			LEFT JOIN T059 ON T059_Id=T174_T059_Id
			LEFT JOIN D009 ON D009_Id=T059_D009_Id
			WHERE T174_T010_Id='{$array['todosCampos']['T059_T010_Id']}' 
			AND T174_T173_Id='{$T173_Id}' 
			AND T174_T059_Id='{$array['todosCampos']['T059_Id']}'";
	
   	$resultado = mysql_query($sql);
   	$row = mysql_fetch_array($resultado);	
    if (($row['T174_Valor_Custo_Unitario']>0) and ($row['Preco_Fob']>0))
    {
        $Percentual=number_format((($row['Preco_Fob']-$row['T174_Valor_Custo_Unitario'])/$row['T174_Valor_Custo_Unitario'])*100,2);
        if ($Percentual>0)
        {
            return number_format($Percentual,2,',','.');
        }
        else if ($Percentual<0)
        {
            $Percentual=$Percentual*-1;
            return number_format($Percentual,2,',','.');
        }
        else
        {
            return "&nbsp;";
        }
    }
    else
    {
         return "&nbsp;";
    }
}

function campoTotal($array) {
	$T173_Id = $array['todosCampos']['T173_Id'];
	$sql = "SELECT T174_Valor_Custo_Unitario,T173_Desconto, T174_Quantidade, T174_IPI FROM T174 LEFT JOIN T173 ON T173_Id=T174_T173_Id WHERE T174_T010_Id='{$array['todosCampos']['T059_T010_Id']}' AND T174_T173_Id='{$T173_Id}' AND T174_T059_Id='{$array['todosCampos']['T059_Id']}'";
   	$resultado = mysql_query($sql);
   	$row = mysql_fetch_array($resultado);
	
	// T010_Flag_Quantidade_Individual
	$sql = "SELECT T010_Flag_Quantidade_Individual FROM T010 WHERE T010_Id='{$array['todosCampos']['T059_T010_Id']}'";
	$result = mysql_query($sql);
	$linhaT010 = mysql_fetch_array($result);
	
	if($linhaT010['T010_Flag_Quantidade_Individual'] == "N"){
		$qnt = $array['todosCampos']['T059_Quantidade'];
	}else{
		$qnt = $row['T174_Quantidade'];
	}
	
   	$desconto = ($row['T174_Valor_Custo_Unitario']*$qnt) - ($row['T174_Valor_Custo_Unitario']*$qnt * ($row['T173_Desconto']/100));
   	$descontoIpi = $desconto + ($desconto * $row['T174_IPI']/100);
   	$Total=number_format($descontoIpi,2,',','.');
   	//(T059_Quantidade * T174_Valor_Custo_Unitario)  * (1 - T173_Desconto)
   	if($row > 0){
   		return $Total;
	}else{
		return '0,00';
	}
}
function campoIPI($array) {
	global $g;
	$T173_Id = $array['todosCampos']['T173_Id'];
	$sql = "SELECT T174_IPI FROM T174 WHERE T174_T010_Id='{$array['todosCampos']['T059_T010_Id']}' AND T174_T173_Id='{$T173_Id}' AND T174_T059_Id='{$array['todosCampos']['T059_Id']}'";
   	$resultado = mysql_query($sql);
   	$row = mysql_fetch_array($resultado);
   	$IPI=number_format($row['T174_IPI'],2,',','.');
   	//(T059_Quantidade * T174_Valor_Custo_Unitario)  * (1 - T173_Desconto)
   	if($row > 0){
   		return "<div onblur=\"divRefresh('{$g['divId']}', true);\" onclick=\"gEditarCampo('T174_IPI', \$(this), '', 'moeda-2', '', ''); return false;\"></span>{$IPI}</div>";
	}else{
		return '0,00';
	}
}

function campoOC($array) {
	$T173_Id = $array['todosCampos']['T173_Id'];
	//return $T173_Id;
	$sql = "SELECT T174_Id,T174_Flag_Gerar_OC FROM T174 WHERE T174_T010_Id='{$array['todosCampos']['T059_T010_Id']}' AND T174_T173_Id='{$T173_Id}' AND T174_T059_Id='{$array['todosCampos']['T059_Id']}'";	
   	$resultado = mysql_query($sql);
   	$row = mysql_fetch_array($resultado);
	
	$sqlT010 = "SELECT * FROM T010 WHERE T010_Id='{$array['todosCampos']['T059_T010_Id']}'";
	$resultT010 = mysql_query($sqlT010);
	$linhaT010 = mysql_fetch_array($resultT010);
	
	$sqlT173 = "SELECT T173_D024_Id FROM T173 WHERE T173_Id='{$T173_Id}'";
	$resultT173 = mysql_query($sqlT173);
	$linhaT173 = mysql_fetch_array($resultT173);
	
	$sqlT011 = "SELECT * FROM T011 WHERE T011_T010_Id='{$linhaT010['T010_Id']}' AND T011_D024_Id='{$linhaT173['T173_D024_Id']}'";
	$resultT011 = mysql_query($sqlT011);
	$totalT011 = mysql_num_rows($resultT011);
	$linhaT011 = mysql_fetch_array($resultT011);
	$numOC="";
	if($totalT011 > 0 || $linhaT010['T010_Flag_Cotacao_Finalizada'] == "S"){
		$disabled = "disabled='disabled'";
		if(!empty($linhaT011['T011_Id'])){
			$numOC = "<b>OC ".$linhaT011['T011_Id']."</b>";
		}
	} else {
		$disabled="";
		$numOC="";
	}
	
	global $g;	
		$retorno = <<<EOT
		<input type='checkbox' name='T174_Flag_Gerar_OC' title='Gerar OC' {$disabled} onChange='est008CheckboxGerarOC(this, "{$row['T174_Id']}", "{$g['divId']}")'
EOT;
		if ($row['T174_Flag_Gerar_OC']=='1'){
			$retorno .= <<<EOT
			checked = 'checked'
EOT;
		}
		$retorno .= <<<EOT
		 />{$numOC}
EOT;
		return $retorno;
}

function validaCampoIndisponivel($array){
	global $g;	
		$retorno = <<<EOT
		<input type='checkbox' name='T174_Flag_Indisponivel' title='Indisponível' onChange='est008CheckboxFlagIndisponivel(this, "{$array['todosCampos']['T174_Id']}", "{$g['divId']}")'
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
function campoeMail($array){
	global $g;
	if($array['todosCampos']['T173_Flag_EMail_Enviado'] == "S"){
		$T139 = mysql_query("SELECT * FROM T139 WHERE T139_T173_Id='{$array['todosCampos']['T173_Id']}'");
		//die($T139);
		$mT139 = mysql_fetch_array($T139);
		$mT139['T139_Data_Hora'] = explode(' ', $mT139['T139_Data_Hora']);
		$data = gCorrigeData($mT139['T139_Data_Hora'][0]);
		$hora = explode(":", $mT139['T139_Data_Hora'][1]);
		$hora = "{$hora[0]}:{$hora[1]}";
		$dataHora = "{$data} - {$hora}";
		return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T173_Id=' + encodeURIComponent('{$array['todosCampos']['T173_Id']}'), [900,550]);\" style=\"text-decoration:none;\">{$dataHora}</a>";
	} else {
		return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T173_Id=' + encodeURIComponent('{$array['todosCampos']['T173_Id']}'), [900,550]);\"><span class=\"ui-icon ui-icon-mail-closed\"></span></a>";
	}
}
function validaCampoImprimir($array){
	global $g;
	$T173_T010_Id = $array['todosCampos']['T173_T010_Id'];
	$D024_Id = $array['todosCampos']['T173_D024_Id'];
	return  "<a href=\"#\" onclick=\"abrirImpressao('{$T173_T010_Id}', '{$D024_Id}')\"><span class=\"ui-icon ui-icon-print\"></span></a>";
}

function campoeMail2($array){
	global $g;
	$T173_T010_Id = $array['todosCampos']['T173_T010_Id'];
	$T173_Id = $array['todosCampos']['T173_Id'];
	$D024_Id = $array['todosCampos']['T173_D024_Id'];
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar E-mail', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T173_Id_EmailGravar=' + encodeURIComponent('{$T173_Id}') + '&T010_Id=' + encodeURIComponent('{$T173_T010_Id}') + '&T173_D024_Id=' + encodeURIComponent('{$D024_Id}'), [900,550]);\" ><span class=\"ui-icon ui-icon-mail-closed\"></span></a>";
}

function validaCampoValor($array){
	$sql= "SELECT T174_Flag_Gerar_OC,T174_Valor_Custo_Unitario,T174_Valor_Total,T174_Quantidade, T059_Quantidade, T174_T010_Id,T174_T173_Id,T174_T059_Id,D001_Codigo_Produto,D001_Descricao_Produto FROM T174 
			LEFT JOIN T059 ON T059_Id=T174_T059_Id
			LEFT JOIN D009 ON D009_Id=T059_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN T173 ON T173_Id=T174_T173_Id
			WHERE T174_T010_Id='{$array['todosCampos']['T010_Id']}' AND T174_Valor_Custo_Unitario>0 AND T174_Flag_Gerar_OC='1' AND T173_D024_Id='{$array['todosCampos']['D024_Id']}'";  		
	//echo $sql."<br /><br />";
	$ret=mysql_query($sql);
	$total=0;
	while($T174 = mysql_fetch_array($ret)){
			
		$sql = "SELECT T010_Flag_Quantidade_Individual from T010 WHERE T010_Id='{$array['todosCampos']['T010_Id']}'";
		$result = mysql_query($sql);
		$linhaT010 = mysql_fetch_array($result);
		//return "T174= ".$T174['T174_Quantidade']."<br />T059: ".$T174['T059_Quantidade']."<br /> Flag: ".$linhaT010['T010_Flag_Quantidade_Individual'];
		/*
		if($linhaT010['T010_Flag_Quantidade_Individual'] == "S"){
			$qnt = $T174['T174_Quantidade'] * $T174['T174_Valor_Custo_Unitario'];
		}else{
			$qnt = $T174['T059_Quantidade'] * $T174['T174_Valor_Custo_Unitario'];
		}
		*/
		$total = $total + $T174['T174_Valor_Total'];
	}
	
	$valor = gCorrigeNumero($total);
	return $valor;
}

function validaCampoFrete($array){
	$sql= "SELECT T174_Flag_Gerar_OC,T174_Valor_Custo_Unitario,T174_Valor_Total,T174_Quantidade,T173_Frete,T059_Quantidade, T174_T010_Id,T174_T173_Id,T174_T059_Id,D001_Codigo_Produto,D001_Descricao_Produto FROM T174 
			LEFT JOIN T059 ON T059_Id=T174_T059_Id
			LEFT JOIN D009 ON D009_Id=T059_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN T173 ON T173_Id=T174_T173_Id
			WHERE T174_T010_Id='{$array['todosCampos']['T010_Id']}' AND T174_Valor_Custo_Unitario>0 AND T174_Flag_Gerar_OC='1' AND T173_D024_Id='{$array['todosCampos']['D024_Id']}'";  		
	$ret=mysql_query($sql);
	$total=0;
	while($T174 = mysql_fetch_array($ret)){
			
		$sql = "SELECT T010_Flag_Quantidade_Individual from T010 WHERE T010_Id='{$array['todosCampos']['T010_Id']}'";
		$result = mysql_query($sql);
		$linhaT010 = mysql_fetch_array($result);
		$percFrete = $T174['T173_Frete'];
		$valorTotal = $T174['T174_Valor_Total'];
		$total = $total + ($valorTotal*($percFrete/100));
	}
	
	$valor = gCorrigeNumero($total);
	return $valor;
}

function validaCampoMelhorPreco($array){
	global $g;
	$T010_Id = $array['todosCampos']['T059_T010_Id'];
	$T059_Id = $array['todosCampos']['T059_Id'];
	
	$sql = "SELECT T174_Valor_Total, D024_Nome_Empresa FROM T174 left join T173 on T173_Id=T174_T173_Id left join D024 on D024_Id=T173_D024_Id WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$T059_Id}' AND (T174_Valor_Total<>'0.00') ORDER BY T174_Valor_Total ASC";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	$preco = gCorrigeNumero($linha['T174_Valor_Total']);
	if((empty($preco)) || ($preco == null) || ($preco == '0,00')){
		return "N/A";
	}
	return $linha['T174_Valor_Total'];
}

function validaCampoPrecoUnit($array){
	global $g;
	$T010_Id = $array['todosCampos']['T059_T010_Id'];
	$T059_Id = $array['todosCampos']['T059_Id'];
	
	$sql = "SELECT T174_Valor_Total, D024_Nome_Empresa,T174_Valor_Custo_Unitario FROM T174 left join T173 on T173_Id=T174_T173_Id left join D024 on D024_Id=T173_D024_Id WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$T059_Id}' AND (T174_Valor_Total<>'0.00') ORDER BY T174_Valor_Total ASC";
	//echo $sql."<br />";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	$preco = gCorrigeNumero($linha['T174_Valor_Custo_Unitario']);
	if((empty($preco)) || ($preco == null) || ($preco == '0,00')){
		return "N/A";
	}
	return $linha['T174_Valor_Custo_Unitario'];
}
function campoOCGridIniciarCotacao($array) {
	$T010_Id = $array['todosCampos']['T059_T010_Id'];
	$T059_Id = $array['todosCampos']['T059_Id'];
	
	$sql = "SELECT T174_T173_Id FROM T174 WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$T059_Id}' AND (T174_Valor_Total<>'0.00' OR T174_Valor_Total IS NOT NULL) ORDER BY T174_Valor_Total ASC";
	$resultT174 = mysql_query($sql);
	$linhaT174 = mysql_fetch_array($resultT174);
	$sql = "SELECT T174_Id,T174_Flag_Gerar_OC FROM T174 WHERE T174_T010_Id='{$array['todosCampos']['T059_T010_Id']}' AND T174_T173_Id='{$linhaT174['T174_T173_Id']}' AND T174_T059_Id='{$array['todosCampos']['T059_Id']}'";	
   	$resultado = mysql_query($sql);
   	$row = mysql_fetch_array($resultado);
	global $g;	
		$retorno = "<input type='checkbox' name='T174_Flag_Gerar_OC' title='Gerar OC' onChange=\"est008CheckboxGerarOC(this, '{$row['T174_Id']}', '{$g['divId']}')\" ";
		
		if ($row['T174_Flag_Gerar_OC']=='1'){
			$retorno .="checked = 'checked'";
		}
		return $retorno .= " />";
}
function calculaFrete($array){
	$percFrete = $array['todosCampos']['T173_Frete'];
	$valorTotal = $array['todosCampos']['T174_Valor_Total'];
	if ($percFrete>0){
		return gCorrigeNumero($valorTotal*($percFrete/100)); 
	}else{
		return '0,00';
	}
}

function calculaItens($array){
	$sql = "SELECT COUNT(*) as itens FROM T174 WHERE T174_Flag_Gerar_OC='1' AND T174_T173_Id='{$array['todosCampos']['T173_Id']}' AND T174_T010_Id='{$array['todosCampos']['T010_Id']}'";	
	$result = mysql_query($sql);
	$res = mysql_fetch_array($result);	
	return $res['itens'];
}

function campoValorUnitarioForn($array) {
	global $totais, $g;
	$T173_Id = $array['todosCampos']['T173_Id'];
   	$sql = "SELECT T174_Valor_Custo_Unitario FROM T174 WHERE T174_T010_Id='{$array['todosCampos']['T059_T010_Id']}' AND T174_T173_Id='{$T173_Id}' AND T174_T059_Id='{$array['todosCampos']['T059_Id']}'";	
   	$resultado = mysql_query($sql);
   	$row = mysql_fetch_array($resultado);
   	if($row == 0){
   		$row['T174_Valor_Custo_Unitario'] = '0,00';
	}
	$sql = "SELECT T010_Flag_Marca_Individual FROM T010 WHERE T010_Id='{$array['todosCampos']['T059_T010_Id']}'";	
	$resultado = mysql_query($sql);
	$row2 = mysql_fetch_array($resultado);
	
	$sql = "SELECT T174_T173_Id, T174_Valor_Custo_Unitario FROM T174 WHERE T174_T059_Id='{$array['todosCampos']['T059_Id']}' ORDER BY T174_Valor_Custo_Unitario";
	$resultado = mysql_query($sql);
	$row3Emp = array();
	$menor = 0;
	if (mysql_numrows($resultado)) {
		while ($row3 = mysql_fetch_array($resultado)) {
			if ($row3['T174_Valor_Custo_Unitario'] == 0) {
				$row3Emp[$row3['T174_T173_Id']] = array(
					'Valor' => $row3['T174_Valor_Custo_Unitario'],
					'Menor' => false
				);
				continue;
			}
			if ($menor == 0) { $menor = $row3['T174_Valor_Custo_Unitario']; }
			$row3Emp[$row3['T174_T173_Id']] = array(
				'Valor' => $row3['T174_Valor_Custo_Unitario'],
				'Menor' => ($row3['T174_Valor_Custo_Unitario'] == $menor) ? true : false
			);
		}
		if (!isset($totais[$T173_Id])) { $totais[$T173_Id] = 0; }
		$totais[$T173_Id] += $row['T174_Valor_Custo_Unitario'];
		
		$T174_Valor_Custo_Unitario = gCorrigeNumero($row3Emp[$T173_Id]['Valor']);
	} else {
		$T174_Valor_Custo_Unitario = gCorrigeNumero(0);
	}
	if (isset($row3Emp[$T173_Id]['Menor']) && $row3Emp[$T173_Id]['Menor']){
   		return "<div onblur=\"divRefresh('{$g['divId']}', true);\" onclick=\"gEditarCampo('T174_Valor_Custo_Unitario', \$(this), '', 'moeda-2', '', ''); return false;\">{$T174_Valor_Custo_Unitario}</div>";
	}else{
		return "<div onblur=\"divRefresh('{$g['divId']}', true);\" onclick=\"gEditarCampo('T174_Valor_Custo_Unitario', \$(this), '', 'moeda-2', '', ''); return false;\">{$T174_Valor_Custo_Unitario}</div>";
	}
}

function buscaMarca($array){
	return $array['todosCampos']['D082_Marca'];
}

function validaCampoMarca($array, $T010_Id){
	global $g;
	$marca = $array['todosCampos']['D082_Marca'];
	$T059_Id = $array['todosCampos']['T059_Id'];
	$T173_Id = $array['todosCampos']['T173_Id'];
	$T174_D009_Id = $array['todosCampos']['T174_D009_Id'];
	
	$T010 = mysql_query("SELECT T010_Flag_Marca_Individual FROM T010 WHERE T010_Id='{$T010_Id}'");
	$mT010 = mysql_fetch_array($T010);
	if($mT010['T010_Flag_Marca_Individual'] == "N"){
		return $marca;
	}else{
		return <<<EOT
		<input type='button' value='{$marca}' 
		onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Marca', '/est/est008/content/est008ContentInserirMarca/', 
		'&tab=geral&T059_Id=' 	   + encodeURIComponent('{$T059_Id}') + 
		          '&T010_Id='      + encodeURIComponent('{$T010_Id}') + 
		          '&T173_Id=' 	   + encodeURIComponent('{$T173_Id}') + 
		          '&T174_D009_Id=' + encodeURIComponent('{$T174_D009_Id}')
		          , [300,150]);" />
EOT;
	}
}

function validaCampoOCGerada($array){
	$sql = "SELECT * FROM T011 WHERE T011_T010_Id='{$array['todosCampos']['T010_Id']}' AND T011_D024_Id='{$array['todosCampos']['D024_Id']}'";
	$result = mysql_query($sql);
	$total = mysql_num_rows($result);
	$linha = mysql_fetch_array($result);
	if($total > 0){
		return '<span class="ui-icon ui-icon-circle-check" title="Ordem de compra gerada" style="float:left;"></span><span style="padding-right:15px;" title="Ordem de compra gerada" >OC '.$linha['T011_Id'].'</span>';
	}
}
function validaCampoOCGeradaGridFornecedores($array){
	global $g;
	$sql = "SELECT * FROM T011 WHERE T011_T010_Id='{$array['todosCampos']['T173_T010_Id']}' AND T011_D024_Id='{$array['todosCampos']['T173_D024_Id']}'";
	$result = mysql_query($sql);
	$total = mysql_num_rows($result);
	$linha = mysql_fetch_array($result);
	$novoIdTelaForm = uniqid();
	if($total > 0){
		return  <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra: ' + {$linha['T011_Id']}, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent('{$linha['T011_Id']}'), [1000,600]);"><b>OC {$linha['T011_Id']}</b></a>
EOT;
	}
}

function preVisualiza($array){
		global $g;
		$T010_Id=$array['todosCampos']['T010_Id'];
		$T174_Id=$array['todosCampos']['T174_Id'];
		$T173_Id=$array['todosCampos']['T173_Id'];
		$D024_Id=$array['todosCampos']['D024_Id'];
		return <<<EOT
		<input type='button' value='Pré Visualizar' onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-Visualização', '/est/est008/content/est008ContentResumoCotacao/', '&tab=geral&T010_Id=' + encodeURIComponent('{$T010_Id}') + '&T174_Id=' + encodeURIComponent('{$T174_Id}') + '&T173_Id=' + encodeURIComponent('{$T173_Id}') + '&D024_Id=' + encodeURIComponent('{$D024_Id}'), [700,500]);" />
EOT;
}

function validaCampoEmailEnviadoForn($array){
	$T010_Id=$array['todosCampos']['T010_Id'];
	
	$sql = "SELECT * FROM T173 WHERE T173_T010_Id='{$T010_Id}' ORDER BY T173_Id";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$retorno = "";
	while($linha = mysql_fetch_array($result)){
		if($linha['T173_Flag_EMail_Enviado'] == "S"){
			$retorno .= "<span class=\"ui-icon ui-icon-mail-closed\"></span>";
		} else {
			$retorno .= "-<br />";
		}
		//$retorno.="<br />";
	}
	return $retorno;
}

function validaCampoEmailFornProd($array){
	$T173_Id = $array['todosCampos']['T173_Id'];
	$T174_Id = $array['todosCampos']['T174_Id'];
	$sql = "SELECT * FROM T173 WHERE T173_Id='{$T173_Id}'";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	
	$sqlT174 = "SELECT * FROM T174 WHERE T174_Id='{$T174_Id}'";
	$resultT174 = mysql_query($sqlT174);
	$linhaT174 = mysql_fetch_array($resultT174);
	$dataHora = explode(" ",$linhaT174['T174_Hora_Email_Forn']);
	@$hora = $dataHora[1];
	@$data = gCorrigeData($dataHora[0]);
	if($linha['T173_Flag_EMail_Enviado'] == "S"){
		$linhaT174['T174_Valor_Custo_Unitario_Forn'] = gCorrigeNumero($linhaT174['T174_Valor_Custo_Unitario_Forn']);
		return "<span class=\"ui-icon ui-icon-mail-closed\" title=\"Valor Unitário: R\$ {$linhaT174['T174_Valor_Custo_Unitario_Forn']} - % IPI: {$linhaT174['T174_IPI_Forn']} - Data: $data Hora: $hora\"></span>";
	} else {
		return "-<br />";
	}
}

function validaCampoPercentual($array){
	global $g;
	$sql = <<<EOT
	SELECT *, 
	(select T014_Valor_Custo_Unitario from T014 left join T013 on T013_Id=T014_T013_Id where T014_D009_Id=D009_Id and T013_Data_Emissao<T010_Data and T014_Valor_Custo_Unitario>0 and T013_Numero_Nota_Fiscal not like 'A%' order by T014_Id desc limit 1) Preco_Fob 
	FROM T059
	left join D009 on D009_Id=T059_D009_Id
	left join D049 on D049_Id=D009_D049_Id
	left join D082 on D082_Id=D049_D082_Id
	left join D001 on D049_D001_Id=D001_Id
	left join C008 on C008_Id=D001_C008_Id
	left join T010 on T010_Id=T059_T010_Id
	left join D037 on D037_Id=D001_D037_Id
	left join T006 on T006_Id=T059_T006_Id
	where T059_Id='{$array['todosCampos']['T059_Id']}'
	order by substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))),floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20)) 
EOT;
	$result = mysql_query($sql);
	$mT059 = mysql_fetch_array($result);
	//echo $sql."<br /><br />";
	//return "<font color='blue' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/est/est008/content/est008ContentResumo/','&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}') + '&D009_Id=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [600,400]);\">".number_format($Percentual,2,',','.')."</font>";
	
	if (($mT059['T059_Valor_Custo_Unitario']>0) and ($mT059['Preco_Fob']>0))
    {
		$Percentual=number_format((($mT059['Preco_Fob']-$mT059['T059_Valor_Custo_Unitario'])/$mT059['T059_Valor_Custo_Unitario'])*100,2);
		if ($Percentual>0)
		{
            return "<font color='blue' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/est/est008/content/est008ContentGerarOC/','&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}') + '&D009_Id=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [450,350]);\">".number_format($Percentual,2,',','.')."</font>";
		}
		else if ($Percentual<0)
		{
            $Percentual=$Percentual*-1;
            return "<font color='red' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Resumo', '/est/est008/content/est008ContentResumo/','&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}') + '&D009_Id=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [450,350]);\">".number_format($Percentual,2,',','.')."</font>";
		}
		else
		{
            return "";
        }
	}
	else
	{
         return "";
    }
}

function validaCampoDuplicar($array){
	global $g;
	$T059_Id = $array['todosCampos']['T059_Id'];
	return <<<EOT
	<span class="ui-icon ui-icon-circle-plus"  title="Duplicar Registro" onclick="duplicaProdutoCotacao('{$T059_Id}', '{$g['divId']}');"></span>
EOT;
}
function gridFornecedresOcDuplicaProduto($array){
	global $g;
	$T059_Id = $array['todosCampos']['T059_Id'];
	$T174_Id = $array['todosCampos']['T174_Id'];
	return <<<EOT
	<span class="ui-icon ui-icon-circle-plus"  title="Duplicar Registro" onclick="duplicaProdutoCotacaoFornecedor('{$T059_Id}', '{$T174_Id}', '{$g['divId']}');"></span>
EOT;
}

function validaCampoIncluirProd($array){
	
	global $g;
	if($array['todosCampos']['T059_D009_Id'] == "" || $array['todosCampos']['T059_D009_Id']=="0"){
		return <<<EOT
			<span class="ui-icon ui-icon-arrowrefresh-1-s" title="Pré-Cadastrar Produto" 
				onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastrar Produto', '/est/est008/content/est008ContentPreCadastrarProduto/','&T059_Id=' + encodeURIComponent('{$array['todosCampos']['T059_Id']}') + '&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}'), [550,400]);"
			>
			</span>
EOT;
	} else {
		$D001 = <<<EOT
		SELECT D001_Id FROM T059 
		LEFT JOIN D009 ON D009_Id=T059_D009_Id
		LEFT JOIN D049 ON D049_Id=D009_D049_Id
		LEFT JOIN D001 ON D001_Id=D049_D001_Id
		WHERE T059_D009_Id='{$array['todosCampos']['T059_D009_Id']}'
EOT;
		$D001 = mysql_query($D001);
		$mD001 = mysql_fetch_array($D001);
		return <<<EOT
			<span class="ui-icon ui-icon-arrowrefresh-1-s" title="Pré-Cadastrar Produto" 
				onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Produto - ID: ' + '{$mD001['D001_Id']}', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$mD001['D001_Id']}'), [980,550]);"
			>
			</span>
EOT;
	}
}

function validaCampoExcluir($array){
	global $g;
	$T174_Id = $array['todosCampos']['T174_Id'];
	return <<<EOT
	<span class="ui-icon ui-icon-circle-close" title="Excluir" onclick="updateT174_Flag_Excluido('{$T174_Id}', '{$g['divId']}');"></span>
EOT;
}

function validaCheckBoxRetorna($array){
	global $g;	
		$retorno = <<<EOT
		<input type='checkbox' name='T174_Flag_Excluido' title='Gerar OC' onChange='est008CheckboxExcluido("{$array['todosCampos']['T174_Id']}", "{$g['divId']}")' />
EOT;
// 		
		// if ($array['todosCampos']['T174_Flag_Excluido']=='N'){
			// $retorno .= <<<EOT
			// checked = 'checked'
// EOT;
		// }
		// $retorno .= <<<EOT
		 // />
// EOT;
		return $retorno;
}

function validaCampoOC($array){
	$sql = "SELECT T011_Id FROM T011 WHERE T011_T010_Id='{$array['todosCampos']['T173_T010_Id']}'";
	//echo $sql."<br />";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	return "<b>OC{$linha['T011_Id']}</b>";
}

function validaCampoDescProd($array){
	$T059_Id=$array['todosCampos']['T059_Id'];
	$T010_Id=$array['todosCampos']['T010_Id'];
	
	$sql = "SELECT T174_Descricao_Produto FROM T174 left join T173 on T173_Id=T174_T173_Id left join D024 on D024_Id=T173_D024_Id WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$T059_Id}' AND (T174_Valor_Total<>'0.00') ORDER BY T174_Valor_Total ASC";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	//echo "T059 ".$array['todosCampos']['T059_Descricao_Produto']."<br />";
//	echo "T174 ".$linha['T174_Descricao_Produto']."<br /><br />";
	if($array['todosCampos']['T059_Descricao_Produto'] != $linha['T174_Descricao_Produto']){
		if($array['todosCampos']['D001_Flag_Pre_Cadastro'] == 'S'){
			return substr($linha['T174_Descricao_Produto'], 0, 50)." (PRÉ)";
		}else{
			return substr($linha['T174_Descricao_Produto'], 0, 50);
		}
	}else {
		if($array['todosCampos']['D001_Flag_Pre_Cadastro'] == 'S'){
			return substr($array['todosCampos']['T059_Descricao_Produto'], 0, 50)." (PRÉ)";
		}else{
			return substr($array['todosCampos']['T059_Descricao_Produto'], 0, 50);
		}
	}
}

function validaCampoDescricao($array){
	global $g;
	return strtoupper($array['todosCampos']['T174_Descricao_Produto']);
}

function campoeMail3($array){
	global $g, $confUsuario;
	$T010_Id = $array['todosCampos']['T173_T010_Id'];
	$D024_Id = $array['todosCampos']['T173_D024_Id'];
	$parametro = base64_encode($g['empresaAtual'].'|'.$T010_Id.'|'.$D024_Id);	
	return "<a href=\"#\" onclick=\"nova_janela('{$confUsuario['urlRaiz']}est/est008/outros/est008OutrosCotacaoCompraOnline/?p={$parametro}&oneWindow=true','Cotação {$T010_Id}');\" style='cursor:pointer'>Link Cotação {$T010_Id}</a>";
}

?>