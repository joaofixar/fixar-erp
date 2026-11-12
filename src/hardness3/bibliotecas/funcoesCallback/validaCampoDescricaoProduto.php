<?php
namespace hardness;

function validaCampoDescricaoProduto($array) {
    //Verificação Se Item já foi cotado
    global $g;

    $sqlT219 = <<<EOT
SELECT T219_Id T174_Id,
    D024_Nome_Empresa,
    T219_Valor_Custo_Unitario T174_Valor_Custo_Unitario,
    T219_IPV T174_IPV,
    '' T174_Flag_Indisponivel,
    '' T174_Prazo_Entrega_Dias,
    T219_Valor_Preco_Venda T174_Preco_Venda,
    '' T174_Observacao,
    T189_Descricao_Produto,
    T219_Descricao_Produto T059_Descricao_Produto,
    T224_Id T011_Id,
    '' T011_Prazo_Entrega,
    T219_Id T059_Id,
    '' T174_Flag_Gerar_OC,
    'S' T010_Flag_Cotacao_Finalizada,
    '' T189_Flag_Tipo
FROM T219
LEFT JOIN T218 ON T218_Id=T219_T218_Id
LEFT JOIN D024 ON D024_Id=T218_D024_Id 
LEFT JOIN T189 ON T189_Id=T219_T189_Id
LEFT JOIN T225 ON T225_T219_Id=T219_Id or T225_T189_Id=T189_Id 
LEFT JOIN T224 ON T224_Id=T225_T224_Id
WHERE
    T219_T189_Id='{$array['todosCampos']['T189_Id']}'
EOT;

    $sqlT059 = <<<EOT
SELECT
    T174_Id,
    D024_Nome_Empresa,
    T174_Valor_Custo_Unitario,
    T174_IPV,
    T174_Flag_Indisponivel,
    T174_Prazo_Entrega_Dias,
    T174_Preco_Venda,
    T174_Observacao,
    T189_Descricao_Produto,
    T059_Descricao_Produto,
    T011_Id,
    T011_Prazo_Entrega,
    T059_Id
FROM T174
LEFT JOIN T173 ON T173_Id=T174_T173_Id
LEFT JOIN D024 ON D024_Id=T173_D024_Id 
LEFT JOIN T010 ON T010_Id=T173_T010_Id 
LEFT JOIN T059 ON T059_Id=T174_T059_Id
LEFT JOIN T189 ON T189_Id=T059_T189_Id
LEFT JOIN T012 ON T012_T174_Id=T174_Id or T012_T189_Id=T189_Id 
LEFT JOIN T011 ON T011_Id=T012_T011_Id
WHERE
    T059_T189_Id='{$array['todosCampos']['T189_Id']}'
    AND T174_Flag_Excluido='N'
EOT;

    $resT059 = mysql_query($sqlT219);
    if (mysql_num_rows($resT059)<=0){
        $resT059 = mysql_query($sqlT059);
    }
    echo mysql_error();
    $rowsT059 = mysql_num_rows($resT059);

    //Verificação Se Item já foi para ordem de compra
    $sqlT012 = <<<EOT
        SELECT T012_Id FROM T012 WHERE T012_T189_Id='{$array['todosCampos']['T189_Id']}'
EOT;
    $resT012 = mysql_query($sqlT012);
    $rowsT012 = mysql_numrows($resT012);
    $retorno='';
    if($rowsT059 > 0){
        while ($mT059=mysql_fetch_array($resT059))
        {
            $mT059['T059_Descricao_Produto'] = str_replace(trim($mT059['T189_Descricao_Produto']), '', trim($mT059['T059_Descricao_Produto']));
            $mT059['D024_Nome_Empresa']=substr($mT059['D024_Nome_Empresa'],0,10);
            if ($mT059['T174_Flag_Indisponivel']=='S'){
                $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</font><font color='red'> INDISPONÍVEL </font>";
            }
            else if ($mT059['T174_Valor_Custo_Unitario']>0)
            {
                if($g['c007']['C007_Flag_Vendedor']=='D' OR $g['c007']['C007_Flag_Vendedor']=='S'){
                    $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</font><font color='green'> R$ ".number_format($mT059['T174_Valor_Custo_Unitario'],4,',','.')." x ".number_format($mT059['T174_IPV'],2,',','.')." = R$ ".number_format($mT059['T174_Preco_Venda'],2,',','.')."</font>";
                } else {
                    $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</font><font color='green'> R$ ".number_format($mT059['T174_Preco_Venda'],4,',','.')."</font>";
                }
                if (!empty($mT059['T174_Prazo_Entrega_Dias'])) {
                    $retorno .= " <font color='blue' style='font-weight:normal'>Ent:</font> <font color='green'>{$mT059['T174_Prazo_Entrega_Dias']}</font>";
                }
            } else {
                $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='blue'> COTANDO </font>";
            }
            if (!empty($mT059['T174_Observacao'])) {
                $retorno .= " <font color='#D96C00' style='font-weight:normal'><i>{$mT059['T174_Observacao']}</font>";
            }
            if (!empty($mT059['T011_Id'])) {
                $retorno .= " <font color='#880088' style='font-weight:bold'>OC {$mT059['T011_Id']} {$mT059['T011_Prazo_Entrega']}</font>";
            }
           
            $sqlT013 = "SELECT GROUP_CONCAT(T013_Numero_Nota_Fiscal SEPARATOR ', ') as NF 
                            FROM T014
                            LEFT JOIN T013 ON T013_Id = T014_T013_Id
                            LEFT JOIN T012 ON T012_Id = T014_T012_Id
                            LEFT JOIN T059 ON T059_Id = T012_T059_Id
                            WHERE T059_Id = '{$mT059['T059_Id']}'
                            group by T013_Numero_Nota_Fiscal";
            //echo $sqlT013;
            $resT013 = mysql_query($sqlT013);
            echo mysql_error();
            $resT013 = mysql_fetch_array($resT013);

            if (!empty($mT059['T011_Id']) && !empty($resT013[0])) {
                $retorno .= " <font color='#880088' style='font-weight:bold'>NF {$resT013[0]}</font>";
            }                             
        }
    } else if($rowsT012 > 0) {
        $retorno = " - <font color='blue'>(OC)</font>";
    } else {
        $retorno = "<br><font color='#585858' style='font-weight:normal'><i>Aguardando comprador...</i></font>";
    }
    // Busca observacao do cabecalho da cotacao
    $sqlT010 = <<<EOT
        SELECT T010_Observacao
          FROM T189
     LEFT JOIN T059 ON T059_T189_Id=T189_Id
     LEFT JOIN T010 ON T010_Id=T059_T010_Id
         WHERE T059_T189_Id='{$array['todosCampos']['T189_Id']}'
      GROUP BY T189_Id
EOT;
    $resT010 = mysql_query($sqlT010);
    echo mysql_error();
    $rowsT010 = mysql_fetch_array($resT010);
    if (!empty($rowsT010['T010_Observacao']))
    {
        $retorno .= "<br><font color='#D96C00' style='font-weight:normal'><i>{$rowsT010['T010_Observacao']}</font>";
    }
    
    return $array['todosCampos']['T189_Descricao_Produto'] . $retorno;
}
