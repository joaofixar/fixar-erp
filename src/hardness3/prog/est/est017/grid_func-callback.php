<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * est - est016 - Pedidos
 */
function campoExclusao($array){
	global $g;
	if($array['todosCampos']['T224_Flag_Status']!=999){
		return "";
	}else{
		//return gExcluirLinha($array, array('T224_Id', $g['divId']));
		return <<<EOT
		<a href="#" onclick="est016excluirPedido('{$array['todosCampos']['T224_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir pedido"></span>
			</a>
EOT;
	}
}

function campoCancelamento($array){
	global $g;
	if($array['todosCampos']['T224_Flag_Status'] == '' || $array['todosCampos']['T224_Flag_Status'] == '0' || $array['todosCampos']['T224_Flag_Status'] == '2' || $array['todosCampos']['T224_Flag_Status'] == '3'){
		return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar Ordem de compra', '/est/est016/content/est016contentCancelarPedido/', '&T224_Id={$array['todosCampos']['T224_Id']}', [350,200]);">
    		<span class="ui-icon ui-icon-circle-close" title="Cancelar pedido"></span>
		</a>
EOT;
	}
}

function campoCancelamentoObs($array){
	global $g;
	if($array['todosCampos']['T224_Flag_Status']!=999){
		return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar Ordem de compra', '/est/est016/content/est016contentCancelarOCObs/', '&T224_Id={$array['todosCampos']['T224_Id']}', [350,200]);">
    		<span class="ui-icon ui-icon-circle-close" title="Cancelar OC"></span>
		</a>
EOT;
	}
}
 
 function geraImagem($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processos', '/est/est016/content/est016content08/', '&T230_Id={$array['todosCampos']['T230_Id']}', [700,500]);\">
				<li title=\"Detalhes do Serviço\" class=\"ui-state-default ui-corner-all\">			
				<span class=\"ui-icon ui-icon-zoomin\" title='Detalhes'></span>
				</li>
			</a>";
}

function T226_D031_Id_Portador($array) {
	return $array['todosCampos']['D031_Portador'];
}

function campoAbrirDocumento($array){
			$id = $array['todosCampos']['T227_Id'];
			$retorno = "<a href=\"/est/est016/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
			return $retorno;
}

function buscaUnidade($array) {
	return $array['todosCampos']['D037_Unidade'];
}	

function est016_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['T226_Data_estcimento'];
	return gPegaDiaSemana($data);
}	

function est016_Detalhes($array) {
	global $g;
	if($array['todosCampos']['D027_Flag_Tipo']=='2'){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Detalhes do Cheque', '/est/est016/content/est016ContentDetalhesCheque/', '&T226_Id={$array['todosCampos']['T226_Id']}', [390,270]);\">
				<li title=\"Detalhes do cheque\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
				</a>";	
	}
}	

function buscaVeiculo($array) {
	$D116=mysql_query("SELECT * from D116 LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_Id=".$array['todosCampos']['T230_D116_Id']);			
	$veiculo=@mysql_fetch_array($D116);
	return $veiculo['D116_Placa'].' - '.$veiculo['D133_Modelo'].' - '.$veiculo['D134_Cor'];
}

function validaCampoExclusaoest016Grid003($array){
	global $g;
	return <<<EOT
    	<a href="#" onclick="est016CampoExclusaoServico('{$array['todosCampos']['T230_Id']}', '{$g['divId']}')">
    		<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}

function validaCampoStatus($array){
	if(($array['todosCampos']['T230_Status'] == "P") || ($array['todosCampos']['T230_Status'] == "NULL")){
		return "<font color='red'><b>Pendente</b></font>";
	}else if($array['todosCampos']['T230_Status'] == "E"){
		return "<font color='green'><b>Execução</b></font>";
	}else if($array['todosCampos']['T230_Status'] == "C"){
		return "<font color='blue'><b>Concluído</b></font>";
	}else if($array['todosCampos']['T230_Status'] == "N"){
		return "<b>Cancelado</b>";
	}else if($array['todosCampos']['T230_Status'] == "NULL"){
		return "<b>Cancelado</b>";
	}
}

