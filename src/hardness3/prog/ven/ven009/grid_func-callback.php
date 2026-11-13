<?php
namespace hardness;
/**
* EN - VEN009 - Logística de aluguel
* 
* Funções PHP para callbacks
*/

// Imagem para exclusão da linha
function campoExclusao($array) {
	global $g;
	return <<<EOT
    		<a href="#" onclick="ven009CampoExclusao('{$array['todosCampos']['T143_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	
}

// gerar link para abertura/visualização do contrato de aluguel
function abreContrato($array) {
	global $g;
	
	// por enquanto não vai exibir o contrato, apenas mostrar o ID e o cliente...
	/*
	return <<<EOT
    		<a href="#" onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Contrato de Aluguel ID: ' + {$array['todosCampos']['T143_T141_Id']}, '/ven/ven010/content/ven010content02/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T143_T141_Id']})+'&readOnly=true', [500,260]);">
    			Ver</span>
			</a>	
		
EOT;
	*/
	return $array['campoAtual'] . ' - ' . $array['todosCampos']['D024_Nome_Fantasia'];
	
}
?>