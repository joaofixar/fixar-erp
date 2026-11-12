<?php
/**
* restricaoIncluirUpdate
* 
* Código PHP para validar um campo de uma tabela na hora de Incluir ou Atualizar.
* Para ver variáveis e funções disponíveis dentro do código, veja:
* 		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoIncluirUpdate']['T012_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T012_Quantidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;



/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysqli_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T012']['updateAfter'] = <<<'EOT'
	mysqli_query("UPDATE T012 SET T012_Valor_IPI=(T012_Valor_Custo_Unitario*(T012_Aliquota_IPI/100)) WHERE T012_Id='{$chaveValor}'");
	mysqli_query("call T012_Gravar_Totalizacao('$chaveValor')");
	$T011=mysqli_query("select T012_T011_Id from T012 where T012_Id='$chaveValor'");
	$mT011=mysql_fetch_array($T011);
	mysqli_query("call T011_Gravar_Totalizacao_3('$mT011[T012_T011_Id]')");
EOT;

$conf['triggers']['T012']['insertAfter'] = <<<'EOT'
	$T011=mysqli_query("select T012_T011_Id from T012 where T012_Id='$chaveValor'");
	$mT011=mysql_fetch_array($T011);
	mysqli_query("call T011_Gravar_Totalizacao_3('$mT011[T012_T011_Id]')");	
EOT;
