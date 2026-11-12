<?php
/**
 * Tabela T258
 */

$conf['triggers']['T258']['updateAfter'] = <<<'EOT'
	$sqlT258 = mysql_query("SELECT T258_D024_Id, IFNULL(T258_D013_Id,0) T258_D013_Id FROM T258 where T258_Id='{$chaveValor}'");
	$rowT258 = mysql_fetch_array($sqlT258);

	$sqlD085 = mysql_query("SELECT D085_Id, D085_T259_Id, D085_T139_Id FROM D085 WHERE D085_T258_Id = '{$chaveValor}'");
	while($rowD085 = mysql_fetch_assoc($sqlD085)){
		mysqli_query("UPDATE D085 SET D085_D024_Id = '{$rowT258['T258_D024_Id']}', D085_D013_Id = '{$rowT258['T258_D013_Id']}' WHERE D085_Id = '{$rowD085['D085_Id']}'");
		mysqli_query("UPDATE T139 SET T139_D024_Id = '{$rowT258['T258_D024_Id']}', T139_D013_Id = '{$rowT258['T258_D013_Id']}' WHERE T139_Id = '{$rowD085['D085_T139_Id']}'");
		mysqli_query("UPDATE T259 SET T259_D024_Id = '{$rowT258['T258_D024_Id']}' WHERE T259_Id = '{$rowD085['D085_T259_Id']}'");
	}
EOT;

