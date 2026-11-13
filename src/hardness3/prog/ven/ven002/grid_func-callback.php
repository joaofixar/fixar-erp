<?php
namespace hardness;

function ven002NFDCQuantidadePedido($array) {
    global $g;
    $casas = isset($g['c020']['C020_Casas_Decimais_Quantidade']) ? (int) $g['c020']['C020_Casas_Decimais_Quantidade'] : 2;
    $casas = max(0, min(6, $casas));
    $itemId = (int) $array['todosCampos']['T006_Id'];
    // Colunas livres não entram no SELECT do framework. Usa o campo de banco
    // já selecionado pela grade para calcular o percentual sobre o saldo real.
    $quantidade = number_format((float) $array['todosCampos']['T006_Quantidade'], 6, '.', '');
    $gerada = number_format((float) $array['todosCampos']['Quantidade_Debito'], 6, '.', '');
    return '<input type="text" class="qtdNFDebitoCredito" data-t006-id="' . $itemId
        . '" data-quantidade-pedido="' . $quantidade . '" data-quantidade-gerada="' . $gerada
        . '" data-casas-quantidade="' . $casas . '" value="' . number_format(0, $casas, ',', '.')
        . '" style="width:95px;text-align:right" />';
}


function ven002CampoNFPedido($array) {
	$T005_Id = (int) $array['todosCampos']['T005_Id'];
	$status = (int) $array['todosCampos']['T005_Flag_Status'];
	$cor = $status == 5 ? 'color: gray;' : ($status <= 0 ? 'color: blue;' : '');
	$notas = array();
	$sql = mysql_query("SELECT DISTINCT NF.T007_Id, NF.T007_Numero_Nota_Fiscal, NF.T007_Flag_ACP
		FROM T007 NF
		WHERE IFNULL(NF.T007_Flag_Cancelada,'N') != 'S'
		AND IFNULL(NF.T007_Flag_Inutilizado,'N') != 'S'
		AND (NF.T007_T005_Id = {$T005_Id} OR EXISTS (
			SELECT 1 FROM T008 NFI
			INNER JOIN T006 PI ON PI.T006_Id = NFI.T008_T006_Id
			WHERE NFI.T008_T007_Id = NF.T007_Id AND PI.T006_T005_Id = {$T005_Id}
		)) ORDER BY NF.T007_Id");
	while ($res = mysql_fetch_array($sql)) {
		$T007_Id = (int) $res['T007_Id'];
		$numero = trim($res['T007_Numero_Nota_Fiscal']) !== '' ? $res['T007_Numero_Nota_Fiscal'] : "ID {$T007_Id}";
		if ($res['T007_Flag_ACP'] == '3') {
			$notas[] = "<a href=\"#\" onclick=\"javascript:abre_doc('/ven/ven003/outro/imprimirACP/?ajax=true&T007_Id={$T007_Id}','NF {$T007_Id}');\" style=\"cursor:pointer\">{$numero}</a>";
		} else {
			$notas[] = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$T007_Id}','NF {$T007_Id}');\" style=\"cursor:pointer; {$cor}\">{$numero}</a>";
		}
	}
	return implode(', ', $notas);
}
/**
 * Weiberlan
 * 08/11/2011
 * VEN - VEN002 - Pedidos
 */
function campoExclusao($array){
	global $g;
	if($array['todosCampos']['T005_Flag_Status']>0){
		return "";
	}else{
		//return gExcluirLinha($array, array('T005_Id', $g['divId']));
		return <<<EOT
		<a href="#" onclick="ven002excluirPedido('{$array['todosCampos']['T005_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir pedido"></span>
			</a>
EOT;
	}
}

function campoCancelamento($array){
	global $g;
	if(empty($array['todosCampos']['T005_Flag_Status']) || ($array['todosCampos']['T005_Flag_Status']==9) || ($array['todosCampos']['T005_Flag_Status']==25)){
		return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar Pedido', '/ven/ven002/content/ven002contentCancelarPedido/', '&T005_Id={$array['todosCampos']['T005_Id']}', [450,275]);">
    		<span class="ui-icon ui-icon-circle-close" title="Cancelar pedido"></span>
		</a>
EOT;
	}
}

