<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

$script = '';

// Ven do pagamentoParcelaEditar
// Vars
$T090_Id = isset($_REQUEST['T090_Id']) ? $_REQUEST['T090_Id'] : false;
$sqlParcela = <<<EOT
	SELECT T090_Id, T090_Numero_Parcela, T090_Prazos_Dias, T090_Data_Vencimento, T090_Valor_Parcela, T090_D027_Id, D027_Portador, D027_Flag_Tipo FROM T090 left join D027 on D027_Id=T090_D027_Id WHERE T090_Id = '{$T090_Id}' LIMIT 0,100;
EOT;
$resParcela = mysql_query($sqlParcela);
$rowParcela = mysql_fetch_assoc($resParcela);
$rowParcela['T090_Data_Vencimento'] = gCorrigeData($rowParcela['T090_Data_Vencimento']);
$valorAntigo=$rowParcela['T090_Valor_Parcela'];

if(isset($_REQUEST['alterarParcela'])) {
	$campoAlterado = $_REQUEST['campoAlterado'];	
	$valueCampo = $_REQUEST[$campoAlterado];
	if($campoAlterado == "T090_Valor_Parcela"){
		$valueCampoUpdate = $valueCampo;
	}elseif($campoAlterado == "T090_Data_Vencimento"){
		$valueCampoUpdate = gCorrigeDataInverte($valueCampo);
	}else{
		$valueCampoUpdate = $valueCampo;
	}
	
	$sqlUpdateParcela = "UPDATE T090 SET {$campoAlterado}='{$valueCampoUpdate}' WHERE T090_Id='{$T090_Id}'";
	$resultUpdateParcela = mysql_query($sqlUpdateParcela);
	$retorno = mysql_error();

	if($campoAlterado == "T090_Valor_Parcela"){
		$query = "SELECT * FROM T090 WHERE T090_Id='{$T090_Id}'";
		$retorno = mysql_query($query);
		$linha = mysql_fetch_assoc($retorno);
		$T005_Id = $linha['T090_T005_Id'];
		$numParcela = $linha['T090_Numero_Parcela'];

		/* total antigo, pegando o valor do pedido
		$query = "SELECT T005_Valor_Total FROM T005 WHERE T005_Id='{$T005_Id}'";
		$retorno=mysql_query($query);		
		$linha = mysql_fetch_assoc($retorno);	
		$valorTotal=$linha['T005_Valor_Total'];
		*/
		
		// pega o valor total com juros (se houver)
		// sem a parcela alterada, e no fim soma com o valor antigo, para pegar o valor total com juros original
		$query = "SELECT SUM(T090_Valor_Parcela) FROM T090 WHERE T090_T005_Id='{$T005_Id}' AND T090_Id!='{$T090_Id}'";
		$retorno=mysql_query($query);		
		$linha = mysql_fetch_assoc($retorno);	
		$valorTotal=$linha['SUM(T090_Valor_Parcela)']+$valorAntigo;

		$query = "SELECT SUM(T090_Valor_Parcela) FROM T090 WHERE T090_T005_Id='{$T005_Id}' AND T090_Numero_Parcela<='{$numParcela}'";
		$retorno=mysql_query($query);		
		$linha = mysql_fetch_assoc($retorno);	
		$valorParcelasRestantes=$linha['SUM(T090_Valor_Parcela)'];
		$valorRestante=$valorTotal-$valorParcelasRestantes;
		
		$query = "SELECT * FROM T090 WHERE T090_T005_Id='{$T005_Id}' AND T090_Numero_Parcela>'{$numParcela}'";
		$retorno=mysql_query($query);
		$parcelasRestantes=mysql_num_rows($retorno);	
		//se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
		if ($parcelasRestantes!=0){
			$valorParcelasRestantes=$valorRestante/$parcelasRestantes;
			if ($valorParcelasRestantes<0){
				$query = "UPDATE T090 SET T090_Valor_Parcela='{$valorAntigo}' WHERE T090_Id='{$T090_Id}'";
				mysql_query($query);
				$uniqId = uniqid();
				$script .= <<<EOT
					$.mobile.changePage( './pagamentoGrid.php?T090_Id={$T090_Id}&prazosDias=true&T005_Id={$T005_Id}', { transition: 'fade'} );
					alert('O valor inserido irá gerar parcelas negativas... Favor alterar novamente');
EOT;
				unset($_REQUEST);
			}else{
				while ($linha = mysql_fetch_assoc($retorno)){
					$query = "UPDATE T090 SET T090_Valor_Parcela='{$valorParcelasRestantes}' WHERE T090_Id='{$linha['T090_Id']}'";
					mysql_query($query);						
				}
			}
		}
	}
	
	if (!empty($retorno)) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	} else {
		// atualizarParcelas	
		$T090_Id 		= $T090_Id;
		$novoValor 		= $valueCampo;
		if ($campoAlterado == 'T090_Prazos_Dias') {
			$campoEditado 	= 'T090_Prazos_Dias';
			$valorAntigo 	= $rowParcela['T090_Prazos_Dias'];
		} else if ($campoAlterado == 'T090_Data_Vencimento') {
			$campoEditado 	= 'T090_Data_Vencimento';
			$valorAntigo 	= $rowParcela['T090_Data_Vencimento'];
		} else if ($campoAlterado == 'T090_Valor_Parcela') {
			$campoEditado 	= 'T090_Valor_Parcela';
			$valorAntigo 	= gCorrigeNumero($rowParcela['T090_Valor_Parcela']);
			$novoValor 		= gCorrigeNumero($novoValor); 
		} else {
			// T090_D027_Id
		}	
		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
		$retorno = $VEN001->ven001AtualizarParcelasGrid($T090_Id, $campoEditado, $valorAntigo, $novoValor);
			
		if ($retorno !== true) {
			$retorno = str_replace("<br>", " - ", $retorno);
			if ($campoAlterado == 'T090_Prazos_Dias') {
				$script .= <<<EOT
					$.mobile.changePage( './pagamentoParcelaEditar.php?T090_Id={$T090_Id}&prazosDias=true&T005_Id={$T005_Id}', { transition: 'fade'} );
					alert('{$retorno}');
EOT;
			} else if ($campoAlterado == 'T090_Data_Vencimento') {
				$script .= <<<EOT
					$.mobile.changePage( './pagamentoParcelaEditar.php?T090_Id={$T090_Id}&dataVencimento=true&T005_Id={$T005_Id}', { transition: 'fade'} );
					alert('{$retorno}');
EOT;
			} else if ($campoAlterado == 'T090_Valor_Parcela') {
				$script .= <<<EOT
					$.mobile.changePage( './pagamentoParcelaEditar.php?T090_Id={$T090_Id}&valorParcela=true&T005_Id={$T005_Id}', { transition: 'fade'} );
					alert('{$retorno}');
EOT;
			}
		}
	}
}


