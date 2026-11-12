<?php               
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {

	// 1. LER E VALIDAR A LOCACAO DE ONDE SERÁ RETIRADO O PRODUTO --------------------------------------------------------------------------------------
    case 'retirarLerLocacao':

		// Scaneou a locacao
		if (isset($_REQUEST['scan'])) {

			$sql = "SELECT * FROM D004 WHERE D004_Id = '{$_REQUEST['scan']}';";
			$res = mysql_query($sql);

			// Existe em D004
			if(mysql_numrows($res) > 0)	{

				$sql = "SELECT * FROM T066  
								LEFT JOIN D004 ON T066_D004_Id = D004_Id 
								WHERE T066_D004_Id = '{$_REQUEST['scan']}'";
				$res = mysql_query($sql);

				// Existe em T066
				if(mysql_numrows($res) > 0)	{

					$result = mysql_fetch_assoc($res);
					$D004_Id = $result['T066_D004_Id'];

					// Chama retirarProduto
					$resposta['data'] = <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
							$.getJSON('./movimentacao.php?opcao=retirarProduto&D004_Id=$D004_Id&callback=?', function(request) {
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
	    				$.getJSON('./movimentacao.php?opcao=retirarLerLocacao&callback=?', function(request) {
	    					modalRemove();
	    					// Proximo Produto da lista
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>					
EOT;
				}

			} else {

                // Locação scaneada não existe em D004
				$resposta['data'] .= <<<EOT
				<div class="alerta" style="margin-top: 45%;"><strong style="font-size: 140%;"><br>LOCAÇÃO NÃO ENCONTRADA<br> </br></strong></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				setTimeout("reload()",2000);
				function reload(){
					$.getJSON('./movimentacao.php?opcao=retirarLerLocacao&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
				}
				}</script></div>
EOT;
			}

		} else {
		    // Pede para scanear a Locação
			$resposta['data'] .= <<<EOT
			<div class="alerta">
				<strong style="font-size: 140%;"><br>Leia uma locação<br>(retirar)</br></strong>
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
						$.getJSON('./movimentacao.php?opcao=retirarLerLocacao&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
						    modalRemove();
						    $('#scan').val('');
							$('#contentMenu').empty().html(request.data);
						});
					}
				});
				$('#scan').focus();
				
				$('#voltar').unbind('click').bind('click', function(e) {
				    modal('Carregando...');
				    $.getJSON('./menuMovimentacao.php?callback=?', function(request) {
				    	modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				});
			}</script></div>
EOT;
		}
		break;

	// MOSTRA FORMULARIO COM O PRODUTO E PEDE QUANTIDADE A RETIRAR E PEDE PARA SCANEAR O PRODUTO ----------------------------------------------------------------------------
	case 'retirarProduto':

		$D004_Id = $_REQUEST['D004_Id'];
		
		if (isset($_REQUEST['scan'])) {

			$sql = "SELECT * FROM T066
				 LEFT JOIN D009 ON T066_D009_Id = D009_Id   
				 LEFT JOIN D049 ON D049_Id = D009_D049_Id
   				 LEFT JOIN D001 ON D001_Id = D049_D001_Id
				 LEFT JOIN D004 ON T066_D004_Id = D004_Id  
				     WHERE D004_Id = $D004_Id
				       AND D009_C004_Id = '{$g['empresaAtual']}' 
				       AND (D049_Codigo_Barras = '{$_REQUEST['scan']}' 
			            OR D001_Codigo_Produto = '{$_REQUEST['scan']}'
			            OR D049_Id = '{$_REQUEST['scan']}')";

			$res = mysql_query($sql);

            // Se o produto foi encontrado na locação
			if(mysql_num_rows($res) > 0){

				$result = mysql_fetch_array($res);
				$T066_Id = $result['T066_Id'];
				$D009_Id = $result['D009_Id'];
			
				$estoque = gCorrigeNumero($result['T066_Quantidade_Estoque_Liquido']);
				$estoqueLiquido = gCorrigeNumero($result['T066_Quantidade_Estoque']);

				$resposta['data'] = <<<EOT
				<table style="width: 100%; align="center">
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Produto: <br>{$result['D001_Descricao_Produto']}<br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Estoque Líquido: {$estoqueLiquido}<br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Estoque: {$estoque}<br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Retirar: <br><input type="text" id="valorRetirou"><br /></td></tr>
				<tr><td><button class="bigBtn" id="retirar">Retirar</button></td></tr>
				<tr><td><button class="bigBtn" id="voltar">Voltar</button></td></tr>
				</table>
EOT;
				// Chama Retirar novamente para gravar o valor retirado
				$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');
    						$('#retirar').unbind('click').bind('click', function() {
	    						var valorRetirado = parseFloat({$result['T066_Retirou_Qtd']}) + parseFloat($('#valorRetirou').val());
	    						var valor = $('#valorRetirou').val();
	    						if ($('#valorRetirou').val() <= {$result['T066_Quantidade_Estoque_Liquido']}) {
	    				 				modal('Verificando...');
	    								$.getJSON('./movimentacao.php?opcao=retirar&D009_Id=$D009_Id&T066_Id=$T066_Id&valor='+valor+'&valorRetirado=' +valorRetirado+ '&callback=?', function(request) {
	    									modalRemove();
		    								$('#scan').val('');
											$('#contentMenu').empty().html(request.data);
										});
									return false;
								} else {
									alert('A quantidade informada é maior que o estoque');
								}
    						});
    						$('#voltar').unbind('click').bind('click', function(e) {
				    			modal('Carregando...');
				    			$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
				    				modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
				    		});
    						
    						$('#valorRetirou').focus();
							
							
							$('#valorRetirou').bind('keypress', function(e) {
							if (e.keyCode == 13) {
					    		var valorRetirado = parseFloat({$result['T066_Retirou_Qtd']}) + parseFloat($('#valorRetirou').val());
	    						var valor = $('#valorRetirou').val();
	    						if ($('#valorRetirou').val() <= {$result['T066_Quantidade_Estoque_Liquido']}) {
	    				 				modal('Verificando...');
	    								$.getJSON('./movimentacao.php?opcao=retirar&D009_Id=$D009_Id&T066_Id=$T066_Id&valor='+valor+'&valorRetirado=' +valorRetirado+ '&callback=?', function(request) {
	    									modalRemove();
		    								$('#scan').val('');
											$('#contentMenu').empty().html(request.data);
										});
									return false;
								} else {
									alert('A quantidade informada é maior que o estoque');
								}
							}
							if(e.keyCode == 27){
								modal('Carregando...');
				    			$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
				    				modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
							}
						});
    					}</script></div>   						
EOT;
			} else {

     			$sql = "SELECT * FROM T066
	    	     LEFT JOIN D009 ON T066_D009_Id = D009_Id   
			     LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			     LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			     LEFT JOIN D082 ON D082_Id=D049_D082_Id 
			         WHERE D009_C004_Id='{$g['empresaAtual']}' 
			           AND (D049_Codigo_Barras = '{$_REQUEST['scan']}' 
			            OR D001_Codigo_Produto = '{$_REQUEST['scan']}'
			            OR D049_Id = '{$_REQUEST['scan']}')";

				$res = mysql_query($sql);
				$result = mysql_fetch_array($res);
				$resposta['data'] .= <<<EOT
				<div class="alerta">
				<strong style="font-size: 140%;">Produto em outra locação ou não existe</strong>
				</div>
				<div class="produto">
				<strong style="font-si ze: 120%;">Produto:<br />{$result['D001_Descricao_Produto']}</strong><br />
				<strong style="font-si ze: 120%;">Local: <br />{$result['D004_Local']}</strong>
				
				</div>	
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout("reload()",2000);
					function reload(){
						$.getJSON('./movimentacao.php?opcao=retirarProduto&D004_Id=' + {$D004_Id} + '&callback=?', function(request) {
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
					$.getJSON('./movimentacao.php?opcao=retirarProduto&scan={$res['T066_D009_Id']}&D004_Id=$D004_Id&callback=?', function(request) {
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
					<strong style="font-size: 140%;"><br>Leia um produto<br> </br></strong>
				</div>
				</div>
				<p>
				<button class="bigBtn" id="voltar">Voltar</button>
				
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#scan').unbind('keypress');
					$('#scan').bind('keypress', function(e) {
						if (e.keyCode == 13) {
					    	modal('Verificando...');
					    	$.getJSON('./movimentacao.php?opcao=retirarProduto&scan=' + encodeURIComponent($('#scan').val()) + '&D004_Id=$D004_Id&callback=?', function(request) {
					    		modalRemove();
					    		$('#scan').val('');
								$('#contentMenu').empty().html(request.data);
							});
						}
					});
					$('#scan').focus();
					
					$('#voltar').unbind('click').bind('click', function(e) {
						modal('Carregando...');
					    $.getJSON('./menuMovimentacao.php?callback=?', function(request) {
					    	modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
					});
				}</script></div>
EOT;
			}
		}
		break;

    //  SCANEA A LOCACAO E GRAVA A QUANTIDADE NO T066 -----------------------------------------------------------------------------------------------------------------------
	case 'retirar':

		$valor = $_REQUEST['valor'];
		$T066_Id = $_REQUEST['T066_Id'];
		$D009_Id = $_REQUEST['D009_Id'];
		$D004_Id = $_REQUEST['D004_Id'];

        // Ja digitou o valor para retirar
		if(isset($_REQUEST['valorRetirado'])){

			$sql = "UPDATE T066 SET T066_C007_Id_Retirou = '{$g['usuarioAtual']}', T066_Retirou_Qtd = '{$_REQUEST['valorRetirado']}', T066_Quantidade_Estoque_Liquido = $valor WHERE T066_Id = $T066_Id;";
			mysql_query($sql);
			
			$sql = "INSERT INTO T001 (T001_D009_Id,	T001_Quantidade, T001_C004_Id, T001_Data_Lancamento, T001_Descricao_Operacao, T001_Flag_Operacao
				)VALUES(
				{$D009_Id}, {$valor}, {$g['empresaAtual']}, CURDATE(), 'MOVIMENTAÇÃO-RETIRADA', 'S'								
				);";
			mysql_query($sql);
				
			$sql = "INSERT INTO T186 (T186_T066_Id,	T186_T001_Id, T186_Quantidade
				)VALUES({$T066_Id},	{$g['mysqlLastId']}, {$valor}			
				);";
			mysql_query($sql);

			// Chama colocar produto passando T066_Id de origem e o $d009_Id
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    			$.getJSON('./movimentacao.php?opcao=retirarLerLocacao&T066_Fonte_Id=$T066_Id&D009_Id=$D009_Id&callback=?', function(request) {
				$('#contentMenu').empty().html(request.data);
			});
			}</script></div>					
EOT;
		}
		
        break;
    
	// SCANEA LOCAÇÃO E GRAVA QUANTIDADE COLOCADA ---------------------------------------------------------------------------------------------------------------------------
	case 'colocar':

		$T066_Id = $_REQUEST['T066_Id'];
		$T066_Fonte_Id = $_REQUEST['T066_Fonte_Id'];
		$D009_Id = $_REQUEST['D009_Id'];
		$D004_Id = $_REQUEST['D004_Id'];
		$valorRealocado = $_REQUEST['valorRealocado']; 
		$valor = $_REQUEST['valor']; 
		$minimo = $_REQUEST['minimo']; 
		$maximo = $_REQUEST['maximo']; 
		$agrupamento = $_REQUEST['agrupamento']; 

		if(isset($valorRealocado)){
			if($valorRealocado > 0){
				$sql = "UPDATE T066 SET T066_Retirou_Qtd = '$valorRealocado' WHERE T066_Id = $T066_Fonte_Id;";
				mysql_query($sql);
			} else {
				$sql = "UPDATE T066 SET T066_C007_Id_Retirou = '', T066_Retirou_Qtd = '$valorRealocado' WHERE T066_Id = $T066_Fonte_Id;";
				mysql_query($sql);
			}

			if(isset($_REQUEST['novoLocal'])){
				$sql = "INSERT INTO T066 (T066_D004_Id, T066_D009_Id, T066_Minimo, T066_Maximo, T066_Agrupamento) 
				                  VALUES ('$D004_Id', '$D009_Id', '$minimo', '$maximo', '$agrupamento');";
				mysql_query($sql);	
				$T066_Id = $g['mysqlLastId'];
			}
		
			$sql = "INSERT INTO T001 (T001_D009_Id,	T001_Quantidade, T001_C004_Id, T001_Data_Lancamento, T001_Descricao_Operacao, T001_Flag_Operacao)
			                  VALUES ('{$D009_Id}', '{$valor}', '{$g['empresaAtual']}', CURDATE(), 'MOVIMENTAÇÃO-COLOCAÇÃO', 'E');";
			mysql_query($sql);
				
			$sql = "INSERT INTO T186 (T186_T066_Id,	T186_T001_Id, T186_Quantidade)
				               VALUES('{$T066_Id}','{$g['mysqlLastId']}', '{$valor}');";
			mysql_query($sql);
			
			$valorRealocado = null;
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$.getJSON('./movimentacao.php?opcao=colocarProduto&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
				}</script></div>		
EOT;
		}
		
		$resposta['data'] .= <<<EOT
			<div class="alerta">
			<strong style="font-size: 140%;"><br>Leia uma locação<br>(colocar)</br></strong>
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
			    	$.getJSON('./movimentacao.php?opcao=colocarProduto&D009_Id=$D009_Id&T066_Fonte_Id=$T066_Fonte_Id&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
			    		modalRemove();
			    		$('#scan').val('');
						$('#contentMenu').empty().html(request.data);
					});
				}
			});
			$('#scan').focus();
			
			$('#voltar').unbind('click').bind('click', function(e) {
				modal('Carregando...');
			    $.getJSON('./menuMovimentacao.php?callback=?', function(request) {
			    	modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			});
		}</script></div>
EOT;

	break;
		
    // MOSTRA FORMULARIO PARA CONFIRMAR A COLOCAÇÃO DO PRODUTO NA LOCAÇÃO --------------------------------------------------------------------------------------------------
	case 'colocarProduto':

		$T066_Fonte_Id = $_REQUEST['T066_Fonte_Id'];
		$D009_Id = $_REQUEST['D009_Id'];
		$D004_Id = $_REQUEST['scan'];
		$minimo = $_REQUEST['minimo'];
		$maximo = $_REQUEST['maximo'];
		$agrupamento = $_REQUEST['agrupamento'];

		if(isset($T066_Fonte_Id)){

			$sql = "SELECT * FROM T066 
				LEFT JOIN D009 ON T066_D009_Id = D009_Id   
				LEFT JOIN D049 ON D049_Id = D009_D049_Id
				LEFT JOIN D001 ON D001_Id = D049_D001_Id
				LEFT JOIN D004 ON T066_D004_Id = D004_Id  
				WHERE T066_Id = '$T066_Fonte_Id'";
			$res = mysql_query($sql);
			$res2 = mysql_fetch_array($res);

		}

		if (isset($_REQUEST['scan'])) {

			$sql = "SELECT * FROM T066
				LEFT JOIN D009 ON T066_D009_Id = D009_Id   
				LEFT JOIN D049 ON D049_Id = D009_D049_Id
				LEFT JOIN D001 ON D001_Id = D049_D001_Id
				LEFT JOIN D004 ON T066_D004_Id = D004_Id  
				WHERE D004_Id = '$D004_Id'
				  AND D009_Id = '$D009_Id' ";
			$res = mysql_query($sql);

			//Local tem produtos armazenados?
			if(mysql_numrows($res) > 0) {

				$result = mysql_fetch_array($res);

				//Não tem TIPO definido, pede para configurar tipo
				if($result['D004_Flag_Tipo'] == NULL) {

					$resposta['data'] = <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$.getJSON('./movimentacao.php?opcao=alterarTipo&D009_Id=$D009_Id&D004_Id=$D004_Id&T066_Id=$T066_Fonte_Id&callback=?', function(request) {
								$('#contentMenu').empty().html(request.data);
							});
						}</script></div>
EOT;

				} else {

					// Pode varios produtos
					if($result['D004_Flag_Tipo'] == 2){

						$T066_Id = $result['T066_Id'];
						$retirado = gCorrigeNumero($res2['T066_Retirou_Qtd']);
						$resposta['data'] = <<<EOT
						<table style="width: 100%; align="center">
						<tr><td style="text-align: center;"><strong style="font-size: 120%;">Movimentando<br> DE: {$res2['D004_Local']} PARA: {$result['D004_Local']}</strong></td></tr>
						<tr><td style="text-align: center;"><strong style="font-size: 120%;">Produto: <br>{$res2['D001_Descricao_Produto']}<br /></td></tr>
						<tr><td style="text-align: center;"><strong style="font-size: 120%;">Quantidade retirada: {$retirado}<br /></td></tr>
						<tr><td style="text-align: center;"><strong style="font-size: 120%;">Colocar: <br><input type="text" id="valorColocado"><br /></td></tr>
						<tr><td><button class="bigBtn" id="colocar">Colocar</button></td></tr>
						<tr><td><button class="bigBtn" id="voltar">Voltar</button></td></tr>
						</table>
EOT;
						$resposta['data'] .= <<<EOT
						<div id="conteudo" style="display: none;">
						Seu conteúdo aqui...
						</div>
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');					    	
							$('#colocar').unbind('click').bind('click', function() {
						    	var valor = $('#valorColocado').val();
						    	var valorRealocado = {$res2['T066_Retirou_Qtd']} - $('#valorColocado').val();
						    	var limite = parseFloat($('#valorColocado').val()) + parseFloat({$result['T066_Quantidade_Estoque']});
								if ($('#valorColocado').val() <= {$res2['T066_Retirou_Qtd']}) {	
						    		if(limite <= {$result['T066_Maximo']}){			
										modal('Verificando...');
							    		$.getJSON('./movimentacao.php?opcao=colocar&minimo=$minimo&maximo=$maximo&agrupamento=$agrupamento&D009_Id=$D009_Id&T066_Id=$T066_Id&T066_Fonte_Id=$T066_Fonte_Id&valorRealocado='+valorRealocado+'&valor='+valor+'&callback=?', function(request) {
							    			modalRemove();
											$('#scan').val('');
											$('#contentMenu').empty().html(request.data);
										});
										return false;
									} else {
										alert('A quantidade mais o estoque ultrapassam o máximo da locação');
									}
								return false;
								} else {
									//alert('A quantidade informada é maior do que o retirado');
									document.getElementById(conteudo).style.display = "inline";
								}
					    	});
					    			
					    	$('#voltar').unbind('click').bind('click', function(e) {
								modal('Carregando...');
								$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
									modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
							});
									
					    	$('#valorColocado').focus();
							
							$('#valorColocado').bind('keypress', function(e) {
							if (e.keyCode == 13) {
					    		var valor = $('#valorColocado').val();
						    	var valorRealocado = {$res2['T066_Retirou_Qtd']} - $('#valorColocado').val();
						    	var limite = parseFloat($('#valorColocado').val()) + parseFloat({$result['T066_Quantidade_Estoque']});
								if ($('#valorColocado').val() <= {$res2['T066_Retirou_Qtd']}) {	
						    		if(limite <= {$result['T066_Maximo']}){			
										modal('Verificando...');
							    		$.getJSON('./movimentacao.php?opcao=colocar&minimo=$minimo&maximo=$maximo&agrupamento=$agrupamento&D009_Id=$D009_Id&T066_Id=$T066_Id&T066_Fonte_Id=$T066_Fonte_Id&valorRealocado='+valorRealocado+'&valor='+valor+'&callback=?', function(request) {
							    			modalRemove();
											$('#scan').val('');
											$('#contentMenu').empty().html(request.data);
										});
										return false;
									} else {
										alert('A quantidade mais o estoque ultrapassam o máximo da locação');
									}
								return false;
								} else {
									//alert('A quantidade informada é maior do que o retirado');
									document.getElementById(conteudo).style.display = "inline";
								}
							}
						});
			    	}</script></div>   						
EOT;
		
					} else {

						// Produto esta em locação 1-1
						if($result['D009_Id'] == $D009_Id){

							$T066_Id = $result['T066_Id'];
							$retirado = gCorrigeNumero($res2['T066_Retirou_Qtd']);
							$resposta['data'] = <<<EOT
								<table style="width: 100%; align="center">
								<tr><td style="text-align: center;"><strong style="font-size: 120%;">Movimentando<br> DE: {$res2['D004_Local']} PARA: {$result['D004_Local']}</strong></td></tr>
								<tr><td style="text-align: center;"><strong style="font-size: 120%;">Produto: <br>{$result['D001_Descricao_Produto']}<br /></td></tr>
								<tr><td style="text-align: center;"><strong style="font-size: 120%;">Quantidade retirada: {$retirado}<br /></td></tr>
								<tr><td style="text-align: center;"><strong style="font-size: 120%;">Colocar: <br><input type="text" id="valorColocado"><br /></td></tr>
								<tr><td><button class="bigBtn" id="colocar">Colocar</button></td></tr>
								<tr><td><button class="bigBtn" id="voltar">Voltar</button></td></tr>
								</table>
EOT;
								//if ($('#valorRetirado').val() <= {$result['T066_Quantidade_Estoque_Liquido']}) {
							$resposta['data'] .= <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$('#colocar').unbind('click').bind('click', function() {
				    				var valor = $('#valorColocado').val();
					    			var valorRealocado = '{$res2['T066_Retirou_Qtd']}' - $('#valorColocado').val();
				    				var limite = parseFloat($('#valorColocado').val()) + parseFloat({$result['T066_Quantidade_Estoque']});
					    			if ($('#valorColocado').val() <= {$res2['T066_Retirou_Qtd']}) {
					    				if(limite <= {$result['T066_Maximo']}){	
				    						modal('Verificando...');
					    					$.getJSON('./movimentacao.php?opcao=colocar&minimo=$minimo&maximo=$maximo&agrupamento=$agrupamento&D009_Id=$D009_Id&T066_Fonte_Id=$T066_Fonte_Id&valorRealocado='+valorRealocado+'&valor='+valor+'&callback=?', function(request) {
					    						modalRemove();
												$('#scan').val('');
												$('#contentMenu').empty().html(request.data);
											});
											return false;
										} else {
											alert('A quantidade mais o estoque ultrapassam o máximo da locação');
										}
									return false;
									} else {
										alert('A quantidade informada é maior do que o retirado');
									}
				    				});
				    						
				    				$('#voltar').unbind('click').bind('click', function(e) {
								    	modal('Carregando...');
								    	$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
								    		modalRemove();
											$('#contentMenu').empty().html(request.data);
										});
								    });
								    		
				    				$('#valorColocado').focus();
				    				}</script></div>   						
EOT;
						} else {

							$resposta['data'] = <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							alert('Produto não está nesta locação');
							$.getJSON('./movimentacao.php?opcao=colocar&T066_Id=$T066_Fonte_Id&D009_Id=$D009_Id&callback=?', function(request) {
								$('#contentMenu').empty().html(request.data);
							});
							}
							</script>
							</div>				
