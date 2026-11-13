<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageOrcamentoPagamento">
EOT;

//Define opções do combobox de Parcelas
// pega as configurações de juros da empresa atual
	$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
	$mC004=mysql_fetch_array($C004);
	$tipoJuros=$mC004['C004_Tipo_Juros'];
	$taxaJuros=$mC004['C004_Taxa_Juros'];
	$parcelasIsentas=$mC004['C004_Parcelas_Isentas_Juros'];						
	$T005=mysql_query("select * from T005 left join D027 on D027_Id=T005_Pgto_Portador where T005_Id='{$T005_Id}'");
	$mT005=mysql_fetch_array($T005);	
	
	//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
	$sql = "SELECT * FROM T147 where T147_T005_Id='{$T005_Id}'";
	$T147_sql = mysql_query($sql);
	
	// LOOPING PARA CALCULAR O TOTAL DE SERVIÇOS CONFIRMADOS PARA ESSE ORÇAMENTO
	$totalServicos=0;
	while($T147 = mysql_fetch_array($T147_sql)){
		$totalServicos+=$T147['T147_Quantidade_Confirmada'] * $T147['T147_Valor_Unitario'];
	}
	
	//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
	$valorTotalOrcamento = $totalServicos + $mT005['T005_Valor_Total'];
	$qtdeParcelas .= "<option value=\"0\">
							 A Combinar
						 </option>";
	for ($i=1; $i<=24; $i++) {
		$valorParcela=$VEN001->calcularParcelaCombo($i,$valorTotalOrcamento,$tipoJuros,$taxaJuros,$parcelasIsentas);				
		if ($i>$parcelasIsentas){
			$valorTotal=gCorrigeNumero($i*gCorrigeNumeroInverte($valorParcela));
			$valorParcela.="*";
		}else{
			$valorTotal=gCorrigeNumero($valorTotalOrcamento);
		}
		
	if($i == $mT005['T005_Pgto_Parcelas']){
		$qtdeParcelas .= "<option selected=\"selected\" value=\"{$i}\">
							 {$i} X - {$valorParcela} = {$valorTotal}
						 </option>";
	}else{	
		$qtdeParcelas .= "<option value=\"{$i}\">
							 {$i} X - {$valorParcela} = {$valorTotal}
						 </option>";
		}
	}
	//Define combobox do Portador
	$sqlPortador = mysql_query("SELECT D027_Id,D027_Portador FROM D027 where D027_Flag_Comercial='S' order by D027_Portador");
	$selectPortador = "";
	while($rowPortador = mysql_fetch_array($sqlPortador)){
		if($rowPortador['D027_Id'] == $mT005['T005_Pgto_Portador']){
			$selectPortador .= "<option selected=\"selected\" value=\"{$rowPortador['D027_Id']}\">
								 {$rowPortador['D027_Portador']}
							    </option>";
		}else{
			$selectPortador .= "<option value=\"{$rowPortador['D027_Id']}\">
								 {$rowPortador['D027_Portador']}
							    </option>";
		}
	}
	//Define valor do campo 1° Venc. Dias e 1° Venc. Data
	if(($mT005['T005_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mT005['T005_Pgto_Primeiro_Vcto_Valor']))){
		if($mT005['T005_Pgto_Primeiro_Vcto_Tipo'] == "Dias"){
			$primeiroVctoValorDias = $mT005['T005_Pgto_Primeiro_Vcto_Valor'];
		}else{
			$primeiroVctoValorData = $mT005['T005_Pgto_Primeiro_Vcto_Valor'];
		}
	}else{
		$primeiroVctoValorDias = $g['conf']->camposValorPadrao("primeiroVencimentoNum");
	}
	//Define valor do campo Prox Venc Intervalo e Prox Venc Data Fixa
	if(($mT005['T005_Pgto_Dias_Vcto_Valor'] != "") || (!empty($mT005['T005_Pgto_Dias_Vcto_Valor']))){
		if($mT005['T005_Pgto_Dias_Vcto_Tipo'] == "Intervalo"){
			$diaVctoValorIntervalo = $mT005['T005_Pgto_Dias_Vcto_Valor'];
		}else{
			$diaVctoValorFixo = $mT005['T005_Pgto_Dias_Vcto_Valor'];
		}
	}else{
		$diaVctoValorIntervalo = $g['conf']->camposValorPadrao("IntervalorDias");
	}
	
	
if(isset($_REQUEST['qtdeParcelas'])) {	
	$row['T005_Id'] = $T005_Id;
	$row['Total_Parcelas'] = $_REQUEST['qtdeParcelas'];
	$row['IntervalorDias'] = $_REQUEST['proximoVencIntervalo'];
	$row['diaFixo'] = $_REQUEST['proximoVencDataFixa'];
	$row['primeiroVencimentoNum'] = $_REQUEST['primeiroVencDias'];
	$row['primeiroVencimentoData'] = $_REQUEST['primeiroVencData'];
	$row['Portador_D027_Id'] = $_REQUEST['Portador'];
	$row['selecionaPrimeiroVencimento'] = (empty($_REQUEST['primeiroVencDias'])) ? "Data" : "Dias";
	$row['selecionaDias'] = (empty($_REQUEST['proximoVencDataFixa'])) ? "I" : "F";
	
	$form = new formWrap($row);
	$retorno = $VEN001->VEN001atualizarParcelas($form);
	if ($retorno !== true) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	} else {
		$script = <<<EOT
  			$.mobile.changePage( "./pagamentoGrid.php?T005_Id={$T005_Id}", { transition: 'slide'} );
EOT;
	}
}
	


$etapa = 4;
include('inc.header.bar.php');
echo <<<EOT
	<div data-role="content">
       	<div data-role="navbar" data-iconpos="top">
			<ul>
            	<li>
                	<a data-theme="" data-icon="" class="ui-btn-active">
                    	Formulário
                    </a>
                </li>
                <li>
                    <a data-theme="" data-icon="" onclick="\$.mobile.changePage( './pagamentoGrid.php?T005_Id={$T005_Id}', { transition: 'fade'} );">
                        Pagamentos
                    </a>
                </li>
            </ul>
        </div>
        {$temErro}
                 <form action="./pagamento.php?T005_Id={$T005_Id}" method="post" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                    <div data-role="fieldcontain">
		                    <label for="qtdeParcelas">
		                        Parcelas
		                    </label>
		                    <select data-native-menu="true" name="qtdeParcelas" id="qtdeParcelas">
		                        {$qtdeParcelas}
		                    </select>
		                </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="primeiroVencDias">
	                                Prazo 1° parcela
	                            </label>
	                            <input name="primeiroVencDias" id="primeiroVencDias" value="{$primeiroVctoValorDias }" placeholder="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="primeiroVencData">
	                                Vencimento 1° parcela
	                            </label>
	                            <div style="display: inline-block; width: 60%;">
	                            <input name="primeiroVencData" id="primeiroVencData" value="{$primeiroVctoValorData}" placeholder="" type="date" data-role="datebox" data-options='{"mode": "calbox", "dateFormat": "dd/mm/YYYY"}' />
	                            </div>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="proximoVencIntervalo">
	                                Prazo parcelamento
	                            </label>
	                            <input name="proximoVencIntervalo" id="proximoVencIntervalo" value="{$diaVctoValorIntervalo}" placeholder="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="proximoVencDataFixa">
	                                Dia fixo
	                            </label>
	                            	<input name="proximoVencDataFixa" id="proximoVencDataFixa" value="{$diaVctoValorFixo}" placeholder="" type="text"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
		                    <label for="Portador">
		                        Portador
		                    </label>
		                    <select data-native-menu="true" name="Portador" id="Portador">
		                        {$selectPortador}
		                    </select>
		                </div>
	                    <!--
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="observacaoPagamento">
	                                Observação
	                            </label>
	                            <textarea placeholder="" name="observacaoPagamento" id="observacaoPagamento" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset"></textarea>
	                        </fieldset>
	                    </div>
	                    --->
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <input value="Gerar parcelas" type="submit" />
                    </div>
                </div>
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageOrcamentoPagamento", "pageinit");
    	$( document ).delegate("#pageOrcamentoPagamento", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