include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagePagamentoGrid">
EOT;
$etapa = 4;
include('mobile/phone/inc.header.bar.php');
include('inc.header.page.php');
if ($_REQUEST['inserirParcela'] == true) {
	//Define valor do campo 1° Venc. Dias e 1° Venc. Data
	if(($T005['T005_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($T005['T005_Pgto_Primeiro_Vcto_Valor']))){
		if($T005['T005_Pgto_Primeiro_Vcto_Tipo'] == "Dias"){
			$form['primeiroVencimentoNum'] = $T005['T005_Pgto_Primeiro_Vcto_Valor'];
		}else{
			$form['primeiroVencimentoData'] = $T005['T005_Pgto_Primeiro_Vcto_Valor'];
		}
	}else{
		$primeiroVctoValorDias = $g['conf']->camposValorPadrao("primeiroVencimentoNum");
	}
	//Define valor do campo Prox Venc Intervalo e Prox Venc Data Fixa
	if(($T005['T005_Pgto_Dias_Vcto_Valor'] != "") || (!empty($T005['T005_Pgto_Dias_Vcto_Valor']))){
		if($T005['T005_Pgto_Dias_Vcto_Tipo'] == "Intervalo"){
			$form['IntervalorDias'] = $T005['T005_Pgto_Dias_Vcto_Valor'];
		}else{
			$form['diaFixo'] = $T005['T005_Pgto_Dias_Vcto_Valor'];
		}
	}
    $form['T005_Id']=$T005['T005_Id'];
	$form['D027_Id']=$T005['T005_Pgto_Portador'];
	$form['Total_Parcelas']=$T005['T005_Pgto_Parcelas'];

	require_once("bibliotecas/classes/VEN001.php");
	$VEN001 = new VEN001();
	$retorno = $VEN001->inserirParcela($form);
	if ($retorno !== true) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	}
}

//Restrição alterar pedido
$restricao = ($g['conf']->restricaoTravar('alterar', 'T005_Id', $T005_Id) === true && isset($g['c029Ids'][213])) ? '' : 'disabled="disabled"';
$href = ($restricao == '') ? "./pagamentoGrid.php?T005_Id={$T005_Id}{$acao}&inserirParcela=true" : '';
$opacity = ($restricao == '') ? '' : 'style="opacity: 0.5"';
echo <<<EOT
<div data-role="content">
	            <div data-role="navbar" data-iconpos="top">
					<ul>
            			<li>
                			<a data-theme="" data-icon="" onclick="\$.mobile.changePage( './pagamento.php?T005_Id={$T005_Id}{$acao}', { transition: 'fade'} );">
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
                        <a href="{$href}" data-ajax="false" data-transition="fade" {$opacity}><input value="Incluir Parcela" data-inline="true" type="button" data-transition="fade" /></a>
                    </div>
                </div>

				<ul data-role="listview" style="margin-top: 1px;">
EOT;
   $sql = <<<EOT
	SELECT T090_Id, T090_Numero_Parcela, T090_Prazos_Dias, T090_Data_Vencimento, T090_Valor_Parcela, T090_D027_Id, D027_Portador, D027_Flag_Tipo FROM T090 left join D027 on D027_Id=T090_D027_Id WHERE T090_T005_Id = '{$T005_Id}' LIMIT 0,100;
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$row['T090_Valor_Parcela'] = gCorrigeNumero($row['T090_Valor_Parcela']);
	$row['T090_Data_Vencimento'] = gCorrigeData($row['T090_Data_Vencimento']);
	$href1 = ($restricao == '') ? "\$.mobile.changePage( './pagamentoParcelaEditar.php?T090_Id={$row['T090_Id']}&prazosDias=true&T005_Id={$T005_Id}{$acao}', { transition: 'fade'} );" : '';
	$href2 = ($restricao == '') ? "\$.mobile.changePage( './pagamentoParcelaEditar.php?T090_Id={$row['T090_Id']}&dataVencimento=true&T005_Id={$T005_Id}{$acao}', { transition: 'fade'} );" : '';
	$href3 = ($restricao == '') ? "\$.mobile.changePage( './pagamentoParcelaEditar.php?T090_Id={$row['T090_Id']}&valorParcela=true&T005_Id={$T005_Id}{$acao}', { transition: 'fade'} );" : '';
	$href4 = ($restricao == '') ? "\$.mobile.changePage( './pagamentoParcelaEditar.php?T090_Id={$row['T090_Id']}&Portador=true&T005_Id={$T005_Id}{$acao}', { transition: 'fade'} );" : '';
	$opacity = ($restricao == '') ? '' : 'style="opacity: 0.5"';
	echo <<<EOT
		<li data-theme="c">
	    	<div style="text-align: center;">Parcela: {$row['T090_Numero_Parcela']}</div>
	    	<div class="ui-grid-a" style="margin-bottom: -6px;">
	    		<div class="ui-block-a">
	    			<span onclick="{$href1}" {$opacity}><input type="button" value="{$row['T090_Prazos_Dias']}"></input></span>
	    		</div>
	    		<div class="ui-block-b">
	    			<span onclick="{$href2}" {$opacity}><input type="button" value="{$row['T090_Data_Vencimento']}"></input></span>
	    		</div>
	    	</div>
    		<span onclick="{$href3}" {$opacity}><input type="button" value="{$row['T090_Valor_Parcela']}"></input></span>
    		<span onclick="{$href4}" {$opacity}><input type="button" value="{$row['D027_Portador']}"></input></span>
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

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
