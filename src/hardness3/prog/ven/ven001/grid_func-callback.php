<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

function campoExclusao($array) {
	global $g;
    $resultado=mysql_query("select T005_Id from T005 where T005_T003_Id='{$array['todosCampos']['T003_Id']}'");
    $row=mysql_fetch_array($resultado);
    if(empty($row['T005_Id'])){	
		if($array['todosCampos']['T003_Flag_Perdido'] != 'S'){
		return <<<EOT
		<a href="#" onclick="ven001CampoExclusao('{$array['todosCampos']['T003_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir orçamento"></span>
			</a>
EOT;
		}
	}else{
		return "";
	}
}
function cancelarProduto($array) {
	global $g;

		return <<<EOT
		<a href="#" onclick="ven001CampoCancelamento('{$array['todosCampos']['T004_Id']}','{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Cancelar Produto"></span>
		</a>
EOT;

}
function campoCancelamento($array) {
	global $g;
    $resultado=mysql_query("select T005_Id from T005 where T005_T003_Id='{$array['todosCampos']['T003_Id']}' AND T005_flag_status != '8'");
    $row=mysql_fetch_array($resultado);
    if(empty($row['T005_Id'])){	
		if($array['todosCampos']['T003_Flag_Perdido'] != 'S'){
				return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Motivo Cancelamento', '/ven/ven001/content/ven001ContentCancelamentoOrcamento/', '&acaoId={$array['todosCampos']['T003_Id']}', [400,300]); return false;">
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
	if($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'P'){
		return "Pendente";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'F'){
		return "Finalizado";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'C'){
		return "Cancelado";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'V'){
		return "Vencido";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'A'){
		return "<font color=\"green\">Aprovado</font>";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'N'){
		return "<font color=\"red\">Negado - ".$array['todosCampos']['T003_Liberou_Alcada_Observacao_Nao_Liberado']."</font>";
	}
	elseif($array['todosCampos']['T003_Flag_Status_Orcamento'] == 'L'){
		return "Análise";
	}
}

function geraImagem($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro', '/ven/ven001/content/ven001ContentEditarServico/', '&acaoId={$array['todosCampos']['T145_Id']}', [700,500]);\">
				<li title=\"Detalhes do Serviço\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
			</a>";
}

function campoPedidos($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['Pedido']}') + '&table=T005', [900,550]); return false;\">
					{$array['campoAtual']}
			</a>";
}

function validaCampoPedido($array){
	global $g;
    if($array['todosCampos']['T003_Flag_Perdido'] == 'S'){
		// busca o motivo do cancelamento 
		$resultado=mysql_query("select * from T003 where T003_Id='{$array['todosCampos']['T003_Id']}'");
    	$row=mysql_fetch_array($resultado);
		$resultado=mysql_query("select * from D047 where D047_Id='{$row['T003_D047_Id']}'");
    	$row=mysql_fetch_array($resultado);    		
		return "<font color='red'><b>".$row['D047_Descricao_Motivo'].'</b> - '.$array['todosCampos']['T003_Observacao_Orcamento_Perdido']."</font>";
	}else{
		if ($array['todosCampos']['T005_Id']>0 and $array['todosCampos']['T005_Flag_Status']!=5) {
		   	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T005_Id']}), [950,615]);\"> <font style='font-weight:bold'>".$array['todosCampos']['Pedido']."</font></a>";
		} else {
		   	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T005_Id']}), [950,615]);\">{$array['todosCampos']['Pedido']}</a>";
		}
	}
}

function buscaUnidade($array) {
	return $array['todosCampos']['D037_Unidade'];
}

function buscaVeiculo($array) {
	$D116=mysql_query("SELECT * from D116 LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_Id=".$array['todosCampos']['T145_D116_Id']);			
	$veiculo=@mysql_fetch_array($D116);
	return $veiculo['D116_Placa'].' - '.$veiculo['D133_Modelo'].' - '.$veiculo['D134_Cor'];
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

function ven001_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['T089_Data_Vencimento'];
	return gPegaDiaSemana($data);
}	

function ven001_Detalhes($array) {
	global $g;
	if($array['todosCampos']['D027_Flag_Tipo']=='2'){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Detalhes do Cheque', '/ven/ven002/content/ven002ContentDetalhesCheque/', '&T090_Id={$array['todosCampos']['T090_Id']}', [390,270]);\">
				<li title=\"Detalhes do cheque\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
				</a>";	
	}
}	

function T089_D027_Id_Portador($array) {
	return $array['todosCampos']['D027_Portador'];
}

function validaCampoStatusServicos($array){
	$sql = "SELECT * FROM T147 WHERE T147_T145_Id='{$array['todosCampos']['T145_Id']}'";
	$result = mysql_query($sql);
	$retorno = "";
	if(mysql_num_rows($result) > 0){
		$linha = mysql_fetch_array($result);
		
		if(($linha['T147_Status'] == "P") || ($linha['T147_Status'] == "NULL")){
			return "<font color='red'><b>Pendente</b></font>";
		}else if($linha['T147_Status'] == "E"){
			return "<font color='green'><b>Execução</b></font>";
		}else if($linha['T147_Status'] == "C"){
			return "<font color='blue'><b>Concluído</b></font>";
		}else if($linha['T147_Status'] == "N"){
			return "<b>Cancelado</b>";
		}else if($linha['T147_Status'] == "NULL"){
			return "<b>Cancelado</b>";
		}
	}
}

function retornaOperacaoTroca($array){
	if ($array['todosCampos']['T138_Operacao']=='P')
		return 'ENTRADAS';
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
		<span onclick="ven001ExcluirServico('{$array['todosCampos']['T145_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</span>
EOT;
	
}

function excluirProduto($array){
	global $g;
	return <<<EOT
		<span onclick="ven001ExcluirProduto('{$array['todosCampos']['T004_Id']}', '{$g['divId']}');"> 
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</span>
EOT;
	
}

function campoAbrirDocumento($array){
			$id = $array['todosCampos']['T188_Id'];
			$retorno = "<a href=\"/ven/ven001/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
			return $retorno;
}
function campoLinkProduto($array){
			$sql = mysql_query("SELECT D001_Id FROM T004 LEFT JOIN D009 ON D009_Id=T004_D009_Id 
					LEFT JOIN D049 ON D049_Id=D009_D049_Id
					LEFT JOIN D001 ON D001_Id=D049_D001_Id
					LEFT JOIN T144 ON T144_D001_Id=D001_Id WHERE T004_Id='{$array['todosCampos']['T004_Id']}'");
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
	$T206 = mysql_query("SELECT * FROM T206 LEFT JOIN D169 ON T206_D169_Id = D169_Id LEFT JOIN T004 ON T004_T206_Id=T206_Id WHERE T206_D009_Id = '{$array['todosCampos']['T004_D009_Id']}' AND D169_Id<>'NULL' AND T004_Id IS NULL");
	
	$select[] = <<<EOT
	<option value="" selected="selected">PE: {$array['todosCampos']['D009_Quantidade_Estoque_Liquido']}</option>
EOT;
	while($row = mysql_fetch_array($T206)){

		// Verificando se é tipo  DEFEITO ou DEMONSTRAÇÃO
		$res=mysql_query("SELECT T206_Quantidade FROM T206 WHERE T206_Id = '{$row['T206_Id']}'");
		$res=mysql_fetch_array($res);
		$quant=number_format($res[0],2);
		if($quant > 0){
			if($array['todosCampos']['T004_T206_Id'] == $row['T206_Id']){
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}

			$select[] = <<<EOT
				<option value="T206_{$row['T206_Id']}" {$selected}> {$row['D169_Sigla_Tipo_Produto']}: {$quant} - {$row['T206_Descricao_Defeito']}</option>
EOT;
		} // END IF
	}// END WHILE
	$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$array['todosCampos']['T004_D009_Id']}' and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 order by T075_Data_Previsao ASC";
	$result = mysql_query($sql);
	while($di = mysql_fetch_array($result)){
		$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['T004_D009_Id']},{$di['T075_Id']})");
		$ret=mysql_fetch_array($ret);
		$quant=number_format($ret[0],2);
		if($quant > 0){
			$data=gCorrigeData($di['T075_Data_Previsao']);		
			//$options.="<option value='{$di['T075_Id']}'>OC:{$di['T075_Id']} - {$data} - {$quant}</option>";
			if ($di['T075_Id']==$array['todosCampos']['T004_T075_Id']){
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
		if ($array['todosCampos']['T004_T075_Id'] > 0){
			$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$array['todosCampos']['T004_D009_Id']}' and T075_Id='{$array['todosCampos']['T004_T075_Id']}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' order by T075_Data_Previsao ASC";
			$result = mysql_query($sql);			
			$di = mysql_fetch_array($result);				
			$ret=mysql_query("SELECT D009_Quantidade_MP({$array['todosCampos']['T004_D009_Id']},{$array['todosCampos']['T004_T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0],2);
			$data=gCorrigeData($di['T075_Data_Previsao']);
			$mostrar="MP: {$data} - {$quant}";

		} else if ($array['todosCampos']['T004_T206_Id'] > 0){

			$T206=mysql_query("SELECT D169_Sigla_Tipo_Produto, T206_Quantidade FROM T206 LEFT JOIN D169 ON D169_Id = T206_D169_Id WHERE T206_Id = '{$array['todosCampos']['T004_T206_Id']}'");
			$mT206=mysql_fetch_array($T206);
			$sigla = $mT206['D169_Sigla_Tipo_Produto'];
			$demonstracao=number_format($mT206['T206_Quantidade'],2);
			$mostrar="{$sigla}: {$demonstracao}";
		} else {
			$mostrar="PE: {$array['todosCampos']['D009_Quantidade_Estoque_Liquido']}"; 
		}
	} else {
		$mostrar = $array['todosCampos']['D009_Quantidade_Estoque_Liquido'];
	}	
	//Dados do return
	$idselect = 'select' . uniqid() . '_' . $array['todosCampos']['T004_Id'];
	$iddiv = 'div' . uniqid() . '_' . $array['todosCampos']['T004_Id'];
	$result = "<select id=\"{$idselect}\" onchange=\"atualizaT004(this);\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
	</select>
	<div id='{$iddiv}' onclick="$('#' + '{$idselect}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT004(select){
			var campo = $(select).val();
			campo = campo.split('_');
			var T004_Id = $(select).attr('id');
			T004_Id = T004_Id.split('_');
			T004_Id = T004_Id[1];
			$.getJSON('/ven/ven001/grid_func-ajax/atualizaT004/?ajax=true&tabela=' + campo[0] +  '&T004_Id='+ encodeURIComponent(T004_Id) + '&campoId=' + campo[1] + '&callback=?', function(request) {
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
		WHERE D001_Id = (SELECT D001_Id FROM D001 LEFT JOIN D049 ON D001_Id = D049_D001_Id LEFT JOIN D009 ON D049_Id = D009_D049_Id WHERE D009_Id = '{$array['todosCampos']['T004_D009_Id']}');
EOT;
	$result1 = mysql_query($D001);

	$select = array();

	while($mD001 = mysql_fetch_array($result1)){

		$T206 = mysql_query("SELECT * FROM T206 WHERE T206_D009_Id = '{$mD001['D009_Id']}'");
		//return $array['todosCampos']['T004_D009_Id_Loja'];
		if($array['todosCampos']['T004_D009_Id_Loja'] == $mD001['D009_Id']){
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
				if($array['todosCampos']['T004_T206_Id'] == $row['T206_Id']){
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
				if ($di['T075_Id']==$array['todosCampos']['T004_T075_Id']){
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

	
	if($array['todosCampos']['T004_D009_Id_Loja'] > 0){
		$D009_Id = $array['todosCampos']['T004_D009_Id_Loja'];

		$D009 = mysql_query("SELECT D009_Quantidade_Estoque_Liquido, C004_Nome_Abreviado from D009 LEFT JOIN C004 on C004_Id=D009_C004_Id where D009_Id='{$D009_Id}'");
		$mD009 = mysql_fetch_array($D009);
		$D009_Quantidade_Estoque_Liquido = $mD009['D009_Quantidade_Estoque_Liquido'];
		$C004_Nome_Abreviado = $mD009['C004_Nome_Abreviado'];
	} else {
		$D009_Id = $array['todosCampos']['T004_D009_Id'];
		$D009_Quantidade_Estoque_Liquido = $array['todosCampos']['D009_Quantidade_Estoque_Liquido'];
		$C004_Nome_Abreviado = $g['C004']['C004_Nome_Abreviado'];
	}

	//Dados do campo
	if (!empty($select)) {	

		if ($array['todosCampos']['T004_T075_Id'] > 0){

			$sql = "SELECT T075_Id,T075_Data_Previsao,T075_Numero_DI FROM T055 left join T075 on T075_Id=T055_T075_Id WHERE T055_D009_Id='{$D009_Id}' and T075_Id='{$array['todosCampos']['T004_T075_Id']}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1 and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' order by T075_Data_Previsao ASC";
			$result = mysql_query($sql);		
			$di = mysql_fetch_array($result);
			$ret=mysql_query("SELECT D009_Quantidade_MP({$D009_Id},{$array['todosCampos']['T004_T075_Id']})");
			$ret=mysql_fetch_array($ret);
			$quant=number_format($ret[0],2);
			$data=gCorrigeData($di['T075_Data_Previsao']);
			$mostrar="{$C004_Nome_Abreviado} MP: {$data} - {$quant}";

		} else if ($array['todosCampos']['T004_T206_Id'] > 0){

			$res=mysql_query("SELECT D009_Quantidade_DF({$D009_Id},{$array['todosCampos']['T004_T206_Id']}, 0)");
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
	$result = "<select id=\"{$array['todosCampos']['T004_Id']}\" onchange=\"atualizaT004(this);\" onblur=\"$(this).hide(); $('#T004_{$array['todosCampos']['T004_Id']}').show();\">";
	foreach($select as $key => $value){
		$result .= $value;
	}
	$result .= <<<EOT
	</select>
	<div id='T004_{$array['todosCampos']['T004_Id']}' onclick="$('#' + '{$array['todosCampos']['T004_Id']}').show(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#' + '{$array['todosCampos']['T004_Id']}').hide();
		function atualizaT004(select){
			var campo = $(select).val();
			campo = campo.split('_');
			var T004_Id = $(select).attr('id');
			$.getJSON('/ven/ven001/grid_func-ajax/atualizaT004Loja/?ajax=true&tabela=' + campo[0] +  '&T004_Id='+ encodeURIComponent(T004_Id) + '&campoId=' + campo[1] + '&D009_Id_Loja=' + campo[3] + '&callback=?', function(request) {
				if(request.code){
					divRefresh('{$g['divId']}');
				}
			});	
		}
	}</script></div>
EOT;

	return $result;
}

function ven001AbrirCotacaoCompra($array){
	$T219_T218_Id = $array['todosCampos']['T219_T218_Id'];
	$T003_C007_Id_Vendedor_Interno = $array['todosCampos']['T003_C007_Id_Vendedor_Interno'];
	$T218_C007_Id_Vendedor_Interno = $array['todosCampos']['T218_C007_Id_Vendedor_Interno'];
	log("t003_c007:".$T003_C007_Id_Vendedor_Interno."      t218_c007:".$T218_C007_Id_Vendedor_Interno);

	if($T003_C007_Id_Vendedor_Interno==$T218_C007_Id_Vendedor_Interno){
		return "<span onclick='abrirCotacao({$T219_T218_Id})'><u>{$T219_T218_Id}</u></span>";
	}else{
		return $T219_T218_Id;
	}

}

function ven001CampoDividir($array,$TXXX){
    global $g,$r_acaoId;
    $disabled = '';
    if(!empty($array['todosCampos']['T007_Id'])){
    	$disabled = ' disabled="disabled" ';
    }
    if($TXXX=="T138"){
        $id = $array['todosCampos']['T138_Id'];
        $Flag_Dividir = $array['todosCampos']['T138_Flag_Dividir'];
        $idInput = "T138_Flag_Dividir";
        if(!empty($array['todosCampos']['T007_Id'])){
            $disabled = ' disabled="disabled" ';
        }
    }elseif($TXXX=="T145"){
        $id = $array['todosCampos']['T145_Id'];
        $Flag_Dividir = $array['todosCampos']['T145_Flag_Dividir'];
        $idInput = "T147_Flag_Dividir";
    }else{
        $id = $array['todosCampos']['T004_Id'];
        $Flag_Dividir = $array['todosCampos']['T004_Flag_Dividir'];
        $idInput = "T004_Flag_Dividir";
    }
    
    $retorno = "<input onclick=\"ven001CheckboxDividir(this, '{$id}', '{$g['divId']}', '{$TXXX}');\" type='checkbox' {$disabled} id='{$idInput}' title='Dividir'";
    if ($Flag_Dividir=='S'){
        return $retorno .="checked='checked'";
    }
    return $retorno .= " />";
}

function T004A_D184_Id_Unidade($array, $param = array('divReload' => false)) {
	global $g;

	$mostrar = "<font style='background:green;color:white;padding:4px;border-radius: .425rem;'>" . $array['todosCampos']['unConversao.D037_Unidade'] . "</font>";
	$mostrar = empty($array['campoAtual']) ? '-' : $mostrar;

	if($array['todosCampos']['T006_T005_Id'] > 0){
		// Se já tiver pedido, não deixa alterar
		return $mostrar;
	}

	$options = gGeraSelect('D184', 
					       'D184_Id', 
						   'D037_Unidade', 
						   "LEFT JOIN D037 ON D184_D037_Id = D037_Id WHERE D184_D001_Id = '{$array['todosCampos']['D001_Id']}' ORDER BY D037_Unidade", 
							true, 
							$array['todosCampos']['T004A_D184_Id']);
	$options[] = array(
		'title' => 'optgroup',
	);

	$options = gProcessaSelect($options);

	$idselect = uniqid();
	$iddiv    = uniqid();
	$result   = "<select id=\"{$idselect}\" onchange=\"atualizaUnidade(event, this, '{$array['todosCampos']['T004A_Id']}', '{$array['todosCampos']['T004_Id']}');\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">{$options}";
	$result   .= <<<EOT
				</select>
					<div id="{$iddiv}" onclick="$('#{$idselect}').show().focus(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							function atualizaUnidade(event, select, T004A_Id, T004_Id){
								var campo = $(select).val();
								$.getJSON('/ven/ven001/grid_func-ajax/atualizarUnidade/?ajax=true&T004A_Id='+ encodeURIComponent(T004A_Id) + '&D184_Id='+ encodeURIComponent(campo) + '&callback=?', function(request) {
									if(request.code){
										divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id');
										divRefresh('{$param['divReload']}');

									}
								});									
							}
					}</script></div>
EOT;

	return $result;

}

function T004A_T066_Id_Local($array) {
	global $g;
    
	if (empty($array['todosCampos']['T004_D009_Id'])) {
		return '-';
	}

	if($array['todosCampos']['D001A_Flag_Validade'] == 'S'){

		$extraD004 = "LEFT JOIN D004  ON T066_D004_Id  = D004_Id 
				 	  LEFT JOIN T066A ON T066A_T066_Id = T066_Id 
					      WHERE T066_D009_Id = '{$array['todosCampos']['T004_D009_Id']}'
				       ORDER BY IF(T066A_Data_Validade = '0000-00-00' OR T066A_Data_Validade IS NULL,'9999-99-99', T066A_Data_Validade) ASC, T066_Quantidade_Estoque_Liquido DESC, T066_Id ASC";

		$options = gGeraSelect('T066', 'T066_Id', 'D004_Local,format(T066_Quantidade_Estoque_Liquido,0),if(T066A_Data_Validade = "0000-00-00" OR T066A_Data_Validade IS NULL,"- VAL. NÃO DEFINIDA", concat("- VAL: ",DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y")))', $extraD004, true, $array['todosCampos']['T004A_T066_Id'],"%1 (QTD:%2) %3");

		$validade = ($array['todosCampos']['T066A_Data_Validade'] > '0000-00-00') ? "- VAL: ".gCorrigeData($array['todosCampos']['T066A_Data_Validade']) : ' VAL. NÃO DEFINIDA';
		$mostrar  = $array['todosCampos']['D004_Local']." (QTD: ".gCorrigeNumero($array['todosCampos']['T066_Quantidade_Estoque_Liquido'],0).")". $validade;

	} else {
		$extraD004 = "LEFT JOIN D004  ON T066_D004_Id  = D004_Id 
					  LEFT JOIN T066A ON T066A_T066_Id = T066_Id 
						  WHERE T066_D009_Id = '{$array['todosCampos']['T004_D009_Id']}'
		 			   ORDER BY T066_Quantidade_Estoque DESC";

		$options = gGeraSelect('T066','T066_Id','D004_Local,format(T066_Quantidade_Estoque_Liquido,0),if(T066A_Data_Validade = "0000-00-00" OR T066A_Data_Validade IS NULL,"", concat("- VAL: ",DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y")))', $extraD004, true, false, "%1 (QTD:%2)");
		$mostrar = $array['todosCampos']['D004_Local'] . " (QTD: ".gCorrigeNumero($array['todosCampos']['T066_Quantidade_Estoque_Liquido'],0) . ")";

	}

	$mostrar = empty($array['campoAtual']) ? '-' : $mostrar;

	$options[] = array(
		'title' => 'optgroup',
		'value' => '- - - - - - - - - - - - - - - - -'
	);
/* 	$options[] = array(
		'title' => 'ALTERAR LOCAÇÃO',
		'value' => 'AL'
	); */
	$options = gProcessaSelect($options);
	$idselect = uniqid();
	$iddiv = uniqid();
	$result = "<select id=\"{$idselect}\" onchange=\"atualizaT004Local(event, this, '{$array['todosCampos']['T004_Id']}', '{$array['todosCampos']['T004_D009_Id']}');\" onblur=\"$(this).hide(); $('#{$iddiv}').show();\" style=\"display: none;\">{$options}";
	$result .= <<<EOT
</select>
	<div id="{$iddiv}" onclick="$('#{$idselect}').show().focus(); $(this).hide();"><span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;" ></span><span style="padding-right: 16px;">{$mostrar}</span></div>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		function atualizaT004Local(event, select, T004_Id, D009_Id){
			var campo = $(select).val();
			if (campo == 'AL') {
				abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/est/est003/content/est003ContentLocacoes/','&acaoId=' + encodeURIComponent(D009_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&T004_Id=' + encodeURIComponent(T004_Id), [700,350]);
				$('#{$idselect}').val(0);
				$('#{$idselect}').hide();
				$('#{$iddiv}').show();
			} else {
				$.getJSON('/ven/ven001/grid_func-ajax/atualizaT004Local/?ajax=true&T004_Id='+ encodeURIComponent(T004_Id) + '&T066_Id='+ encodeURIComponent(campo) + '&callback=?', function(request) {
					if(request.code){
						divRefresh('{$g['divId']}', true, false, false, T004_Id, 'T004_Id');
					}
				});	
			}
		}
	}</script></div>
EOT;
	return $result;
}

?>