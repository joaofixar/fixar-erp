<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('mobile/tablet/inc.header.php');
$script = '';

if (isset($_REQUEST['alcada_update'])) {
	require_once("bibliotecas/classes/VEN002.php");
	$VEN002 = new VEN002();
	$alcadaUpdate = $_REQUEST['alcada_update'];
	$retorno = $VEN002->enviarParaAnalise($T005_Id,$alcadaUpdate);
	if ($retorno === true) {
		$script .= "alert('Pedido enviado para análise');";
	} else {
		$script .= "alert('{$retorno}');";
	}
	$uniqId = uniqid();
	$script .= "\$.mobile.changePage('../orcamentoLista.php?refresh={$uniqId}', { transition: 'fade'} );";
}

$alcadaUpdate = $_REQUEST['alcadaUpdate'];
if($alcadaUpdate == "S"){
	$alcadaNecessaria = "SUPERVISOR";
}elseif($alcadaUpdate == "V"){
		$alcadaNecessaria = "VENDEDOR";
}elseif($alcadaUpdate == "D"){
	 $alcadaNecessaria = "DIRETOR";
}

echo <<<EOT
	<div data-role="dialog" id="pedidoGravarDescontoManual">
		<div data-role="header" data-theme="d">
			<h1>Pedir liberação</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form action="./pedidoPedirLiberacao.php?T005_Id={$T005_Id}" method="POST" data-ajax="false">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                                A liberação deste Pedido exige alçada de <b>{$alcadaNecessaria}</b>, pedir liberação?
	                                <input id="alcada_update" name="alcada_update" type="hidden" value="{$_REQUEST['alcadaUpdate']}">
	                        </fieldset>
	                    </div>
                <div class="ui-grid-a">
					<div class="ui-block-a">
						<a data-role="button" data-transition="slide" data-rel="back">
            				Não
        				</a>
        			</div>
                	<div class="ui-block-b">
	                    <input value="Sim" type="submit" />
                	</div>   
                </div>
            </form>    
		</div>
	</div>
	
	 <script>
    	$( document ).undelegate("#pedidoGravarDescontoManual", "pageinit");
    	$( document ).delegate("#pedidoGravarDescontoManual", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
