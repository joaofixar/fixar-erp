<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');
$script="";

// Incluir produtos orçamento (Através da tabela T198)
if(!empty($_REQUEST['incluirProdutos'])){
	require_once("bibliotecas/classes/VEN002.php");
	$VEN002 = new VEN002();
	$retorno = $VEN002->incluirProdutosPesquisaGridPedido($T005_Id);
	$retorno = true;
	if($retorno === true){
		$uniqId=uniqid();
		$script .= <<<EOT
			$.mobile.changePage('./pedido.php?T005_Id={$T005_Id}{$acao}&refresh={$uniqId}', { transition: 'slide'} );
EOT;
	}
}

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoAddListaProdutos">
EOT;


include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pagepedidoAddListaProdutos", "pageinit");
    	$( document ).delegate("#pagepedidoAddListaProdutos", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
