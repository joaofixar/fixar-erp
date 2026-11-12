<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
$uniqId=uniqid();

echo <<<EOT
    	 <div data-role="dialog">
			<div data-role="header" data-theme="d">
				<h1>Aprovar caixa?</h1>
			</div>
			<div data-role="content" data-theme="c">
				<p>Deseja aprovar este caixa?</p>
				<a data-role="button"  data-theme="b" data-ajax="false"  href="./caixaAprovacao.php?acao=aprovar&T005_Id={$T005_Id}&btn=Aprovar">Sim</a>
				<a data-role="button" data-rel="back" data-theme="c">Não</a>    
			</div>
		</div>
EOT;
