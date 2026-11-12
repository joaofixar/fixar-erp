<?php
namespace hardness;
// D001_D003_Id, D003_Imagem, D003_Nome_Linha
function mostrarImagemPequenaB2B($array){
	global $g, $confUsuario;

	$Imagem = $g['C031']['ImagemPrincipalB2B'];

	if($Imagem=='1'){
		$D001_Id = $array['todosCampos']['D001_Id'];
		$T172 = mysql_query("SELECT T172_Id,T172_Nome_Arquivo from T172 WHERE T172_D001_Id='{$D001_Id}' ORDER BY T172_Id desc limit 1;");
		$mT172 = mysql_fetch_array($T172);
		$extensao = explode(".", $mT172['T172_Nome_Arquivo']);
		$dirFull = $confUsuario['pathRaiz']."{$g['pathDados']}produtos/".$D001_Id."/fotos/{$mT172['T172_Id']}-100.{$extensao[1]}";
		$dir = "/hardness3/{$g['pathDados']}produtos/".$D001_Id."/fotos/{$mT172['T172_Id']}-100.{$extensao[1]}";
	}
	else if($Imagem=='2')
	{
		$linhaD003['D003_Imagem'] = explode(".", $array['todosCampos']['D003_Imagem']);
		$dirFull = $confUsuario['pathRaiz']."{$g['pathDados']}produtos/linhas/{$linhaD003['D003_Imagem'][0]}.{$linhaD003['D003_Imagem'][1]}";
		$dir = "/hardness3/{$g['pathDados']}produtos/linhas/{$linhaD003['D003_Imagem'][0]}-100.{$linhaD003['D003_Imagem'][1]}";
	}else if ($Imagem=='3'){
		$D001_Id = $array['todosCampos']['D001_Id'];
		$T144 = mysql_query("SELECT T144_Id, T144_Data_Cadastro, D001_Descricao_Produto, T144_Url, T144_Descricao, T144_Ultimo_Acesso, T144_Versao_Cache 
							   FROM T144 
						  LEFT JOIN D001 ON D001_Id=T144_D001_Id 
						      WHERE (T144_D001_Id = '{$D001_Id}') 
						   ORDER BY T144_Id DESC LIMIT 1;");
		$mT144 = mysql_fetch_array($T144);
		$dir = $mT144['T144_Url'];
	}
		
	if ((empty($dir)) || (empty($dirFull) && empty($dir)) || (!empty($dirFull) && !file_exists($dirFull))) {
    	$dir = "/hardness3/static/img/indisponivel200x200.jpg";
	}
	
	return "{$dir}";
}