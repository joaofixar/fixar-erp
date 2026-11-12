<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('inc.header.php');

$descontoReais = gCorrigeNumero($T003['T003_Valor_Desconto']);
$descontoReais = ($descontoReais == "0,00" ? "" : $descontoReais);

$percentualDesconto = gCorrigeNumero(($T003['T003_Valor_Desconto']/$T003['T003_Valor_Total_Produtos_Sem_Desconto'])*100);
$percentualDesconto = ($percentualDesconto == "0,00" ? "" : $percentualDesconto);

$valorTotal = gCorrigeNumero($T003['T003_Valor_Total']);
$valorTotal = ($valorTotal == "0,00" ? "" : $valorTotal);

$descontoTotal = gCorrigeNumero($T003['T003_Valor_Desconto_Manual']);
$descontoTotal = ($descontoTotal == "0,00" ? "" : $descontoTotal);

$habilitarDescontoPercentualTablet = $g['C031']['habilitarDescontoPercentualTablet'] == 'S' ? '' : 'display:none;';
$habilitarDescontoReaisTablet = $g['C031']['habilitarDescontoReaisTablet'] == 'S' ? '' : 'display:none;';
$habilitarDefinirValorTotalTablet = $g['C031']['habilitarDefinirValorTotalTablet'] == 'S' ? '' : 'display:none;';
$habilitarDescontoValorTotalTablet = $g['C031']['habilitarDescontoValorTotalTablet'] == 'S' ? '' : 'display:none;';

echo <<<EOT
	<div data-role="dialog" id="orcamentoGravarDescontoManual">
		<div data-role="header" data-theme="d" style="margin: 2% auto 15px; {$habilitarDescontoPercentualTablet}">
			<h1>Desconto em Percentual</h1>
		</div>
		<div data-role="content" data-theme="c" class="ui-overlay-shadow ui-corner-bottom ui-content ui-body-c" style="{$habilitarDescontoPercentualTablet}">
			{$temErro}
			<form action="./orcamento.php?T003_Id={$T003_Id}" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="numeroParcelaExibir">
	                                Percentual(%):
	                            </label>
	                            	<input name="percentualDesconto" id="percentualDesconto" value="{$percentualDesconto}" type="text" />
	                            	<input name="alterarDesconto" id="alterarDesconto" value="alterarDesconto" placeholder="" type="hidden"/>
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
                	<div class="ui-block-b">
	                    <input value="Aplicar" type="submit" />
                	</div>
                </div>
            </form>
		</div>


		<div data-role="header" data-theme="d" style="margin: 2% auto 15px; {$habilitarDescontoReaisTablet}">
			<h1>Desconto em Reais</h1>
		</div>
		<div data-role="content" data-theme="c" class="ui-overlay-shadow ui-corner-bottom ui-content ui-body-c" style="{$habilitarDescontoReaisTablet}">
			{$temErro}
			<form action="./orcamento.php?T003_Id={$T003_Id}" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="numeroParcelaExibir">
	                                Valor(R$):
	                            </label>
	                            	<input name="valorDescontoReais" id="valorDescontoReais" value="{$descontoReais}" type="text" />
	                            	<input name="alterarDesconto" id="alterarDesconto" value="alterarDesconto" placeholder="" type="hidden"/>
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
                	<div class="ui-block-b">
	                    <input value="Aplicar" type="submit" />
                	</div>
                </div>
            </form>
		</div>


		<div data-role="header" data-theme="d" style="margin: 2% auto 15px; {$habilitarDefinirValorTotalTablet}">
			<h1>Informar Valor Total</h1>
		</div>
		<div data-role="content" data-theme="c" class="ui-overlay-shadow ui-corner-bottom ui-content ui-body-c" style="{$habilitarDefinirValorTotalTablet}">
			{$temErro}
			<form action="./orcamento.php?T003_Id={$T003_Id}" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="numeroParcelaExibir">
	                                Valor Total(R$):
	                            </label>
	                            	<input name="valorTotal" id="valorTotal" value="{$valorTotal}" type="text" />
	                            	<input name="alterarDesconto" id="alterarDesconto" value="alterarDesconto" placeholder="" type="hidden"/>
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
                	<div class="ui-block-b">
	                    <input value="Aplicar" type="submit" />
                	</div>
                </div>
            </form>
		</div>


		<div data-role="header" data-theme="d" style="margin: 2% auto 15px; {$habilitarDescontoValorTotalTablet}">
			<h1>Desconto no Valor Total</h1>
		</div>
		<div data-role="content" data-theme="c" class="ui-overlay-shadow ui-corner-bottom ui-content ui-body-c" style="{$habilitarDescontoValorTotalTablet}">
			{$temErro}
			<form action="./orcamento.php?T003_Id={$T003_Id}" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="numeroParcelaExibir">
	                                Desconto(R$):
	                            </label>
	                            	<input name="descontoValorTotal" id="descontoValorTotal" value="{$descontoTotal}" type="text" />
	                            	<input name="alterarDesconto" id="alterarDesconto" value="alterarDesconto" placeholder="" type="hidden"/>
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
                	<div class="ui-block-b">
	                    <input value="Aplicar" type="submit" />
                	</div>
                </div>
            </form>
		</div>
	</div>
	<script>
    	$( document ).undelegate("#orcamentoGravarDescontoManual", "pageinit");
    	$( document ).delegate("#orcamentoGravarDescontoManual", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