function statusFinanceiro($array){
	if($array['todosCampos']['T224_Flag_Status_Financeiro'] == "1"){
		return "<font>Pendente</font>";
	}else if($array['todosCampos']['T224_Flag_Status_Financeiro'] == "2"){
		return "<font>Concluído</font>";
	}else if($array['todosCampos']['T224_Flag_Status_Financeiro'] == "1"){
		return "<font>Cancelado</font>";
	}
}

function validaCampoStatusGridPrincipal($array){
	$sql = "SELECT * FROM T230 WHERE T230_T224_Id='{$array['todosCampos']['T224_Id']}'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0){
		$sql = "SELECT * FROM T230 WHERE T230_T224_Id='{$array['todosCampos']['T224_Id']}' AND (T230_Status='P' OR T230_Status='E')";
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			return "<font color='red'><b>Serviços Pendentes</b></font>";
		}else{
			return "<font color='blue'><b>Serviços Concluídos</b></font>";
		}
	}else{
		return "-";
	}
}

function validaCampoStatusOS($array){
	$statusOS = $array['todosCampos']['T224_Flag_Status_Servicos'];
	
	if($statusOS == "1"){
		return "<font>O.S. Pendente</font>";
	}else if($statusOS == "2"){
		return "<font>O.S. Concluída</font>";
	}else if($statusOS == "3"){
		return "<font>O.S. Cancelada</font>";
	}
}

function validaCampoVeiculo($array){
	$D116=mysql_query("SELECT * from D116 LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_Id=".$array['todosCampos']['D116_Id']);			
	$veiculo=@mysql_fetch_array($D116);
	return $veiculo['D116_Placa'].' - '.$veiculo['D133_Modelo'].' - '.$veiculo['D134_Cor'];	
}

function retornaOperacaoTroca($array){
	if ($array['todosCampos']['T222_Operacao']=='P')
		return 'PARTE PAGAMENTO';
	else 	
		return 'TROCA';	
	
}

function calculaPrecoTotalTroca($array){
	$preco=$array['todosCampos']['T222_Valor_Preco_Unitario'];
	$qte=$array['todosCampos']['T222_Quantidade'];
	return $preco*$qte;
}

function excluirServico($array){
	global $g;
	return <<<EOT
		<span onclick="est016ExcluirServico('{$array['todosCampos']['T230_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</span>
EOT;
	
}

function excluirProduto($array){
	global $g;
	$T224_Flag_Status = $array['todosCampos']['T224_Flag_Status'];
	if($T224_Flag_Status == '' || $T224_Flag_Status == '0' || $T224_Flag_Status == '2' || $T224_Flag_Status == '3'){
		return <<<EOT
			<a href="#" onclick="excluirProdutoOC('{$array['todosCampos']['T225_Id']}', '{$g['divId']}')">
				<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	} else {
		return '';
	}
}

