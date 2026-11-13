<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$T138_Id = isset($_REQUEST['T138_Id']) ? $_REQUEST['T138_Id'] : false;
$uniqId=uniqid();

echo <<<EOT
	<div data-role="dialog">
		<div data-role="header" data-theme="d">
			<h1>Remover Produto?</h1>
		</div>
		<div data-role="content" data-theme="c">
			<p>Deseja remover esta troca do caixa?</p>
			<a data-role="button" data-theme="b" href="./caixa.php?refresh={$uniqId}&T005_Id={$T005_Id}&T138_Id={$T138_Id}" data-ajax="false">Sim</a>
			<a data-role="button" data-rel="back" data-theme="c">Não</a>    
		</div>
	</div>
EOT;
