<?php
/**
* Tiago Fischer
* 23/04/2011
* 
* Tabela T185
*/

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T185']['updateAfter'] = <<<'EOT'
$T006=mysqli_query("select T185_T006_Id from T185 where T185_Id='$chaveValor'");
$mT006=mysql_fetch_array($T006);
mysqli_query("call T006_Gravar_Totalizacao_4('$mT006[T185_T006_Id]')");
EOT;

?>