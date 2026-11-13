<?php
namespace hardness;
// D001_D003_Id, D003_Imagem, D003_Nome_Linha
function mostrarImagemD003($array){
	global $g, $confUsuario;
	
	if (empty($array['todosCampos']['D003_Imagem'])) {
		$dir = "/hardness3/static/img/indisponivel100x100.jpg";
		$dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
	} else {
		$linhaD003['D003_Imagem'] = explode(".", $array['todosCampos']['D003_Imagem']);
		$dirFull = "/hardness3/{$g['pathDados']}produtos/linhas/{$linhaD003['D003_Imagem'][0]}.{$linhaD003['D003_Imagem'][1]}";
		$dir = "/hardness3/{$g['pathDados']}produtos/linhas/{$linhaD003['D003_Imagem'][0]}-100.{$linhaD003['D003_Imagem'][1]}";
	}
	
    $nomeLinha = $array['todosCampos']['D003_Nome_Linha'];

	return <<<EOT
		<img src="{$dir}" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imagem Original', '/crm/crm001/outro/crm001ImagemOriginal/', '&urlImagem=' + encodeURIComponent('{$dirFull}') + '&nomeLinha=' + encodeURIComponent('{$nomeLinha}'), [800,500]); return false;" title="Ver tamanho original" class="bt_galeria">
EOT;
}