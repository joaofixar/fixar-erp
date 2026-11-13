<?php
 $conf['restricaoIncluirUpdate']['T029_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T029_Data'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*    http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoTravar']['T029'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T029_Id FROM T029 WHERE T029_Id = '{$valorPk}' AND T029_Flag_Ok='S'";
        $res = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0) {
            $retorno = 'Esta Divergência já foi concluída.';
        }
    }
EOT;

?>