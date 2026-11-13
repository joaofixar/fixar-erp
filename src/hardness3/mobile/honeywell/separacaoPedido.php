<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	
	/**
	* Produtos
	* 
	* Indica o próximo produto para separação
	* Considera os dois casos: 
	* 	- novas locação (que possuem estoque por locação)
	* 	- locações antigas
	*/
	case 'produtos':
		$T005_Id = $_REQUEST['T005_Id'];
		$T005_C007_Id = $_REQUEST['T005_C007_Id'];
		
		$T185_Id = $_REQUEST['T185_Id'];
		
		// Ação: Informar box
		if(isset($_REQUEST['box'])){
			$sql = "UPDATE T005 SET T005_Flag_Status = '10', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Estoque_Box = '{$_REQUEST['box']}' WHERE T005_Id = '{$T005_Id}'";			
			$sql = mysql_query($sql);
			$resposta['data'] = <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('O PEDIDO FOI SEPARADO');
				setTimeout("reload()", 1000);
				function reload(){
					modalRemove();
					window.location="./menu.php";
				}
			} </script></div>
EOT;
		}
		
		// Ação: separar produto
		$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
		if ($T006_Id) {
				//$_REQUEST['separar'] = mysql_real_escape_string(gCorrigeNumeroInverte($_REQUEST['separar']));
				//$sql = "UPDATE T006 SET T006_Quantidade_Separacao = '{$_REQUEST['separar']}' WHERE T006_Id = '{$T006_Id}';";
				//mysql_query($sql);
				$sql = "UPDATE T185 SET T185_Qtd_Separado = '{$_REQUEST['separar']}' WHERE T185_Id = '{$T185_Id}';";
				mysql_query($sql);
		}
		$Divergencia = isset($_REQUEST['divergencia']) ? $_REQUEST['divergencia'] : false;
		if($Divergencia){
				$sql = "UPDATE T006 SET T006_Flag_Divergencia = 'S' WHERE T006_Id = '{$T006_Id}';";
				mysql_query($sql);
				$sql = "UPDATE T185 SET T185_Tipo_Divergencia = '{$Divergencia}' WHERE T185_Id = '{$T185_Id}';";
				mysql_query($sql);	
		}
		
		// Ação: Voltar
		$sql2 = <<<EOT
				SELECT * FROM T006 
					LEFT JOIN D009 on D009_Id=T006_D009_Id 
					LEFT JOIN T185 ON T185_T006_Id=T006_Id 
					LEFT JOIN T066 ON T066_Id=T185_T066_Id 
					LEFT JOIN D004 ON D004_Id=T066_D004_Id 
					LEFT JOIN D001 ON D001_Id=D009_Id 
				WHERE 
				T006_T005_Id = '{$T005_Id}' AND (T185_Qtd_Separado != 0 OR T185_Tipo_Divergencia > 0) AND T185_Qtd_Para_Separar IS NOT NULL ORDER BY T185_Id DESC LIMIT 1
EOT;
		$res1 = mysql_query($sql2);
		if(isset($_REQUEST['voltar'])) {
			$result = mysql_fetch_array($res1);
			$sql = "UPDATE T185 SET T185_Qtd_Separado = '0', T185_Tipo_Divergencia = 0 WHERE T185_Id = '{$result['T185_Id']}';";
			mysql_query($sql);
			$sql = "UPDATE T006 SET T006_Flag_Divergencia = 'N' WHERE T006_Id = '{$result['T006_Id']}';";
			mysql_query($sql);
		}
		
		// Lista próximo produto		
		$sql = <<<EOT
			SELECT * FROM T006 
				LEFT JOIN D009 on D009_Id=T006_D009_Id 
				LEFT JOIN T185 ON T185_T006_Id=T006_Id
				LEFT JOIN T066 ON T066_Id=T185_T066_Id
				LEFT JOIN D004 ON D004_Id=T066_D004_Id
				LEFT JOIN D001 ON D001_Id=D009_Id
			WHERE 
				T006_T005_Id = '{$T005_Id}' AND (T185_Qtd_Separado IS NULL OR T185_Qtd_Separado = 0) AND T006_Flag_Divergencia = 'N' AND T185_Qtd_Para_Separar IS NOT NULL ORDER BY T185_Id ASC LIMIT 1
