<?php
namespace hardness;
/**
* Funções PHP para callbacks
*/

function acoesRegistro($array) {
	global $g, $r_tabela;
	$T162_Registro = unserialize($array['todosCampos']['T162_Registro']);
	$T162_Acao = $array['todosCampos']['T162_Acao'];
	if(empty($r_tabela)){

	}	
	$tabela = empty($r_tabela) ? $array['todosCampos']['T162_Tabela'] : $r_tabela;
	$nome = ($T162_Acao == 'U') ? 'alteração' : 'exclusão';
	if (!empty($T162_Registro)) {
    	return <<<EOT
    		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Visualizar Registro', '/etc/etc007/outro/etc007OutroRegistro/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T162_Id']}') + '&tabela=' + encodeURIComponent('{$tabela}'), [900,550]); return false;">
    			(ver alterações)
			</a>
EOT;
	} else {
		return '&nbsp;';
	}
}

function acaoTipos($array) {
	global $g;
	switch ($array['todosCampos']['T162_Acao']) {
		case 'I': return 'Inclusão'; break;
		case 'U': return 'Alteração'; break;
		case 'D': return 'Exclusão'; break;
		case 'S': return 'Listagem'; break;
	}
}