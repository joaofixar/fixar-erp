<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	
	case 'menu':

	$resposta['data'] = <<<EOT
		<button class="bigBtn" id="DE">D.E</button>
		<button class="bigBtn" id="balanco">Balanço</button>
EOT;

$resposta['data'] .= <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    	$('#DE').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./menuDivergenciaEstoque.php?opcao=de&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
    	
    	$('#balanco').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./menuDivergenciaEstoque.php?opcao=balanco&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
    	
    	$('#naoDE').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		window.location = './menu.php';
    	});
	} </script></div>
EOT;
		break;
		
	case 'de':

	$resposta['data'] = <<<EOT
		<div class="titulo"><strong style="font-size: 140%;"><br>Abrir nova divergência de estoque?<br> </br></strong></div>
		<button class="bigBtn" id="simDE">Sim</button>
		<button class="bigBtn" id="naoDE">Não</button>
EOT;

$resposta['data'] .= <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    	$('#simDE').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./divergenciaEstoque.php?opcao=cabecalho&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
    	
    	$('#naoDE').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		window.location = './menu.php';
    	});
	} </script></div>
EOT;
		break;
		
	case 'balanco':

	$resposta['data'] = <<<EOT
		<div class="titulo"><strong style="font-size: 140%;"><br>Abrir novo balanço?<br> </br></strong></div>
		<button class="bigBtn" id="simDE">Sim</button>
		<button class="bigBtn" id="naoDE">Não</button>
EOT;

$resposta['data'] .= <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    	$('#simDE').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		$.getJSON('./balanco.php?opcao=cabecalho&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
			});
    	});
    	
    	$('#naoDE').unbind('click').bind('click', function(e) {
    		modal('Carregando...');
    		window.location = './menu.php';
    	});
	} </script></div>
EOT;
		break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";