EOT;
		$res = mysql_query($sql);
		if(mysql_numrows($res)> 0){ 
			$row = mysql_fetch_assoc($res);
			
			// Pode ser dois casos:
			// 		- locação antiga (sem estoque por locação)
			//		- locação nova
			
			// Verifica se estamos em uma locação antigo (onde o estoque não era definido por locação)
			$antigo = false;
			if (empty($row['D004_Id']) || empty($row['D004_Flag_Tipo'])) {
				$antigo = true;
			}
			if(!empty($row['D004_Local']) || $antigo) {
				$row['T185_Qtd_Para_Separar'] = number_format($row['T185_Qtd_Para_Separar'], 2);
				$quantidade = gCorrigeNumero($row['T185_Qtd_Para_Separar']);
				if ($antigo) {
					// Antigo: Não é necessário escanear locação ou produto
					// Apenas pede a quantidade separada
					$sqlLocs = "SELECT * FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '{$row['D009_Id']}';";
					$resLocs = mysql_query($sqlLocs);
					$locs = '';
					while ($loc = mysql_fetch_assoc($resLocs)) {
						if (empty($loc['D004_Id']) || empty($loc['D004_Flag_Tipo'])) {
							$loc['D004_Local'] = empty($loc['D004_Local']) ? 'DESCONHECIDO' : $loc['D004_Local'];
							$locs .= (empty($locs)) ? $loc['D004_Local'] : ', ' . $loc['D004_Local'];
						}
					}
					$resposta['data'] .= <<<EOT
						<table style="width: 100%; font-size: 120%;" align="center">
							<tr id="T005_Id-{$row['T005_Id']}"></tr>
								<tr align="center"><td style="text-align: center;">Locações: <strong style="font-size: 120%;"><br>{$locs}</strong></td>
								<tr align="center"><td>Produto: <strong style="font-size: 120%;"><br>{$row['T006_Descricao_Produto']}</strong></td></tr>
								<tr align="center"><td>Qtd. para Separar: <strong style="font-size: 120%;"><br>{$quantidade}</strong></td></tr>
								<tr align="center"><td>Separar: <strong style="font-size: 100%;"><br>
									<input type="text" id="separarOld" value="{$row['T185_Qtd_Para_Separar']}" />
								</strong></td></tr>
							<tr>
							<tr>
								<td><button class="bigBtn" id="separar">Separar</button></td>
							</tr>
						</table>
EOT;
					$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#separarOld').focus();
							
							var valorSeparado = $('#separarOld').val();
							$('#separar').unbind('click').bind('click', function(e) {
								if (confirm('Deseja separar ?')) {   
									modal('Verificando...');
			   						$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id={$row['T006_Id']}&T185_Id={$row['T185_Id']}&separar='+ $('#separarOld').val() +'&callback=?', function(request) {
	    								modalRemove();
										$('#contentMenu').empty().html(request.data);
									});
								}
			   				});
						} </script></div>
EOT;
				} else {
					// Novo: Pede cod. locação e se necessário cod. produto
					$resposta['data'] .= <<<EOT
						<table style="width: 100%; font-size: 120%;" align="center">
							<tr id="T005_Id-{$row['T005_Id']}"></tr>
								<tr align="center"><td style="text-align: center;">Local: <strong style="font-size: 120%;"><br>{$row['D004_Local']}</strong></td>
								<tr align="center"><td>Produto: <strong style="font-size: 120%;"><br>{$row['T006_Descricao_Produto']}</strong></td></tr>
								<tr align="center"><td>Qtd. para Separar: <strong style="font-size: 120%;"><br>{$quantidade}</strong></td></tr>
							<tr>
						</table>
						<button class="bigBtn" id="separacaoPedidoNaoEncontrado">Não Encontrei</button>
EOT;
					$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#separacaoPedidoNaoEncontrado').unbind('click').bind('click', function(e) {
								if (confirm('Confirmar?')) {   
									modal('Verificando...');
			   						$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id={$row['T006_Id']}&T185_Id={$row['T185_Id']}&divergencia=1&callback=?', function(request) {
	    								modalRemove();
										$('#contentMenu').empty().html(request.data);
									});
								}
			   				});
			   				
			   				$('#scan').unbind('keypress');
							$('#scan').bind('keypress', function(e) {
								if (e.keyCode == 13) {
		    						modal('Verificando...');
		    						$.getJSON('./separacaoPedido.php?opcao=busca&T005_Id={$T005_Id}&scan=' + encodeURIComponent($('#scan').val()) + '&D004_Id={$row['D004_Id']}&callback=?', function(request) {
		    							modalRemove();
		    							$('#scan').val('');
										$('#contentMenu').empty().html(request.data);
									});
								}
							});
		    				$('#scan').focus();
			   			} </script></div>
EOT;
				}
				if(mysql_num_rows($res1) > 0){
					$resposta['data'] .= <<<EOT
						<button class="bigBtn" id="retornarProdutoAnterior">Voltar Produto</button>
EOT;
				}
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#retornarProdutoAnterior').unbind('click').bind('click', function(e) {
							if (confirm('Confirmar?')) {
								modal('Verificando...');
		    					$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&voltar=1&callback=?', function(request) {
	  								modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
							}
		    			});
					} </script></div>
