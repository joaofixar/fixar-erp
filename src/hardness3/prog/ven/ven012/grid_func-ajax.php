<?php
namespace hardness;
	/**
	* Weiberlan
	* 12/11/2011
	*/
require_once('bibliotecas/classes/VEN012.php');
require_once('bibliotecas/classes/NFeRejeicao225.php');
$VEN012 = new VEN012();

include('scripts/queueNF/codigos.php');

$resposta = array('code' => true, 'data' => array());

	switch($g['r'][3]) {
		case 'ven012VerificarFaturamentoLote':
			$extraStatus = ($g['C031']['pedidoGerarNFEmQualquerStatus'] == 'S')
				? "AND T005_Flag_Status IS NOT NULL AND T005_Flag_Status != 0 AND T005_Flag_Status != '' AND T005_Flag_Status NOT IN (4, 5, 8)"
				: "AND T005_Flag_Status = '3'";

			$sql = "SELECT T005_Id
				FROM T005
				WHERE T005_Flag_Marcado = 'S'
					AND T005_C004_Id = '{$g['empresaAtual']}'
					AND IFNULL(T005_T005_Id_Agrupado, 0) <= 0
					{$extraStatus}
				ORDER BY T005_Id";
			$resultado = mysql_query($sql);
			$resposta['pedidos'] = array();
			while ($row = mysql_fetch_assoc($resultado)) {
				$resposta['pedidos'][] = (int) $row['T005_Id'];
			}
			$resposta['quantidade'] = count($resposta['pedidos']);
			if ($resposta['quantidade'] <= 0) {
				$resposta['code'] = false;
				$resposta['data'] = 'Nenhum pedido foi marcado para faturar.';
			}
		break;

		case 'ven012PrepararFaturamentoLote':
			$T005_Id = isset($r_T005_Id) ? (int) $r_T005_Id : 0;
			$extraStatus = ($g['C031']['pedidoGerarNFEmQualquerStatus'] == 'S')
				? "AND T005_Flag_Status IS NOT NULL AND T005_Flag_Status != 0 AND T005_Flag_Status != '' AND T005_Flag_Status NOT IN (4, 5, 8)"
				: "AND T005_Flag_Status = '3'";
			$resposta['erro'] = '';

			$pedido = mysql_fetch_assoc(mysql_query("SELECT T005_Id,
				T005_D024_Id AS D024_Nome_Empresa,
				T005_D024_Id_Nfe,
				T005_D024_Id_Contas,
				T005_Numero_Nota_Manual AS Numero_Nota
				FROM T005
				WHERE T005_Id = '{$T005_Id}'
					AND T005_Flag_Marcado = 'S'
					AND T005_C004_Id = '{$g['empresaAtual']}'
					AND IFNULL(T005_T005_Id_Agrupado, 0) <= 0
					{$extraStatus}
				LIMIT 1"));
			if (empty($pedido['T005_Id'])) {
				$resposta['erro'] = 'O pedido não está mais marcado ou não está disponível para faturamento.';
				break;
			}

			$notaExistente = mysql_fetch_assoc(mysql_query("SELECT T007_Id
				FROM T007
				WHERE T007_T005_Id = '{$T005_Id}'
					AND T007_Flag_Cancelada != 'S'
					AND T007_Flag_Inutilizado != 'S'
					AND IFNULL(T007_Protocolo_Inutilizacao, '') = ''
				LIMIT 1"));
			if (!empty($notaExistente['T007_Id'])) {
				$resposta['recuperado'] = true;
				break;
			}

			if ($g['C031']['validarNFEGerarNotaFiscal'] == 'S') {
				require_once('bibliotecas/validadorNFe/validadorNFe.php');
				$validadorNFe = new validadorNFe();
				$retorno = $validadorNFe->validadorNFe(null, $T005_Id, null);
				if ($retorno !== true) {
					$resposta['erro'] = trim(strip_tags((string) $retorno));
					break;
				}
			}

			$pedido['T007_T075_Id'] = '';
			$resposta['form'] = $pedido;
		break;

		case 'ven012FinalizarFaturamentoLote':
			$T005_Id = isset($r_T005_Id) ? (int) $r_T005_Id : 0;
			mysql_query("UPDATE T005
				SET T005_Flag_Marcado = 'N'
				WHERE T005_Id = '{$T005_Id}'
					AND T005_C004_Id = '{$g['empresaAtual']}'
					AND T005_Flag_Marcado = 'S'");
		break;

		case 'abreXML':
			$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;

			$sql = 'select T007_Numero_Protocolo_Nfe, T007_Data_Emissao from T007 WHERE T007_Id ='.$id;
			$result = mysql_query($sql);
			$array = mysql_fetch_assoc($result);


			$cAno_Mes_NFe=substr($array['T007_Data_Emissao'],0,7);
			$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
			//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
			$cArquivo="$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['T007_Numero_Protocolo_Nfe'].".xml";

			if (file_exists($cArquivo)){
				// We'll be outputting a PDF
				header('Content-type: text/xml');

				// It will be called downloaded.pdf
				header('Content-Disposition: attachment; filename="'.$array['T007_Numero_Protocolo_Nfe'].'"');

				// The PDF source is in original.pdf
				readfile($cArquivo);
			}
			else{
				echo "XML Inexistente";
			}
			die();
			break;

		case 'excluirNFe':
			//Scrip ainda não funcionando, verificar com Tiago. OS pausada
			$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
			$verifica = $g['conf']->restricaoExcluir('T007_Id', $T007_Id);

			if ($verifica !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $verifica;
			} else {
				$retorno = $VEN012->excluirNFe($T007_Id);
				if($retorno !== true){
					$resposta['code'] = false;
					$resposta['data'] = $retorno;
				}else{
					$resposta['code'] = true;
					$resposta['data'] = "Nota excluida com sucesso";
				}
			}
			break;

		case 'retornarPedido':
			global $g;

			$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
			
			$statusPedido = 0;
			if($g['C031']['modeloExpedicao'] == '2'){
				$statusPedido = 22;
			}

			$verifica = $VEN012->retornarPedido($T005_Id, $statusPedido);

			if ($verifica !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $verifica;
			}
		break;
		
		case 'atualizarParcelas':
			require_once("bibliotecas/classes/VEN012.php");
			
			$T090_Id 		= isset($r_T090_Id) ? $r_T090_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			$VEN012 = new VEN012();
			
			$retorno = $VEN012->VEN012AtualizarParcelas($T090_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
    
    case 'ven012CheckboxDividir':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $id = isset($r_id) ? $r_id : false;
        $TXXX = isset($r_TXXX) ? $r_TXXX : false;
        
        $VEN012->updateCheckboxDividir($id, $selected, $TXXX);
    break;
    
    case 'ven012CheckboxComissao':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $id = isset($r_id) ? $r_id : false;
        $TXXX = isset($r_TXXX) ? $r_TXXX : false;
        
        $VEN012->updateCheckboxComissao($id, $selected, $TXXX);
    break;
    
    case 'ven012VerificaItensDividir':
        $T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;

        $sql = <<<EOT
	        SELECT * FROM T006 
				LEFT JOIN T005 ON T005_Id=T006_T005_Id
				LEFT JOIN T147 ON T147_T005_Id=T005_Id
				LEFT JOIN T138 ON T138_T005_Id=T005_Id
				LEFT JOIN T008 ON T008_T006_Id=T006_Id
				LEFT JOIN T203 ON T203_T147_Id=T147_Id
			WHERE T006_T005_Id='{$T005_Id}'
				AND (T008_T006_Id='' OR T008_T006_Id IS NULL OR T008_T006_Id=0)
				AND (T203_T147_Id='' OR T203_T147_Id IS NULL OR T203_T147_Id=0)
				AND (T008_T138_Id='' OR T008_T138_Id IS NULL OR T008_T138_Id=0)
				AND ((T006_Flag_Dividir='S' OR T147_Flag_Dividir='S' OR T138_Flag_Dividir='S')
			OR T006_Quantidade_Dividir > 0);
EOT;
        $result = mysql_query($sql);

        if(mysql_num_rows($result) == 0){
        	$resposta['code'] = false;
            $resposta['data'] = "Não há itens selecionados para duplicar";
        }else{
        	$erro = 0;
        	while($mT006 = mysql_fetch_array($result)){
        		if($mT006['T006_Quantidade_Dividir'] > $mT006['T006_Quantidade'] or $mT006['T006_Quantidade_Dividir'] > $mT006['T006_Quantidade_Estoque']){
        			$erro=1;
        			continue;
        		}
        	}
        	if($erro == 0){
        		$resposta['code'] = true;
        	}else{
        		$resposta['code'] = false;
            	$resposta['data'] = "Quantidade informada para dividir é maior que a quantidade disponível no pedido";
        	}
        }
    break;

    case 'ven012VerificaItensDividirEmpresa':
        $qtdProdutoParaDividir = 0;
        $r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T006 = mysql_query($r_sql);
		while($mT006 = mysql_fetch_array($T006)) {
			if ($mT006['T006_Quantidade_Dividir'] > 0) {
				$qtdProdutoParaDividir++;
	        	if($mT006['T006_Quantidade_Dividir'] > $mT006['T006_Quantidade']){ // or $mT006['T006_Quantidade_Dividir'] > $mT006['T006_Quantidade_Estoque']
	        		log("{$mT006['T006_Codigo_Produto']} - {$mT006['T006_Quantidade_Dividir']} > {$mT006['T006_Quantidade']} or {$mT006['T006_Quantidade_Dividir']} > {$mT006['T006_Quantidade_Estoque']}");
	        		$resposta['code'] = false;
	            	$resposta['data'] = "Quantidade informada para dividir é maior que a quantidade disponível no pedido.";
	        	}
	        }
		}

        if(!$qtdProdutoParaDividir){
        	$resposta['code'] = false;
            $resposta['data'] = "Não há itens selecionados para duplicar";
        }
    break;
    
    case 'ven012VerificaItensMudarFIlial':
        $T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
        
        $contador = 0;
        
        $sql = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id='{$T005_Id}'");
        if (mysql_num_rows($sql)>0) {
            $contador = 1;
        }
        $sql = mysql_query("SELECT T147_Id FROM T147 WHERE T147_T005_Id='{$T005_Id}'");
        if (mysql_num_rows($sql)>0) {
            $contador = 1;
        }
        $sql = mysql_query("SELECT T138_Id FROM T138 WHERE T138_T005_Id='{$T005_Id}'");
        if (mysql_num_rows($sql)>0) {
            $contador = 1;
        }
        
        if ($contador == 0) {
            $resposta['code'] = false;
        }
    break;
    
    case 'ven012VerificaItensGerarNF':
        $T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
        
        $contador = 0;
        
        $sql = mysql_query("SELECT T006_Id FROM T006 LEFT JOIN T008 ON T008_T006_Id=T006_Id WHERE T006_Flag_Dividir='S' AND T006_T005_Id='{$T005_Id}' AND (T008_T006_Id='' or T008_T006_Id is null or T008_T006_Id=0)");
        if (mysql_num_rows($sql)>0) {
            $contador = 1;
        }
        $sql = mysql_query("SELECT T147_Id FROM T147 LEFT JOIN T203 ON T203_T147_Id=T147_Id WHERE T147_Flag_Dividir='S' AND T147_T005_Id='{$T005_Id}' AND (T203_T147_Id='' or T203_T147_Id is null or T203_T147_Id=0)");
        if (mysql_num_rows($sql)>0) {
            $contador = 1;
        }
        $sql = mysql_query("SELECT T138_Id FROM T138 LEFT JOIN T008 ON T008_T138_Id=T138_Id WHERE T138_Flag_Dividir='S' AND T138_T005_Id='{$T005_Id}' AND (T008_T138_Id='' or T008_T138_Id is null or T008_T138_Id=0)");
        if (mysql_num_rows($sql)>0) {
            $contador = 1;
        }
        
        if ($contador == 0) {
            $resposta['code'] = false;
        }
    break;
    
    case 'ven012ConcluirPedido':
    	$T005_Id=isset($r_T005_Id) ? $r_T005_Id : false;
    	
    	$retorno = $VEN012->ven012ConcluirPedido($T005_Id);
    	
    	 if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Conclusão efetuada com sucesso";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
    	
    break;
    
    case 'ven012DuplicarProduto':
    	$T006_Id=isset($r_T006_Id) ? $r_T006_Id : false;
    	
    	$retorno = $VEN012->ven012DuplicarProduto($T006_Id);
    	
    	 if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Conclusão efetuada com sucesso";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
    break;

    case 'ven012DuplicarPedido':
    	global $novoT005_Id;
    	$T005_Id=isset($r_T005_Id) ? $r_T005_Id : false;
    	
    	$retorno = $VEN012->ven012DuplicarPedido($T005_Id);
    	
    	 if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedido duplicado com sucesso";
			$resposta['T005_Id_Novo'] = $novoT005_Id;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
    break;
    
    case 'txSefaz':
    	$resposta['code'] = false;
    	$resposta['data'] = 'A base local não emite nota.';
    	break;
		session_write_close();

		$status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$resposta['status_normal'] = $status[0];
		$resposta['status_scan'] = $status[1];

		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		if (!$T007_Id) { die(); }
		$T007_Id = mysql_real_escape_string($T007_Id);

		$sql = "SELECT T007_Id, T007_TX_Timestamp_Envio_Sefaz, T007_TX_Resposta_Codigo, T007_TX_Resposta_Extra, T007_TX_Retorno_Sefaz, T007_TX_Mensagem, T007_Data_Envio_XML, T007_Flag_Almoxarifado, T007_Flag_Impresso, T007_Numero_Nota_Fiscal, T007_TX_Flag_Finalizada, T007_C004_Id, T007_Flag_ACP, T007_Flag_Scan, T007_Numero_Protocolo_Nfe, T007_Flag_Expedicao, T007_Numero_Lote FROM T007 WHERE T007_Id = '{$T007_Id}';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);

		if ($row['T007_Flag_Scan']=='S' and $r_scan!='S'){
			$r_refazer = 'S';
		} else if ($row['T007_Flag_Scan']!='S' and $r_scan=='S'){
            $r_refazer = 'S';
		}

		// Verifica se será habilitado a emissão em contingência ou não
		$C020  = mysql_query("SELECT C020_NFe_SVC_Ativado FROM C020 WHERE C020_C004_Id='{$g['empresaAtual']}'");
		$mC020 = mysql_fetch_array($C020);

		if($mC020['C020_NFe_SVC_Ativado'] == 'S'){
			$resposta['Contingencia'] = true;
		} else {
			$resposta['Contingencia'] = false;
		}

		if (isset($r_tx) || !empty($r_tx)) {
			//$sql = "UPDATE T007 SET T007_TX_Resposta_Codigo = 12, T007_TX_Timestamp_Envio_Sefaz = current_timestamp(), T007_Numero_Lote = 1 WHERE T007_Id = '{$T007_Id}';";
			$dataHoraAtual = date("Y-m-d H:i:s");			
			$sql = "UPDATE T007 SET T007_TX_Resposta_Codigo = 12, T007_TX_Timestamp_Envio_Sefaz = '{$dataHoraAtual}', T007_Numero_Lote = 1 WHERE T007_Id = '{$T007_Id}';";			
			mysql_query($sql);
			$confUsuario['urlRaiz'] = mysql_real_escape_string($confUsuario['urlRaiz']);
			if ((isset($g['C031']['EnviarDanfeBoletoMesmoEmail']) && $g['C031']['EnviarDanfeBoletoMesmoEmail'] == 'S') || $g['C031']['NFeEnviarEmailAutomatico'] == 'S' || $g['C031']['BoletosEnviarEmailAutomatico'] == 'S'){
				$sql = "INSERT INTO `global`.`Q001` (`Q001_Database`,`Q001_T007_Id`,`Q001_C004_Id`,`Q001_Flag_Refazer`,`Q001_Flag_Scan`,`Q001_Url`,`Q001_Path`,`Q001_C007_Id`) VALUES ('{$confUsuario['dbDatabase']}','{$T007_Id}','{$g['empresaAtual']}','{$r_refazer}','{$r_scan}','{$confUsuario['urlRaiz']}', '{$confUsuario['pathRaiz']}','{$g['usuarioAtual']}');";
			} else {
				$sql = "INSERT INTO `global`.`Q001` (`Q001_Database`,`Q001_T007_Id`,`Q001_C004_Id`,`Q001_Flag_Refazer`,`Q001_Flag_Scan`,`Q001_Url`,`Q001_Path`,`Q001_C007_Id`) VALUES ('{$confUsuario['dbDatabase']}','{$T007_Id}','{$g['empresaAtual']}','{$r_refazer}','{$r_scan}','{$confUsuario['urlRaiz']}', '{$confUsuario['pathRaiz']}','');";
			}
			mysql_query($sql);
			$erro = mysql_error();
			if($erro){
				$sql = "UPDATE T007 SET T007_TX_Mensagem = \"" . $erro . "\", T007_TX_Resposta_Codigo = 122  WHERE T007_Id = '{$T007_Id}';";
				mysql_query($sql);
			}
		}else{
			
			if($row['T007_Numero_Lote'] == 1) {
				//Caso a data hora de inicio da transmissão da nota tenha sido a mais de 5 minutos, e a nota ainda permanece em transmissão, reiniciamos o status para zero
				if((strtotime(date("Y-m-d H:i:s")) > strtotime(date("Y-m-d H:i:s", strtotime('+1 minute', strtotime($row['T007_TX_Timestamp_Envio_Sefaz']))))) && $row['T007_TX_Resposta_Codigo'] != '' && $r_tx != 'true') {
					if(in_array($row['T007_TX_Resposta_Codigo'], array('1', '2', '3', '4', '5', '6', '7', '8', '10', '11', '12', '13'))) {
						$row['T007_TX_Resposta_Codigo'] = '';
					}
				}
			}
			
			// Existem casos (utilizado primeira vez na empresa Koisas Frescas) que a NF aparece na tela de envio, mais ainda não pode ser enviada
			$resposta['exibir'] = true;
			if ($row['T007_Flag_Almoxarifado'] != 'S' || empty($row['T007_Numero_Nota_Fiscal'])) { // || $row['T007_Flag_Impresso'] != 'S'
				$resposta['exibir'] = false;
			}
			
			// Email enviado? Mostrar no botão
			$resposta['emailEnviado'] = '0';
			$resposta['emailEnviadoTxt'] = "E-mail DANFE";
			if ($row['T007_Data_Envio_XML'] != '0000-00-00 00:00:00' && $row['T007_Data_Envio_XML']!='') {
				$row['T007_Data_Envio_XML'] = gCorrigeData($row['T007_Data_Envio_XML']);
				$resposta['emailEnviadoTxt'] = "DANFE enviado em:<br />{$row['T007_Data_Envio_XML']}";
				if ($row['T007_TX_Flag_Finalizada'] != '2') {
					$resposta['emailEnviado'] = '1';
				}
			}
			
			// Nota denegada?
			$resposta['denegada'] = '0';
			$modelo = ($row['T007_Flag_ACP'] == '6') ? '55' : '65';
			$T127 = mysql_query("SELECT * FROM T127 WHERE T127_Numero_Nota_Fiscal = '{$row['T007_Numero_Nota_Fiscal']}' AND T127_C004_Id = '{$row['T007_C004_Id']}' AND T127_Modelo = '{$modelo}' ORDER BY T127_Id DESC LIMIT 1");
			$mT127 = mysql_fetch_array($T127);

			// O evento de denegação não existe mais a partir de 02/09/2024, ele é substituido pelo evento de rejeição onde pode ser corrigida a NF (Ajuste Sinief nº 43/2023)
			//if($mT127['T127_Codigo_Retorno'] == '301' || $mT127['T127_Codigo_Retorno'] == '302' || $mT127['T127_Codigo_Retorno'] == '303' || $mT127['T127_Codigo_Retorno'] == '110'){         
			//	$resposta['denegada'] = '1';
			//	$resposta['T007_TX_Resposta_Codigo'] = $row['T007_TX_Resposta_Codigo'];
			//	$resposta['codigoTXT'] = 'Nota denegada!';
			//} else {
				//
			$resposta['T007_TX_Resposta_Codigo'] = $row['T007_TX_Resposta_Codigo'];
			if ($resposta['T007_TX_Resposta_Codigo'] >= 100 && $resposta['T007_TX_Resposta_Codigo'] < 200) {
				$resposta['codigoTXT'] = $codigos[$row['T007_TX_Resposta_Codigo']] . ' - ' . $row['T007_TX_Resposta_Extra'];
			} else {
				$resposta['codigoTXT'] = $codigos[$row['T007_TX_Resposta_Codigo']];
			}
			//}
			$resposta['T007_TX_Resposta_Extra'] = $row['T007_TX_Resposta_Extra'];
			$resposta['T007_TX_Retorno_Sefaz'] = $row['T007_TX_Retorno_Sefaz'];
			$resposta['T007_TX_Retorno_Sefaz_40'] = '';
			$resposta['Lote'] = $row['T007_Numero_Lote'] > 1;
			if (strlen($resposta['T007_TX_Retorno_Sefaz']) > 250) {
				$resposta['T007_TX_Retorno_Sefaz_40'] = substr($row['T007_TX_Retorno_Sefaz'], 0, 250);
			}
			$resposta['T007_TX_Mensagem'] = '';
			$resposta['Procedimento'] = '';
			
			if (!empty($row['T007_TX_Mensagem'])) {
				$row['T007_TX_Mensagem'] = str_replace(" ", " ", $row['T007_TX_Mensagem']);
				$resposta['T007_TX_Mensagem'] .= $row['T007_TX_Mensagem'];
			}

			if (!empty($row['T007_Numero_Protocolo_Nfe']) && $mT127['T127_Codigo_Retorno']=='100' && ($row['T007_Flag_Expedicao'] != 2 || $g['C031']['modeloExpedicao'] == '2') && $resposta['T007_TX_Resposta_Codigo'] <= 0) {
				$resposta['T007_TX_Resposta_Codigo'] = 9;
			}
			
			// 201 e 202: extra = T136_Numero_Erro
			if ($resposta['T007_TX_Resposta_Codigo'] == 201 || $resposta['T007_TX_Resposta_Codigo'] == 202) {
				$sql = "SELECT * FROM global.T136 WHERE T136_Numero_Erro = '{$resposta['T007_TX_Resposta_Extra']}';";
				$res = mysql_query($sql);
				$T136 = mysql_fetch_assoc($res);
				if (empty($T136)) {
					$resposta['Procedimento'] .= "";
					//if (!empty($resposta['T007_TX_Retorno_Sefaz'])) {
						$resposta['status_scan'] = 0;
						//}
						$resposta['erro'] = <<<EOT
							<strong>Erro:</strong><br />
							{$resposta['T007_TX_Resposta_Codigo']} - {$resposta['codigoTXT']}<br /><br />
							<strong>Erro Sefaz:</strong><br />
							{$resposta['T007_TX_Resposta_Extra']} - Erro não cadastrado na T136.<br /><br />
							<strong>Retorno Sefaz:</strong><br />
							{$resposta['T007_TX_Retorno_Sefaz']}<br /><br />
							<strong>Procedimento:</strong><br />
							Este erro ainda não possui um procedimento de ajuda.<br /><br />
							<strong>Chave de Acesso Refeita:</strong><br />
							N (erro não cadastrado, padrão é não)
EOT;
				} else {
					if ($T136['T136_Flag_Refazer_Chave_Acesso'] == 'N') {
						$resposta['status_scan'] = 0;
					}
					$T136['T136_Procedimento'] = !empty($T136['T136_Procedimento']) ? $T136['T136_Procedimento'] : "Este erro ainda não possui um procedimento de ajuda.";
					if (!empty($T136['T136_Procedimento'])) {
						$resposta['Procedimento'] .= ven012quebraBr($T136['T136_Procedimento']);
					}
					$resposta['erro'] = <<<EOT
						<strong>Erro:</strong><br />
						{$resposta['T007_TX_Resposta_Codigo']} - {$resposta['codigoTXT']}<br /><br />
						<strong>Detalhes do erro:</strong><br />
						{$T136['T136_Numero_Erro']} - {$T136['T136_Descricao_Erro']}<br /><br />
						<strong>Resposta Sefaz:</strong><br />
						{$resposta['T007_TX_Retorno_Sefaz']}<br /><br />
						<strong>Procedimento:</strong><br />
						{$T136['T136_Procedimento']}<br /><br />
						<strong>Chave de Acesso Refeita:</strong><br />
						{$T136['T136_Flag_Refazer_Chave_Acesso']}
EOT;
					// 1a. Correção:
					if ($T136['T136_Numero_Erro'] == 539) {
						preg_match("/Duplicidade de NF-e, com difere.* na Chave de Acesso \[([0-9]+)\] \[nRec:([0-9]+)\]/", $resposta['T007_TX_Retorno_Sefaz'], $matches);
						if (isset($matches[1]) && isset($matches[2])) {
							$resposta['erro'] .= <<<EOT
							<br /><br /><strong>Correção:</strong><br />
							Chave: {$matches[1]}<br />
							Protocolo: {$matches[2]}<br /><br />
							<button type="button" t007_id="{$T007_Id}" chave="{$matches[1]}" protocolo="{$matches[2]}" class="correcaoChaveProtocolo ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="ui-button-text">Corrigir Chave/Protocolo</span></button>
EOT;
						}
					}
				}
			} else {
				$resposta['erro'] = <<<EOT
					<strong>Erro:</strong><br />
					{$resposta['T007_TX_Resposta_Codigo']} - {$resposta['codigoTXT']}<br /><br />
					<strong>Mais informações:</strong><br />
					{$resposta['T007_TX_Resposta_Extra']}<br /><br />
					<strong>Procedimento:</strong><br />
					Caso não saiba como solucionar o problema, entre em contato com o suporte e informe o erro. Suporte Hardness: 41 3344-2938<br /><br />
EOT;
			}

			// A fila grava rejeições da SEFAZ como código interno 201/202 e o
			// cStat oficial em T007_TX_Resposta_Extra. Para o 225, substituímos
			// somente a apresentação; o xMotivo original permanece no banco.
			if (($resposta['T007_TX_Resposta_Codigo'] == 201 || $resposta['T007_TX_Resposta_Codigo'] == 202)
				&& trim((string) $resposta['T007_TX_Resposta_Extra']) === '225') {
				$xml225 = '';
				$sqlXml225 = mysql_query("SELECT T007_Arquivo_XML_Temp FROM T007 WHERE T007_Id = '{$T007_Id}'");
				if ($sqlXml225) {
					$registroXml225 = mysql_fetch_assoc($sqlXml225);
					$xml225 = isset($registroXml225['T007_Arquivo_XML_Temp']) ? $registroXml225['T007_Arquivo_XML_Temp'] : '';
				}
				$diagnostico225 = NFeRejeicao225::interpretar(
					$row['T007_TX_Retorno_Sefaz'],
					$xml225
				);

				$escapar225 = function ($valor) {
					return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
				};
				$resumo225 = $escapar225($diagnostico225['resumo']);
				$onde225 = $escapar225($diagnostico225['ondeVerificar']);
				$orientacao225 = $escapar225($diagnostico225['orientacao']);
				$tag225 = $escapar225($diagnostico225['tag']);
				$caminho225 = $escapar225($diagnostico225['caminho']);
				$item225 = $escapar225($diagnostico225['item']);
				$valor225 = $escapar225($diagnostico225['valorInvalido']);
				$tecnico225 = $escapar225($diagnostico225['retornoTecnico']);

				$contexto225 = '';
				if ($tag225 !== '') {
					$contexto225 .= '<strong>Tag:</strong> &lt;' . $tag225 . '&gt;<br />';
				}
				if ($item225 !== '') {
					$contexto225 .= '<strong>Item:</strong> ' . $item225 . '<br />';
				}
				if ($valor225 !== '') {
					$contexto225 .= '<strong>Valor enviado:</strong> ' . $valor225 . '<br />';
				}
				if ($caminho225 !== '') {
					$contexto225 .= '<strong>Caminho no XML:</strong> ' . $caminho225 . '<br />';
				}

				$resposta['status_scan'] = 0;
				$resposta['layoutCompacto225'] = true;
				$resposta['codigoTXT'] = 'NF-e rejeitada';
				$resposta['T007_TX_Retorno_Sefaz'] = $resumo225;
				$resposta['T007_TX_Retorno_Sefaz_40'] = '';
				$resposta['Procedimento'] = '<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;width:100%;white-space:normal;">'
					. '<div style="flex:1 1 700px;min-width:0;white-space:normal;overflow-wrap:break-word;">'
					. '<strong>Onde verificar:</strong><br />' . $onde225
					. '<div style="margin-top:8px;"><strong>Como corrigir:</strong><br />' . $orientacao225 . '</div>'
					. '</div>'
					. '<div style="flex:0 1 440px;min-width:320px;border-left:1px solid #d7a0a0;padding-left:14px;white-space:normal;overflow-wrap:anywhere;">'
					. $contexto225
					. '<details style="margin-top:7px;"><summary style="cursor:pointer;">Detalhes técnicos da SEFAZ</summary>'
					. '<div style="margin-top:6px;word-break:break-word;">225 - Falha no Schema XML da NF-e<br />'
					. $tecnico225 . '</div></details></div></div>';
				$resposta['erro'] = '<strong>NF-e rejeitada:</strong><br />' . $resumo225
					. '<br /><br /><strong>Onde verificar:</strong><br />' . $onde225
					. '<br /><br /><strong>Como corrigir:</strong><br />' . $orientacao225;
			}
		}
		
	break;
	
	case 'fechar':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		if (!$T007_Id) { die(); }
		$T007_Id = mysql_real_escape_string($T007_Id);
		$T007_TX_Flag_Finalizada = isset($r_T007_TX_Flag_Finalizada) ? $r_T007_TX_Flag_Finalizada : 1;
		mysqli_query("UPDATE T007 SET T007_TX_Flag_Finalizada = '{$T007_TX_Flag_Finalizada}' WHERE T007_Id = '{$T007_Id}';");
	break;
	
	case 'correcaoChaveProtocolo':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		if (!$T007_Id) { die(); }
		$T007_Id = mysql_real_escape_string($T007_Id);
		
		if (!isset($r_chave) || !isset($r_protocolo)) {
			die();
		}
		$T007_Chave_Acesso_Nfe = mysql_real_escape_string($r_chave);
		$T007_Numero_Recibo_Nfe = mysql_real_escape_string($r_protocolo);
		
		mysql_query("UPDATE T007 SET T007_Chave_Acesso_Nfe = '{$T007_Chave_Acesso_Nfe}', T007_Numero_Recibo_Nfe = '{$T007_Numero_Recibo_Nfe}' WHERE T007_Id = '{$T007_Id}';");
	break;
	
	case 'ven012DefinirQuantidadePedido';
		global $g;
		//$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$erro=0;
		mysql_query("START_TRANSACTION");
		//$T005 = mysql_query("SELECT T006_Id, T006_Quantidade from T006 LEFT JOIN T005 on T005_Id=T006_T005_Id WHERE T005_D024_Id='{$D024_Id}'");
		$r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T005 = mysql_query($r_sql);
		while($mT005 = mysql_fetch_array($T005)){

			if($mT005['T005A_Flag_Conversao_Unidade'] == 'S'){
				$quantidade = $mT005['T006A_Quantidade_Comercial'];
			}else {
				$quantidade = $mT005['T006_Quantidade'];
			}

			$upT006 = mysqli_query("UPDATE T006 SET T006_Quantidade_Dividir='{$quantidade}' WHERE T006_Id='{$mT005['T006_Id']}'");
			$erro = mysql_error();
			if($erro){
				mysql_query("ROLLBACK");
				$erro=1;
			}
		}
		mysql_query("COMMIT");
		if($erro==0){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
		}
	break;

	case 'ven012DefinirQuantidadeSeparada':
		
	global $g;
	//$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
	$erro=0;
	mysql_query("START_TRANSACTION");
	//$T005 = mysql_query("SELECT T006_Id, T006_Quantidade_Separacao from T006 LEFT JOIN T005 on T005_Id=T006_T005_Id WHERE T005_D024_Id='{$D024_Id}'");
	$r_sql = str_replace('LIMIT 0,100', '', $r_sql);
	$T005 = mysql_query($r_sql);
	while($mT005 = mysql_fetch_array($T005)){
		$upT006 = mysqli_query("UPDATE T006 SET T006_Quantidade_Dividir = if(T006_Quantidade_Estoque>=T006_Quantidade, T006_Quantidade, if(T006_Quantidade_Estoque < 0, 0, T006_Quantidade_Estoque)) WHERE T006_Id='{$mT005['T006_Id']}'");
		$erro = mysql_error();
		if($erro){
			mysql_query("ROLLBACK");
			$erro=1;
		}
	}
	mysql_query("COMMIT");
	if($erro==0){
		$resposta['code'] = true;
	}else{
		$resposta['code'] = false;
	}
	
	break;    

	case 'ven012PreencherQuantidadeSeparada':
		
		global $g;
		//$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$erro=0;
		mysql_query("START_TRANSACTION");
		//$T005 = mysql_query("SELECT T006_Id, T006_Quantidade_Separacao from T006 LEFT JOIN T005 on T005_Id=T006_T005_Id WHERE T005_D024_Id='{$D024_Id}'");
		$r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T005 = mysql_query($r_sql);
		while($mT005 = mysql_fetch_array($T005)){
			$upT006 = mysqli_query("UPDATE T006 SET T006_Quantidade_Dividir = if(T006_Quantidade_Separacao>=T006_Quantidade, T006_Quantidade, if(T006_Quantidade_Separacao < 0, 0, T006_Quantidade_Separacao)) WHERE T006_Id='{$mT005['T006_Id']}'");
			$erro = mysql_error();
			if($erro){
				mysql_query("ROLLBACK");
				$erro=1;
			}
		}
		mysql_query("COMMIT");
		if($erro==0){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
		}
		
		break;    
	
	case 'ven012MarcarParaEnviar':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
        
        $VEN012->updateCheckboxEnviar($selected, $T007_Id);
    break;

    case 'ven012LimparQuantidades':
    	//$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;

		//$retorno = mysql_query("update T006 left join T005 on T005_id= T006_T005_Id set T006_Quantidade_Dividir=0 where T005_D024_Id = '{$D024_Id}'");
    	$erro=0;
		mysql_query("START_TRANSACTION");
    	$r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T005 = mysql_query($r_sql);
		while($mT005 = mysql_fetch_array($T005)){
			$upT006 = mysqli_query("UPDATE T006 SET T006_Quantidade_Dividir = 0 WHERE T006_Id='{$mT005['T006_Id']}'");
			$erro = mysql_error();
			if($erro){
				mysql_query("ROLLBACK");
				$erro=1;
			}
		}
		mysql_query("COMMIT");

		if($erro==0){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Houve algum erro ao atualizar a quantidade. Por favor, contato o suporte técnico.";
		}
    break;

  case 'ven012MarcarTodas':
    	$sqlT007 = mysql_query("SELECT T007_Id FROM T007 
    		WHERE T007_Flag_ACP=6 
    		AND T007_C004_Id='{$g['empresaAtual']}' 
    		AND T007_Flag_Cancelada='N' 
    		AND T007_TX_Flag_Finalizada != 1 
    		AND T007_Flag_Almoxarifado = 'S' 
    		AND T007_Numero_Nota_Fiscal > 0");
    	while($T007 = mysql_fetch_array($sqlT007)){
    		mysql_query("UPDATE T007 SET T007_Flag_Enviar = 'S' WHERE T007_Id = '{$T007['T007_Id']}'");
    	}
    	$resposta['code'] = true;
    break;

    case 'ven012DesmarcarTodas':
    	mysql_query("UPDATE T007 SET T007_Flag_Enviar = 'N' WHERE T007_Flag_Enviar = 'S' AND T007_C004_Id = '{$g['empresaAtual']}'");
    break;

    case 'ven012FecharTodas':
    	mysql_query("UPDATE T007 SET T007_TX_Flag_Finalizada = '1' 
    		WHERE T007_TX_Flag_Finalizada = '2' 
    		AND T007_TX_Resposta_Codigo = 9
    		AND T007_C004_Id='{$g['empresaAtual']}'");
    break;

    case 'ven012validaQuantidadeDividir':
    	$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
    	$r_novoValor = gCorrigeNumeroInverte($r_novoValor);
    	$r_valorAntigo = gCorrigeNumeroInverte($r_valorAntigo);
    	$novoValor = isset($r_novoValor) ? $r_novoValor : false;
    	$valorAntigo = isset($r_valorAntigo) ? $r_valorAntigo : false;

    	$T006 = mysql_query("SELECT T006_Quantidade, 
		                            T006A_Quantidade_Comercial,
							        IFNULL(T219_Id, 0) AS T219_Id 
							   FROM T006 
						  LEFT JOIN T006A ON T006_Id = T006A_T006_Id
						  LEFT JOIN T219 ON T006_Id=T219_T006_Id 
						      WHERE T006_Id = '{$T006_Id}'");
    	$T006 = mysql_fetch_array($T006);

		$quantidade = $T006['T006A_Quantidade_Comercial'] > 0 ? $T006['T006A_Quantidade_Comercial'] : $T006['T006_Quantidade'];

    	if($novoValor > $quantidade){
			mysql_query("UPDATE T006 SET T006_Quantidade_Dividir = '{$valorAntigo}' WHERE T006_Id = '{$T006_Id}'");
			$resposta['data'] = "O valor informado é maior que a quantidade de produtos no pedido";
			$resposta['code'] = false;
    	} else if($T006['T219_Id'] > 0 && ($novoValor < $quantidade) && $novoValor > 0){
    		mysql_query("UPDATE T006 SET T006_Quantidade_Dividir = '{$valorAntigo}' WHERE T006_Id = '{$T006_Id}'");
			$resposta['data'] = "Só é possível dividir a quantidade integral deste item, devido este item está vinculado a uma cotação";
			$resposta['code'] = false;
    	} else {
			$resposta['code'] = true;
    	}
    break;
	
	case 'ven012validaQuantidadeConversaoDividir':
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
    	$r_novoValor = gCorrigeNumeroInverte($r_novoValor);
    	$r_valorAntigo = gCorrigeNumeroInverte($r_valorAntigo);
    	$novoValor = isset($r_novoValor) ? $r_novoValor : false;
    	$valorAntigo = isset($r_valorAntigo) ? $r_valorAntigo : false;
		$campoEditado = isset($r_campoEditado) ? $r_campoEditado : false;

		$VEN012 = new VEN012();
		$retorno = $VEN012->aplicarQuantidadeDividir($T006_Id, $campoEditado, $valorAntigo, $novoValor);

		if ($retorno === true) {
			$VEN012->gerarAtividadeItemEdicaoDividir($T006_Id, $campoEditado, $valorAntigo, $novoValor);
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

    case 'ven012ImprimirMarcadas':
		global $g;
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();
		$retorno = $VEN003->gerarDANFENotasFilaEnvio();
		require_once("bibliotecas/PDFMerger/PDFMerger.php");
		$pdf = new \PDFMerger;
		$sql = mysql_query("SELECT * FROM T007 WHERE T007_Flag_Enviar = 'S' AND T007_C004_Id = '{$g['empresaAtual']}' AND T007_TX_Flag_Finalizada = 2");
		while($res = mysql_fetch_array($sql)){
			if(!empty($res['T007_Numero_Protocolo_Nfe'])) {
				$pdf->addPDF('../hardness/'.$g['pathDados'].'nfe/danfe/danfe_'.$res['T007_Numero_Protocolo_Nfe'].'.pdf', 'all');
			}
		}
		$pdf->merge('../hardness/'.$g['pathDados'].'nfe/danfe', 'danfe.pdf');
		die();
    break;

    case 'est002LogAlteracoesT006QuantidadeDividir':
		// habilitar somente onde precisar fazer o log (alumifix usa)
		/*
		$r_T006_Id = mysql_real_escape_string($r_T006_Id);
		$r_local = mysql_real_escape_string($r_local);
		$r_valorAntigo = mysql_real_escape_string($r_valorAntigo);
		$r_novoValor = mysql_real_escape_string($r_novoValor);
		$r_novoValor = gCorrigeNumeroInverte($r_novoValor);
		$sql = <<<EOT
INSERT INTO P001 
(P001_C007_Id, P001_C004_Id, P001_T006_Id, P001_Local, P001_Data_Hora, P001_T006_Quantidade_Dividir)
values
('{$g['usuarioAtual']}', '{$g['empresaAtual']}', '{$r_T006_Id}', '{$r_local}', NOW(), '{$r_novoValor}')
EOT;
		mysql_query($sql);
		*/
	break;

	case 'visuXML':
		$sql = "SELECT T007_Arquivo_XML_Temp FROM T007 WHERE T007_Id = '{$r_T007_Id}';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		
		if (empty($row['T007_Arquivo_XML_Temp'])) {
			$resposta['code'] = false;
		} else {
			$resposta['xml'] = $row['T007_Arquivo_XML_Temp'];
			
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, 'https://www.sefaz.rs.gov.br/nfe/nfe-val.aspx');
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, 'txtxml=' . urlencode($row['T007_Arquivo_XML_Temp']));
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$body = curl_exec($ch);
			curl_close($ch);
			preg_match_all('/<span id="resultado">(.*)<\/table>/isU', $body, $matches);
			$r = $matches[1][0];
			$r = str_replace('../Imagens/valid.png', '/hardness3/static/img/sefaz/valid.png', $r);
			$r = str_replace('../Imagens/ico_menos.gif', '/hardness3/static/img/sefaz/ico_menos.gif', $r);
			$r = str_replace('../Imagens/bullet_black.png', '/hardness3/static/img/sefaz/bullet_black.png', $r);
			$r = str_replace('../Imagens/erro.png', '/hardness3/static/img/sefaz/erro.png', $r);
			$resposta['table'] = $r . '</table>';
		}
	break;

	case 'ven012HabilitaEnvioSVC':
		//$Q007 = mysql_query("SELECT Q007_Valor FROM global.Q007 WHERE Q007_Campo='habilitarSVC'");
		//$mQ007 = mysql_fetch_array($Q007);

/* 		if(empty($mQ007['Q007_Valor']) || $mQ007['Q007_Valor'] != date('d/m/Y')){
			$Q007_Valor = date('d/m/Y');
			$resposta['code'] = true;
			$resposta['data'] = "Habilitado botão 'Enviar por SVC - contingência'";
		}else{
			$Q007_Valor = '';
			$resposta['code'] = true;
			$resposta['data'] = "Desabilitado botão 'Enviar por SVC - contingência'";
		}
		mysql_query("UPDATE global.Q007 SET Q007_Valor='{$Q007_Valor}' WHERE Q007_Campo='habilitarSVC'"); */

		$C020 = mysql_query("SELECT C020_NFe_SVC_Ativado FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}'");
		$mC020 = mysql_fetch_array($C020);

		if($mC020['C020_NFe_SVC_Ativado'] == 'S'){
			mysql_query("UPDATE C020 SET C020_NFe_SVC_Ativado = 'N',  C020_Data_NFe_SVC_Ativado = NOW() WHERE C020_C004_Id = '{$g['empresaAtual']}'");
		} else {
			mysql_query("UPDATE C020 SET C020_NFe_SVC_Ativado = 'S',  C020_Data_NFe_SVC_Ativado = NOW() WHERE C020_C004_Id = '{$g['empresaAtual']}'");
		}

		$error = mysql_error();
		if(!empty($error)){
			$resposta['data'] = 'Não foi possível habilitar/desabilitar o botão SVC ' . $error;
			$resposta['code'] = false;
		} else {
			if($mC020['C020_NFe_SVC_Ativado'] == 'S'){
				$resposta['data'] = 'Desabilitado o envio por SVC';
				$resposta['code'] = true;
			} else {
				$resposta['data'] = 'Habilitado envio por SVC';
				$resposta['code'] = true;
			}
		}
		
		
		
    break;

	case 'ven012HabilitaEnvioSVCNfce':
		//$Q007 = mysql_query("SELECT Q007_Valor FROM global.Q007 WHERE Q007_Campo='habilitarSVC'");
		//$mQ007 = mysql_fetch_array($Q007);

/* 		if(empty($mQ007['Q007_Valor']) || $mQ007['Q007_Valor'] != date('d/m/Y')){
			$Q007_Valor = date('d/m/Y');
			$resposta['code'] = true;
			$resposta['data'] = "Habilitado botão 'Enviar por SVC - contingência'";
		}else{
			$Q007_Valor = '';
			$resposta['code'] = true;
			$resposta['data'] = "Desabilitado botão 'Enviar por SVC - contingência'";
		}
		mysql_query("UPDATE global.Q007 SET Q007_Valor='{$Q007_Valor}' WHERE Q007_Campo='habilitarSVC'"); */

		$C020 = mysql_query("SELECT C020_NFCe_SVC_Ativado FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}'");
		$mC020 = mysql_fetch_array($C020);

		if($mC020['C020_NFCe_SVC_Ativado'] == 'S'){
			mysql_query("UPDATE C020 SET C020_NFCe_SVC_Ativado = 'N',  C020_Data_NFCe_SVC_Ativado = NOW() WHERE C020_C004_Id = '{$g['empresaAtual']}'");
		} else {
			mysql_query("UPDATE C020 SET C020_NFCe_SVC_Ativado = 'S',  C020_Data_NFCe_SVC_Ativado = NOW() WHERE C020_C004_Id = '{$g['empresaAtual']}'");
		}

		$error = mysql_error();
		if(!empty($error)){
			$resposta['data'] = 'Não foi possível habilitar/desabilitar o botão SVC ' . $error;
			$resposta['code'] = false;
		} else {
			if($mC020['C020_NFCe_SVC_Ativado'] == 'S'){
				$resposta['data'] = 'Desabilitado o envio OFFLINE da NFCe';
				$resposta['code'] = true;
			} else {
				$resposta['data'] = 'Habilitado envio OFFLINE da NFCe';
				$resposta['code'] = true;
			}
		}
		
		
		
    break;

	case 'ven012EnviarLote':
		$resposta['code'] = false;
		$resposta['data'] = 'A base local não emite nota.';
		break;
		global $g;		 

		mysql_query("LOCK T007");
		mysql_query("START TRANSACTION");
		$T007 = mysql_query("SELECT MAX(T007_Numero_Lote)  as NumeroLote
								FROM T007 								
							WHERE T007_C004_Id = '{$g['empresaAtual']}'");								
		$mT007 = mysql_fetch_array($T007);		
		$numeroLote = (int)$mT007['NumeroLote'] + 1;								
		mysql_query("UPDATE T007 SET T007_Numero_Lote = '{$numeroLote}' 
								WHERE T007_Flag_Enviar = 'S' AND T007_C004_Id ='{$g['empresaAtual']}' AND T007_Numero_Protocolo_Nfe IS NULL");		
		$cErro = mysql_error();
		if(!empty($cErro)){
			mysql_query("ROLLBACK");			
			$retorno =  "Erro ao atualizar o número do LOTE: ".$cErro;      			
		} 

		mysql_query("COMMIT");
		mysql_query("UNLOCK ALL");

		list($status,$retorno) = $VEN012->enviarLoteNfe($numeroLote);				
		if ($status === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Lote transmitido com sucesso!<br>Foi transmitida {$retorno} nota";			
			if($retorno > 1) {
				$resposta['data'] = "Lote transmitido com sucesso!<br> Foram transmitidas {$retorno} notas";			
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'ven012consultaReciboNfe':
		$resposta['code'] = false;
		$resposta['data'] = 'A base local não emite nota.';
		break;
		session_write_close();
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		if (!$T007_Id) { die(); }
		$T007_Id = mysql_real_escape_string($T007_Id);	

		//UPDATE INICIANDO PROCESSO
		$sql = "UPDATE T007 SET T007_TX_Resposta_Codigo = 12, T007_TX_Timestamp_Envio_Sefaz = current_timestamp() WHERE T007_Id = '{$T007_Id}'";
		mysql_query($sql);			
					
		log("ConsultaReciboNfe $sql");
		$retorno = $VEN012->consultarRecibo($T007_Id);		
		$resposta['code'] = true;	
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
