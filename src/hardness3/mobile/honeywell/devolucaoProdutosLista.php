<?php
namespace hardness;

include('conf.php');

global $g;

$scan = $_REQUEST['scan'];

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'autentica':
		if (isset($_REQUEST['scan'])) {
			//$scan = $_REQUEST['scan'];
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
				$_SESSION['Usuario_Atual']=$usuarioAtual['C007_Id'];
				$resposta['data'] = <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						modal('Carregando...');
			    		$.getJSON('./devolucaoProdutosLista.php?opcao=pedidos&callback=?', function(request) {
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
			    		$.getJSON('./devolucaoProdutosLista.php?opcao=autentica&callback=?', function(request) {
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
					    	$.getJSON('./devolucaoProdutosLista.php?opcao=autentica&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
	
	case 'codigo':
		$D001_Id = $_REQUEST['D001_Id'];
		$T005_Id = $_REQUEST['T005_Id'];
		if(isset($_REQUEST['scan'])){
			$scan = $_REQUEST['scan'];
			
			$updateD001 = "UPDATE D001 SET D001_Codigo_Barras = '{$scan}' WHERE D001_Id = '{$D001_Id}'";
			mysql_query($updateD001);
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					modal('Verificando...');
			    	$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
			    		modalRemove();
			    		$('#scan').val('');
						$('#contentMenu').empty().html(request.data);
					});		
				} </script></div>
EOT;
		} else {
			$D001 = mysql_query("SELECT * FROM D001 LEFT JOIN D049 ON D049_Id = D049_D001_Id LEFT JOIN D082 ON D082_Id = D049_D082_Id WHERE D001_Id = '{$D001_Id}'");
			$res = mysql_fetch_array($D001);
			$resposta['data'] = <<<EOT
				<div class="produto"><br />
					<strong style="font-size: 100%;">{$res['D001_Codigo_Produto']}</strong><br />
					<strong style="font-size: 100%;">{$res['D001_Descricao_Produto']}</strong><br />
					<strong style="font-size: 100%;">{$res['D082_Marca']}</strong><br />
					<div class="alerta" style="margin: 15% 0 10% 0;"><strong style="font-size: 120%;"><br>LEIA O CODIGO DE BARRAS<br> </br></strong></div>
					<button class="bigBtn" id="voltar" style="margin-left: -2px;">Voltar</button>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#voltar').unbind('click').bind('click', function(e) {
						modal('Verificando...');
				    	$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
				    		modalRemove();
				    		$('#scan').val('');
							$('#contentMenu').empty().html(request.data);
						});
					});
					
					$('#scan').unbind('keypress');
					$('#scan').bind('keypress', function(e) {
						if (e.keyCode == 13) {
							if (confirm('Deseja associar o codigo ' + $('#scan').val() + ' a este produto?')) {
			    				modal('Verificando...');
			    				$.getJSON('./devolucaoProdutosLista.php?opcao=codigo&D001_Id={$D001_Id}&T005_Id={$T005_Id}&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
			    					modalRemove();
			    					$('#scan').val('');
									$('#contentMenu').empty().html(request.data);
								});
							}
							$('#scan').val('');
							return false;
						}
					});
					// (não perder o foco do campo scan)
		    		$('#scan').focus();
	    		} </script></div>
EOT;
		}
		
	break;
	
	case 'produtos':
		$T005_Id = $_REQUEST['T005_Id'];
		$T006_Id = $_REQUEST['T006_Id'];
		$T185_Id = isset($_REQUEST['T185_Id']) ? $_REQUEST['T185_Id'] : false;

		if($T185_Id){
			mysql_query("UPDATE T185 SET T185_Qtd_Separado = '0' WHERE T185_Id = '{$T185_Id}'");
		}

		$sqlProdutos = <<<EOT
			SELECT * FROM T006 
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN T185 ON T185_T006_Id=T006_Id
			LEFT JOIN T066 ON T066_Id=T185_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			WHERE 
			T006_T005_Id = '{$T005_Id}'
			AND T006_Quantidade_Separacao > 0
