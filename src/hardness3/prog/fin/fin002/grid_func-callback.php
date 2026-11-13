<?php
namespace hardness;
/**
 * FIN - FIN002 - Contas a Pagar
 */

function campoExclusao($array) {
	global $g;
	if (isset($g['c029Ids'][111])) {
		return <<<EOT
			<a href="#" onclick="fin002CampoExclusao('{$array['todosCampos']['T015_Id']}', '{$g['divId']}')">
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
		<span style='margin-left: 7px;' class=\"ui-icon ui-icon-closethick\" title=\"Estornar título\" onClick=\"fin002ValidaCampoBxEstorno('{$array['todosCampos']['T015_Id']}','{$T015_DP}', '{$g['divId']}', '{$g['divIdRoot']}') \">
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
	// Define qual valor está selecionado
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
/* function baixarTitulosValorTaxas($array, $form) {
	global $g;
	// Define qual valor está seleciona
	$form->formArray['campos']['baixar_titulos']['T103_Valor_Taxas']['value'] = number_format($array['todosCampos']['T103_Valor_Taxas'], 2, ',', '.');
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_Valor_Taxas');
} */

/**
* Campo 'T103_Valor_Juros' do grid baixar títulos (fin001grid07)
* 
* @param array $array
* @param Formulario $form
*/
function baixarTitulosValorJuros($array, $form) {
	global $g;
	// Define qual valor está seleciona
	$form->formArray['campos']['baixar_titulos']['T103_Valor_Juros']['value'] = number_format($array['todosCampos']['T103_Valor_Juros'], 2, ',', '.');
	$form->processarFormulario();
	return $form->campoCodigoHTML('T103_Valor_Juros');
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

function validaCampoRp($array){
    global $g;
    if($array['todosCampos']['T015_T015_Id_Agrupado'] > 0){
       
        return '';

    } else if($array['todosCampos']['T015_C004_Id'] != $g['empresaAtual']){
        
        $retorno = "<input type='checkbox' name='T015_Flag_Reparcelar' title='Permitido apenas reparcelamento/agrupamento de títulos da mesma empresa' ";
        $empresa = ($array['todosCampos']['T015_C004_Id'] == 0) ? 'Sem Emp' : substr($array['todosCampos']['C004_Nome_Abreviado'], 0,12) ;
        $mensagem = <<<EOT
            <font color='gray' style='font-size:8px;'>{$empresa}</font>
EOT;
        return $retorno .= " disabled='disabled'/><br />{$mensagem}";

    }else if($array['todosCampos']['T015_Data_Pagamento']=='0000-00-00' && $array['todosCampos']['T015_Flag_Cancelada']!='S'){
       
        $retorno = "<input type='checkbox' name='T015_Flag_Reparcelar' title='Reparcelamento / Agrupamento' onChange=\"fin002CheckboxRp(this, '{$array['todosCampos']['T015_Id']}', '{$g['divId']}')\" ";
        
        if ($array['todosCampos']['T015_Flag_Reparcelar']=='S') {
            $retorno .="checked = 'checked'";
        }        
        return $retorno .= " />";

    }else{
        return "&nbsp;";
    }
}
function validaCampoTipo($array){
    if($array['todosCampos']['T196_Tipo'] == "R"){
        return "Reparcelamento";
    }elseif($array['todosCampos']['T196_Tipo'] == "A"){
        return "Agrupamento";
    }
}

function formataGerados($array){
    $sql = "SELECT T196_Titulos_Gerados FROM T196 WHERE T196_Id='{$array['todosCampos']['T196_Id']}'";
    $result = mysql_query($sql);
    $retorno = "";
    if(mysql_num_rows($result) > 0){
        $linha = mysql_fetch_array($result);
        $res=explode("\n", $linha['T196_Titulos_Gerados']);
        foreach ($res as $elemento){
            if ($elemento!=""){
                $part=explode(";", $elemento);
                $retorno.=$part[1]." - ";
            }
        }
        while ((substr($retorno,-1,1)==" ") || (substr($retorno,-1,1)=="-")){
            $retorno=substr_replace($retorno, '', -1);    
        }
    }
    return $retorno;
}

function formataGeradores($array){
    $sql = "SELECT T196_Titulos_Geradores FROM T196 WHERE T196_Id='{$array['todosCampos']['T196_Id']}'";
    $result = mysql_query($sql);
    $retorno = "";
    if(mysql_num_rows($result) > 0){
        $linha = mysql_fetch_array($result);
        $res=explode("\n", $linha['T196_Titulos_Geradores']);
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

function T197_D031_Id_Portador($array) {
    return $array['todosCampos']['D031_Portador'];
}

function fin002_Dia_Semana($array) {
    global $g;
    $data=$array['todosCampos']['T197_Data_Vencimento'];
    return gPegaDiaSemana($data);
}

function validaCampoFlagBoleto($array) {
    global $g;
    $T015_Id = $array['todosCampos']['T015_Id'];    
    $retorno = "<input onclick=\"fin002MarcarVisto(this, '{$T015_Id}', '{$g['divId']}')\" type='checkbox' id='T015_Flag_Boleto'";
    if($array['todosCampos']['T015_Flag_Boleto'] == 'S'){
        $retorno .="checked='checked'";
    }
    return $retorno.=">";
}

function campoAbrirDocumento($array){
    $id = $array['todosCampos']['T243_Id'];
    $retorno = "<a href=\"/fin/fin002/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
    return $retorno;
}

function mostrarPortador($array){
    return $array['todosCampos']['D031_Portador'];
}