EOT;

						}
					}
				}
				
			} else {
				
				$sql = "SELECT * FROM D004 WHERE D004_Id = $D004_Id;";
				$res = mysql_query($sql);
				
				if(mysql_numrows($res) > 0){

					$result2 = mysql_fetch_array($res);
					if($result2['D004_Flag_Tipo'] == NULL){

						$resposta['data'] = <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$.getJSON('./movimentacao.php?opcao=alterarTipo&D009_Id=$D009_Id&D004_Id=$D004_Id&T066_Id=$T066_Fonte_Id&callback=?', function(request) {
									$('#contentMenu').empty().html(request.data);
								});
							}</script></div>
EOT;

					} else {

						$retirado = gCorrigeNumero($res2['T066_Retirou_Qtd']);

						if(isset($_REQUEST['minimo'])) {
							$minimo = $_REQUEST['minimo'];
							$maximo = $_REQUEST['maximo'];
							$agrupamento = $_REQUEST['agrupamento'];
							$resposta['data'] = <<<EOT
							<table style="width: 100%; align="center">
							<tr><td style="text-align: center;"><strong style="font-size: 120%;">Movimentando<br> DE: {$res2['D004_Local']} PARA: {$result2['D004_Local']}</strong></td></tr>
							<tr><td style="text-align: center;"><strong style="font-size: 120%;">Produto: <br>{$res2['D001_Descricao_Produto']}<br /></td></tr>
							<tr><td style="text-align: center;"><strong style="font-size: 120%;">Quantidade retirada: {$retirado}<br /></td></tr>
							<tr><td style="text-align: center;"><strong style="font-size: 120%;">Colocar: <br><input type="text" id="valorColocado"><br /></td></tr>
							<tr><td><button class="bigBtn" id="colocar">Colocar</button></td></tr>
							<tr><td><button class="bigBtn" id="voltar">Voltar</button></td></tr>
							</table>
EOT;
							$resposta['data'] .= <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#colocar').unbind('click').bind('click', function() {
									var valor = $('#valorColocado').val();
									var valorRealocado = {$res2['T066_Retirou_Qtd']} - $('#valorColocado').val();
									var novo = 'S';
							    	if ($('#valorColocado').val() <= {$res2['T066_Retirou_Qtd']}) {
							    		modal('Verificando...');
							    		$.getJSON('./movimentacao.php?opcao=colocar&minimo=$minimo&maximo=$maximo&agrupamento=$agrupamento&D009_Id=$D009_Id&D004_Id=$D004_Id&novoLocal='+novo+'&T066_Id=$T066_Id&T066_Fonte_Id=$T066_Fonte_Id&valorRealocado='+valorRealocado+'&valor='+valor+'&callback=?', function(request) {
							    			modalRemove();
											$('#scan').val('');
											$('#contentMenu').empty().html(request.data);
										});
										return false;
									} else {
										alert('A quantidade informada é maior do que o retirado');
									}
						    	});
						    						
						    	$('#voltar').unbind('click').bind('click', function(e) {
									modal('Carregando...');
									$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
										modalRemove();
										$('#contentMenu').empty().html(request.data);
									});
								});
										    		
						    	$('#valorColocado').focus();
						    }</script></div>   						
