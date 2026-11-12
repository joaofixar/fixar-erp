<?php
namespace hardness;
/**
 * est - est015 - Cadastro de Bens
 */

function campoExclusao($array) {
	global $g;
    $resultado=mysql_query("select T224_Id from T224 where T224_T218_Id='{$array['todosCampos']['T218_Id']}' and T224_Flag_Status!=8");
    $row=mysql_fetch_array($resultado);
    if(empty($row['T224_Id'])){	
		if($array['todosCampos']['T218_Flag_Perdido'] != 'S'){
			return <<<EOT
				<a href="#" onclick="est015CampoExclusao('{$array['todosCampos']['T218_Id']}','{$g['divId']}')">
	    			<span class="ui-icon ui-icon-circle-close" title="Excluir orçamento"></span>
				</a>
EOT;
		}
	}else{
		return "";
	}
}
function validaCampoComprar($array){
	global $g,$r_acaoId;
	if ($array['todosCampos']['T225_T224_Id'] <= 0){
		$retorno = "<input onclick=\"est015CheckboxComprar(this, '{$array['todosCampos']['T219_Id']}', '{$g['divId']}')\" type='checkbox' id='T219_Flag_Comprar' title='Comprar'";
		if ($array['todosCampos']['T219_Flag_Comprar']=='S'){
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	} else {
		return $array['todosCampos']['T225_T224_Id'];
	}
}
function campoCancelamento($array) {
	global $g;
    $resultado=mysql_query("select T224_Id from T224 where T224_T218_Id='{$array['todosCampos']['T218_Id']}' and T224_Flag_Status!=8");
    $row=mysql_fetch_array($resultado);
    if(empty($row['T224_Id'])){	
		if($array['todosCampos']['T218_Flag_Perdido'] != 'S'){
			return <<<EOT
				<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Motivo Cancelamento', '/est/est015/content/est015ContentCancelamentoOrcamento/', '&acaoId={$array['todosCampos']['T218_Id']}', [400,300]); return false;">
					<span class="ui-icon ui-icon-circle-close" title="Cancelar"></span>
				</a>
EOT;
		}
	}else{
		return "";
	}
}
function campoStatus($array) {
	global $g;
	if($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'P'){
		return "Pendente";
	}
	elseif($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'F' && $array['todosCampos']['T218_Flag_Notificacao'] == 1 && $array['todosCampos']['T218_C007_Id_Vendedor_Interno'] == $g['usuarioAtual']) {
		return "<strong>Finalizado Recentemente</strong>";
	}
	elseif($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'F'){
		return "Finalizado";
	}
	elseif($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'C'){
		return "Cancelado";
	}
	elseif($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'V'){
		return "estcido";
	}
	elseif($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'A'){
		return "<font color=\"green\">Aprovado</font>";
	}
	elseif($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'N'){
		return "<font color=\"red\">Negado - ".$array['todosCampos']['T218_Liberou_Alcada_Observacao_Nao_Liberado']."</font>";
	}
	elseif($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'L'){
		return "Análise";
	}
}

function geraImagem($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro', '/est/est015/content/est015ContentEditarServico/', '&acaoId={$array['todosCampos']['T223_Id']}', [700,500]);\">
				<li title=\"Detalhes do Serviço\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
			</a>";
}

function campoPedidos($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedido', '/est/est002/content/est002content02/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['Pedido']}') + '&table=T224', [900,550]); return false;\">
					{$array['campoAtual']}
			</a>";
}

