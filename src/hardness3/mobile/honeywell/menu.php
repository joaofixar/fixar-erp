<?php
namespace hardness;

include('conf.php');
$usarLoginColetor = $g['C031']['usarLoginColetor'];
if($usarLoginColetor == 'N'){
	if($_REQUEST['arquivo']==""){
		$session = @session_destroy();
		$_SESSION = array();
		$g['usuarioAtual']="";
	}
}

include('inc.header.php');
global $g;


$resUsuarioAtual = mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id={$g['usuarioAtual']}");

echo <<<EOT
	<input name="scan" id="scan" value="" type="text" style="width: 97%; position: absolute; top: -100px;" />
	<div id="barraTopCentral" class="barraTop" style="text-align: center;">Hardness Sistemas</div>
	<div id="barraTop" class="barraTop">		
		<div id="barraTopUsuario" style="float: left; margin: 2px;"></div>
		<div id="barraTopPedido" style="float: right; margin: 2px;"></div>
	</div>
    <div id="contentMenu"></div>
EOT;

if(@mysql_num_rows($resUsuarioAtual) > 0){
	$rowUsuarioAtual = mysql_fetch_array($resUsuarioAtual);
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {			
			$('#barraTopUsuario').empty().html('Usuário: ' + '{$rowUsuarioAtual['C007_Primeiro_Nome']}');
		} </script></div>
EOT;
} else {
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {			
			$('#barraTopCentral').show();
			$('#barraTop').hide();
		} </script></div>		
EOT;
}

if (!isset($_REQUEST['arquivo'])) {
echo <<<EOT
	</div>
    <script>
    	function refreshOpcoes() {
    		modal('Carregando...');
    		$.getJSON('./opcoes.php?&callback=?', function(request) {
    			modalRemove();
				$('#contentMenu').empty().html(request.data);
				$('#barraTopPedido').html('');
			});
		}
		
    	$(document).ready(function() {
			refreshOpcoes();
    		
    		// focus sempre no scan
    		$(document).unbind('click');
    		$(document).bind('click', function(e) {
    			if (e.target.id == 'separacaoPedidoQtdProd' || e.target.id == 'balanco' || e.target.id == 'codProduto' || e.target.id == 'valorRetirou'|| e.target.id == 'valorColocado'|| e.target.id == 'minimo' || e.target.id == 'maximo' || e.target.id == 'agrupamento' || e.target.id == 'separarOld' || e.target.id == 'qntEmp' || e.target.id == 'qntDev' || e.target.id == 'qntDivEst' || e.target.id == 'qntRecebida') {
    				return true;
    			} else {
    				$('#scan').focus();
    				return false;
    			}
    		});
    	});
    	
    	function modal(text) {
    		var \$modal = $('<div>').css({'text-align': 'center', 'padding-top': '100px', 'color': 'white', 'font-size': '1.2em', 'font-weight': 'bold', 'width': '100%', 'height': $(document).height() + 'px', 'background-color': 'black', 'z-index': '999999', 'opacity': '0.7', 'top': '0', 'left': '0', 'position': 'absolute'}).attr('id', 'modal').empty().html(text).bind('click', function() { return false; });
			$('body').append(\$modal);
    	}
    	
    	function modalRemove() {
    		$('#modal').remove();
    	}
    </script>
EOT;
}else{
	echo <<<EOT
    <script>
    	function modal(text) {
    		var \$modal = $('<div>').css({'text-align': 'center', 'padding-top': '100px', 'color': 'white', 'font-size': '1.2em', 'font-weight': 'bold', 'width': '100%', 'height': $(document).height() + 'px', 'background-color': 'black', 'z-index': '999999', 'opacity': '0.7', 'top': '0', 'left': '0', 'position': 'absolute'}).attr('id', 'modal').empty().html(text).bind('click', function() { return false; });
			$('body').append(\$modal);
    	}
    	
    	function modalRemove() {
    		$('#modal').remove();
    	}
    	
    	$(document).ready(function() {   		
    		// focus sempre no scan
    		$(document).unbind('click');
    		$(document).bind('click', function(e) {
    			if (e.target.id == 'separacaoPedidoQtdProd' || e.target.id == 'balanco' || e.target.id == 'codProduto' || e.target.id == 'valorRetirou'|| e.target.id == 'valorColocado'|| e.target.id == 'minimo' || e.target.id == 'maximo' || e.target.id == 'agrupamento' || e.target.id == 'separarOld' || e.target.id == 'qntEmp' || e.target.id == 'qntDev' || e.target.id == 'qntDivEst' || e.target.id == 'qntRecebida') {
    				return true;
    			} else {
    				$('#scan').focus();
    				return false;
    			}
    		});
    	});
		$('#scan').focus();
    </script>
EOT;
}

if ($usarLoginColetor == false){
	
	$arquivo = $_REQUEST['arquivo'];
	$case = $_REQUEST['case'];
	
	switch($arquivo){
		case 'separacaoPedido':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
    			$.getJSON('./separacaoPedido.php?opcao={$case}&callback=?', function(request) {
    				modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
			
		case 'separacaoPedido3':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
				$.getJSON('./separacaoPedido3.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
		
		case 'separacaoPedido2':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
	    		$.getJSON('./separacaoPedido2.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
		break;
			
		case 'separacaoPedidoLista':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
	    		$.getJSON('./separacaoPedidoLista.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
		break;
			
		case 'alocacao':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
	    		$.getJSON('./alocacao.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
			
		case 'inventario':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
	    		$.getJSON('./inventario.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
		
		case 'menuMovimentacao':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
	    		$.getJSON('./menuMovimentacao.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
		
		case 'menuEmprestimoProduto':
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$.getJSON('./menuEmprestimoProduto.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
		
		case 'menuDivergenciaEstoque':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
	    		$.getJSON('./menuDivergenciaEstoque.php?&opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
		
		case 'menuReceberProdutos':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
	    		$.getJSON('./menuReceberProdutos.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
		
		case 'conferencia':
			echo <<<EOT
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
	    		$.getJSON('./conferencia.php?opcao={$case}&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
			break;
	}
}
