<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('inc.header.php');
$script = '';

if (isset($_REQUEST['alcada_update'])) {
	require_once("bibliotecas/classes/VEN001.php");
	$VEN001 = new VEN001();
	$alcadaUpdate = $_REQUEST['alcada_update'];
	$retorno = $VEN001->enviarParaAnalise($T003_Id,$alcadaUpdate);
	if ($retorno === true) {
		$script .= "alert('Orçamento enviado para análise');";
	} else {
		$script .= "alert('{$retorno}');";
	}
	$uniqId = uniqid();
	$script .= "\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );";
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
	<div data-role="dialog" id="orcamentoGravarDescontoManual">
		<div data-role="header" data-theme="d">
			<h1>Pedir liberação</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form action="./orcamentoPedirLiberacao.php?T003_Id={$T003_Id}" method="POST" data-ajax="false">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                                A liberação deste orçamento exige alçada de <b>{$alcadaNecessaria}</b>, pedir liberação?
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
    	$( document ).undelegate("#orcamentoGravarDescontoManual", "pageinit");
    	$( document ).delegate("#orcamentoGravarDescontoManual", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
