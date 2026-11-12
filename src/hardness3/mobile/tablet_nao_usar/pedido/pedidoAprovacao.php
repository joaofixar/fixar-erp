<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('mobile/tablet/inc.header.php');
require_once('bibliotecas/classes/VEN013.php');
$VEN013 = new VEN013();
$script = "";

if($_REQUEST['btn'] == "Aprovar"){
	$T005_Liberou_Alcada_Observacao_Nao_Liberado=$_REQUEST['T005_Liberou_Alcada_Observacao_Nao_Liberado'];	
	mysql_query("UPDATE T005 SET T005_Liberou_Alcada_Observacao_Nao_Liberado='{$T005_Liberou_Alcada_Observacao_Nao_Liberado}' WHERE T005_Id = '{$T005_Id}'");
	$verifica = $VEN013->ven013AprovarPedido($T005_Id, true);
	$uniqId = uniqid();
	
		if ($verifica !== true) {
			$script .="alert('{$verifica}');";
			$script .= "\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}else{
			$script .="alert('Pedido aprovado com sucesso!');";
			$script .= "\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}
}elseif($_REQUEST['btn'] == "Negar"){
	$form['T005_Liberou_Alcada_Observacao_Nao_Liberado']=$_REQUEST['T005_Liberou_Alcada_Observacao_Nao_Liberado'];	
	$form['T005_Id']=$_REQUEST['T005_Id'];
	$form = new formWrap($form);	
	$verifica = $VEN013->ven013NaoLiberarPedido($form);
	$uniqId = uniqid();
	
		if ($verifica !== true) {
			$script .="alert('{$verifica}');";
			//$script .= "\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}else{
			$script .="alert('Pedido Negado!');";
			$script .= "\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}
}


echo <<<EOT
	<div data-role="dialog" id="pedidoAprovacao" data-ajax="false">
		<div data-role="header" data-theme="d">
			<h1>Análise de Pedido</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form method="POST" action="./pedidoAprovacao.php?T005_Id={$T005_Id}" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T005_Liberou_Alcada_Observacao_Nao_Liberado">
	                                Observação: 
	                            </label>
	                            	<textarea name="T005_Liberou_Alcada_Observacao_Nao_Liberado" required="required" id="T005_Liberou_Alcada_Observacao_Nao_Liberado" value="" style="width:150%;height:100px;">{$T005['T005_Liberou_Alcada_Observacao_Nao_Liberado']}</textarea>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-b">
					<div class="ui-block-a">
						<a data-role="button" data-transition="slide" data-rel="back">
            				Voltar
        				</a>
        			</div>
					<div class="ui-block-b">
						<input type="submit" name="btn" value="Negar">
					</div>
					<div class="ui-block-c">
						<input type="submit" name="btn" value="Aprovar">
					</div>
                </div>
                <input type="hidden" name="T005_Id" id="T005_Id" value="{$T005_Id}" />
            </form>    
		</div>
	</div>
	
	 <script>
    	$( document ).undelegate("#pedidoAprovacao", "pageinit");
    	$( document ).delegate("#pedidoAprovacao", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
