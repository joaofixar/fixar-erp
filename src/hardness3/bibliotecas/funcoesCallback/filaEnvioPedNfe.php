<?php
namespace hardness;

function filaEnvioPedNfe($array){
	global $g;
	$T007_Id=$array['todosCampos']['T007_Id'];
	$T007_T005_Id = $array['todosCampos']['T007_T005_Id'] ? $array['todosCampos']['T007_T005_Id'] : '-';
	$T007_Numero_Nota_Fiscal = $array['todosCampos']['T007_Numero_Nota_Fiscal'] ? $array['todosCampos']['T007_Numero_Nota_Fiscal'] : '-';
	$novoIdTelaForm = uniqid();
	$from='T007';
	$retorno = <<<EOT
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: {$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [990,605]);">
			{$T007_Numero_Nota_Fiscal}
		</span>
		<br />
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: {$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [990,605]);">
			{$T007_T005_Id}
		</span>
EOT;
	return $retorno;
}
