<?php
$conf['restricaoTravar']['T216'] = <<<'EOT'
	$retorno = true;
	$T216 = mysql_fetch_array(mysql_query("SELECT * FROM T216 WHERE T216_Id = '{$valorPk}'"));
	if(!empty($T216['T216_T019_Id'])){
		$retorno = false;
	}
EOT;

  
?>