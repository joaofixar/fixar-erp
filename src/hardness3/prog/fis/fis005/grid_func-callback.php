<?php
namespace hardness;

function excluirCep($array) {
	global $g;
    
    return <<<EOT
    <a href="#" onclick="excluirCep('{$array['todosCampos']['W004_Id']}','{$g['divId']}')">
            <span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
    </a>
EOT;
}
?>