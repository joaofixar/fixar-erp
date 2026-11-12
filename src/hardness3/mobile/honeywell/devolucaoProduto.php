<?php
namespace hardness;

include('conf.php');

$scan = $_REQUEST['scan'];

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'produtos':
		$D109_Id = $_REQUEST['D109_Id'];
		
  		  if(!isset($scan)){
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
			<div class="alerta" style="margin-top: 10%">
				<strong style="font-size: 140%;"><br>LEIA O PRODUTO<br> </br></strong>
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
				    	$.getJSON('./devolucaoProduto.php?opcao=quantidade&scan=' + encodeURIComponent($('#scan').val()) + '&D109_Id=' + {$D109_Id} + '&callback=?', function(request) {
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
		  }
	break;
	
	case 'quantidade':
		$D109_Id = $_REQUEST['D109_Id'];
		
		// PEGA O D009_ID DO SCAN RECEBIDO.
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
			$row = mysql_fetch_assoc($res);
			if (mysql_num_rows($res) == 0) {
				$resposta['data'] = <<<EOT
			    <p align='center' style='color:red'>
					<strong>Código do produto não encontrado.</strong>
					<button class="btn" id="voltar">Voltar</button>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#voltar').unbind('click').bind('click', function() {
		    				 window.location="./menu.php";
		    			});
					}</script></div>
				</p>
EOT;
			} else {
				$sqlT175 = <<<EOT
				SELECT * FROM T175 WHERE T175_D109_Id='{$D109_Id}' AND T175_D009_Id='{$row['D009_Id']}';
EOT;
				$resultT175Select = mysql_query($sqlT175);
				// CASO TENHA ENCONTRADO O PRODUTO, ELE VERIFICA SE EXISTE UM EMPRÉSTIMO ABERTO DAQUELE D009 PARA AQUELE D109(FUNCIONÁRIO)
				if(mysql_num_rows($resultT175Select) > 0){
					// CASO EXISTA O EMPRÉSTIMO, ELE MOSTRA O PRODUTO E PEDE A QUANTIDADE
					$resposta['data'] = <<<EOT
				    <p align=center>
						Código:<br /><strong>{$row['D001_Codigo_Produto']}</strong><br />
						Descrição:<br /><strong>{$row['D001_Descricao_Produto']}</strong><br />
						Marca:<br /><strong>{$row['D082_Marca']}</strong><br />
						Estoque:<br /><strong>{$row['D009_Quantidade_Estoque']}</strong><br />
					</p>
					<p align="center">
						Informe a quantidade a ser devolvida:
					</p>
					<input type="text" name="qntDev" id="qntDev" style="width:90%;margin-left:5px;"/><br />
					<input type="button" class="btn" id="enviaQntDev" value="Enviar" style="width:95%;margin-left:5px;"/>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						(function(){
							$('#qntDev').focus()
						})();
						$('#enviaQntDev').unbind('click').bind('click', function(e) {
				    		//clearTimeout(refreshMenu);
				    		modal('Carregando...');
				    		$.getJSON('./devolucaoProduto.php?opcao=salvaDevolucao&qntDev=' + $('#qntDev').val() 
				    														   + '&D009_Id=' + '{$row['D009_Id']}'
				    														   + '&scan=' + '{$_REQUEST['scan']}' 
				    														   + '&D109_Id=' + '{$D109_Id}'+'&callback=?', function(request) {
				    			modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
				    	});
						
						$('#qntDev').bind('keypress', function(e) {
							if (e.keyCode == 13) {
					    		//clearTimeout(refreshMenu);
					    		modal('Carregando...');
					    		$.getJSON('./devolucaoProduto.php?opcao=salvaDevolucao&qntDev=' + $('#qntDev').val() 
				    														   + '&D009_Id=' + '{$row['D009_Id']}'
				    														   + '&scan=' + '{$_REQUEST['scan']}' 
				    														   + '&D109_Id=' + '{$D109_Id}'+'&callback=?', function(request) {
				    				modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
							}
						});
				    }</script></div>
EOT;
				} else {
					// SE NÃO, DIZ QUE O EMPRÉSTIMO NÃO FOI ENCONTRADO
					$resposta['data'] = <<<EOT
				    <p align='center' style='color:red'>
						<strong>Empréstimo não encontrado.</strong>
						<button class="btn" id="voltar">Voltar</button>
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#voltar').unbind('click').bind('click', function() {
			    				 window.location="./menu.php";
			    			});
						}</script></div>
					</p>
EOT;
				}
			}
		
	break;
	
	case 'salvaDevolucao':
		global $g;
		$qnt = $_REQUEST['qntDev'];
		$D009_Id = $_REQUEST['D009_Id'];
		$D109_Id = $_REQUEST['D109_Id'];
		
		$selectT175E = "SELECT SUM(T175_Quantidade) as qnt FROM T175 WHERE T175_D009_Id='{$D009_Id}' AND T175_D109_Id='{$D109_Id}' AND T175_Flag_Operacao='E'";
		$resultT175E = mysql_query($selectT175E);
		$linhaT175E = mysql_fetch_array($resultT175E);
		
		$selectT175D = "SELECT SUM(T175_Quantidade) as qnt FROM T175 WHERE T175_D009_Id='{$D009_Id}' AND T175_D109_Id='{$D109_Id}' AND T175_Flag_Operacao='D'";
		$resultT175D = mysql_query($selectT175D);
		$linhaT175D = mysql_fetch_array($resultT175D);
		
		$saldo = ($linhaT175E['qnt'] - $linhaT175D['qnt']);
		$ret = $saldo;
		$saldoGravar = ($saldo - $qnt);
		$ret.=" ".$saldoGravar;
		if(($saldo > $saldoGravar) && $saldoGravar >= 0){
			// echo "entrou";
			// die();
			/**
			 * T175_Flag_Operacao
			 * E = EMPRESTIMO
			 * D = DEVOLUÇÃO
			 */
			$sqlT175 =<<<EOT
			INSERT INTO T175 (T175_D009_Id,
						      T175_C007_Id,
						      T175_D109_Id,
						      T175_Data_Retorno,
						      T175_Quantidade,
						      T175_Flag_Operacao)
					VALUES
							  ('{$D009_Id}',
							  '{$g['usuarioAtual']}',
							  '{$D109_Id}',
							  CURRENT_TIMESTAMP(),
							  '{$qnt}',
							  'D');
EOT;
			$resultT175 = mysql_query($sqlT175);
			
			if($resultT175){
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						alert("Devolução efetuada com sucesso!");
						window.location="./menu.php";
					}</script></div>
EOT;
			} else {
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						alert("Houve algum erro ao efetuar a devolução. Tente novamente.!");
						window.location="./menu.php";
					}</script></div>
