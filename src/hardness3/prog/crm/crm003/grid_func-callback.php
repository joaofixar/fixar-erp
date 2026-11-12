<?php
namespace hardness;
/**
 * FIN - FIN002 - Contas a Pagar
 */

function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="fin002CampoExclusao('{$array['todosCampos']['T015_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}

function campoExclusaoPag($array) {
	global $g;
	if ($array['todosCampos']['T007_Id'] > 0){
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
    	<a href="#" onclick="fin002CampoExclusaoAd('{$array['todosCampos']['T033_Id']}','{$array['todosCampos']['T033_T015_Id']}', '{$g['divId']}')">
    		<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}

// Valida o campo BX
function validaCampoBx($array){
	global $g;
	if (empty($array['todosCampos']['T015_Data_Pagamento']) || ($array['todosCampos']['T015_Data_Pagamento']=='0000-00-00'))	{
		$retorno = "<input onchange=\"fin002CheckboxBx(this, '{$array['todosCampos']['T015_Id']}', '{$g['divId']}')\" type='checkbox' id='T015_Flag_Baixar_Lote' title='Baixar Título'";

		if ($array['todosCampos']['T015_Flag_Baixar_Lote']=='S'){
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	}else{

		$T015_DP = $array['todosCampos']['T015_Data_Pagamento'];
		return "<a href=\"#\">
		<span class=\"ui-icon ui-icon-closethick\" title=\"Estornar título\" onClick=\"fin002ValidaCampoBxEstorno('{$array['todosCampos']['T015_Id']}','{$T015_DP}', '{$g['divId']}', '{$g['divIdRoot']}') \">
		</span>
			   </a>";
	}
}
function defineDescricao($array){
	if ($array['todosCampos']['T019_Valor_Lancamento']>0)
	return "Estorno de pagamento";
	else
	return 'Pagamento '.$array['todosCampos']['D031_Portador'];
}

/**
 * Campo 'D031_Id' do grid baixar títulos (fin002grid04)
 *
 * @param array $array
 * @param Formulario $form
 */
function baixarTitulosPortador($array, $form) {
	// Define qual valor está seleciona
	foreach ($form->formArray['campos']['baixar_titulos']['T103_D031_Id']['select'] as &$select) {
		if ($select['value'] == $array['campoAtual']) {
			$select['selected'] = true;
		} else {
			unset($select['selected']);
		}
	}
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_D031_Id');
}

/**
 * Campo 'T103_Valor_Recebido' do grid baixar títulos (fin002grid04)
 *
 * @param array $array
 * @param Formulario $form
 */
function baixarTitulosValorRecebido($array, $form) {
	// Define qual valor está seleciona
	$form->formArray['campos']['baixar_titulos']['T103_Valor_Recebido']['value'] = number_format($array['todosCampos']['T103_Valor_Recebido'], 2, ',', '.');
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_Valor_Recebido');
}

/**
 * Campo 'T015_Flag_Baixar_Lote' do grid baixar títulos (fin002grid04)
 *
 * @param array $array
 * @param Formulario $form
 */
function baixarTitulosBx($array, $form) {
	// Define qual valor está seleciona
	$form->formArray['campos']['baixar_titulos']['T015_Flag_Baixar_Lote']['checked'] = ($array['campoAtual'] == 'S') ? true : false;
	$form->processarFormulario();
	return $form->campoCodigoHTML('T015_Flag_Baixar_Lote');
}

function validaCampoBxPag($array){
	global $g;
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

function campoCancelar($array){
	global $g;
	if ($array['todosCampos']['T117_Flag_Cancelado']=='N')
	{
		//No antigo sistema: <a href=\"javascript:cancelar_boleto('$mT117[T117_Id]');\" style='color:blue;text-decoration:none;'>";
		return "<a href=\"#\" onclick=\"fin005CampoCancelar('{$array['todosCampos']['T117_Id']}', '{$g['divId']}')\">Cancelar</a>";
	}
	else
	{
		return "Cancelado";
	}
}

function campoeMail($array){
	global $g;
	if ($array['todosCampos']['T117_Flag_Cancelado']=='S')
	{
		return "&nbsp;";
	}
	else
	{
		if ($array['todosCampos']['T117_Data_Envio_Email']!='0000-00-00 00:00:00' and $array['todosCampos']['T117_Data_Envio_Email']!='')
		{
			return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T117_Id=' + encodeURIComponent('{$array['todosCampos']['T117_Id']}') + '&D024_Id=' + encodeURIComponent('{$array['todosCampos']['T117_D024_Id']}'), [900,550]);\"><span class='ui-icon ui-icon-check' title='E-mail enviado em: ".gCorrigeData($array['todosCampos']['T117_Data_Envio_Email'])."''> </span></a>";
		}
		else
		{
			return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T117_Id=' + encodeURIComponent('{$array['todosCampos']['T117_Id']}') + '&D024_Id=' + encodeURIComponent('{$array['todosCampos']['T117_D024_Id']}'), [900,550]);\"><img src='/hardness3/static/img/tmp/icone_enviar.gif' /></a>";
		}
	}
}

function fin001CampoStatus($array){
    if($array['todosCampos']['T002_Flag_Status'] == '2'){
        return "Perdido";
    }else{
        return "Normal";
    }
}
