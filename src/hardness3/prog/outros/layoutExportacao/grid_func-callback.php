<?php
namespace hardness;

function docLink($array) {
	global $g;
	return "<a href=\"{$array['campoAtual']}\" target=\"_blank\">Link</a>";
}

function subLayoutSelect($array) {
	global $g;
	$campo = substr($array['campoAtual'], 0, 30);
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Select', '/outros/layoutExportacao/content/editarSelect/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D151_Id']}') + '&tabela=D151', [800,500]);\">{$campo} ...</a>";
}

function subLayoutFormato($array) {
	global $g;
	return $array['campoAtual'] == '9' ? 'Número' : 'Texto';
}

function subLayoutComentario($array) {
	$retorno = '';
	$temp = explode("\n", $array['campoAtual']);
	$total = count($temp);
	foreach ($temp as $key => $linha) {
		$retorno .= $linha;
		if ($key < (count($temp)-1)) {
			$retorno .= '<br/><br/>                        ';
		}
	}
	return $retorno;
}

function callbackOpcoes($array) {
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Callback', '/outros/layoutExportacao/content/adicionarCallback/', '&acaoId={$array['todosCampos']['D153_Id']}', [650,550]);\">Editar</a>";
}

function subLayoutOpcoes($array) {
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Testar Sub Layout', '/outros/layoutExportacao/outro/testarSubLayout/', '&testar=true&D151_Id={$array['todosCampos']['D151_Id']}', [950,700]);\">Testar</a>";
}

function subLayoutOpcoesItem($array) {
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Testar Sub Layout', '/outros/layoutExportacao/outro/testarSubLayout/', '&testar=true&D151_Id={$array['todosCampos']['D152_D151_Id']}&D152_Id={$array['todosCampos']['D152_Id']}', [950,700]);\">Testar</a>";
}

function checkboxUtilizaDatas($array){
	global $g;
	$D151_Flag_Utiliza_Data = $array['todosCampos']['D151_Flag_Utiliza_Data'];
	$D151_Id = $array['todosCampos']['D151_Id'];

	$retorno = "<input onclick=\"exportacaoEBSalteraFlagData(this, '{$D151_Id}', '{$g['divId']}');\" type='checkbox' id='D151_Flag_Utiliza_Data' title='Marcar'";
	if ($D151_Flag_Utiliza_Data == 'S'){
		return $retorno .="checked='checked' />";
	} else {
		return $retorno .= " />";
	}
}
