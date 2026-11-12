<?php
namespace hardness;
function validaCampoExtratoCliente($array){
 	global $g;
	$imagem = <<<EOT
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Extrato Cliente', '/rel/relatorios/extratoCliente/contentRelatorio/', '&acaoId={$array['todosCampos']['T002_Id']}&D024_Id={$array['todosCampos']['D024_Id']}&Empresa={$g['empresaAtual']}', [1000,600]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-document"  title="Extrato Cliente"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}