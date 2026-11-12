<?php
namespace hardness;

/**
 * Gera a coluna compartilhada de retorno entre etapas da expedição.
 */
function campoRetornarExpedicao($array) {
	global $g;

	return <<<EOT
		<span onclick="est002RetornarPedidoExpedicao('{$array['todosCampos']['T005_Id']}', '{$g['divId']}', '{$array['todosCampos']['T005_Flag_Status']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para uma etapa anterior da expedição"></span>
		   </div>
		</span>
EOT;
}
