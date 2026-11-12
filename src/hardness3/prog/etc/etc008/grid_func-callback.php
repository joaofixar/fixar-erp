<?php
namespace hardness;

require_once('prog/etc/etc008/grid_func-js.php');
/**
* FIN - FIN001 - Contas a Receber
* 
* Funções PHP para callbacks
*/
function validaCampoAts($array){
	if ($array['todosCampos']['Tem_Contas']>0){
         return $array['todosCampos']['Atraso'];
    }else{
         return "-";
    }
}

function validaCampoAcessoLocal($array){
	global $g;
	//return "<a href='#' onclick=\"acessarl(\"$array['todosCampos']['C025_Banco_Dados']\",\"root\",\"root\",\"$array['todosCampos']['C025_Senha']\",\"$array['todosCampos']['D024_Id']\");\">Acessar</a>";
	return <<<EOT
	 
	<a href="#" onclick="acessar1('{$array['todosCampos']['C025_Banco_Dados']}', 
										  'root', 
										  'root', 
										  '{$array['todosCampos']['C025_Senha']}', 
										  '{$array['todosCampos']['D024_Id']}',
										  '{$g['usuarioAtual']}');">Acessar</a>

EOT;
										  
}

function validaCampoAcessoRemoto($array){
	
	global $g;
	// onclick=\"acessarr('$mC024[C025_Banco_Dados]','root','root','$mC024[C025_Senha]','$mC024[D024_Id]');\">
	
	return <<<EOT
		<a href="#" onclick="acessarr('{$array['todosCampos']['C024_Banco_Dados']}', 
										   'root', 
										   'root', 
										   '{$array['todosCampos']['C025_Senha']}', 
										   '{$array['todosCampos']['D024_Id']}',
										   '{$g['usuarioAtual']}');">Acessar</a>
EOT;
}