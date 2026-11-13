<?php
namespace hardness;

include('conf.php');

$scan = $_REQUEST['scan'];

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	// 1. LER E VALIDAR A LOCACAO DE ONDE SERÁ RETIRADO O PRODUTO --------------------------------------------------------------------------------------
    case 'lerLocacao':
		$D109_Id = $_REQUEST['D109_Id'];

		// Scaneou a locacao
		if (isset($_REQUEST['scan'])) {

			$sql = "SELECT * FROM D004 WHERE D004_Id = '{$_REQUEST['scan']}';";
			$res = mysql_query($sql);

			// Existe em D004
			if(mysql_num_rows($res) > 0){

				$sql = "SELECT * FROM T066  
								LEFT JOIN D004 ON T066_D004_Id = D004_Id 
								WHERE T066_D004_Id = '{$_REQUEST['scan']}'";
				$res = mysql_query($sql);

				// Existe em T066
				if(mysql_numrows($res) > 0){

					$result = mysql_fetch_assoc($res);
					$D004_Id = $result['T066_D004_Id'];

					// Chama retirarProduto
					$resposta['data'] = <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
							$.getJSON('./emprestimoProduto.php?opcao=produtos&D109_Id=$D109_Id&D004_Id=$D004_Id&callback=?', function(request) {
			   	    			$('#scan').val('');
								$('#contentMenu').empty().html(request.data);
							});
						}</script></div>
EOT;
				} else {

					// Não existe produtos nesta locação para retirar
					$resposta['data'] .= <<<EOT
					<div class="alerta" style="margin-top: 45%;"><strong style="font-size: 140%;"><br>NÃO EXISTEM PRODUTOS NESTA LOCAÇÃO<br>(retirar)</br></strong></div>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
     					setTimeout("reload()",2000);
	    				$.getJSON('./emprestimoProduto.php?opcao=lerLocacao&callback=?', function(request) {
	    					modalRemove();
	    					// Proximo Produto da lista
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>					
EOT;
				}

			} else {

				$sql = <<<EOT
					SELECT D109_Nome
					  FROM D109
					 WHERE D109_Id='{$D109_Id}'
EOT;
				$res = mysql_query($sql);
				$row = mysql_fetch_array($res);

				$resposta['data'] .= <<<EOT
				 <p align=center>
				Funcionário:<br /><strong>{$row['D109_Nome']}</strong><br />
				</p>
				<p align=center>
				Local:<br /><strong>{$row['D004_Local']}</strong><br />
				</p>
				<div class="alerta" style="margin-top: 45%;"><strong style="font-size: 140%;"><br>LOCAÇÃO NÃO ENCONTRADA<br> </br></strong></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				setTimeout("reload()",2000);
				function reload(){
					$.getJSON('./emprestimoProduto.php?opcao=lerLocacao&D109_Id='+'{$D109_Id}'+'&D004_Id='+'{$D004_Id}'+'&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
				}
				}</script></div>
EOT;
			}

		} else {
			$sql = <<<EOT
				SELECT D109_Nome
				  FROM D109
				 WHERE D109_Id='{$D109_Id}'
EOT;
			$res = mysql_query($sql);
			$row = mysql_fetch_array($res);

			$resposta['data'] .= <<<EOT
			<p align=center>
			Funcionário:<br /><strong>{$row['D109_Nome']}</strong><br />
			</p>
			<div class="alerta">
				<strong style="font-size: 140%;">Leia uma locação<br>(retirar)</br></strong>
			</div>
			<p>
			<button class="bigBtn" id="voltar">Voltar</button>
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
						modal('Verificando...');
						$.getJSON('./emprestimoProduto.php?opcao=lerLocacao&D109_Id=$D109_Id&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
						    modalRemove();
						    $('#scan').val('');
							$('#contentMenu').empty().html(request.data);
						});
					}
				});
				$('#scan').focus();
				
				$('#voltar').unbind('click').bind('click', function(e) {
				    modal('Carregando...');
				    $.getJSON('./opcoes.php?&callback=?', function(request) {
				    	modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				});
			}</script></div>
EOT;
		}
		break;

	case 'produtos':
		$D109_Id = $_REQUEST['D109_Id'];
		$D004_Id = $_REQUEST['D004_Id'];
		
	    if(!isset($scan)){

			$sql = <<<EOT
				SELECT D109_Nome
				  FROM D109
				 WHERE D109_Id='{$D109_Id}'
EOT;
			$res = mysql_query($sql);
			$row = mysql_fetch_array($res);

			$sql = <<<EOT
				SELECT D004_Local
				  FROM D004
				 WHERE D004_Id='{$D004_Id}'
EOT;
			$res = mysql_query($sql);
			$rowD004 = mysql_fetch_array($res);
			
			// SQL PARA SELECIONAR OS PRDUTOS JÁ EMPRESTADOS DO FUNCIONÁRIO EM ABERTO
			$sqlT175 = "SELECT * FROM T175 WHERE T175_D109_Id='{$D109_Id}' AND T175_Flag_Operacao='E'";
			$resultT175 = mysql_query($sqlT175);
			if(mysql_num_rows($resultT175) > 0){
				// monta a table com os produtos em abertos do funcionário
				$produtos = <<<EOT
				<table align="center" border="1" style="width:80%;">
					<th colspan="2">Produtos emprestados</th>
					<tr>
						<td><b>Produto</b></td>
						<td align="center"><b>Qnt</b></td>
					</tr>
EOT;
				while($linhaT175 = mysql_fetch_array($resultT175)){
					$produtos .=<<<EOT
					<tr>
						<td>{$linhaT175['T175_D009_Id']}</td>
						<td align="center">{$linhaT175['T175_Quantidade']}</td>
					</tr>
EOT;
				}
				$produtos.=<<<EOT
				</table>
EOT;
			}else{
				$produtos="";
			}

			$resposta['data'] .= <<<EOT
		    <p align=center>
			Funcionário:<br /><strong>{$row['D109_Nome']}</strong><br />
			</p>
		    <p align=center>
			Local:<br /><strong>{$rowD004['D004_Local']}</strong><br />
			</p>
			<div class="alerta" style="margin-top: 5%">
				<strong style="font-size: 140%;"><br>LEIA O PRODUTO<br> </br></strong>
			</div>
			<button class="btn" id="voltar">Voltar</button>
			<p>
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
				    	modal('Verificando...');
				    	$.getJSON('./emprestimoProduto.php?opcao=produtos&scan=' + encodeURIComponent($('#scan').val()) + '&D109_Id=' + {$D109_Id} + '&D004_Id=' + {$D004_Id} + '&callback=?', function(request) {
				    		modalRemove();
				    		$('#scan').val('');
							$('#contentMenu').empty().html(request.data);
						});
					}
				});
				$('#scan').focus();
				$('#voltar').unbind('click').bind('click', function() {
			    	$.getJSON('./emprestimoProduto.php?opcao=cracha&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
    			});
			}</script></div>
