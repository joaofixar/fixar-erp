<?php
namespace hardness;

function desvincularLancamentoTransacao($array){
	global $g;
	if($array['campoAtual'] > 0) {
		return "<a href=\"#\" onclick=\"desvincularLancamentoTransacao('{$array['todosCampos']['T216_Id']}', '{$g['divId']}'); return false;\">{$array['campoAtual']}</a>";
	}
	return '';
}
