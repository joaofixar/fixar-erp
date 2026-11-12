<?php
namespace hardness;

include('conf.php');

global $g;

$scan = $_REQUEST['scan'];

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'autentica':
		if (isset($_REQUEST['scan'])) {
			$scan = $_REQUEST['scan'];
			$erro = '';
			// Login
			@session_destroy();
			@session_start();
			$e=1;
			$l=$_REQUEST['scan'];
			
			if (empty($l)) {
				$erro = 'ERRO! Login Inválido.';
			}
			$cC004=mysql_query("select * from C004 where C004_Id='$e'");
			$mC004=mysql_fetch_array($cC004);
			if (empty($mC004['C004_Banco_Configuracao'])) {
				$erro = 'Erro! Banco de configuração nao definido.';
			} else {
				$cBd_Configuracao=$mC004['C004_Banco_Configuracao'];
				$cC020=mysql_query("select C020_Modelo_Sistema_Nfe from C020 where C020_C004_Id='$mC004[C004_Id]'");
				$mC020=mysql_fetch_array($cC020);
				// Consulta a Senha Digitada do Usuario Selecionado
				$Sql=mysql_query("select * from C007
							              left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
							              left join C021 on C021_Id=C019_C021_Id
							              where C007_Codigo_Barras='$l'
							                and C007_Ativo='S'
							                and (C007_D024_Id=0 or C007_D024_Id is null)");
				if (mysql_num_rows($Sql)==0) {
					$erro = 'ERRO! Login Inválido.';
				} else {
					$usuarioAtual = mysql_fetch_array($Sql);
				}
			}
			 
			if (empty($erro)) {
				$sqlUsuarioAtual = "SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id={$usuarioAtual['C007_Id']}";
				$resUsuarioAtual = mysql_query($sqlUsuarioAtual);
				$rowUsuarioAtual = @mysql_fetch_array($resUsuarioAtual);
				
				$resposta['data'] = <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						modal('Carregando...');
			    		$.getJSON('./entradaPedidoLista.php?opcao=pedidos&callback=?', function(request) {
			    			modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
						$('#barraTop').show();
						$('#barraTopCentral').hide();
						$('#barraTopUsuario').empty().html('Usuário: ' + '{$rowUsuarioAtual['C007_Primeiro_Nome']}');
					} </script></div>		
EOT;
			} else {
				$resposta['data'] = <<<EOT
					<div style="margin-bottom: 50%;">
	    			<!--<img src="http://www.carsigma.com.br/images/logo.jpg" style="width: 100%; height: 70px;" />-->
	    			</div>
					<div class="alerta" style="height: 50px;"><strong style="font-size: 125%;"><br>{$erro}<br> </br></strong></div>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						modal('Carregando...');
			    		$.getJSON('./entradaPedidoLista.php?opcao=autentica&callback=?', function(request) {
			    			modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
					}, 1100);
					} </script></div>				
EOT;
			}
		} else {
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#barraTop').hide();
			    	$('#scan').unbind('keypress');
					$('#scan').bind('keypress', function(e) {
						if (e.keyCode == 13) {
					    	modal('Verificando...');
					    	$.getJSON('./entradaPedidoLista.php?opcao=autentica&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
					    		modalRemove();
					    		$('#scan').val('');
								$('#contentMenu').empty().html(request.data);
							});
						}
					});
				} </script></div>
				<div>
	    		<!--<img src="http://www.carsigma.com.br/images/logo.jpg" style="width: 100%; height: 70px;" />-->
	    		</div>
	    		<div class="alerta" style="margin-top: 30%; width: 92%; margin-left: 8px; height: 50px;">	    		
	    		<div style="margin-top: 17px;"><strong style="font-size: 125%;">LEIA O CODIGO DE ACESSO</strong></div>
	    		</div>
	    		<button class="voltarLarge" id="retornarMenu" style="margin-bottom: 3px; margin-left: 8px;">Voltar</button>
	    		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#retornarMenu').unbind('click').bind('click', function() {
	    				 window.location="./menu.php";
	    			});
    			} </script></div>	
