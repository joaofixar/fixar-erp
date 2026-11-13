<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="enviarCaixa">
EOT;

if(isset($_REQUEST['Reabrir'])) {
	$T005_Id = $_REQUEST['T005_Id'];	

	mysql_query("update T005 set T005_Flag_Esta_No_Caixa = 'S', T005_Flag_Status_Servicos = '1', T005_Data_Finalizado_Caixa=CURDATE(), T005_Flag_Status = '0', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) where T005_Id='{$T005_Id}'");
	//Insere registros no histórico do caixa (T178)
	mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Pedido reaberto.','')");
	$script .= <<<EOT
		alert('Pedido reaberto!');
		$.mobile.changePage( './caixaLista.php', { transition: 'fade'} );
EOT;
}

$etapa = 5;
include('inc.header.bar.php');

	echo <<<EOT
		<div data-role="content">
			<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
					<h1>Reabrir pedido</h1>
					<form action="./reabrir.php?T005_Id={$T005_Id}&Reabrir=S" method="POST" data-ajax="false">
		                 <div class="ui-grid-a">
                 			<div class="ui-block-a" style="width: 60%;">
			                     <div data-role="fieldcontain">
			                        <fieldset data-role="controlgroup">
			                             Deseja reabrir o pedido {$T005_Id}?
			                             <input name="reabrir" id="reabrir" value="reabrir" placeholder="" type="hidden"/>
			                        </fieldset>
			                    </div>
			                </div>
		                </div>
		                <div class="ui-grid-a">
	    	                <div class="ui-block-a">
EOT;
							if($_REQUEST['acao'] == caixa){
								echo <<<EOT
		                        <a data-role="button" data-transition="slide" data-direction="reverse" href="./caixa.php?T005_Id={$T005_Id}{$acao}">
		                            Voltar
		                        </a>
EOT;
							} else {
								echo <<<EOT
			                        <a data-role="button" data-transition="slide" data-direction="reverse" href="./caixa.php?T005_Id={$T005_Id}">
			                            Voltar
		                        	</a>
EOT;
								}		
echo <<<EOT
		                    </div>
	                    <div class="ui-block-b">
	                        <input value="Reabrir" type="submit" />
	                    </div>
	                </div>
	            </form>
			</div>
		</div>
EOT;

include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#enviarCaixa", "pageinit");
    	$( document ).delegate("#enviarCaixa", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