EOT;
		$resProdutos = mysql_query($sqlProdutos);
		if(mysql_numrows($resProdutos) == 0){
			//Após todos os produtos serem separados ou notificados com divergencia a separacao do pedido é finalizada
			//$finalizarSeparacao = mysql_query("UPDATE T005 SET T005_Flag_Status = '10', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = '{$T005_Id}'");
			$resposta['data'] .= <<<EOT
				<div style="margin-top: 20%" class="titulo">
					<strong style="font-size: 140%;"><br>Sem produtos cancelados</br> <br /></strong>
				</div>		
EOT;
		}
		$resposta['data'] .= <<<EOT
				<button class="voltarLarge" id="retornarMenu" style="margin-left: 10px; margin-bottom: 1px;">Voltar</button>
				<style>
				.tabela {
					background-color: #F6F6F6; 
					border: 0px solid #8F859C;
					border-top: 1px solid #444;
					margin: 1px;
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
		while($row = mysql_fetch_array($resProdutos)) {
			$separado = number_format($row['T185_Qtd_Separado'], 2);
			$quantidade = gCorrigeNumero($row['T006_Quantidade']);
				$resposta['data'] .= <<<EOT
					<tr style="margin-top: -1px;">
						<td class="lista">
							<strong>{$row['T006_Descricao_Produto']}</strong><br> COD: {$row['D001_Codigo_Barras']}<br>SEPARADO: {$row['T006_Quantidade_Separacao']}
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
			   			
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
	    				modal('Verificando...');
	    				$.getJSON('./devolucaoProdutosLista.php?opcao=devolver&T005_Id={$T005_Id}&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
	    					modalRemove();
	    					$('#scan').val('');
							$('#contentMenu').empty().html(request.data);
						});
					}
				});
				// (não perder o foco do campo scan)
	    		$('#scan').focus();
			} </script></div>		
