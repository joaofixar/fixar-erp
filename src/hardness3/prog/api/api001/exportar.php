<?php
namespace hardness;

switch ($g['r'][3]) {
	
	case 'API001exportarBI':
		$cArquivo = $_REQUEST['cArquivo'];

		$cArquivo = isset($cArquivo) ? $cArquivo : false;

		//-- Abre a tela de download do arquivo
        header('Content-type: application/x-msdownload');
        header('Content-Disposition: attachment; filename='.basename($cArquivo));
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $handle=fopen($cArquivo, "r");
        $Conteudo=fread($handle, filesize($cArquivo));
        
        echo $Conteudo;

	break;
}

