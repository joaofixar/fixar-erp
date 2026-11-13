<?php
/**
 * Tabela T255
 */

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T255'] = <<<'EOT'
    $retorno = true;

    if($operacao != 'incluir'){
	    $sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais FROM T255 WHERE T255_Id = '{$valorPk}'");
	    $row = mysql_fetch_array($sql);
	    if ($row['T255_Data_Hora_Baixa_Materiais'] != '0000-00-00 00:00:00') {
	        $retorno = 'Este ordem de produção já baixou matéria prima e material de consumo';
	    }
	}
    if ($operacao == 'excluir') {
        $sql = mysql_query("SELECT T257_Id FROM T257 LEFT JOIN T256 ON T256_Id=T257_T256_Id WHERE T256_T255_Id = '{$valorPk}' LIMIT 1");
        $row = mysql_fetch_assoc($sql);
        if($row['T257_Id'] > 0){
            $retorno = 'Esta ordem de produção já possui quantidade produzida.';
        }
	    $sql = mysql_query("SELECT T255_Flag_Status FROM T255 WHERE T255_Id = '{$valorPk}'");
        $row = mysql_fetch_assoc($sql);
        if($row['T255_Flag_Status'] == '99'){
            $retorno = 'Esta ordem de produção já está Finalizada.';
        }
    }

EOT;
