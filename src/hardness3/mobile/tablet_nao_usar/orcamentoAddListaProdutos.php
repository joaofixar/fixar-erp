<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');
$script="";

// Incluir produtos orçamento (Através da tabela T198)
if(!empty($_REQUEST['incluirProdutos'])){
	require_once("bibliotecas/classes/VEN001.php");
	$VEN001 = new VEN001();
	$retorno = $VEN001->incluirProdutosPesquisaGridOrcamento($T003_Id);
	$retorno = true;
	if($retorno === true){
		$uniqId=uniqid();
		$script .= <<<EOT
			$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'slide'} );
EOT;
	}
}
include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageorcamentoAddListaProdutos">
EOT;


include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageorcamentoAddListaProdutos", "pageinit");
    	$( document ).delegate("#pageorcamentoAddListaProdutos", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
