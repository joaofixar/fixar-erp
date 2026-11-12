<?php
namespace hardness;
/**
* ETC - ETC006 - Emails
* 
* Funções PHP para callbacks
*/

// Imagem para exclusão da linha
function excluirEmail($array) {
	global $g;
	$T139_Id = urlencode($array['todosCampos']['T139_Id']);
    	return <<<EOT
    		<a href="#" onclick="excluirEmail('{$array['todosCampos']['T139_Id']}', '{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
}

function campoLido($array) {
	global $g;
	if ($array['campoAtual']) {
    	return <<<EOT
    		<a href="#" onclick="emailMarcarComoNaoLido(this, '{$array['todosCampos']['T139_Id']}', '{$g['divId']}')">
    			<span style='margin-left: 5px;' class="ui-icon ui-icon-check" title="Lido - clique para marcar como não lido"></span>
    		</a>
EOT;
	} else {
		return "<img src='/hardness3/static/img/tmp/icone_enviar.gif' style='margin-left: 1px;'/>";
	}
}

function campoLidoNegrito($array, $campo) {
	global $g;
	if ($campo == 'T139_Data_Hora') {
		$array['campoAtual'] = gCorrigeData($array);
	} else if ($campo == 'T139_De' || $campo == 'T139_Para') {
		$array['campoAtual'] = explode('<', $array['campoAtual']);
		$array['campoAtual'] = $array['campoAtual'][0];
		$array['campoAtual'] = str_replace('"', '', $array['campoAtual']);
		if (strlen($array['campoAtual']) > 30) {
			$array['campoAtual'] = substr($array['campoAtual'], 0, 30) . ' ... ';
		}
		$array['campoAtual'] = gHtmlentities($array,ENT_COMPAT,'utf-8');
	} else if ($campo == 'T139_Titulo') {
		$array['campoAtual'] = explode('<', $array['campoAtual']);
		$array['campoAtual'] = $array['campoAtual'][0];
		$array['campoAtual'] = str_replace('"', '', $array['campoAtual']);
		if (strlen($array['campoAtual']) > 60) {
			$array['campoAtual'] = substr($array['campoAtual'], 0, 60) . ' ... ';
		}
		$array['campoAtual'] = gHtmlentities($array,ENT_COMPAT,'utf-8');
	} else if ($campo == 'D024_Nome_Fantasia') {
		return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D024_Id']}') + '&table=D024', [950,550]); return false;">{$array['campoAtual']}</a>
EOT;
	} else if ($campo == 'T139_T003_Id') {
		if (empty($array['campoAtual'])) {
			return '';
		}
		return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T139_T003_Id']}') + '&table=T003', [900,550]); return false;">{$array['campoAtual']}</a>
EOT;
	} else if ($campo == 'T139_T005_Id') {
		if (empty($array['campoAtual'])) {
			return '';
		}
		return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T139_T005_Id']}') + '&table=T005', [900,550]); return false;">{$array['campoAtual']}</a>
EOT;
	} else if ($campo == 'T003_Valor_Total') {
		if (empty($array['campoAtual'])) {
			$array['campoAtual'] = '&nbsp;';
		}
		return $array['campoAtual'];
	}
	if (!$array['todosCampos']['T139_Flag_Lido']) {
    	return "<span style=\"font-weight: bold;\">" . trim($array['campoAtual']) . "</span>";
	} else {
		return $array['campoAtual'];
	}
}

function checkBoxLido($array) {
    global $g, $r_acaoId;
    if ($array['todosCampos']['T139_C007_Id'] == $g['usuarioAtual']) {
		$retorno = "<input onchange=\"selecionarEmail(this, '{$array['todosCampos']['T139_Id']}', '{$g['divId']}')\" type='checkbox' id='T161_Flag_Ok' title='Confirmar Item'";
	    if ($array['todosCampos']['T139_Flag_Selecionado'] == 1) {
    		return $retorno .="checked='checked'";
		}
	    $retorno .= " />";
	} else {
		$retorno = '&nbsp;';
	}
    return $retorno;

}

function temAnexo($array) {
	global $g, $mail;
	$anexos = $mail->listaAnexos($array['todosCampos']['T139_Id']);
	if (count($anexos) == 0) {
    	return "&nbsp;";
	} else {
		return "<img src='/hardness3/static/img/tmp/anexo.png' title='Este e-mail contém ".count($anexos)." anexo(s)'/>";
	}
}

function spam($array) {
	global $g, $mail;
	$array['todosCampos']['T139_De'] = str_replace('"', '', $array['todosCampos']['T139_De']);
	return "<img src='/hardness3/static/img/tmp/spam.png' style='width: 20px; height: 20px;' title='Este e-mail foi considerado um spam'/ onclick=\"removerEmailSpam('{$array['todosCampos']['T139_Id']}', '{$array['todosCampos']['T139_De']}', '{$g['divId']}')\">";
}

function vincularEmailOrcamentoCallback($array) {
	global $g, $r_T003_Id, $r_divRefreshTop;

	if (empty($r_T003_Id)) {
		return '';
	} else if ($array['todosCampos']['T139_T003_Id'] === $r_T003_Id) {
		return "<span class='ui-icon ui-icon-check' style='cursor: default; margin: 0 auto;' title='E-mail já vinculado ao orçamento'></span>";
	} else if ($array['todosCampos']['T139_T003_Id'] > 0) {
		return "<span class='ui-icon ui-icon-close' style='cursor: default; margin: 0 auto;' title='E-mail já vinculado a outro orçamento'></span>";
	}

	$divRefreshTop = isset($r_divRefreshTop) ? addslashes($r_divRefreshTop) : '';

	$abrirJanela = <<<JS
		abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Responder', '/etc/etc006/content/etc006ContentEmailEnviar/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T139_Id']}') + '&orcId=' + encodeURIComponent('{$r_T003_Id}') + '&emailResposta=true' + '&divRefreshTop=' + encodeURIComponent('{$divRefreshTop}') + '&tabela=T139', [1000,500]);
JS;
	return <<<EOT
		<a href="#" onclick="{$abrirJanela} return false;">
			<span class="ui-icon ui-icon-link" style="margin: 0 auto;" title="Vincular ao orçamento e responder"></span>
		</a>
EOT;
}

function retornaUsuario($array) {
	global $g;
	$retorno = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$array['todosCampos']['T139_C007_Id']}'");
	$retorno = mysql_fetch_array($retorno);
	return strtoupper($retorno['C007_Primeiro_Nome']); 
}
