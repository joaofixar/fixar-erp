<?php
namespace hardness;
/**
 * Weiberlan
 * 10/11/2011
 * CAD - CAD011 - Cadastro de Bem
 *
 * Processamento de ações AJAX
 */
 
 //Classes
 require_once('bibliotecas/classes/CAD001.php');
$CAD001 = new CAD001();
 
$resposta = array('code' => true, 'data' => array());
 switch($g['r'][3]) {
 	case 'abreDocumento':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select D087_D024_Id, D087_Nome_Arquivo from D087 WHERE D087_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['D087_Nome_Arquivo']);
		$cArquivo = "{$g['pathDados']}documentos/D024_" . str_repeat('0',5-strlen($row['D087_D024_Id'])) . $row['D087_D024_Id']."/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['D087_Nome_Arquivo'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
			return false;
		}
		die();
	break;	
	
	case 'abreDocumentoVeiculo':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select D127_Nome_Arquivo FROM D127 WHERE D127_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['D127_Nome_Arquivo']);
		$cArquivo = "{$g['pathDados']}veiculos/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['D127_Nome_Arquivo'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
			return false;
		}
		die();
	break;
    
    case 'cad001CheckboxImprimir':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
        
        $CAD001->updateCheckboxImprimir($D024_Id, $selected);
    break;
    
    case 'cad001DesmarcarImprimir':
        $sql = "update D024 set D024_Flag_Imprimir_Etiqueta = 'N'";
         mysql_query($sql);    
    break;
    
    case 'cad001VerificaProdutosImprimir':
        $sql = "SELECT D024_Id FROM D024 WHERE D024_Flag_Imprimir_Etiqueta='S'";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)<=0) {
            $resposta['code'] = false;
        }        
    break;
	
	case 'buscaCEP':
		$W004_Id = isset($r_W004_Id) ? $r_W004_Id : false;
		$sql = mysql_query("SELECT * FROM global.W004 WHERE W004_Id ='{$W004_Id}'");
		$result = mysql_fetch_array($sql);
		
		$sql2 = mysql_query("SELECT D020_Id FROM D020 WHERE D020_Codigo_IBGE ='{$result['W004_IBGE_Cidade']}'");
		$result2 = mysql_fetch_array($sql2);
		
		$sql3 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Codigo_IBGE ='{$result['W004_IBGE_UF']}'");
		$result3 = mysql_fetch_array($sql3);
		
		$sql4 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais LIKE '%BRASIL%'");
		$result4 = mysql_fetch_array($sql4);
		
		if(mysql_num_rows($sql) > 0){
			$resposta['code'] = true;
			$resposta['data']['EnderecoDb'] = $result['W004_Logradouro'];
			$resposta['data']['Cep'] = $result['W004_Cep'];
			$resposta['data']['BairroDb'] = $result['W004_Bairro'];
			$resposta['data']['CidadeDb'] = $result2['D020_Id'];
			$resposta['data']['UfDb'] = $result3['D018_Id'];
			$resposta['data']['PaisDb'] = $result4['D030_Id'];
		}else{
			$resposta['code'] = false;
			$resposta['data'] = "CEP não encontrado";
		}
		
	break;

	case 'cad001CheckboxMarcarCliente':
		$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysql_query("UPDATE D024 SET D024_Flag_Mkt = '{$selected}' WHERE D024_Id = '{$D024_Id}'");
		$resposta['code'] = true;
	break;

	case 'verificarCodigo':
		$sql = "SELECT MAX(CAST(SUBSTR(D001_Codigo_Produto, 5) AS UNSIGNED))+1 AS Proximo FROM D001 WHERE D001_C008_Id = '{$r_C008_Id}' AND D001_Flag_Pre_Cadastro = 'N';";
		$res = mysql_query($sql);
		if (mysql_num_rows($res) > 0) {
			$row = mysql_fetch_assoc($res);
			if (empty($row['Proximo'])) {
				$resposta['data'] = 1;
			} else {
				$resposta['data'] = $row['Proximo'];
			}
		} else {
			$resposta['data'] = 1;
		}

		$resposta['data'] = $r_C008_Id . '-' . str_pad($resposta['data'], 5, "0", STR_PAD_LEFT);
	break;

	case 'cad001CalcularLimite':        
        $retorno = $CAD001->calcularLimite();

        if($retorno){
        	$resposta['code'] = true;
        	if(is_string($retorno)){
        		$resposta['data'] = 'Liminte não calculado, informar o seguinte erro ao suporte:<br><br>'.$retorno;
        	}else{
        		$resposta['data'] = 'O limite foi cálculado para todos os clientes.<br>';
        	}
        }else{
        	$resposta['code'] = false;
        	$resposta['data'] = 'O Cálculo do Limite não está configurado.';
        }
    break;
 }

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>