EOT;
							
						} else {

							$resposta['data'] .= <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$.getJSON('./movimentacao.php?opcao=regrasLocacao&D009_Id=$D009_Id&D004_Id=$D004_Id&T066_Fonte_Id=$T066_Fonte_Id&callback=?', function(request) {
									$('#contentMenu').empty().html(request.data);
								});
								}</script></div>
EOT;
						}
					}

				} else {

					$resposta['data'] = <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert('Locação não cadastrada');
					$.getJSON('./movimentacao.php?opcao=colocar&T066_Id=$T066_Fonte_Id&D009_Id=$D009_Id&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
					}
					</script>
					</div>				
EOT;

				}
			}
			
		} else {
			
			$sql = "SELECT * FROM T066 
			LEFT JOIN D009 ON T066_D009_Id = D009_Id   
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id
			LEFT JOIN D004 ON T066_D004_Id = D004_Id  
			WHERE T066_C007_Id_Retirou = '{$g['usuarioAtual']}' AND T066_Retirou_Qtd > 0;";
			$sql = mysql_query($sql);
			if(mysql_numrows($sql) > 0){
				$resposta['data'] = <<<EOT
				<div class="titulo"><strong style="font-size: 140%;">Armazenamento de Produtos</strong></div>
					<button class="bigBtn" id="voltar">Voltar</button><br>
					
EOT;
				while($res = mysql_fetch_array($sql)) {
					$resposta['data'] .= <<<EOT
						<table cellspacing="0" cellpadding="0" border="1" style="width: 100%;" align="center">		
							<tr class="selecionarPedido" id="D009_Id-{$res['D009_Id']}" cod="{$res['T066_Id']}">
								<td>
									<table width="100%" border="1">	
										<tr>
											<td>Código</td>
											<td>{$res['D009_Id']}</td>
										</tr>
										<tr>
											<td>Produto</td>
											<td>{$res['D001_Descricao_Produto']}</td>
										</tr>
										<tr>
											<td>Locação</td>
											<td>{$res['D004_Local']}</td>
										</tr>
										<tr>
											<td>Quantidade</td>
											<td>{$res['T066_Retirou_Qtd']}</td>
										</tr>
									</table>
								</td>
							</tr>
EOT;
				}
				$resposta['data'] .= <<<EOT
					</table>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    			$('.selecionarPedido').unbind('click').bind('click', function(e) {
		    				modal('Verificando...');
		    				var id = $(this).attr('id');
		    				id = id.replace('D009_Id-', '');
		    				var cod = $(this).attr('cod');
		    				$.getJSON('./movimentacao.php?opcao=colocar&T066_Fonte_Id=' + encodeURIComponent(cod) + '&D009_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
		    					modalRemove();
							$('#contentMenu').empty().html(request.data);
							});
		    			});
		    			
		    			
		    			$('#voltar').unbind('click').bind('click', function(e) {
				    		modal('Carregando...');
				    		$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
				    			modalRemove();
								$('#contentMenu').empty().html(request.data);
								});
				    		});
					} </script></div>		