function montaGridEstoque($array){
	global $g;
	//Dados do combobox	
	$select = array();
	$T206 = mysql_query("SELECT * FROM T206 WHERE T206_D009_Id = '{$array['todosCampos']['T225_D009_Id']}'");
	$select[] = <<<EOT
	<option value="" selected="selected">PE: {$array['todosCampos']['T225_Quantidade_Estoque']}</option>
EOT;
	while($row = mysql_fetch_array($T206)){

		if($row['T206_Flag_Tipo']==1){

			$res=mysql_query("SELECT D009_Quantidade_DF({$array['todosCampos']['T225_D009_Id']},{$row['T206_Id']}, 0)");
			$res=mysql_fetch_array($res);
			$quant=number_format($res[0],2);
			if($quant > 0){
				if($array['todosCampos']['T225_T206_Id'] == $row['T206_Id']){
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}

				$select[] = <<<EOT
					<option value="T206_{$row['T206_Id']}" {$selected}> DF: {$quant} - {$row['T206_Descricao_Defeito']}</option>
EOT;
			}
			
		}else{
			$res=mysql_query("SELECT D009_Quantidade_DM({$array['todosCampos']['T225_D009_Id']},{$row['T206_Id']})");
			$res=mysql_fetch_array($res);
			$quant=number_format($res[0],2);
			if($quant > 0){
				if($array['todosCampos']['T225_T206_Id'] == $row['T206_Id']){
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}

				$select[] = <<<EOT
					<option value="T206_{$row['T206_Id']}" {$selected}> DM: {$quant} - {$row['T206_Descricao_Defeito']}</option>
EOT;
			}
		}
	}	
	$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$array['todosCampos']['T225_D009_Id']}' and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 order by T075_Data_Previsao ASC";
	$result = mysql_query($sql);
	while($di = mysql_fetch_array($result)){
		$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['T225_D009_Id']},{$di['T075_Id']})");
		$ret=mysql_fetch_array($ret);
		$quant=number_format($ret[0],2);
		if($quant > 0){
			$data=gCorrigeData($di['T075_Data_Previsao']);		
			//$options.="<option value='{$di['T075_Id']}'>OC:{$di['T075_Id']} - {$data} - {$quant}</option>";
			if ($di['T075_Id']==$array['todosCampos']['T225_T075_Id']){
				$selected = 'selected="selected"';
			}else{
				$selected = '';
			}	
			$select[] = <<<EOT
				<option value="T075_{$di['T075_Id']}" {$selected}> MP: {$data} - {$quant}</option>
EOT;
		}
	}
	//Dados do campo
	if (!empty($select)) {			
		if ($array['todosCampos']['T225_T075_Id'] > 0){
			$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$array['todosCampos']['T225_D009_Id']}' and T075_Id='{$array['todosCampos']['T225_T075_Id']}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' order by T075_Data_Previsao ASC";
			$result = mysql_query($sql);			
			$di = mysql_fetch_array($result);				
			$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['T225_D009_Id']},{$array['todosCampos']['T225_T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0]+$array['todosCampos']['T225_Quantidade'],2);
			$data=gCorrigeData($di['T075_Data_Previsao']);
			$mostrar="MP: {$data} - {$quant}";			
		} else if ($array['todosCampos']['T225_T206_Id'] > 0){

			$T206=mysql_query("SELECT T206_Flag_Tipo FROM T206 WHERE T206_Id='{$array['todosCampos']['T219_T206_Id']}'");
			$mT206=mysql_fetch_array($T206);

			if($mT206['T206_Flag_Tipo']==1){

				$res=mysql_query("SELECT D009_Quantidade_DF({$array['todosCampos']['T225_D009_Id']},{$array['todosCampos']['T225_T206_Id']}, 0)");
				$res=mysql_fetch_array($res);
				$defeito=number_format($res[0]+$array['todosCampos']['T225_Quantidade'],2);
				$mostrar="DF: {$defeito}";			
			
			}else{
				$res=mysql_query("SELECT D009_Quantidade_DM({$array['todosCampos']['T225_D009_Id']},{$array['todosCampos']['T225_T206_Id']})");
				$res=mysql_fetch_array($res);
				$demonstracao=number_format($res[0]+$array['todosCampos']['T225_Quantidade'],2);
				$mostrar="DM: {$demonstracao}";
			}

		} else {
			$mostrar="PE: {$array['todosCampos']['T225_Quantidade_Estoque']}"; 
		}
	} else {
		$mostrar = $array['todosCampos']['T225_Quantidade_Estoque'];
	}	
	//Dados do return
	$result = "<select id=\"{$array['todosCampos']['T225_Id']}\" onchange=\"atualizaT225(this);\" onblur=\"$(this).hide(); $('#T225_{$array['todosCampos']['T225_Id']}').show();\" style=\"display: none;\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
	</select>
	<div id='T225_{$array['todosCampos']['T225_Id']}' onclick="$('#' + '{$array['todosCampos']['T225_Id']}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT225(select){
			var campo = $(select).val();
			campo = campo.split('_');
			var T225_Id = $(select).attr('id');
			$.getJSON('/est/est016/grid_func-ajax/atualizaT225/?ajax=true&tabela=' + campo[0] +  '&T225_Id='+ encodeURIComponent(T225_Id) + '&campoId=' + campo[1] + '&callback=?', function(request) {
				if(request.code){
					divRefresh('{$g['divId']}');
				}
			});	
		}
	}</script></div>
