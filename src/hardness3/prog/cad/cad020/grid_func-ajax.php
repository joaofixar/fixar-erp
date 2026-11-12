<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'atualizarPreco':
		$D001_Id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		
		if($D001_Id>0){
			$C020=mysql_query("select C020_IPV_Atacado,C020_IPV_Revenda,C020_IPV_Varejo from C020 where C020_C004_Id={$g['empresaAtual']}");
			$mC020=mysql_fetch_array($C020);
			
			mysql_query("update D041
              left join D049 on D049_Id=D041_D049_Id
              left join D009 on D009_D049_Id=D049_Id
              left join D082 on D082_Id=D049_D082_Id
                    set D041_Valor_Preco_Revenda   =D009_Valor_Custo_Unitario*$mC020[C020_IPV_Revenda],
                        D041_Valor_Base            =D009_Valor_Custo_Unitario*$mC020[C020_IPV_Atacado],
                        D041_Valor_Preco_Consumidor=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Varejo]
                  where D049_D001_Id='$D001_Id'
                    and D009_C004_Id={$g['empresaAtual']}
                    and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
					
					$resposta['data'] = "Atualizado! ID: " . $D001_Id;
		}else{
			mysql_query("update D041
              left join D049 on D049_Id=D041_D049_Id
              left join D009 on D009_D049_Id=D049_Id
              left join D082 on D082_Id=D049_D082_Id
                    set D041_Valor_Preco_Revenda   =D009_Valor_Custo_Unitario*$mC020[C020_IPV_Revenda],
                        D041_Valor_Base            =D009_Valor_Custo_Unitario*$mC020[C020_IPV_Atacado],
                        D041_Valor_Preco_Consumidor=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Varejo]
                  where D009_C004_Id={$g['empresaAtual']}
                    and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
                    $resposta['data'] = "Atualizado!";
		}
		
		
	break;
	
	case 'excluir':
		$T140_Id = isset($_REQUEST['T140_Id']) ? $_REQUEST['T140_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('T140_Id', $T140_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD002->excluir($T140_Id);
			$resposta['data'] = 'T140_Id-' . $T140_Id;
		}
	break;
	
	case 'abreCertificado':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T140_Nome_Arquivo from T140 WHERE T140_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T140_Nome_Arquivo']);
		$cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/certificados/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['T140_Nome_Arquivo'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
			return false;
		}
		die();
	break;
    
    case 'cad020CheckboxImprimir':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
        
        require_once('bibliotecas/classes/CAD020.php');
        $CAD020 = new CAD020();
        
        $CAD020->updateCheckboxImprimir($D009_Id, $selected);
    break;
    
    case 'cad020DesmarcarImprimir':
        $sql = "update D009 set D009_Flag_Imprimir_Etiqueta = 'N'";
         mysql_query($sql);    
    break;
    
    case 'cad020VerificaProdutosImprimir':
        $sql = "SELECT D009_Id FROM D009 WHERE D009_Flag_Imprimir_Etiqueta='S'";
        $result = mysql_query($sql);
        if (mysql_num_rows($result)<=0) {
            $resposta['code'] = false;
        }        
	break;
	
	case 'atualizarEstoqueRapido': 
    	/** 
		* Funcionamento em etapas
    	*/
		$valorTotal = $r_valorTotal; 
    	$registrosEtapa = floor($valorTotal / $r_etapas);
    	$etapa = $r_etapa;
    	$limit1 = floor(( $registrosEtapa * $etapa ) - $registrosEtapa);
    	if($etapa >= $r_etapas){
    		$limit2 = ceil($registrosEtapa + ($valorTotal - ($limit1 + $registrosEtapa)));
    	} else {
    		$limit2 = $registrosEtapa ;
    	}
    	$concluido = 0;

        $resposta['percentual'] = ceil((100/$r_etapas) * $etapa);
        
    	if($limit2 < $valorTotal){
        	$etapa = $r_etapa + 1;	
        }else{
        	$concluido = 1;
        }
        $resposta['etapa'] = $etapa;
        $resposta['concluido'] = $concluido;
    	// --

		$ok = 0;
    	$erro = 0;
    	$erros ="";

		$sql = base64_decode($r_extraSql);
     	$sql = $sql . " LIMIT 0,100";
        $sql = preg_replace('/LIMIT [0-9]+,[0-9]+/', "LIMIT $limit1 , $limit2", $sql);
        $sql = "SELECT D001_Id, D009_Id FROM D001 ".$sql;
		$mSql = mysql_query($sql);

		$mysql_erro=mysql_error();
        if (!empty($mysql_erro)){
        	$erros .= htmlspecialchars($sql) . ' erro: ' . $mysql_erro;
        } 
		
		set_time_limit(0);
    	while($row = mysql_fetch_array($mSql)){
            $retorno = $CAD002->D001_reprocessa_historico(false,$row['D009_Id'],50);
            $retorno = true;
            if($retorno !== true){
             	$erro++;
             	$erros .= $erro ." - ". $retorno."<br />";
            }else{
             	$ok++;
            }
        }

    	$resposta['data']="Ocorreu um erro inesperado!";
        $resposta['code'] = true;
        $resposta['ok'] = $ok;
        $resposta['erro'] = $erro;
        $resposta['erros'] = $erros;

	break;
    
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";


?>