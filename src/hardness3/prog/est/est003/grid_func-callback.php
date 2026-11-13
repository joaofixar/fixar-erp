<?php
namespace hardness;
	/**
	* Weiberlan
	* 12/11/2011
	*/
// Carrega funções do ajax
require_once('prog/est/est003/grid_func-js.php');

function campoOpcoes($array){
	 	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="est003FaturarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}')">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Faturamento"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="est003RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Imprimir Etiqueta', '/est/est003/content/est123/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}

function OpcoesAguardandoProdCompra($array){
	 	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Faturamento', '/est/est003/content/est123/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Faturamento"></span>
		   </div>
		</span>
EOT;
 	$imagem .= <<<EOT
		<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
		   </div>
		</span>
EOT;
	$imagem .= <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Imprimir Etiqueta', '/est/est003/content/est123/','&T005_Id={$array['todosCampos']['T005_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-tag" title="Imprimir Etiquetas"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}

function campoCFOP($array){
	$retorno = "{$array['todosCampos']['D006_Codigo_CFOP']} - ({$array['todosCampos']['D006_Descricao_Comercial']})";
	return $retorno;
}

function ValidaCampoDanfe($array){
	$retorno = "";
	$retorno = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T007_Id']}');\" style='cursor:pointer'>DANFE</a>";
	/**
	* onClick dentro de <a>
	* onclick=\"javascript:abre_doc('../almoxarifado/gerar_danfe.php?T007_Id=$mT005[T007_Id]','NF $mT005[T007_Id]');\"
	*/
	return $retorno;
}

function validaCampoXML($array){
	if (!empty($array['todosCampos']['T007_Chave_Acesso_Nfe']) and $array['todosCampos']['T007_Flag_ACP']==6){
		$retorno = "";
		$retorno = "<a title='Download do arquivo XML' href=\"/ven/ven003/grid_func-ajax/abreXML/?ajax=true&acaoId={$array['todosCampos']['T007_Id']}\">XML</a>";
        /*if (!empty($mT005[T007_Numero_Protocolo_Nfe]))
        {
        if (file_exists("$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$mT005[T007_Numero_Protocolo_Nfe].".xml"))
        {
        $retorno = "<font class=fonte><u>XML $mT005[T007_Versao_Nfe]</u></font>";
        }
        else
        {
        $retorno = "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>Erro!</font>";
        }
        }*/
      	return $retorno;
		}
}
function validaCampoEmail($array){
		$retorno = "";
		if(!empty($array['todosCampos']['T007_Chave_Acesso_Nfe']) and $array['todosCampos']['T007_Flag_ACP']==6){
			/**
			* onclick=\"javascript:parent.AbrirFormularioEmail('$mT005[T007_Id]')\"
			*/
			if($array['todosCampos']['T007_Data_Envio_XML']!='0000-00-00 00:00:00' and $array['todosCampos']['T007_Data_Envio_XML']!=''){
			$retorno = "<a href='#'><li title='E-mail enviado em: '".$array['todosCampos']['T007_Data_Envio_XML']."><span class='ui-icon ui-icon-check'></span></li></a>";
				/**
				* No attr title do <a> acima, era chamada a seguinte função
				* title='E-mail enviado em: ".troca_datahora_dma($mT005[T007_Data_Envio_XML])."
				*/
				return $retorno;
			}else{
				$retorno = "<a href=\"#\"><li title=\"Enviar DANFE e XML por E-mail\"><span class=\"ui-icon ui-icon-mail-closed\"></span></li></a>";
				return $retorno;
				}
	}
}

function validaCampoCancelar($array){
		if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
			$retorno = "";
			$retorno = "<u>Cancelada</u>";
			return $retorno;
		}else{
			$retorno="";
			$retorno="<a href=\"#\" onclick=\"javascript:cancela_nfe('/hardness/nota_emitida/formulario_cancelar_nota.php?T007_Id={$array['todosCampos']['T007_Id']}','NF {$array['todosCampos']['T005_Id']}');\" style='cursor:pointer'>Cancelar</a>";

			/**
			* onClick dentro de <a>
			* onclick=\"javascript:cancela_nfe('../nota_emitida/formulario_cancelar_nota.php?T007_Id=$mT005[T007_Id]','NF $mT005[T005_Id]');\"
			*/
			return $retorno;
		}
	}
	
function campoOC($array){
		 if ($array['todosCampos']['OC']>0)
         {
		 	$retorno = number_format($array['todosCampos']['OC'],2,',','.');
         }
         else
		 {
         	$retorno = "&nbsp;";
			}
		 return $retorno;
}

function campoSeparado($array){
		$retorno = number_format($array['todosCampos']['Percentual_Separado'],2,',','.')."%";

		return $retorno;
}

function campoSeparadoGeral($array){
		$retorno = number_format($array['todosCampos']['D009_Quantidade_Estoque'] - $array['todosCampos']['Separado'],2);
		return $retorno;
}

 function campoStatus($array){
	if($array['todosCampos']['T006_Flag_Tipo_Produto'] == 'AA'){
		$retorno = "SP"; 
	}elseif($array['todosCampos']['T006_Flag_Tipo_Produto'] == 'CP'){
		$retorno = "CP"; 
	}elseif($array['todosCampos']['T006_Flag_Tipo_Produto'] == 'MD'){
		$retorno = "SV";
	}
		return $retorno;
}
function campoDE($array){
	global $g;
	$novoIdTelaForm = uniqid();
	
	if ($array['todosCampos']['T001_Id']>0){
		return "Id:{$array['todosCampos']['T001_Id']}"; 
	}else {
		return "<input type='button' value='D.E.' style='width:30px; cursor:pointer' onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Divergência de Estoque', '/est/est003/content/est003contentIncluirDE/','&T006_Id={$array['todosCampos']['T006_Id']}', [550,300]);\">";
	}
}

function tipoDivergencia($array) {
	if ($array['campoAtual'] == 1) {
		return "Locação não encotrada.";
	} else if ($array['campoAtual'] == 2) {
		return "Quantidade insuficiente.";
	} else if ($array['campoAtual'] == 0) {
		return '';
	}else if ($array['campoAtual'] == 3) {
		return "Local não cadastrado.";
	} else {
		return $array['campoAtual'];
	}
}

function validaCampoComprar($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$T006_Id = $array['todosCampos']['T006_Id'];
	$D009_Id = $array['todosCampos']['D009_Id'];
	return "<input type='button' value='Comprar' style='width:50px; cursor:pointer' onclick=\"est003RequisitarCompra('{$T006_Id}', '{$D009_Id}', '{$g['divId']}', '{$g['divIdRoot']}');\">";
}

function mostrarLocacao($array) {
	return $array['todosCampos']['D004_Local'];
}
 
?>
