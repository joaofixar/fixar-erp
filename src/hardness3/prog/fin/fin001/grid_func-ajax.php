<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/

// Classes
require_once('bibliotecas/classes/FIN001.php');
$FIN001 = new FIN001();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$T002_Id = isset($_REQUEST['T002_Id']) ? $_REQUEST['T002_Id'] : false;
		if (empty($T002_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro não existe.';
		} else {
			$result = mysql_query("SELECT T117_Id FROM T002 LEFT JOIN T117 ON T117_Id = T002_T117_Id AND T117_Flag_Cancelado = 'N' WHERE T002_Id = $T002_Id");

			if (mysql_result($result, 0)) {
				$resposta['code'] = false;
				$resposta['data'] = 'Existe um boleto vinculado a este título.';
			} else {
				$retorno = $FIN001->excluir($T002_Id);
				if ($retorno === true) {
					$resposta['data'] = 'T002_Id-' . $T002_Id;
				} else {
					$resposta['code'] = false;
					$resposta['data'] = $retorno;
				}
			}
		}
	break;
	
	case 'excluirAd':
		$T020_Id = isset($_REQUEST['T020_Id']) ? $_REQUEST['T020_Id'] : false;
		$T020_T002_Id = isset($_REQUEST['T020_T002_Id']) ? $_REQUEST['T020_T002_Id'] : false;
		if (empty($T020_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro não existe.';
		} else {
			$retorno = $FIN001->excluirAcrescimoDesconto($T020_Id,$T020_T002_Id);
			if ($retorno === true) {
				$resposta['data'] = 'T020_Id-' . $T020_Id;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;	
	
	case 'verificaBaixar':			
		$retorno = $FIN001->listaItensParaBaixar(false,$r_extra);

		if(!is_array($retorno)){
			$resposta['code'] = '1';
			$resposta['data'] = $retorno;
		}else{
			if (count($retorno) == 0) {
				$resposta['code'] = '1';
				$resposta['data'] = 'Não foram selecionados títulos para baixar...';
			} else {
				$resposta['code'] = '0';
			}
		}
	break;
	
	case 'verificaBaixaAgrupada':
		$retorno = $FIN001->listaItensParaBaixaAgrupada(false,$r_extra);

		if(!is_array($retorno)){
			$resposta['code'] = '1';
			$resposta['data'] = $retorno;
		}else{
			if (count($retorno) == 0) {
				$resposta['code'] = '1';
				$resposta['data'] = 'Não foram selecionados títulos para baixar...';
			} else {
				$resposta['code'] = '0';
			}
		}
	break;

	case 'verificaSaldoCredito':
		$retorno = $FIN001->consultarSaldoCredito($r_extra);

		$resposta['code'] = !empty($retorno);
		$resposta['data'] = $retorno;
	break;
	
	case 'baixarSaldoCredito':
		$retorno = $FIN001->baixarSaldoCredito($r_extra);

        if ((!empty($retorno)) and (is_numeric($retorno))) {
			$resposta['code'] = '0';
			$resposta['data'] = $retorno;
        } else {
			$resposta['code'] = '1';
			$resposta['data'] = $retorno;
		}
	break;

	case 'verificaAgrupar':			
		if ($g['C031']['agrupamentoDesabilitado'] == 'S') {
			$itensAgrupar = $FIN001->listaItensParaAgruparSmart(false,$r_extra); //Carlos Carvalho
		}else{
			$itensAgrupar = $FIN001->listaItensParaAgrupar(false,$r_extra);
			
		}
		if($itensAgrupar === -1){
			$resposta['code'] = '1';
			$resposta['data'] = 'O saldo é menor do que o total do título. <br> Não é possível agrupar.';
		}else if(!is_array($itensAgrupar)){
			$resposta['code'] = '1';
			$resposta['data'] = $itensAgrupar;
		}else if (count($itensAgrupar) == 0) {
			$resposta['code'] = '1';
			$resposta['data'] = 'Não foram selecionados títulos para agrupar...';
		} else {
			$itensAgrupar = $FIN001->listaItensParaAgrupar(true,$r_extra);
			if (count($itensAgrupar) > 1) {
				$resposta['code'] = '2';
				$resposta['data'] = 'Mais de um cliente foi selecionado...';
			} else {
				$resposta['code'] = '0';
			}	
		}
		
		
	break;	
	
	case 'verificaRemessa':
		$resposta['code'] = '1';
		$resposta['data'] = 'A base local não registra boleto.';
		break;
		$itensRemessa = $FIN001->listaItensParaRemessa();
		if(!is_array($itensRemessa)){
			$resposta['code'] = '1';
			$resposta['data'] = $itensRemessa;
		}else if (count($itensRemessa) == 0) {
			$resposta['code'] = '1';
			$resposta['data'] = 'Não existem titulos marcados para gerar remessa.';
		} else {
			$resposta['code'] = '0';
			$resposta['data'] = count($itensRemessa);
		}
	break;	

	case 'verificaValidacaoGerarBoleto':
		$dadosQuery = array('where' => "T002_Id='{$r_T002_Id}'");
        $retorno = $FIN001->validacoesTravarT002($dadosQuery);
		 if($retorno!==false){
           	$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
		}
	break;
	
	case 'verificaReparcelar':
		$itensReparcelar = $FIN001->listaItensParaReparcelar(false,$r_extra);
		if(!is_array($itensReparcelar)){
			$resposta['code'] = '1';
			$resposta['data'] = $itensReparcelar;
		}else if (count($itensReparcelar) == 0) {
			$resposta['code'] = '1';
			$resposta['data'] = 'Não foram selecionados títulos para reparcelar...';
		} else {
			$itensReparcelar = $FIN001->listaItensParaReparcelar(true,$r_extra);
			if (count($itensReparcelar) > 1) {
				$resposta['code'] = '2';
				$resposta['data'] = 'Mais de um cliente foi selecionado...';
			} else {
				$resposta['code'] = '0';
			}
		}
	break;

	case 'verificaDuplicar':
		global $g;
		$extra = base64_decode($r_extra);	
		$extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Flag_Reparcelar='S'"));
		$res  = mysql_query("SELECT T002_Id FROM T002 
		LEFT JOIN D024 ON D024_Id = T002_D024_Id 
		LEFT JOIN D027 ON D027_Id = T002_D027_Id 
		LEFT JOIN T117 ON T117_Id = T002_T117_Id 
		LEFT JOIN D014 ON D014_Id = T002_D014_Id 
		LEFT JOIN D046 ON D046_Id = D024_D046_Id 
		LEFT JOIN T007 ON T007_Id = T002_T007_Id 
		LEFT JOIN T035 ON T035_Id = T002_T035_Id 
		LEFT JOIN C007 ON C007_Id = T007_C007_Id_Vendedor_Interno 
		LEFT JOIN T103 ON T002_Id = T103_T002_Id 
		WHERE {$extra}");

		$rows = mysql_num_rows($res);
		if ($rows > 0) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Nenhum título foi selecionado para duplicar';
		}
	break;
	
	case 'checkboxRm':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T002_Id = isset($_REQUEST['T002_Id']) ? $_REQUEST['T002_Id'] : false;
		$retorno = $FIN001->updateT002_Flag_Enviar_Banco($T002_Id, $selected);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'checkboxRp':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T002_Id = isset($_REQUEST['T002_Id']) ? $_REQUEST['T002_Id'] : false;	
		$FIN001->updateT002_Flag_Reparcelar($T002_Id, $selected);
	break;

	case 'checkboxBx':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T002_Id = isset($_REQUEST['T002_Id']) ? $_REQUEST['T002_Id'] : false;	
		$FIN001->updateT002_Flag_Baixar_Lote($T002_Id, $selected);
	break;	
	
	case 'downloadRemessa':		
		$T002_T035_Id = isset($_REQUEST['T002_T035_Id']) ? $_REQUEST['T002_T035_Id'] : false;
		$retorno = $FIN001->geraDownloadRemessa($T002_T035_Id);		
		echo $retorno;	
		die();
		
	break;	

	case 'carregaInputBaixar':
		$retorno = $FIN001->listaTotalBaixar(base64_decode($r_extra));
    	$resposta['data']=number_format($retorno['Total_Baixar'],2,',','.');
              
	break;		
	
	case 'carregaInputReparcelar':		
		$retorno = $FIN001->listaTotalReparcelar(base64_decode($r_extra));
    	$resposta['data']=number_format($retorno['Total_Reparcelar'],2,',','.');          
              
	break;	
	case 'carregaInputReparcelar2':
		$retorno = $FIN001->listaTotalReparcelar(base64_decode($r_extra));
    	$resposta['data']=number_format($retorno['Total_Reparcelar'],2,',','.');          
              
	break;	

	case 'carregaInputAgrupar':
		$retorno = $FIN001->listaTotalAgrupar(base64_decode($r_extra));
    	$resposta['data']=number_format($retorno['Total_Agrupar'],2,',','.');
              
	break;	

	case 'carregaInputRemessa':
		$retorno = $FIN001->listaTotalRemessa(base64_decode($r_extra));
    	$resposta['data']=number_format($retorno['Total_Remessa'],2,',','.');          
              
	break;		
	
	case 'baixarTitulosEditarCampo':
		
		$acaoId         	    = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$Campo_Editado 			= isset($_REQUEST['Campo_Editado']) ? $_REQUEST['Campo_Editado'] : false;
		$Conteudo_Campo_Editado = isset($_REQUEST['Conteudo_Campo_Editado']) ? $_REQUEST['Conteudo_Campo_Editado'] : false;

		if ($Campo_Editado == 'T103_Flag_Baixar_Lote')
		{
		   mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado == 'T002_Flag_Baixar_Lote')
		{		   
		   mysql_query("update T002 set $Campo_Editado='$Conteudo_Campo_Editado' where T002_Id='$acaoId'");
		}
		else if ($Campo_Editado == 'T103_Valor_Recebido')
		{
		   //$Conteudo_Campo_Editado=formata_valor_gravar($Conteudo_Campo_Editado,3);
			$Conteudo_Campo_Editado=str_replace('.', '', $Conteudo_Campo_Editado);
			$Conteudo_Campo_Editado=str_replace(',', '.', $Conteudo_Campo_Editado);		   	
			mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado == 'T103_D027_Id')
		{
		   mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado == 'T103_Valor_Desconto')
		{
		   $Conteudo_Campo_Editado=str_replace('.', '', $Conteudo_Campo_Editado);
		   $Conteudo_Campo_Editado=str_replace(',', '.', $Conteudo_Campo_Editado);
		   mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado == 'T103_Valor_Taxas')
		{
		   $Conteudo_Campo_Editado=str_replace('.', '', $Conteudo_Campo_Editado);
		   $Conteudo_Campo_Editado=str_replace(',', '.', $Conteudo_Campo_Editado);
		   mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado == 'T103_Valor_Juros')
		{
		   $Conteudo_Campo_Editado=str_replace('.', '', $Conteudo_Campo_Editado);
		   $Conteudo_Campo_Editado=str_replace(',', '.', $Conteudo_Campo_Editado);
		   mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}

	break;
	
	case 'atualizaTotaisBaixar':
		$total = $FIN001->carregaTotaisBaixar(base64_decode($r_extra));
		$resposta['data'] = array('fin001BaixarValor'=>number_format($total['Total'],2,',','.'),'fin001BaixarValorRecebido'=>number_format($total['TotalRecebido'],2,',','.'));
	break;	
	
	case 'fin001atualizarParcelas':
			require_once("bibliotecas/classes/FIN001.php");
			
			$T180_Id 		= isset($r_T180_Id) 		? $r_T180_Id 		: false;
			$campoEditado 	= isset($r_campoEditado) 	? $r_campoEditado 	: false;
			$valorAntigo 	= isset($r_valorAntigo) 	? $r_valorAntigo 	: false;
			$novoValor 		= isset($r_novoValor) 		? $r_novoValor 		: false;
			$valorTotal 	= isset($r_valorTotal) 		? $r_valorTotal 	: false;
			
			
			$FIN001 = new FIN001();
			
			$retorno = $FIN001->fin001AtualizarParcelas2($T180_Id, $campoEditado, $valorAntigo, $novoValor, $valorTotal);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'fin001Reparcelar':
			require_once("bibliotecas/classes/FIN001.php");
			$extra = base64_decode($r_extra);
			
			$D024_Id 		= isset($r_D024_Id) 	? $r_D024_Id : false;
			$C007_Id 		= isset($r_C007_Id) 	? $r_C007_Id : false;
			$valorSomado 	= isset($r_valorSomado) ? $r_valorSomado : false;
			
			$FIN001 = new FIN001();
			
			$retorno = $FIN001->fin001reparcelar($D024_Id, $C007_Id, $valorSomado, $extra);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'fin001LimparGrid':
			require_once("bibliotecas/classes/FIN001.php");
			
			$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
			
			$FIN001 = new FIN001();
			
			$sql = "DELETE FROM T180 WHERE T180_D024_Id='{$D024_Id}'";
			$retorno = mysql_query($sql);
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;


	case 'processarRetorno':
		$T037_Id = isset($_REQUEST['T037_Id']) ? $_REQUEST['T037_Id'] : false;
		$step = isset($_REQUEST['step']) ? $_REQUEST['step'] : false;
		$lastId = isset($_REQUEST['lastId']) ? (int)$_REQUEST['lastId'] : 0;
		$limit = isset($_REQUEST['limit']) ? (int)$_REQUEST['limit'] : 200;
		if ($limit < 10) $limit = 10;
		if ($limit > 500) $limit = 500;
		if (empty($T037_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'T037_Id não informado';
			break;
		}
		$T037 = mysql_query("SELECT T037_Codigo_Banco,
									D007_Layout_Cobranca 
							   FROM T037 
						  LEFT JOIN D007 ON D007_Id = T037_D007_Id
							  WHERE T037_Id = '{$T037_Id}'");
		$res = mysql_fetch_array($T037);
		$codigoBanco = $res['T037_Codigo_Banco'];
		if($res['D007_Layout_Cobranca'] == 2){
			// Bancos no CNAB240
			require_once("bibliotecas/layouts_remessa/" . $codigoBanco . "/retorno_". $codigoBanco . "_CNAB240.php");
		} else {
			require_once("bibliotecas/layouts_remessa/" . $codigoBanco . "/retorno_". $codigoBanco . ".php");
		}
		$RETORNO_REMESSA = new RETORNO_REMESSA();
		if ($step) {
			$pendenteWhere = "(T002_Data_Recebimento='0000-00-00' OR T002_Data_Recebimento IS NULL)";
			$total = 0;
			if ($lastId <= 0) {
				error_log("vai fazer quantas vezes a query?");
				$totalRes = mysql_query("SELECT COUNT(*) AS total
										   FROM T038
									  LEFT JOIN T002 ON T002_Id = T038_T002_Id
										  WHERE T038_T037_Id = '{$T037_Id}'
										    AND {$pendenteWhere}");
				$totalRow = mysql_fetch_array($totalRes);
				$total = (int)$totalRow['total'];
			}

			$detalhe = null;
			$opcoes = array(
				'limite' => $limit,
				'ultimoId' => $lastId,
			);
			$retorno = $RETORNO_REMESSA->processarRetorno($T037_Id, $opcoes, $detalhe);
			if($retorno === true){
				$novoLastId = (is_array($detalhe) && isset($detalhe['ultimoId'])) ? (int)$detalhe['ultimoId'] : $lastId;
				$lidos = (is_array($detalhe) && isset($detalhe['lidos'])) ? (int)$detalhe['lidos'] : 0;

				$done = ($lidos <= 0 || $novoLastId <= $lastId);

				$dataResposta = array(
					'done' => $done,
					'lastId' => $novoLastId,
					'batchRead' => $lidos,
					'total' => $total,
					'message' => $done ? 'Arquivo processado com sucesso' : 'Processando...'
				);

				$resposta['code'] = true;
				$resposta['data'] = $dataResposta;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		} else {
			$retorno = $RETORNO_REMESSA->processarRetorno($T037_Id);
			if($retorno === true){
				$resposta['code'] = true;
				$resposta['data'] = 'Arquivo processado com sucesso';
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;

	case 'fin001MarcarTodos':
		$extra = isset($r_extra) ? base64_decode($r_extra) : false;
		$campo = isset($r_campo) ? $r_campo : false;

		$T002 = mysql_query("SELECT * FROM T002 ".$extra);
		if($campo == "RM"){
			$count=0;
			while($mT002 = mysql_fetch_array($T002)){

				// Faz as mesmas verificações para exibir ou não o checkbox
				if($mT002['T002_Data_Recebimento']=='0000-00-00' && $mT002['T002_T035_Id']<=0 && $mT002['T002_Flag_Cancelada']!='S' && $mT002['T117_Nosso_Numero']!='' or $mT002['D027_Flag_Tipo']=='1'){
					if((!empty($mT002['T002_T117_Id']) or $mT002['D027_Flag_Tipo']=='1') && $mT002['T117_Flag_Cancelado'] != "S"){
						mysql_query("UPDATE T002 SET T002_Flag_Enviar_Banco='S' WHERE T002_Id='{$mT002['T002_Id']}'");
						$count++;
					}
				}
				$retorno=true;
			}

		} else if($campo == "BX"){
			$count=0;
			while($mT002 = mysql_fetch_array($T002)){

				// Faz as mesmas verificações para exibir ou não o checkbox
				if($mT002['T002_Data_Recebimento']=='0000-00-00' && $mT002['T002_Flag_Cancelada']!='S'){
					mysql_query("UPDATE T002 SET T002_Flag_Baixar_Lote='S' WHERE T002_Id='{$mT002['T002_Id']}'");
					$count++;
				}
			}
			$retorno=true;

		} else if($campo == "RP"){
			$count=0;
			while($mT002 = mysql_fetch_array($T002)){

				// Faz as mesmas verificações para exibir ou não o checkbox
				if($mT002['T002_Data_Recebimento']=='0000-00-00' && $mT002['T002_T035_Id']<=0 && $mT002['T002_Flag_Cancelada']!='S'){
					mysql_query("UPDATE T002 SET T002_Flag_Reparcelar='S' WHERE T002_Id='{$mT002['T002_Id']}'");
					$count++;
				}
			}
			$retorno=true;
		}
		
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Processo concluído com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'fin001DesmarcarTodos':
		$campo = isset($r_campo) ? $r_campo : false;

		//$T002 = mysql_query("SELECT * FROM T002 LEFT JOIN T117 on T117_Id=T002_T117_Id");
		if($campo == "RM"){
			$count=0;

			$sql = <<<EOT
				UPDATE 
				T002 
				LEFT JOIN T117 ON T002_T117_Id=T117_Id
				SET T002_Flag_Enviar_Banco='N' 
				WHERE T002_Data_Recebimento = '0000-00-00' AND T002_Flag_Cancelada != 'S' AND (T002_T035_Id<=0 OR T002_T035_Id IS NULL) AND T117_Nosso_Numero != '')
EOT;
			mysql_query($sql);
				$retorno=true;

		} else if($campo == "BX"){

			$sql = <<<EOT
				UPDATE 
				T002 
				LEFT JOIN T117 ON T002_T117_Id=T117_Id
				SET T002_Flag_Baixar_Lote='N' 
				WHERE T002_Data_Recebimento = '0000-00-00' AND T002_Flag_Cancelada != 'S'
EOT;
			mysql_query($sql);

			$retorno=true;

		} else if ($campo == "RP"){

			$sql = <<<EOT
				UPDATE 
				T002 
				LEFT JOIN T117 ON T002_T117_Id=T117_Id
				SET T002_Flag_Reparcelar='N' 
				WHERE T002_Data_Recebimento = '0000-00-00' AND T002_Flag_Cancelada != 'S' AND (T002_T035_Id<=0 OR T002_T035_Id IS NULL);
EOT;
			mysql_query($sql);

			$retorno=true;
		}
		
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Processo concluído com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'fin001MarcarVisto':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $T002_Id = isset($_REQUEST['T002_Id']) ? $_REQUEST['T002_Id'] : false;    
        mysql_query("UPDATE T002 SET T002_Flag_Titulo_Conferido = '{$selected}' WHERE T002_Id = '{$T002_Id}'");
    break;

	case 'agrupamentoAutomatico':
		global $g;
		$extra = base64_decode($r_extra);	
		$retorno = $FIN001->agrupamentoAutomatico($extra);

		if ($retorno == true) {
			$resposta['code'] = true;
			$resposta['data'] = "Títulos agrupados com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'abreDocumento':
        $id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;     
        $sql = 'select T242_Nome_Arquivo from T242 WHERE T242_Id ='.$id;
        $result = mysql_query($sql);
        $row = mysql_fetch_assoc($result);
        $pathinfo = pathinfo($row['T242_Nome_Arquivo']);
        $cArquivo = "{$g['pathDados']}documentos/contas_receber/{$id}.{$pathinfo['extension']}";
        if (file_exists($cArquivo))
        {
            $finfo = new \finfo();
            header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
            header('Content-Disposition: attachment; filename="' . $row['T242_Nome_Arquivo'] . '"');
            readfile($cArquivo);
        }
        else
        {
            echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
            return false;
        }
        die();
    break;

    case 'processarSaldoCliente':
    	global $g;
		$extra = base64_decode($r_extra);	
		$retorno = $FIN001->processarSaldoCliente($extra);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
    break;

	case 'processarSaldosCreditoTroca':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			$retorno = 'Usuário sem acesso à opção Contas a Receber.';
		} else {
			$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '';
			$clientes = isset($_REQUEST['clientes']) ? json_decode($_REQUEST['clientes'], true) : array();
			$justificativa = isset($_REQUEST['justificativa']) ? $_REQUEST['justificativa'] : '';
			$retorno = $FIN001->processarSaldosCreditoTroca($clientes, $tipo, $justificativa);
		}

		if (is_array($retorno)) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'abaterSaldoCreditoTroca':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			$retorno = 'Usuário sem acesso à opção Contas a Receber.';
		} else {
			$clienteId = isset($_REQUEST['clienteId']) ? (int)$_REQUEST['clienteId'] : 0;
			$tituloId = isset($_REQUEST['tituloId']) ? (int)$_REQUEST['tituloId'] : 0;
			$retorno = $FIN001->abaterSaldoCreditoTrocaEmTitulo($clienteId, $tituloId);
		}
		if (is_array($retorno)) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'fin001excluirRetorno':
		$T037_Id = isset($_REQUEST['T037_Id']) ? $_REQUEST['T037_Id'] : false;

		$retorno = $FIN001->fin001excluirRetorno($T037_Id);

		if($retorno === true){
			$resposta['code']=true;
			$resposta['data']="Registro excluído com sucesso";
		}else{
			$resposta['code']=false;
			$resposta['data']=$retorno;
		}
		
	break;	

	case 'downloadRetorno':
		$T037_Id = isset($_REQUEST['T037_Id']) ? $_REQUEST['T037_Id'] : false;
		$retorno = $FIN001->geraDownloadRetorno($T037_Id);		
		echo $retorno;	
		die();
	break;

	case 'campoAutorizarBaixaRetorno':
		$T038_Id = isset($r_T038_Id) ? $r_T038_Id : false;
		$check = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T038 SET T038_Flag_Autorizado = '{$check}' WHERE T038_Id = '{$T038_Id}'");
		$resposta['code'] = true;
	break;

	case 'marcarTituloEnviarEmail':
		$T002_Id = isset($r_T002_Id) ? $r_T002_Id : false;
		$check = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE T002A SET T002A_Flag_Enviar_Email = '{$check}' WHERE T002A_T002_Id = '{$T002_Id}'");
		$resposta['code'] = true;
	break;

	case 'enviarMultiEmailBoleto':
		require_once("bibliotecas/classes/FIN001.php");
		$FIN001        = new FIN001();
		$erros         = '';
		$extra 		   = $r_extra;
		$extraOriginal = base64_decode($extra); 
		$extraOriginal = gInsertExtraWhere($extraOriginal, "T002A_Flag_Enviar_Email = 'S' AND T002_Valor_Saldo > 0");
		$enviarDanfe   = (!empty($r_enviarDanfe)) ? true : false;
		$emailPadrao   = (!empty($r_emailPadrao)) ? true : false;
		$emailLembrete = (!empty($r_emailLembrete)) ? true : false;
		$emailCobranca = (!empty($r_emailCobranca)) ? true : false;


		$T002 = mysql_query("SELECT T002_Id,T002_Numero_Duplicata FROM T002 {$extraOriginal}");
		log("qual query que fez agora para enviar boletos");
		while($mT002 = mysql_fetch_array($T002)){
			$retorno = $FIN001->enviarMultiEmailBoleto($mT002['T002_Id'], $enviarDanfe, $emailPadrao, $emailLembrete, $emailCobranca);
			if($retorno !== true){
				$erros .= "<b>".$mT002['T002_Numero_Duplicata']."</b> - Erro: ".$retorno."<br>";
			} 
		}
		if($erros != '') { 
			$erros = substr($erros, 0, -2);
			$resposta['data'] = "Erros no envio dos boletos abaixo:<br><br>".$erros;
			$resposta['code'] = false;
		} else {
			$resposta['data'] = "Os boletos foram enviados com sucesso!";
			$resposta['code'] = true;
		} 
				
	break;

	case 'verificaDividir':
        $itensDividir = $FIN001->listaItensParaDividir(false,$r_extra);
        if (count($itensDividir) == 0) {
            $resposta['code'] = '1';
            $resposta['data'] = 'Não foram selecionados títulos para dividir...';
        } else {
            $itensDividir = $FIN001->listaItensParaDividir(true,$r_extra);
            if (count($itensDividir) > 1) {
                $resposta['code'] = '2';
                $resposta['data'] = 'Mais de um cliente foi selecionado...';
            } else {
                $resposta['code'] = '0';
            }
        }
    break;

	case 'fin001atualizarParcelasDivisao':
        $T317_Id      = isset($r_T317_Id)      ? $r_T317_Id      : false;
        $campoEditado = isset($r_campoEditado) ? $r_campoEditado : false;
        $valorAntigo  = isset($r_valorAntigo)  ? $r_valorAntigo  : false;
        $novoValor    = isset($r_novoValor)    ? $r_novoValor    : false;
        $valorTotal   = isset($r_valorTotal)   ? $r_valorTotal   : false;        
        
        require_once("bibliotecas/classes/FIN001.php");
        $FIN002 = new FIN001();
        $retorno = $FIN001->atualizarParcelasDivisao($T317_Id, $campoEditado, $valorAntigo, $novoValor, $valorTotal);
        if ($retorno === true) {
            $resposta['code'] = true;
        } else {
            // excluir
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }
    break;

	case 'fin001Dividir':
        $extra        = base64_decode($r_extra);    
        $C007_Id      = isset($r_C007_Id)     ? $r_C007_Id : false;
        $valorSomado  = isset($r_valorSomado) ? $r_valorSomado : false;
        
        require_once("bibliotecas/classes/FIN001.php");
        $FIN001 = new FIN001();        
        $retorno = $FIN001->fin001Dividir($C007_Id, $valorSomado, $extra);
        
        if ($retorno === true) {
            $resposta['code'] = true;
        } else {
            // excluir
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }
    break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
