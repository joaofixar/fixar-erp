<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

function mostrarImagemProdutoCatalogo($array){
	global $g;
	
	$sqlD003 = "SELECT T172_Id,T172_Nome_Arquivo FROM T172 WHERE T172_D001_Id='{$array['todosCampos']['D001_Id']}' limit 1";
	//echo $sqlD003."<br />";
	$resultD003 = mysql_query($sqlD003);
	$linhaD003 = mysql_fetch_array($resultD003);
	$linhaD003['T172_Nome_Arquivo'] = pathinfo($linhaD003['T172_Nome_Arquivo']);
	$dir = "/hardness3/static/img/indisponivel100x100.jpg";
	if($linhaD003['T172_Nome_Arquivo']['extension'] == 'pdf' || $linhaD003['T172_Nome_Arquivo']['extension'] == 'PDF'){
		$dir = "/hardness3/static/img/icone_pdf.png";
	} else if ($linhaD003['T172_Id']>0){
    	$dir = "/hardness3/{$g['pathDados']}produtos/{$array['todosCampos']['D001_Id']}/fotos/{$linhaD003['T172_Id']}.{$linhaD003['T172_Nome_Arquivo']['extension']}";
	}
	return <<<EOT
    <img src="{$dir}" style="max-width:150px;max-height:150px"/>
EOT;
}