<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'alterar':
		$minimo = $_REQUEST['minimo'];
		$maximo = $_REQUEST['maximo'];
		$agrupamento = $_REQUEST['agrupamento'];
		$tipo = $_REQUEST['tipo'];
		$D009_Id = $_REQUEST['D009_Id'];
		$D004_Id = $_REQUEST['D004_Id'];
		$T066_Id = $_REQUEST['T066_Id'];
		$sql = "INSERT INTO T001 (
				T001_D009_Id,
				T001_Quantidade, 
				T001_C004_Id, 
				T001_Data_Lancamento, 
				T001_Descricao_Operacao, 
				T001_Flag_Operacao
				)VALUES(
				{$D009_Id},
				{$_REQUEST['balanco']},
				{$g['empresaAtual']},
				CURDATE(),
				'BALANCO-COLETOR',
				'B'								
			);";
				mysql_query($sql);
				
		$sql = "INSERT INTO T186 (
				T186_T066_Id,
				T186_T001_Id, 
				T186_Quantidade
				)VALUES(
				{$T066_Id},
				{$g['mysqlLastId']},
				{$_REQUEST['balanco']}			
			);";
				mysql_query($sql);
				
		$sql = "UPDATE T066 SET 
			T066_Minimo = $minimo,
			T066_Maximo = $maximo,
			T066_Agrupamento = $agrupamento WHERE T066_Id = $T066_Id;";
				mysql_query($sql);
		if($tipo == 1){
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$.getJSON('./inventario.php?opcao=balanco&callback=?', function(request) {
					$('#contentMenu').empty().html(request.data);
				});
				}
				</script>
				</div>
EOT;
		} else {
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$.getJSON('./inventario.php?opcao=produto&tipo=$tipo&D009_Id=$D009_Id&D004_Id=$D004_Id&callback=?', function(request) {
					$('#contentMenu').empty().html(request.data);
				});
				}
				</script>
				</div>
