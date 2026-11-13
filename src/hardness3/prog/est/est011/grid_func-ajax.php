<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'est011CheckboxMarcado':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T182_Id = isset($_REQUEST['T182_Id']) ? $_REQUEST['T182_Id'] : false;
		mysql_query("update T182 set T182_Marcado='$selected' where T182_Id='$T182_Id'");	
	break;		

	case 'abreDocumentoRMA':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T271_T182_Id, T271_Nome_Arquivo from T271 WHERE T271_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T271_Nome_Arquivo']);
		$cArquivo =  "{$g['pathDados']}RMA/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
			header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
			header('Content-Disposition: attachment; filename="' . $row['T271_Nome_Arquivo'] . '"');
			readfile($cArquivo);
		}
		else
		{
			echo "$cArquivo";
			return false;
		}
		die();
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>