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
			$retorno = $FIN001->excluir($T002_Id);
			if ($retorno === true) {
				$resposta['data'] = 'T002_Id-' . $T002_Id;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
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
		$itensBaixar = $FIN001->listaItensParaBaixar(false,$r_extra);
		if (count($itensBaixar) == 0) {
			$resposta['code'] = '1';
			$resposta['data'] = 'Não foram selecionados títulos para baixar...';
		} else {
			$resposta['code'] = '0';
		}
	break;		
	
	case 'verificaAgrupar':			
		$itensAgrupar = $FIN001->listaItensParaAgrupar(false,$r_extra);
		if (count($itensAgrupar) == 0) {
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
		$itensRemessa = $FIN001->listaItensParaRemessa();
		if (count($itensRemessa) == 0) {
			$resposta['code'] = '1';
			$resposta['data'] = 'Não existem títulos para gerar remessa...';
		} else {
			$resposta['code'] = '0';
		}
	break;		
	
	case 'verificaReparcelar':
		$itensReparcelar = $FIN001->listaItensParaReparcelar(false,$r_extra);
		if (count($itensReparcelar) == 0) {
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
	
	case 'checkboxRm':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T002_Id = isset($_REQUEST['T002_Id']) ? $_REQUEST['T002_Id'] : false;
		$FIN001->updateT002_Flag_Enviar_Banco($T002_Id, $selected);
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

	case 'carregaInputAgrupar':
		$retorno = $FIN001->listaTotalAgrupar(base64_decode($r_extra));
    	$resposta['data']=number_format($retorno['Total_Agrupar'],2,',','.');
              
	break;	

	case 'carregaInputRemessa':
		$retorno = $FIN001->listaTotalRemessa(base64_decode($r_extra));
    	$resposta['data']=number_format($retorno['Total_Remessa'],2,',','.');          
              
	break;		
	
	case 'baixarTitulosEditarCampo':
		
		$acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$Campo_Editado = isset($_REQUEST['Campo_Editado']) ? $_REQUEST['Campo_Editado'] : false;
		$Conteudo_Campo_Editado = isset($_REQUEST['Conteudo_Campo_Editado']) ? $_REQUEST['Conteudo_Campo_Editado'] : false;

		if ($Campo_Editado=='T103_Flag_Baixar_Lote')
		{
		   mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado=='T002_Flag_Baixar_Lote')
		{		   
		   mysql_query("update T002 set $Campo_Editado='$Conteudo_Campo_Editado' where T002_Id='$acaoId'");
		}
		else if ($Campo_Editado=='T103_Valor_Recebido')
		{
		   //$Conteudo_Campo_Editado=formata_valor_gravar($Conteudo_Campo_Editado,3);
			$Conteudo_Campo_Editado=str_replace('.', '', $Conteudo_Campo_Editado);
			$Conteudo_Campo_Editado=str_replace(',', '.', $Conteudo_Campo_Editado);		   	
			mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado=='T103_D027_Id')
		{
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
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>