<?php
namespace hardness;

function rowData($array, $pks){
    global $sys;
	$tabelas = array();
	foreach ($pks as $pk) {
	    // Exemplo: usuario.C007_Id
        preg_match('#^([^\.]+\.)?(?<tabela>[A-Z]{1,3}[0-9]{3}A?)_Id#', $pk, $match);
        $tabela = strtoupper($match['tabela']);
		$acaoId = $array['todosCampos'][$pk];
		$tabelas[$tabela] = $acaoId;
	}
	$tabelas = base64_encode(serialize($tabelas));
	return <<<EOT
		<a href="#" onclick="abrirJanela(false, divIdRootAberto(), gridPegarDivId($(this)), unique(), '', 'Editar registro' , '/sistema/funcoes/editarRegistro/content/', '&tabelas=' + encodeURIComponent('{$tabelas}'), '', [990,600]); return false;" tabindex='-1'>
			<div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-wrench" title="Editar registro"></span>
			</div>
		</a>
EOT;
}