EOT;
			}
		} else {
			$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						alert("Quantidade devolvida é maior que a quantidade emprestada!");
						$.getJSON('./devolucaoProduto.php?opcao=quantidade&scan=' + {$_REQUEST['scan']} + '&D109_Id=$D109_Id&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>
EOT;
		}
		
		///////////////////////////////////////////////////
		
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
				    	$.getJSON('./devolucaoProduto.php?opcao=cracha&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
			$D109 = mysql_fetch_array($res);
			if(mysql_num_rows($res) > 0){
				$D109_Id = $D109['D109_Id'];
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#barraTopPedido').html('Pedido: {$C007_Id}');
	    			modal('Pedido {$C007_Id}');
	    			$.getJSON('./devolucaoProduto.php?opcao=produtos&D109_Id={$D109_Id}&callback=?', function(request) {
	    				modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
			    } </script></div>
EOT;
			} else {
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert("Funcionário não cadastado.");
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
							<div class="alerta" style="margin-top: 15%"><p><p><strong style="font-size: 140%;"><br>PRODUTO INCORRETO.<br> Tente novamente<br> </br></strong></div>
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout("reload()",2000);
								function reload(){
				    				$.getJSON('./devolucaoProduto.php?opcao=produtos&C007_Id={$C007_Id}&scan={$D004_Id}&callback=?', function(request) {
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
							/*$('#scan').unbind('keypress');
							$('#scan').bind('keypress', function(e) {
								if (e.keyCode == 13) {*/
									if (confirm('Produto não possui código de barra cadastrado, deseja associar o Cod. {$scan} a ele?')) {
					    				modal('Verificando...');
					    				$.getJSON('./devolucaoProduto.php?opcao=busca&T185_Id={$row['T185_Id']}&C007_Id=$C007_Id&D049_Id=$D049_Id&scan=' + encodeURIComponent('{$scan}') + '&novo=S&callback=?', function(request) {
					    					modalRemove();
					    					$('#scan').val('');
											$('#contentMenu').empty().html(request.data);
										});
									} else {
										modal('Carregando...');
	    								$.getJSON('./devolucaoProduto.php?opcao=cracha&callback=?', function(request) {
	    									modalRemove();
											$('#contentMenu').empty().html(request.data);
										});
									}
							/*		return false;
								}
							});
							// (não perder o foco do campo scan)
				    		$('#scan').focus();*/
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
						$.getJSON('./devolucaoProduto.php?opcao=produtos&C007_Id={$C007_Id}&callback=?', function(request) {
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
							<input id="devolucaoProdutoQtdProdId" type="hidden" value="{$row['T006_Id']}">
						</div>
EOT;
						//echo $row['T185_Id'];
						$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');
    						$('#separacaoPedidoQtdProdSubmit').unbind('click').bind('click', function() {
    							if (confirm('Separar {$row['T006_Quantidade']} itens?')) {
    				 				modal('Verificando...');
    								$.getJSON('./devolucaoProduto.php?opcao=produtos&C007_Id={$C007_Id}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&T185_Id={$row['T185_Id']}&T006_Id={$row['T006_Id']}&separar={$row['T006_Quantidade']}&callback=?', function(request) {
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
    								$.getJSON('./devolucaoProduto.php?opcao=produtos&C007_Id={$C007_Id}&T006_Id=' + encodeURIComponent($('#devolucaoProdutoQtdProdId').val()) + '&T185_Id={$row['T185_Id']}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&divergencia=2&callback=?', function(request) {
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
    								$.getJSON('./devolucaoProduto.php?opcao=produtos&C007_Id={$C007_Id}&T006_Id=' + encodeURIComponent($('#devolucaoProdutoQtdProdId').val()) + '&T185_Id={$row['T185_Id']}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&callback=?', function(request) {
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