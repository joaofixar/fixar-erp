<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: campoOpcoesConferencia3()
*/

function imprimirMinutaDeDespacho ($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$T007_Id = $array['todosCampos']['T007_Id'];

	$imagem = <<<EOT
		<span onclick="nova_janela('/est/est002/report/minutaDespacho/?ajax=true&T007_Id=$T007_Id',0,0,750,500,'yes','no','no','no','yes','no','Minuta_De_Despacho');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print"  title="Imprimir Minuta de Despacho"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
