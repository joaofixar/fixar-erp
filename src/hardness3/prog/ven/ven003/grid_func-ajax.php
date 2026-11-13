<?php
namespace hardness;
/**
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/VEN003.php');
$VEN003 = new VEN003();

	switch($g['r'][3]) {

	case 'ven003IncluirItensEvento112140PorPercentual':
		require_once('hardness/class/nfe4_eventos.class.php');

		$T321_Id = isset($_REQUEST['T321_Id']) ? (int)$_REQUEST['T321_Id'] : 0;
		$percentual = isset($_REQUEST['percentual']) ? $_REQUEST['percentual'] : 0;
		$eventosNFe = new classeNFeEventos();
		$retorno = $eventosNFe->incluirItens112140PorPercentual($T321_Id, $percentual);

		$resposta['code'] = !empty($retorno['sucesso']);
		$resposta['data'] = isset($retorno['mensagem']) ? $retorno['mensagem'] : 'Não foi possível incluir os itens.';
	break;

	case 'ven003BaixarXmlEventoNFe':
		require_once('hardness/class/nfe4_eventos.class.php');

		$T321_Id = isset($_REQUEST['T321_Id']) ? (int)$_REQUEST['T321_Id'] : 0;
		$eventosNFe = new classeNFeEventos();
		$retorno = $eventosNFe->baixarXmlEvento($T321_Id);
		if (empty($retorno['sucesso'])) {
			die(isset($retorno['mensagem']) ? $retorno['mensagem'] : 'Não foi possível baixar o XML do evento.');
		}

		header('Content-Type: text/xml; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $retorno['nomeArquivo'] . '"');
		echo $retorno['conteudo'];
		die();
	break;

	case 'ven003CriarCancelamentoEventoNFe':
		require_once('hardness/class/nfe4_eventos.class.php');

		$T321_Id = isset($_REQUEST['T321_Id']) ? (int)$_REQUEST['T321_Id'] : 0;
		$eventosNFe = new classeNFeEventos();
		$retorno = $eventosNFe->criarCancelamentoEvento($T321_Id, $g['usuarioAtual']);

		$resposta['code'] = !empty($retorno['sucesso']);
		$resposta['data'] = isset($retorno['mensagem']) ? $retorno['mensagem'] : 'Não foi possível criar o evento de cancelamento.';
	break;

	case 'ven003EnviarEventoNFe':
		require_once('hardness/class/nfe4_eventos.class.php');

		$T321_Id = isset($_REQUEST['T321_Id']) ? (int)$_REQUEST['T321_Id'] : 0;
		$eventosNFe = new classeNFeEventos();
		$retorno = $eventosNFe->gerarETransmitirEvento($T321_Id);

		$resposta['code'] = !empty($retorno['sucesso']);
		$resposta['data'] = isset($retorno['mensagem']) ? $retorno['mensagem'] : 'Não foi possível transmitir o evento.';
	break;

	case 'ven003AtualizarObservacoes':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$retorno = $VEN003->VEN003AtualizarObservacoes($T007_Id);
		$resposta['code'] = ($retorno === true);
		$resposta['data'] = ($retorno === true) ? 'Observações atualizadas com sucesso' : $retorno;
	break;

	case 'ven003CheckboxMarcarNota':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		$T007=mysql_query("UPDATE T007 SET T007_Flag_Marcado='{$selected}' where T007_Id='{$T007_Id}'");
		if($T007_Id){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
		}
	break;
	
	case 'abreXML':
		global $g;
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
		    header('Content-Disposition: attachment; filename="'.$array['T007_Numero_Protocolo_Nfe'].'.xml"');
		
		    // The PDF source is in original.pdf
		    readfile($cArquivo);
		}
		else{
		   echo "XML Inexistente";
		}
		die();
	break;


	case 'abreXMLCCe':
		global $g;
		$T199_Id = $r_T199_Id;

		$T199 = mysql_query("SELECT T007_Chave_Acesso_Nfe, 
									T199_Sequencial_Evento, 
									T199_Flag_Enviado, 
									T200_Retorno_XML 
							   FROM T199 
						  LEFT JOIN T200 on T200_T199_Id=T199_Id AND T200_Status='135' 
						  LEFT JOIN T007 on T199_T007_Id=T007_Id 
						      WHERE T199_Id='$T199_Id'");
		$mT199=mysql_fetch_assoc($T199);
		
		if ($mT199['T199_Flag_Enviado']=='N')
		{
			echo "Essa carta de correção ainda não foi enviada";
		}
		
		if (empty($mT199['T200_Retorno_XML']))
		{
			echo "Essa carta de correção ainda não tem xml de retorno";
		}
        
		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="'.$mT199['T007_Chave_Acesso_Nfe']."_".$mT199['T199_Sequencial_Evento'].".xml".'"');
		echo $mT199['T200_Retorno_XML'];
	break;	

	case 'BaixarXMLNFEMarcada':
		global $g;
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();
		$extra = base64_decode($r_extra);
		$extra = gInsertExtraWhere($extra, "T007_Flag_Impresso='M'");
		log("$extra");
		$retorno = $VEN003->exportarXMLSelecionados($extra);
		if (!is_array($retorno)) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$retorno = $retorno[0];
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
    break;

	case 'excluirProduto':
		$T008_Id = isset($_REQUEST['T008_Id']) ? $_REQUEST['T008_Id'] : false;
		$T007_Id = isset($_REQUEST['T008_T007_Id']) ? $_REQUEST['T008_T007_Id'] : false;
		
		$result = $VEN003->excluirProduto($T008_Id, $T007_Id);
		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;
		}
		
	break;

	case 'abreXMLCancelamento':
		global $g;
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		
		$sql = 'select T007_Numero_Protocolo_Cancelamento_Nfe, T007_Data_Emissao from T007 WHERE T007_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);
		
		
		$cAno_Mes_NFe=substr($array['T007_Data_Emissao'],0,7);
		$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
		//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
		$cArquivo="$Diretorio_NFe/xml/autorizado/ID".$array['T007_Numero_Protocolo_Cancelamento_Nfe'].".xml";
		
		if (file_exists($cArquivo)){
		    // We'll be outputting a PDF
		    header('Content-type: text/xml');
		
		    // It will be called downloaded.pdf
		    header('Content-Disposition: attachment; filename="'.$array['T007_Numero_Protocolo_Cancelamento_Nfe'].'"');
		
		    // The PDF source is in original.pdf
		    readfile($cArquivo);
		}
		else{
		   echo "XML Inexistente";
		}
		die();
	break;
	
	case 'abreXMLAssinado':
		global $g;
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		
		$sql = 'select T007_Chave_Acesso_Nfe, T007_C004_Id, T007_Data_Emissao from T007 WHERE T007_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);
		
		
		$cAno_Mes_NFe=substr($array['T007_Data_Emissao'],0,7);
		$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
		//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
		$cArquivo="$Diretorio_NFe/xml/assinado/NFe".$array['T007_Chave_Acesso_Nfe'].".xml";
		if (!file_exists($cArquivo)){
			$cArquivo="$Diretorio_NFe/xml/assinado/".$array['T007_Numero_Nota_Fiscal'].'_'.$array['T007_C004_Id'].".xml";
		}
		
		if (file_exists($cArquivo)){
		    // We'll be outputting a PDF
		    header('Content-type: text/xml');
		
		    // It will be called downloaded.pdf
		    header('Content-Disposition: attachment; filename="'.$array['T007_Chave_Acesso_Nfe'].'"');
		
		    // The PDF source is in original.pdf
		    readfile($cArquivo);
		}
		else{
		   echo "XML Inexistente";
		}
		die();
	break;

	case 'baixaXML':
		global $g;
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		
		$sql = 'select T007_Chave_Acesso_Nfe, T007_Numero_Protocolo_Nfe, T007_C004_Id, T007_Data_Emissao, T007_Arquivo_XML from T007 WHERE T007_Id ='.$id;
		$result = mysql_query($sql);
		$array = mysql_fetch_assoc($result);


		$cAno_Mes_NFe=substr($array['T007_Data_Emissao'],0,7);
		$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
		$arquivo = "$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['T007_Numero_Protocolo_Nfe'].".xml";

		if (file_exists($arquivo)){
			die("O arquivo XML autorizado já existe na pasta");
		}
		if (empty($array['T007_Arquivo_XML'])){
		    die("O campo Arquivo XML está vazio este registro");
		}
				
		//Abre arquivo para gravação
		$fp = fopen($arquivo, "a+");

		if (!$fp){
			die("ERRO criando arquivo $arquivo <br><br>" . $array['T007_Arquivo_XML']);
		}

		//Escreve no arquivo aberto.
		$retorno = fwrite($fp, $array['T007_Arquivo_XML']);

		if (!$retorno){
			die("ERRO gravando conteúdo arquivo $arquivo <br><br>" . $array['T007_Arquivo_XML']);
		}

		//Fecha o arquivo.
		$retorno = fclose($fp);

		if ($retorno){

			die("Gravado XML: $arquivo <br> " . $array['T007_Arquivo_XML']);

		} else {

			die("ERRO! fechando arquivo $arquivo <br> " . $array['T007_Arquivo_XML']);
		}

	break;


	case 'xatualizarParcelas':
			require_once("bibliotecas/classes/VEN003.php");
			
			$T118_Id 		= isset($r_T118_Id) ? $r_T118_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			$VEN003 = new VEN003();
			
			$retorno = $VEN003->ven003AtualizarParcelas($T118_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;

	case 'atualizarParcelas':
		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
		
		$T118_Id 		= isset($r_T118_Id) ? $r_T118_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		$dadosTabela = array('tabela' => 'T118', 
								 'tabelaId' => $T118_Id,
								 'tabelaPai' => 'T007',
								 'campos' => 'T007_Data_Emissao as dataEmissao,T118_Prazos_Dias as prazoDias,T118_Numero_Parcela as numeroParcela, T007_Id as tabelaPaiId', 
                                 'where' => "T118_Id='{$T118_Id}'", 
                                 'campoEditado' => $campoEditado, 
                                 'valorAntigo' => $valorAntigo, 
                                 'novoValor' => $novoValor);

		if ($campoEditado == 'T118_Prazos_Dias') {
			$retorno = $VEN001->atualizarPrazoGridPagamento($dadosTabela);
	 	} else if ($campoEditado == 'T118_Data_Vencimento') {
			$retorno = $VEN001->atualizarVencimentoGridPagamento($dadosTabela);
 		} else if ($campoEditado == 'T118_Valor_Parcela') {
				$retorno = $VEN001->atualizarValorParcelaGridPagamento($dadosTabela);
 		}
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'excluirNFe':
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();
		
		$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
		$retorno = $VEN003->ven003excluirNota($T007_Id);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Nota excluída com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
    
    case 'ven003ImprimirCce':
        require_once("bibliotecas/classes/VEN003.php");
        $VEN003 = new VEN003();
        $T199_Id = isset($r_T199_Id) ? $r_T199_Id : false;
        $alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
        
        $retorno = $VEN003->montarEmailPdfCce($T199_Id);
        $resposta['code'] = true;
        $resposta['data'] = isset($retorno['AnexosWeb'][1]) ? $retorno['AnexosWeb'][1] : '';
    break;

    case 'ven003MarcarImprimir':
    	$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
    	$sql = mysql_query("SELECT * FROM T007 WHERE T007_Id = '{$T007_Id}'");
		$res = mysql_fetch_array($sql);
		if($res['T007_Flag_Impresso'] != 'M') {
			mysql_query("UPDATE T007 SET T007_Flag_Impresso = 'M' WHERE T007_Id = '{$T007_Id}'");
		} else {
			if(!empty($res['T007_Numero_Nota_Fiscal'])){
				mysql_query("UPDATE T007 SET T007_Flag_Impresso = 'S' WHERE T007_Id = '{$T007_Id}'");
			} else {
				mysql_query("UPDATE T007 SET T007_Flag_Impresso = 'N' WHERE T007_Id = '{$T007_Id}'");
			}
		}
		$resposta['code'] = true;
    break;

    case 'ven003ImprimirMarcadas':
		global $g;
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();
		$extra = base64_decode($r_extra);

		$flagType = (empty($r_flagType) ? "T007_Flag_Impresso='M'" : base64_decode($r_flagType));

		//die($extra);
		$extra = gInsertExtraWhere($extra, $flagType);
		$extra = gInsertGroupBy($extra, "T007_Id");
		// Valir o LEFT JOIN T007 com espaço no final para nao pegar a tabela estendida T007A
		if (strpos(strtoupper($extra),'LEFT JOIN T007 ')){
			$sql = "SELECT T007_Id,T007_Numero_Protocolo_Nfe,T007_Data_Emissao,if(T007_Flag_ACP!='3',LPAD(T007_Numero_Nota_Fiscal,7,'0'),T007_Numero_Nota_Fiscal) as NFe FROM T008 {$extra}";
		} else {
			$sql = "SELECT T007_Id,T007_Numero_Protocolo_Nfe,T007_Data_Emissao,if(T007_Flag_ACP!='3',LPAD(T007_Numero_Nota_Fiscal,7,'0'),T007_Numero_Nota_Fiscal) as NFe FROM T007 {$extra}";
		}
		$retorno = $VEN003->imprimirNFeMarcada(false, $sql);
		if($retorno === true){
			require_once("bibliotecas/PDFMerger/PDFMerger.php");
			$pdf = new \PDFMerger;
			$sql = mysql_query($sql);
			while($res = mysql_fetch_array($sql)){
				if(!empty($res['T007_Numero_Protocolo_Nfe'])) {
					$AnoMesNFe=substr($res['T007_Data_Emissao'],0,7);
					$arquivoXML = '../hardness/'.$g['pathDados'].'nfe/xml/autorizado/'.$AnoMesNFe.'/'.$res['T007_Numero_Protocolo_Nfe'].'.xml';
					$pdf->addPDF('../hardness/'.$g['pathDados'].'nfe/danfe/danfe_'.$res['T007_Numero_Protocolo_Nfe'].'.pdf', 'all');
				}
			}
			$pdf->merge('../hardness/'.$g['pathDados'].'nfe/danfe', 'danfe.pdf');
			die();
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nao foi possível gerar os DANFES para impressao';
		}
    break;

    case 'ven003checkMarcarTodos':
    	$sqlFiltro = (isset($r_sqlFiltro)) ? $r_sqlFiltro : false;
		$sqlFiltroNormal = base64_decode($sqlFiltro);
		$sqlFiltroNormal = str_replace("\\", "", $sqlFiltroNormal);
		
		$retorno = mysql_query($sqlFiltroNormal);
		while($linha = mysql_fetch_array($retorno)){
			mysql_query("UPDATE T007 SET T007_Flag_Impresso = 'M' WHERE T007_Id = '{$linha['T007_Id']}'");
		}
		if(mysql_num_rows($retorno) > 0){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhuma nota foi filtrada';
		}
    break;

    case 'ven003checkDesmarcarTodos':
    	$sql = mysql_query("SELECT * FROM T007 WHERE T007_Flag_Impresso = 'M' AND T007_C004_Id = '{$g['empresaAtual']}'");
		while($res = mysql_fetch_array($sql)){	
			if(!empty($res['T007_Numero_Nota_Fiscal'])){
				mysql_query("UPDATE T007 SET T007_Flag_Impresso = 'S' WHERE T007_Id = '{$res['T007_Id']}'");
			} else {
				mysql_query("UPDATE T007 SET T007_Flag_Impresso = 'N' WHERE T007_Id = '{$res['T007_Id']}'");
			}
		}
		if(mysql_num_rows($sql) > 0){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
    break;

    case 'cancelarNotaFiscal':
    	$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
    	$retorno = $VEN003->verificaCancelarNF($T007_Id);

    	if($retorno === true){
    		$resposta['code'] = true;
    	} else {
    		$resposta['code'] = false;
    		$resposta['data'] = $retorno;	
    	}
    break;

    case 'denegarNFe':
    	$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
    	$retorno = $VEN003->denegarNFe($T007_Id);

    	if($retorno === true){
    		$resposta['code'] = true;
    	} else {
    		$resposta['code'] = false;
    		$resposta['data'] = $retorno;	
    	}
    break;

    case 'estornarCancelarNotaFiscal':
    	$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
    	$retorno = $VEN003->estornarCancelamento($T007_Id);

    	if($retorno === true){
    		$resposta['code'] = true;
    	} else {
    		$resposta['code'] = false;
    		$resposta['data'] = $retorno;	
    	}
    break;

	case 'verificarGerarNotaDevolucao':
    	$resposta['code'] = false;
    	$resposta['data'] = 'Preencha a quantidade dos produtos que serão devolvidos.';
    	$r_sql = str_replace('LIMIT 0,100', ' ', $r_sql);
    	$res = mysql_query($r_sql);
    	$D024_Id = false;
    	while ($row = mysql_fetch_assoc($res)) {
			
    		if ($row['T008_Quantidade_Devolucao'] > 0) {
    			$resposta['code'] = true;
    			if (!$D024_Id) { $D024_Id = $row['D024_Id']; }
    			if ($D024_Id != $row['D024_Id']) {
    				$resposta['code'] = false;
	    			$resposta['data'] = 'Notas fiscais com clientes diferentes! As notas devem ser do mesmo cliente.';
	    			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	    			die();
    			}

				// Verifica se ja tem nota de devolucao e conta a quantidade ja devolvida
				$T008 = mysql_query("SELECT SUM(T008_Quantidade) AS quantidadeJaGerada,
											GROUP_CONCAT(T007_Numero_Nota_Fiscal SEPARATOR ', ') AS T007_Numero_Nota_Fiscal
									FROM T008
							           LEFT JOIN T007 ON T007_Id = T008_T007_Id
									WHERE T008_T008_Id = {$row['T008_Id']} AND T007_Flag_Cancelada != 'S'");

				$mT008 = mysql_fetch_assoc($T008);
				
				$saldo = $row['T008_Quantidade'] - $mT008['quantidadeJaGerada'];
				// Saldo menos a quantidade que está sendo devolvida
				$temSaldo         				= ($saldo - $row['T008_Quantidade_Devolucao']) >= 0;												
				$mT008['quantidadeJaGerada'] 	= gCorrigeNumero($mT008['quantidadeJaGerada'],2);				
				
				
				if($mT008['quantidadeJaGerada'] !== null && !$temSaldo) {					
					$resposta['code'] = false;				
					$resposta['data'] = "A quantidade total do código {$row['D001_Codigo_Produto']} ultrapassou a quantidade da nota de origem <br> Já geradas as notas {$mT008['T007_Numero_Nota_Fiscal']} com quantidade total de  {$mT008['quantidadeJaGerada']}.";
					echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
					die();
				}	

				/*********************** CARLOS CARVALHO **************************************************************/
				if ($g['C031']['habilitaRNC'] == 'S') {
					require_once("bibliotecas/classes/VEN003.php");
					$VEN003 = new VEN003();
					$retorno = $VEN003->ValidarRNC($row);
					if($retorno === false){
						$resposta['code'] = false;				
						$resposta['data'] = "A RNC precisa ser gerada e aprovada.";
						echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
						die();
					}
				}
    		}
    	}
    break;

    case 'verificarGerarNotaDevolucaoRecebidas':
    	$resposta['code'] = false;
    	$resposta['data'] = 'Preencha a quantidade dos produtos que serão devolvidos.';
    	$r_sql = str_replace('LIMIT 0,100', ' ', $r_sql);
    	$res = mysql_query($r_sql);
    	$D024_Id = false;
    	while ($row = mysql_fetch_assoc($res)) {
    		if ($row['T014_Quantidade_Devolucao'] > 0) {
    			$resposta['code'] = true;
    			if (!$D024_Id) { $D024_Id = $row['D024_Id']; }
    			if ($D024_Id != $row['D024_Id']) {
    				$resposta['code'] = false;
	    			$resposta['data'] = 'Notas fiscais com clientes diferentes! As notas devem ser do mesmo cliente.';
	    			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	    			die();
    			}

				// Verifica se ja tem nota de devolucao e conta a quantidade ja devolvida
				$T008 = mysql_query("SELECT SUM(T008_Quantidade) AS quantidadeJaGerada,
											GROUP_CONCAT(T007_Numero_Nota_Fiscal SEPARATOR ', ') AS T007_Numero_Nota_Fiscal
									   FROM T008
							      LEFT JOIN T007 ON T007_Id = T008_T007_Id
								      WHERE T008_T014_Id = {$row['T014_Id']}
									    AND T007_Flag_Cancelada != 'S'
									    AND T007_Flag_Inutilizado != 'S'
								   GROUP BY T008_T014_Id");
				$mT008 = mysql_fetch_assoc($T008);

				// Quantidade ja gerada mais a quantidade que está sendo devolvida
				$saldo = $row['T014_Quantidade'] - $mT008['quantidadeJaGerada'];
				//$temSaldo			          	= (number_format($saldo,2) - number_format($row['T014_Quantidade_Devolucao'],2)) >= 0;
				$temSaldo			          	= ($saldo - $row['T014_Quantidade_Devolucao']) >= 0;
				$mT008['quantidadeJaGerada'] 	= gCorrigeNumero($mT008['quantidadeJaGerada']);				

				if ($mT008['quantidadeJaGerada'] != null && !$temSaldo) {
					$resposta['code'] = false;
					$resposta['data'] = "A quantidade total do código {$row['D001_Codigo_Produto']} ultrapassou a quantidade da nota de origem <br> Já geradas as notas {$mT008['T007_Numero_Nota_Fiscal']} com quantidade total de {$mT008['quantidadeJaGerada']}.";
					echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
					die();
				}
    		}		
    	}
    break;

    case 'ven003incluirNotaFiscalConsumidor':
    	global $g;
    	require_once('bibliotecas/classes/VEN003.php');
        $VEN003 = new VEN003();

    	if(!empty($g['C020']['C020_Cupom_Fiscal_Cliente'])){
	    	$sql = mysql_query("SELECT D024_Id FROM D024 WHERE D024_Id = '{$g['C020']['C020_Cupom_Fiscal_Cliente']}'");
			if (mysql_num_rows($sql)) {
				$row = mysql_fetch_assoc($sql);
				$idCliente = $row['D024_Id'];
			}
		}

		if(!empty($g['C031']['codigoClienteCupomFiscal'])){
			$idCliente = $g['C031']['codigoClienteCupomFiscal'];
		}

		if(empty($idCliente)){
			$resposta['code'] = false;
    		$resposta['data'] = 'Não foi possível incluir a NFC-e, cliente padrão não parametrizado, por gentileza entre em contato com o suporte técnico.';
		} else {
			$row = array();
			$row['T007_D024_Id']                  = $idCliente;
            $row['T007_Flag_ACP']                 = '8';
            $row['T007_Data_Emissao']             = date("d/m/Y");
            $row['T007_Hora_Emissao']             = date("H:i:s");
            $row['T007_Data_Saida']               = date("d/m/Y");
            $row['T007_Hora_Saida']               = date("H:i:s");
            $row['T007_C007_Id_Vendedor_Interno'] = $g['usuarioAtual'];
            //$row['T007_Versao_NFe']               = $g['C020']['C020_Versao_NFe'];
            $row['T007_Versao_NFe']               = $g['c004']['C004_Versao_NFe'];
            $row['T007_Flag_Entrada_Saida']       = 'S';
            $row['T007_Flag_Finalidade_NFe']      = '1';
            $row['T007_Flag_Frete']      		  = '9';
            $row['T007_Flag_Destino_Produto']     = '3';
            $row['T007_Casas_Decimais_Preco']     = $g['c004']['C004_Casas_Decimais_Preco'];
            $row['T007_Casas_Decimais_Quantidade']= '2';
            $row['T007_Flag_PDV']				  = 'S';
			$row['T007_Flag_Indicador_Presenca']  = '1';
            
            require_once('bibliotecas/classes/GFormWrap.php');
	        $form = new GFormWrap($row);	        
	        $retorno = $VEN003->incluirNota($form);
	        if($retorno === true){
	        	$resposta['code'] = true;
	        	$resposta['data'] = $g['mysqlLastId'];
	        } else {
	        	$resposta['code'] = false;
	        	$resposta['data'] = $retorno;
	        }
		}
    break;

    case 'ven003aplicarPercentualDesconto':
		$T008_Id 		= isset($r_T008_Id) ? $r_T008_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();		
		$retorno = $VEN003->ven003AplicarPercentualDesconto($T008_Id, $campoEditado, $valorAntigo, $novoValor);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

    case 'ven003MarcarConhecimento':
    	$T007_Id=isset($r_T007_Id) ? $r_T007_Id : false;
    	$selected=isset($r_selected) ? $r_selected : false;
    	mysql_query("UPDATE T007 SET T007_Flag_Gerar_Conhecimento='{$selected}' WHERE T007_Id='{$T007_Id}'");
		$resposta['code'] = true;
    break;

    case 'ven003GerarConhecimento':
		
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();		
		$retorno = $VEN003->ven003GerarConhecimento($r_extra);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Conhecimento(s) gerado com sucesso";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'gerarPDFboleto':
		unlink($g['pathRaiz']. $dir."boleto.pdf");
		$T007_Id=isset($r_T007_Id) ? $r_T007_Id : false;

		$link = '/hardness3/hardness/boleto/gerar_boleto.php?T007_Id=' . $T007_Id;					
		$filename = 'boleto_' . $T007_Id . '.pdf';
		// $filename = 'boleto.pdf';
					
		$link = str_replace(' ', '+', $link);
		$dir  = $g['pathDados'] . 'pdf/';
		is_dir($dir) or mkdir($dir, 0777, true);
		$file     = $dir . $filename;
		$fileFull = $g['pathRaiz'] . $file;
		$chmod = "xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --lowquality --margin-bottom 0 \"{$g['urlRaiz']}{$link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"";
		shell_exec($chmod);
		$resposta['code'] = true;
		$resposta['data'] = 'hardness3/'.$file;
	break;

	case 'ven003DeletarPDFBoleto':
		global $g;
		$T007_Id=isset($r_T007_Id) ? $r_T007_Id : false;

		$filename = 'boleto_' . $T007_Id . '.pdf';
		
		$dir  = $g['pathDados'] . 'pdf/';
		$fileFull = $g['pathRaiz'] . '/' . $dir . $filename;
		if(file_exists($fileFull)){
			sleep(3);
			unlink($fileFull);
		}
	break;

	case 'insereRegistroT001':
		global $g;
		$T008_Id=isset($r_T008_Id) ? $r_T008_Id : false;
		log("Estou no ajax");
		$retorno = $VEN003->insereRegistroT001($T008_Id); 
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Inserido registro do T001";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'ven003AssociacaoVendaCompra':
		global $g;

		$retorno = $VEN003->ven003AssociacaoVendaCompra($r_dataInicial,$r_dataFinal);

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Foram associados os produtos da venda com a compra.";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'ven003EnviarNFTray':

		$T007_Id = $r_T007_Id;

		require_once('bibliotecas/webservices/tray/envioDadosNFTray.php');
        $envioDadosNFTray = new EnvioDadosNFTray();

		$resposta = $envioDadosNFTray->enviarDados(0,$T007_Id);
	break;

	case 'atualizarUnidade':

		$T008A_Id = isset($_REQUEST['T008A_Id']) ? $_REQUEST['T008A_Id'] : false;
		$D184_Id  = isset($_REQUEST['D184_Id']) ? $_REQUEST['D184_Id'] : false;

		mysql_query("UPDATE T008A 
				  LEFT JOIN T008 ON T008_Id = T008A_T008_Id 
						SET T008A_Preco_Unitario_Comercial = if({$D184_Id} <= 0 OR {$D184_Id} != T008A_D184_Id, T008_Valor_Preco_Sem_Desconto_Unitario , T008A_Preco_Unitario_Comercial),
							T008A_D184_Id                  = '{$D184_Id}'
					  WHERE T008A_Id = '{$T008A_Id}'");	

		// Faz essa verifcação porque precisa atualizar a unidade tributavel na nota para enviar para o XML
		$D037 = mysql_query("SELECT unPadrao.D037_Unidade    AS unPadrao_D037_Unidade,
									unConversao.D037_Unidade AS unConversao_D037_Unidade,
									D006_Flag_Unidade_Tributavel,
									D005_D037_Id_Tributavel,
									T008A_D184_Id,
									D184_D037_Id,
									T008_D037_Id,
									D184_Id,
									T008_Id
		   				       FROM T008A
   						  LEFT JOIN T008 ON T008_Id = T008A_T008_Id
						  LEFT JOIN D006 ON D006_Id = T008_D006_Id
						  LEFT JOIN D005 ON D005_Id = T008_D005_Id
						  LEFT JOIN D184 ON D184_Id = T008A_D184_Id
						  LEFT JOIN D037 AS unPadrao ON unPadrao.D037_Id = T008_D037_Id
						  LEFT JOIN D037 AS unConversao ON unConversao.D037_Id = D184_D037_Id
						      WHERE T008A_Id = '{$T008A_Id}'");
		$mD037 = mysql_fetch_array($D037);		
		
		if($mD037['D006_Flag_Unidade_Tributavel'] == 'N' || empty($mD037['D005_D037_Id_Tributavel'])){

			$unidadeTributavel = $mD037['T008_D037_Id'];
			if($mD037['D184_Id'] > 0 && $mD037['D184_D037_Id'] > 0){
				// Se tiver unidade conversao, usa ela
				$unidadeTributavel = $mD037['D184_D037_Id'];
			}

			$mD037['D005_D037_Id_Tributavel'] = $unidadeTributavel;
		}

		$nomeUnidade = ($mD037['D184_Id'] > 0 && $mD037['D184_D037_Id'] > 0) ? $mD037['unConversao_D037_Unidade'] : $mD037['unPadrao_D037_Unidade'];
		
		mysqli_query("UPDATE T008 
			  		     SET T008_Unidade            = '{$nomeUnidade}',
				  		 	 T008_D037_Id_Tributavel = '{$mD037['D005_D037_Id_Tributavel']}'
					   WHERE T008_Id = '{$mD037['T008_Id']}'");	
		
		$resposta['code'] = true;

	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
