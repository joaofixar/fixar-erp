<?php
namespace hardness;
	/**
	* Weiberlan
	* 29/11/2011
	*/
require_once('bibliotecas/classes/EST004.php');
$EST004 = new EST004();

$resposta = array('code' => true, 'data' => array());

	switch($g['r'][3]) {

		case 'downloadxml':
			$id = isset($r_T013_Id) ? $r_T013_Id : false;

			$sql = 'select T013_Numero_Protocolo_Nfe, T013_Data_Emissao from T013 WHERE T013_Id ='.$id;
			$result = mysql_query($sql);
			$array = mysql_fetch_assoc($result);


			$cAno_Mes_NFe=substr($array['T013_Data_Emissao'],0,7);
			$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
			//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
			//$cArquivo="$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['T013_Numero_Protocolo_Nfe'].".xml";
			$cArquivo="$Diretorio_NFe/xml/recebido/".$array['T013_Numero_Protocolo_Nfe'].".xml";

			if (file_exists($cArquivo)){
				// We'll be outputting a PDF
				header('Content-type: text/xml');

				// It will be called downloaded.pdf
				header('Content-Disposition: attachment; filename="'.$array['T013_Numero_Protocolo_Nfe'].'"');

				// The PDF source is in original.pdf
				readfile($cArquivo);
			}
			else{
				echo "XML Inexistente";
			}
			die();
		break;
		
		case 'atualizarPedidosVenda':
			$retorno = $EST004->atualizarPedidosVenda();
	
			if ($retorno !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			} else {
				// excluir
				$resposta['data'] = 'Pedidos Venda atualizados com sucesso!';
			}
		break;
		
		case 'atualizarTotal':
			require_once("bibliotecas/classes/EST004.php");
			
			$T014_Id 		= isset($r_T014_Id) ? $r_T014_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			$EST004 = new EST004();
			
			$retorno = $EST004->est004AtualizarTotal($T014_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;
		
		case 'excluirNota':
			require_once("bibliotecas/classes/EST004.php");
			$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;				
			
			$EST004 = new EST004();
			
			$retorno = $EST004->excluirNota($T013_Id);

			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;	
		
		case 'receberNota':
			require_once("bibliotecas/classes/EST004.php");
			$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;				
			
			$EST004 = new EST004();
			
			$T013_Id = 0;
			$retorno = $EST004->receberNota($T007_Id);				

			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['T013_Id'] = $T013_Id;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;			

		case 'atualizarParcelas':
				require_once("bibliotecas/classes/EST004.php");
				
				$T154_Id 		= isset($r_T154_Id) ? $r_T154_Id : false;
				$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
				$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
				$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
				
				
				$EST004 = new EST004();
				
				$retorno = $EST004->est004AtualizarParcelas($T154_Id, $campoEditado, $valorAntigo, $novoValor);
				
				if ($retorno === true) {
					$resposta['code'] = true;
				} else {
					// excluir
					$resposta['code'] = false;
					$resposta['data'] = $retorno;
				}
		break;
		
		case 'est004ConcluirInclusaoItens':
				require_once("bibliotecas/classes/EST004.php");
				
				$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
				
				$EST004 = new EST004();
				
				$retorno = $EST004->est004ConcluirInclusaoItens($T013_Id);
				
				if ($retorno === true) {
					$resposta['code'] = true;
				} else {
					// excluir
					$resposta['code'] = false;
					$resposta['data'] = $retorno;
				}
		break;
			
	}
	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");"
?>