EOT;
			} else {
				$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id={$row['T006_Id']}&T185_Id={$row['T185_Id']}&divergencia=3&callback=?', function(request) {
	    				$('#contentMenu').empty().html(request.data);
					});
				} </script></div>
EOT;
			}
		} else {		
			$resposta['data'] .= <<<EOT
			<br><div class="alerta" style="margin-top: 45%"><strong style="font-size: 140%;"><br>INFORME O BOX<br> </br></strong></div>		
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#scan').val('');
			$('#scan').unbind('keypress');
			$('#scan').bind('keypress', function(e) {
				if (e.keyCode == 13) {
			    	modal('Verificando...');
			    	$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id=$T005_Id&box=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
			    		modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				}
			});
			$('#scan').focus();
			}</script></div>
EOT;
		}
	break;
	
	/**
	* Pedidos
	* 
	* Busca: ou o pedido já aberto para aquele usuário ou o próximo pedido da lista
	*/
	case 'pedidos':
			$sql = <<<EOT
				SELECT 
					*
				FROM T005 
					left join D024 on D024_Id=T005_D024_Id 
					left join D020 on D020_Id=D024_D020_Id 
					left join D018 on D018_Id=D024_D018_Id 
					left join D021 on D021_Id=T005_D021_Id 
					left join D022 on D022_Id=T005_D022_Id 
					left join D006 on D006_Id=T005_D006_Id 
					left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno 
					left join C004 on C004_Id=T005_C004_Id 
					left join D036 on T005_D036_Id=D036_Id 
					left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S' 
					left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id left 
					join C007 as Separou on Separou.C007_Id=T005_Usuario_Pecas 
				WHERE T005_C004_Id='{$g['empresaAtual']}' AND ((T005_Flag_Status='2' AND T005_C007_Id_Separou='{$g['usuarioAtual']}') OR (T005_Flag_Status='1'))
				GROUP BY T005_Id 
				ORDER BY T005_Flag_Status DESC, T005_Id LIMIT 1
EOT;
			$res = mysql_query($sql);
			$status = mysql_fetch_array($res);
			$T005_C007_Id = $status['T005_C007_Id_Separou'];
			$T005_Id = $status['T005_Id'];
			if($status['T005_Flag_Status'] == 2){
				// Pedido em aberto deste vendedor
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#barraTopPedido').html('Pedido: {$T005_Id}');
		    			modal('Pedido {$T005_Id}');
		    			$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&T005_C007_Id={$T005_C007_Id}&callback=?', function(request) {
		    				modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
				    } </script></div>
EOT;
			} else if($status['T005_Flag_Status'] == 1) {
				// Novo pedido!
				mysql_query("UPDATE T005 SET T005_C007_Id_Separou = '{$g['usuarioAtual']}' WHERE T005_Id = {$T005_Id}");
				mysql_query("UPDATE T005 SET T005_Flag_Status = '2', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id ='$T005_Id'");
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#barraTopPedido').html('Pedido: {$T005_Id}');
		    			modal('Pedido {$T005_Id}');
		    			$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&T005_C007_Id={$g['usuarioAtual']}&callback=?', function(request) {
		    				modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
				    } </script></div>
EOT;
			} else {
				// Sem pedidos
				$resposta['data'] .= <<<EOT
					<div style="margin-top: 45%" class="titulo">
					<strong style="font-size: 140%;"><br>Sem Pedidos para<br>Separar<br> </br></strong></div>			
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout("reload()",1000);
					function reload(){
						window.location="./menu.php";
					}
					} </script></div>
EOT;
			}
	break;
	
	/**
	* Busca
	*/
	case 'busca':
		$resposta['data'] = <<<EOT
		<div class="divHidden"><script type="text/javascript">
		alert('{$_REQUEST['D004_Id']}');
		</script></div>		
EOT;
		$T005_Id = $_REQUEST['T005_Id'];
		$T185_Id = $_REQUEST['T185_Id'];
		$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
		if (isset($_REQUEST['scan'])) {			
			$sql = <<<EOT
				SELECT * FROM T006 
				LEFT JOIN D009 on D009_Id=T006_D009_Id
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN T185 ON T185_T006_Id=T006_Id
				LEFT JOIN T066 ON T066_Id=T185_T066_Id
				LEFT JOIN D004 ON D004_Id=T066_D004_Id
				LEFT JOIN D001 ON D001_Id=D009_D001_Id
				LEFT JOIN D082 ON D082_Id=D049_D082_Id  
				WHERE 
				T006_T005_Id = '{$T005_Id}' AND (T185_Qtd_Separado IS NULL OR T185_Qtd_Separado = 0) AND T006_Flag_Divergencia = 'N' AND T185_Qtd_Para_Separar IS NOT NULL LIMIT 1
EOT;
			//echo $sql;
			//die();
			$res = mysql_query($sql);
			while($row = mysql_fetch_array($res)) {
				$D004_Id = $row['D004_Id']; 
				if ($row['D004_Id'] != $_REQUEST['scan']) {
					$resposta['data'] = <<<EOT
						<div class="alerta" style="margin-top: 35%"><p><p><strong style="font-size: 140%;"><br>LOCAÇÃO INCORRETA.<br> Tente novamente<br> </br></strong></div>
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							setTimeout("reload()",2000);
							function reload(){
			    				$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&scan={$D004_Id}&callback=?', function(request) {
			    					$('#scan').val('');
									$('#contentMenu').empty().html(request.data);
								});
							}
					    } </script></div>
					
