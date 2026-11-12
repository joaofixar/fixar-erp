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
				<h1>Negar Pedido?</h1>
			</div>
			<div data-role="content" data-theme="c">
				<p>Deseja Não Liberar o título?</p>
				<a data-role="button"  data-theme="b" data-ajax="false"  href="./analisePedidoAprovacao.php?T005_Id={$T005_Id}>Sim</a>
				<a data-role="button" data-rel="back" data-theme="c">Não</a>    
			</div>
		</div>
EOT;
