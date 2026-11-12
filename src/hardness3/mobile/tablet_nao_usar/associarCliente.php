<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="gerarPedido">
EOT;


if(isset($_REQUEST['associar']) == true) {
	$D024_Id=$_REQUEST['D024_Id'];
	$T003_Id=$_REQUEST['T003_Id'];

	$T003=mysql_query("UPDATE T003 SET T003_D024_Id='{$D024_Id}' WHERE T003_Id='{$T003_Id}'");
	if($T003){
		$script =<<<EOT
			alert('Cliente associado com sucesso');
			\$.mobile.changePage( './clientesPreCadastro.php?T003_Id={$T003_Id}&D024_Id={$D024_Id}&update=true', { transition: 'fade'} );
EOT;
	}
}
	


$etapa = 5;
include('inc.header.bar.php');
$D024_Id=$_REQUEST['D024_Id'];
$D024=mysql_query("SELECT D024_Nome_Empresa, D024_Codigo from D024 WHERE D024_Id='{$D024_Id}'");
$mD024=mysql_fetch_array($D024);
$D024_Nome_Empresa = $mD024['D024_Nome_Empresa'];
echo <<<EOT
		<div data-role="content">
			<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
					<h1>Gerar Pedido</h1>
					{$temErro}
					<form action="./associarCliente.php?T003_Id={$T003_Id}&D024_Id={$D024_Id}&associar=true" method="POST" data-ajax="false">
		                 <div class="ui-grid-a">
                 			<div class="ui-block-a" style="width: 60%;">
			                     <div data-role="fieldcontain">
			                        <fieldset data-role="controlgroup">
			                        	Cliente já cadastrado. Deseja associar o cliente {$D024_Codigo} - {$D024_Nome_Empresa} a esta venda?
			                             
			                             <input name="gerarPedido" id="gerarPedido" value="gerarPedido" placeholder="" type="hidden"/>
			                        </fieldset>
			                    </div>
			                </div>
		                </div>
		                <div class="ui-grid-a">
		                    <div class="ui-block-a">
		                        <a data-role="button" data-transition="slide" data-direction="reverse" href="./clientesPreCadastro.php?T003_Id={$T003_Id}">
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
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#gerarPedido", "pageinit");
    	$( document ).delegate("#gerarPedido", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
