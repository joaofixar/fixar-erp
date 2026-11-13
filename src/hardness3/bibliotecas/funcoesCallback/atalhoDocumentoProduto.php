<?php
namespace hardness;

function atalhoDocumentoProduto ($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Documentos', '/cad/cad002/content/cad002ContentDocumentosProduto/','&acaoId={$array['todosCampos']['D001_Id']}', [990,605]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-document" title="Documentos do Produto"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
