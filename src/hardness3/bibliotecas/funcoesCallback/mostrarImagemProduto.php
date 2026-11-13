<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

function mostrarImagemProduto($array){
	global $g;
	
	$sqlD003 = "SELECT T172_Nome_Arquivo, T172_Link_Video FROM T172 WHERE T172_Id='{$array['todosCampos']['T172_Id']}'";
	//echo $sqlD003."<br />";
	$resultD003 = mysql_query($sqlD003);
	$linhaD003 = mysql_fetch_array($resultD003);
	$linhaD003['T172_Nome_Arquivo'] = pathinfo($linhaD003['T172_Nome_Arquivo']);

	if(!empty($linhaD003['T172_Link_Video'])){
        return '<i class="material-icons" style="font-size: 150px; color: red;">smart_display</i>'; 
	}else{
		if($linhaD003['T172_Nome_Arquivo']['extension'] == 'pdf' || $linhaD003['T172_Nome_Arquivo']['extension'] == 'PDF'){
			$dir = "/hardness3/static/img/icone_pdf.png";
		} else {
			$dir = "/hardness3/{$g['pathDados']}produtos/{$array['todosCampos']['T172_D001_Id']}/fotos/{$array['todosCampos']['T172_Id']}.{$linhaD003['T172_Nome_Arquivo']['extension']}";
		}
		   return <<<EOT
		<img src="{$dir}" style="max-width:150px;max-height:150px"/>
EOT;
	}
}