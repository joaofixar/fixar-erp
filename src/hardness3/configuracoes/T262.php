<?php
/**
 * Tabela T262
 */

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T262'] = <<<'EOT'
    $retorno = true;

    if ($operacao == 'incluir') {
    	$sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais, T255_Id FROM T255 LEFT JOIN T256 ON T255_Id=T256_T255_Id WHERE T256_Id = '{$valorPkParent}'");
    } else {
    	$sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais, T255_Id FROM T255 LEFT JOIN T256 ON T255_Id=T256_T255_Id LEFT JOIN T262 ON T262_T256_Id=T256_Id WHERE T262_Id = '{$valorPk}'");
    }
    $row = mysql_fetch_array($sql);
    if ($row['T255_Data_Hora_Baixa_Materiais'] != '0000-00-00 00:00:00' && !empty($row['T255_Id'])) {
        $retorno = 'Este ordem de produção já baixou matéria prima e material de consumo';
    }
EOT;
