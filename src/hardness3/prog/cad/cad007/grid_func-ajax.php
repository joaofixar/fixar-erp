<?php
namespace hardness;
/**
 * CAD - CAD007 - Cadastro de Certificado
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD007.php');
$CAD007 = new CAD007();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	// -> campoExclusao($array) -> fin002CampoExclusao(T015_Id)
	case 'excluir':
		$T140_Id = isset($_REQUEST['T140_Id']) ? $_REQUEST['T140_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('T140_Id', $T140_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD007->excluir($T140_Id);
			$resposta['data'] = 'T140_Id-' . $T140_Id;
		}
	break;
		
	case 'abreCertificado':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		$sql = 'select T140_Nome_Arquivo from T140 WHERE T140_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T140_Nome_Arquivo']);
		$cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/certificados/{$id}.{$pathinfo['extension']}";
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