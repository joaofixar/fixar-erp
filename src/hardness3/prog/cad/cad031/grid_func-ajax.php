<?php
namespace hardness;
/**
 *
 * 
 * CAD - CAD031 - Configuração da empresa
 *
 * Processamento de ações AJAX
 */
 
 //Classes

$resposta = array('code' => true, 'data' => array());
 switch($g['r'][3]) {
 	case 'campoAbrirAviso':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'SELECT  C032_Imagem FROM C032 WHERE C032_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['C032_Imagem']);
		$cArquivo = "{$g['pathDados']}imagens/avisos/".$row['C032_Imagem'];
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['C032_Imagem'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.".$cArquivo;
			return false;
		}
		die();
	break;	

 }

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>