function campoCancelamentoProduto($array){
	global $g;
	if(empty($array['todosCampos']['T005_Flag_Status']) || ($array['todosCampos']['T005_Flag_Status']==9) || ($array['todosCampos']['T005_Flag_Status']==25)){
		return <<<EOT
		<a href="#" onclick="ven002CancelarProduto('{$array['todosCampos']['T006_Id']}','{$array['todosCampos']['T006_T005_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Cancelar Produto"></span>
			</a>
EOT;
	}
}
 
 function geraImagem($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processos', '/ven/ven002/content/ven002content08/', '&T147_Id={$array['todosCampos']['T147_Id']}', [700,500]);\">
				<li title=\"Detalhes do Serviço\" class=\"ui-state-default ui-corner-all\">			
				<span class=\"ui-icon ui-icon-zoomin\" title='Detalhes'></span>
				</li>
			</a>";
}

function T090_D027_Id_Portador($array) {
	return $array['todosCampos']['D027_Portador'];
}

function campoAbrirDocumento($array){
			$id = $array['todosCampos']['T167_Id'];
			$retorno = "<a href=\"/ven/ven002/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
			return $retorno;
}

function buscaUnidade($array) {
	return $array['todosCampos']['D037_Unidade'];
}	

function ven002_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['T090_Data_Vencimento'];
	return gPegaDiaSemana($data);
}	

function ven002_Detalhes($array) {
	global $g;
	if($array['todosCampos']['D027_Flag_Tipo']=='2'){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Detalhes do Cheque', '/ven/ven002/content/ven002ContentDetalhesCheque/', '&T090_Id={$array['todosCampos']['T090_Id']}', [390,270]);\">
				<li title=\"Detalhes do cheque\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
				</a>";	
	}
}	

function buscaVeiculo($array) {
	$D116=mysql_query("SELECT * from D116 LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_Id=".$array['todosCampos']['T147_D116_Id']);			
	$veiculo=@mysql_fetch_array($D116);
	return $veiculo['D116_Placa'].' - '.$veiculo['D133_Modelo'].' - '.$veiculo['D134_Cor'];
}

function validaCampoExclusaoVen002Grid003($array){
	global $g;
	return <<<EOT
    	<a href="#" onclick="ven002CampoExclusaoServico('{$array['todosCampos']['T147_Id']}', '{$g['divId']}')">
    		<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}

function validaCampoStatus($array){
	if(($array['todosCampos']['T147_Status'] == "P") || ($array['todosCampos']['T147_Status'] == "NULL")){
		return "<font color='red'><b>Pendente</b></font>";
	}else if($array['todosCampos']['T147_Status'] == "E"){
		return "<font color='green'><b>Execução</b></font>";
	}else if($array['todosCampos']['T147_Status'] == "C"){
		return "<font color='blue'><b>Concluído</b></font>";
	}else if($array['todosCampos']['T147_Status'] == "N"){
		return "<b>Cancelado</b>";
	}else if($array['todosCampos']['T147_Status'] == "NULL"){
		return "<b>Cancelado</b>";
	}
}

function statusFinanceiro($array){
	if($array['todosCampos']['T005_Flag_Status_Financeiro'] == "1"){
		return "<font>Pendente</font>";
	}else if($array['todosCampos']['T005_Flag_Status_Financeiro'] == "2"){
		return "<font>Concluído</font>";
	}else if($array['todosCampos']['T005_Flag_Status_Financeiro'] == "1"){
		return "<font>Cancelado</font>";
	}
}

function validaCampoStatusGridPrincipal($array){
	$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$array['todosCampos']['T005_Id']}'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0){
		$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$array['todosCampos']['T005_Id']}' AND (T147_Status='P' OR T147_Status='E')";
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
	$statusOS = $array['todosCampos']['T005_Flag_Status_Servicos'];
	
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
	if ($array['todosCampos']['T138_Operacao']=='P')
		return 'ENTRADA';
	else 	
		return 'DEVOLUÇÕES';	
	
}