function validaCampoPedido($array){
	global $g;
    if($array['todosCampos']['T218_Flag_Perdido'] == 'S'){
		// busca o motivo do cancelamento 
		$resultado=mysql_query("select * from T218 where T218_Id='{$array['todosCampos']['T218_Id']}'");
    	$row=mysql_fetch_array($resultado);
		$resultado=mysql_query("select * from D047 where D047_Id='{$row['T218_D047_Id']}'");
    	$row=mysql_fetch_array($resultado);    		
		//return "<font color='red'><b>".$row['D047_Descricao_Motivo'].'</b> - '.$array['todosCampos']['T218_Observacao_Orcamento_Perdido']."</font>";
		return "<font color='red'>".$array['todosCampos']['T218_Observacao_Orcamento_Perdido']."</font>";
	}else{
		if ($array['todosCampos']['T224_Id']>0 and $array['todosCampos']['T224_Flag_Status']==13) {
		   	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordem de Compra - ID: ' + id, '/est/est016/content/est016content02/', '&naoAtualizarLinha=true&acaoId=' + encodeURIComponent({$array['todosCampos']['T224_Id']}), [950,615]);\"> <font style='font-weight:normal;color:blue'>".$array['todosCampos']['Pedido']."</font></a>";
		} else if ($array['todosCampos']['T224_Id']>0 and $array['todosCampos']['T224_Flag_Status']==14) {
		   	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordem de Compra - ID: ' + id, '/est/est016/content/est016content02/', '&naoAtualizarLinha=true&acaoId=' + encodeURIComponent({$array['todosCampos']['T224_Id']}), [950,615]);\"> <font style='font-weight:normal;color:green'>".$array['todosCampos']['Pedido']."</font></a>";
		} else if ($array['todosCampos']['T224_Id']>0 and $array['todosCampos']['T224_Flag_Status']==15) {
		   	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordem de Compra - ID: ' + id, '/est/est016/content/est016content02/', '&naoAtualizarLinha=true&acaoId=' + encodeURIComponent({$array['todosCampos']['T224_Id']}), [950,615]);\"> <font style='font-weight:normal;color: var(--BGVermelho)'>".$array['todosCampos']['Pedido']."</font></a>";
		} else if ($array['todosCampos']['T224_Id']>0 and $array['todosCampos']['T224_Flag_Status']!=5) {
		   	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordem de Compra - ID: ' + id, '/est/est016/content/est016content02/', '&naoAtualizarLinha=true&acaoId=' + encodeURIComponent({$array['todosCampos']['T224_Id']}), [950,615]);\"> <font style='font-weight:normal'>".$array['todosCampos']['Pedido']."</font></a>";
		} else {
		   	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordem de Compra - ID: ' + id, '/est/est016/content/est016content02/', '&naoAtualizarLinha=true&acaoId=' + encodeURIComponent({$array['todosCampos']['T224_Id']}), [950,615]);\">{$array['todosCampos']['Pedido']}</a>";
		}
	}
}

function buscaUnidade($array) {
	return $array['todosCampos']['D037_Unidade'];
}

function buscaVeiculo($array) {
	$D116=mysql_query("SELECT * from D116 LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_Id=".$array['todosCampos']['T223_D116_Id']);			
	$veiculo=@mysql_fetch_array($D116);
	return $veiculo['D116_Placa'].' - '.$veiculo['D133_Modelo'].' - '.$veiculo['D134_Cor'];
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

function est015_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['T220_Data_Vencimento'];
	return gPegaDiaSemana($data);
}	

function est015_Detalhes($array) {
	global $g;
	if($array['todosCampos']['D027_Flag_Tipo']=='2'){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Detalhes do Cheque', '/est/est002/content/est002ContentDetalhesCheque/', '&T226_Id={$array['todosCampos']['T226_Id']}', [390,270]);\">
				<li title=\"Detalhes do cheque\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
				</a>";	
	}
}	

function T220_D031_Id_Portador($array) {
	return $array['todosCampos']['D031_Portador'];
}

