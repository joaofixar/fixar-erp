<?php
namespace hardness;
 
$resposta = array('code' => true, 'data' => array());
switch($g['r'][3]) {
	case 'abreDocumento':
		$cArquivo = isset($_REQUEST['cArquivo']) ? $_REQUEST['cArquivo'] : false;
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['T140_Nome_Arquivo'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
			return false;
		}
		die();
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>