<?php
namespace hardness;
require_once('bibliotecas/classes/EST005.php');
$EST005 = new EST005();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
	/**
	* @author AndreySliwinski
	* @since 29/11/2011
	*/
switch ($g['r'][3]) {
	case 'abreXML':
		global $g;
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		
		$sql = 'SELECT T084_Numero_Protocolo_Cte, T084_Chave_Acesso_Cte from T084 WHERE T084_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);		
		
		$Chave_Acesso = $array['T084_Chave_Acesso_Cte'];
		$Protocolo = $array['T084_Numero_Protocolo_Cte'];
		$Diretorio_CTe = $g['pathDadosAntigo'] . "cte/xml/recebido/" . $Chave_Acesso . "-" . $Protocolo . ".xml";
		//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
		$cArquivo="$Diretorio_CTe";
		
		if (file_exists($cArquivo)){
		    // We'll be outputting a PDF
		    header('Content-type: text/xml');
		
		    // It will be called downloaded.pdf
		    header('Content-Disposition: attachment; filename="'.$array['T084_Numero_Protocolo_Cte'].'.xml"');
		
		    // The PDF source is in original.pdf
		    readfile($cArquivo);
		}
		else{
		   echo "XML inexistente";
		}
		die();
	break;

	case 'CampoCancelar':
		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;
		
		// irá retornar TRUE ou uma mensagem de erro
		$retorno = $EST005->campoCancelar($T084_Id);	

		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'CampoExcluir':
		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;
		
		// irá retornar TRUE ou uma mensagem de erro
		$retorno = $EST005->campoExcluir($T084_Id);	

		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>