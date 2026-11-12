<?php
/**
* camposTipo
* Define qual é o tipo do campo. Com essa informação é feita a formatação dele no FORM, GRID, etc.
*/

$conf['restricaoTravar']['T226'] = <<<'EOT'
	$retorno = true;

    if ($operacao == 'alterar' || $operacao == 'excluir') {
        
        $sql = "SELECT T224_Id FROM T226 LEFT JOIN T224 ON T224_Id=T226_T224_Id WHERE T226_Id = '{$valorPk}' AND T224_Flag_Status != '0' AND T224_Flag_Status != '' AND T224_Flag_Status != '2'";

    } else if ($operacao == 'incluir'){
        
        $sql = "SELECT T224_Id FROM T224 WHERE T224_Id = '{$valorPkParent}' AND T224_Flag_Status != '0' AND T224_Flag_Status != '' AND T224_Flag_Status != '2'";
    
    }

    $res = mysql_query($sql);
    $rows = mysql_num_rows($res);
    if ($rows > 0 and $g['nivelAlcada'] != 4 && (!isset($g['c029Ids'][199]))) {
         $retorno = "Ordem de Compra já está aprovada!";
    }

    if ($operacao == 'alterar' || $operacao == 'excluir') {

        $T013 = mysql_query("SELECT T013_Id,
                                    T013_Numero_Nota_Fiscal 
                            FROM T013 
                        LEFT JOIN T014 ON T014_T013_Id=T013_Id
                        LEFT JOIN T225 ON T225_Id=T014_T225_Id
                        LEFT JOIN T224 ON T224_Id=T225_T224_Id
                        LEFT JOIN T226 ON T226_T224_Id=T224_Id
                            WHERE T226_Id='{$valorPk}'
                                AND T013_Flag_Travar_Nota='S'
                            LIMIT 1");

    } else if ($operacao == 'incluir') {

        $T013 = mysql_query("SELECT T013_Id,
                                    T013_Numero_Nota_Fiscal 
                               FROM T013 
                           LEFT JOIN T014 ON T014_T013_Id=T013_Id
                           LEFT JOIN T225 ON T225_Id=T014_T225_Id
                           LEFT JOIN T224 ON T224_Id=T225_T224_Id
                               WHERE T224_Id='{$valorPkParent}'
                                 AND T013_Flag_Travar_Nota='S'
                                LIMIT 1");

    }

    $mT013 = mysql_fetch_assoc($T013);
    if(mysql_num_rows($T013) > 0){
        $retorno = "Não é possível alterar OC vinculada a NF-e {$mT013['T013_Numero_Nota_Fiscal']} já finalizada.";
    }
EOT;
