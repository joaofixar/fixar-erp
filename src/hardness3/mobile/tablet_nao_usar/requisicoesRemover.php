<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$T189_Id = isset($_REQUEST['T189_Id']) ? $_REQUEST['T189_Id'] : false;
$uniqId=uniqid();

echo <<<EOT
	<div data-role="dialog">
		<div data-role="header" data-theme="d">
			<h1>Remover Requisição?</h1>
		</div>
		<div data-role="content" data-theme="c">
			<p>Deseja remover esta requisição de compra?</p>
			<a data-role="button" data-theme="b" onclick="\$.mobile.changePage( './requisicoes.php?T189_Id={$T189_Id}&refresh={$uniqId}', { transition: 'slide'} );">Sim</a>
			<a data-role="button" data-rel="back" data-theme="c">Não</a>    
		</div>
	</div>
EOT;
