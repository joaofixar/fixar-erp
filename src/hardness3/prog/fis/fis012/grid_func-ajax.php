<?php
namespace hardness;
	/**
	* Raquel
	* 23/11/2018
	*/
require_once('bibliotecas/classes/FIS012.php');
$FIS012 = new FIS012();

include('scripts/queueMDFE/codigos.php');

$resposta = array('code' => true, 'data' => array());

	switch($g['r'][3]) {    

	case 'gerarMDFeCabecalho':
		$T007 = mysql_query("SELECT T007_Id 
			                   FROM T007
			              LEFT JOIN T284 ON T284_T007_Id = T007_Id 
			              LEFT JOIN T276 ON T284_T276_Id = T276_Id
			                  WHERE T007_Flag_Marcado = 'S'
			                   AND (T007_C004_Id='{$g['empresaAtual']}' 
			                    AND T007_Flag_Emitida_Recebida!='R')");
		if(mysql_num_rows($T007) > 0){
			$retorno = $FIS012->gerarMDFeCabecalho($form);
			log("retorno: ".$retorno);
			if (is_array($retorno)) {
				$resposta['code'] = true;
				$resposta['T276_Id'] = $retorno['T276_Id'];
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}else{
			$resposta['code'] = false;
			$resposta['data'] = "Não existem notas selecionadas";
		}
	break;

    case 'txSefazMDFe':
		session_write_close();

		$status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$resposta['status_normal'] = $status[0];
		$resposta['status_scan'] = $status[1];

		$T276_Id = isset($r_T276_Id) ? $r_T276_Id : false;
		$resposta['encerrar'] = (trim($r_encerrar) == 'S')? true: false;
		$resposta['encerrado'] = (trim($r_encerrado) == 'S')? true: false;

		if($resposta['encerrar']){
			$resposta['teste'] = <<<EOT
			<font style='color: var(--BGVermelho)'>UF descarregamento e Cidade Descarregamento devem estar preenchidos.</font>
EOT;
		}
		if (!$T276_Id) { die(); }
		$T276_Id = mysql_real_escape_string($T276_Id);

		$sql = "SELECT T276_Id, 
					   T276_TX_Timestamp_Envio_Sefaz, 
					   T276_TX_Resposta_Codigo, 
					   T276_TX_Resposta_Extra, 
					   T276_TX_Retorno_Sefaz, 
					   T276_TX_Mensagem, 
					   T276_Data_Envio_XML,  
					   T276_Numero_MDFe, 
					   T276_TX_Flag_Finalizada, 
					   T276_C004_Id,
					   T276_Flag_Scan,
					   T276_Numero_Recibo_MDFe 
				  FROM T276 
				 WHERE T276_Id = '{$T276_Id}';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);

		if ($row['T276_Flag_Scan']=='S' and $r_scan!='S'){
			$r_refazer = 'S';
		} else if ($row['T276_Flag_Scan']!='S' and $r_scan=='S'){
            $r_refazer = 'S';
		}

		//Caso a data hora de inicio da transmissão do mdfe tenha sido a mais de 5 minutos, e a nota ainda permanece em transmissão, reiniciamos o status para zero
		if((strtotime(date("Y-m-d H:i:s")) > strtotime(date("Y-m-d H:i:s", strtotime('+1 minute', strtotime($row['T276_TX_Timestamp_Envio_Sefaz']))))) && $row['T276_TX_Resposta_Codigo'] != '' && $r_tx != 'true') {
			if(in_array($row['T276_TX_Resposta_Codigo'], array('1', '2', '3', '4', '5', '6', '7', '8', '10', '11', '12', '13'))) {
				$row['T276_TX_Resposta_Codigo'] = '';
			}
		}

		// Existem casos (utilizado primeira vez na empresa Koisas Frescas) que a NF aparece na tela de envio, mais ainda não pode ser enviada
		$resposta['exibir'] = true;

		// MDFe denegada?
		$resposta['denegada'] = '0';
		$T127 = mysql_query("SELECT T285_Codigo_Retorno FROM T285 WHERE T285_T276_Id = '{$T276_Id}' AND T285_Modelo = '58' ORDER BY T285_Id DESC LIMIT 1");
		$mT127 = mysql_fetch_array($T127);
		if($mT127['T285_Codigo_Retorno'] == '110'){         
    		$resposta['denegada'] = '1';
    		$resposta['T276_TX_Resposta_Codigo'] = $row['T276_TX_Resposta_Codigo'];
    		$resposta['codigoTXT'] = 'Nota denegada!';
		} else {
			//
			$resposta['T276_TX_Resposta_Codigo'] = $row['T276_TX_Resposta_Codigo'];
			if ($resposta['T276_TX_Resposta_Codigo'] >= 100 && $resposta['T276_TX_Resposta_Codigo'] < 200) {
				$resposta['codigoTXT'] = $codigos[$row['T276_TX_Resposta_Codigo']] . ' - ' . $row['T276_TX_Resposta_Extra'];
			} else {
				$resposta['codigoTXT'] = $codigos[$row['T276_TX_Resposta_Codigo']];
			}
		}
		$resposta['T276_TX_Resposta_Extra'] = $row['T276_TX_Resposta_Extra'];
		$resposta['T276_TX_Retorno_Sefaz'] = $row['T276_TX_Retorno_Sefaz'];
		$resposta['T276_TX_Retorno_Sefaz_40'] = '';
		if (strlen($resposta['T276_TX_Retorno_Sefaz']) > 250) {
			$resposta['T276_TX_Retorno_Sefaz_40'] = substr($row['T276_TX_Retorno_Sefaz'], 0, 250);
		}
		$resposta['T276_TX_Mensagem'] = '';
		$resposta['Procedimento'] = '';
		
		if (!empty($row['T276_TX_Mensagem'])) {
			$row['T276_TX_Mensagem'] = str_replace(" ", " ", $row['T276_TX_Mensagem']);
			$resposta['T276_TX_Mensagem'] .= $row['T276_TX_Mensagem'];
		}
		
		// 201 e 202: extra = T137_Numero_Erro
		if ($resposta['T276_TX_Resposta_Codigo'] == 201 || $resposta['T276_TX_Resposta_Codigo'] == 202) {
			$sql = "SELECT * FROM global.T137 WHERE T137_Numero_Erro = '{$resposta['T276_TX_Resposta_Extra']}';";
			$res = mysql_query($sql);
			$T137 = mysql_fetch_assoc($res);
			if (empty($T137)) {
				$resposta['Procedimento'] .= "";
				//if (!empty($resposta['T276_TX_Retorno_Sefaz'])) {
					$resposta['status_scan'] = 0;
				//}
				$resposta['erro'] = " <strong>Erro:</strong><br /> {$resposta['T276_TX_Resposta_Codigo']} - {$resposta['codigoTXT']}<br /><br /> <strong>Erro Sefaz:</strong><br /> {$resposta['T276_TX_Resposta_Extra']} - Erro não cadastrado na T137.<br /><br /> <strong>Retorno Sefaz:</strong><br /> {$resposta['T276_TX_Retorno_Sefaz']}<br /><br /> <strong>Procedimento:</strong><br /> Este erro ainda não possui um procedimento de ajuda.<br /><br /> <strong>Chave de Acesso Refeita:</strong><br /> N (erro não cadastrado, padrão é não)";
			} else {
				if ($T137['T137_Flag_Refazer_Chave_Acesso'] == 'N') {
					$resposta['status_scan'] = 0;
				}
				$T137['T137_Procedimento'] = !empty($T137['T137_Procedimento']) ? $T137['T137_Procedimento'] : "Este erro ainda não possui um procedimento de ajuda.";
				if (!empty($T137['T137_Procedimento'])) {
					$resposta['Procedimento'] .= ven012quebraBr($T137['T137_Procedimento']);
				}
				$resposta['erro'] = " <strong>Erro:</strong><br /> {$resposta['T276_TX_Resposta_Codigo']} - {$resposta['codigoTXT']}<br /><br /> <strong>Detalhes do erro:</strong><br /> {$T137['T137_Numero_Erro']} - {$T137['T137_Descricao_Erro']}<br /><br /> <strong>Resposta Sefaz:</strong><br /> {$resposta['T276_TX_Retorno_Sefaz']}<br /><br /> <strong>Procedimento:</strong><br /> {$T137['T137_Procedimento']}<br /><br /> <strong>Chave de Acesso Refeita:</strong><br /> {$T137['T137_Flag_Refazer_Chave_Acesso']}";
				// 1a. Correção:
				if ($T137['T137_Numero_Erro'] == 539) {
					preg_match("/Duplicidade de MDF-e, com difere.* na Chave de Acesso \[([0-9]+)\] \[nRec:([0-9]+)\]/", $resposta['T276_TX_Retorno_Sefaz'], $matches);
					if (isset($matches[1]) && isset($matches[2])) {
						$resposta['erro'] .= "<br /><br /><strong>Correção:</strong><br />Chave: {$matches[1]}<br />Protocolo: {$matches[2]}<br /><br /><button type=\"button\" T276_id=\"{$T276_Id}\" chave=\"{$matches[1]}\" protocolo=\"{$matches[2]}\" class=\"correcaoChaveProtocolo ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only\"><span class=\"ui-button-text\">Corrigir Chave/Protocolo</span></button>";
					}
				}
			}
		} else {
			$resposta['erro'] = "<strong>Erro:</strong><br />{$resposta['T276_TX_Resposta_Codigo']} - {$resposta['codigoTXT']}<br /><br /><strong>Mais informações:</strong><br />{$resposta['T276_TX_Resposta_Extra']}<br /><br /><strong>Procedimento:</strong><br />Caso não saiba como solucionar o problema, entre em contato com o suporte e informe o erro. Suporte Hardness: 41 3344-2938<br /><br />";
		}
		if (isset($r_tx) || !empty($r_tx)) {
			if($resposta['encerrar']){
				$sql = "UPDATE T276 SET T276_TX_Resposta_Codigo = 14, T276_TX_Timestamp_Envio_Sefaz = current_timestamp() WHERE T276_Id = '{$T276_Id}';";
				echo " SQL = ".$sql;
				mysql_query($sql);
				$confUsuario['urlRaiz'] = mysql_real_escape_string($confUsuario['urlRaiz']);
				$sql = "INSERT INTO `global`.`Q004` (`Q004_Database`,`Q004_T276_Id`,`Q004_C004_Id`,`Q004_Flag_Refazer`,`Q004_Flag_Scan`,`Q004_Url`,`Q004_Path`,`Q004_C007_Id`) VALUES ('{$confUsuario['dbDatabase']}','{$T276_Id}','{$g['empresaAtual']}','N','{$r_scan}','{$confUsuario['urlRaiz']}', '{$confUsuario['pathRaiz']}','{$g['usuarioAtual']}');";
				mysql_query($sql);
			}else{
				$sql = "UPDATE T276 SET T276_TX_Resposta_Codigo = 12, T276_TX_Timestamp_Envio_Sefaz = current_timestamp() WHERE T276_Id = '{$T276_Id}';";
				mysql_query($sql);
				$confUsuario['urlRaiz'] = mysql_real_escape_string($confUsuario['urlRaiz']);
				$sql = "INSERT INTO `global`.`Q004` (`Q004_Database`,`Q004_T276_Id`,`Q004_C004_Id`,`Q004_Flag_Refazer`,`Q004_Flag_Scan`,`Q004_Url`,`Q004_Path`,`Q004_C007_Id`) VALUES ('{$confUsuario['dbDatabase']}','{$T276_Id}','{$g['empresaAtual']}','{$r_refazer}','{$r_scan}','{$confUsuario['urlRaiz']}', '{$confUsuario['pathRaiz']}','{$g['usuarioAtual']}');";
				mysql_query($sql);
			}
		}
		
	break;
	
	case 'fechar':
		$T276_Id = isset($r_T276_Id) ? $r_T276_Id : false;
		if (!$T276_Id) { die(); }
		$T276_Id = mysql_real_escape_string($T276_Id);
		$T276_TX_Flag_Finalizada = isset($r_T276_TX_Flag_Finalizada) ? $r_T276_TX_Flag_Finalizada : 1;
		mysqli_query("UPDATE T276 SET T276_TX_Flag_Finalizada = '{$T276_TX_Flag_Finalizada}' WHERE T276_Id = '{$T276_Id}';");
	break;

	//DEVE SER MELHOR ANALISADO DEPOIS (MOTIVO: MDF-e)
	case 'cancelarMdfe':
    	$T276_Id = isset($_REQUEST['T276_Id']) ? $_REQUEST['T276_Id'] : false;
    	//$retorno = $FIS012->verificaCancelarMdfe($T276_Id);

    		$resposta['code'] = true;
    	/*if($retorno === true){
    	} else {
    		$resposta['code'] = false;
    		$resposta['data'] = $retorno;	
    	}*/
    break;

    case 'abreXMLCancelamento':
		global $g;
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		
		$sql = 'SELECT T276_Numero_Protocolo_Cancelamento_MDFe, 
					   T276_Data_Emissao,
					   T276_Chave_Acesso_MDFe 
			      FROM T276 
			     WHERE T007_Id ='.$id;

		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);
		
		$cAno_Mes_MDFe=substr($array['T276_Data_Emissao'],0,7);
		$Diretorio_MDFe=$g['pathDadosAntigo'] . 'mdfe';
		//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
		$cArquivo="$Diretorio_MDFe/xml/cancelado/ID110111".$array['T276_Chave_Acesso_MDFe']."01.xml";
		
		if (file_exists($cArquivo)){
		    // We'll be outputting a PDF
		    header('Content-type: text/xml');
		
		    // It will be called downloaded.pdf
		    header('Content-Disposition: attachment; filename=ID110111"'.$array['T276_Chave_Acesso_MDFe'].'"01');
		
		    // The PDF source is in original.pdf
		    readfile($cArquivo);
		}
		else{
		   echo "XML Inexistente";
		}
		die();
	break;

	case 'abreXML':
		global $g;
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		
		$sql = 'SELECT T276_Numero_Protocolo_MDFe, 
					   T276_Data_Emissao 
			      FROM T276 
			     WHERE T276_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);
		
		
		$cAno_Mes_MDFe=substr($array['T276_Data_Emissao'],0,7);
		$Diretorio_MDFe=$g['pathDadosAntigo'] . 'mdfe';
		//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
		$cArquivo="$Diretorio_MDFe/xml/autorizado/{$cAno_Mes_MDFe}/".$array['T276_Numero_Protocolo_MDFe'].".xml";
		
		if (file_exists($cArquivo)){
		    // We'll be outputting a PDF
		    header('Content-type: text/xml');
		
		    // It will be called downloaded.pdf
		    header('Content-Disposition: attachment; filename="'.$array['T276_Numero_Protocolo_MDFe'].'.xml"');
		
		    // The PDF source is in original.pdf
		    readfile($cArquivo);
		}
		else{
		   echo "XML Inexistente";
		}
		die();
	break;

	case 'excluirMDFe':
		require_once("bibliotecas/classes/FIS012.php");
		$FIS012 = new FIS012();
		
		$T276_Id = isset($_REQUEST['T276_Id']) ? $_REQUEST['T276_Id'] : false;
		$retorno = $FIS012->excluirMDFe($T276_Id);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "MDF-e excluído com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;


}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

function ven012quebraBr($txt, $tamanho = 80){
	global $g;
	$novo = '';
	$count = 0;
	for ($i = 0; $i < strlen($txt); $i++) {
		$count++;
		if ($count > $tamanho && $txt[$i] == ' ') {
			$count = 0;
			$novo .= '<br />';
		} else {
			$novo .= $txt[$i];
		}
	}
	return $novo;
}
