<?php
/**
 * Tabela T263
 */
// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T263_D009_Id'] = <<<'EOT'
    $retorno = rVerificaFK('D009_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T263_Quantidade'] = <<<'EOT'
    $retorno = rNumeroMaiorQueZero($valor);
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T263'] = <<<'EOT'
    $retorno = true;

    if ($operacao == 'incluir') {
    	$sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais FROM T255 LEFT JOIN T256 ON T255_Id=T256_T255_Id LEFT JOIN T262 ON T262_T256_Id=T256_Id WHERE T262_Id = '{$valorPkParent}'");
    } else {
    	$sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais FROM T255 LEFT JOIN T256 ON T255_Id=T256_T255_Id LEFT JOIN T262 ON T262_T256_Id=T256_Id LEFT JOIN T263 ON T263_T262_Id=T262_Id WHERE T263_Id = '{$valorPk}'");
    }
    $row = mysql_fetch_array($sql);
    if ($row['T255_Data_Hora_Baixa_Materiais'] != '0000-00-00 00:00:00') {
        $retorno = 'Este ordem de produção já baixou matéria prima e material de consumo';
    }
EOT;
