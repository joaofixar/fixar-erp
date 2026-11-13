<?php
$conf['restricaoIncluirUpdate']['T182_Data'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T182_Quantidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T182_C007_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T182_D009_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T182_Tipo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T182_NF_Venda'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T182_D164_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;


/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*	http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoTravar']['T182'] = <<<'EOT'
	$retorno=true;
	if ($operacao=='excluir'){
		$ret=mysql_query("SELECT T182_Status from T182 where T182_Id='$valorPk'");
		$result=mysql_fetch_array($ret);
	}	
EOT;
?>