EOT;
				}
		break;	
	case 'produto':
		$D004_Id = $_REQUEST['D004_Id'];
		if (isset($_REQUEST['scan'])) {
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
			$resD009 = mysql_query($sql);
			if(mysql_num_rows($resD009) > 0){
				
				$row = mysql_fetch_array($resD009);
				$sqlT066 = "SELECT * FROM T066 LEFT JOIN D004 on D004_Id={$D004_Id} WHERE T066_D004_Id='{$D004_Id}' AND T066_D009_Id='{$row['D009_Id']}'";
				$resultT066 = mysql_query($sqlT066);
				
				
				$result = mysql_fetch_array($resultT066);
				$estoque = gCorrigeNumero($result['T066_Quantidade_Estoque']);
				$minimo = number_format($result['T066_Minimo'], 2);
				$maximo = number_format($result['T066_Maximo'], 2);
				$agrupamento = number_format($result['T066_Agrupamento'], 0);
	        	$D009_Id = $row['D009_Id'];
				$resposta['data'] = <<<EOT
				<table style="width:90%;" cellspacing="0">
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">{$row['D001_Descricao_Produto']}<br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Estoque: {$estoque}<br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Balanço: <input type="text" id="balanco" size="23"><br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Mínimo: </strong><input type="text" id="minimo" size="5" value="{$minimo}">
				<strong style="font-size: 120%;"> Máximo: <input type="text" id="maximo" size="6" value="{$maximo}"><br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Agrupamento: <input type="text" id="agrupamento" size="17" value="{$agrupamento}"><br /></td></tr>
				<tr><td><button class="bigBtn" id="alterarEstoque">Alterar</button></td></tr>
				<tr><td><button class="bigBtn" id="voltar">Voltar</button></td></tr>
				
				</table>
EOT;
				$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');
    						$('#alterarEstoque').unbind('click').bind('click', function() {
    				 				modal('Verificando...');
    								$.getJSON('./inventario.php?opcao=alterar&minimo='+$('#minimo').val()+'&maximo='+$('#maximo').val()+'&agrupamento='+$('#agrupamento').val()+'&tipo={$result['D004_Flag_Tipo']}&D004_Id=$D004_Id&D009_Id=$D009_Id&T066_Id={$result['T066_Id']}&balanco=' + encodeURIComponent($('#balanco').val()) + '&callback=?', function(request) {
    									modalRemove();
    									$('#scan').val('');
  
										$('#contentMenu').empty().html(request.data);
									});
    						});
    						
    						$('#voltar').unbind('click').bind('click', function() {
    				 				window.location="./menu.php";
    						});
    						
    						$('#balanco').focus();
    						
    						$('#balanco').bind('keypress', function(e) {
								if (e.keyCode == 13) {
						    		modal('Verificando...');
	    							$.getJSON('./inventario.php?opcao=alterar&minimo='+$('#minimo').val()+'&maximo='+$('#maximo').val()+'&agrupamento='+$('#agrupamento').val()+'&tipo={$result['D004_Flag_Tipo']}&D004_Id=$D004_Id&D009_Id=$D009_Id&T066_Id={$result['T066_Id']}&balanco=' + encodeURIComponent($('#balanco').val()) + '&callback=?', function(request) {
	    								modalRemove();
	    								$('#scan').val('');
	  
										$('#contentMenu').empty().html(request.data);
									});
								}
								if(e.keyCode == 27){
									window.location="./menu.php";
								}
							});
							
							$('#minimo').bind('keypress', function(e) {
								if (e.keyCode == 13) {
						    		modal('Verificando...');
	    							$.getJSON('./inventario.php?opcao=alterar&minimo='+$('#minimo').val()+'&maximo='+$('#maximo').val()+'&agrupamento='+$('#agrupamento').val()+'&tipo={$result['D004_Flag_Tipo']}&D004_Id=$D004_Id&D009_Id=$D009_Id&T066_Id={$result['T066_Id']}&balanco=' + encodeURIComponent($('#balanco').val()) + '&callback=?', function(request) {
	    								modalRemove();
	    								$('#scan').val('');
	  
										$('#contentMenu').empty().html(request.data);
									});
								}
								if(e.keyCode == 27){
									window.location="./menu.php";
								}
							});
							
							$('#maximo').bind('keypress', function(e) {
								if (e.keyCode == 13) {
						    		modal('Verificando...');
	    							$.getJSON('./inventario.php?opcao=alterar&minimo='+$('#minimo').val()+'&maximo='+$('#maximo').val()+'&agrupamento='+$('#agrupamento').val()+'&tipo={$result['D004_Flag_Tipo']}&D004_Id=$D004_Id&D009_Id=$D009_Id&T066_Id={$result['T066_Id']}&balanco=' + encodeURIComponent($('#balanco').val()) + '&callback=?', function(request) {
	    								modalRemove();
	    								$('#scan').val('');
	  
										$('#contentMenu').empty().html(request.data);
									});
								}
								if(e.keyCode == 27){
									window.location="./menu.php";
								}
							});
							
							$('#agrupamento').bind('keypress', function(e) {
								if (e.keyCode == 13) {
						    		modal('Verificando...');
	    							$.getJSON('./inventario.php?opcao=alterar&minimo='+$('#minimo').val()+'&maximo='+$('#maximo').val()+'&agrupamento='+$('#agrupamento').val()+'&tipo={$result['D004_Flag_Tipo']}&D004_Id=$D004_Id&D009_Id=$D009_Id&T066_Id={$result['T066_Id']}&balanco=' + encodeURIComponent($('#balanco').val()) + '&callback=?', function(request) {
	    								modalRemove();
	    								$('#scan').val('');
	  
										$('#contentMenu').empty().html(request.data);
									});
								}
								if(e.keyCode == 27){
									window.location="./menu.php";
								}
							});
    					}</script></div>   						
EOT;
			} else {
				$sql = "SELECT * FROM T066
					LEFT JOIN D009 ON D009_Id = T066_D009_Id 
					LEFT JOIN D049 ON D049_Id = D009_D049_Id
					LEFT JOIN D001 ON D001_Id = D049_D001_Id
					LEFT JOIN D004 ON D004_Id = T066_D004_Id 
			         WHERE D009_C004_Id = '{$g['empresaAtual']}'
                        AND (D049_Codigo_Barras = '{$_REQUEST['scan']}' 
			            OR D001_Codigo_Produto = '{$_REQUEST['scan']}'
			            OR D049_Id = '{$_REQUEST['scan']}')";
				$res = mysql_query($sql);
				$result = mysql_fetch_array($res);
				$resposta['data'] .= <<<EOT
				<div class="alerta">
				<strong style="font-size: 140%;">Produto em outra locação</strong>
				</div>
				<div class="produto">
				<strong style="font-size: 120%;">Produto:<br />{$result['D001_Descricao_Produto']}</strong><br />
				<strong style="font-size: 120%;">Local: <br />{$result['D004_Local']}</strong>
				
				</div>	
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setTimeout("reload()",2000);
				function reload(){
					$.getJSON('./inventario.php?opcao=produto&D004_Id=$D004_Id&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
				}
				}
				</script>
				</div>
EOT;
			}
		} else {
			$sql = "SELECT * FROM T066 
			LEFT JOIN D004 ON D004_Id = T066_D004_Id
			WHERE D004_Id = $D004_Id;";
			$res = mysql_query($sql);
			$res = mysql_fetch_array($res);
			if($res['D004_Flag_Tipo'] == '1'){
				$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$.getJSON('./inventario.php?opcao=produto&scan={$res['T066_D009_Id']}&D004_Id=$D004_Id&callback=?', function(request) {
						$('#scan').val('');
						$('#contentMenu').empty().html(request.data);
					});
				}</script></div>
EOT;
			} else {
				$resposta['data'] .= <<<EOT
				<p>
				<div class="produto">
				<strong style="font-size: 140%;">Local: {$res['D004_Local']}</strong>
				<p>
				<div class="alerta">
					<strong style="font-size: 140%;">Leia um produto</strong>
				</div>
				</div>
				<p>
				<button class="bigBtn" id="voltar">Voltar</button>
				
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#scan').unbind('keypress');
					$('#scan').bind('keypress', function(e) {
						if (e.keyCode == 13) {
					    	modal('Verificando...');
					    	$.getJSON('./inventario.php?opcao=produto&scan=' + encodeURIComponent($('#scan').val()) + '&D004_Id={$D004_Id}&callback=?', function(request) {
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
		}
		break;	
			
	case 'balanco':
		if (isset($_REQUEST['scan'])) {
			$sql = "SELECT * FROM D004 WHERE D004_Id = '{$_REQUEST['scan']}';";
			$res = mysql_query($sql);
			if(mysql_numrows($res) > 0)	{
				$sql2 = "SELECT * FROM T066  
							LEFT JOIN D004 ON T066_D004_Id = D004_Id 
							WHERE T066_D004_Id = '{$_REQUEST['scan']}';";
				$res2 = mysql_query($sql2);
				if(mysql_numrows($res2) > 0)	{
					$result = mysql_fetch_assoc($res2);
					$D004_Id = $result['T066_D004_Id'];
					if($result['D004_Flag_Tipo'] != NULL){
						$resposta['data'] = <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
								$.getJSON('./inventario.php?opcao=produto&D004_Id=$D004_Id&callback=?', function(request) {
			   	    				$('#scan').val('');
									$('#contentMenu').empty().html(request.data);
								});
							}</script></div>
EOT;
					} else {
						$resposta['data'] .= <<<EOT
							<div class="alerta"><strong style="font-size: 140%;"><br>Informe o tipo da locação<br> </br></strong></div>
							<button class="bigBtn" id="tipo_1">Apenas um Produto</button>
							<button class="bigBtn" id="tipo_2">Vários Produtos</button>
							<button class="bigBtn" id="voltar">Voltar</button>
EOT;
						$resposta['data'] .= <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#tipo_1').unbind('click').bind('click', function() {
								if (confirm('Locação com apenas um produto?')) {
		    				 		modal('Verificando...');
		    						$.getJSON('./inventario.php?opcao=alterarTipo&D004_Id=$D004_Id&tipo=1&callback=?', function(request) {
		    							modalRemove();
		    							// Proximo Produto da lista
										$('#contentMenu').empty().html(request.data);
									});
								}
								return false;
							});
							
							$('#tipo_2').unbind('click').bind('click', function() {
								if (confirm('Locação com vários produtos?')) {
		    				 		modal('Verificando...');
		    						$.getJSON('./inventario.php?opcao=alterarTipo&D004_Id=$D004_Id&tipo=2&callback=?', function(request) {
		    							modalRemove();
		    							// Proximo Produto da lista
										$('#contentMenu').empty().html(request.data);
									});
								}
								return false;
							});
								
							$('#voltar').unbind('click').bind('click', function() {
    				 			$.getJSON('./inventario.php?opcao=balanco&callback=?', function(request) {
									$('#contentMenu').empty().html(request.data);
								})
    						});
							}</script></div>					
EOT;
					}

				} else {
					$resposta['data'] .= <<<EOT
					<div class="alerta" style="margin-top: 45%"><strong style="font-size: 140%;"><br>LOCAÇÃO SEM PRODUTOS<br> </br></strong></div>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout("reload()",2000);
					function reload(){
						$.getJSON('./inventario.php?opcao=balanco&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}
					}</script></div>
EOT;
				}
			} else {
				$resposta['data'] .= <<<EOT
				<div class="alerta" style="margin-top: 45%"><strong style="font-size: 140%;"><br>LOCAÇÃO NÃO CADASTRADA<br> </br></strong></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setTimeout("reload()",2000);
				function reload(){
					$.getJSON('./inventario.php?opcao=balanco&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
				}
				}</script></div>
EOT;
			}
		} else {
			$resposta['data'] .= <<<EOT
			<div class="alerta" style="margin-top: 30%">
				<strong style="font-size: 140%;"><br>Leia uma locação<br> </br></strong>
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
				    	$.getJSON('./inventario.php?opcao=balanco&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
	case 'alterarTipo':
		$D004_Id = $_REQUEST['D004_Id'];
		$sql = "UPDATE D004 SET D004_Flag_Tipo = '{$_REQUEST['tipo']}' WHERE D004_Id = $D004_Id;";
		mysql_query($sql);
		if(isset($_REQUEST['tipo'])){
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				$.getJSON('./inventario.php?opcao=produto&D004_Id=$D004_Id&callback=?', function(request) {
			   		$('#contentMenu').empty().html(request.data);
				});
				}</script></div>
EOT;
		}
		break;
	
	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";