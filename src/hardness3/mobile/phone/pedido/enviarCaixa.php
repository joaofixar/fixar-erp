<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="enviarCaixa">
EOT;


if(isset($_REQUEST['enviarCaixa'])) {
	$T005_Id = $_REQUEST['T005_Id'];
	
	mysql_query("update T005 set T005_Flag_Almoxarifado='S',
			T005_Flag_Travar_Pedido=0,
			T005_Flag_Status=3,
			T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),
			T005_Flag_Reenviado_Almoxarifado='N',
			T005_Flag_Esta_No_Caixa='S'
			where T005_Id='$T005_Id'");
	//Insere registros no histórico do pedido (T178)
    mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Pedido re-enviado ao caixa.','')");
    
	$script .= <<<EOT
		alert('Pedido enviado ao caixa!');
		$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}', { transition: 'fade'} );
EOT;
}

$etapa = 5;
include('inc.header.bar.php');
echo <<<EOT
		<div data-role="content">
			<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
					<h1>Re-enviar para Caixa</h1>
					{$temErro}
					<form action="./enviarCaixa.php?T005_Id={$T005_Id}&enviarCaixa=true" method="POST" data-ajax="false">
		                 <div class="ui-grid-a">
                 			<div class="ui-block-a" style="width: 60%;">
			                     <div data-role="fieldcontain">
			                        <fieldset data-role="controlgroup">
			                             Deseja re-enviar o pedido {$T005_Id} ao caixa?
			                             <input name="enviarCaixa" id="enviarCaixa" value="enviarCaixa" placeholder="" type="hidden"/>
			                        </fieldset>
			                    </div>
			                </div>
		                </div>
		                <div class="ui-grid-a">
		                    <div class="ui-block-a">
		                        <a data-role="button" data-transition="slide" data-direction="reverse" href="./pagamento.php?T005_Id={$T005_Id}">
		                            Voltar
		                        </a>
		                    </div>
		                    <div class="ui-block-b">
		                        <input value="Enviar" type="submit" />
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
