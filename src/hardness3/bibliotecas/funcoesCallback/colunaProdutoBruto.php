<?php
namespace hardness;

function colunaProdutoBruto($array){
	global $g;
	if($array['campoAtual'] != ''){
		return $array['campoAtual'];
	} else {
		return "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Produto Base', '/cad/cad002/content/cad002contentCadastroProdutoBase/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D009_Id']}'), [560,95]); return false;\">INCLUIR</a>";
	}
}
