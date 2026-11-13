<?php
namespace hardness;
/**
 * Weiberlan
 * 10/11/2011
 * CAD - CAD011 - Cadastro de Bem
 *
 * Processamento de ações AJAX
 */
 
require_once('bibliotecas/classes/IMP005.php');
$IMP005 = new IMP005();
 
$resposta = array('code' => true, 'data' => array());
 switch($g['r'][3]) {
 	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T155_T295_Id, T155_Nome_Arquivo from T155 WHERE T155_Id ='.$id;
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
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		$IMP005->updateT161_Flag_Ok($D122_Id, $T295_Id, $selected);
		$resposta['data'] = date('d/m/Y');
	break;

	case 'imp005ImportarItensOC':
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;

		$retorno = $IMP005->importarItensOC($T295_Id, $D024_Id);
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Itens importados com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizaT296Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T296_Id = isset($_REQUEST['T296_Id']) ? $_REQUEST['T296_Id'] : false;
		$D004_Id = getD004($T066_Id);

		mysql_query("UPDATE T296 SET T296_T066_Id = '{$T066_Id}' WHERE T296_Id = '{$T296_Id}'");
		
		$resposta['data'] = true;
	break;

	case 'imp005DefinirQuantidadeItensOC':
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$sql = isset($r_sql) ? $r_sql : false;
		$quantidade = isset($r_quantidade) ? $r_quantidade : false;

		$retorno = $IMP005->preencherQuantidadeItensOC($D024_Id, $sql, $quantidade);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'imp005validaQuantidadeDI':
    	$T296_Id = isset($r_T296_Id) ? $r_T296_Id : false;
    	$r_novoValor = gCorrigeNumeroInverte($r_novoValor);
    	$r_valorAntigo = gCorrigeNumeroInverte($r_valorAntigo);
    	$novoValor = isset($r_novoValor) ? $r_novoValor : false;
    	$valorAntigo = isset($r_valorAntigo) ? $r_valorAntigo : false;

    	$T296 = mysql_query("SELECT T296_Quantidade_Saldo, IFNULL(T296_Id, 0) AS T296_Id FROM T296 WHERE T296_Id = '{$T296_Id}'");
    	$T296 = mysql_fetch_array($T296);
    	if($novoValor > $T296['T296_Quantidade_Saldo']){
			mysql_query("UPDATE T296 SET T296_Quantidade_DI = '{$valorAntigo}' WHERE T296_Id = '{$T296_Id}'");
			$resposta['data'] = "O valor informado é maior que saldo disponível";
			$resposta['code'] = false;
    	} else {
			$resposta['code'] = true;
    	}
    break;

	case 'imp005DefinirQuantidadeDI';
		global $g;
		$erro=0;
		mysql_query("START_TRANSACTION");
		$r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T296 = mysql_query($r_sql);
		while($mT296 = mysql_fetch_array($T296)){
			$upT296 = mysqli_query("UPDATE T296 SET T296_Quantidade_DI=T296_Quantidade_Saldo WHERE T296_Id='{$mT296['T296_Id']}'");
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

	case 'imp005LimparQuantidades':
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;

		$retorno = $IMP005->limparQuantidadeDI($D024_Id);

        if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
    break;

	case 'imp005VerificaItensGerarDI':
        $qtdProdutoParaDividir = 0;
        $r_sql = str_replace('LIMIT 0,100', '', $r_sql);
		$T296 = mysql_query($r_sql);
		while($mT296 = mysql_fetch_array($T296)) {
			if ($mT296['T296_Quantidade_DI'] > 0) {
				$qtdProdutoParaDividir++;
	        	if($mT296['T296_Quantidade_DI'] > $mT296['T296_Quantidade_Saldo']){
	        		$resposta['code'] = false;
	            	$resposta['data'] = "Quantidade informada para gerar a DI é maior que a quantidade disponível.";
	        	}
	        }
		}
		
        if(!$qtdProdutoParaDividir){
        	$resposta['code'] = false;
            $resposta['data'] = "Não há itens selecionados para gerar a DI";
        }
    break;

	case 'imp005GerarDI':
        $D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;
		
		$retorno = $IMP005->gerarDI($T295_Id, $D024_Id);

		if(!is_array($retorno)){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['T075_Id'];

		}

    break;

	case 'imp005AprovarPO':
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		
		$retorno = $IMP005->imp005AprovarPO($T295_Id);
		$resposta['code'] = true;
		$resposta['data'] = $retorno;
	break;

	case 'processarOrdemDespesa':
		$T300_Id = isset($r_T300_Id) ? $r_T300_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		$retorno = $IMP005->processarOrdemDespesa($T300_Id, $novoValor);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = $retorno;
		}
	break;

	case 'checkboxExcluirItemPO':
		$T296_Id  = isset($r_T296_Id) ? $r_T296_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysqli_query("UPDATE T296 SET T296_Flag_Excluir = '{$selected}' WHERE T296_Id = '{$T296_Id}'");
		$resposta['code'] = true;
	break;

	case 'adicionarProdutosDI':

		require_once('bibliotecas/classes/IMP005.php');
		$IMP005 = new IMP005();
		
		//T075_Id pode ser o T075_Numero_DI ou T075_Ordem_Compra
		$numeroDI = isset($r_numeroDI) ? $r_numeroDI : false;
		$D024_Id  = isset($r_D024_Id) ? $r_D024_Id : false;

		$T075 = mysql_query("SELECT T075_Id,
									T075_Numero_DI
							   FROM T075 
						  LEFT JOIN T007 ON T075_Id = T007_T075_Id
							  WHERE (T075_Numero_DI = '{$numeroDI}' OR T075_Ordem_Compra LIKE '%{$numeroDI}%')
							    AND T075_C004_Id = '{$g['empresaAtual']}'
								AND T007_Id IS NULL");

		$contador = mysql_num_rows($T075);
		if($contador == 1){
				$mT075   = mysql_fetch_array($T075);
				$DI 	 = (!empty($mT075['T075_Numero_DI'])) ? 'número '.$mT075['T075_Numero_DI'] : 'com ID '.$mT075['T075_Id'];
				$retorno = $IMP005->adicionarProdutosDI($mT075['T075_Id'], $D024_Id);
				if($retorno === true){
					$resposta['code']    = true;
					$resposta['data']    = 'Itens incluídos com sucesso na DI '.$DI; 
					$resposta['T075_Id'] = $mT075['T075_Id'];

				}else{
					$resposta['code'] = false;
					$resposta['data'] = $retorno; 
				}
		} else if ($contador > 1){
			$resposta['code'] = false;
			$resposta['data'] = "Foram encontrada {$contador} DIs com o número digitado, verifique";					
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível localizar a DI informada, verifique";
		}
	break;

	case 'cancelarPO':
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;
		$retorno = $IMP005->cancelarPO($T295_Id);
		
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
 }
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>