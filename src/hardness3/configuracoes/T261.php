<?php
/**
 * Tabela T261
 */
// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T261_D001_Id_Composicao'] = <<<'EOT'
    $retorno = rVerificaFK('D001_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T261_Quantidade'] = <<<'EOT'
    $retorno = rNumeroMaiorQueZero($valor);
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T261'] = <<<'EOT'
    $retorno = true;

    if ($operacao == 'incluir') {
    	$sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais, T255_Id FROM T255 LEFT JOIN T256 ON T255_Id=T256_T255_Id WHERE T256_Id = '{$valorPkParent}'");
    } else {
    	$sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais, T255_Id FROM T255 LEFT JOIN T256 ON T255_Id=T256_T255_Id LEFT JOIN T261 ON T261_T256_Id=T256_Id WHERE T261_Id = '{$valorPk}'");
    }
    $row = mysql_fetch_array($sql);
    if ($row['T255_Data_Hora_Baixa_Materiais'] != '0000-00-00 00:00:00' && !empty($row['T255_Id'])) {
        $retorno = 'Este ordem de produção já baixou matéria prima e material de consumo';
    }
EOT;
