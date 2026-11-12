<?php
namespace hardness;

include('conf.php');
$resposta = array('code' => true, 'data' => '');
$resposta['data'] = <<<EOT
	<div class="titulo"><strong style="font-size: 140%;"><br>EMPRÉSTIMOS<br></br></strong></div>
	<button class="bigBtn" id="emprestar">Emprestar</button>
	<button class="bigBtn" id="devolver">Devolver</button>
	<button class="bigBtn" onclick="window.location = './menu.php';">Voltar</button>
EOT;
$resposta['data'] .= <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    	$('#emprestar').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./emprestimoProduto.php?opcao=cracha&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
    	
    	$('#devolver').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./devolucaoProduto.php?opcao=cracha&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
	} </script></div>
EOT;

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";