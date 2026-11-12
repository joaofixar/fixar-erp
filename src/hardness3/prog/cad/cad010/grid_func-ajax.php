<?php
namespace hardness;
/**
 * CAD - CAD010 - Cadastro de Bem
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD010.php');
$CAD010 = new CAD010();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$D109_Id = isset($_REQUEST['D109_Id']) ? $_REQUEST['D109_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('D109_Id', $D109_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD010->excluir($D109_Id);
			$resposta['data'] = 'D109_Id-' . $D109_Id;
		}
		break;
		
			case 'excluircompetencia':
		$D115_Id = isset($_REQUEST['D115_Id']) ? $_REQUEST['D115_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('D115_Id', $D115_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD010->excluircompetencia($D115_Id);
			$resposta['data'] = 'D115_Id-' . $D115_Id;
		}
		break;
		
	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select D123_D109_Id, D123_Nome_Arquivo from D123 WHERE D123_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['D123_Nome_Arquivo']);
		$cArquivo = "{$g['pathDados']}documentos/D109_" . str_repeat('0',5-strlen($row['D123_D109_Id'])) . $row['D123_D109_Id']."/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['D123_Nome_Arquivo'] . '"');
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