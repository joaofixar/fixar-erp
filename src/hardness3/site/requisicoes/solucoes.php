<?php
// Header
$headerHeadTitle = 'Hardness Sistemas - Soluções de negócio';
include('requisicoes/header.php');

$footerJS = <<<EOT
	$(document).ready(function() {
		var modulos = [];
EOT;

$modulos = (!empty($_REQUEST['modulos'])) ? explode(",", $_REQUEST['modulos']) : array();
if(count($modulos) > 0){
	foreach($modulos as $id){
		$footerJS .= <<<EOT
			$('#' + {$id}).attr('checked', true);
EOT;
	}
	$footerJS .= <<<EOT
		marcarModulos();
EOT;
}
/*
* Detalhamento dos modulos
*/
//Notas Fiscais
$detail1 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Emissão de Nota Fiscal Eletrônica - NFe</li>
	<li><span class='glyphicon glyphicon-ok'></span> Controle de Notas Fiscais Emitidas</li>
	<li><span class='glyphicon glyphicon-ok'></span> Carta de Correção Eletrônica (CC-e)</li>
	<li><span class='glyphicon glyphicon-ok'></span> Cancelamento de Notas Fiscais</li>
	<li><span class='glyphicon glyphicon-ok'></span> Geração do DANFE</li>
	<li><span class='glyphicon glyphicon-ok'></span> Exportação dos XML's</li>
EOT;
$detail1 = htmlentities(utf8_decode($detail1));
$detail2 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Orçamentos</li>
	<li><span class='glyphicon glyphicon-ok'></span> Pedidos</li>
	<li><span class='glyphicon glyphicon-ok'></span> Geração Automatica de Nota Fiscal</li>
EOT;
$detail2 = htmlentities(utf8_decode($detail2));
$detail3 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Conhecimento de Transporte</li>
	<li><span class='glyphicon glyphicon-ok'></span> Notas Fiscais Recebidas</li>
	<li><span class='glyphicon glyphicon-ok'></span> Divergência de Estoque</li>
EOT;
$detail3 = htmlentities(utf8_decode($detail3));
$detail4 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Baixa dos Títulos</li>
	<li><span class='glyphicon glyphicon-ok'></span> Agrupamento e Reparcelamento</li>
EOT;
$detail4 = htmlentities(utf8_decode($detail4));
$detail5 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Baixa dos Títulos</li>
	<li><span class='glyphicon glyphicon-ok'></span> Agrupamento e Reparcelamento</li>
	<li><span class='glyphicon glyphicon-ok'></span> Alteração do Vencimento</li>
EOT;
$detail5 = htmlentities(utf8_decode($detail5));
$detail6 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Controle dos Lançamentos Bancários</li>
	<li><span class='glyphicon glyphicon-ok'></span> Reconciliamento Bancário com Extrato</li>
EOT;
$detail6 = htmlentities(utf8_decode($detail6));
$detail7 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Emissão de Boletos</li>
	<li><span class='glyphicon glyphicon-ok'></span> Visualização e Impressão de Boletos</li>
	<li><span class='glyphicon glyphicon-ok'></span> Envio de Boletos por Email</li>
	<i>* Necessita do módulo de contas a receber</i>
EOT;
$detail7 = htmlentities(utf8_decode($detail7));
$detail8 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Geração de Remessa Bancária</li>
	<i>* Necessita do módulo de contas a receber e boletos bancários</i>
EOT;
$detail8 = htmlentities(utf8_decode($detail8));
$detail9 = <<<EOT
	<li><span class='glyphicon glyphicon-ok'></span> Análise de Retorno Bancário</li>
	<li><span class='glyphicon glyphicon-ok'></span> Processamento e baixa automática <br />de títulos a receber</li>
EOT;
$detail9 = htmlentities(utf8_decode($detail9));

echo <<<EOT
<br /><br /><br /><br /><br /><br />
<div id="solucoes" class="container">
	<div class="row">
		<div class="col-xs-12">
			Mussum ipsum cacilds, vidis litro abertis. Consetis adipiscings elitis. Pra lá , depois divoltis porris, paradis. Paisis, filhis, espiritis santis. Mé faiz elementum girarzis, nisi eros vermeio, in elementis mé pra quem é amistosis quis leo. Manduma pindureta quium dia nois paga. Sapien in monti palavris qui num significa nadis i pareci latim. Interessantiss quisso pudia ce receita de bolis, mais bolis eu num gostis. Suco de cevadiss, é um leite divinis, qui tem lupuliz, matis, aguis e fermentis. Interagi no mé, cursus quis, vehicula ac nisi. Aenean vel dui dui. Nullam leo erat, aliquet quis tempus a, posuere ut mi. Ut scelerisque neque et turpis posuere pulvinar pellentesque nibh ullamcorper. Pharetra in mattis molestie, volutpat elementum justo. Aenean ut ante turpis. Pellentesque laoreet mé vel lectus scelerisque interdum cursus velit auctor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam ac mauris lectus, non scelerisque augue. Aenean justo massa. Casamentiss faiz malandris se pirulitá, Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.
		</div>
	</div>
	<div class="row">
		<div class="col-xs-8">
			<table class="table table-striped table-hover">
			<thead>
			<tr>
				<th class="text-center">Módulo</th>
				<th class="text-center">Mensalidade</th>
			</tr>
			</thead>
			<tbody>
			<tr class="detail" detail-title="Nota Fiscal Eletronica" detail-content="{$detail1}">
				<td>
					<div class="checkbox">
						<label id="popover">
							<input type="checkbox" checked="checked" id="1">  Nota Fiscal Eletronica - NFe</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 89,00</td>
			</tr>
			<tr class="detail" detail-title="Vendas" detail-content="{$detail2}">
				<td>
					<div class="checkbox">
						<label>
							<input type="checkbox" disabled="disabled" id="2">  Vendas</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 89,00</td>
			</tr>
			<tr class="detail" detail-title="Estoque" detail-content="{$detail3}">
				<td>
					<div class="checkbox">
						<label>
							<input type="checkbox" disabled="disabled" id="3">  Estoque</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 89,00</td>
			</tr>
			<tr class="detail" detail-title="Contas a Pagar" detail-content="{$detail4}">
				<td>
					<div class="checkbox">
						<label>
							<input type="checkbox" disabled="disabled" id="4">  Contas a Pagar</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 50,00</td>
			</tr>
			<tr class="detail" detail-title="Contas a Receber" detail-content="{$detail5}">
				<td>
					<div class="checkbox">
						<label>
							<input type="checkbox" disabled="disabled" id="5">  Contas a Receber</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 50,00</td>
			</tr>
			<tr class="detail" detail-title="Caixas e Bancos" detail-content="{$detail6}">
				<td>
					<div class="checkbox">
						<label>
							<input type="checkbox" disabled="disabled" id="6">  Caixa e Bancos</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 50,00</td>
			</tr>
			<tr class="detail" detail-title="Boletos" detail-content="{$detail7}">
				<td>
					<div class="checkbox">
						<label>
							<input type="checkbox" disabled="disabled" id="7">  Boletos</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 80,00</td>
			</tr>
			<tr class="detail" detail-title="Remessa Bancária" detail-content="{$detail8}">
				<td>
					<div class="checkbox">
						<label>
							<input type="checkbox" disabled="disabled" id="8">  Remessa Bancária</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 80,00</td>
			</tr>
			<tr class="detail" detail-title="Retorno Bancário" detail-content="{$detail9}">
				<td>
					<div class="checkbox">
						<label>
							<input type="checkbox" disabled="disabled" id="9">  Retorno Bancário</input>
						</label>
					</div>
				</td>
				<td style="text-align: center; vertical-align: middle;">R$ 80,00</td>
			</tr>
			</tbody>
			</table>
			<div id="total">
				<div id="calculoPreco">
					<span style="width: 85px; text-align:right; float: left;">Valor</span>
					<span style="width: 110px; text-align:right; float: left;" id="valorBruto">R$ 0,00</span><br />
					<span style="width: 85px; text-align:right; float: left;">Desconto</span>
					<span style="width: 110px; text-align:right; float: left;" id="valorDesconto">R$ 0,00</span>
					<span id="desconto" style="color: green; margin-left: 5px;">(0%)</span>
				</div>
				<div id="valorTotal">R$ 0,00</div>
			</div>
			<div class="text-center">
				<button type="button" class="btn btn-success btn-lg" style="margin: 0 10px; width: 220px;" id="comprar">Comprar</button>
				<button type="button" class="btn btn-primary btn-lg" style="margin: 0 10px; width: 220px;" id="testar">Testar Gratuitamente</button>
			</div>
		</div>
		<div class="col-xs-4">
			<div class="affix col-xs-3" style="margin-top: 10px;">
				<div class="panel panel-default">
					<div class="panel-heading">
					<span class="glyphicon glyphicon-info-sign"></span>
					<span id="tituloPanel">Detalhes do Módulo</span>
					</div>
					<div class="panel-body" id="contentPanel">
						<i><font color="gray">Posicione o cursor do mouse sobre um módulo para mais detalhes.</font></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
EOT;
// Footer
$footerJS .= <<<EOT
	modulos = [];
	$("input:checkbox").each(function(){
		if($(this).is(":checked")){
			modulos.push($(this).attr("id"));
			$(this).closest('tr').addClass('success');
		} else {
			$(this).closest('tr').removeClass('success');
		}
	});

	$('.detail').mouseover(function(){
		$('#tituloPanel').html($(this).attr('detail-title'));
		$('#contentPanel').html('<ul style="margin-left: -20px;">' + $(this).attr('detail-content') + '</ul>');
	});
	
	$('tr').bind('click', function(){
		$(this).find('input[type=checkbox]').trigger('click');
	});

	$('#testar').bind('click', function(){
		var modulosSelecionados = modulos.filter(function(value){ return value; }).length;
		if(modulosSelecionados > 0){
			window.location = "http://fabio.site/cadastro/?modulos=" + encodeURIComponent(modulos);
		} else {
			alert("Para prosseguir é necessário selecionar pelo menos um módulo do sistema.");
		}
	});

	$('#comprar').bind('click', function(){
		var modulosSelecionados = modulos.filter(function(value){ return value; }).length;
		if(modulosSelecionados > 0){
			window.location = "http://fabio.site/cadastro/?buy=true&modulos=" + encodeURIComponent(modulos);
		} else {
			alert("Para prosseguir é necessário selecionar pelo menos um módulo do sistema.");
		}
	});

	$('input[type=checkbox]').unbind().bind('click', function(event){
		event.stopPropagation();
		modulos = [];

		if($(this).attr("id") == '5' && ($('#7').is(":checked")) || $('#8').is(":checked")) {
			$('#5').prop('checked', true);
		}
		if($(this).attr("id") == '7' && $('#8').is(":checked")) {
			$('#7').prop('checked', true);
		}

		if($(this).attr("id") == '7'){
			$('#5').prop('checked', true);
		} else if($(this).attr("id") == '8'){
			$('#5').prop('checked', true);
			$('#7').prop('checked', true);
		}

		$("input:checkbox").each(function(){
			if($(this).is(":checked")){
				modulos.push($(this).attr("id"));
				$(this).closest('tr').addClass('success');
			} else {
				$(this).closest('tr').removeClass('success');
			}
		});
		$.getJSON('/ajax/?funcao=calcularPreco&modulos=' + encodeURIComponent(modulos) + '&callback=?', function(request) {
			if(request.code) {
				$('#valorDesconto').html('R$ ' + number_format(request.data['valorDesconto'], 2, ',', '.'));
				$('#valorTotal').html('R$ ' + number_format(request.data['valorTotal'], 2, ',', '.'));
				$('#valorBruto').html('R$ ' + number_format(request.data['valorBruto'], 2, ',', '.'));
				$('#desconto').html('(' + request.data['percentualDesconto'] + '%)');
			}
		});
	});
	
	function marcarModulos(){
		modulos = [];
		$("input:checkbox").each(function(){
			if($(this).is(":checked")){
				modulos.push($(this).attr("id"));
				$(this).closest('tr').addClass('success');
			} else {
				$(this).closest('tr').removeClass('success');
			}
		});

		$.getJSON('/ajax/?funcao=calcularPreco&modulos=' + encodeURIComponent(modulos) + '&callback=?', function(request) {
			if(request.code) {
				$('#valorDesconto').html('R$ ' + number_format(request.data['valorDesconto'], 2, ',', '.'));
				$('#valorTotal').html('R$ ' + number_format(request.data['valorTotal'], 2, ',', '.'));
				$('#valorBruto').html('R$ ' + number_format(request.data['valorBruto'], 2, ',', '.'));
				$('#desconto').html('(' + request.data['percentualDesconto'] + '%)');
			}
		});
	}
});
EOT;
include('requisicoes/footer.php');