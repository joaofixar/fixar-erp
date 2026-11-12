<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

$sql = "SELECT * FROM T066 WHERE T066_C007_Id_Retirou = '{$g['usuarioAtual']}';";
$res = mysql_query($sql);
$result = mysql_num_rows($res);
if($result > 0){
	$resposta['data'] = <<<EOT
		<div class="titulo"><strong style="font-size: 140%;"><br>MOVIMENTAÇÃO<br> </br></strong></div>
		<button class="bigBtn" id="retirar">Retirar</button>
		<button class="bigBtn" id="colocar">Colocar ($result)</button>
		<button class="bigBtn" onclick="window.location = './menu.php';">Voltar</button>
EOT;
} else {
	$resposta['data'] = <<<EOT
		<div class="titulo"><strong style="font-size: 140%;"><br>MOVIMENTAÇÃO<br> </br></strong></div>
		<button class="bigBtn" id="retirar">Retirar</button>
		<button class="bigBtn" disabled="disabled" id="colocar">Colocar</button>
		<button class="bigBtn" onclick="window.location = './menu.php';">Voltar</button>
EOT;
}

$resposta['data'] .= <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    	$('#retirar').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./movimentacao.php?opcao=retirarLerLocacao&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
    	
    	$('#colocar').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./movimentacao.php?opcao=colocarProduto&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
	} </script></div>
EOT;

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";