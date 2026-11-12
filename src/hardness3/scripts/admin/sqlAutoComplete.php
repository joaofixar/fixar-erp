<?php
$ch = $_REQUEST['ch'];
$line = $_REQUEST['line'];
$base = $_REQUEST['base'];

$resposta = $line;

echo <<<EOT
<tr>
	<td>
		{$resposta}
	</td>
</tr>
EOT;
