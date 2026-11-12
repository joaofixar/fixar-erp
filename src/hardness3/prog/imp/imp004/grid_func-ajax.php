<?php
namespace hardness;
/**
 * Weiberlan
 * 10/11/2011
 * CAD - CAD011 - Cadastro de Bem
 *
 * Processamento de ações AJAX
 */
 
require_once('bibliotecas/classes/IMP004.php');
$IMP004 = new IMP004();
 
$resposta = array('code' => true, 'data' => array());
 switch($g['r'][3]) {
 	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T155_T293_Id, T155_Nome_Arquivo from T155 WHERE T155_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T155_Nome_Arquivo']);
		$cArquivo =  "{$g['pathDados']}di/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['T155_Nome_Arquivo'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo {$cArquivo} não encontrado. Por favor, contate o suporte tecnico.";
			return false;
		}
		die();
	break;
        
	case 'gravarCampo':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D122_Id = isset($_REQUEST['D122_Id']) ? $_REQUEST['D122_Id'] : false;
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		$IMP004->updateT161_Flag_Ok($D122_Id, $T293_Id, $selected);
		$resposta['data'] = date('d/m/Y');
	break;

	case 'imp004ImportarItensOC':
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;

		$retorno = $IMP004->importarItensOC($T293_Id, $D024_Id);
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Itens importados com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizaT294Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T294_Id = isset($_REQUEST['T294_Id']) ? $_REQUEST['T294_Id'] : false;
		$D004_Id = getD004($T066_Id);

		mysql_query("UPDATE T294 SET T294_T066_Id = '{$T066_Id}' WHERE T294_Id = '{$T294_Id}'");
		
		$resposta['data'] = true;
	break;

	case 'imp004DefinirQuantidadeItensOC':
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$sql = isset($r_sql) ? $r_sql : false;
		$quantidade = isset($r_quantidade) ? $r_quantidade : false;

		$retorno = $IMP004->preencherQuantidadeItensOC($D024_Id, $sql, $quantidade);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'imp004validaQuantidadePO':
    	$T294_Id = isset($r_T294_Id) ? $r_T294_Id : false;
    	$r_novoValor = gCorrigeNumeroInverte($r_novoValor);
    	$r_valorAntigo = gCorrigeNumeroInverte($r_valorAntigo);
    	$novoValor = isset($r_novoValor) ? $r_novoValor : false;
    	$valorAntigo = isset($r_valorAntigo) ? $r_valorAntigo : false;

    	$T294 = mysql_query("SELECT T294_Quantidade_Saldo, IFNULL(T294_Id, 0) AS T294_Id FROM T294 WHERE T294_Id = '{$T294_Id}'");
    	$T294 = mysql_fetch_array($T294);
    	if($novoValor > $T294['T294_Quantidade_Saldo']){
			mysql_query("UPDATE T294 SET T294_Quantidade_PO = '{$valorAntigo}' WHERE T294_Id = '{$T294_Id}'");
			$resposta['data'] = "O valor informado é maior que saldo disponível";
			$resposta['code'] = false;
    	} else {
			$resposta['code'] = true;
    	}
    break;

	case 'imp004DefinirQuantidadePO';
		global $g;
		$erro=0;
		mysql_query("START_TRANSACTION");
		$r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T294 = mysql_query($r_sql);
		while($mT294 = mysql_fetch_array($T294)){
			$upT294 = mysqli_query("UPDATE T294 SET T294_Quantidade_PO=T294_Quantidade_Saldo WHERE T294_Id='{$mT294['T294_Id']}'");
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

	case 'imp004LimparQuantidades':
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;

		$retorno = $IMP004->limparQuantidadePO($D024_Id);

        if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
    break;

	case 'imp004VerificaItensGerarPO':
        $qtdProdutoParaDividir = 0;
        $r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T294 = mysql_query($r_sql);
		while($mT294 = mysql_fetch_array($T294)) {
			if ($mT294['T294_Quantidade_PO'] > 0) {
				$qtdProdutoParaDividir++;
	        	if($mT294['T294_Quantidade_PO'] > $mT294['T294_Quantidade_Saldo']){
	        		$resposta['code'] = false;
	            	$resposta['data'] = "Quantidade informada para gerar a PO é maior que a quantidade disponível.";
	        	}
	        }
		}
		
        if(!$qtdProdutoParaDividir){
        	$resposta['code'] = false;
            $resposta['data'] = "Não há itens selecionados para gerar a PO";
        } else {
			$resposta['code'] = true;
		}
    break;

	case 'imp004GerarPO':
        $D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
				
		$retorno = $IMP004->gerarPO($T293_Id,$D024_Id);

		if(!is_array($retorno)){			
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['T295_Id'];
		}

    break;

	case 'processarOrdemDespesa':
		$T299_Id = isset($r_T299_Id) ? $r_T299_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		$retorno = $IMP004->processarOrdemDespesa($T299_Id, $novoValor);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = $retorno;
		}
	break;

	case 'checkboxExcluirItemQuotation':
		$T294_Id  = isset($r_T294_Id) ? $r_T294_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysqli_query("UPDATE T294 SET T294_Flag_Excluir = '{$selected}' WHERE T294_Id = '{$T294_Id}'");
		$resposta['code'] = true;
	break;
 }
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>