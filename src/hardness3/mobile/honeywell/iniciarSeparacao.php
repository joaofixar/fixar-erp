<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

$resposta['data'] = <<<EOT
	<button class="bigBtn" id="separacao">Iniciar Separação</button>
    <button class="bigBtn" onclick="window.location = './index.php';">Sair</button>
EOT;

$resposta['data'] .= <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    	$('#separacao').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./separacaoPedido.php?opcao=pedidos&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
	} </script></div>
EOT;

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
