<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

function mostrarImagemAvisoLinhas($array){
	global $g, $confUsuario;
	
	$sqlC032 = "SELECT C032_Imagem FROM C032 WHERE C032_Id='{$array['todosCampos']['C032_Id']}'";
	//echo $sqlC032."<br />";
	$resultC032 = mysql_query($sqlC032);
	$linhaC032 = mysql_fetch_array($resultC032);
    
    if (empty($linhaC032['C032_Imagem'])) {
        $dir = "/hardness3/static/img/indisponivel100x100.jpg";
        $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
    }else{
	    $linhaC032['C032_Imagem'] = explode(".", $linhaC032['C032_Imagem']);
	    $dir = "/hardness3/{$g['pathDados']}imagens/avisos/{$linhaC032['C032_Imagem'][0]}.{$linhaC032['C032_Imagem'][1]}";
        $dirFull = $confUsuario['urlRaiz'] . "hardness3/{$g['pathDados']}imagens/avisos/{$linhaC032['C032_Imagem'][0]}.{$linhaC032['C032_Imagem'][1]}";
	}
	log("DIR".$dir);
    return <<<EOT
        <img src="{$dir}" height="100" width="100"/>
EOT;
	//echo $dir."<br />";
	
	
}