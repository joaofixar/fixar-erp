<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

$script = '';

// Ven do pagamentoParcelaEditar
// Vars
$T089_Id = isset($_REQUEST['T089_Id']) ? $_REQUEST['T089_Id'] : false;
$sqlParcela = <<<EOT
	SELECT T089_Id, T089_Numero_Parcela, T089_Prazos_Dias, T089_Data_Vencimento, T089_Valor_Parcela, T089_D027_Id, D027_Portador, D027_Flag_Tipo FROM T089 left join D027 on D027_Id=T089_D027_Id WHERE T089_Id = '{$T089_Id}' LIMIT 0,100;
EOT;
$resParcela = mysql_query($sqlParcela);
$rowParcela = mysql_fetch_assoc($resParcela);
$rowParcela['T089_Data_Vencimento'] = gCorrigeData($rowParcela['T089_Data_Vencimento']);
$rowParcela['T089_Valor_Parcela'] = $rowParcela['T089_Valor_Parcela'];
$valorAntigo=$rowParcela['T089_Valor_Parcela'];
if(isset($_REQUEST['alterarParcela'])) {
	$campoAlterado = $_REQUEST['campoAlterado'];	
	$valueCampo = $_REQUEST[$campoAlterado];
	if($campoAlterado == "T089_Valor_Parcela"){
		$valueCampoUpdate = $valueCampo;
	}elseif($campoAlterado == "T089_Data_Vencimento"){
		$valueCampoUpdate = gCorrigeDataInverte($valueCampo);
	}else{
		$valueCampoUpdate = $valueCampo;
	}
	
	$sqlUpdateParcela = "UPDATE T089 SET {$campoAlterado}='{$valueCampoUpdate}' WHERE T089_Id='{$T089_Id}'";
	$resultUpdateParcela = mysql_query($sqlUpdateParcela);
	$retorno = mysql_error();

	if($campoAlterado == "T089_Valor_Parcela"){

		$query = "SELECT * FROM T089 WHERE T089_Id='{$T089_Id}'";
		$retorno = mysql_query($query);
		$linha = mysql_fetch_assoc($retorno);
		$T003_Id = $linha['T089_T003_Id'];
		$numParcela = $linha['T089_Numero_Parcela'];
	
		/* total antigo, pegando o valor do orçamento
		$query = "SELECT T003_Valor_Total FROM T003 WHERE T003_Id='{$T003_Id}'";
		$retorno=mysql_query($query);		
		$linha = mysql_fetch_assoc($retorno);	
		$valorTotal=$linha['T003_Valor_Total'];
		*/
		
		// pega o valor total com juros (se houver)
		// sem a parcela alterada, e no fim soma com o valor antigo, para pegar o valor total com juros original
		$query = "SELECT SUM(T089_Valor_Parcela) FROM T089 WHERE T089_T003_Id='{$T003_Id}' AND T089_Id!='{$T089_Id}'";
		$retorno=mysql_query($query);		
		$linha = mysql_fetch_assoc($retorno);	
		$valorTotal=$linha['SUM(T089_Valor_Parcela)']+$valorAntigo;
		
		$query = "SELECT SUM(T089_Valor_Parcela) FROM T089 WHERE T089_T003_Id='{$T003_Id}' AND T089_Numero_Parcela<='{$numParcela}'";
		$retorno=mysql_query($query);		
		$linha = mysql_fetch_assoc($retorno);	
		$valorParcelasRestantes=$linha['SUM(T089_Valor_Parcela)'];
		$valorRestante=$valorTotal-$valorParcelasRestantes;
		
		$query = "SELECT * FROM T089 WHERE T089_T003_Id='{$T003_Id}' AND T089_Numero_Parcela>'{$numParcela}'";
		$retorno=mysql_query($query);
		$parcelasRestantes=mysql_num_rows($retorno);	
		//se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
		if ($parcelasRestantes!=0){
			$valorParcelasRestantes=$valorRestante/$parcelasRestantes;
			if ($valorParcelasRestantes<0){
				$query = "UPDATE T089 SET T089_Valor_Parcela='{$valorAntigo}' WHERE T089_Id='{$T089_Id}'";
				mysql_query($query);
				$uniqId = uniqid();
				$script .= <<<EOT
					$.mobile.changePage( './pagamentoGrid.php?T089_Id={$T089_Id}&T003_Id={$T003_Id}&ajax={$uniqId}', { transition: 'fade'} );
					alert('O valor inserido irá gerar parcelas negativas... Favor alterar novamente');
EOT;
					unset($_REQUEST);
			}else{
				while ($linha = mysql_fetch_assoc($retorno)){
					$query = "UPDATE T089 SET T089_Valor_Parcela='{$valorParcelasRestantes}' WHERE T089_Id='{$linha['T089_Id']}'";
					mysql_query($query);						
				}
			}
		}
	}

	if (!empty($retorno)) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	} else {
		// atualizarParcelas	
		$T089_Id 		= $T089_Id;
		$novoValor 		= $valueCampo;
		if ($campoAlterado == 'T089_Prazos_Dias') {
			$campoEditado 	= 'T089_Prazos_Dias';
			$valorAntigo 	= $rowParcela['T089_Prazos_Dias'];
		} else if ($campoAlterado == 'T089_Data_Vencimento') {
			$campoEditado 	= 'T089_Data_Vencimento';
			$valorAntigo 	= $rowParcela['T089_Data_Vencimento'];
		} else if ($campoAlterado == 'T089_Valor_Parcela') {
			$campoEditado 	= 'T089_Valor_Parcela';
			$valorAntigo 	= gCorrigeNumero($rowParcela['T089_Valor_Parcela']);
			$novoValor 		= gCorrigeNumero($novoValor); 
		} else {
			// T089_D027_Id
		}	
		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
		$retorno = $VEN001->ven001AtualizarParcelasGrid($T089_Id, $campoEditado, $valorAntigo, $novoValor);
			
		if ($retorno !== true) {
			$retorno = str_replace("<br>", " - ", $retorno);
			if ($campoAlterado == 'T089_Prazos_Dias') {
				$script .= <<<EOT
					$.mobile.changePage( './pagamentoParcelaEditar.php?T089_Id={$T089_Id}&prazosDias=true&T003_Id={$T003_Id}', { transition: 'fade'} );
					alert('{$retorno}');
EOT;
			} else if ($campoAlterado == 'T089_Data_Vencimento') {
				$script .= <<<EOT
					$.mobile.changePage( './pagamentoParcelaEditar.php?T089_Id={$T089_Id}&dataVencimento=true&T003_Id={$T003_Id}', { transition: 'fade'} );
					alert('{$retorno}');
EOT;
			} else if ($campoAlterado == 'T089_Valor_Parcela') {
				$script .= <<<EOT
					$.mobile.changePage( './pagamentoParcelaEditar.php?T089_Id={$T089_Id}&valorParcela=true&T003_Id={$T003_Id}', { transition: 'fade'} );
					alert('{$retorno}');
EOT;
			}
		}
	}
}

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagePagamentoGrid">
EOT;
$etapa = 4;
include('inc.header.bar.php');
include('inc.header.page.php');
if ($_REQUEST['inserirParcela'] == true) {
	//Define valor do campo 1° Venc. Dias e 1° Venc. Data
	if(($T003['T003_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($T003['T003_Pgto_Primeiro_Vcto_Valor']))){
		if($T003['T003_Pgto_Primeiro_Vcto_Tipo'] == "Dias"){
			$form['primeiroVencimentoNum'] = $T003['T003_Pgto_Primeiro_Vcto_Valor'];
		}else{
			$form['primeiroVencimentoData'] = $T003['T003_Pgto_Primeiro_Vcto_Valor'];
		}
	}else{
		$primeiroVctoValorDias = $g['conf']->camposValorPadrao("primeiroVencimentoNum");
	}
	//Define valor do campo Prox Venc Intervalo e Prox Venc Data Fixa
	if(($T003['T003_Pgto_Dias_Vcto_Valor'] != "") || (!empty($T003['T003_Pgto_Dias_Vcto_Valor']))){
		if($T003['T003_Pgto_Dias_Vcto_Tipo'] == "Intervalo"){
			$form['IntervalorDias'] = $T003['T003_Pgto_Dias_Vcto_Valor'];
		}else{
			$form['diaFixo'] = $T003['T003_Pgto_Dias_Vcto_Valor'];
		}
	}
    $form['T003_Id']=$T003['T003_Id'];
	$form['D027_Id']=$T003['T003_Pgto_Portador'];
	$form['Total_Parcelas']=$T003['T003_Pgto_Parcelas'];

	require_once("bibliotecas/classes/VEN001.php");
	$VEN001 = new VEN001();
	$retorno = $VEN001->inserirParcela($form);
	if ($retorno !== true) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	}
}
	
echo <<<EOT
<div data-role="content">
	            <div data-role="navbar" data-iconpos="top">
					<ul>
            			<li>
                			<a data-theme="" data-icon="" onclick="\$.mobile.changePage( './pagamento.php?T003_Id={$T003_Id}', { transition: 'fade'} );">
                    			Formulário
		                    </a>
		                </li>
		                <li>
		                    <a data-theme="" data-icon="" class="ui-btn-active">
		                        Pagamentos
		                    </a>
		                </li>
		            </ul>
		        </div>
		        <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a href="./pagamentoGrid.php?T003_Id={$T003_Id}&inserirParcela=true" data-ajax="false" data-transition="fade"><input value="Incluir Parcela" data-inline="true" type="button" data-transition="fade" /></a>
                    </div>
                </div>

				<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%;">
	    					<tr>
	    						<td style="width: 10%;" align=center>Parcela</td>
	    						<td style="width: 20%;" align=center>Prazo</td>
	    						<td style="width: 20%;" align=center>Vencimento</td>
	    						<td style="width: 25%;" align=center>Valor R$</td>
	    						<td style="width: 25%;" align=center>Portador</td>
	    					</tr>
	    				</table>
                    </li>
EOT;
   $sql = <<<EOT
	SELECT T089_Id, T089_Numero_Parcela, T089_Prazos_Dias, T089_Data_Vencimento, T089_Valor_Parcela, T089_D027_Id, D027_Portador, D027_Flag_Tipo FROM T089 left join D027 on D027_Id=T089_D027_Id WHERE T089_T003_Id = '{$T003_Id}' LIMIT 0,100;
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$row['T089_Valor_Parcela'] = gCorrigeNumero($row['T089_Valor_Parcela']);
	$row['T089_Data_Vencimento'] = gCorrigeData($row['T089_Data_Vencimento']);
	echo <<<EOT
		<li data-theme="c">
	    <a data-transition="slide" data-ajax="false" style="padding-top:0px;padding-bottom:0px;">
	    	<table style="width: 100%;">
	    		<tr>
	    			<td style="width: 10%;" align=center>{$row['T089_Numero_Parcela']}</td>
	    			<td style="width: 20%;" onclick="\$.mobile.changePage( './pagamentoParcelaEditar.php?T089_Id={$row['T089_Id']}&prazosDias=true&T003_Id={$T003_Id}', { transition: 'fade'} );"><input type="button" value="{$row['T089_Prazos_Dias']}"></input></td>
	    			<td style="width: 20%;" onclick="\$.mobile.changePage( './pagamentoParcelaEditar.php?T089_Id={$row['T089_Id']}&dataVencimento=true&T003_Id={$T003_Id}', { transition: 'fade'} );"><input type="button" value="{$row['T089_Data_Vencimento']}"></input></td>
	    			<td style="width: 25%;" onclick="\$.mobile.changePage( './pagamentoParcelaEditar.php?T089_Id={$row['T089_Id']}&valorParcela=true&T003_Id={$T003_Id}', { transition: 'fade'} );"><input type="button" value="{$row['T089_Valor_Parcela']}"></input></td>
	    			<td style="width: 25%;" onclick="\$.mobile.changePage( './pagamentoParcelaEditar.php?T089_Id={$row['T089_Id']}&Portador=true&T003_Id={$T003_Id}', { transition: 'fade'} );"><input type="button" value="{$row['D027_Portador']}"></input></td>
	    		</tr>
	    	</table>
	    </a>
	    </li>
EOT;
}
echo <<<EOT
                </ul>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pagePagamentoGrid", "pageinit");
    	$( document ).delegate("#pagePagamentoGrid", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
