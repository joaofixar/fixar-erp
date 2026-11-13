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
	//$form['T003_Liberou_Alcada_Observacao_Nao_Liberado']=$_REQUEST['T003_Liberou_Alcada_Observacao_Nao_Liberado'];
	$form['T003_Liberou_Alcada_Observacao_Nao_Liberado']="Orçamento Negado via Celular";	
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
	<div data-role="dialog" id="orcamentoAprovacao">
		<div data-role="header" data-theme="d">
			<h1>Análise de Orçamento</h1>
		</div>
		<div data-role="content" data-theme="c">
		<script>
			{$script}	
		</script>
			{$temErro}
			<form>
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 64%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T003_Liberou_Alcada_Observacao">
	                                Observação: 
	                            </label>
	                            	<textarea name="T003_Liberou_Alcada_Observacao" id="T003_Liberou_Alcada_Observacao" value="" style="width:150%;height:100px;">{$T003['T003_Liberou_Alcada_Observacao_Nao_Liberado']}</textarea>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
        			<div>
	                    <input data-role="button" data-icon="check" type="button" name="btn" id="btnAprovar" value="Aprovar" onclick="$.mobile.changePage( './orcamentoAprovacao.php?T003_Id={$T003_Id}&refresh={$uniqId}&btn=Aprovar&T003_Liberou_Alcada_Observacao_Nao_Liberado=' + encodeURIComponent($('#T003_Liberou_Alcada_Observacao').val()), { transition: 'fade'} );">
                	</div>
                	<div>
	                    <input data-role="button" data-icon="delete" type="button" name="btn" id="btnNegar" value="Negar" onclick="$.mobile.changePage( './orcamentoAprovacao.php?T003_Id={$T003_Id}&refresh={$uniqId}&btn=Negar&T003_Liberou_Alcada_Observacao_Nao_Liberado=' + encodeURIComponent($('#T003_Liberou_Alcada_Observacao').val()), { transition: 'fade'} );">
                	</div>
                	<div>
						<a data-role="button" data-icon="back" data-transition="slide" onclick="$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );">
            				Voltar
        				</a>
        			</div>
                </div>
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
