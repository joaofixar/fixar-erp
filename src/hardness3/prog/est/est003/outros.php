<?php
namespace hardness;

switch($g['r'][3]){
	case 'est003OutrosEtiquetas':
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etiquetaXX000">Etiqueta Genérica (XX000)</button>
			</div>
EOT;
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#etiquetaXX000').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Impressão de etiquetas', '/sistema/etiquetas/content/etiquetasGenericaXX000/', '', [300,200]);
			});
		} </script></div>
EOT;
	  break;

	  case 'est003ManualCSVBalanco':
		echo<<<EOT
		
		<style>
			.tabela{
				border:1px solid black;
				border-colapse: colapse;	
				cellspace="10";
				font-size:20px;
				padding:10px 15px;
				background-color:#000;
				text-align:left;
			}
			.LinhaReferencia{
				text-align:center;
				background: rgba(76, 175, 80, 0);
				padding:0px 20px 0px 27px;
			}
			.linhaTabela{				
				font-size:15px;
				padding:10px 15px;
				background-color:#FFF;
				text-align:center;
				
	
			}	
		</style>
	
		<div style="padding-left:10px;">
		<h2>Modelo de documento CSV</h2>
		<h3>Observações:</h3>
		<p style="font-size:12px">-Abaixo está o formato modelo do documento para a importação!
		<br/>-O documento deve ter <strong>2 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
		<br/>-Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma gráfia do documento modelo.
		<br/>-A extesão do documento deve ser CSV (separado por vírgulas)(*.csv)
		<br/>-Ao final do documento não deve haver nenhuma linha em branco.
		<br/>-<strong>Os campos com * são de preenchimento obrigatório.</strong>
		<br/><font style="color: var(--BGVermelho);font-weight:bold">-Informar no campo LOCACAO qual locação será alimentado a quantidade balanço</font>
		<br/><font style="color: var(--BGVermelho);font-weight:bold">-Informar SIM no campo ALTERAR_LOCACAO caso deseje alterar a locação do produto</font>
		</p>
		</div>
		<table cellspacing=1px bgcolor="black">		
			<tr>		
				<td class="linhaTabela">*Codigo_Produto</td>
				<td class="linhaTabela">*Locacao</td>
				<td class="linhaTabela">Quantidade_Balanco</td>
				<td class="linhaTabela">Alterar_Locacao</td>
			</tr>
			<tr>			
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
			</tr>
			<tr>			
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
			</tr>
			<tr>			
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
			</tr>
			<tr>		
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
				<td class="linhaTabela">---</td>
			</tr>	
		</table>
EOT;
		break;
	

}
?>
