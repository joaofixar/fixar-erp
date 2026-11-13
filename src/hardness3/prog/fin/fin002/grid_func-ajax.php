<?php
namespace hardness;
/**
 * FIN - FIN001 - Contas a Receber
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/FIN002.php');
$FIN002 = new FIN002();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	// -> campoExclusao($array) -> fin002CampoExclusao(T015_Id)
    case 'excluir':
        $T015_Id = isset($_REQUEST['T015_Id']) ? $_REQUEST['T015_Id'] : false;
        if (empty($T015_Id)) {
            $resposta['code'] = false;
            $resposta['data'] = 'Registro não existe.';
        } else {
            $retorno = $FIN002->excluir($T015_Id);
            if ($retorno === true) {
                $resposta['data'] = 'T015_Id-' . $T015_Id;
            } else {
                $resposta['code'] = false;
                $resposta['data'] = $retorno;
            }
        }
    break;

	// -> campoExclusao($array) -> fin002CampoExclusaoAd(T033_Id)
	case 'excluirAd':
		$T033_Id = isset($_REQUEST['T033_Id']) ? $_REQUEST['T033_Id'] : false;
		$T033_T015_Id = isset($_REQUEST['T033_T015_Id']) ? $_REQUEST['T033_T015_Id'] : false;
		if (empty($T033_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro não existe.';
		} else {
			$retorno = $FIN002->excluirAcrescimoDesconto($T033_Id, $T033_T015_Id);
			if ($retorno === true) {
				$resposta['data'] = 'T033_Id-' . $T033_Id;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
		break;

	case 'gravarCampo':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T015_Id = isset($_REQUEST['T015_Id']) ? $_REQUEST['T015_Id'] : false;
		$FIN002->updateT015_Flag_Baixar_Lote($T015_Id, $selected);

		break;

	case 'carregaInputBaixar':
		$retorno = $FIN002->listaTotalBaixar(base64_decode($r_extra));
		$resposta['data']=number_format($retorno['Total_Baixar'],2,',','.');
		break;

	case 'verificaBaixar':
		$itensBaixar = $FIN002->listaItensParaBaixar();
		if (count($itensBaixar) == 0) {
			$resposta['code'] = '1';
			$resposta['data'] = 'Não existem títulos para baixar...';
		} else {
			$resposta['code'] = '0';
		}
		break;

	case 'atualizaTotaisBaixar':
		$total = $FIN002->carregaTotaisBaixar(base64_decode($r_extra));
		$resposta['data'] = array('fin002BaixarValor'=>number_format($total['Total'],2,',','.'),'fin002BaixarValorPago'=>number_format($total['TotalRecebido'],2,',','.'));
		break;

	case 'baixarTitulosEditarCampo':

		$acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		$Campo_Editado = isset($_REQUEST['Campo_Editado']) ? $_REQUEST['Campo_Editado'] : false;
		$Conteudo_Campo_Editado = isset($_REQUEST['Conteudo_Campo_Editado']) ? $_REQUEST['Conteudo_Campo_Editado'] : false;

		if ($Campo_Editado=='T103_Flag_Baixar_Lote')
		{
			mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado=='T015_Flag_Baixar_Lote')
		{
			mysql_query("update T015 set $Campo_Editado='$Conteudo_Campo_Editado' where T015_Id='$acaoId'");
		}
		else if ($Campo_Editado=='T103_Valor_Recebido')
		{
			//$Conteudo_Campo_Editado=formata_valor_gravar($Conteudo_Campo_Editado,3);
			$Conteudo_Campo_Editado=str_replace('.', '', $Conteudo_Campo_Editado);
			$Conteudo_Campo_Editado=str_replace(',', '.', $Conteudo_Campo_Editado);
			mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
		else if ($Campo_Editado=='T103_D031_Id')
		{
			mysql_query("update T103 set $Campo_Editado='$Conteudo_Campo_Editado' where T103_Id='$acaoId'");
		}
        else if ($Campo_Editado == 'T103_Valor_Desconto')
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
    
    case 'checkboxRp':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $T015_Id = isset($_REQUEST['T015_Id']) ? $_REQUEST['T015_Id'] : false;    
        $FIN002->updateT015_Flag_Reparcelar($T015_Id, $selected);
    break;
    
    case 'carregaInputReparcelar':        
        $retorno = $FIN002->listaTotalReparcelar(base64_decode($r_extra));
        $resposta['data']=number_format($retorno['Total_Reparcelar'],2,',','.');          
          
    break;    

    case 'carregaInputAgrupar':
        $retorno = $FIN002->listaTotalAgrupar(base64_decode($r_extra));
        $resposta['data']=number_format($retorno['Total_Agrupar'],2,',','.');
              
    break;
    
    case 'verificaAgrupar':            
        $itensAgrupar = $FIN002->listaItensParaAgrupar(false,$r_extra);
        if (count($itensAgrupar) == 0) {
            $resposta['code'] = '1';
            $resposta['data'] = 'Não foram selecionados títulos para agrupar...';
        } else {
            $itensAgrupar = $FIN002->listaItensParaAgrupar(true,$r_extra);
            if (count($itensAgrupar) > 1) {
                $resposta['code'] = '2';
                $resposta['data'] = 'Mais de um cliente foi selecionado...';
            } else {
                $resposta['code'] = '0';
            }    
        }
    break;    
    
      
    
    case 'verificaReparcelar':
        $itensReparcelar = $FIN002->listaItensParaReparcelar(false,$r_extra);
        if (count($itensReparcelar) == 0) {
            $resposta['code'] = '1';
            $resposta['data'] = 'Não foram selecionados títulos para reparcelar...';
        } else {
            $itensReparcelar = $FIN002->listaItensParaReparcelar(true,$r_extra);
            if (count($itensReparcelar) > 1) {
                $resposta['code'] = '2';
                $resposta['data'] = 'Mais de um cliente foi selecionado...';
            } else {
                $resposta['code'] = '0';
            }
        }
    break;
    
    case 'fin002atualizarParcelas':
        require_once("bibliotecas/classes/FIN002.php");
        
        $T197_Id         = isset($r_T197_Id)         ? $r_T197_Id         : false;
        $campoEditado     = isset($r_campoEditado)     ? $r_campoEditado     : false;
        $valorAntigo     = isset($r_valorAntigo)     ? $r_valorAntigo     : false;
        $novoValor         = isset($r_novoValor)         ? $r_novoValor         : false;
        $valorTotal     = isset($r_valorTotal)         ? $r_valorTotal     : false;
        
        
        $FIN002 = new FIN002();
        
        $retorno = $FIN002->fin002AtualizarParcelas2($T197_Id, $campoEditado, $valorAntigo, $novoValor, $valorTotal);
        
        if ($retorno === true) {
            $resposta['code'] = true;
        } else {
            // excluir
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }
    break;
    
    case 'fin002LimparGrid':
            //require_once("bibliotecas/classes/FIN002.php");
            
            $D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
            
            //$FIN002 = new FIN002();
            
            $sql = "DELETE FROM T197 WHERE T197_D024_Id='{$D024_Id}' AND T197_C007_Id = '{$g['usuarioAtual']}'";
            $retorno = mysql_query($sql);
            if ($retorno === true) {
                $resposta['code'] = true;
            } else {
                // excluir
                $resposta['code'] = false;
                $resposta['data'] = $retorno;
            }
    break;
    
    case 'fin002Reparcelar':
        require_once("bibliotecas/classes/FIN002.php");
        $extra = base64_decode($r_extra);
        
        $D024_Id         = isset($r_D024_Id)     ? $r_D024_Id : false;
        $C007_Id         = isset($r_C007_Id)     ? $r_C007_Id : false;
        $valorSomado     = isset($r_valorSomado) ? $r_valorSomado : false;
        
        $FIN002 = new FIN002();
        
        $retorno = $FIN002->fin002reparcelar($D024_Id, $C007_Id, $valorSomado, $extra);
        
        if ($retorno === true) {
            $resposta['code'] = true;
        } else {
            // excluir
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }
    break;

    case 'fin002MarcarVisto':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $T015_Id = isset($_REQUEST['T015_Id']) ? $_REQUEST['T015_Id'] : false;    
        mysqli_query("UPDATE T015 SET T015_Flag_Boleto = '{$selected}' WHERE T015_Id = '{$T015_Id}'");
    break;

    case 'abreDocumento':
        $id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;     
        $sql = 'select T243_Nome_Arquivo from T243 WHERE T243_Id ='.$id;
        $result = mysql_query($sql);
        $row = mysql_fetch_assoc($result);
        $pathinfo = pathinfo($row['T243_Nome_Arquivo']);
        $cArquivo = "{$g['pathDados']}documentos/contas_pagar/{$id}.{$pathinfo['extension']}";
        if (file_exists($cArquivo))
        {
            $finfo = new \finfo();
            header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
            header('Content-Disposition: attachment; filename="' . $row['T243_Nome_Arquivo'] . '"');
            readfile($cArquivo);
        }
        else
        {
            echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
            return false;
        }
        die();
    break;

    case 'verificaDividir':
        $itensDividir = $FIN002->listaItensParaDividir(false,$r_extra);
        if (count($itensDividir) == 0) {
            $resposta['code'] = '1';
            $resposta['data'] = 'Não foram selecionados títulos para dividir...';
        } else {
            $itensDividir = $FIN002->listaItensParaDividir(true,$r_extra);
            if (count($itensDividir) > 1) {
                $resposta['code'] = '2';
                $resposta['data'] = 'Mais de um cliente foi selecionado...';
            } else {
                $resposta['code'] = '0';
            }
        }
    break;

    case 'fin002atualizarParcelasDivisao':
        $T247_Id      = isset($r_T247_Id)      ? $r_T247_Id      : false;
        $campoEditado = isset($r_campoEditado) ? $r_campoEditado : false;
        $valorAntigo  = isset($r_valorAntigo)  ? $r_valorAntigo  : false;
        $novoValor    = isset($r_novoValor)    ? $r_novoValor    : false;
        $valorTotal   = isset($r_valorTotal)   ? $r_valorTotal   : false;        
        
        require_once("bibliotecas/classes/FIN002.php");
        $FIN002 = new FIN002();
        $retorno = $FIN002->atualizarParcelasDivisao($T247_Id, $campoEditado, $valorAntigo, $novoValor, $valorTotal);
        if ($retorno === true) {
            $resposta['code'] = true;
        } else {
            // excluir
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }
    break;

    case 'excluirEmMassa':
        if (!isset($g['c029Ids'][111])) {
            $resposta['code'] = false;
            $resposta['data'] = 'Seu usuário não possui permissão para excluir títulos.';
            break;
        }

        $extraOriginal = base64_decode(isset($r_extra) ? $r_extra : '', true);
        if ($extraOriginal === false || empty($extraOriginal)) {
            $resposta['code'] = false;
            $resposta['data'] = 'O filtro atual do grid não foi informado.';
            break;
        }

        $whereExclusao = "T015_Flag_Baixar_Lote = 'S'
            AND T015_Flag_Cancelada != 'S'
            AND T015_Data_Pagamento = '0000-00-00'";
        $where = gPegarParteWhere(gInsertExtraWhere($extraOriginal, $whereExclusao));
        $sql = "SELECT DISTINCT
                    T015.T015_Id,
                    EXISTS(SELECT 1 FROM T019 WHERE T019_T015_Id = T015.T015_Id) AS possui_lancamento_bancario,
                    EXISTS(SELECT 1 FROM T013 WHERE T013_Id = T015.T015_T013_Id) AS possui_nota_recebida,
                    EXISTS(SELECT 1 FROM T007 WHERE T007_Id = T015.T015_T007_Id) AS possui_nota_emitida
                FROM T015
                    LEFT JOIN D024 ON D024_Id = T015_D024_Id
                    LEFT JOIN D073 ON D073_Id = T015_D073_Id
                    LEFT JOIN D031 ON D031_Id = T015_D031_Id
                    LEFT JOIN D014 ON D014_Id = T015_D014_Id
                    LEFT JOIN D032 ON D032_Id = D014_D032_Id
                    LEFT JOIN D154 ON D154_Id = D014_D154_Id
                    LEFT JOIN T007 ON T007_Id = T015_T007_Id
                    LEFT JOIN C004 ON C004_Id = T015_C004_Id
                    LEFT JOIN C007 ON C007_Id = T007_C007_Id_Vendedor_Interno
                WHERE {$where}
                ORDER BY T015.T015_Id
                LIMIT 501";
        $consulta = mysql_query($sql);
        if (!$consulta) {
            $resposta['code'] = false;
            $resposta['data'] = 'Não foi possível consultar os títulos marcados em Bx dentro do filtro atual.';
            break;
        }

        $ids = array();
        $bloqueios = array();
        while ($titulo = mysql_fetch_assoc($consulta)) {
            $tituloId = (int) $titulo['T015_Id'];
            $ids[] = $tituloId;
            if ($titulo['possui_lancamento_bancario']) {
                $bloqueios[] = "Título {$tituloId}: possui lançamento bancário";
            } else if ($titulo['possui_nota_recebida']) {
                $bloqueios[] = "Título {$tituloId}: foi gerado por nota fiscal recebida";
            } else if ($titulo['possui_nota_emitida']) {
                $bloqueios[] = "Título {$tituloId}: foi gerado por nota fiscal emitida";
            }
        }

        if (count($ids) === 0) {
            $resposta['code'] = false;
            $resposta['data'] = 'Não existem títulos marcados em Bx dentro do filtro atual para excluir.';
            break;
        }
        if (count($ids) > 500) {
            $resposta['code'] = false;
            $resposta['data'] = 'Foram encontrados mais de 500 títulos marcados em Bx. Reduza a seleção e tente novamente.';
            break;
        }
        if (count($bloqueios) > 0) {
            $resposta['code'] = false;
            $resposta['data'] = 'Nenhum título foi excluído. ' . implode('; ', $bloqueios) . '.';
            break;
        }

        mysql_query('START TRANSACTION');
        $erro = false;
        foreach ($ids as $tituloId) {
            $retorno = $FIN002->excluir($tituloId);
            if ($retorno !== true) {
                $erro = $retorno;
                break;
            }
            if (!mysql_query("UPDATE T015 SET T015_Flag_Baixar_Lote = 'N' WHERE T015_Id = '{$tituloId}'")) {
                $erro = 'Não foi possível desmarcar o título em Bx.';
                break;
            }
        }

        if ($erro) {
            mysql_query('ROLLBACK');
            $resposta['code'] = false;
            $resposta['data'] = 'Nenhum título foi excluído. ' . $erro;
        } else {
            mysql_query('COMMIT');
            $resposta['code'] = true;
            $resposta['data'] = 'Foram excluídos ' . count($ids) . ' título(s).';
        }
    break;

    case 'fin002Dividir':
        $extra        = base64_decode($r_extra);    
        $C007_Id      = isset($r_C007_Id)     ? $r_C007_Id : false;
        $valorSomado  = isset($r_valorSomado) ? $r_valorSomado : false;
        
        require_once("bibliotecas/classes/FIN002.php");
        $FIN002 = new FIN002();        
        $retorno = $FIN002->fin002Dividir($C007_Id, $valorSomado, $extra);
        
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