function calculaPrecoTotalTroca($array){
	$preco=$array['todosCampos']['T138_Valor_Preco_Unitario'];
	$qte=$array['todosCampos']['T138_Quantidade'];
	return $preco*$qte;
}

function excluirServico($array){
	global $g;
	return <<<EOT
		<span onclick="ven002ExcluirServico('{$array['todosCampos']['T147_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</span>
EOT;
	
}
function cancelarServico($array){
	global $g;
	return <<<EOT
		<span onclick="ven002CancelarServico('{$array['todosCampos']['T147_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Cancelar"></span>
		</span>
EOT;
	
}

function excluirProduto($array){
	global $g;

	$restricao = $g['conf']->restricaoTravar('excluir', 'T006_Id', $array['todosCampos']['T006_Id'], 'T006_T005_Id', $array['todosCampos']['T006_T005_Id']);
	if ($restricao === true) {
		return <<<EOT
			<span onclick="ven002ExcluirProduto('{$array['todosCampos']['T006_Id']}', '{$array['todosCampos']['T006_T005_Id']}', '{$g['divId']}');"> 
				<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</span>
EOT;
	}
	return '';
	
}

function montaGridEstoque($array){
	global $g;
	//Dados do combobox	
	$select = array();
	$T206 = mysql_query("SELECT * FROM T206 LEFT JOIN D169 ON T206_D169_Id = D169_Id LEFT JOIN T006 ON T006_T206_Id=T206_Id  WHERE T206_D009_Id = '{$array['todosCampos']['T006_D009_Id']}' AND D169_Id<>'NULL' AND T006_Id IS NULL");
	$select[] = <<<EOT
	<option value="" selected="selected">PE: {$array['todosCampos']['T006_Quantidade_Estoque']}</option>
EOT;

	while($row = mysql_fetch_array($T206)){

		$res=mysql_query("SELECT T206_Quantidade FROM T206 WHERE T206_Id = '{$row['T206_Id']}'");
		$res=mysql_fetch_array($res);
		$quant=number_format($res[0],2);
		if($quant > 0){
			if($array['todosCampos']['T006_T206_Id'] == $row['T206_Id']){
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}

			$select[] = <<<EOT
				<option value="T206_{$row['T206_Id']}" {$selected}> {$row['D169_Sigla_Tipo_Produto']}: {$quant} - {$row['T206_Descricao_Defeito']}</option>
EOT;
		}
	}
	$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$array['todosCampos']['T006_D009_Id']}' and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 order by T075_Data_Previsao ASC";
	$result = mysql_query($sql);
	while($di = mysql_fetch_array($result)){
		$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['T006_D009_Id']},{$di['T075_Id']})");
		$ret=mysql_fetch_array($ret);
		$quant=number_format($ret[0],2);
		if($quant > 0){
			$data=gCorrigeData($di['T075_Data_Previsao']);		
			//$options.="<option value='{$di['T075_Id']}'>OC:{$di['T075_Id']} - {$data} - {$quant}</option>";
			if ($di['T075_Id']==$array['todosCampos']['T006_T075_Id']){
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
		if ($array['todosCampos']['T006_T075_Id'] > 0){
			$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$array['todosCampos']['T006_D009_Id']}' and T075_Id='{$array['todosCampos']['T006_T075_Id']}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' order by T075_Data_Previsao ASC";
			$result = mysql_query($sql);			
			$di = mysql_fetch_array($result);				
			$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['T006_D009_Id']},{$array['todosCampos']['T006_T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0]+$ret['T006_Quantidade'],2);
			log("Teste");
			$data=gCorrigeData($di['T075_Data_Previsao']);
			$mostrar="MP: {$data} - {$quant}";			
		} else if ($array['todosCampos']['T006_T206_Id'] > 0){
			$T206=mysql_query("SELECT D169_Sigla_Tipo_Produto, T206_Quantidade FROM T206 LEFT JOIN D169 ON D169_Id = T206_D169_Id WHERE T206_Id = '{$array['todosCampos']['T006_T206_Id']}'");
			$mT206=mysql_fetch_array($T206);
			$sigla = $mT206['D169_Sigla_Tipo_Produto'];
			$demonstracao=gCorrigeNumero($mT206['T206_Quantidade'],2);
			$mostrar="{$sigla}: {$demonstracao}";
		} else {
			$mostrar="PE: {$array['todosCampos']['T006_Quantidade_Estoque']}"; 
		}
	} else {
		$mostrar = $array['todosCampos']['T006_Quantidade_Estoque'];
	}	
	//Dados do return
	$result = "<select id=\"{$array['todosCampos']['T006_Id']}\" onchange=\"atualizaT006(this);\" onblur=\"$(this).hide(); $('#T006_{$array['todosCampos']['T006_Id']}').show();\" style=\"display: none;\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
	</select>
	<div id='T006_{$array['todosCampos']['T006_Id']}' onclick="$('#' + '{$array['todosCampos']['T006_Id']}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT006(select){
			var campo = $(select).val();
			campo = campo.split('_');
			var T006_Id = $(select).attr('id');
			$.getJSON('/ven/ven002/grid_func-ajax/atualizaT006/?ajax=true&tabela=' + campo[0] +  '&T006_Id='+ encodeURIComponent(T006_Id) + '&campoId=' + campo[1] + '&callback=?', function(request) {
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
		WHERE D001_Id = (SELECT D001_Id FROM D001 LEFT JOIN D049 ON D001_Id = D049_D001_Id LEFT JOIN D009 ON D049_Id = D009_D049_Id WHERE D009_Id = '{$array['todosCampos']['T006_D009_Id']}');
EOT;
	$result1 = mysql_query($D001);

	$select = array();

	while($mD001 = mysql_fetch_array($result1)){
		$T206 = mysql_query("SELECT * FROM T206 WHERE T206_D009_Id = '{$mD001['D009_Id']}'");

		if($array['todosCampos']['T006_D009_Id_Loja'] == $mD001['D009_Id']){
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
				if($array['todosCampos']['T006_T206_Id'] == $row['T206_Id']){
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
				if ($di['T075_Id']==$array['todosCampos']['T006_T075_Id']){
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

	if($array['todosCampos']['T006_D009_Id_Loja'] > 0){
		$D009_Id = $array['todosCampos']['T006_D009_Id_Loja'];

		$D009 = mysql_query("SELECT D009_Quantidade_Estoque_Liquido, C004_Nome_Abreviado from D009 LEFT JOIN C004 on C004_Id=D009_C004_Id where D009_Id='{$D009_Id}'");
		$mD009 = mysql_fetch_array($D009);
		$Quantidade_Estoque = $mD009['D009_Quantidade_Estoque_Liquido'];
		$C004_Nome_Abreviado = $mD009['C004_Nome_Abreviado'];
		//return "D009> ".$D009_Id . " Quantidade Estoque: ".$Quantidade_Estoque . " C004->".$C004_Nome_Abreviado;
	}else{
		$D009_Id = $array['todosCampos']['T006_D009_Id'];
		$Quantidade_Estoque = $array['todosCampos']['D009_Quantidade_Estoque_Liquido'];
		$C004_Nome_Abreviado = $g['C004']['C004_Nome_Abreviado'];
	}

	//Dados do campo
	if (!empty($select)) {		

		if ($array['todosCampos']['T006_T075_Id'] > 0){

			$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$D009_Id}' and T075_Id='{$array['todosCampos']['T006_T075_Id']}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' order by T075_Data_Previsao ASC";
			$result = mysql_query($sql);			
			$di = mysql_fetch_array($result);				
			$ret=mysql_query("SELECT D009_Quantidade_MP({$D009_Id},{$array['todosCampos']['T006_T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0]+$array['todosCampos']['T006_Quantidade'],2);
			$data=gCorrigeData($di['T075_Data_Previsao']);
			$mostrar="{$C004_Nome_Abreviado} MP: {$data} - {$quant}";			

		} else if ($array['todosCampos']['T006_T206_Id'] > 0){

			$res=mysql_query("SELECT D009_Quantidade_DF({$D009_Id},{$array['todosCampos']['T006_T206_Id']}, 0)");
			$res=mysql_fetch_array($res);
			$defeito=number_format($res[0]+$array['todosCampos']['T006_Quantidade'],2);
			$mostrar="{$C004_Nome_Abreviado} DF: {$defeito}";			

		} else {

			$mostrar="{$C004_Nome_Abreviado} PE: {$Quantidade_Estoque}"; 

		}
	} else {

		$mostrar = $C004_Nome_Abreviado . $Quantidade_Estoque;
	}	

	//Dados do return
	$result = "<select id=\"{$array['todosCampos']['T006_Id']}\" onchange=\"atualizaT006(this);\" onblur=\"$(this).hide(); $('#T006_{$array['todosCampos']['T006_Id']}').show();\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
	</select>
	<div id='T006_{$array['todosCampos']['T006_Id']}' onclick="$('#' + '{$array['todosCampos']['T006_Id']}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#' + '{$array['todosCampos']['T006_Id']}').hide();
		function atualizaT006(select){
			var campo = $(select).val();
			campo = campo.split('_');
			var T006_Id = $(select).attr('id');
			$.getJSON('/ven/ven002/grid_func-ajax/atualizaT006Loja/?ajax=true&tabela=' + campo[0] +  '&T006_Id='+ encodeURIComponent(T006_Id) + '&campoId=' + campo[1] + '&D009_Id_Loja=' + campo[3] + '&callback=?', function(request) {
				if(request.code){
					divRefresh('{$g['divId']}');
				}
			});	
		}
	}</script></div>
EOT;
	return $result;
}


function gridDocumentosArquivo($array){
	global $g;
	$pathinfo = pathinfo($array['todosCampos']['T167_Nome_Arquivo']);
	$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
	$dir = "{$g['pathDados']}pedido";
	$file = $dir . '/' . $array['todosCampos']['T167_Id'] . '.' . $pathinfo['extension'];
	if (file($file)){
		return $array['todosCampos']['T167_Nome_Arquivo'];
	} else {
		return "<font style='color: var(--BGVermelho);font-weight:bold'>".$array['todosCampos']['T167_Nome_Arquivo']." ** arquivo não encontrado **</font>";
	}
}

function T006A_D184_Id_Unidade($array, $param = array('divReload' => false)) {
	global $g;

	$mostrar = "<font style='background:green;color:white;padding:4px;border-radius: .425rem;'>" . $array['todosCampos']['unConversao.D037_Unidade'] . "</font>";
	$mostrar = empty($array['campoAtual']) ? '-' : $mostrar;

	$statusPedido = $array['todosCampos']['T005_Flag_Status'];
	if($statusPedido > 0 && $statusPedido != '' && $statusPedido != 14 && $statusPedido != 15 && $statusPedido != 9 && $statusPedido != 26 && $statusPedido != 22 ){
		return $mostrar;
	}
	
	$options = gGeraSelect('D184', 
					       'D184_Id', 
						   'D037_Unidade', 
						   "LEFT JOIN D037 ON D184_D037_Id = D037_Id WHERE D184_D001_Id = '{$array['todosCampos']['D001_Id']}' ORDER BY D037_Unidade", 
							true, 
							$array['todosCampos']['T006A_D184_Id']);
	$options[] = array(
		'title' => 'optgroup',
	);

	$options = gProcessaSelect($options);

	$idselect = uniqid();
	$iddiv    = uniqid();
	$result   = "<select id=\"{$idselect}\" onchange=\"atualizaUnidade(event, this, '{$array['todosCampos']['T006A_Id']}', '{$array['todosCampos']['T006_Id']}');\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">{$options}";
	$result   .= <<<EOT
				</select>
					<div id="{$iddiv}" onclick="$('#{$idselect}').show().focus(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							function atualizaUnidade(event, select, T006A_Id, T006_Id){
								var campo = $(select).val();
								$.getJSON('/ven/ven002/grid_func-ajax/atualizarUnidade/?ajax=true&T006A_Id='+ encodeURIComponent(T006A_Id) + '&D184_Id='+ encodeURIComponent(campo) + '&callback=?', function(request) {
									if(request.code){
										divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id');
										divRefresh('{$param['divReload']}');

									}
								});									
							}
					}</script></div>
EOT;

	return $result;

}

?>
