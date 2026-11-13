<?php
namespace hardness;
set_time_limit(0);

global $g;

mysqli_query("update D024 set D024_Flag_Comercial = 'I', D024_Data_Inativo = CURDATE()");

$sqlC031 = mysql_query("SELECT C031_Campo, C031_Valor FROM C031 WHERE C031_C004_Id = '1' AND (C031_Campo = 'carteiraClienteDiasMaxContato' OR C031_Campo = 'carteiraClienteDiasMaxOrcamento' OR C031_Campo = 'carteiraClienteDiasMaxPedido' OR C031_Campo = 'carteiraClienteDiasMaxNotaFiscal')");
while($rowC031 = mysql_fetch_array($sqlC031)){
    switch ($rowC031['C031_Campo']) {
        case 'carteiraClienteDiasMaxContato':
            $diasAgenda = $rowC031['C031_Valor'];
        break;
        case 'carteiraClienteDiasMaxOrcamento':
            $diasOrcamento = $rowC031['C031_Valor'];
        break;
        case 'carteiraClienteDiasMaxPedido':
            $diasPedido = $rowC031['C031_Valor'];
        break;
        case 'carteiraClienteDiasMaxNotaFiscal':
            $diasNotaFiscal = $rowC031['C031_Valor'];
        break;
    }
}

$sqlD024 = mysql_query("SELECT D024_Id, IFNULL(D024_Data_Cadastro, '0000-00-00') as D024_Data_Cadastro, IFNULL(D024_Data_Ativo, '0000-00-00') as D024_Data_Ativo FROM D024 ORDER BY D024_Id");
while($D024 = mysql_fetch_array($sqlD024)){
    $ativarCliente = true;
    $observacaoInativo = '';
    
    if($D024['D024_Data_Cadastro'] == '0000-00-00'){
        $diasCadastro = 999;
    } else {
        $diasCadastro = round(abs(strtotime(date("Y-m-d"))-strtotime($D024['D024_Data_Cadastro']))/86400);
    }

    if($D024['D024_Data_Ativo'] == '0000-00-00'){
        $diasAtivo = 120;
    } else {
        $diasAtivo = round(abs(strtotime(date("Y-m-d"))-strtotime($D024['D024_Data_Ativo']))/86400);
    }
    
    if(!empty($diasNotaFiscal) && $diasNotaFiscal > 0){
        $sqlNotaFiscal = mysql_query("SELECT DISTINCT T007_Id FROM T007 LEFT JOIN D024 ON D024_Id = T007_D024_Id WHERE T007_D024_Id = '{$D024['D024_Id']}' AND IFNULL(T007_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasNotaFiscal} DAY");
        if(mysql_num_rows($sqlNotaFiscal) <= 0 && $diasCadastro >= $diasNotaFiscal && $diasAtivo >= $diasNotaFiscal){
            $ativarCliente = false;
            $observacaoInativo .= "Cliente não possui NOTA FISCAL no(s) último(s) $diasNotaFiscal dia(s)<br>";
        }
    }

    if(!empty($diasPedido) && $diasPedido > 0){
        $sqlPedido = mysql_query("SELECT DISTINCT T005_Id FROM T005 LEFT JOIN D024 ON D024_Id = T005_D024_Id WHERE T005_D024_Id = '{$D024['D024_Id']}' AND IFNULL(T005_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasPedido} DAY");
        if(mysql_num_rows($sqlPedido) <= 0 && $diasCadastro >= $diasPedido && $diasAtivo >= $diasPedido){
            $ativarCliente = false;
            $observacaoInativo .= "Cliente não possui PEDIDO no(s) último(s) $diasPedido dia(s)<br>";
        }
    }

    if(!empty($diasOrcamento) && $diasOrcamento > 0){
        $sqlOrcamento = mysql_query("SELECT DISTINCT T003_Id FROM T003 LEFT JOIN D024 ON D024_Id = T003_D024_Id WHERE T003_D024_Id = '{$D024['D024_Id']}' AND IFNULL(T003_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasOrcamento} DAY");
        if(mysql_num_rows($sqlOrcamento) <= 0 && $diasCadastro >= $diasOrcamento && $diasAtivo >= $diasOrcamento){
            $ativarCliente = false;
            $observacaoInativo .= "Cliente não possui ORÇAMENTO no(s) último(s) $diasOrcamento dia(s)<br>";
        }
    }

    if(!empty($diasAgenda) && $diasAgenda > 0){
        $sqlAgenda = mysql_query("SELECT DISTINCT D085_Id FROM D085 LEFT JOIN D024 ON D024_Id = D085_D024_Id WHERE D085_D024_Id = '{$D024['D024_Id']}' AND IFNULL(D085_Data, '0000-00-00') > CURDATE() - INTERVAL {$diasAgenda} DAY");
        if(mysql_num_rows($sqlAgenda) <= 0 && $diasCadastro >= $diasAgenda && $diasAtivo >= $diasAgenda){
            $ativarCliente = false;
            $observacaoInativo .= "Cliente não possui AGENDA no(s) último(s) $diasAgenda dia(s)";
        }
    }

    if($ativarCliente){
        mysqli_query("UPDATE D024 SET D024_Flag_Comercial = 'A', D024_Data_Inativo = '0000-00-00' WHERE D024_Flag_Comercial = 'I' AND D024_Id = '{$D024['D024_Id']}'");
    } else {
        mysqli_query("UPDATE D024 SET D024_Flag_Comercial = 'I', D024_Data_Inativo = CURDATE(), D024_Observacao_Inativo = '$observacaoInativo' WHERE D024_Id = '{$D024['D024_Id']}'");
    }
}

echo "Carteira de clientes concluido!\n";

?>
