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

	mysql_query("update T005 set T005_Flag_Esta_No_Caixa = 'N',T005_Data_Finalizado_Caixa=CURDATE(), T005_Flag_Status = '3', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) where T005_Id='{$T005_Id}'");
	//Insere registros no histórico do caixa (T178)
	mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Pedido finalizado.','')");
	$script .= <<<EOT
		alert('Pedido finalizado!');
		$.mobile.changePage( './caixaLista.php', { transition: 'fade'} );
EOT;
}

$etapa = 5;
include('inc.header.bar.php');

$T005 = mysql_query("SELECT T005_Flag_Status_Servicos FROM T005 WHERE T005_Id = '{$T005_Id}'");
$resT005 = mysql_fetch_array($T005);
if($resT005['T005_Flag_Status_Servicos'] != "2"){
	echo <<<EOT
	<div data-role="content">
		<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
			<h1>Finalizar pedido</h1>
			{$temErro}
		    <div class="ui-grid-a">
     			<div class="ui-block-a" style="width: 60%;">
                     <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
							A Ordem de Serviço deve estar concluida para o pedido ser finalizado.
							<input name="finalizar" id="finalizar" value="finalizar" placeholder="" type="hidden"/>
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
            </div>
		</div>
	</div>
EOT;
} else {

	// Verificando se existe cliente ou item em pré-cadastro
	$T005=mysql_query("SELECT T005_D024_Id from T005 WHERE T005_Id='$T005_Id'");
	$mT005 = mysql_fetch_array($T005);

	$D024=mysql_query("SELECT D024_Flag_Pre_Cadastro FROM D024 WHERE D024_Id='{$mT005['T005_D024_Id']}'");
	$mD024=mysql_fetch_array($D024);

	$D024_Flag_Pre_Cadastro=$mD024['D024_Flag_Pre_Cadastro'];

	$T006=mysql_query("SELECT T006_D009_Id from T006 WHERE T006_T005_Id='{$T005_Id}'");
	$D001_Flag_Pre_Cadastro=1;
	while($mT006 = mysql_fetch_array($T006)){
		$D009=mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D009 LEFT JOIN D049 on D049_Id=D009_D049_Id LEFT JOIN D001 on D001_Id=D049_D001_Id where D009_Id='{$mT006['T006_D009_Id']}'");
		$mD009=mysql_fetch_array($D009);
		if($mD009['D001_Flag_Pre_Cadastro'] == "S"){
			$D001_Flag_Pre_Cadastro=2;
		}
	}
	//if($D024_Flag_Pre_Cadastro == "S" || $D001_Flag_Pre_Cadastro == 2){
	if($D024_Flag_Pre_Cadastro == "S"){
		echo <<<EOT
			<div data-role="content">
				<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
					<h1>Finalizar pedido</h1>
					{$temErro}
				    <div class="ui-grid-a">
	         			<div class="ui-block-a" style="width: 60%;">
		                     <div data-role="fieldcontain">
		                        <fieldset data-role="controlgroup">
		                        	Não é possível finalizar o pedido, o cliente está em pré-cadastro. Por favor, verifique.
		                            <input name="finalizar" id="finalizar" value="finalizar" placeholder="" type="hidden"/>
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
	                </div>	
				</div>
			</div>
EOT;
		die();
	}

	//SE O STATUS DO SERVIÇO FOR IGUAL A 2 É VERIFICADO OS ITENS PENDENTES
	$T207 = mysql_query("SELECT * FROM T207 WHERE T207_T005_Id = '{$T005_Id}' AND (T207_Flag_Status = '5' OR T207_Flag_Status = '3')");
	$rows = mysql_num_rows($T207);
	if($rows == 0){
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
EOT;
								if($_REQUEST['acao'] == caixa){
									echo <<<EOT
			                        <a data-role="button" data-transition="slide" data-direction="reverse" href="../pedido/pedido.php?T005_Id={$T005_Id}{$acao}">
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
			                        <input value="Finalizar" type="submit" />
			                    </div>
			                </div>
			            </form>
				</div>
			</div>
EOT;

	} else {
	echo <<<EOT
			<div data-role="content">
				<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
					<h1>Finalizar pedido</h1>
					{$temErro}
				    <div class="ui-grid-a">
	         			<div class="ui-block-a" style="width: 60%;">
		                     <div data-role="fieldcontain">
		                        <fieldset data-role="controlgroup">
EOT;
										if($rows > 1) { echo 'O pedido não pode ser finalizado, verifique os itens pendentes'; } else { echo 'O pedido não pode ser finalizado, verifique o item pendente'; } 
	echo <<<EOT
		                            <input name="finalizar" id="finalizar" value="finalizar" placeholder="" type="hidden"/>
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
	                </div>	
				</div>
			</div>
EOT;
	}
}
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
