<?php

$conf['restricaoTravar']['T250'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar') {
        $sql = "SELECT T250_Flag_Campo_Calculado FROM T250 WHERE T250_Id = '{$valorPk}'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if($row['T250_Flag_Campo_Calculado']=='S'){
           $retorno = 'Este campo não pode ser alterado por ser calculado (c).';
        }
    }
EOT;
