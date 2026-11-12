<?php
namespace hardness;

function horizontal($array){
	return <<<EOT
			<a href="#" onclick="horizontal(this)">
				<span class="ui-icon ui-icon-zoomin" title="Ver Registro"></span>
			</a>
EOT;
}