EOT;
	break;
	
	case 'pedidos':
		$scan = $_REQUEST['scan'];
	  	if(!isset($scan)){
	  		$resposta['data'] .= <<<EOT
			<div class="alerta" style="margin-top: 20%; margin-left: 6px;">
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
				    	$.getJSON('./devolucaoProdutosLista.php?opcao=pedidos&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
				mysql_query("UPDATE T005 SET T005_C007_Id_Separou = {$g['usuarioAtual']}, T005_Flag_Status = '2', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = {$T005_Id}");
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#barraTopPedido').html('Pedido: {$T005_Id}');
		    			modal('Pedido {$T005_Id}');
		    			$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
		    				modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
				    } </script></div>
EOT;
			} else {
				$resposta['data'] .= <<<EOT
					<div style="margin-top: 45%" class="titulo">
					<strong style="font-size: 140%;"><br>Pedido não disponível<br>para devolver<br> </br></strong></div>			
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						setTimeout("reload()",1000);
						function reload(){
							$.getJSON('./devolucaoProdutosLista.php?opcao=pedidos&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
						}
					} </script></div>
EOT;
			}
		}			  			
	break;
	
	case 'devolver':
		$scan = $_REQUEST['scan'];
		$codigo = isset($_REQUEST['codigo']) ? $_REQUEST['codigo'] : false;
		$T005_Id = $_REQUEST['T005_Id'];
		$T185_Id = isset($_REQUEST['T185_Id']) ? $_REQUEST['T185_Id'] : false;
		if($T185_Id != false){			
			$T006_Id = $_REQUEST['T006_Id'];
			$T185 = "SELECT T185_Qtd_Para_Separar, T185_Qtd_Separado, T185_Qtd_Devolvido FROM T185 WHERE T185_Id = '{$T185_Id}'";
			$T185 = mysql_query($T185);
			$resT185 = mysql_fetch_array($T185);		
			$quantidade = $resT185['T185_Qtd_Separado'] - 1;
			$devolvido = $resT185['T185_Qtd_Devolvido'] + 1;		
			mysql_query("UPDATE T185 SET T185_Qtd_Separado = '{$quantidade}', T185_Qtd_Devolvido = '{$devolvido}' WHERE T185_Id = '{$T185_Id}';");
			mysql_query("INSERT INTO T205 (
							T205_T006_Id, T205_C007_Id, T205_Quantidade, T205_Data, T205_Operacao
							) VALUES (
							'{$T006_Id}', '{$g['usuarioAtual']}', '+1', CURDATE(), 'E'
						)");
			//Atualiza o controle do produto T207
			mysql_query("UPDATE T207 SET T207_Flag_Status = '4', T207_C007_Id = '{$g['usuarioAtual']}' WHERE T207_Flag_Status = '2' AND T207_T006_Id = '{$T006_Id}' LIMIT 1");
		}
		if(isset($scan)) {
			$T006 = mysql_query("SELECT * FROM T006 
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN T185 ON T185_T006_Id=T006_Id
			LEFT JOIN T066 ON T066_Id=T185_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			WHERE T006_T005_Id = '{$T005_Id}'
			AND T185_Qtd_Para_Separar IS NOT NULL ORDER BY T185_Id ASC");
			while($row = mysql_fetch_array($T006)){
				if($row['D001_Codigo_Produto'] == $scan || $row['D001_Codigo_Barras'] == $scan){
					//Inclui o produto na separação
					$separou = $row['T006_Quantidade_Separacao'];
					$devolvido = $row['T185_Qtd_Devolvido'];
					if($T185_Id == false && $row['T006_Quantidade_Separacao'] > 0){
						$T185 = "SELECT T185_Qtd_Separado, T185_Qtd_Devolvido FROM T185 WHERE T185_Id = '{$row['T185_Id']}'";
						$T185 = mysql_query($T185);
						$resT185 = mysql_fetch_array($T185);
						$quantidade = $resT185['T185_Qtd_Separado'] - 1;
						$devolvido = $resT185['T185_Qtd_Devolvido'] + 1;
						mysql_query("UPDATE T185 SET T185_Qtd_Separado = '{$quantidade}', T185_Qtd_Devolvido = '{$devolvido}' WHERE T185_Id = '{$row['T185_Id']}';");
						$separou = $quantidade;
						mysql_query("INSERT INTO T205 (
										T205_T006_Id, T205_C007_Id, T205_Quantidade, T205_Data, T205_Operacao
										) VALUES (
										'{$row['T006_Id']}', '{$g['usuarioAtual']}', '+1', CURDATE(), 'E'
									)");
						//Atualiza o controle do produto T207
						mysql_query("UPDATE T207 SET T207_Flag_Status = '4', T207_C007_Id = '{$g['usuarioAtual']}' WHERE T207_Flag_Status = '2' AND T207_T006_Id = '{$row['T006_Id']}' LIMIT 1");
					}		
					//VERIFICAÇÃO DAS QUANTIDADES ATUALIZADO
					$T185 = "SELECT T185_Qtd_Para_Separar, T185_Qtd_Separado, T185_Qtd_Devolvido FROM T185 WHERE T185_Id = '{$row['T185_Id']}'";
					$T185 = mysql_query($T185);
					$resT185 = mysql_fetch_array($T185);
					//produto lido esta na lista
					$encontrado = true;
					$resposta['data'] = <<<EOT
						<div class="alerta" style="margin-bottom: 3px; width: 92.3%; margin-left: 8px;"><strong style="font-size: 100%;">LEIA O PRODUTO PARA DEVOLVER</strong><br /></div>
						<div class="produto" style="font-size: 120%; margin-left: 8px;">							
							<!--<strong>{$row['D001_Codigo_Produto']}</strong><br />-->
							<strong>{$row['D001_Descricao_Produto']}</strong><br />
							<strong>{$row['D082_Marca']}</strong><br />
							<strong>ESTOQUE : {$row['D009_Quantidade_Estoque']}</strong><br />
							<strong style="font-size: 115%;">QUANTIDADE: {$row['T006_Quantidade']}</strong>
							<table align="center">
							<tr>														
							<td>
							<strong style="font-size: 120%;">SEPARADO<br />{$separou}</strong><br />
							</td>
							<td style="width: 20px;"></td>
							<td>
							<strong style="font-size: 120%;">DEVOLVIDO<br />{$devolvido}</strong><br />
							</td>
							</tr>
							</table>
						</div>
						<div style="margin-left: 5px;">
							<button class="voltar" style="width: 46.5%;" id="voltar">Voltar</button>
							<button class="voltar" style="width: 46.5%;" id="historico">Histórico</button>
						</div>						
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var separado = '{$resT185['T185_Qtd_Separado']}';
							var codigoProduto = '{$row['D001_Codigo_Barras']}';
							$('#scan').unbind('keypress');
							$('#scan').bind('keypress', function(e) {
								if (e.keyCode == 13) {
									if(separado > 0){
										if(codigoProduto == $('#scan').val()) {
								    		modal('Verificando...');
									    	$.getJSON('./devolucaoProdutosLista.php?opcao=devolver&scan=' + encodeURIComponent($('#scan').val()) + '&T005_Id={$T005_Id}&T185_Id={$row['T185_Id']}&codigo={$row['D001_Codigo_Barras']}&T006_Id={$row['T006_Id']}&callback=?', function(request) {
									    		modalRemove();
									    		$('#scan').val('');
												$('#contentMenu').empty().html(request.data);
											});
										} else {
											modal('Produto Incorreto!');
											setTimeout(function() {
												modalRemove();
												$('#scan').val('');
											}, 1500);
										}
									} else {
										modal('Este produto não possui quantidades separadas');
										setTimeout(function() {
											$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
							    				modalRemove();
												$('#contentMenu').empty().html(request.data);
											});
										}, 1800);
									}
								}
							});
							$('#scan').focus();    						

    						$('#voltar').unbind('click').bind('click', function(e) {
								modal('Aguarde...');
    							$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
    							 	modalRemove();
    							 	$('#scan').val('');
    								$('#contentMenu').empty().html(request.data);
								});
								return false;
		    				});
		    				
		    				$('#historico').unbind('click').bind('click', function(e) {
								modal('Aguarde...');
    							$.getJSON('./devolucaoProdutosLista.php?opcao=historico&T006_Id={$row['T006_Id']}&T005_Id={$T005_Id}&scan={$scan}&callback=?', function(request) {
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
							$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
			    				modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
						}
					} </script></div>
EOT;
			}
		}
	break;
	
	case 'historico':
		if(isset($_REQUEST['T006_Id'])){
			$resposta['data'] = <<<EOT
				<style>
					.tabela {
						background-color: #F6F6F6; 
						border: 1px solid #8F859C;
						border-top: 0px;
						margin-top: 3px;
						padding: 1px;
						align: center;
					}
					.lista {
						border-top: 1px solid #8F859C;
						padding: 2px;
						margin-left: 2px;
						text-align: center;			
					}
				</style>
				<button class="voltarLarge" style="margin-left: 8px;" id="voltar">Voltar</button>
				<table class="tabela" style="width: 98%;" align="center" cellspacing="0" cellpadding="0">	
				<tr>
					<td class="lista">Data</td>
					<td class="lista">Usuario</td>
					<td class="lista">Operação</td>
					<td class="lista">Quantidade</td>
					
				</tr>		
EOT;
			$T205 = mysql_query("SELECT * FROM T205 LEFT JOIN C007 ON C007_Id = T205_C007_Id WHERE T205_T006_Id = '{$_REQUEST['T006_Id']}' ORDER BY T205_Id DESC");
			while($row = mysql_fetch_array($T205)){
				if($row['T205_Operacao'] == 'S'){
					$operacao = "Saída";
				} else {
					$operacao = "Entrada"; 
				}
				$data = substr(gCorrigeData($row['T205_Data']), 0, 10);
				$resposta['data'] .= <<<EOT
					<tr>
						<td class="lista">{$data}</td>
						<td class="lista">{$row['C007_Primeiro_Nome']}</td>
						<td class="lista">{$operacao}</td>
						<td class="lista">{$row['T205_Quantidade']}</td>
					</tr>
EOT;
			}
			$resposta['data'] .= <<<EOT
				</table>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#voltar').unbind('click').bind('click', function(e) {
						modal('Aguarde...');
    					$.getJSON('./devolucaoProdutosLista.php?opcao=devolver&T005_Id={$_REQUEST['T005_Id']}&scan={$_REQUEST['scan']}&T185_Id=true&callback=?', function(request) {
    					 	modalRemove();
    					 	$('#scan').val('');
    						$('#contentMenu').empty().html(request.data);
						});
						return false;
    				});				
				}</script></div>
