<?php
namespace hardness;
/**
 * Weiberlan
 * 10/11/2011
 * CAD - CAD011 - Cadastro de Bem
 *
 * Processamento de ações AJAX
 */
 
require_once('bibliotecas/classes/IMP003.php');
$IMP003 = new IMP003();
 
$resposta = array('code' => true, 'data' => array());
 switch($g['r'][3]) {
 	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T155_T075_Id, T155_Nome_Arquivo from T155 WHERE T155_Id ='.$id;
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
    
	case 'excluir':
        $T015_Id = isset($_REQUEST['T015_Id']) ? $_REQUEST['T015_Id'] : false;
        $verifica = $g['conf']->restricaoExcluir('T015_Id', $T015_Id);

        if ($verifica !== true) {
            $resposta['code'] = false;
            $resposta['data'] = $verifica;
        } else {
            // excluir
            $retorno = $IMP003->excluir($T015_Id);
            $resposta['data'] = 'T015_Id-' . $T015_Id;
        }
    break;
        
	case 'gravarCampo':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D122_Id = isset($_REQUEST['D122_Id']) ? $_REQUEST['D122_Id'] : false;
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		$IMP003->updateT161_Flag_Ok($D122_Id, $T075_Id, $selected);
		$resposta['data'] = date('d/m/Y');
	break;

	case 'imp003ImportarItensOC':
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;

		$retorno = $IMP003->importarItensOC($T075_Id, $D024_Id);
		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Itens importados com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizaT055Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T055_Id = isset($_REQUEST['T055_Id']) ? $_REQUEST['T055_Id'] : false;
		$D004_Id = getD004($T066_Id);

		mysql_query("UPDATE T055 SET T055_T066_Id = '{$T066_Id}' WHERE T055_Id = '{$T055_Id}'");
		
		$resposta['data'] = true;
	break;

	case 'imp003DefinirQuantidadeItensOC':
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$sql = isset($r_sql) ? $r_sql : false;
		$quantidade = isset($r_quantidade) ? $r_quantidade : false;

		$retorno = $IMP003->preencherQuantidadeItensOC($D024_Id, $sql, $quantidade);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'checkboxExcluirItemDI':
		$T055_Id  = isset($r_T055_Id) ? $r_T055_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysqli_query("UPDATE T055 SET T055_Flag_Excluir = '{$selected}' WHERE T055_Id = '{$T055_Id}'");
		$resposta['code'] = true;
	break;

	
	case 'checkboxMarcarDivergencia':
		$T055_Id  = isset($r_T055_Id) ? $r_T055_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;

		// atualiza divergencia no item da DI
		mysqli_query("UPDATE T055 SET T055_Flag_Produto_Divergencia = '{$selected}' WHERE T055_Id = '{$T055_Id}'");
		$resposta['code'] = true;
		
	break;

	case 'imp003verificaValidadeItem':
		$T055A_Id = isset($r_T055A_Id) ? $r_T055A_Id : false;
		$D001A = mysql_query("SELECT D001A_Flag_Validade
								FROM D001A
						   LEFT JOIN D001 ON D001_Id = D001A_D001_Id
						   LEFT JOIN D049 ON D001_Id = D049_D001_Id
						   LEFT JOIN D009 ON D049_Id = D009_D049_Id
						   LEFT JOIN T055 ON D009_Id = T055_D009_Id
						   LEFT JOIN T055A ON T055_Id = T055A_T055_Id
							   WHERE T055A_Id = '{$T055A_Id}'");
	
		$mD001A = mysql_fetch_array($D001A);
		if($mD001A['D001A_Flag_Validade'] == 'N'){
			mysql_query("UPDATE T055A SET T055A_Data_Validade = '0000-00-00' WHERE T055A_Id = '{$T055A_Id}'");
			$resposta['code'] = false;
			$resposta['data'] = 'Produto não tem controle de validade';
		}else{
			$resposta['code'] = true;
		}						   
	break;

	case 'downloadxml':
		$numeroDI = isset($r_T075_Numero_DI) ? $r_T075_Numero_DI : false;

		$Diretorio_DI=$g['pathDadosAntigo'] . 'di';
		$cArquivo="$Diretorio_DI/xml/recebido/".$numeroDI.".xml";

		if (file_exists($cArquivo)){
			// We'll be outputting a PDF
			header('Content-type: text/xml');

			// It will be called downloaded.pdf
			header('Content-Disposition: attachment; filename="'.$numeroDI.'.xml"');

			// The PDF source is in original.pdf
			readfile($cArquivo);
		}
		else{
			echo "XML Inexistente";
		}
		die();
	break;

	case 'processarOrdemDespesa':
		$T168_Id   = isset($r_T168_Id) ? $r_T168_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		$retorno = $IMP003->processarOrdemDespesa($T168_Id, $novoValor);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = $retorno;
		}
	break;

	case 'imp003ConfirmaValoresDespesas';
		global $g;
		$T075_Id = $r_T075_Id;
		$T168 = mysql_query("SELECT T168_Valor, 
									T168_Id,
									T168_Flag_ME,
									T168_Cotacao_ME 
							   FROM T168 
							  WHERE T168_T075_Id = '{$T075_Id}'");

		while($mT168 = mysql_fetch_array($T168)){
			
			if($mT168['T168_Flag_ME'] == 'S'){
				$mT168['T168_Cotacao_ME'] = ($mT168['T168_Cotacao_ME'] <= 0) ? 1 : $mT168['T168_Cotacao_ME'];
				$mT168['T168_Valor'] = $mT168['T168_Valor'] * $mT168['T168_Cotacao_ME'];
			}

			mysqli_query("UPDATE T168 SET T168_Valor_Confirmado = '{$mT168['T168_Valor']}' WHERE T168_Id='{$mT168['T168_Id']}'");
		}

		$resposta['code'] = true;
		
	break;

	case 'associarItemXML';
		global $g;
		$T055_Id = $r_T055_Id;
		$T310_Id = $r_T310_Id;

		$retorno = $IMP003->associarItemXML($T055_Id,$T310_Id);

		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = 'Item atualizado com sucesso';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}		
	break;

	case 'desvincularItemXML';
		global $g;
		$T055_Id = $r_T055_Id;
		$T310_Id = $r_T310_Id;

		$retorno = true;

		mysql_query("UPDATE T310 SET T310_T055_Id = ''
							   WHERE T310_Id = '{$T310_Id}'");
		
		$error = mysql_error();
		if(!empty($error)){
			$retorno = false;
		}

		if($retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = 'Item desvinculado com sucesso';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $error;
		}		
	break;

	case 'desvincularProduto':
		global $g;
		$T055_Id = isset($r_T055_Id) ? $r_T055_Id : false;
		$retorno = true;
		
		mysql_query("UPDATE T055
		   	      LEFT JOIN T055A ON T055_Id = T055A_T055_Id
				  	    SET T055_D009_Id 		   = '0',
						    T055_Descricao_Produto = T055A_Descricao_Produto_Fornecedor
					  WHERE T055_Id = '{$T055_Id}'");
		$error = mysql_error();
		if(!empty($error)){
			$retorno = $error;
		}
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Produto desvinculado";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'excluirDI':
		require_once("bibliotecas/classes/IMP003.php");
		$IMP003 = new IMP003();
		
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		$retorno = $IMP003->excluirDI($T075_Id);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "D.I excluída com sucesso!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
 }
 
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>