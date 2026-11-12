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
$C004 = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
$mC004 = mysql_fetch_array($C004);
$tipoJuros = $mC004['C004_Tipo_Juros'];
$taxaJuros = $mC004['C004_Taxa_Juros'];
$parcelasIsentas = $mC004['C004_Parcelas_Isentas_Juros'];
$sqlT003 = mysql_query("SELECT * FROM T003 LEFT JOIN D027 ON D027_Id=T003_Pgto_Portador LEFT JOIN D024 ON D024_Id=T003_D024_Id WHERE T003_Id='{$T003_Id}'");
$mT003 = mysql_fetch_array($sqlT003);

if ($mT003['T003_Pgto_Parcelas'] <= 0 && $mT003['D024_Quantidade_Parcelas'] > 0) {
	$mT003['T003_Pgto_Parcelas'] = $mT003['D024_Quantidade_Parcelas'];
}

if ($mT003['T003_Pgto_Portador'] <= 0 && $mT003['D024_D027_Id'] > 0) {
	$mT003['T003_Pgto_Portador'] = $mT003['D024_D027_Id'];
}

if (empty($mT003['D024_Prazo_Parcela'])) {
	$mT003['D024_Prazo_Parcela'] = $g['conf']->camposValorPadrao("primeiroVencimentoNum");
}
if (empty($mT003['D024_Dias_Vencimento'])) {
	$mT003['D024_Dias_Vencimento'] = $g['conf']->camposValorPadrao("IntervalorDias");
}

//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
$sql = "SELECT * FROM T145 where T145_T003_Id='{$T003_Id}'";
$T145_sql = mysql_query($sql);

// LOOPING PARA CALCULAR O TOTAL DE SERVIÇOS CONFIRMADOS PARA ESSE ORÇAMENTO
$totalServicos = 0;
while ($T145 = mysql_fetch_array($T145_sql)) {
	$totalServicos += $T145['T145_Quantidade_Confirmada'] * $T145['T145_Valor_Unitario'];
}