EOT;
			} else {
				$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$.getJSON('./menuMovimentacao.php?callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
				}</script></div>
EOT;
			}
		}
		break;

	// CADASTRAR LOCAÇÃO T066 -----------------------------------------------------------------------------------------------------------------------------------------------
	case 'cadastrarLocacao':
		$D009_Id = $_REQUEST['D009_Id'];
		$D004_Id = $_REQUEST['D004_Id'];
		$sql = "INSERT INTO T066 (T066_D009_Id,T066_D004_Id) values ('$D009_Id','$D004_Id')";
		mysql_query($sql);
		$resposta['data'] = <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		alert('Locação cadastrada para o produto');
		$.getJSON('./movimentacao.php?opcao=retirar&D009_Id=$D009_Id&callback=?', function(request) {
			$('#contentMenu').empty().html(request.data);
		});
		}
		</script>
		</div>				
EOT;
		break;
			
	// CONFIGURAR TIPO DA LOCAÇÃO: 1 - PERMITE APENAS 1 PRODUTO NA LOCAÇÃO  2 - PERMITE VÁRIOS PRODUTOS NA LOCAÇÃO -----------------------------------------------------------
	case 'alterarTipo':

		$tipo = $_REQUEST['tipo'];
		$D004_Id = $_REQUEST['D004_Id'];
		$D009_Id = $_REQUEST['D009_Id'];
		$T066_Id = $_REQUEST['T066_Id'];

		if(!isset($tipo)){

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
	    			$.getJSON('./movimentacao.php?opcao=alterarTipo&D004_Id=$D004_Id&D009_Id=$D009_Id&T066_Id=$T066_Id&tipo=1&callback=?', function(request) {
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
	    			$.getJSON('./movimentacao.php?opcao=alterarTipo&D004_Id=$D004_Id&D009_Id=$D009_Id&T066_Id=$T066_Id&tipo=2&callback=?', function(request) {
	    				modalRemove();
	    				// Proximo Produto da lista
						$('#contentMenu').empty().html(request.data);
					});
				}
				return false;
			});
				
			$('#voltar').unbind('click').bind('click', function() {
	     		$.getJSON('./movimentacao.php?opcao=colocarProduto&callback=?', function(request) {
					$('#contentMenu').empty().html(request.data);
				})
	    	});
			}</script></div>					
