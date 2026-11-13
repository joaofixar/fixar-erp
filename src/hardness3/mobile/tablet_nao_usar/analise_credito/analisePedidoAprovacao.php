<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('mobile/tablet/inc.header.php');
//Classe para Análise de Crédito
require_once("bibliotecas/classes/FIN007.php");
$script = "";

if($_REQUEST['btn'] == "Aprovar"){
	$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
	$T005_Id = mysql_real_escape_string($T005_Id);

	$FIN007 = new FIN007();
	$liberarPedido = $FIN007->validaCampoLiberar($T005_Id);

	if ($liberarPedido === true) {
		$script .="alert('Pedido aprovado com sucesso!');";
		$script .= "\$.mobile.changePage( './pedidoAnalise.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
	} else {
		$script .="alert('Não Foi Possível Aprovar o Pedido');";
		$script .= "\$.mobile.changePage( './pedidoAnalise.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
	}

}elseif($_REQUEST['btn'] == "Negar"){
	$T005_Motivo_Nao_Liberado=$_REQUEST['T005_Motivo_Nao_Liberado'];	
	$T005_Id=$_REQUEST['T005_Id'];
	$FIN007 = new FIN007();
	$retorno = $FIN007->naoLiberar($T005_Id, $T005_Motivo_Nao_Liberado);
	$retorno = ($retorno === true) ? 'Pedido não liberado!' : $retorno;
	
		if ($verifica !== true) {
			$script .="alert('{$retorno}');";
			$script .= "\$.mobile.changePage( './pedidoAnalise.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}else{
			$script .="alert('Pedido não liberado!!');";
			$script .= "\$.mobile.changePage( './pedidoAnalise.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}
}


echo <<<EOT
	<div data-role="dialog" id="analisePedidoAprovacao" data-ajax="false">
		<div data-role="header" data-theme="d">
			<h1>Não Liberar Título</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form method="POST" action="./analisePedidoAprovacao.php?acao=aprovar&T005_Id={$T005_Id}" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T005_Motivo_Nao_Liberado">
	                                Observação: 
	                            </label>
	                            	<textarea name="T005_Motivo_Nao_Liberado" id="T005_Motivo_Nao_Liberado" value="" style="width:150%;height:100px;">{$T005['T005_Motivo_Nao_Liberado']}</textarea>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
					<div class="ui-block-a">
						<a data-role="button" data-transition="slide" data-rel="back">
            				Voltar
        				</a>
        			</div>
                	<div class="ui-block-b">
	                    <input type="submit" name="btn" value="Negar">
                	</div>
                </div>
            </form>    
		</div>
	</div>
	
	 <script>
    	$( document ).undelegate("#analisePedidoAprovacao", "pageinit");
    	$( document ).delegate("#analisePedidoAprovacao", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
