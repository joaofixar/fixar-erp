<?php

$conf['restricaoIncluirUpdate']['T234_Descricao'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T234_D024_Id'] = <<<'EOT'
    $retorno = rVerificaFK('D024_Id', $valor);
EOT;

// $conf['restricaoIncluirUpdate']['T234_T272_Id'] = <<<'EOT'
//     if($valores['T234_Flag_Tipo_OS'] == 'M'){
//         $retorno = rVerificaFK('T272_Id', $valor);
//     }
// EOT;

$conf['restricaoIncluirUpdate']['T234_Revisao_SVN'] = <<<'EOT'
    $retorno = true;
    if($valores['T234_Flag_Finalizado'] != 'N' && $valores['T234_Flag_Personalizado'] != 'S'){
        $retorno = rRegx('numero', $valor);
    }
EOT;

/**
* trigger
*/
$conf['triggers']['T234']['insertAfter'] = <<<'EOT'
    mysqli_query("INSERT INTO T254 (T254_T234_Id, T254_C007_Id, T254_Data_Hora, T254_Status) VALUES ('{$chaveValor}', '999', CURRENT_TIMESTAMP, 'Inclusão do chamado')");
EOT;

$conf['triggers']['T234']['updateBefore'] = <<<'EOT'
    global $registroT234_beforeUpdate;
    $sql = mysql_query("SELECT * FROM T234 WHERE T234_Id = '{$chaveValor}'");
    $registroT234_beforeUpdate = mysql_fetch_assoc($sql);
EOT;
$conf['triggers']['T234']['updateAfter'] = <<<'EOT'
    global $registroT234_beforeUpdate;
    $sqlT234 = mysql_query("SELECT * FROM T234 WHERE T234_Id = '{$chaveValor}'");
    $rowT234 = mysql_fetch_assoc($sqlT234);

    if($registroT234_beforeUpdate['T234_C007_Id'] != $rowT234['T234_C007_Id']){
        if($rowT234['T234_C007_Id'] > 0){
            $rowC007 = mysql_fetch_array(mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id = '{$rowT234['T234_C007_Id']}'"));
            $mensagemC007 = "Alterou o responsável pelo chamado para {$rowC007['C007_Primeiro_Nome']}";
        } else {
            $mensagemC007 = "Removeu o responsável pelo chamado";
        }
        mysqli_query("INSERT INTO T254 (T254_T234_Id, T254_C007_Id, T254_Data_Hora, T254_Status) VALUES ('{$chaveValor}', '{$g['usuarioAtual']}', CURRENT_TIMESTAMP, '{$mensagemC007}')");
    }

    if($registroT234_beforeUpdate['T234_Flag_Triagem'] != $rowT234['T234_Flag_Triagem']){
        $C007_Id = $g['usuarioAtual'];
        $C007_Primeiro_Nome = $g['C007']['C007_Primeiro_Nome'];
        if($rowT234['T234_Flag_Status_Desenvolvimento'] == '0' && $rowT234['T234_Ordem_Desenvolvimento'] == '0' && $rowT234['T234_Flag_Triagem'] == 'S'){
            $C007_Id = '999';
            $C007_Primeiro_Nome = 'SISTEMA';
        }
        if($rowT234['T234_Flag_Triagem'] == 'S'){
            $mensagem = "Enviou para triagem";
        } else {
            $mensagem = "Removeu da triagem";
        }
    } else if($registroT234_beforeUpdate['T234_Flag_Finalizado'] != $rowT234['T234_Flag_Finalizado']){
        $C007_Id = $g['usuarioAtual'];
        if($rowT234['T234_Flag_Finalizado'] == 'N'){
            $mensagem = "Mudou o chamado para PENDENTE";
        } else if($rowT234['T234_Flag_Finalizado'] == 'S'){
            $mensagem = "Mudou o chamado para FINALIZADO";
        } else {
            $mensagem = "Mudou o chamado para CONCLUÍDO";
        }
    } else if($registroT234_beforeUpdate['T234_Flag_Status_Desenvolvimento'] != $rowT234['T234_Flag_Status_Desenvolvimento']){
        $C007_Id = $g['usuarioAtual'];
        require_once('bibliotecas/funcoesCallback/statusDesenvolvimento.php');
        $status = statusDesenvolvimento(false, $rowT234['T234_Flag_Status_Desenvolvimento']);
        if($rowT234['T234_Flag_Status_Desenvolvimento'] > $registroT234_beforeUpdate['T234_Flag_Status_Desenvolvimento']){
            $mensagem = "Enviou para {$status}";
        } else {
            $mensagem = "Retornou para {$status}";
        }
    }

    if($C007_Id > 0 && !empty($mensagem)){
        mysqli_query("INSERT INTO T254 (T254_T234_Id, T254_C007_Id, T254_Data_Hora, T254_Status) VALUES ('{$chaveValor}', '{$C007_Id}', CURRENT_TIMESTAMP, '{$mensagem}')");
    }
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T234'] = <<<'EOT'
    $retorno = true;

    if($operacao == 'alterar' && $campo == 'T234_Prioridade_Cliente'){
    	$sqlT234 = mysql_query("SELECT T234_Flag_Finalizado FROM T234 WHERE T234_Id = '{$valorPk}'");
        $rowT234 = mysql_fetch_array($sqlT234);
    	if($rowT234['T234_Flag_Finalizado'] == 'O'){
    		$retorno = "Não é possível alterar a prioridade de chamados já concluídos!";
    	}
    }
EOT;
