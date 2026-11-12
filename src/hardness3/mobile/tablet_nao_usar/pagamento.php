<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageOrcamentoPagamento">
EOT;

//Define opções do combobox de Parcelas
// pega as configurações de juros da empresa atual
$C004=mysql_query("SELECT * FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
$mC004=mysql_fetch_array($C004);
$CNPJ_Empresa_Corrente = substr($mC004['C004_Cnpj'], 0, -8);
$tipoJuros=$mC004['C004_Tipo_Juros'];
$taxaJuros=$mC004['C004_Taxa_Juros'];
$parcelasIsentas=$mC004['C004_Parcelas_Isentas_Juros'];
$sqlT003=mysql_query("SELECT * FROM T003 LEFT JOIN D024 ON D024_Id=T003_D024_Id LEFT JOIN D027 ON D027_Id=D024_D027_Id WHERE T003_Id='{$T003_Id}'");
$mT003=mysql_fetch_array($sqlT003);

if($mT003['T003_Pgto_Parcelas'] <= 0 && $mT003['D024_Quantidade_Parcelas'] > 0){
	$mT003['T003_Pgto_Parcelas'] = $mT003['D024_Quantidade_Parcelas'];
}

if($mT003['T003_Pgto_Portador'] <= 0 && $mT003['D024_D027_Id'] > 0){
	$mT003['T003_Pgto_Portador'] = $mT003['D024_D027_Id'];
}

if(empty($mT003['D024_Prazo_Parcela'])){
	$mT003['D024_Prazo_Parcela'] = $g['conf']->camposValorPadrao("primeiroVencimentoNum");
}
if(empty($mT003['D024_Dias_Vencimento'])){
	$mT003['D024_Dias_Vencimento'] = $g['conf']->camposValorPadrao("IntervalorDias");
}

//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
$sql = "SELECT * FROM T145 where T145_T003_Id='{$T003_Id}'";
$T145_sql = mysql_query($sql);

// LOOPING PARA CALCULAR O TOTAL DE SERVIÇOS CONFIRMADOS PARA ESSE ORÇAMENTO
$totalServicos=0;
while($T145 = mysql_fetch_array($T145_sql)){
	$totalServicos+=$T145['T145_Quantidade_Confirmada'] * $T145['T145_Valor_Unitario'];
}

//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
$valorTotalOrcamento = $mT003['T003_Valor_Total'];
$qtdeParcelas .= "<option value=\"0\">Sem Pagamento</option>";
for ($i=1; $i<=24; $i++) {
	$valorParcela = $VEN001->calcularParcelaCombo($i, $valorTotalOrcamento, $tipoJuros, $taxaJuros, $parcelasIsentas);
	if ($i>$parcelasIsentas){
		$valorTotal = gCorrigeNumero($i*gCorrigeNumeroInverte($valorParcela));
		$valorParcela.="*";
	} else {
		$valorTotal = gCorrigeNumero($valorTotalOrcamento);
	}

	if($i == $mT003['T003_Pgto_Parcelas']){
		$qtdeParcelas .= "<option selected=\"selected\" value=\"{$i}\">{$i} X - {$valorParcela} = {$valorTotal}</option>";
	} else {
		$qtdeParcelas .= "<option value=\"{$i}\">{$i} X - {$valorParcela} = {$valorTotal}</option>";
	}
}
//Define combobox do Portador
$sqlPortador = mysql_query("SELECT D027_Id, D027_Portador FROM D027 LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE C004_Cnpj LIKE '%{$CNPJ_Empresa_Corrente}%' OR ifnull(D027_D007_Id,0) = 0 ORDER BY D027_Portador");
$selectPortador = "";
while($rowPortador = mysql_fetch_array($sqlPortador)){
	if($rowPortador['D027_Id'] == $mT003['T003_Pgto_Portador']){
		$selectPortador .= "<option selected=\"selected\" value=\"{$rowPortador['D027_Id']}\">{$rowPortador['D027_Portador']}</option>";
	}else{
		$selectPortador .= "<option value=\"{$rowPortador['D027_Id']}\">{$rowPortador['D027_Portador']}</option>";
	}
}
//Define valor do campo 1° Venc. Dias e 1° Venc. Data
if(($mT003['T003_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mT003['T003_Pgto_Primeiro_Vcto_Valor']))){
	if($mT003['T003_Pgto_Primeiro_Vcto_Tipo'] == "Dias"){
		$primeiroVctoValorDias = $mT003['T003_Pgto_Primeiro_Vcto_Valor'];
	} else {
		$primeiroVctoValorData = $mT003['T003_Pgto_Primeiro_Vcto_Valor'];
	}
} else {
	$primeiroVctoValorDias = $mT003['D024_Prazo_Parcela'];
}
//Define valor do campo Prox Venc Intervalo e Prox Venc Data Fixa
if(($mT003['T003_Pgto_Dias_Vcto_Valor'] != "") || (!empty($mT003['T003_Pgto_Dias_Vcto_Valor']))){
	if($mT003['T003_Pgto_Dias_Vcto_Tipo'] == "Intervalo"){
		$diaVctoValorIntervalo = $mT003['T003_Pgto_Dias_Vcto_Valor'];
	} else {
		$diaVctoValorFixo = $mT003['T003_Pgto_Dias_Vcto_Valor'];
	}
} else {
	$diaVctoValorIntervalo = $mT003['D024_Dias_Vencimento'];
}

if(isset($_REQUEST['qtdeParcelas'])) {
	$row['T003_Id'] = $T003_Id;
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
  			$.mobile.changePage( "./pagamentoGrid.php?T003_Id={$T003_Id}", { transition: 'slide'} );
EOT;
	}
}

$obs='';
$obs .=	empty($mT003['D024_Prazo_Parcela']) 				? '' : " 1º Venc: ".$mT003['D024_Prazo_Parcela']."<br />";
$obs .= empty($mT003['D024_Dias_Vencimento']) 			? '' : " Dia Fixo/Intervalo: ".$mT003['D024_Dias_Vencimento']."<br />";
$obs .= empty($mT003['D024_Quantidade_Parcelas']) 		? '' : " Nº Parcelas: ".$mT003['D024_Quantidade_Parcelas']."<br />";
$obs .= empty($mT003['D027_Portador']) 					? '' : " Portador: ".$mT003['D027_Portador']."<br />";

$etapa = 4;
include('inc.header.bar.php');
include('inc.header.page.php');
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
                    <a data-theme="" data-icon="" onclick="\$.mobile.changePage( './pagamentoGrid.php?T003_Id={$T003_Id}', { transition: 'fade'} );">
                        Pagamentos
                    </a>
                </li>
            </ul>
        </div>
        {$temErro}
                 <form action="./pagamento.php?T003_Id={$T003_Id}" method="post" data-ajax="false">
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
	                <div class="ui-block-b" style="width: 39%;">
	                	<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
							<h3>Informação de Pagamento</h3>
							<div data-role="fieldcontain">
								{$obs}{$mT003['D024_Instrucao_Pagamento']}
							</div>
						</div>
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
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
