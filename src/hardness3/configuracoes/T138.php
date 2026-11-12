<?php
$conf['camposTipo']['T138_Quantidade'] = array('numero', 0);
$conf['restricaoTravar']['T138'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T138_Id FROM T138 LEFT JOIN T005 ON T005_Id=T138_T005_Id WHERE T138_Id = '{$valorPk}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL)";
        $res = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0) {
            $retorno = 'Este pedido já foi faturado.';
        }
    } elseif ($operacao == 'incluir') {
        $sql = "SELECT T005_Id FROM T005 WHERE T005_Id = '{$valorPkParent}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL)";
        $res = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0) {
            $retorno = 'Este pedido já foi faturado.';
        }
    } else {
        $retorno = true;
    }
EOT;
