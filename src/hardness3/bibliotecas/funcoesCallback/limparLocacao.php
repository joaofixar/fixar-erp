<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: limparLocacao()
*/

function limparLocacao ($array) {
	global $g;
	$exibir = false;
	$sql = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM T066 LEFT JOIN D009 ON D009_Id=T066_D009_Id LEFT JOIN D049 ON D009_D049_Id=D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE T066_Id = '{$array['todosCampos']['T066_Id']}'");
	$row = mysql_fetch_assoc($sql);
	if ($row['D001_Flag_Pre_Cadastro'] == 'S'){
		if(isset($g['c029Ids'][187])){
			$exibir = true;
		}
	} else {
		if(isset($g['c029Ids'][186])){
			$exibir = true;
		}
	}
	if($exibir){
		return <<<EOT
			<a href="#" onclick="limparLocacao('{$g['divId']}', '{$array['todosCampos']['T066_Id']}'); return false;">Limpar Locação</a>
EOT;
	} else {
		return '';
	}
}
