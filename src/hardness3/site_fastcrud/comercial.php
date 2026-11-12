<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<script type="text/javascript" src="static/jquery/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="static/js/site.js"></script>
<link href="static/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
<style>
.content {
  width: 650px;
  margin: 0 auto;
  padding: 25px 0;
}

#valorTotal{
	font-size: 24px;
	font-weight: bold;
	border-top: 1px solid #000;
	width: 250px;
	padding-bottom: 20px;
	padding-right: 48px;
	float: right;
	text-align: right;
	clear: both;
}

#calculoPreco{
	font-size: 16px;
	font-weight: bold;
	width: 245px;
	float: right;
}

#total {
	float: right;
	width: 650px;
	margin: 0 auto;
}
</style>
</head>
<script type="text/javascript">
	$(document).ready(function() {
		var modulos = [];
		$('#testar').bind('click', function(){
			console.log(modulos);
		});

		$('input[type=checkbox]').bind('click', function(){
			$("input:checkbox").each(function(){
				modulos = [];
				var $this = $(this);
				if($this.is(":checked")){
					modulos.push($this.attr("id"));
					$(this).closest('tr').addClass('success');
				} else {
					$(this).closest('tr').removeClass('success');
				}
			});

			$.getJSON('requisicoes/ajax.php?funcao=calcularPreco&modulos=' + encodeURIComponent(modulos) + '&callback=?', function(request) {
				if(request.code) {
					$('#valorDesconto').html('R$ ' + number_format(request.data['valorDesconto'], 2, ',', '.'));
					$('#valorTotal').html('R$ ' + number_format(request.data['valorTotal'], 2, ',', '.'));
					$('#valorBruto').html('R$ ' + number_format(request.data['valorBruto'], 2, ',', '.'));
					$('#desconto').html('(' + request.data['percentualDesconto'] + '%)');
				}
			});
		});
	});
</script>
<body>
	<div class="content">	
		<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th class="text-center">Módulo</th>
			<th class="text-center">Mensalidade</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="89" id="1">Nota Fiscal Eletronica - NFe (Emissão de Notas)
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 89,00</td>
		</tr>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="89" id="2">Vendas - (Orçamento, Pedido)
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 89,00</td>
		</tr>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="89" id="3">Estoque - (NFe Entrada, Divergência de Estoque)
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 89,00</td>
		</tr>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="50" id="4">Contas a Pagar
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 50,00</td>
		</tr>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="50" id="5">Contas a Receber
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 50,00</td>
		</tr>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="50" id="6">Caixa / Bancos (Lançamentos Bancários)
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 50,00</td>
		</tr>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="80" id="7">Boletos
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 80,00</td>
		</tr>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="80" id="8">Remessa Bancária
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 80,00</td>
		</tr>
		<tr>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" valor="80" id="9">Retorno Bancário
					</label>
				</div>
			</td>
			<td style="padding-right: 48px; text-align: right; vertical-align: middle;">R$ 80,00</td>
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
			<button type="button" class="btn btn-success btn-lg" style="margin: 0 10px; width: 180px;" id="comprar">Comprar</button>
			<button type="button" class="btn btn-primary btn-lg" style="margin: 0 10px; width: 180px;" id="testar">Testar</button>
		</div>
	</div>
</body>
</html>