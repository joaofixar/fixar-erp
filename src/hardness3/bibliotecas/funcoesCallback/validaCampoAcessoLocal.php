<?php
namespace hardness;
function validaCampoAcessoLocal($array){
	global $g;
	//return "<a href='#' onclick=\"acessarl(\"$array['todosCampos']['C025_Banco_Dados']\",\"root\",\"root\",\"$array['todosCampos']['C025_Senha']\",\"$array['todosCampos']['D024_Id']\");\">Acessar</a>";
	$array['todosCampos']['D024_Link'] = strtolower($array['todosCampos']['D024_Link']);
	if(!empty($array['todosCampos']['D024_Link'])){
		$url = explode("erp", $array['todosCampos']['D024_Link']);
		$url = explode("login", $url[0]);
		$url = $url[0];
		if(strpos($array['todosCampos']['D024_Link'], "redireciona") === false){
			$url .= "/hardness3/outros/login/?login=root&senha=%23hard%403871";
		}
		return <<<EOT
			<a href="#" onclick="window.open('{$url}', '', 'height=650, width=1200, top=20, left=20, scrollbars=no, resizable=no'); ">Acessar</a>
EOT;
	}else{

		return <<<EOT
			<a href="#" onclick="acessar1('{$array['todosCampos']['C025_Banco_Dados']}', 
												  'root', 
												  'root', 
												  '{$array['todosCampos']['C025_Senha']}', 
												  '{$array['todosCampos']['D024_Id']}',
												  '{$g['usuarioAtual']}');">Acessar</a>
EOT;
	}
}