EOT;
		}
	break;

	case 'produtos':
		$T005_Id = $_REQUEST['T005_Id'];
		
		if(isset($_REQUEST['T138_Id'])){
			$T138_Id = $_REQUEST['T138_Id'];
			mysql_query("UPDATE T138 SET T138_Quantidade_Separado = '{$_REQUEST['quantidadeProduto']}' WHERE T138_Id = '{$T138_Id}'");
		}
		
		$T138 = <<<EOT
		SELECT * FROM T138 	
		LEFT JOIN D009 ON D009_Id=T138_D009_Id 
		LEFT JOIN D049 ON D049_Id=D009_D049_Id 
		LEFT JOIN D001 ON D001_Id=D049_D001_Id 
		LEFT JOIN D082 ON D082_Id=D049_D082_Id
		WHERE T138_T005_Id = '{$T005_Id}'
EOT;
		$resT138 = mysql_query($T138);
		//Após todos os produtos serem recebidos, o processo de recebimento é finalizado
		if(mysql_numrows($resT138) == 0){			
			//$finalizarSeparacao = mysql_query("UPDATE T005 SET T005_Flag_Status = '10', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = '{$T005_Id}'");
			$resposta['data'] .= <<<EOT
				<div style="margin-top: 20%" class="tituloBlue">
					<strong style="font-size: 120%;"><div style="margin-top: 35px; width: 103.6%;">SEM PRODUTOS PARA RECEBER</div></strong>
				</div>		
EOT;
		}
		$resposta['data'] .= <<<EOT
				<button class="voltarLarge" id="retornarMenu" style="margin-left: 9px;">Voltar</button>
				<style>
				.tabela {
					background-color: #F6F6F6; 
					border: 0px solid #8F859C;
					border-top: 1px solid #444;
					margin: 3px;
					padding: 1px;
					align: center;
					
				}
				.lista {
					border-top: 1px solid #8F859C;
					padding: 2px;
					margin-left: 2px;
					 					
				}
				</style>
				<table class="tabela" style="width: 98%; margin-left: 2px;" align="center" cellspacing="0" cellpadding="0">			
EOT;
		while($row = mysql_fetch_array($resT138)) {
			$quantidade = number_format($row['T138_Quantidade'], 2);
				$resposta['data'] .= <<<EOT
					<tr style="margin-top: -1px;" T138_Id="{$row['T138_Id']}">
						<td class="lista">
							<strong>{$row['D001_Descricao_Produto']}</strong><br> COD: {$row['D001_Codigo_Produto']}<br> QTDE: {$quantidade} - SEPARADO: {$row['T138_Quantidade_Separado']}
						</td>
					</tr>
EOT;
		}
		$resposta['data'] .= <<<EOT
			</table>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#retornarMenu').unbind('click').bind('click', function() {
    				 window.location="./menu.php";
    			});
					
    			$('tr').unbind('click').unbind('click').bind('click', function(e) {
    				var id = $(this).closest('tr').attr('T138_Id');
    				if(id){
						modal('Verificando...');					
	    				$.getJSON('./entradaPedidoLista.php?opcao=busca&T138_Id=' + encodeURIComponent(id) + '&T005_Id={$T005_Id}&callback=?', function(request) {
	    					modalRemove();
	    					$('#contentMenu').empty().html(request.data);
						});
					}
    			});
			} </script></div>		
EOT;
	break;
	
	case 'pedidos':
		$scan = $_REQUEST['scan'];
	  	if(!isset($scan)){
			$resposta['data'] .= <<<EOT
			<div class="alerta" style="margin-top: 35%; margin-left: 6px;">
				<div style="margin-top: 20px;"> <strong style="font-size: 120%;">LEIA O CÓDIGO DO PEDIDO<br> <br /></strong></div>
			</div>
			<button style="margin-left: 6px; width: 94.5%;" class="voltarLarge" id="voltar">Voltar</button>
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
				    	modal('Verificando...');
				    	$.getJSON('./entradaPedidoLista.php?opcao=pedidos&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
				    		modalRemove();
				    		$('#scan').val('');
							$('#contentMenu').empty().html(request.data);
						});
					}
				});
				$('#scan').focus();
				
				$('#voltar').unbind('click').bind('click', function() {
    				 window.location="./menu.php";
    			});
			}</script></div>
