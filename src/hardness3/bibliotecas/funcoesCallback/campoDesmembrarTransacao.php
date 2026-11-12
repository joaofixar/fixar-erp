<?php
namespace hardness;

function campoDesmembrarTransacao($array){
	global $g;
	if ($array['todosCampos']['T216_Flag_Importar'] != 'I') {
		return <<<EOT
			<span class="ui-icon ui-icon-folder-open" title="Desmembrar Transação" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Desmembrar Transação', '/fin/fin003/content/fin003ContentDesmembrarTransacao/', '&T216_Id=' + encodeURIComponent('{$array['todosCampos']['T216_Id']}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}'), [990,590]);"></span>
EOT;
	} else {
		return '<span class="ui-icon ui-icon-check" style="margin-left: 10px; float: left;" />';
	}
}
