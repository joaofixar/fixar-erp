<?php
namespace hardness;
switch ($g['r'][3]) {
	
	case 'fis006abrirSintegra':
		$path = isset($r_path) ? $r_path : false;
		
		//$cArquivo_Aberto = fopen( $path, 'x' );
		
		
		//-- Abre a tela de download do arquivo
		header('Content-type: application/x-msdownload');
		header('Content-Disposition: attachment; filename='.basename($path));
		header('Pragma: no-cache');
		header('Expires: 0');
		
		$Ponteiro=fopen($path, "r");
		$Conteudo=fread($Ponteiro, filesize($path));
		
		echo $Conteudo;
	break;
}		
?>