<?php	
namespace hardness;

function campoCancelarTituloReceber($array) {
	global $g;
	$T002_Id = $array['todosCampos']['T002_Id'];
	$sql = mysql_query("SELECT * FROM T019 WHERE T019_T002_Id = '{$T002_Id}'");	
	if (mysql_num_rows($sql) > 0 || $array['todosCampos']['T002_Flag_Cancelada'] == 'S'){
		return "&nbsp";
	} else {
		return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Motivo Cancelamento', '/fin/fin001/content/fin001ContentCancelamento/', '&acaoId={$array['todosCampos']['T002_Id']}', [400,230]); return false;">
				<span class="ui-icon ui-icon-circle-close" title="Cancelar"></span>
			</a>
EOT;
	}
}