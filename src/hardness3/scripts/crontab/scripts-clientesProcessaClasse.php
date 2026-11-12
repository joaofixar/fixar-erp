<?php

set_time_limit(0);

//Maior atraso dias, Meses cadastrado, Intervalo vendas dias

global $g;
mysql_query("UPDATE D024 SET D024_Flag_Classe = 'D'");

$dataInicio = date("Y-m-d", strtotime("-{$g['C031']['mesesProcessarClasseCliente']} months"));
$dataVenda   = strtotime(date("Y-m-d"));
$ultimaVenda = strtotime($dataInicio);
$diff = $dataVenda-$ultimaVenda;
$totalDias = floor($diff/(60*60*24));

$classes = array('A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'X' => 0);
//Maior atraso e percentual de atrasos do cliente
$diasAtraso = array();
$percentualAtrasos = array();
$sqlT002 = mysql_query("SELECT T002_D024_Id, MAX(MaiorAtraso) AS MaiorAtraso, ((SUM(Atrasos)/COUNT(DISTINCT T002_Id))*100) AS PercentualAtrasos FROM (SELECT T002_D024_Id, DATEDIFF(IF(T002_Data_Recebimento='0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) AS MaiorAtraso, IF(DATEDIFF(IF(T002_Data_Recebimento = '0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) > 0, 1, 0) AS Atrasos, T002_Id FROM T002 WHERE T002_Data_Emissao >= '{$dataInicio}' AND T002_Flag_Cancelada = 'N' ORDER BY DATEDIFF(IF(T002_Data_Recebimento='0000-00-00',CURDATE(),T002_Data_Recebimento),T002_Data_Vencimento) DESC) AS T1 GROUP BY T002_D024_Id");
while($rowT002 = mysql_fetch_array($sqlT002)){
    $rowT002['MaiorAtraso'] = ($rowT002['MaiorAtraso'] < 0) ? '0' : $rowT002['MaiorAtraso'];
    $diasAtraso[$rowT002['T002_D024_Id']] = $rowT002['MaiorAtraso'];
    $percentualAtrasos[$rowT002['T002_D024_Id']] = $rowT002['PercentualAtrasos'];
}

//Ticket médio do cliente
$vendasCliente = array();
$sqlT007 = mysql_query("SELECT T007_D024_Id, (SUM(T007_Valor_Total_Produtos)/COUNT(T007_Id)) AS TicketMedio, (SUM(T007_Valor_Total_Produtos)/{$g['C031']['mesesProcessarClasseCliente']}) AS MediaMes FROM T007 LEFT JOIN D006 ON D006_Id=T007_D006_Id WHERE T007_Flag_Cancelada!='S' AND D006_Flag_Venda_Compra_Outros='V' AND T007_Data_Emissao >= '{$dataInicio}' GROUP BY T007_D024_Id");
while($rowT007 = mysql_fetch_array($sqlT007)){
    $vendasCliente[$rowT007['T007_D024_Id']] = array('ticketMedio' => $rowT007['TicketMedio'], 'mediaMes' => $rowT007['MediaMes']);
}

//Loop clientes
$sqlD024 = mysql_query("SELECT D024_Id, PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(IF(IFNULL(D024_Data_Cadastro,'0000-00-00')='0000-00-00', CURDATE(), D024_Data_Cadastro), '%Y%m')) AS mesesCadastro FROM D024");
while($rowD024 = mysql_fetch_array($sqlD024)){
    $prazos = array();
    unset($classe);
    $ultimaVenda = $dataInicio;
    $sqlT007 = mysql_query("SELECT T007_Data_Emissao FROM T007 LEFT JOIN D006 ON D006_Id=T007_D006_Id WHERE T007_D024_Id = '{$rowD024['D024_Id']}' AND T007_Flag_Cancelada!='S' AND D006_Flag_Venda_Compra_Outros='V' AND T007_Data_Emissao >= '{$dataInicio}' ORDER BY T007_Data_Emissao");
    if(mysql_num_rows($sqlT007) > 0){
        while($rowT007 = mysql_fetch_array($sqlT007)){
            $dataVenda   = strtotime($rowT007['T007_Data_Emissao']);
            $ultimaVenda = strtotime($ultimaVenda);
            $diff = $dataVenda-$ultimaVenda;
            $prazos[] = floor($diff/(60*60*24));
            $ultimaVenda = $rowT007['T007_Data_Emissao'];
        }
        //Adiciona o prazo da última venda à data atual
        $dataVenda   = strtotime(date("Y-m-d"));
        $ultimaVenda = strtotime($ultimaVenda);
        $diff = $dataVenda-$ultimaVenda;
        $prazos[] = floor($diff/(60*60*24));

        rsort($prazos);
        $maiorPrazo = $prazos[0];
    } else {
        $maiorPrazo = $totalDias;
    }
    $maiorAtraso        = $diasAtraso[$rowD024['D024_Id']];
    $percentualAtraso   = $percentualAtrasos[$rowD024['D024_Id']];
    $ticketMedioCliente = $vendasCliente[$rowD024['D024_Id']]['ticketMedio'];
    $mediaMesCliente    = $vendasCliente[$rowD024['D024_Id']]['mediaMes'];
    $mesesCadastro      = $rowD024['mesesCadastro'];

    if($rowD024['D024_Id'] == '3384'){
        //echo "maiorAtraso: {$maiorAtraso} ticketMedioCliente: {$ticketMedioCliente} mediaMesCliente: {$mediaMesCliente} mesesCadastro: {$mesesCadastro} maiorPrazo: {$maiorPrazo} percentualAtraso: {$percentualAtraso}\n";
    }

    if($mesesCadastro >= $g['C031']['mesesCadastroClienteClasseA'] && $g['C031']['mesesCadastroClienteClasseA'] > 0){
        $classe = 'A';
    } else if($mesesCadastro >= $g['C031']['mesesCadastroClienteClasseB'] && $g['C031']['mesesCadastroClienteClasseB'] > 0){
        $classe = 'B';
    } else if($mesesCadastro >= $g['C031']['mesesCadastroClienteClasseC'] && $g['C031']['mesesCadastroClienteClasseC'] > 0){
        $classe = 'C';
    } else if($mesesCadastro >= $g['C031']['mesesCadastroClienteClasseD']){
        $classe = 'D';
    } else {
        $classe = 'X';
    }
    if($rowD024['D024_Id'] == '3384'){
        //echo "mesesCadastro " . $classe . "\n";
    }

    if($maiorAtraso <= $g['C031']['diasAtrasoPgtoClienteClasseA'] && $g['C031']['diasAtrasoPgtoClienteClasseA'] > 0 && $classe == 'A'){
        $classe = 'A';
    } else if($maiorAtraso <= $g['C031']['diasAtrasoPgtoClienteClasseB'] && $g['C031']['diasAtrasoPgtoClienteClasseB'] > 0 && ($classe == 'A' || $classe == 'B')){
        $classe = 'B';
    } else if($maiorAtraso <= $g['C031']['diasAtrasoPgtoClienteClasseC'] && $g['C031']['diasAtrasoPgtoClienteClasseC'] > 0 && ($classe == 'A' || $classe == 'B' || $classe == 'C')){
        $classe = 'C';
    } else if($maiorAtraso <= $g['C031']['diasAtrasoPgtoClienteClasseD'] && $classe != 'X'){
        $classe = 'D';
    } else {
        $classe = 'X';
    }
    if($rowD024['D024_Id'] == '3384'){
        //echo "maiorAtraso " . $classe . "\n";
    }

    if($maiorPrazo <= $g['C031']['prazoVendaClienteClasseA'] && $g['C031']['prazoVendaClienteClasseA'] > 0 && $classe == 'A'){
        $classe = 'A';
    } else if($maiorPrazo <= $g['C031']['prazoVendaClienteClasseB'] && $g['C031']['prazoVendaClienteClasseB'] > 0 && ($classe == 'A' || $classe == 'B')){
        $classe = 'B';
    } else if($maiorPrazo <= $g['C031']['prazoVendaClienteClasseC'] && $g['C031']['prazoVendaClienteClasseC'] > 0 && ($classe == 'A' || $classe == 'B' || $classe == 'C')){
        $classe = 'C';
    } else if($maiorPrazo <= $g['C031']['prazoVendaClienteClasseD'] && $classe != 'X'){
        $classe = 'D';
    } else {
        $classe = 'X';
    }
    if($rowD024['D024_Id'] == '3384'){
        //echo "maiorPrazo " . $classe . "\n";
    }

    if($mediaMesCliente >= $g['C031']['mediaVendaMesClienteClasseA'] && $classe == 'A'){
        $classe = 'A';
    } else if($mediaMesCliente >= $g['C031']['mediaVendaMesClienteClasseB'] && ($classe == 'A' || $classe == 'B')){
        $classe = 'B';
    } else if($mediaMesCliente >= $g['C031']['mediaVendaMesClienteClasseC'] && ($classe == 'A' || $classe == 'B' || $classe == 'C')){
        $classe = 'C';
    } else if(($mediaMesCliente >= $g['C031']['mediaVendaMesClienteClasseD'] || $mediaMesCliente <= 0) && $classe != 'X'){
        $classe = 'D';
    } else {
        $classe = 'X';
    }
    if($rowD024['D024_Id'] == '3384'){
        //echo "mediaVendaMesClienteClasse " . $classe . "\n";
    }

    if($ticketMedioCliente >= $g['C031']['ticketMedioClienteClasseA'] && $classe == 'A'){
        $classe = 'A';
    } else if($ticketMedioCliente >= $g['C031']['ticketMedioClienteClasseB'] && ($classe == 'A' || $classe == 'B')){
        $classe = 'B';
    } else if($ticketMedioCliente >= $g['C031']['ticketMedioClienteClasseC'] && ($classe == 'A' || $classe == 'B' || $classe == 'C')){
        $classe = 'C';
    } else if(($ticketMedioCliente >= $g['C031']['ticketMedioClienteClasseD'] || $ticketMedioCliente <= 0) && $classe != 'X'){
        $classe = 'D';
    } else {
        $classe = 'X';
    }
    if($rowD024['D024_Id'] == '3384'){
        //echo "ticketMedioCliente " . $classe . "\n";
    }

    if($percentualAtraso <= $g['C031']['percentualAtrasoClienteClasseA'] && $g['C031']['percentualAtrasoClienteClasseA'] > 0 && $classe == 'A'){
        $classe = 'A';
    } else if($percentualAtraso <= $g['C031']['percentualAtrasoClienteClasseB'] && $g['C031']['percentualAtrasoClienteClasseB'] > 0 && ($classe == 'A' || $classe == 'B')){
        $classe = 'B';
    } else if($percentualAtraso <= $g['C031']['percentualAtrasoClienteClasseC'] && $g['C031']['percentualAtrasoClienteClasseC'] > 0 && ($classe == 'A' || $classe == 'B' || $classe == 'C')){
        $classe = 'C';
    } else if($percentualAtraso <= $g['C031']['percentualAtrasoClienteClasseD'] && $classe != 'X'){
        $classe = 'D';
    } else {
        $classe = 'X';
    }
    if($rowD024['D024_Id'] == '3384'){
        //echo "percentualAtraso " . $classe . "\n";
    }

    $classes[$classe]++;
    mysql_query("UPDATE D024 SET D024_Flag_Classe = '{$classe}' WHERE D024_Id = '{$rowD024['D024_Id']}'");
}

echo "Classes dos clientes atualizada!\n";
echo "Classe A: {$classes['A']}\n";
echo "Classe B: {$classes['B']}\n";
echo "Classe C: {$classes['C']}\n";
echo "Classe D: {$classes['D']}\n";
echo "Classe X: {$classes['X']}\n";
?>
