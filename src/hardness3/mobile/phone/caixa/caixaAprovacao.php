<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('mobile/tablet/inc.header.php');
require_once('bibliotecas/classes/VEN005.php');
$VEN005 = new VEN005();
$script = "";

if($_REQUEST['btn'] == "Aprovar"){
	$T005_Liberou_Alcada_Observacao_Nao_Liberado=$_REQUEST['T005_Liberou_Alcada_Observacao_Nao_Liberado'];	
	mysql_query("UPDATE T005 SET T005_Liberou_Alcada_Observacao_Nao_Liberado='{$T005_Liberou_Alcada_Observacao_Nao_Liberado}' WHERE T005_Id = '{$T005_Id}'");
	$verifica = $VEN005->ven005Aprovarcaixa($T005_Id,true);
	$uniqId = uniqid();
	
		if ($verifica !== true) {
			$script .="alert('{$verifica}');";
			$script .= "\$.mobile.changePage( './caixa.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}else{
			$script .="alert('caixa aprovado com sucesso!');";
			$script .= "\$.mobile.changePage( './caixa.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}
}elseif($_REQUEST['btn'] == "Negar"){
	$form['T005_Liberou_Alcada_Observacao_Nao_Liberado']=$_REQUEST['T005_Liberou_Alcada_Observacao_Nao_Liberado'];	
	$form['T005_Id']=$_REQUEST['T005_Id'];
	$form = new formWrap($form);	
	$verifica = $VEN005->ven005NaoLiberarcaixa($form);
	$uniqId = uniqid();
	
		if ($verifica !== true) {
			$script .="alert('{$verifica}');";
			$script .= "\$.mobile.changePage( './caixa.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}else{
			$script .="alert('caixa Negado!');";
			$script .= "\$.mobile.changePage( './caixa.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );";
		}
}


echo <<<EOT
	<div data-role="dialog" id="caixaAprovacao" data-ajax="false">
		<div data-role="header" data-theme="d">
			<h1>Análise de caixa</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form method="POST" action="./caixaAprovacao.php?acao=aprovar&T005_Id={$T005_Id}" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T005_Liberou_Alcada_Observacao_Nao_Liberado">
	                                Observação: 
	                            </label>
	                            	<textarea name="T005_Liberou_Alcada_Observacao_Nao_Liberado" id="T005_Liberou_Alcada_Observacao_Nao_Liberado" value="" style="width:150%;height:100px;">{$T005['T005_Liberou_Alcada_Observacao_Nao_Liberado']}</textarea>
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
                	<div class="ui-block-b">
	                    <input type="submit" name="btn" value="Aprovar">
                	</div>
                </div>
            </form>    
		</div>
	</div>
	
	 <script>
    	$( document ).undelegate("#caixaAprovacao", "pageinit");
    	$( document ).delegate("#caixaAprovacao", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
