<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('inc.header.php');
require_once('bibliotecas/classes/VEN005.php');
$VEN005 = new VEN005();
$script = "";

if($_REQUEST['btn'] == "Aprovar"){
	$T003_Liberou_Alcada_Observacao_Nao_Liberado=$_REQUEST['T003_Liberou_Alcada_Observacao_Nao_Liberado'];	
	mysql_query("UPDATE T003 SET T003_Liberou_Alcada_Observacao_Nao_Liberado='{$T003_Liberou_Alcada_Observacao_Nao_Liberado}' WHERE T003_Id = '{$T003_Id}'");
	$verifica = $VEN005->ven005AprovarOrcamento($T003_Id,true);
	$uniqId = uniqid();
	
		if ($verifica !== true) {
			$script .="alert('{$verifica}');";
			$script .= "\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}else{
			$script .="alert('Orçamento aprovado com sucesso!');";
			$script .= "\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}
}elseif($_REQUEST['btn'] == "Negar"){
	$form['T003_Liberou_Alcada_Observacao_Nao_Liberado']=$_REQUEST['T003_Liberou_Alcada_Observacao_Nao_Liberado'];	
	$form['T003_Id']=$_REQUEST['T003_Id'];
	$form = new formWrap($form);	
	$verifica = $VEN005->ven005NaoLiberarOrcamento($form);
	$uniqId = uniqid();
	
		if ($verifica !== true) {
			$script .="alert('{$verifica}');";
			$script .= "\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}else{
			$script .="alert('Orçamento Negado!');";
			$script .= "\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}
}


echo <<<EOT
	<div data-role="dialog" id="orcamentoAprovacao" data-ajax="false">
		<div data-role="header" data-theme="d">
			<h1>Análise de Orçamento</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form method="POST" action="./orcamentoAprovacao.php?T003_Id={$T003_Id}" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T003_Liberou_Alcada_Observacao_Nao_Liberado">
	                                Observação: 
	                            </label>
	                            	<textarea name="T003_Liberou_Alcada_Observacao_Nao_Liberado" id="T003_Liberou_Alcada_Observacao_Nao_Liberado" value="" style="width:150%;height:100px;">{$T003['T003_Liberou_Alcada_Observacao_Nao_Liberado']}</textarea>
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
                <input type="hidden" id="T003_Id" name="T003_Id" value="{$T003_Id}"/>
            </form>    
		</div>
	</div>
	
	 <script>
    	$( document ).undelegate("#orcamentoAprovacao", "pageinit");
    	$( document ).delegate("#orcamentoAprovacao", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
