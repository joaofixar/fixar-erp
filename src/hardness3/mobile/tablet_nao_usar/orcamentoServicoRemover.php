<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$T145_Id = isset($_REQUEST['T145_Id']) ? $_REQUEST['T145_Id'] : false;
$uniqId = uniqid();

echo <<<EOT
	<div data-role="dialog">
		<div data-role="header" data-theme="d">
			<h1>Remover Serviço?</h1>
		</div>
		<div data-role="content" data-theme="c">
			<p>Deseja remover este serviço do orçamento?</p>
			<a href="#" data-role="button" data-theme="b" onclick="\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&T145_Id={$T145_Id}&refresh={$uniqId}', { transition: 'slide'} );">Sim</a>
			<a href="#" data-role="button" data-rel="back" data-theme="c">Não</a>
		</div>
	</div>
EOT;
