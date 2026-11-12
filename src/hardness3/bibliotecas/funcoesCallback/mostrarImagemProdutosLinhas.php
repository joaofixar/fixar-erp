<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

function mostrarImagemProdutosLinhas($array){
	global $g, $confUsuario;
	
	$sqlD003 = "SELECT D003_Imagem FROM D003 WHERE D003_Id='{$array['todosCampos']['D003_Id']}'";
	//echo $sqlD003."<br />";
	$resultD003 = mysql_query($sqlD003);
	$linhaD003 = mysql_fetch_array($resultD003);
    
    if (empty($linhaD003['D003_Imagem'])) {
        $dir = "/hardness3/static/img/indisponivel100x100.jpg";
        $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
    }else{
	    $linhaD003['D003_Imagem'] = explode(".", $linhaD003['D003_Imagem']);
	    $dir = "/hardness3/{$g['pathDados']}produtos/linhas/{$linhaD003['D003_Imagem'][0]}-100.{$linhaD003['D003_Imagem'][1]}";
        $dirFull = $confUsuario['urlRaiz'] . "hardness3/{$g['pathDados']}produtos/linhas/{$linhaD003['D003_Imagem'][0]}.{$linhaD003['D003_Imagem'][1]}";
	}
    $nomeLinha = $array['todosCampos']['D003_Nome_Linha'];
    return <<<EOT
 
        <!-- <a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imagem Original', '/crm/crm001/outro/crm001ImagemOriginal/', '&urlImagem=' + encodeURIComponent('{$dirFull}') + '&nomeLinha=' + encodeURIComponent('{$nomeLinha}'), [800,500]); return false;" title="Ver tamanho original" class="bt_galeria"> -->
            <img src="{$dir}" />
        <!-- </a> -->
EOT;
	//echo $dir."<br />";
	
	
}