EOT;
	return $result;
}

function montaGridEstoqueLoja($array){
	global $g;
	//Dados do combobox	

	$D001=<<<EOT
		SELECT D009_Id, D001_Id, D009_C004_Id, C004_Nome_Abreviado, D009_Quantidade_Estoque_Liquido FROM D009 
		LEFT JOIN D049 ON D049_Id=D009_D049_Id 
		LEFT JOIN D001 ON D001_Id=D049_D001_Id
		LEFT JOIN C004 ON C004_Id=D009_C004_Id
		WHERE D001_Id = (SELECT D001_Id FROM D001 LEFT JOIN D049 ON D001_Id = D049_D001_Id LEFT JOIN D009 ON D049_Id = D009_D049_Id WHERE D009_Id = '{$array['todosCampos']['T225_D009_Id']}');
EOT;
	$result1 = mysql_query($D001);

	$select = array();

	while($mD001 = mysql_fetch_array($result1)){
		$T206 = mysql_query("SELECT * FROM T206 WHERE T206_D009_Id = '{$mD001['D009_Id']}'");

		if($array['todosCampos']['T225_D009_Id_Loja'] == $mD001['D009_Id']){
			$selected = 'selected="selected"';
		}else{
			$selected='';
		}

		if($mD001['D009_C004_Id']!=$g['empresaAtual']){
			$select[] = <<<EOT
				<option value="__D009Loja_{$mD001['D009_Id']}" {$selected}>{$mD001['C004_Nome_Abreviado']} PE: {$mD001['D009_Quantidade_Estoque_Liquido']}</option>
EOT;
		}else{
			$select[] = <<<EOT
				<option value="" {$selected}>{$mD001['C004_Nome_Abreviado']} PE: {$mD001['D009_Quantidade_Estoque_Liquido']}</option>
EOT;
		}

		while($row = mysql_fetch_array($T206)){
			$res=mysql_query("SELECT D009_Quantidade_DF({$mD001['D009_Id']},{$row['T206_Id']}, 0)");
			$res=mysql_fetch_array($res);
			$quant=number_format($res[0],2);
			if($quant > 0){
				if($array['todosCampos']['T225_T206_Id'] == $row['T206_Id']){
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
							
				$select[] = <<<EOT
					<option value="T206_{$row['T206_Id']}_D009Loja_{$mD001['D009_Id']}" {$selected}>{$mD001['C004_Nome_Abreviado']} DF: {$quant} - {$row['T206_Descricao_Defeito']}</option>
EOT;
			}
		}	
		$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$mD001['D009_Id']}' and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 order by T075_Data_Previsao ASC";
		$result = mysql_query($sql);
		while($di = mysql_fetch_array($result)){
			$ret=mysql_query("SELECT D009_Quantidade_MP({$mD001['D009_Id']},{$di['T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0],2);
			if($quant > 0){
				$data=gCorrigeData($di['T075_Data_Previsao']);		
				//$options.="<option value='{$di['T075_Id']}'>OC:{$di['T075_Id']} - {$data} - {$quant}</option>";
				if ($di['T075_Id']==$array['todosCampos']['T225_T075_Id']){
					$selected = 'selected="selected"';
				}else{
					$selected = '';
				}	
				$select[] = <<<EOT
					<option value="T075_{$di['T075_Id']}_D009Loja_{$mD001['D009_Id']}" {$selected}>{$mD001['C004_Nome_Abreviado']} MP: {$data} - {$quant}</option>
EOT;
			}
		}
	}// END WHILE

	if($array['todosCampos']['T225_D009_Id_Loja'] > 0){
		$D009_Id = $array['todosCampos']['T225_D009_Id_Loja'];

		$D009 = mysql_query("SELECT D009_Quantidade_Estoque_Liquido, C004_Nome_Abreviado from D009 LEFT JOIN C004 on C004_Id=D009_C004_Id where D009_Id='{$D009_Id}'");
		$mD009 = mysql_fetch_array($D009);
		$Quantidade_Estoque = $mD009['D009_Quantidade_Estoque_Liquido'];
		$C004_Nome_Abreviado = $mD009['C004_Nome_Abreviado'];
		//return "D009> ".$D009_Id . " Quantidade Estoque: ".$Quantidade_Estoque . " C004->".$C004_Nome_Abreviado;
	}else{
		$D009_Id = $array['todosCampos']['T225_D009_Id'];
		$Quantidade_Estoque = $array['todosCampos']['D009_Quantidade_Estoque_Liquido'];
		$C004_Nome_Abreviado = $g['C004']['C004_Nome_Abreviado'];
	}

	//Dados do campo
	if (!empty($select)) {		

		if ($array['todosCampos']['T225_T075_Id'] > 0){

			$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$D009_Id}' and T075_Id='{$array['todosCampos']['T225_T075_Id']}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' order by T075_Data_Previsao ASC";
			$result = mysql_query($sql);			
			$di = mysql_fetch_array($result);				
			$ret=mysql_query("SELECT D009_Quantidade_MP({$D009_Id},{$array['todosCampos']['T225_T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0]+$array['todosCampos']['T225_Quantidade'],2);
			$data=gCorrigeData($di['T075_Data_Previsao']);
			$mostrar="{$C004_Nome_Abreviado} MP: {$data} - {$quant}";			

		} else if ($array['todosCampos']['T225_T206_Id'] > 0){

			$res=mysql_query("SELECT D009_Quantidade_DF({$D009_Id},{$array['todosCampos']['T225_T206_Id']}, 0)");
			$res=mysql_fetch_array($res);
			$defeito=number_format($res[0]+$array['todosCampos']['T225_Quantidade'],2);
			$mostrar="{$C004_Nome_Abreviado} DF: {$defeito}";			

		} else {

			$mostrar="{$C004_Nome_Abreviado} PE: {$Quantidade_Estoque}"; 

		}
	} else {

		$mostrar = $C004_Nome_Abreviado . $Quantidade_Estoque;
	}	

	//Dados do return
	$result = "<select id=\"{$array['todosCampos']['T225_Id']}\" onchange=\"atualizaT225(this);\" onblur=\"$(this).hide(); $('#T225_{$array['todosCampos']['T225_Id']}').show();\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
	</select>
	<div id='T225_{$array['todosCampos']['T225_Id']}' onclick="$('#' + '{$array['todosCampos']['T225_Id']}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#' + '{$array['todosCampos']['T225_Id']}').hide();
		function atualizaT225(select){
			var campo = $(select).val();
			campo = campo.split('_');
			var T225_Id = $(select).attr('id');
			$.getJSON('/est/est016/grid_func-ajax/atualizaT225Loja/?ajax=true&tabela=' + campo[0] +  '&T225_Id='+ encodeURIComponent(T225_Id) + '&campoId=' + campo[1] + '&D009_Id_Loja=' + campo[3] + '&callback=?', function(request) {
				if(request.code){
					divRefresh('{$g['divId']}');
				}
			});	
		}
	}</script></div>
EOT;
	return $result;
}

