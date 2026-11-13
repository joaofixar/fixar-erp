<?php
namespace hardness;

require_once('bibliotecas/classes/EST020.php');
require_once('hardness/class/cte/logCTE.php');
$EST020 = new EST020();

$resposta = array('code' => true, 'data' => array());

	switch($g['r'][3]) {    		
    case 'enviaCTe':
		session_write_close();//pesquisar

		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;		
		if(!$T084_Id) die();
		$T084_Id = mysql_real_escape_string($T084_Id); //pesquisar		 													

		//UPDATE INICIANDO PROCESSO
		$sql = "UPDATE T084 SET T084_TX_Resposta_Codigo_Interno = 1, T084_TX_Timestamp_Envio_Sefaz = current_timestamp() WHERE T084_Id = '{$T084_Id}'";
		mysql_query($sql);				
		$retorno = $EST020->enviaCTe($T084_Id);		
		$resposta['code'] = true;	
	break;		
	
    case 'consultaReciboCTe':
		session_write_close();//pesquisar

		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;		
		if(!$T084_Id) die();
		$T084_Id = mysql_real_escape_string($T084_Id); //pesquisar		 											

		//UPDATE INICIANDO PROCESSO
		$sql = "UPDATE T084 SET T084_TX_Resposta_Codigo_Interno = 1, T084_TX_Timestamp_Envio_Sefaz = current_timestamp() WHERE T084_Id = '{$T084_Id}'";
		mysql_query($sql);				
		$retorno = $EST020->consultarRecibo($T084_Id);		
		$resposta['code'] = true;	
	break;

    case 'recuperaDuplicidadeCTe':
		session_write_close();

		$T084_Id = isset($r_T084_Id) ? mysql_real_escape_string($r_T084_Id) : false;
		if (!$T084_Id) die();
		$retorno = $EST020->recuperarDuplicidadeCTe($T084_Id);
		$resposta['code'] = $retorno[0];
		$resposta['data'] = $retorno[1];
	break;

	case 'consultaStatusCTe':

		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;
		if(!$T084_Id) die();

		$T084_Id = mysql_real_escape_string($T084_Id); //pesquisar		 

		$sql = "SELECT T084_Chave_Acesso_Cte,
					T084_TX_Resposta_Codigo_Interno,
					T084_TX_Retorno_Codigo,
					T084_TX_Retorno_Motivo ,
					T084_Flag_Denegado 
				FROM T084 
			WHERE T084_Id = '{$T084_Id}'";

		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		
		
		$resposta["T084_TX_Resposta_Codigo_Interno"] = $row['T084_TX_Resposta_Codigo_Interno'];
		if ($resposta['T084_TX_Resposta_Codigo_Interno'] >= 100 && $resposta['T084_TX_Resposta_Codigo_Interno'] < 200) {
			$resposta['codigoTXT'] = "<b>".logCTE::$codigos[$row['T084_TX_Resposta_Codigo_Interno']]. "</b><br>" . $row['T084_TX_Retorno_Motivo'];
		} else {
			$resposta['codigoTXT'] = logCTE::$codigos[$row['T084_TX_Resposta_Codigo_Interno']];
		}

		$resposta['T084_TX_Retorno_Codigo'] = $row['T084_TX_Retorno_Codigo'];
		$resposta['T084_TX_Retorno_Motivo'] = $row['T084_TX_Retorno_Motivo'];		
		
		if($resposta['T084_TX_Resposta_Codigo_Interno']  == 202) {
			$resposta['erro'] = " <strong>Codigo Interno:</strong><br /> {$resposta['T084_TX_Resposta_Codigo_Interno']} - {$resposta['codigoTXT']}<br /><br /> <strong>Erro Sefaz:</strong><br /> ({$resposta['T084_TX_Retorno_Codigo']}) <br /><br /> <strong>Retorno Sefaz:</strong><br /> {$resposta['T084_TX_Retorno_Motivo']}<br />";
		}				
	break;

	case 'fechar':
		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;
		if (!$T084_Id) { die(); }
		$T084_Id = mysql_real_escape_string($T084_Id);
		$T084_TX_Flag_Finalizada = isset($r_T084_TX_Flag_Finalizada) ? $r_T084_TX_Flag_Finalizada : 1;
		mysqli_query("UPDATE T084 SET T084_TX_Flag_Finalizada = '{$T084_TX_Flag_Finalizada}' WHERE T084_Id = '{$T084_Id}';");
	break;

	case 'baixaXML':
		global $g;
		$id = (isset($r_acaoId)) ? $r_acaoId : false;
		
		$sql = 'select T084_Chave_Acesso_Cte, T084_Numero_Protocolo_Cte, T084_C004_Id, T084_Data_Emissao, T084_Arquivo_XML from T084 WHERE T084_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);


		$anoMesCte=substr($array['T084_Data_Emissao'],0,7);
		$diretorioCte=$g['pathDadosAntigo'] . 'cte';
		$arquivo = "$diretorioCte/xml/autorizado/{$anoMesCte}/".$array['T084_Numero_Protocolo_Cte'].".xml";

		if (file_exists($arquivo)){
			die("O arquivo XML autorizado já existe na pasta");
		}
		if (empty($array['T084_Arquivo_XML'])){
		    die("O campo Arquivo XML está vazio este registro");
		}
				
		//Abre arquivo para gravação
		$fp = fopen($arquivo, "a+");

		if (!$fp){
			die("ERRO criando arquivo $arquivo <br><br>" . $array['T084_Arquivo_XML']);
		}

		//Escreve no arquivo aberto.
		$retorno = fwrite($fp, $array['T084_Arquivo_XML']);

		if (!$retorno){
			die("ERRO gravando conteúdo arquivo $arquivo <br><br>" . $array['T084_Arquivo_XML']);
		}

		//Fecha o arquivo.
		$retorno = fclose($fp);

		if ($retorno){

			die("Gravado XML: $arquivo <br> " . $array['T084_Arquivo_XML']);

		} else {

			die("ERRO! fechando arquivo $arquivo <br> " . $array['T084_Arquivo_XML']);
		}

	break;
	case 'abreXML':
		global $g;
		$id = (isset($r_acaoId)) ? $r_acaoId : false;		
		
		$sql = 'select T084_Numero_Protocolo_Cte, T084_Data_Emissao from T084 WHERE T084_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);
		
		
		$anoMesCte=substr($array['T084_Data_Emissao'],0,7);
		$diretorioCte=$g['pathDadosAntigo'] . 'cte';
		//-- $Diretorio_Usuario definido no arquivo include/pasta_usuario.inc
		$cArquivo="$diretorioCte/xml/autorizado/{$anoMesCte}/".$array['T084_Numero_Protocolo_Cte'].".xml";
		
		if (file_exists($cArquivo)){		    
		    header('Content-type: text/xml');				    
		    header('Content-Disposition: attachment; filename="'.$array['T084_Numero_Protocolo_Cte'].'.xml"');				    
		    readfile($cArquivo);
		}
		else{
		   echo "XML Inexistente";
		}
		die();
	break;

	case 'abreXMLAssinado':
		global $g;
		$id = (isset($r_acaoId)) ? $r_acaoId : false;
		
		$sql = 'select T084_Chave_Acesso_Cte, T084_Data_Emissao from T084 WHERE T084_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);
				
		$diretorioCte=$g['pathDadosAntigo'] . 'cte';
		//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
		$cArquivo="$diretorioCte/xml/assinado/CTe".$array['T084_Chave_Acesso_Cte'].".xml";		
		if (file_exists($cArquivo)){
		    // We'll be outputting a PDF
		    header('Content-type: text/xml');
		
		    // It will be called downloaded.pdf
		    header('Content-Disposition: attachment; filename="CTe'.$array['T084_Chave_Acesso_Cte'].'.xml"');
		
		    // The PDF source is in original.pdf
		    readfile($cArquivo);
		}
		else{
		   echo "XML Inexistente";
		}
		die();
	break;

	case 'excluirCTe':		
		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;
		$retorno = $EST020->excluirCTe($T084_Id);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "CT-e excluído com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	case 'est020CheckboxEnviarCTe':		
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;		
		$T084_Id = isset($r_T084_Id) ? $r_T084_Id : false;
		$usuarioAtual = ($selected == 'S') ? $g['usuarioAtual'] : "";
		mysql_query("UPDATE T084 
						SET T084_Flag_Enviar = '{$selected}',
							T084_C007_Id_Marcou_Enviar = '{$usuarioAtual}'
						WHERE T084_Id = '{$T084_Id}' AND T084_C004_Id = '{$g['empresaAtual']}'");
		$resposta['code'] = true;
	break;	

	case 'est020EnviarLote':		
		global $g;		 

		mysql_query("LOCK T084");
		mysql_query("START TRANSACTION");
		$T084 = mysql_query("SELECT MAX(T084_Numero_Lote)  as NumeroLote
								FROM T084 
								WHERE T084_Flag_Entrada_Saida = 'S'
								AND T084_C004_Id = '{$g['empresaAtual']}'");
		$mT084 = mysql_fetch_array($T084);		
		$numeroLote = (int)$mT084['NumeroLote'] + 1;				

		mysql_query("UPDATE T084 SET T084_Numero_Lote = '{$numeroLote}' 
						WHERE  T084_Flag_Enviar = 'S' AND T084_Flag_Entrada_Saida = 'S'  AND T084_C004_Id = '{$g['empresaAtual']}' AND T084_C007_Id_Marcou_Enviar = '{$g['usuarioAtual']}'");		
		$cErro = mysql_error();
		if(!empty($cErro)){
			mysql_query("ROLLBACK");
			return "Erro ao atualizar o número do LOTE: ".$cErro;        
		} 

		mysql_query("COMMIT");
		mysql_query("UNLOCK ALL");
		$retorno = $EST020->enviarLoteCTe($numeroLote);		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Lote transmitido com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
