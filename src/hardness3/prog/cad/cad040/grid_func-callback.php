<?php
namespace hardness;

function campoCheckboxChecklist($array){
	global $g,$r_acaoId,$D106_Id;

		$retorno = "<input onchange=\"cad040CheckboxChecklist(this, '{$array['todosCampos']['D120_Id']}', '{$r_acaoId}', '{$g['divId']}','{$D106_Id}')\" type='checkbox' id='T159_Flag_Ok' title='Confirmar Item'";
		if ($array['todosCampos']['T159_Flag_Ok']=='S'){
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
	
}
function campoImpressao($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	//onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Recibo Locação', '/cad/cad035/outro/cad035locacaoBensMoveis/','&T156_Id={$array['todosCampos']['T156_Id']}', [1000,700]);	
	$imagem = <<<EOT
		<span onclick="">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
?>
