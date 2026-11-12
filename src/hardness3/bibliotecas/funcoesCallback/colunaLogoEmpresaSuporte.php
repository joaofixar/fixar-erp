<?php
namespace hardness;

function colunaLogoEmpresaSuporte($array, $param){
	global $g;
        
    if($array['todosCampos']['T252_C007_Id_Cliente'] > 0 && $array['todosCampos']['T252_C007_Id'] <= 0){
		if (!empty($array['todosCampos']['T234_Logo_Empresa'])) {
    		$caminhoImagem = $array['todosCampos']['T234_Logo_Empresa'];
    	} else {
    		$caminhoImagem = '';
    	}
    } else {
    	$caminhoImagem = "http://hardness.hardness.com.br/hardness3/dados_usuarios/e001/imagens/logo/1.jpg";
    }
    $caminhoImagem = "<img src='{$caminhoImagem}' style='max-width: 100px; max-height: 100px; horizontal-position: center;'>";
    return $caminhoImagem;
}

