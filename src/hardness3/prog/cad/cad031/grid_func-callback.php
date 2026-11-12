<?php
namespace hardness;
/**
 * Raquel Pazini
 * 01/03/2019
*/
function campoAbrirAviso($array){
	$id = $array['todosCampos']['C032_Id'];
	$retorno = "<a href=\"/cad/cad031/grid_func-ajax/campoAbrirAviso/?ajax=true&acaoId={$id}\">Abrir</a>"; 
	return $retorno;
}

/**
 * Marcelo de Paula
 * 05/07/2021
*/
function campoAviso($array){
	global $g, $confUsuario;
	$array['todosCampos']['C032_Aviso'] = str_replace(array('size="1"','size="2"','size="3"','size="4"','size="5"','size="6"'), 'style="font-size:13px;"', $array['todosCampos']['C032_Aviso']);
    return <<<EOT
	    <div style='border: 0px; width: 610px; max-width: 610px; white-space: normal; margin: 5px; margin-left: 6px; margin-bottom:0px; height: 120px; padding: 10px; background: white; border-radius: 10px; background-image: url("{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/avisos/{$array['todosCampos']['C032_Imagem']}");background-repeat: no-repeat;background-size: 630px 140px;'>
			{$array['todosCampos']['C032_Aviso']}
		</div>
EOT;
}

/**
 * Marcelo de Paula
 * 05/07/2021
*/
function campoPerfil($array)
{
	global $g;

	$C036 =  mysql_query("SELECT C004_Nome_Abreviado, C021_Nome_Perfil,C007_Primeiro_Nome,C036_Data_Inicial,C036_Data_Final
						    FROM C036
					   LEFT JOIN C004 ON C004_Id=C036_C004_Id
					   LEFT JOIN C021 ON C021_Id=C036_C021_Id
					   LEFT JOIN C007 ON C007_Id=C036_C007_Id
					       WHERE C036_C032_Id='{$array['todosCampos']['C032_Id']}'");
	$retorno = <<<EOT
		<style>
			.avisoPerfil .cab 
			{
				background-color:#4F5F6E;
				color: white;
				border-radius: 2px;
			}
			.avisoPerfil > div 
			{
				padding: 3px;
			}
		</style>

		<div class='avisoPerfil'style='display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; grid-gap: 2px;'>

			<div class='cab'>Empresa</div>
			<div class='cab'>Perfil</div>
			<div class='cab'>Usuário</div>
			<div class='cab'>Período</div>
EOT;

	while ($mC036 = mysql_fetch_assoc($C036)){

		$mC036['C036_Data_Inicial'] = gCorrigeData($mC036['C036_Data_Inicial']);
		$mC036['C036_Data_Final']   = gCorrigeData($mC036['C036_Data_Final']);

		$retorno .= <<<EOT
			<div>{$mC036['C004_Nome_Abreviado']}</div>
			<div>{$mC036['C021_Nome_Perfil']}</div>
			<div>{$mC036['C007_Primeiro_Nome']}</div>
			<div>{$mC036['C036_Data_Inicial']} {$mC036['C036_Data_Final']}</div>
EOT;
	}

	$retorno .= <<<EOT
		</div>
EOT;

return $retorno;


}


?>