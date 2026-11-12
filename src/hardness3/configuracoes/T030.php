<?php
$conf['restricaoIncluirUpdate']['T030_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;
 $conf['restricaoIncluirUpdate']['T030_Codigo_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T030_Flag_Entrada_Saida'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T030_Flag_Entrada_Saida'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T030_T066_Id'] = <<<'EOT'
    $retorno = rVerificaFK('T066_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T030_T066_Id_Destino'] = <<<'EOT'
    if ($valores['T030_Flag_Entrada_Saida'] == 'T') {
        $retorno = rVerificaFK('T066_Id', $valor);
    }
EOT;

/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*    http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoTravar']['T030'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T030_Id FROM T030 LEFT JOIN T029 ON T029_Id=T030_T029_Id WHERE T030_Id = '{$valorPk}' AND T029_Flag_Ok='S' ";
        $res = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0) {
            $retorno = 'Este D.E. já foi processado.';
        }
    }
EOT;

?>