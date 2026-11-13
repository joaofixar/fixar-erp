<?php
namespace hardness;

function backtrace($array) {
	$id = uniqid();
	$array['campoAtual'] = unserialize($array['campoAtual']);
	array_pop($array['campoAtual']);
	$array['campoAtual'] = var_export($array['campoAtual'], true);
	$array['campoAtual'] = str_replace("\n", '<br/>', $array['campoAtual']);
	$array['campoAtual'] = str_replace(" ", '&nbsp;', $array['campoAtual']);
	$array['campoAtual'] = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $array['campoAtual']);
	return <<<EOT
<div id="{$id}" style="display: none;">
{$array['campoAtual']}
</div>
<a href="#" onclick="dialogConfirm('Backtrace', '', false, false, false, $('#{$id}'));">Backtrace</a>
EOT;
}