function est016NaoLiberar($array) {
	global $g;
	return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Não Liberar', '/est/est016/content/est016ContentNaoLiberar/', '&acaoId={$array['todosCampos']['T224_Id']}', [400,300]); return false;">
				<span class="ui-icon ui-icon-closethick" title="Reprovar"></span>
			</a>
EOT;

}
function est016Liberar($array) {
	global $g;
	return <<<EOT
			<a href="#" onclick="est016AprovarPedido('{$array['todosCampos']['T224_Id']}', '{$g['divId']}');">
				<span class="ui-icon ui-icon-check" title="Aprovar"></span>
			</a>
EOT;

}
function D006_Codigo_CFOP($array) {
	$valor = $array['campoAtual'];
	//log("{$array['campoAtual']} != {$array['todosCampos']['D6NF_Codigo']}");
	if ($array['campoAtual'] != $array['todosCampos']['D6NF_Codigo']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}
function T014_Classificacao_Fiscal($array) {
	$valor = $array['campoAtual'];
	if ($array['campoAtual'] != $array['todosCampos']['T014_Classificacao_Fiscal']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_Total_Custo_Recebida($array) {
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_Total_Custo']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_Total_Preco_Recebida($array) {
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_Total_Custo']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}


function T014_Valor_ICMS_Recebida($array) {
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_ICMS']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_IPI_Recebida($array) {
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_IPI']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_ICMS_Substituicao_Recebida($array) {
	$valor = gCorrigeNumero($array['campoAtual']);
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_ICMS_Substituicao']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function T014_Valor_Custo_Unitario_Recebida($array) {
	$valor = number_format($array['campoAtual'],6,',','.');
	if ($array['campoAtual'] != $array['todosCampos']['T014_Valor_Custo_Final_Unitario']) {
		return '<span style="font-weight: bold; color: var(--BGVermelho);">' . $valor . '</span>';
	} else {
		return '<span style="">' . $valor . '</span>';
	}
}

function est017DescricaoProduto($array){
	global $g;
	if($array['todosCampos']['D049_Flag_Tipo'] == 'PD'){
		$retorno = $array['campoAtual'];
		$sqlT261 = mysql_query("SELECT D001_Codigo_Produto, 
		                               D001_Descricao_Produto, 
									   T261_Quantidade
								  FROM T261 
							 LEFT JOIN D001 ON D001_Id = T261_D001_Id_Composicao 
							 LEFT JOIN D049 ON D001_Id = D049_D001_Id
							 LEFT JOIN D009 ON D049_Id = D009_D049_Id
							 LEFT JOIN D037 ON D037_Id = D001_D037_Id
							     WHERE T261_T006_Id = '{$array['todosCampos']['T006_Id']}' 
								   AND D009_C004_Id='{$g['empresaAtual']}'");
		if(mysql_num_rows($sqlT261) > 0){
			$retorno .= "<br><table cellspacing=0 cellpadding=0 style=\"border: 1px solid gray; border-bottom: 0px; font-size: 11px; font-weight: normal;\">";
			while($rowT261 = mysql_fetch_array($sqlT261)){
				$quantidadeTotal = gCorrigeNumero($rowT261['T261_Quantidade']*$array['todosCampos']['Falta'], 5);
				$rowT261['T261_Quantidade'] = gCorrigeNumero($rowT261['T261_Quantidade'],5);
				$rowT261['D001_Descricao_Produto'] = $rowT261['D001_Descricao_Produto'];
				$retorno .= <<<EOT
					<tr>
						<td style="border-bottom: 1px solid gray;">{$rowT261['D001_Codigo_Produto']}</td>
						<td style="border-bottom: 1px solid gray; border-left: 1px solid gray;">{$rowT261['D001_Descricao_Produto']}</td>
						<td style="border-bottom: 1px solid gray; border-left: 1px solid gray;">{$rowT261['T261_Quantidade']}</td>
						<td style="border-bottom: 1px solid gray; border-left: 1px solid gray;">{$quantidadeTotal}</td>
					</tr>
EOT;
			}
			$retorno .= "</table>";
		} else {
			$retorno .= "<br><span style=\"color: var(--BGVermelho);\">Falta engenharia do produto</span>";
		}
		return $retorno;
	} else {
		return $array['campoAtual'];
	}
}

function est017CampoCancelarOrdemProducao($array){
	global $g;

	$T255_Id = $array['todosCampos']['T255_Id'];
	$status = isset($array['todosCampos']['T255_Flag_Status']) ? $array['todosCampos']['T255_Flag_Status'] : '0';

	if ($status != '99' && $status != '98') {
		return <<<EOT
			<a href="#" onclick="est017CancelarOrdemProducao('{$T255_Id}', '{$g['divId']}'); return false;">
				<span class="ui-icon ui-icon-closethick" title="Cancelar"></span>
			</a>
EOT;
	}

	return '';
}

function est017CampoExcluirOrdemProducao($array){
	global $g;

	$T255_Id = $array['todosCampos']['T255_Id'];
	$status = isset($array['todosCampos']['T255_Flag_Status']) ? $array['todosCampos']['T255_Flag_Status'] : '0';

	if ($status != '99') {
		return <<<EOT
			<a href="#" onclick="est017ExcluirOrdemProducao('{$T255_Id}', '{$g['divId']}'); return false;">
				<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}

	return '';
}



?>
