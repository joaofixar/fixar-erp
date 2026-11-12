<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
$T006_Id = $_REQUEST['T006_Id'];
$T204_Id = isset($_REQUEST['T204_Id']) ? $_REQUEST['T204_Id'] : false;
$botaoEnviar = isset($_REQUEST['T204_Id']) ? 'Alterar' : 'Adicionar';
include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoProcurarProduto">
EOT;
include('mobile/tablet/pedido/inc.header.bar.php');
/*
/	INSERT
*/
if($_REQUEST['insert'] == true){	
	$erro = '';
	
	if($_REQUEST['descricao'] != ''){ 
		$descricao = $_REQUEST['descricao']; 
	} else { 
		$erro .= "Campo descrição deve ser preenchido\\n"; 
	}
	
	if($_REQUEST['preco'] != 0){ 
		$preco = gCorrigeNumeroInverte($_REQUEST['preco']); 
	} else { 
		$erro .= "Campo preço deve ser preenchido"; 
	}
	$tipo = $_REQUEST['tipo'];
	$observacao = isset($_REQUEST['observacao']) ? $_REQUEST['observacao'] : '';	
	if($erro == ''){
		if($T204_Id){
			$SQL = <<<EOT
				UPDATE T204 
				SET T204_Descricao = '{$descricao}', T204_Observacao = '{$observacao}', T204_Valor_Unitario = '{$preco}', T204_T209_Id = '{$tipo}'
				WHERE T204_Id = '{$T204_Id}'
EOT;
		} else {
			$SQL = <<<EOT
				INSERT INTO T204 (T204_Descricao, T204_Observacao, T204_Valor_Unitario, T204_T209_Id, T204_T006_Id) 
				VALUES ('{$descricao}', '{$observacao}', '{$preco}', '{$tipo}', '{$T006_Id}')
EOT;
		}
		mysql_query($SQL);
		if(mysql_error()){
			echo mysql_error();
		} else {
			mysqli_query("CALL T006_Gravar_Totalizacao_4('{$T006_Id}')");
			mysqli_query("CALL T005_Gravar_Totalizacao_4('{$T005_Id}')");
			$script = "\$.mobile.changePage('./pedidoAgregados.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}{$acao}', {transition: 'none'} );";
		}
	} else {
		$script = "alert('{$erro}');";
	}
}

$sqlT204 = mysql_query("SELECT * FROM T204 LEFT JOIN T209 ON T209_Id = T204_T209_Id WHERE T204_Id = '{$T204_Id}'");
$T204 = mysql_fetch_array($sqlT204);

$sqlT209 = mysql_query("SELECT * FROM T209");
$selectTipoAgregado = '';
while($T209 = mysql_fetch_array($sqlT209)){
	$selectTipoAgregado .= "<option value='{$T209['T209_Id']}'".($T209['T209_Id'] == $T204['T204_T209_Id'] ? 'selected="selected"' : '').">{$T209['T209_Nome']}</option>";
}
/*
/		FORM
*/
echo <<<EOT
	<div data-role="content">
		<form action="./pedidoAgregados.php?T005_Id={$T005_Id}&T006_Id={$T006_Id}&T204_Id={$T204_Id}&insert=true{$acao}" method="post" data-ajax="false">
			<div class="ui-grid-b" style="width:100%;">
				<div data-role="fieldcontain">
					<div data-role="fieldcontain">
						<label for="tipo" style="width:10%;">
							Produto
						</label>
						<select id="tipo" name="tipo">
							{$selectTipoAgregado}
						</select>
					</div>
				</div>
				<div data-role="fieldcontain">
					<fieldset data-role="controlgroup">
						<label for="descricao" style="width:10%;">
							Descrição
						</label>
						<input id="descricao" name="descricao" value="{$T204['T204_Descricao']}" type="text" style="width:40%;" />
					</fieldset>
				</div>
				<div data-role="fieldcontain">
					<fieldset data-role="controlgroup">
						<label for="observacao" style="width:10%;">
							Observação
						</label>
						<textarea placeholder="" name="observacao" id="observacao" value="{$T204['T204_Observacao']}" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">{$T204['T204_Observacao']}</textarea>
					</fieldset>
				</div>
				<div data-role="fieldcontain">
					<fieldset data-role="controlgroup">
						<label for="preco" style="width:10%;">
							Preço
						</label>
						<input name="preco" id="preco" placeholder="" value="{$T204['T204_Valor_Unitario']}"  type="text" style="width:30%;"/>
					</fieldset>
				</div>
			</div>
			<div class="ui-grid-c">
				<div class="ui-block-a">
					<a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './pedidoDelProduto.php?T006_Id={$T006_Id}&T005_Id={$T005_Id}{$acao}', {transition: 'slide'} );">
						Voltar
					</a>
				</div>
				<div class="ui-block-b">
					<input value="{$botaoEnviar}" type="submit" />
				</div>
			</div>
		</form>
EOT;
/*
/		GRID
*/
echo <<<EOT
		<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 10px;">
			<li data-role="list-divider" role="heading">
				<table style="width: 100%";>
					<tr>
						<td style="width: 15%; text-align: center;">Tipo</td>
						<td style="width: 20%; text-align: center;">Descrição</td>
						<td style="width: 50%; text-align: center;">Observação</td>
						<td style="width: 15%; text-align: right;">Preço R$</td>
					</tr>
				</table>
			</li>
EOT;

$res = mysql_query("SELECT * FROM T204 LEFT JOIN T209 ON T209_Id = T204_T209_Id WHERE T204_T006_Id = '{$T006_Id}'");
while ($row = mysql_fetch_array($res)) {
	$row['T204_Valor_Unitario'] = gCorrigeNumero($row['T204_Valor_Unitario']);
	echo <<<EOT
			<li data-theme="c" data-icon="false">
				<a href="./pedidoAgregados.php?T006_Id={$T006_Id}&T005_Id={$T005_Id}&T204_Id={$row['T204_Id']}{$acao}" data-ajax="false" data-transition="slide">
					<table style="width: 100%";>
						<tr>
							<td style="width: 15%; text-align: center;">{$row['T209_Nome']}</td>
							<td style="width: 20%; text-align: center;">{$row['T204_Descricao']}</td>
							<td style="width: 50%; text-align: center;">{$row['T204_Observacao']}</td>
							<td style="width: 15%; text-align: right;">{$row['T204_Valor_Unitario']}</td>
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
		$( document ).undelegate("#pagepedidoProcurarProduto", "pageinit");
		$( document ).delegate("#pagepedidoProcurarProduto", "pageinit", function() {
EOT;

echo <<<EOT
			{$script}
		});	
	</script>
EOT;
