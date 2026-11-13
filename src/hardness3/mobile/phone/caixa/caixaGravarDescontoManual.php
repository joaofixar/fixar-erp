<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('mobile/tablet/inc.header.php');

// Vars

$descontoReais = gCorrigeNumero($T005['T005_Valor_Desconto_Manual']);
$descontoReais = ($descontoReais == "0,00" ? "" : $descontoReais);

$valorTotalAntigo = $T005['T005_Valor_Total'] + $T005['T005_Valor_Desconto_Manual'];
$descontoPercentual = gCorrigeNumero((($valorTotalAntigo - $T005['T005_Valor_Total']) / $valorTotalAntigo) * 100);   
$descontoPercentual = ($descontoPercentual == "0,00" ? "" : $descontoPercentual);

$descontoTotal = gCorrigeNumero($T005['T005_Valor_Total']);
$descontoTotal = ($descontoTotal == "0,00" ? "" : $descontoTotal);

  // Os resultados serão 80% e 20%

echo <<<EOT
	<div data-role="dialog" id="caixaGravarDescontoManual">
		<div data-role="header" data-theme="d" style="margin: 2% auto 15px;">
			<h1>Desconto em Reais</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form action="./caixa.php?T005_Id={$T005_Id}" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="numeroParcelaExibir">
	                                Valor(R$): 
	                            </label>
	                            	<input name="T005_Valor_Desconto_Manual" id="T005_Valor_Desconto_Manual" value="{$descontoReais}" type="text" />
	                            	<input name="alterarDescontoManual" id="alterarDescontoManual" value="alterarDescontoManual" placeholder="" type="hidden"/>
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
		<div data-role="header" data-theme="d" style="margin: 2% auto 15px;">
			<h1>Desconto em Percentual</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form action="./caixa.php?T005_Id={$T005_Id}" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="numeroParcelaExibir">
	                                Percentual(%): 
	                            </label>
	                            	<input name="Valor_Percentual" id="Valor_Percentual" value="{$descontoPercentual}" type="text" />
	                            	<input name="alterarDescontoManual" id="alterarDescontoManual" value="alterarDescontoManual" placeholder="" type="hidden"/>
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
		<div data-role="header" data-theme="d" style="margin: 2% auto 15px;">
			<h1>Desconto Total</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form action="./caixa.php?T005_Id={$T005_Id}" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="numeroParcelaExibir">
	                                Valor Total(R$): 
	                            </label>
	                            	<input name="Valor_Total" id="Valor_Total" value="{$descontoTotal}" type="text" />
	                            	<input name="alterarDescontoManual" id="alterarDescontoManual" value="alterarDescontoManual" placeholder="" type="hidden"/>
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
    	$( document ).undelegate("#caixaGravarDescontoManual", "pageinit");
    	$( document ).delegate("#caixaGravarDescontoManual", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
