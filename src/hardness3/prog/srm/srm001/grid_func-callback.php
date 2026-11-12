<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */
//Carrega funções do ajax
require_once('prog/crm/crm001/grid_func-js.php');
require_once('prog/ven/ven002/grid_func-js.php');

function campoExclusaoCotacao($array) {
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

function campoCancelamentoCotacao($array) {
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

function campoCancelamentoOrdemCompra($array){
	global $g;
	if($array['todosCampos']['T224_Flag_Status']!=999){
		return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar Ordem de compra', '/est/est016/content/est016contentCancelarPedido/', '&T224_Id={$array['todosCampos']['T224_Id']}', [350,200]);">
    		<span class="ui-icon ui-icon-circle-close" title="Cancelar pedido"></span>
		</a>
EOT;
	}
}

function coloreLinha($array){
	if ($array['todosCampos']['T013_Flag_Travar_Nota'] == 'N' or $array['todosCampos']['T013_Flag_Cancelada']=='S'){
		return "<font color='red'>" . $array['todosCampos']['D024_Nome_Empresa'] . "</font>";
	}else if($array['todosCampos']['T013_Flag_Estoque_Finalizado'] == 'S'){
		return "<font color='gray'>" . $array['todosCampos']['D024_Nome_Empresa'] . "</font>";
	}else if($array['todosCampos']['T013_Flag_Travar_Nota'] == 'S'){
		return "<font color='black'>" . $array['todosCampos']['D024_Nome_Empresa'] . "</font>";
	}
}

function validaCampoAlmoxarifado($array){
	$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness/nota_recebida_lancamento/T013_imprimir_almoxarifado.php3?T013_Id={$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Imprimir</a>";
	return $retorno;
}
function validaCampoDanfe($array){
	$retorno = "";
	$retorno = "<a href=\"#\" onclick=\"nova_janela('/hardness3/hardness/nota_recebida/gerar_danfe.php?T013_Id={$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>DANFE</a>";
	return $retorno;
}

function validaCampoXML($array){
	// echo "<td align=center bgcolor='$cor' class='td_itens' onclick=\"javascript:parent.AbrirXML('$mT013[T013_Id]')\" style='cursor:pointer'>";

	global $confUsuario, $g;
	if (!empty($array['todosCampos']['T013_Chave_Acesso_Nfe'])){
		if (!empty($array['todosCampos']['T013_Numero_Protocolo_Nfe'])){
                //if (file_exists("dados_usuarios/{$confUsuario['dbDatabase']}/nfe/xml/recebido/{$array['todosCampos']['T013_Numero_Protocolo_Nfe']}.xml")){
                if (file_exists("{$g['pathDadosAntigo']}/nfe/xml/recebido/{$array['todosCampos']['T013_Numero_Protocolo_Nfe']}.xml")){
				$retorno = "<a href=\"/est/est004/grid_func-ajax/downloadxml/?ajax=true&T013_Id=" . urlencode($array['todosCampos']['T013_Id']) . "\">XML</a>";
			} else {
				$retorno = "<a title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>Erro!</a>";
			}
			return $retorno;
		}
		return '';
	}else{        
		return '';
	}
	 return false;		
}

function validaCampoCancelar($array){
	global $g;
	if($array['todosCampos']['T013_Flag_Cancelada']=='S'){
		$retorno = "";
		$retorno = <<<EOT
		 <font color='red'><a href="#" onclick="EST004ReverterCancelamento('{$array['todosCampos']['T013_Id']}', '{$g['divId']}');">Cancelada</a></font>
EOT;
		return $retorno;
	} elseif($array['todosCampos']['T013_Flag_Travar_Nota']=='S'){
		$retorno="";
		//$retorno="<a href=\"#\" onclick=\"nova_janela('/hardness/nota_recebida/cancelar_nf.php?T013_Id={$array['todosCampos']['T013_Id']}&Nova_Janela=S','NF {$array['todosCampos']['T013_Id']}',0,0,800,500,'no','no','no','no','yes','yes',0);\" style='cursor:pointer'>Cancelar</a>";
		return <<<EOT
		    <a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cancelar Nota Fiscal de Entrada', '/est/est004/content/est004ContentCancelarNotaEntrada/', '&tab=geral&T013_Id=' + encodeURIComponent('{$array['todosCampos']['T013_Id']}'), [500,250]);"><u>Cancelar</u></a>
EOT;
	} else {
		return <<<EOT
			<a href="#" onclick="EST004ExcluirNota('{$array['todosCampos']['T013_Id']}', '{$g['divId']}')">Excluir</a>
EOT;
	}
}

function validaCampoCancelarConhecimento($array){
	global $g;
	return <<<EOT
    	<a href="#" onclick="est005CampoCancelar('{$array['todosCampos']['T084_Id']}', '{$g['divId']}')">
    		Cancelar
		</a>
EOT;
}
	
function validaCampoExcluirConhecimento($array){
	global $g;
	return <<<EOT
    	<a href="#" onclick="est005CampoExcluir('{$array['todosCampos']['T084_Id']}', '{$g['divId']}')">
    		Excluir
		</a>
EOT;
}

function validaNotasLancadas($array){
	if ($array['todosCampos']['T084_Flag_Fechado']=='S')
     {
         return "<span class=\"ui-icon ui-icon-circle-check\"></span>";
     }
     else
     {
     	return "&nbsp";
     }
}

function est009campoConsumido($array){
	if($array['todosCampos']['D009_Quantidade_Consumo']>0){
		return "{$array['todosCampos']['D009_Quantidade_Consumo']}";
	}else{
		return "0,00";
	}
}

function validaCampoComprar($array){
	global $g,$r_acaoId;
	$retorno = "<input onchange=\"est009CheckboxComprar(this, '{$array['todosCampos']['D009_Id']}', '{$g['divId']}')\" type='checkbox' id='D009_Flag_Cotar' title='Cotar'";
	if ($array['todosCampos']['D009_Flag_Comprar']=='S'){
		return $retorno .="checked='checked'";
	}
	return $retorno .= " />";
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

function validaCampoComprarCotacao($array){
	global $g,$r_acaoId;
	if ($array['todosCampos']['T225_Id']<=0){
		$retorno = "<input onclick=\"est015CheckboxComprar(this, '{$array['todosCampos']['T219_Id']}', '{$g['divId']}')\" type='checkbox' id='T219_Flag_Comprar' title='Comprar'";
		if ($array['todosCampos']['T219_Flag_Comprar']=='S'){
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	} else {
		return "";
	}
}

function coloreLinhaProdutos($array){
	global $g;
	if ($array['todosCampos']['T014_D009_Id'] <= 0 or $array['todosCampos']['D009_C004_Id'] != $g['empresaAtual']){
		return "<font color='red'>" . $array['todosCampos']['T014_Descricao_Produto'] . "</font>";
	}else{
		return "<font color='black'>" . $array['todosCampos']['T014_Descricao_Produto'] . "</font>";
	}
}

function T014_D006_Id_CFOP($array){
	return $array['todosCampos']['D006_Codigo_CFOP'];
}