EOT;
		}
	break;
		
	case 'busca':
		$scan = $_REQUEST['scan'];
		$T005_Id = $_REQUEST['T005_Id'];
		
		if(isset($scan)) {
			$T006 = mysql_query("SELECT * FROM T006 
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN T185 ON T185_T006_Id=T006_Id
			LEFT JOIN T066 ON T066_Id=T185_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			WHERE T006_T005_Id = '{$T005_Id}'");
			while($row = mysql_fetch_array($T006)){
				if($row['D001_Codigo_Produto'] == $scan || $row['D001_Codigo_Barras'] == $scan){
					//produto lido esta na lista
					$encontrado = true;
					$resposta['data'] = <<<EOT
						<div class="produto" style="font-size: 120%;">
							<strong style="font-size: 100%;">{$row['D001_Codigo_Produto']}</strong><br />
							<strong style="font-size: 100%;">{$row['D001_Descricao_Produto']}</strong><br />
							<strong style="font-size: 100%;">{$row['D082_Marca']}</strong><br />
							<strong style="font-size: 100%;">Estoque: {$row['D009_Quantidade_Estoque']}</strong><br />
							<button class="bigBtn" id="devolver">Devolver Produto</button>
							<button class="bigBtn" id="voltar">Voltar</button>							
						</div>
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {						
    						$('#devolver').unbind('click').bind('click', function(e) {
								if (confirm('Confirmar?')) {
									modal('Verificando...');
    								$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id={$row['T006_Id']}&T185_Id={$row['T185_Id']}&callback=?', function(request) {
    								 	modalRemove();
    								 	$('#scan').val('');
    									$('#contentMenu').empty().html(request.data);
									});
								}
								return false;
		    				});

    						$('#voltar').unbind('click').bind('click', function(e) {
								modal('Aguarde...');
    							$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
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
							$.getJSON('./devolucaoProdutosLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
			    				modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
						}
					} </script></div>
EOT;
			}
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";