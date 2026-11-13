<?php
namespace hardness;

/**
* Weiberlan
* 24/02/2012
*/
function campoFornecedor($array){
	$sql="SELECT GROUP_CONCAT(D024_Nome_Empresa SEPARATOR '<br />') as D024_Nome_Empresa FROM T173 LEFT JOIN D024 ON T173_D024_Id=D024_Id WHERE T173_T010_Id='{$array['todosCampos']['T010_Id']}'";
	$resultado=mysql_query($sql);
	$row=mysql_fetch_array($resultado);
	return $row['D024_Nome_Empresa'];
}
function campoValorCotacao($array){
	$sql="SELECT T174_Valor_Total FROM T174 WHERE T174_T173_Id='{$array['todosCampos']['T173_Id']}'";
	$resultado=mysql_query($sql);
	$total=0;
	while($row=mysql_fetch_array($resultado)){
		$total = $total + $row['T174_Valor_Total'];
	}
	$total = gCorrigeNumero($total);
	return $total;
}

function est012NaoLiberar($array) {
	global $g;
	return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Não Liberar', '/est/est012/content/est012ContentNaoLiberar/', '&acaoId={$array['todosCampos']['T173_Id']}', [400,300]); return false;">
				<span class="ui-icon ui-icon-circle-minus" title="Negar"></span>
			</a>
EOT;

}
function est012Liberar($array) {
	global $g;
	return <<<EOT
			<a href="#" onclick="est012AprovarCotacao('{$array['todosCampos']['T173_Id']}', '{$g['divId']}');">
				<span class="ui-icon ui-icon-circle-check" title="Aprovar"></span>
			</a>
EOT;

}

?>