//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
$valorTotalOrcamento = $mT003['T003_Valor_Total'];
$T003_Pgto_Parcelas .= "<option value=\"0\">Sem Pagamento</option>";
for ($i = 1; $i <= 24; $i++) {
	$valorParcela = $VEN001->calcularParcelaCombo($i, $valorTotalOrcamento, $tipoJuros, $taxaJuros, $parcelasIsentas);
	if ($i > $parcelasIsentas) {
		$valorTotal = gCorrigeNumero($i * gCorrigeNumeroInverte($valorParcela));
		$valorParcela .= "*";
	} else {
		$valorTotal = gCorrigeNumero($valorTotalOrcamento);
	}

	if ($i == $mT003['T003_Pgto_Parcelas']) {
		$T003_Pgto_Parcelas .= "<option selected=\"selected\" value=\"{$i}\">{$i} X - {$valorParcela} = {$valorTotal}</option>";
	} else {
		$T003_Pgto_Parcelas .= "<option value=\"{$i}\">{$i} X - {$valorParcela} = {$valorTotal}</option>";
	}
}
//Define combobox do Portador
$sqlPortador = mysql_query("SELECT D027_Id, D027_Portador FROM D027 ORDER BY D027_Portador");
$selectPortador = "";
while ($rowPortador = mysql_fetch_array($sqlPortador)) {
	if ($rowPortador['D027_Id'] == $mT003['T003_Pgto_Portador']) {
		$selectPortador .= "<option selected=\"selected\" value=\"{$rowPortador['D027_Id']}\">{$rowPortador['D027_Portador']}</option>";
	} else {
		$selectPortador .= "<option value=\"{$rowPortador['D027_Id']}\">{$rowPortador['D027_Portador']}</option>";
	}
}
//Define valor do campo 1° Venc. Dias e 1° Venc. Data
if (($mT003['T003_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mT003['T003_Pgto_Primeiro_Vcto_Valor']))) {
	$primeiroVctoValorData = $mT003['T003_Pgto_Primeiro_Vcto_Valor'];
} else {
	$primeiroVctoValorDias = $mT003['D024_Prazo_Parcela'];
}

//Define valor do campo Prox Venc Intervalo e Prox Venc Data Fixa
if (($mT003['T003_Pgto_Dias_Vcto_Valor'] != "") || (!empty($mT003['T003_Pgto_Dias_Vcto_Valor']))) {
	if ($mT003['T003_Pgto_Dias_Vcto_Tipo'] == "INTERVALO") {
		$diaVctoValorIntervalo = $mT003['T003_Pgto_Dias_Vcto_Valor'];
	} else {
		$diaVctoValorFixo = $mT003['T003_Pgto_Dias_Vcto_Valor'];
	}
} else {
	$diaVctoValorIntervalo = $mT003['D024_Dias_Vencimento'];
}

# POST (Momento que ele vai gerar as parcelas)
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

	if ($_REQUEST['T003_Pgto_Primeiro_Vcto_Tipo'] === 'DATA') {
		$_REQUEST['T003_Pgto_Primeiro_Vcto_Valor_Data'] = \DateTime::createFromFormat('d/m/Y', $_REQUEST['T003_Pgto_Primeiro_Vcto_Valor_Data'])->format('Y-m-d');
	}


	mysql_query("UPDATE T003 
						SET 
							T003_Pgto_Parcelas = '{$_REQUEST['T003_Pgto_Parcelas']}', 
							T003_Pgto_Primeiro_Vcto_Tipo = '{$_REQUEST['T003_Pgto_Primeiro_Vcto_Tipo']}',
							T003_Pgto_Primeiro_Vcto_Valor = '{$_REQUEST['T003_Pgto_Primeiro_Vcto_Valor']}',
							T003_Pgto_Primeiro_Vcto_Valor_Data = '{$_REQUEST['T003_Pgto_Primeiro_Vcto_Valor_Data']}',						
							T003_Pgto_Dias_Vcto_Tipo = '{$_REQUEST['T003_Pgto_Dias_Vcto_Tipo']}',
							T003_Pgto_Dias_Vcto_Valor = '{$_REQUEST['T003_Pgto_Dias_Vcto_Valor']}',
							T003_D027_Id  = '{$_REQUEST['T003_Pgto_Portador']}' 
					  WHERE T003_Id = '{$T003_Id}'");


	$dadosTabela = array(
		'tabela' => 'T003',
		'tabelaId' => $T003_Id,
		'campos' => 'T003_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
							T003_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
							T003_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
							T003_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
							T003_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
							T003_D027_Id as D027_Id,
							T003_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
							T003_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
							T003_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
							T003_Pgto_Parcelas as Total_Parcelas,
							T003_Valor_Total_IPI as valorTotalIPI,
							T003_Valor_Total as valorTotal,
							T003_Valor_Frete as valorTotalFrete,
							T003_Valor_ST as valorTotalST',
		'where' => "T003_Id = '{$T003_Id}'",
		'tabelaPgto' => 'T089',
		'camposPgto' => 'T089_Prazos_Dias as prazoDias,T089_D027_Id as D027Pagamento,T089_Numero_Parcela as numeroParcela',
		'wherePgto' => "T089_T003_Id='{$T003_Id}'"
	);

	$retorno = $VEN001->atualizarParcelas($dadosTabela);

	if ($retorno !== true) {
		$temErro = '<h3 style="text-align: center;">' . $retorno . '</h3>';
	} else {
		$script = <<<EOT
  			$.mobile.changePage( "./pagamentoGrid.php?T003_Id={$T003_Id}", { transition: 'slide'} );
EOT;
	}
}


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
                 	<div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
		                    <label for="T003_Pgto_Parcelas">Parcelas</label>
		                    <select data-native-menu="true" name="T003_Pgto_Parcelas" id="T003_Pgto_Parcelas">
		                        {$T003_Pgto_Parcelas}
		                    </select>
		                </div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
							<label for="T003_Pgto_Primeiro_Vcto_Tipo">
								Tipo dias vencimento
							</label>
							<select data-native-menu="true" name="T003_Pgto_Primeiro_Vcto_Tipo" id="T003_Pgto_Primeiro_Vcto_Tipo">
								<option value="DATA">Data</option>
								<option value="DIAS">Dias</option>
							</select>
						</div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;" id='divPrimeiroValor'>
	                        <fieldset data-role="controlgroup">
	                            <label for="T003_Pgto_Primeiro_Vcto_Valor">
								  1º Vencimento.
	                            </label>
	                            <div style="display: inline-block; width: 60%;">
	                            <input name="T003_Pgto_Primeiro_Vcto_Valor" id="T003_Pgto_Primeiro_Vcto_Valor" value="{$primeiroVctoValorDias}" placeholder="" type="text" />
	                            </div>
	                        </fieldset>
	                    </div>
						<div data-role="fieldcontain" style="padding: 2px 8px;" id='divPrimeiraData'>
						<fieldset data-role="controlgroup">
							<label for="T003_Pgto_Primeiro_Vcto_Valor_Data">
							  1º Vencimento.
							</label>
							<div style="display: inline-block; width: 60%;">
							<input name="T003_Pgto_Primeiro_Vcto_Valor_Data" id="T003_Pgto_Primeiro_Vcto_Valor_Data" value="{$primeiroVctoValorData}" placeholder="" type="date" data-role="datebox" data-options='{"mode": "calbox", "dateFormat": "dd/mm/YYYY"}' />
							</div>
						</fieldset>
					</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
							<label for="T003_Pgto_Dias_Vcto_Tipo">
								Tipo dias vencimento
							</label>
							<select data-native-menu="true" name="T003_Pgto_Dias_Vcto_Tipo" id="T003_Pgto_Dias_Vcto_Tipo">
							<option value="INTERVALO">Intervalo</option>
							<option value="FIXO">Fixo</option>
							</select>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
							<fieldset data-role="controlgroup">
								<label for="T003_Pgto_Dias_Vcto_Valor">
									Dias Vencimento
								</label>
								<input name="T003_Pgto_Dias_Vcto_Valor" id="T003_Pgto_Dias_Vcto_Valor" value="{$diaVctoValorIntervalo}" placeholder="" type="text" />
							</fieldset>
						</div>
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
		                    <label for="T003_Pgto_Portador">
		                        Portador
		                    </label>
		                    <select data-native-menu="true" name="T003_Pgto_Portador" id="T003_Pgto_Portador">
		                        {$selectPortador}
		                    </select>
		                </div>
	                    <!--
	                    <div data-role="fieldcontain" style="padding: 2px 8px;">
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
                    <div>
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

		$('#T003_Pgto_Primeiro_Vcto_Tipo').change(function() {
			if ($(this).val() == 'DIAS') {
				$('#divPrimeiraData').hide();
				$('#T003_Pgto_Primeiro_Vcto_Valor_Data').val('');
				$('#divPrimeiroValor').show();
				$('#divPrimeiroValor').focus();
			} else {
				$('#divPrimeiroValor').hide();
				$('#T003_Pgto_Primeiro_Vcto_Valor').val('');
				$('#divPrimeiraData').show();
				$('#divPrimeiraData').focus();
			}
		});

		$('#T003_Pgto_Primeiro_Vcto_Tipo').trigger('change');	

    	$( document ).undelegate("#pageOrcamentoPagamento", "pageinit");
    	$( document ).delegate("#pageOrcamentoPagamento", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;