EOT;
				} else {					
					// PRODUTO LIDO
					$row['T006_Quantidade'] = gCorrigeNumero($row['T006_Quantidade']);
					$row['T006_Quantidade_Separacao'] = gCorrigeNumero($row['T006_Quantidade_Separacao']);
					$resposta['data'] = <<<EOT
						<div class="produto" style="font-size: 120%;">
							Código:<br /><strong style="font-size: 120%;">{$row['D001_Codigo_Produto']}</strong><br />
							Descrição:<br /><strong style="font-size: 120%;">{$row['D001_Descricao_Produto']}</strong><br />
							Marca:<br /><strong style="font-size: 120%;">{$row['D082_Marca']}</strong><br />
							Estoque:<br /><strong style="font-size: 120%;">{$row['D009_Quantidade_Estoque']}</strong><br />
						</div>
EOT;
				// PRODUTO SEPARADO
					if ($T006_Id) {
						$resposta['data'] .= <<<EOT
						<div class="produto">
						Separado:<br /><strong>{$row['T006_Quantidade_Separacao']}</strong><br />
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#scan').focus();
						$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					    } </script></div>
						</div>
EOT;
					} else {
						if($row['D004_Flag_Tipo'] == 2 && $_REQUEST['check'] != 1){
							$check = 1;
							$resposta['data'] .= <<<EOT
							<p><div class="alerta"><strong style="font-size: 140%;"><br>LEIA O CODIGO DO PRODUTO<br>  </br></strong></div>
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#scan').unbind('keypress');	    						
								$('#scan').bind('keypress', function(e) {
									if (e.keyCode == 13) {
										if({$row['D009_Id']} == $('#scan').val()){
						    				modal('Verificando...');	
						    				//alert('lol');					    					
						    				$.getJSON('./separacaoPedido.php?opcao=busca&T005_Id={$T005_Id}&scan={$D004_Id}&check={$check}&callback=?', function(request) {
						    					modalRemove();
						    					$('#scan').val('');
												$('#contentMenu').empty().html(request.data);
											});
										} else {
											alert('Produto incorreto');
											$('#scan').val('');
										}
									}
								});
								// (não perder o foco do campo scan)
					    		$('#scan').focus();	
			    			} </script></div>				
EOT;
						} else {
							$resposta['data'] .= <<<EOT
								<div class="produto">
									<button class="bigBtn" id="separacaoPedidoQtdProdSubmit">Separar: {$row['T006_Quantidade']}</button>
									<button class="bigBtn" id="separacaoPedidoDivergencia">Qtd Insuficiente</button>
									<input id="separacaoPedidoQtdProdId" type="hidden" value="{$row['T006_Id']}">
								</div>
EOT;
							$resposta['data'] .= <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$('#scan').unbind('keypress');
		    						$('#separacaoPedidoQtdProdSubmit').unbind('click').bind('click', function() {
		    							if (confirm('Separar {$row['T006_Quantidade']} itens?')) {
		    				 				modal('Verificando...');
		    								$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&T185_Id={$row['T185_Id']}&T006_Id={{$row['T006_Id']}}&separar={$row['T006_Quantidade']}&callback=?', function(request) {
		    									modalRemove();
		    									$('#scan').val('');
		    									// Proximo Produto da lista
												$('#contentMenu').empty().html(request.data);
											});
										}
										return false;
		    						});
		    						
		    						$('#separacaoPedidoDivergencia').unbind('click').bind('click', function(e) {
										if (confirm('Confirmar?')) {
											modal('Verificando...');
		    								$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id=' + encodeURIComponent($('#separacaoPedidoQtdProdId').val()) + '&T185_Id={$row['T185_Id']}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&divergencia=2&callback=?', function(request) {
		    								 	modalRemove();
		    								 	$('#scan').val('');
		    									// Proximo Produto da lista
												$('#contentMenu').empty().html(request.data);
											});
										}
										return false;
				    				});
							    } </script></div>
EOT;
						}
					}
				}
			}
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";