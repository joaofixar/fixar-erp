<?php

$conf['restricaoTravar']['T251'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar') {
        $sql = "SELECT T251_Flag_Campo_Calculado FROM T251 WHERE T251_Id = '{$valorPk}'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if($row['T251_Flag_Campo_Calculado']=='S'){
           $retorno = 'Este campo não pode ser alterado por ser calculado (c).';
        }
    }
EOT;