EOT;
		  }else{
			$sql = "SELECT D001_Codigo_Produto, 
			               D001_Descricao_Produto, 
			               D009_Quantidade_Estoque, 
			               D082_Marca,
			               D009_Id
			          FROM D009 
			     LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			     LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			     LEFT JOIN D082 ON D082_Id=D049_D082_Id 
			         WHERE D049_Codigo_Barras = '{$_REQUEST['scan']}' 
			            OR D001_Codigo_Produto = '{$_REQUEST['scan']}'
			            OR D049_Id = '{$_REQUEST['scan']}';";
			$res = mysql_query($sql);
			if (mysql_numrows($res) == 0) {
				$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					modalRemove();
					modal('PRODUTO NÃO ENCONTRADO');
					setTimeout("reload()",1000);
					function reload(){
						modalRemove();
			    		$.getJSON('./emprestimoProduto.php?opcao=produtos&D004_Id=$D004_Id&D109_Id=' + {$D109_Id} + '&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}
				} </script></div>
EOT;
			} else {
				$row = mysql_fetch_assoc($res);
				
				$sqlT066 = "SELECT * FROM T066 WHERE T066_D004_Id='{$D004_Id}' AND T066_D009_Id='{$row['D009_Id']}'";
				$retornoT066 = mysql_query($sqlT066);
				if(mysql_num_rows($retornoT066) > 0){
					$resposta['data'] = <<<EOT
				    <p align=center>
						<strong>{$row['D001_Codigo_Produto']}</strong><br />
						<strong>{$row['D001_Descricao_Produto']}</strong><br />
						<strong>{$row['D082_Marca']}</strong><br />
						Estoque:<br /><strong>{$row['D009_Quantidade_Estoque']}</strong><br />
					</p>
					<p align="center">
						Informe a quantidade:
					<input type="text" name="qntEmp" id="qntEmp" style="width:95%;"/><br />
          			<button class="btn" id="enviaQnt">Enviar</button>
         			<button class="btn" id="voltar">Voltar</button>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						(function(){
							$('#qntEmp').focus()
						})();
						$('#enviaQnt').unbind('click').bind('click', function(e) {
				    		//clearTimeout(refreshMenu);
				    		modal('Carregando...');
				    		$.getJSON('./emprestimoProduto.php?opcao=salva&D004_Id=$D004_Id&qntSep='+ $('#qntEmp').val() +'&D009_Id='+ '{$row['D009_Id']}' +'&D109_Id='+'{$D109_Id}'+'&callback=?', function(request) {
				    			modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
				    	});
						$('#voltar').unbind('click').bind('click', function() {
			    			$.getJSON('./emprestimoProduto.php?opcao=produtos&D109_Id=' + {$D109_Id} + '&callback=?', function(request) {
								$('#contentMenu').empty().html(request.data);
							});
    					});
						
						$('#qntEmp').bind('keypress', function(e) {
							if (e.keyCode == 13) {
					    		//clearTimeout(refreshMenu);
					    		modal('Carregando...');
					    		$.getJSON('./emprestimoProduto.php?opcao=salva&D004_Id=$D004_Id&qntSep='+ $('#qntEmp').val() +'&D009_Id='+ '{$row['D009_Id']}' +'&D109_Id='+'{$D109_Id}'+'&callback=?', function(request) {
					    			modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
							}
							if(e.keyCode == 27){
								$.getJSON('./emprestimoProduto.php?opcao=produtos&D109_Id=' + {$D109_Id} + '&callback=?', function(request) {
									$('#contentMenu').empty().html(request.data);
								});
							}
						});
				    }</script></div>
					</p>
					
EOT;
				} else{
					$resposta['data'] = <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						alert("Produto não encontrado na locação");
						$.getJSON('./emprestimoProduto.php?opcao=produtos&D109_Id=' + {$D109_Id} + '&D004_Id=' + {$D004_Id} + '&callback=?', function(request) {
				    		modalRemove();
				    		$('#scan').val('');
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>
EOT;
				}
			}
		}
	break;
	
	case 'salva':
		global $g;
		$qnt = $_REQUEST['qntSep'];
		$D009_Id = $_REQUEST['D009_Id'];
		$D109_Id = $_REQUEST['D109_Id'];
		
		
		/**
		 * T175_Flag_Operacao
		 * E = EMPRESTIMO
		 * D = DEVOLUÇÃO
		 */
		$sqlT175 =<<<EOT
		INSERT INTO T175 (T175_D009_Id,
					      T175_C007_Id,
					      T175_D109_Id,
					      T175_Data_Saida,
					      T175_Quantidade,
					      T175_Flag_Operacao)
				VALUES
						  ('{$D009_Id}',
						  '{$g['usuarioAtual']}',
						  '{$D109_Id}',
						  CURRENT_TIMESTAMP(),
						  '{$qnt}',
						  'E');
EOT;
		$resultT175 = mysql_query($sqlT175);
		
		if($resultT175){
			$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert("Emprestimo cadastrado com sucesso!");
					window.location="./menu.php";
				}</script></div>
EOT;
		} else {
			$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert("Houve algum erro ao cadastrar o empréstimo. Tente novamente.!");
					window.location="./menu.php";
				}</script></div>
EOT;
		}
	break;
	
	case 'cracha':
  		  if(!isset($scan)){
			$resposta['data'] .= <<<EOT
			<div class="alerta" style="margin-top: 10%">
				<strong style="font-size: 140%;"><br>LEIA O CRACHÁ<br> </br></strong>
			</div>
			<p>
			<button class="btn" id="voltar">Voltar</button>
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
				    	modal('Verificando...');
				    	$.getJSON('./emprestimoProduto.php?opcao=cracha&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
		  }else{
			$sql = <<<EOT
				SELECT D109_Codigo_Barras, D109_Id
				  FROM D109
				 WHERE D109_Codigo_Barras='{$scan}'
EOT;
			$res = mysql_query($sql);
			echo mysql_error();
			$D109 = mysql_fetch_array($res);
			if(mysql_num_rows($res) > 0){
				$D109_Id = $D109['D109_Id'];
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#barraTopPedido').html('');
	    			//modal('Pedido {$C007_Id}');
	    			$.getJSON('./emprestimoProduto.php?opcao=lerLocacao&D109_Id={$D109_Id}&callback=?', function(request) {
	    				modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
			    } </script></div>
EOT;
			} else {
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert("Funcionário não cadastado. {$scan}");
					window.location="./menu.php";
				} </script></div>
EOT;
			}
	   }
	break;
	
	case 'busca':
		$C007_Id = $_REQUEST['C007_Id'];
		$T185_Id = $_REQUEST['T185_Id'];
		$scan = $_REQUEST['scan'];
		if(isset($_REQUEST['novo'])){
			$sql = "UPDATE D049 SET D049_Codigo_Barras = '{$scan}' WHERE D049_Id = '{$_REQUEST['D049_Id']}';";
			mysql_query($sql);
			//$scan = $_REQUEST['D009_Id'];
		}
		$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
		if (isset($_REQUEST['scan'])) {
			$sql = <<<EOT
				SELECT * FROM T006 
				LEFT JOIN D009 on D009_Id=T006_D009_Id
				LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				LEFT JOIN D082 ON D082_Id=D049_D082_Id 
				LEFT JOIN T185 ON T185_T006_Id=T006_Id
				LEFT JOIN T066 ON T066_Id=T185_T066_Id
				LEFT JOIN D004 ON D004_Id=T066_D004_Id  
				WHERE 
				T006_C007_Id = '{$C007_Id}' AND (T185_Qtd_Separado IS NULL OR T185_Qtd_Separado = 0) AND T006_Flag_Divergencia = 'N' AND T185_Qtd_Para_Separar IS NOT NULL LIMIT 1
EOT;
			$res = mysql_query($sql);
			while($row = mysql_fetch_array($res)) {
				$D049_Id = $row['D049_Id']; 
				if ($row['D001_Codigo_Produto'] != $scan && $row['D049_Codigo_Barras'] != $scan && $row['D049_Id'] != $scan) {
					$sql2 = "SELECT * FROM D049 WHERE D049_Codigo_Barras = '{$scan}';";
					$res2 = mysql_query($sql2);
					if(!empty($row['D049_Codigo_Barras']) || mysql_num_rows($res2) > 0){
						$resposta['data'] = <<<EOT
							<div class="alerta" style="margin-top: 35%"><p><p><strong style="font-size: 140%;"><br>PRODUTO INCORRETO.<br> Tente novamente<br> </br></strong></div>
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout("reload()",2000);
								function reload(){
				    				$.getJSON('./emprestimoProduto.php?opcao=produtos&C007_Id={$C007_Id}&scan={$D004_Id}&callback=?', function(request) {
				    					$('#scan').val('');
										$('#contentMenu').empty().html(request.data);
									});
								}
						    } </script></div>					
EOT;
					} else {
						//PRODUTO NAO CADASTRADO
						/*$resposta['data'] = <<< EOT
						<div class="titulo"><strong style="font-size: 140%;"><br>CADASTRO DE CODIGO DE BARRA<br> </br></strong></div>
						<br/ ><br/ >
						<div class="alerta"><p><p><strong style="font-size: 120%;">Leia o codigo de barra do produto</strong></div>					
EOT;*/
						$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');
							$('#scan').bind('keypress', function(e) {
								if (e.keyCode == 13) {*/
									if (confirm('Produto não possui código de barra cadastrado, deseja associar o Cod. {$scan} a ele?')) {
					    				modal('Verificando...');
					    				$.getJSON('./emprestimoProduto.php?opcao=busca&T185_Id={$row['T185_Id']}&C007_Id=$C007_Id&D049_Id=$D049_Id&scan=' + encodeURIComponent('{$scan}') + '&novo=S&callback=?', function(request) {
					    					modalRemove();
					    					$('#scan').val('');
											$('#contentMenu').empty().html(request.data);
										});
									} else {
										modal('Carregando...');
	    								$.getJSON('./emprestimoProduto.php?opcao=cracha&callback=?', function(request) {
	    									modalRemove();
											$('#contentMenu').empty().html(request.data);
										});
									}
							/*		return false;
								}
							});
							// (não perder o foco do campo scan)
				    		$('#scan').focus();
						} </script></div>
							
EOT;
					}
				} else {					
					// PRODUTO LIDO
					$row['T006_Quantidade'] = gCorrigeNumero($row['T006_Quantidade']);
					$row['T006_Quantidade_Separacao'] = gCorrigeNumero($row['T006_Quantidade_Separacao']);
					$resposta['data'] = <<<EOT
						<div class="produto" style="font-size: 120%;">
							<strong style="font-size: 120%;">{$row['D001_Codigo_Produto']}</strong><br />
							<strong style="font-size: 120%;">{$row['D001_Descricao_Produto']}</strong><br />
							<strong style="font-size: 120%;">{$row['D082_Marca']}</strong><br />
							Estoque:<strong style="font-size: 120%;">{$row['D009_Quantidade_Estoque']}</strong><br />
						</div>
EOT;
				// PRODUTO SEPARADO
					if ($T006_Id) {
						$resposta['data'] .= <<<EOT
						<div class="produto">
						Separado:<br /><strong>{$row['T006_Quantidade_Separacao']}</strong><br />
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#scan').focus();
						$.getJSON('./emprestimoProduto.php?opcao=produtos&C007_Id={$C007_Id}&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					    } </script></div>
						</div>
EOT;
					} else {
						$resposta['data'] .= <<<EOT
						<div class="produto">
							<button class="bigBtn" id="separacaoPedidoQtdProdSubmit">Separar: {$row['T006_Quantidade']}</button>
							<button class="bigBtn" id="separacaoPedidoDivergencia">Qtd Insuficiente</button>
							<button class="bigBtn" id="separacaoPedidoVoltar">Voltar</button>
							<input id="emprestimoProdutoQtdProdId" type="hidden" value="{$row['T006_Id']}">
						</div>
EOT;
						//echo $row['T185_Id'];
						$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');
    						$('#separacaoPedidoQtdProdSubmit').unbind('click').bind('click', function() {
    							if (confirm('Separar {$row['T006_Quantidade']} itens?')) {
    				 				modal('Verificando...');
    								$.getJSON('./emprestimoProduto.php?opcao=produtos&C007_Id={$C007_Id}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&T185_Id={$row['T185_Id']}&T006_Id={$row['T006_Id']}&separar={$row['T006_Quantidade']}&callback=?', function(request) {
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
    								$.getJSON('./emprestimoProduto.php?opcao=produtos&C007_Id={$C007_Id}&T006_Id=' + encodeURIComponent($('#emprestimoProdutoQtdProdId').val()) + '&T185_Id={$row['T185_Id']}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&divergencia=2&callback=?', function(request) {
    								 	modalRemove();
    								 	$('#scan').val('');
    									// Proximo Produto da lista
										$('#contentMenu').empty().html(request.data);
									});
								}
								return false;
		    				});

    						$('#separacaoPedidoVoltar').unbind('click').bind('click', function(e) {
								if (confirm('Confirmar?')) {
									modal('Aguarde...');
    								$.getJSON('./emprestimoProduto.php?opcao=produtos&C007_Id={$C007_Id}&T006_Id=' + encodeURIComponent($('#emprestimoProdutoQtdProdId').val()) + '&T185_Id={$row['T185_Id']}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&callback=?', function(request) {
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
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";