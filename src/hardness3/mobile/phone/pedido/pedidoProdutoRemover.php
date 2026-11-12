<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
$uniqId=uniqid();

echo <<<EOT
	<div data-role="dialog">
		<div data-role="header" data-theme="d">
			<h1>Remover Produto?</h1>
		</div>
		<div data-role="content" data-theme="c">
			<p>Deseja remover este produto do Pedido?</p>
			<a data-role="button" data-theme="b" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&refresh={$uniqId}', { transition: 'slide'} );">Sim</a>
			<a data-role="button" data-rel="back" data-theme="c">Não</a>    
		</div>
	</div>
EOT;