function validaCampoStatusServicos($array){
	$sql = "SELECT * FROM T230 WHERE T230_T223_Id='{$array['todosCampos']['T223_Id']}'";
	$result = mysql_query($sql);
	$retorno = "";
	if(mysql_num_rows($result) > 0){
		$linha = mysql_fetch_array($result);
		
		if(($linha['T230_Status'] == "P") || ($linha['T230_Status'] == "NULL")){
			return "<font color='red'><b>Pendente</b></font>";
		}else if($linha['T230_Status'] == "E"){
			return "<font color='green'><b>Execução</b></font>";
		}else if($linha['T230_Status'] == "C"){
			return "<font color='blue'><b>Concluído</b></font>";
		}else if($linha['T230_Status'] == "N"){
			return "<b>Cancelado</b>";
		}else if($linha['T230_Status'] == "NULL"){
			return "<b>Cancelado</b>";
		}
	}
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
		<span onclick="est015ExcluirServico('{$array['todosCampos']['T223_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</span>
EOT;
	
}

function excluirProduto($array){
	global $g;
	return <<<EOT
		<span onclick="est015ExcluirProduto('{$array['todosCampos']['T219_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</span>
EOT;
	
}

function campoAbrirDocumento($array){
			$id = $array['todosCampos']['T221_Id'];
			$retorno = "<a href=\"/est/est015/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
			return $retorno;
}
function campoLinkProduto($array){
			$sql = mysql_query("SELECT D001_Id FROM T219 LEFT JOIN D009 ON D009_Id=T219_D009_Id 
					LEFT JOIN D049 ON D049_Id=D009_D049_Id
					LEFT JOIN D001 ON D001_Id=D049_D001_Id
					LEFT JOIN T144 ON T144_D001_Id=D001_Id WHERE T219_Id='{$array['todosCampos']['T219_Id']}'");
			$row = mysql_fetch_array($sql);
			$idProduto = $row['D001_Id'];
			
			$sql = mysql_query("SELECT T144_Url FROM T144 WHERE T144_D001_Id='{$idProduto}'");
			$row = mysql_fetch_array($sql);
			$url = $row['T144_Url'];
			
			if(!empty($url)){
				$retorno = "<a href=\"{$url}\" target=\"_blank\">Ver Produto</a>"; 
			}else{
				$retorno = "Não cadastrado"; 
			}
			return $retorno;
}

function montaGridEstoque($array){
	global $g;
	//Dados do combobox	
	$select = array();
	$T206 = mysql_query("SELECT * FROM T206 WHERE T206_D009_Id = '{$array['todosCampos']['T219_D009_Id']}'");
	
	$select[] = <<<EOT
	<option value="" selected="selected">PE: {$array['todosCampos']['D009_Quantidade_Estoque_Liquido']}</option>
EOT;
	while($row = mysql_fetch_array($T206)){

		// Verificando se é tipo  DEFEITO ou DEMONSTRAÇÃO
		if ($row['T206_Flag_Tipo']==1){

			$res=mysql_query("SELECT D009_Quantidade_DF({$array['todosCampos']['T219_D009_Id']},{$row['T206_Id']}, 0)");
			//return $row['T206_Id'];
			$res=mysql_fetch_array($res);
			$quant=number_format($res[0],2);
			if($quant > 0){
				if($array['todosCampos']['T219_T206_Id'] == $row['T206_Id']){
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
							
				$select[] = <<<EOT
					<option value="T206_{$row['T206_Id']}" {$selected}> DF: {$quant} - {$row['T206_Descricao_Defeito']}</option>
EOT;
			}

		}else{

			$res=mysql_query("SELECT D009_Quantidade_DM({$array['todosCampos']['T219_D009_Id']},{$row['T206_Id']})");
			//return $row['T206_Id'];
			$res=mysql_fetch_array($res);
			$quant=number_format($res[0],2);
			if($quant > 0){
				if($array['todosCampos']['T219_T206_Id'] == $row['T206_Id']){
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
							
				$select[] = <<<EOT
					<option value="T206_{$row['T206_Id']}" {$selected}> DM: {$quant} - {$row['T206_Descricao_Defeito']}</option>
EOT;
			}
			
		} // END IF
	}// END WHILE
	$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$array['todosCampos']['T219_D009_Id']}' and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 order by T075_Data_Previsao ASC";
	$result = mysql_query($sql);
	while($di = mysql_fetch_array($result)){
		$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['T219_D009_Id']},{$di['T075_Id']})");
		$ret=mysql_fetch_array($ret);
		$quant=number_format($ret[0],2);
		if($quant > 0){
			$data=gCorrigeData($di['T075_Data_Previsao']);		
			//$options.="<option value='{$di['T075_Id']}'>OC:{$di['T075_Id']} - {$data} - {$quant}</option>";
			if ($di['T075_Id']==$array['todosCampos']['T219_T075_Id']){
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
		if ($array['todosCampos']['T219_T075_Id'] > 0){
			$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$array['todosCampos']['T219_D009_Id']}' and T075_Id='{$array['todosCampos']['T219_T075_Id']}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' order by T075_Data_Previsao ASC";
			$result = mysql_query($sql);			
			$di = mysql_fetch_array($result);				
			$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['T219_D009_Id']},{$array['todosCampos']['T219_T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0],2);
			$data=gCorrigeData($di['T075_Data_Previsao']);
			$mostrar="MP: {$data} - {$quant}";

		} else if ($array['todosCampos']['T219_T206_Id'] > 0){

			$T206=mysql_query("SELECT T206_Flag_Tipo FROM T206 WHERE T206_Id='{$array['todosCampos']['T219_T206_Id']}'");
			$mT206=mysql_fetch_array($T206);

			if($mT206['T206_Flag_Tipo']==1){

				$res=mysql_query("SELECT D009_Quantidade_DF({$array['todosCampos']['T219_D009_Id']},{$array['todosCampos']['T219_T206_Id']}, 0)");
				$res=mysql_fetch_array($res);
				$defeito=number_format($res[0],2);
				$mostrar="DF: {$defeito}";
				
			}else{

				$res=mysql_query("SELECT D009_Quantidade_DM({$array['todosCampos']['T219_D009_Id']},{$array['todosCampos']['T219_T206_Id']})");
				$res=mysql_fetch_array($res);
				$demonstracao=number_format($res[0],2);
				$mostrar="DM: {$demonstracao}";
				
			}
		} else {
			$mostrar="PE: {$array['todosCampos']['D009_Quantidade_Estoque_Liquido']}"; 
		}
	} else {
		$mostrar = $array['todosCampos']['D009_Quantidade_Estoque_Liquido'];
	}	
	//Dados do return
	$idselect = 'select' . uniqid() . '_' . $array['todosCampos']['T219_Id'];
	$iddiv = 'div' . uniqid() . '_' . $array['todosCampos']['T219_Id'];
	$result = "<select id=\"{$idselect}\" onchange=\"atualizaT219(this);\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
	</select>
	<div id='{$iddiv}' onclick="$('#' + '{$idselect}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT219(select){
			var campo = $(select).val();
			campo = campo.split('_');
			var T219_Id = $(select).attr('id');
			T219_Id = T219_Id.split('_');
			T219_Id = T219_Id[1];
			$.getJSON('/est/est015/grid_func-ajax/atualizaT219/?ajax=true&tabela=' + campo[0] +  '&T219_Id='+ encodeURIComponent(T219_Id) + '&campoId=' + campo[1] + '&callback=?', function(request) {
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
		WHERE D001_Id = (SELECT D001_Id FROM D001 LEFT JOIN D049 ON D001_Id = D049_D001_Id LEFT JOIN D009 ON D049_Id = D009_D049_Id WHERE D009_Id = '{$array['todosCampos']['T219_D009_Id']}');
EOT;
	$result1 = mysql_query($D001);

	$select = array();

	while($mD001 = mysql_fetch_array($result1)){

		$T206 = mysql_query("SELECT * FROM T206 WHERE T206_D009_Id = '{$mD001['D009_Id']}'");
		//return $array['todosCampos']['T219_D009_Id_Loja'];
		if($array['todosCampos']['T219_D009_Id_Loja'] == $mD001['D009_Id']){
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
			//return $row['T206_Id'];
			$res=mysql_fetch_array($res);
			$quant=number_format($res[0],2);
			if($quant > 0){
				if($array['todosCampos']['T219_T206_Id'] == $row['T206_Id']){
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
				if ($di['T075_Id']==$array['todosCampos']['T219_T075_Id']){
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

	
	if($array['todosCampos']['T219_D009_Id_Loja'] > 0){
		$D009_Id = $array['todosCampos']['T219_D009_Id_Loja'];

		$D009 = mysql_query("SELECT D009_Quantidade_Estoque_Liquido, C004_Nome_Abreviado from D009 LEFT JOIN C004 on C004_Id=D009_C004_Id where D009_Id='{$D009_Id}'");
		$mD009 = mysql_fetch_array($D009);
		$D009_Quantidade_Estoque_Liquido = $mD009['D009_Quantidade_Estoque_Liquido'];
		$C004_Nome_Abreviado = $mD009['C004_Nome_Abreviado'];
	} else {
		$D009_Id = $array['todosCampos']['T219_D009_Id'];
		$D009_Quantidade_Estoque_Liquido = $array['todosCampos']['D009_Quantidade_Estoque_Liquido'];
		$C004_Nome_Abreviado = $g['C004']['C004_Nome_Abreviado'];
	}

	//Dados do campo
	if (!empty($select)) {	

		if ($array['todosCampos']['T219_T075_Id'] > 0){

			$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$D009_Id}' and T075_Id='{$array['todosCampos']['T219_T075_Id']}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' order by T075_Data_Previsao ASC";
			$result = mysql_query($sql);		
			$di = mysql_fetch_array($result);
			$ret=mysql_query("SELECT D009_Quantidade_MP({$D009_Id},{$array['todosCampos']['T219_T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0],2);
			$data=gCorrigeData($di['T075_Data_Previsao']);
			$mostrar="{$C004_Nome_Abreviado} MP: {$data} - {$quant}";

		} else if ($array['todosCampos']['T219_T206_Id'] > 0){

			$res=mysql_query("SELECT D009_Quantidade_DF({$D009_Id},{$array['todosCampos']['T219_T206_Id']}, 0)");
			$res=mysql_fetch_array($res);
			$defeito=number_format($res[0],2);
			$mostrar="{$C004_Nome_Abreviado} DF: {$defeito}";

		} else {

			$mostrar="{$C004_Nome_Abreviado} PE: {$D009_Quantidade_Estoque_Liquido}";
		}
	} else {

		$mostrar = $C004_Nome_Abreviado . $D009_Quantidade_Estoque_Liquido;
	}

	//Dados do return
	$result = "<select id=\"{$array['todosCampos']['T219_Id']}\" onchange=\"atualizaT219(this);\" onblur=\"$(this).hide(); $('#T219_{$array['todosCampos']['T219_Id']}').show();\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
	</select>
	<div id='T219_{$array['todosCampos']['T219_Id']}' onclick="$('#' + '{$array['todosCampos']['T219_Id']}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#' + '{$array['todosCampos']['T219_Id']}').hide();
		function atualizaT219(select){
			var campo = $(select).val();
			campo = campo.split('_');
			var T219_Id = $(select).attr('id');
			$.getJSON('/est/est015/grid_func-ajax/atualizaT219Loja/?ajax=true&tabela=' + campo[0] +  '&T219_Id='+ encodeURIComponent(T219_Id) + '&campoId=' + campo[1] + '&D009_Id_Loja=' + campo[3] + '&callback=?', function(request) {
				if(request.code){
					divRefresh('{$g['divId']}');
				}
			});	
		}
	}</script></div>
EOT;

	return $result;
}

function est015CampoDividir($array,$TXXX){
    global $g,$r_acaoId;
    $disabled = '';
    if(!empty($array['todosCampos']['T007_Id'])){
    	$disabled = ' disabled="disabled" ';
    }
    if($TXXX=="T222"){
        $id = $array['todosCampos']['T222_Id'];
        $Flag_Dividir = $array['todosCampos']['T222_Flag_Dividir'];
        $idInput = "T222_Flag_Dividir";
        if(!empty($array['todosCampos']['T007_Id'])){
            $disabled = ' disabled="disabled" ';
        }
    }elseif($TXXX=="T223"){
        $id = $array['todosCampos']['T223_Id'];
        $Flag_Dividir = $array['todosCampos']['T223_Flag_Dividir'];
        $idInput = "T230_Flag_Dividir";
    }else{
        $id = $array['todosCampos']['T219_Id'];
        $Flag_Dividir = $array['todosCampos']['T219_Flag_Dividir'];
        $idInput = "T219_Flag_Dividir";
    }
    
    $retorno = "<input onclick=\"est015CheckboxDividir(this, '{$id}', '{$g['divId']}', '{$TXXX}');\" type='checkbox' {$disabled} id='{$idInput}' title='Dividir'";
    if ($Flag_Dividir=='S'){
        return $retorno .="checked='checked'";
    }
    return $retorno .= " />";
}

function est015NaoLiberar($array) {
	global $g;
	return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Não Liberar', '/est/est015/content/est015ContentNaoLiberar/', '&acaoId={$array['todosCampos']['T218_Id']}', [400,300]); return false;">
				<span class="ui-icon ui-icon-closethick" title="Reprovar"></span>
			</a>
EOT;

}
function est015Liberar($array) {
	global $g;
	return <<<EOT
			<a href="#" onclick="est015AprovarOrcamento('{$array['todosCampos']['T218_Id']}', '{$g['divId']}');">
				<span class="ui-icon ui-icon-check" title="Aprovar"></span>
			</a>
EOT;

}

function validaCampoNFCotacoes($array){
	$T218_Id = $array['todosCampos']['T218_Id'];
	$retorno = '';
	$sql = mysql_query("SELECT T013_Id, T013_Numero_Nota_Fiscal FROM T218
		left join T219 on T219_T218_Id=T218_Id
		left join T225 on T225_T219_Id=T219_Id
		left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
		left join T014 on T014_T225_Id = T225_Id
		left join T013 on T013_Id=T014_T013_Id
		WHERE T218_Id = '{$T218_Id}' AND T013_Flag_Travar_Nota = 'S' GROUP BY T013_Id");
	while($res = mysql_fetch_array($sql)){
		$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness3/hardness/nota_recebida/gerar_danfe.php?T013_Id={$res['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>{$res['T013_Numero_Nota_Fiscal']}, </a>";
	}
    $retorno = substr($retorno, 0, -6);
    return $retorno . '</a>';
}

function validaCampoComprarAnalise($array){
	global $g,$r_acaoId;
	$param = explode("|", $array['campoAtual']);
	$T225_Id = $param[0];
	$T219_Id = $param[1];
	$T219_Flag_Comprar = $param[2];
	$T224_Id = $param[3];

	if($T219_Id <= 0){
		return '';
	}
	if ($T225_Id <= 0){
		$retorno = "<input onclick=\"est015CheckboxComprar(this, '{$T219_Id}', '{$g['divId']}')\" type='checkbox' id='T219_Flag_Comprar' title='Comprar'";
		if ($T219_Flag_Comprar=='S'){
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	} else {
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Ordem de Compra', '/est/est016/content/est016content02/', '&tabela=T224&acaoId=' + encodeURIComponent('{$T224_Id}'), '[990,605]'); return false;\">{$T224_Id}</a>";
	}
}