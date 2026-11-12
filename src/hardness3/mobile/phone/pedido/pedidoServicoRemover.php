<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$T147_Id = isset($_REQUEST['T147_Id']) ? $_REQUEST['T147_Id'] : false;
$uniqId = uniqid();

echo <<<EOT
	<div data-role="dialog">
		<div data-role="header" data-theme="d">
			<h1>Remover Serviço?</h1>
		</div>
		<div data-role="content" data-theme="c">
			<p>Deseja remover este serviço do Pedido?</p>
			<a href="#" data-role="button" data-theme="b" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&T147_Id={$T147_Id}&refresh={$uniqId}', { transition: 'slide'} );">Sim</a>
			<a href="#" data-role="button" data-rel="back" data-theme="c">Não</a>
		</div>
	</div>
EOT;
