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


if(isset($_REQUEST['gerarPedido'])) {
    // VERIFICA SE O CLIENTE FOI INFORMADO NO ORCAMENTO	
	$T003=mysql_query("select * from T003 where T003_Id='{$T003_Id}'");
	$mT003=mysql_fetch_array($T003);
	
	If ($mT003[T003_D024_Id]<=0)
	{
	   echo <<<EOT
	        <script>
    	       $( document ).undelegate("#gerarPedido", "pageinit");
    	       $( document ).delegate("#gerarPedido", "pageinit", function() {
        	   alert('Você deve informar o Cliente');
	    	   $.mobile.changePage( './clientes.php?T003_Id={$T003_Id}', { transition: 'fade'} );
     	       });
            </script>	
EOT;
       die();
    }
	// FIM

    //Busca o Veículo
    $D116 = mysql_query("select * from D116 left join T003 on T003_D116_Id=D116_Id where T003_Id='{$T003_Id}'");
    $mD116 = mysql_fetch_assoc($D116);
    
    // VERIFICA CASO TENHA SERVIÇO NO ORÇMANETO E VEÍCULO SELECIONADO, SE FOI INCLUIDO OS DADOS DE HORA INICIO E HORA FIM - EM OBSERVACAO E PREVISÃO ENTREGA VEÍCULO
	$T145=mysql_query("select * from T145 left join T003 on T145_T003_Id=T003_Id where T003_Id='{$T003_Id}'");
	//$mT145=mysql_fetch_array($T145);
    If (mysql_num_rows($T145)>0 && mysql_num_rows($D116)>0 && (empty($mT003['T003_Hora_Inicio_Servico']) || $mT003['T003_Hora_Inicio_Servico']<=0 || empty($mT003['T003_Hora_Fim_Servico']) || $mT003['T003_Hora_Fim_Servico']<=0))
	{
		//Verifica se todos os dados do veículo estão preenchidos
		if(empty($mD116['D116_Modelo']) || empty($mD116['D116_Cor']) || empty($mD116['D116_Placa'])){
			echo <<<EOT
	        <script>
    	       $( document ).undelegate("#gerarPedido", "pageinit");
    	       $( document ).delegate("#gerarPedido", "pageinit", function() {
        	   alert('Você deve informar a Placa, Modelo e Cor do Veículo!');
	    	   $.mobile.changePage( './clientes.php?T003_Id={$T003_Id}', { transition: 'fade'} );
     	       });
            </script>	
EOT;
      		die();
		}else{
	  		echo <<<EOT
	        <script>
    	       $( document ).undelegate("#gerarPedido", "pageinit");
    	       $( document ).delegate("#gerarPedido", "pageinit", function() {
        	   alert('Você deve informar a Hora Inicio e Hora Fim, em Observação e Previsão Entrega Veículo');
	    	   $.mobile.changePage( './clientes.php?T003_Id={$T003_Id}', { transition: 'fade'} );
     	       });
            </script>	
EOT;
       		die();
		}
    }
	// FIM
	
	//Confirma todos os produtos
	$sqlConfirmarTudo = <<<EOT
 				update T004 set T004_Quantidade_Confirmada=T004_Quantidade where T004_T003_Id = '{$T003_Id}';
EOT;
    
	$respostaConfirmarTudo = mysql_query($sqlConfirmarTudo);
	
	//Gera o Pedido
	require_once("bibliotecas/classes/VEN001.php");
	
	$VEN001 = new VEN001();
	$T005_Id = false;
	$retorno = $VEN001->gerarPedido($T003_Id);
	
	if ($retorno !== true) {
		$retorno = str_replace("<br>", " - ", $retorno);
		$script .= <<<EOT
			alert('{$retorno}');
			$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}', { transition: 'fade'} );
EOT;
	}else{
		/*
		Criado campo T005_Valor_Desconto_Manual, e na geração do pedido foi colocado para este campo copiar o valor da T003_Valor_Desconto_Manual. 
		Criada tbm procecdure T005_Gravar_Totalizacao_3.sql em outro commit. Para atualizar o campo T005_Valor_Total. 
		*/
		$sql = "UPDATE T005 SET T005_D022_Id = '1',
		                        T005_Valor_Desconto_Manual='{$mT003['T003_Valor_Desconto_Manual']}',
		                        T005_Ordem_Compra='Faturado via Ipad',
		                        T005_Hora_Inicio_Servico='{$mT003['T003_Hora_Inicio_Servico']}',
                                T005_Hora_Fim_Servico='{$mT003['T003_Hora_Fim_Servico']}',
                                T005_Flag_Aguardando_Entrega='{$mT003['T003_Flag_Aguardando_Entrega']}'
		                  WHERE T005_Id = '{$T005_Id}';";
		mysql_query($sql);
		
		//Fatura o Pedido
		require_once("bibliotecas/classes/VEN002.php");
		
		$VEN002 = new VEN002();
		
		$mensagem = 'Pedido gerado com sucesso!';
		//$retorno2 = $VEN002->faturarPedido($T005_Id);
		$VEN002->gerarControleProdutosPedido($T005_Id);
		//---- Mudando status do pedido para branco
		$sql = "UPDATE T005 SET T005_Flag_Status='', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = '{$T005_Id}';";
		mysql_query($sql);
		//----
		/*
		if ($retorno2 !== true) {
			$excluirPedido = $VEN002->excluirPedido($T005_Id);
			$sql = "update T003 set T003_Flag_Status_Orcamento='P' where T003_Id='{$T003_Id}'";
			mysql_query($sql);
			
			$retorno2 = str_replace("<br>", " - ", $retorno2);
			$script .= <<<EOT
				alert('{$retorno2}');
				$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}', { transition: 'fade'} );
EOT;
		}else{
			*/
			$mensagem = str_replace("<br />", " - ", $mensagem);
			$script .= <<<EOT
				$.mobile.changePage( './orcamentoLista.php?T003_Id=last', { transition: 'fade'} );
				alert('{$mensagem}');
EOT;
		//}
	}
}
	


$etapa = 5;
include('inc.header.bar.php');
echo <<<EOT
		<div data-role="content">
			<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
					<h1>Gerar Pedido</h1>
					{$temErro}
					<form action="./gerarPedido.php?T003_Id={$T003_Id}&gerarPedido=true" method="POST" data-ajax="false">
		                 <div class="ui-grid-a">
                 			<div class="ui-block-a" style="width: 60%;">
			                     <div data-role="fieldcontain">
			                        <fieldset data-role="controlgroup">
			                             Deseja gerar pedido para o orçamento {$T003_Id}?
			                             <input name="gerarPedido" id="gerarPedido" value="gerarPedido" placeholder="" type="hidden"/>
			                        </fieldset>
			                    </div>
			                </div>
		                </div>
		                <div class="ui-grid-a">
		                    <div class="ui-block-a">
		                        <a data-role="button" data-transition="slide" data-direction="reverse" href="./pagamento.php?T003_Id={$T003_Id}">
		                            Voltar
		                        </a>
		                    </div>
		                    <div class="ui-block-b">
		                        <input value="Gerar" type="submit" />
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
