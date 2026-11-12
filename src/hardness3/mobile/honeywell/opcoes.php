<?php 
namespace hardness;

include('conf.php');
global $g;
$resposta = array('code' => true, 'data' => '');
$usarLoginColetor = $g['C031']['usarLoginColetor'];

$sqlSeparacao = mysql_query("SELECT * FROM T005 WHERE T005_C004_Id='{$g['empresaAtual']}' 
	AND (T005_Flag_Status='2' OR T005_Flag_Status='1')
	GROUP BY T005_Id ORDER BY T005_Flag_Status DESC, T005_Id");
	$sep = mysql_num_rows($sqlSeparacao);
	
$sqlOrcamento = mysql_query("SELECT * FROM T003 WHERE T003_Flag_Status_Orcamento = 'I' AND T003_D024_Id IS NULL");
$orc = mysql_num_rows($sqlOrcamento);	

$sqlCancelados = mysql_query("SELECT * FROM T207 WHERE T207_Flag_Status = '5' GROUP BY T207_D009_Id");
$canc = mysql_num_rows($sqlCancelados);
	
	
	
	
/*
Inventário		102
Movimentação	107
Conferência		108
Separação		109
Coletor			113
D.E.			114
Receber Prod.	115
Alocação		116
Empréstimos		117
*/
$resposta['data'] = '<div style="margin-left: 10px;">';
if($usarLoginColetor == 'S'){
	if(isset($g['c029Ids'][109])){
		if($sep > 0) {
			$resposta['data'] .= <<<EOT
				<button class="bigBtn" id="separacao">Separação ({$sep})</button>
EOT;
		} else {
			$resposta['data'] .= '<button class="bigBtn" id="separacao">Separação</button>';		
		}
	}
	if(isset($g['c029Ids'][117])){	
		$resposta['data'] .= '<button class="bigBtn" id="emprestimos">Empréstimos</button>';
	}
	if(isset($g['c029Ids'][109])){
		$resposta['data'] .= '<button class="bigBtn" id="movimentacao">Movimentação</button>';
	}
	if(isset($g['c029Ids'][102])){	
		$resposta['data'] .= '<button class="bigBtn" id="inventario">Inventário</button>';
	}
	if(isset($g['c029Ids'][114])){	
		$resposta['data'] .= '<button class="bigBtn" id="diverEst">D.E.</button>';
	}
	if(isset($g['c029Ids'][115])){	
		$resposta['data'] .= '<button class="bigBtn" id="receberProd">Receber produtos.</button>';
	}
	if(isset($g['c029Ids'][116])){	
		$resposta['data'] .= '<button class="bigBtn" id="alocacao">Alocação</button>';
	}
	if(isset($g['c029Ids'][108])){
		$resposta['data'] .= '<button class="bigBtn" id="conferencia" disabled="disable">Conferência</button>';
	}
	$resposta['data'] .= <<<EOT
		<button class="bigBtn" onclick="window.location = './index.php';">Sair</button>
EOT;
}else{
	//UTILIZA-SE APENAS LOGIN PARA ACESSAR O COLETOR
	$resposta['data'] .= <<<EOT
		<button class="menuBtn" id="separacao"><div style="margin-top: 35px;"><strong>SEPARAÇÃO<br /> <div style="margin-top: 20px; margin-right: 3px;" align="right">{$sep}</div></div></strong></button>
		<button class="menuBtn" id="devolucao">Devolução</button>
		<button class="menuBtn" id="entradas">Entrada de<br /> Produto</button>
		<button class="menuBtn" id="cancelados"><div style="margin-top: 35px;"><strong>CANCELADOS<br /> <div style="margin-top: 20px; margin-right: 3px;" align="right">{$canc}</div></div></strong></button>		
		<button class="menuBtn" id="consulta">Consulta<br /> Produto</button>				
		<button class="menuBtn" id="inventario">Inventário</button>		
		<!--<button class="menuBtn" id="orcamento"><div style="margin-top: 35px;"><strong>ORÇAMENTO<br /> <div style="margin-top: 20px; margin-right: 3px;" align="right">{$orc}</div></div></strong></button>-->
EOT;
	/*$resposta['data'] .= <<<EOT
		<button class="bigBtn" id="emprestimos">Empréstimos</button>
		<button class="bigBtn" id="movimentacao">Movimentação</button>
		<button class="bigBtn" id="inventario">Inventário</button>
		<button class="bigBtn" id="diverEst">D.E.</button>
		<button class="bigBtn" id="receberProd">Receber produtos.</button>
		<button class="bigBtn" id="alocacao">Alocação</button>
		<button class="bigBtn" id="conferencia" disabled="disable">Conferência</button>
EOT;*/
}
$resposta['data'] .= '</div>';

if($g['C031']['modeloColetor'] == 'locacao') {
	$resposta['data'] .= <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    		$('#separacao').unbind('click').bind('click', function(e) {
    			clearTimeout(refreshMenu);
    			if('{$usarLoginColetor}' == true){
    				modal('Carregando...');
	    			$.getJSON('./separacaoPedido.php?opcao=pedidos&callback=?', function(request) {
	    				modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				} else {
					location.href="verificaLogin.php?arquivo=separacaoPedido&case=pedidos";
				}
    		});
EOT;
} else if($g['C031']['modeloColetor'] == 'pedidoProduto') {
	
	$resposta['data'] .= <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	    	$('#separacao').unbind('click').bind('click', function(e) {
	    		clearTimeout(refreshMenu);
	    		if('{$usarLoginColetor}' == true){
	    			modal('Carregando...');
		    		$.getJSON('./separacaoPedido3.php?opcao=pedidos&callback=?', function(request) {
		    			modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
    			} else {
    				location.href = "./verificaLogin.php?arquivo=separacaoPedido3&case=pedidos";
				}
	    	});
EOT;
} else {
	$resposta['data'] .= <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	    	$('#separacao').unbind('click').bind('click', function(e) {
	    		clearTimeout(refreshMenu);
	    		if('{$usarLoginColetor}' == true){
	    			modal('Carregando...');
		    		$.getJSON('./separacaoPedido2.php?opcao=pedidos&callback=?', function(request) {
		    			modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
    			} else {
    				modal('Carregando...');
		    		$.getJSON('./separacaoPedidoLista.php?opcao=autentica&callback=?', function(request) {
		    			modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				}
	    		
	    	});
EOT;
}

$resposta['data'] .= <<<EOT
		$('#alocacao').unbind('click').bind('click', function(e) {
			clearTimeout(refreshMenu);
			if('{$usarLoginColetor}' == true){
				modal('Carregando...');
	    		$.getJSON('./alocacao.php?opcao=selecionarProduto&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			} else {
				location.href="./verificaLogin.php?arquivo=alocacao&case=alocarProduto";
			}
    	});
    	
    	$('#entradas').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			modal('Carregando...');
	    		$.getJSON('./entradaPedidoLista.php?opcao=autentica&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}
    	});
    	
    	$('#orcamento').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			modal('Carregando...');
	    		$.getJSON('./orcamento.php?opcao=autentica&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}
    	});
    	
    	$('#consulta').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			modal('Carregando...');
	    		$.getJSON('./consultaProduto.php?opcao=procura&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}
    	});
    	
    	$('#devolucao').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			modal('Carregando...');
	    		$.getJSON('./devolucaoProdutosLista.php?opcao=autentica&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}
    	});

		$('#cancelados').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			modal('Carregando...');
	    		$.getJSON('./produtosCanceladosPedido.php?opcao=autentica&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}
    	});
    	
    	$('#inventario').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./inventario.php?opcao=balanco&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}else{
				modal('Carregando...');
	    		$.getJSON('./inventarioNovo.php?opcao=autentica&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}
    	});
    	
    	$('#movimentacao').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			location.href="./verificaLogin.php?arquivo=menuMovimentacao&callback=?";
    		}
    	});
    	
    	$('#emprestimos').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		modal('Carregando...');
    		if('{$usarLoginColetor}' == true){
    			$.getJSON('./menuEmprestimoProduto.php?callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			location.href="verificaLogin.php?arquivo=menuEmprestimoProduto";
    		}
    	});
    	
		$('#devolucoes').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuEmprestimoProduto.php?callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			location.href="./verificaLogin.php?arquivo=menuEmprestimoProduto";
    		}
    	});
    	
		$('#diverEst').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuDivergenciaEstoque.php?&opcao=menu&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			location.href="./verificaLogin.php?arquivo=menuDivergenciaEstoque&case=menu&";
    		}
    	});
    	
		$('#receberProd').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./menuReceberProdutos.php?&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		}else{
    			location.href="./verificaLogin.php?arquivo=menuReceberProdutos";
    		}
    	});
    	
    	$('#conferencia').unbind('click').bind('click', function(e) {
    		clearTimeout(refreshMenu);
    		if('{$usarLoginColetor}' == true){
    			modal('Carregando...');
	    		$.getJSON('./conferencia.php?opcao=&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}else{
				location.href="./verificaLogin.php?arquivo=conferencia";
			}
    	});
    	
    	refreshMenu = setTimeout(refreshOpcoes, 30000);
						
		//$('#scan').unbind('keypress');
		//$('#scan').bind('keypress', function(e) {
		//	if (e.keyCode == 13) {
		//   	modal('Verificando...');
		//    	$.getJSON('./buscaProduto.php?opcao=busca&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
		//    		modalRemove();
		//    		$('#scan').val('');
		//			$('#contentMenu').empty().html(request.data);
		//		});
		//	}
		//});
		// (não perder o foco do campo scan)
		$('#scan').focus();

	} </script></div>
EOT;

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";