EOT;
		} else {
			$sqlT005 = mysql_query("SELECT * FROM T005 WHERE T005_Id='{$scan}' GROUP BY T005_Id ORDER BY T005_Flag_Status DESC, T005_Id LIMIT 1");
			$status = mysql_fetch_array($sqlT005);
			$T005_Id = $status['T005_Id'];
			if($status['T005_Flag_Status'] == 1 || $status['T005_Flag_Status'] == 2) {
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#barraTopPedido').html('Pedido: {$T005_Id}');
		    			modal('Pedido {$T005_Id}');
		    			$.getJSON('./entradaPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
		    				modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
				    } </script></div>
EOT;
			} else {
				$resposta['data'] .= <<<EOT
					<div style="margin-top: 45%" class="titulo">
					<strong style="font-size: 140%;"><br>PEDIDO NÃO DISPONÍVEL<br> </br></strong></div>			
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						setTimeout("reload()",1000);
						function reload(){
							window.location="./menu.php";
						}
					} </script></div>
EOT;
			}
		}			  			
	break;
	
	case 'busca':
		$T138_Id = $_REQUEST['T138_Id'];
		$T005_Id = $_REQUEST['T005_Id'];
		
		if(isset($T138_Id)) {
			$T138 = mysql_query("SELECT * FROM T138
			LEFT JOIN D009 ON D009_Id=T138_D009_Id 
			LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			WHERE T138_Id = '{$T138_Id}'");
			
			if(mysql_num_rows($T138) > 0){
				$encontrado = true;
				$row = mysql_fetch_array($T138);
				$separar = number_format($row['T138_Quantidade'], 2);								
				$resposta['data'] = <<<EOT
					<div class="produto" style="font-size: 120%; margin-left: 6px; margin-top: 3px; padding: 2px;">
						<strong style="font-size: 110%;">{$row['D001_Codigo_Produto']}</strong><br />
						<strong style="font-size: 110%;">{$row['D001_Descricao_Produto']}</strong><br />
						<strong style="font-size: 110%;">{$row['D082_Marca']}</strong><br /><br>
						<strong style="font-size: 120%;">QUANTIDADE {$separar}</strong><br />
						<br /><strong style="font-size: 120%;">Receber </strong><input type="text" id="quantidadeMarcada">					
					</div>					
					<div style="margin-left: 3px;">	
						<button class="voltar" style="width: 47%;" id="receberProduto">RECEBER</button>
						<button class="voltar" style="width: 47%;" id="voltar">Voltar</button>
					</div>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#quantidadeMarcada').focus();
	    				$('#receberProduto').unbind('click').bind('click', function() {
	    				if($('#quantidadeMarcada').val() > 0){
	    					if (confirm('Deseja dar entrada no produto?')) {
	    		 				modal('Verificando...');
	    						$.getJSON('./entradaPedidoLista.php?opcao=produtos&T138_Id={$T138_Id}&T005_Id={$T005_Id}&quantidadeProduto='+ encodeURIComponent($('#quantidadeMarcada').val()) +'&callback=?', function(request) {
	    							modalRemove();
	    							$('#scan').val('');
	    							$('#contentMenu').empty().html(request.data);
								});
							}
							return false;
						} else {
							modal('A quantidade deve ser maior que zero!');
							setTimeout(function() {
								modalRemove();
							}, 1500);
							$('#quantidadeMarcada').focus();
						}
	    				});
	
	    				$('#voltar').unbind('click').bind('click', function(e) {
							modal('Aguarde...');
	    					$.getJSON('./entradaPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
	    					 	modalRemove();
	    					 	$('#scan').val('');
	    						$('#contentMenu').empty().html(request.data);
							});
							return false;
	    				});
	    				
				    } </script></div>
EOT;
			}
		}
		if($encontrado == false){
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					modal('Produto não encontrado');
					setTimeout("reload()",1250);
					function reload(){
						$.getJSON('./entradaPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
		    				modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
					}
				} </script></div>
EOT;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";