EOT;

		} else {

			$sql = "UPDATE D004 SET D004_Flag_Tipo = $tipo WHERE D004_Id = $D004_Id";
			mysql_query($sql);
			
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$.getJSON('./movimentacao.php?opcao=colocarProduto&D009_Id=$D009_Id&scan=$D004_Id&T066_Id=$T066_Id&callback=?', function(request) {
				$('#contentMenu').empty().html(request.data);
			});
			}</script></div>
EOT;

		}
		break;
	
    // SENDO ESTE PRODUTO NOVO NESTA LOCAÇÃO, PEDE DADOS DE CONFIGURACAO DO PRODUTO NA LOCACAO ------------------------------------------------------------------------------
	case 'regrasLocacao':

		$D004_Id = $_REQUEST['D004_Id'];
		$D009_Id = $_REQUEST['D009_Id'];
		$T066_Fonte_Id = $_REQUEST['T066_Fonte_Id'];
		$resposta['data'] = <<<EOT
		<div class="alerta">
			<strong style="font-size: 140%;">Novo produto na locação</strong>
		</div>
		<div style="width:100%;">
			<table cellspacing="0" cellpadding="0" style="width: 1%;margin:0 auto;text-align:center;">		
				<tr>
					<td>
						<strong style="font-size: 120%;">Mín: </strong>
					</td>
					<td>
						<input type="text" id="minimo" size="5">
					</td>
					<td>
						<strong style="font-size: 120%;">Max: </strong>
					</td>
					<td>
						<input type="text" id="maximo" size="5">
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<strong style="font-size: 120%;">Agrupamento: <input type="text" id="agrupamento" size="18"><br />
					</td>
				</tr>
			</table>
		</div>
		<button class="bigBtn" id="alterarRegras">Alterar</button>
		<button class="bigBtn" id="voltar">Voltar</button>
EOT;
		
		$resposta['data'] .= <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#alterarRegras').unbind('click').bind('click', function() {
				modal('Verificando...');
				$.getJSON('./movimentacao.php?opcao=colocarProduto&D009_Id=$D009_Id&scan=$D004_Id&T066_Fonte_Id=$T066_Fonte_Id&minimo='+$('#minimo').val()+'&maximo='+$('#maximo').val()+'&agrupamento='+$('#agrupamento').val()+'&callback=?', function(request) {
					modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			});
			$('#voltar').unbind('click').bind('click', function() {
				modal('Verificando...');
				$.getJSON('./movimentacao.php?opcao=colocar&D009_Id=$D009_Id&scan=$D004_Id&T066_Fonte_Id=$T066_Fonte_Id&minimo='+$('#minimo').val()+'&maximo='+$('#maximo').val()+'&agrupamento='+$('#agrupamento').val()+'&callback=?', function(request) {
					modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			});
		}</script></div>
EOT;
		
	break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

