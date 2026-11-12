<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="enviarCaixa">
EOT;


if(isset($_REQUEST['Finalizar'])) {
	$T005_Id = $_REQUEST['T005_Id'];
	mysql_query("update T005 set T005_FLAG_ESTA_NO_CAIXA = 'N'  where T005_Id='$T005_Id'");
	//Insere registros no histórico do caixa (T178)
    mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Pedido finalizado.','')");
    
	$script .= <<<EOT
		alert('Pedido finalizado!');
		$.mobile.changePage( './caixaLista.php', { transition: 'fade'} );
EOT;
}

$etapa = 5;
include('inc.header.bar.php');
echo <<<EOT
		<div data-role="content">
			<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
					<h1>Finalizar pedido</h1>
					{$temErro}
					<form action="./finalizar.php?T005_Id={$T005_Id}&Finalizar=S" method="POST" data-ajax="false">
		                 <div class="ui-grid-a">
                 			<div class="ui-block-a" style="width: 60%;">
			                     <div data-role="fieldcontain">
			                        <fieldset data-role="controlgroup">
			                             Deseja finalizar o pedido {$T005_Id}?
			                             <input name="finalizar" id="finalizar" value="finalizar" placeholder="" type="hidden"/>
			                        </fieldset>
			                    </div>
			                </div>
		                </div>
		                <div class="ui-grid-a">
		                    <div class="ui-block-a">
		                        <a data-role="button" data-transition="slide" data-direction="reverse" href="./caixa.php?T005_Id={$T005_Id}">
		                            Voltar
		                        </a>
		                    </div>
		                    <div class="ui-block-b">
		                        <input value="Finalizar" type="submit" />
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
