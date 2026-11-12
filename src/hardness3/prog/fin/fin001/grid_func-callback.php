<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Funções PHP para callbacks
*/

// Imagem para exclusão da linha
function campoExclusao($array) {
	global $g;
	if (($array['todosCampos']['T007_Id'] > 0 && $array['todosCampos']['T002_Flag_Agrupado_Reparcelado'] == 'N') || (!isset($g['c029Ids'][111]))){
		return "&nbsp";
	} else {
    	return <<<EOT
    		<a href="#" onclick="fin001CampoExclusao('{$array['todosCampos']['T002_Id']}','{$array['todosCampos']['T002_T035_Id']}', '{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}
}

// exclusão do grid de acrescimos e descontos
function campoExclusaoAd($array) {	
	global $g;
	return <<<EOT
    	<a href="#" onclick="fin001CampoExclusaoAd('{$array['todosCampos']['T020_Id']}','{$array['todosCampos']['T020_T002_Id']}', '{$g['divId']}')">
    		<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
function campoExclusaoRemessa($array) {	
	global $g;
	if ($array['todosCampos']['T035_Numero_Titulos']>0){
	return <<<EOT
    	<a href="#" onclick="fin001CampoExclusaoRemessa('{$array['todosCampos']['T035_Id']}', '{$g['divId']}')">
    		<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
	}
}

function validaCampoBx($array){
	global $g;
	if($array['todosCampos']['T002_T002_Id_Agrupado'] > 0){
		return '';
	}
	if($array['todosCampos']['T002_Data_Recebimento']=='0000-00-00' && $array['todosCampos']['T002_Flag_Cancelada']!='S'){
		$retorno = "<input type='checkbox' id='T002_Flag_Baixar_Lote' title='Baixar titulo' onChange=\"fin001CheckboxBx(this, '{$array['todosCampos']['T002_Id']}', '{$g['divId']}')\" ";
		
		if ($array['todosCampos']['T002_Flag_Baixar_Lote']=='S'){
			$retorno .="checked='checked'";
		}
		return $retorno .= " />";
	}elseif($array['todosCampos']['T002_Flag_Cancelada']!='S'){
		
		$T002_DR = $array['todosCampos']['T002_Data_Recebimento'];
		if ($T002_DR == null || $T002_DR == '' || $T002_DR == '0000-00-00')
			$T002_DR=0;
		
		return "<a href=\"#\"><span class=\"ui-icon ui-icon-closethick\" title=\"Estornar título\" onClick=\"fin001ValidaCampoRmEstorno(event, '{$array['todosCampos']['T002_Id']}','{$T002_DR}', '{$g['divId']}', '{$g['divIdRoot']}')\">
							</span>
			    </a>";
	}
}

function validaCampoRp($array){
	global $g;
	if($array['todosCampos']['T002_T002_Id_Agrupado'] > 0){
		return '';
	}
	if($array['todosCampos']['T002_Data_Recebimento']=='0000-00-00' && $array['todosCampos']['T002_T035_Id']<=0 && $array['todosCampos']['T002_Flag_Cancelada']!='S'){
		$retorno = "<input type='checkbox' name='T002_Flag_Reparcelar' title='Reparcelamento / Agrupamento' onChange=\"fin001CheckboxRp(this, '{$array['todosCampos']['T002_Id']}', '{$g['divId']}')\" ";
		
		if ($array['todosCampos']['T002_Flag_Reparcelar']=='S') {
			$retorno .="checked = 'checked'";
		}
		return $retorno .= " />";
	}else{
		return "&nbsp;";
	}
}

function defineDescricao($array){
	global $g;
	if ($array['todosCampos']['T019_Valor_Lancamento']<=0)
		return "Estorno de pagamento";
	else 
		return 'Recebimento '.$array['todosCampos']['D027_Portador'];
}

/**
* Campo 'T103_D027_Id' do grid baixar títulos (fin001grid07)
* 
* @param array $array
* @param Formulario $form
*/
function baixarTitulosPortador($array, $form) {
	global $g;
	// Define qual valor está seleciona
	foreach ($form->formArray['campos']['baixar_titulos']['T103_D027_Id']['select'] as &$select) {
		if ($select['value'] == $array['campoAtual']) { 
			$select['selected'] = true;
		} else {
			unset($select['selected']);
		}
	}
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_D027_Id');
}

/**
* Campo 'T103_Valor_Recebido' do grid baixar títulos (fin001grid07)
* 
* @param array $array
* @param Formulario $form
*/
function baixarTitulosValorRecebido($array, $form) {
	global $g;
	// Define qual valor está selecionado
	$form->formArray['campos']['baixar_titulos']['T103_Valor_Recebido']['value'] = number_format($array['todosCampos']['T103_Valor_Recebido'], 2, ',', '.');
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_Valor_Recebido');
}

/**
* Campo 'T103_Valor_Desconto' do grid baixar títulos (fin001grid07)
* 
* @param array $array
* @param Formulario $form
*/
function baixarTitulosValorDesconto($array, $form) {
	global $g;
	// Define qual valor está selecionado
	$form->formArray['campos']['baixar_titulos']['T103_Valor_Desconto']['value'] = number_format($array['todosCampos']['T103_Valor_Desconto'], 2, ',', '.');
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_Valor_Desconto');
}

/**
* Campo 'T103_Valor_Taxas' do grid baixar títulos (fin001grid07)
* 
* @param array $array
* @param Formulario $form
*/
function baixarTitulosValorTaxas($array, $form) {
	global $g;
	// Define qual valor está selecionado
	$form->formArray['campos']['baixar_titulos']['T103_Valor_Taxas']['value'] = number_format($array['todosCampos']['T103_Valor_Taxas'], 2, ',', '.');
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_Valor_Taxas');
}

/**
* Campo 'T103_Valor_Juros' do grid baixar títulos (fin001grid07)
* 
* @param array $array
* @param Formulario $form
*/
function baixarTitulosValorJuros($array, $form) {
	global $g;
	// Define qual valor está selecionado
	$form->formArray['campos']['baixar_titulos']['T103_Valor_Juros']['value'] = number_format($array['todosCampos']['T103_Valor_Juros'], 2, ',', '.');
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_Valor_Juros');
}

function fin001_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['T180_Data_Vencimento'];
	return gPegaDiaSemana($data);
}	

function T180_D027_Id_Portador($array) {
	return $array['todosCampos']['D027_Portador'];
}

function fin001_Detalhes($array) {
	global $g;
	if($array['todosCampos']['D027_Flag_Tipo']=='2'){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Detalhes do Cheque', '/fin/fin001/content/fin001ContentDetalhesCheque/', '&T180_Id={$array['todosCampos']['T180_Id']}', [390,270]);\">
				<li title=\"Detalhes do cheque\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
				</a>";	
	}
}	

/**
* Campo 'T002_Flag_Baixar_Lote' do grid baixar títulos (fin001grid07)
* 
* @param array $array
* @param Formulario $form
*/
function baixarTitulosBx($array, $form) {
	global $g;
	// Define qual valor está seleciona
	$form->formArray['campos']['baixar_titulos']['T002_Flag_Baixar_Lote']['checked'] = ($array['campoAtual'] == 'S') ? true : false;
	$form->processarFormulario();
	return $form->campoCodigoHTML('T002_Flag_Baixar_Lote');
}

/**
* Campo 'T002_Flag_Baixar_Lote' do grid baixar títulos (fin001grid07)
* 
* @param array $array
* @param Formulario $form
*/
function formataMesAno($array) {	
	$mes=$array['todosCampos']['Mes'];
	$ano=$array['todosCampos']['Ano'];
	if ($mes<10)
		$mes='0'.$mes;
	return $mes.'/'.$ano;
}

$totalAcumuladoRecebido = 0;
function calculaAcumuladoRecebido($array){
	global $totalAcumuladoRecebido;
	return $totalAcumuladoRecebido+=$array['todosCampos']['Total_Recebido'];	
}

$totalAcumuladoReceber = 0;
function calculaAcumuladoReceber($array){
	global $totalAcumuladoReceber;
	return $totalAcumuladoReceber+=$array['todosCampos']['Total_Vencer'];	
}

function calculaTotalReceber($array){
	return $array['todosCampos']['Total_Vencer']+$array['todosCampos']['Total_Atrasado'];	
}

function validaCampoTipo($array){
	if($array['todosCampos']['T166_Tipo'] == "R"){
		return "Reparcelamento";
	}elseif($array['todosCampos']['T166_Tipo'] == "A"){
		return "Agrupamento";
	}
}
/**
* Mesmos links para download nas 5 funções, apenas muda os valores de retorno para o grid
*/
function campoDownloadRemessa1($array){
	global $g;	
	return "<a style=\"text-decoration:none;\" href=\"/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id={$array['todosCampos']['T035_Id']}&ajax=true\">
				<span title=\"Baixar Remessa\">{$array['todosCampos']['T035_Id']}</span>
	        </a>";
}
function campoDownloadRemessa2($array){
	global $g;
	$dataProcessamento = gCorrigeData($array['todosCampos']['T035_Data_Processamento']);
	return "<a style=\"text-decoration:none;\" href=\"/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id={$array['todosCampos']['T035_Id']}&ajax=true\">
				<span title=\"Baixar Remessa\">{$dataProcessamento}</span>
	        </a>";
}
function campoDownloadRemessa3($array){
	global $g;	
	$temponto = strstr($array['todosCampos']['T035_Nome_Arquivo'], '.');
    if($temponto==''){
        $nomeArquivo = $array['todosCampos']['T035_Nome_Arquivo'] . '.txt';
    }else{
        $nomeArquivo = $array['todosCampos']['T035_Nome_Arquivo'];
    }
	return "<a style=\"text-decoration:none;\" href=\"/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id={$array['todosCampos']['T035_Id']}&ajax=true\">
				<span title=\"Baixar Remessa\">{$nomeArquivo}</span>
	        </a>";
}
function campoDownloadRemessa4($array){
	global $g;	
	return "<a style=\"text-decoration:none;\" href=\"/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id={$array['todosCampos']['T035_Id']}&ajax=true\">
				<span title=\"Baixar Remessa\">{$array['todosCampos']['T035_Numero_Titulos']}</span>
	        </a>";
}
function campoDownloadRemessa5($array){
	global $g;
	$totalRemessas = gCorrigeNumero($array['todosCampos']['T035_Total_Remessa']);	
	return "<a style=\"text-decoration:none;\" href=\"/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id={$array['todosCampos']['T035_Id']}&ajax=true\">
				<span title=\"Baixar Remessa\">{$totalRemessas}</span>
	        </a>";
}
function campoDownloadRemessa6($array){
	global $g;	
	return "<a style=\"text-decoration:none;\" href=\"/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id={$array['todosCampos']['T035_Id']}&ajax=true\">
				<span title=\"Baixar Remessa\">{$array['todosCampos']['D007_Nome_Banco']}</span>
	        </a>";
}

function validaCampoTipoAcrescimosDescontos($array){
	if($array['todosCampos']['T020_Flag_Deb_Cred']=='D'){
		return 'Débito';
	}elseif($array['todosCampos']['T020_Flag_Deb_Cred']=='C'){
		return 'Crédito';
	}
}

function formataGeradores($array){
	$sql = "SELECT T166_Titulos_Geradores FROM T166 WHERE T166_Id='{$array['todosCampos']['T166_Id']}'";
	$result = mysql_query($sql);
	$retorno = "";
	if(mysql_num_rows($result) > 0){
		$linha = mysql_fetch_array($result);
		$res=explode("\n", $linha['T166_Titulos_Geradores']);
		foreach ($res as $elemento){
			if ($elemento!=""){
				$part=explode(";", $elemento);
				$retorno.=$part[1]." - ";
			}
		}
		while ( (substr($retorno,-1,1)==" ") || (substr($retorno,-1,1)=="-")){
			$retorno=substr_replace($retorno, '', -1);	
		}
	}
	return $retorno;
}
function formataGerados($array){
	$sql = "SELECT T166_Titulos_Gerados FROM T166 WHERE T166_Id='{$array['todosCampos']['T166_Id']}'";
	//return $sql;
	$result = mysql_query($sql);
	$retorno = "";
	if(mysql_num_rows($result) > 0){
		$linha = mysql_fetch_array($result);
		$res=explode("\n", $linha['T166_Titulos_Gerados']);
		foreach ($res as $elemento){
			if ($elemento!=""){
				$part=explode(";", $elemento);
				// posição 1 do array refere-se ao T002_Numero_Duplicata
				$retorno.=$part[1]." - ";
			}
		}
		while ((substr($retorno,-1,1)==" ") || (substr($retorno,-1,1)=="-")){
			$retorno=substr_replace($retorno, '', -1);	
		}
	}
	return $retorno;
}

function fin001CampoStatus($array){
    if($array['todosCampos']['T002_Flag_Status'] == '2'){
        return "Perdido";
    }else{
        return "Normal";
    }
}

function validaCampoTituloConferido($array){
	global $g;
    $T002_Id = $array['todosCampos']['T002_Id'];    
    $retorno = "<input onclick=\"fin001MarcarVisto(this, '{$T002_Id}', '{$g['divId']}')\" type='checkbox' id='T002_Flag_Titulo_Conferido'";
    if($array['todosCampos']['T002_Flag_Titulo_Conferido'] == 'S'){
        $retorno .="checked='checked'";
    }
    return $retorno.=">";
}

function campoImprimir($array){
	global $g;
	$T002_Id = $array['todosCampos']['T002_Id'];
	$imagem = <<<EOT
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Carta', '/fin/fin001/content/fin001ContentCarta/', '&acaoId={$T002_Id}', [250,100]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-mail-closed"  title="Imprimir"></span>
		   </div>
		</span>
EOT;
	$T002 = mysql_query("SELECT * FROM T002 LEFT JOIN D027 ON D027_Id = T002_D027_Id WHERE T002_Id = '{$T002_Id}' AND D027_Portador LIKE '%HOMOLOGAÇÃO%'");
	if(mysql_num_rows($T002) > 0){
		return $imagem;
	} else {
		return '';
	}
}	

function campoCancelar($array){
	global $g;
	$T117_T002_Id = $array['todosCampos']['T117_T002_Id'];
	$sqlT002 = mysql_query("SELECT T002_T117_Id FROM T002 WHERE T002_Id = '{$T117_T002_Id}'");
	$T002 = mysql_fetch_array($sqlT002);
	if ($array['todosCampos']['T117_Flag_Cancelado']=='N')
	{
		//No antigo sistema: <a href=\"javascript:cancelar_boleto('$mT117[T117_Id]');\" style='color:blue;text-decoration:none;'>";
		return "<a href=\"#\" onclick=\"fin005CampoCancelar('{$array['todosCampos']['T117_Id']}', '{$g['divId']}')\">Cancelar</a>";
	} else {
		if($T117_T002_Id > 0 && $T002['T002_T117_Id'] == 0){
			return "<a href=\"#\" onclick=\"fin005CampoDesfazerCancelamento('{$array['todosCampos']['T117_Id']}', '{$T117_T002_Id}','{$g['divId']}')\">Restaurar</a>";
		} else {
			return "Cancelado";
		}
	}
}

function campoAtualizarVencimento($array){
	global $g;
	if ($array['todosCampos']['T117_Flag_Cancelado']=='S') {
		return "Cancelado";
	} else if($array['todosCampos']['T002_Data_Recebimento'] != '0000-00-00' && !empty($array['todosCampos']['T002_Data_Recebimento'])){
		return 'Pago';
	} else {
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar Vencimento Boleto', '/fin/fin005/content/fin005ContentAtualizarVencimento/', '&T117_Id=' + encodeURIComponent('{$array['todosCampos']['T117_Id']}'), [450,350]);\">Atualizar Vencimento</a>";
	}
}

function campoAbrirDocumento($array){
	$id = $array['todosCampos']['T242_Id'];
	$retorno = "<a href=\"/fin/fin001/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
	return $retorno;
}

// Imagem para exclusão da linha
function excluirRetorno($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="fin001excluirRetorno('{$array['todosCampos']['T037_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}

function fin001BaixarRetorno($array){
	global $g;
	return "<a href=\"/fin/fin001/grid_func-ajax/downloadRetorno/?T037_Id={$array['todosCampos']['T037_Id']}&ajax=true\">
				<span title=\"Baixar Retorno\">Baixar</span>
	        </a>";
}

function fin001campoMarcarTitulosEnviar($array) {
	global $g;
	$T002_Id = $array['todosCampos']['T002_Id'];
	if($array['todosCampos']['T002_Valor_Saldo'] > 0){
		$retorno = "<span style='display:block;'><input onclick=\"fin001MarcarBoletoAtualizar(this, '{$T002_Id}', '{$g['divId']}')\" type='checkbox' id='T002A_Flag_Enviar_Email' title='Marcar para enviar email'";
		if ($array['todosCampos']['T002A_Flag_Enviar_Email'] == 'S'){
		    $retorno .="checked='checked'";
		}
		return $retorno .= " /></span>";
	} else {
		return '';
	}
}

function fin001CampoeMail($array){
	global $g;

	if ($array['todosCampos']['T002A_Data_Envio_Email'] == '0000-00-00 00:00:00') {
		return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&emailTituloReceber=S&T002_Id=' + encodeURIComponent('{$array['todosCampos']['T002_Id']}') + '&D024_Id=' + encodeURIComponent('{$array['todosCampos']['T002_D024_Id']}'), [900,550]);\"><img src='/hardness3/static/img/tmp/icone_enviar.gif' /></a>";
	}else{
		return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&emailTituloReceber=S&T002_Id=' + encodeURIComponent('{$array['todosCampos']['T002_Id']}') + '&D024_Id=' + encodeURIComponent('{$array['todosCampos']['T002_D024_Id']}'), [900,550]);\"><span class='ui-icon ui-icon-check' title='E-mail enviado em: ".gCorrigeData($array['todosCampos']['T002A_Data_Envio_Email'])."''> </span></a>";
	}
	
}

/**
 * Seleção dos clientes que terão o saldo de crédito troca processado.
 * O tipo da operação é definido pelo sinal do saldo e validado novamente
 * no servidor antes de qualquer lançamento financeiro.
 */
function fin001SelecionarSaldoCreditoTroca($array){
	$saldo = round((float)$array['todosCampos']['Saldo_Em_Aberto'], 2);
	if (abs($saldo) < 0.01) {
		return '&nbsp;';
	}

	$tipo = ($saldo > 0) ? 'abatimento' : 'ajuste';
	$titulo = ($saldo > 0)
		? 'Selecionar para zerar o saldo positivo'
		: 'Selecionar para ajustar o saldo negativo legado';
	$id = (int)$array['todosCampos']['D024_Id'];

	return "<input type='checkbox' class='fin001CreditoTrocaSelecionar' data-cliente-id='{$id}' data-tipo='{$tipo}' title='{$titulo}' onclick='event.stopPropagation();' />";
}

function fin001SelecionarAbaterSaldoCreditoTroca($array){
	$saldo = round((float)$array['todosCampos']['Saldo_Em_Aberto'], 2);
	if ($saldo < 0.01) return '&nbsp;';
	$id = (int)$array['todosCampos']['D024_Id'];
	return "<input type='checkbox' class='fin001CreditoTrocaSelecionarAbatimento' data-cliente-id='{$id}' title='Selecionar cliente para abater o saldo em um título' onclick='event.stopPropagation();' />";
}

function fin001SelecionarTituloAbaterCreditoTroca($array){
	$id = (int)$array['todosCampos']['T002_Id'];
	return "<input type='checkbox' class='fin001TituloAbaterCreditoTroca' data-titulo-id='{$id}' title='Selecionar título para abatimento' onclick='event.stopPropagation();' />";
}
