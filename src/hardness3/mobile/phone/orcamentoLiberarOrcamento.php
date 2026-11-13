<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
$uniqId=uniqid();

echo <<<EOT
    	 <div data-role="dialog">
			<div data-role="header" data-theme="d">
				<h1>Aprovar Orçamento?</h1>
			</div>
			<div data-role="content" data-theme="c">
				<p>Deseja aprovar este orçamento?</p>
				<a data-role="button"  data-theme="b" href="./orcamentoAprovacao.php?T003_Id={$T003_Id}&btn=Aprovar">Sim</a>
				<a data-role="button" data-rel="back" data-theme="c">Não</a>    
			</div>
		</div>
EOT;
