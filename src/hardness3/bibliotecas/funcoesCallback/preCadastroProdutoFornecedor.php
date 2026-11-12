<?php
namespace hardness;

function preCadastroProdutoFornecedor($array, $tabela){
	global $g;
	if($array['todosCampos']['D009_Id'] <= 0){
		$acaoId = $array['todosCampos'][$tabela.'_Id'];
		$array['todosCampos']['D069_Descricao_Produto'] = base64_encode($array['todosCampos']['D069_Descricao_Produto']);		
		return <<<EOT
			<a href="#" onclick="abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Pré Cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&{$tabela}_Id=' + encodeURIComponent('{$acaoId}') + '&descricaoProduto=' + encodeURIComponent('{$array['todosCampos']['D069_Descricao_Produto']}') + '&D037_Id=' + encodeURIComponent('{$array['todosCampos']['D037_Id']}') + '&divForm=' + encodeURIComponent('{$g['divIdParent']}') + '&D005_Id=' + encodeURIComponent('{$array['todosCampos']['D005_Id']}') + '&D069_Id=' + encodeURIComponent('{$array['todosCampos']['D069_Id']}') + '&NCM=' + encodeURIComponent('{$array['todosCampos']['NCM']}') + '&Marca=' + encodeURIComponent('{$array['todosCampos']['Marca']}') + '&Unidade=' + encodeURIComponent('{$array['todosCampos']['Unidade']}') + '&D069_Flag_Origem_Produto=' + encodeURIComponent('{$array['todosCampos']['D069_Flag_Origem_Produto']}'), [600,430]); return false;">Pré Cadastrar</a>
EOT;
	} else {
		return $array['todosCampos']['D001_Codigo_Produto'];
	}
}