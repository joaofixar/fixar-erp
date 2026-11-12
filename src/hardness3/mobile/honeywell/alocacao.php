<?php
namespace hardness;

include('conf.php');
$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'selecionarProduto':
		$resposta['data'] = <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				$('#codProduto').focus();
			}</script></div>
			
			<table align="center" cellspacing="0" width="98%">
				<tr>
					<td colspan="3" style="align: center;">
						<button class="bigBtn" id="voltar">Voltar</button>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<strong style="font-size: 120%;">Produto: <input type="text" id="codProduto" size="28">
					</td>
				</tr>
EOT;
		if(isset($_REQUEST['codProduto'])){
			$sql = "SELECT * FROM D009
			LEFT JOIN D049 ON D009_D049_Id = D049_Id
			LEFT JOIN D001 ON D009_D001_Id = D001_Id
			LEFT JOIN D082 ON D049_D082_Id = D082_Id
			WHERE D001_Codigo_Produto LIKE '%{$_REQUEST['codProduto']}%' AND D009_C004_Id = '{$g['empresaAtual']}' LIMIT 50;";
			$res = mysql_query($sql);
			if(mysql_num_rows($res) > 0){
				while($row = mysql_fetch_assoc($res)){
					$resposta['data'] .= <<<EOT
					<tr style="height: 4px;"></tr>
					<tr class="selecionarMarca" id="D009_Id-{$row['D009_Id']}">
						<td width="15%" style="border: 1px solid #8F859C; padding: 10px 3px; border-right: 0;">{$row['D001_Codigo_Produto']}</td>
						<td width="45%" style="font-size: 80%; border: 1px solid #8F859C; padding: 10px 3px; border-left: 0; border-right: 0;">{$row['D001_Descricao_Produto']}</td>
						<td width="40%" style="font-size: 80%; border: 1px solid #8F859C; padding: 10px 3px; border-left: 0;">{$row['D082_Marca']}</td>
					</tr>
EOT;
				}
			} else {
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
					modal('PRODUTO NÃO ENCONTRADO');
					setTimeout("reload()",2000);
					function reload(){
						modalRemove();
						$.getJSON('./alocacao.php?opcao=selecionarProduto&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}
				}</script></div>
EOT;
			}
		}
		$resposta['data'] .= <<<EOT
			</table>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#codProduto').unbind('keypress');
			$('#codProduto').bind('keypress', function(e) {	
			if (e.keyCode == 13) {
				if($('#codProduto').val() != ''){
			    	modal('Verificando...');
	    			$.getJSON('./alocacao.php?opcao=selecionarProduto&codProduto=' + encodeURIComponent($('#codProduto').val()) + '&callback=?', function(request) {
	    				modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				return false;
				}
			}
					
			});
			
			$('.selecionarMarca').unbind('click').bind('click', function(e) {
    			modal('Verificando...');
    			var id = $(this).attr('id');
    			id = id.replace('D009_Id-', '');
    			$.getJSON('./alocacao.php?opcao=produtoInfo&D009_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
    				modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
    		});
    	
			$('#voltar').unbind('click').bind('click', function() {
     			$.getJSON('./alocacao.php?opcao=alocarProduto&callback=?', function(request) {
					$('#contentMenu').empty().html(request.data);
				})
    		});
			}</script></div>					
EOT;
		
		break;
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
		if(isset($_REQUEST['novoLocal'])){
				$sql = "INSERT INTO T066 (T066_D004_Id, T066_D009_Id) VALUES ($D004_Id, $D009_Id);";
				mysql_query($sql);	
				$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$.getJSON('./alocacao.php?opcao=produto&scan=$D009_Id&D004_Id=$D004_Id&callback=?', function(request) {
						$('#scan').val('');
						$('#contentMenu').empty().html(request.data);
					});
				}</script></div>
EOT;
		} else {
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$.getJSON('./alocacao.php?opcao=alocarProduto&callback=?', function(request) {
						$('#scan').val('');
						$('#contentMenu').empty().html(request.data);
					});
				}</script></div>
EOT;
		}
		/*if($tipo == 1){
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$.getJSON('./alocacao.php?opcao=alocarProduto&callback=?', function(request) {
					$('#contentMenu').empty().html(request.data);
				});
				}
				</script>
				</div>
EOT;
		} else {
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$.getJSON('./alocacao.php?opcao=produto&tipo=$tipo&D009_Id=$D009_Id&D004_Id=$D004_Id&callback=?', function(request) {
					$('#contentMenu').empty().html(request.data);
				});
				}
				</script>
				</div>
EOT;
				}*/
		break;
		
			
	case 'produto':
		$D004_Id = $_REQUEST['D004_Id'];
		$D009_Id = isset($_REQUEST['scan']) ? $_REQUEST['scan'] : false;
		if (isset($_REQUEST['scan'])) {
			$sql = "SELECT * FROM T066
							LEFT JOIN D009 ON T066_D009_Id = D009_Id   
							LEFT JOIN D001 ON D009_D001_Id = D001_Id
							LEFT JOIN D004 ON T066_D004_Id = D004_Id 
							WHERE D004_Id = $D004_Id AND D009_Id = $D009_Id;";
				$res = mysql_query($sql);
			if(mysql_num_rows($res) > 0){
				$result = mysql_fetch_array($res);
				$estoque = gCorrigeNumero($result['T066_Quantidade_Estoque']);
				$minimo = number_format($result['T066_Minimo'], 2);
				$maximo = number_format($result['T066_Maximo'], 2);
				$agrupamento = number_format($result['T066_Agrupamento'], 0);
				$resposta['data'] = <<<EOT
				<table style="width: 100%; align="center" cellspacing="0">
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Produto: <br>{$result['D001_Descricao_Produto']}<br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Estoque: {$estoque}<br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Balanço: <input type="text" id="balanco" size="23"><br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Mínimo: </strong><input type="text" id="minimo" size="5" value="{$minimo}">
				<strong style="font-size: 120%;"> Máximo: <input type="text" id="maximo" size="6" value="{$maximo}"><br /></td></tr>
				<tr><td style="text-align: center;"><strong style="font-size: 120%;">Agrupamento: <input type="text" id="agrupamento" size="17" value="{$agrupamento}"><br /></td></tr>
				<tr><td><button class="bigBtn" id="alterarEstoque">Alterar</button></td></tr>
				<tr><td><button class="btn" id="voltar">Voltar</button></td></tr>
				
				</table>
EOT;
				$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');
    						$('#alterarEstoque').unbind('click').bind('click', function() {
    				 				modal('Verificando...');
    								$.getJSON('./alocacao.php?opcao=alterar&minimo='+$('#minimo').val()+'&maximo='+$('#maximo').val()+'&agrupamento='+$('#agrupamento').val()+'&tipo={$result['D004_Flag_Tipo']}&D004_Id=$D004_Id&D009_Id=$D009_Id&T066_Id={$result['T066_Id']}&balanco=' + encodeURIComponent($('#balanco').val()) + '&callback=?', function(request) {
    									modalRemove();
    									$('#scan').val('');
  
										$('#contentMenu').empty().html(request.data);
									});
    						});
    						
    						$('#voltar').unbind('click').bind('click', function() {
    				 				window.location="./menu.php";
    						});
    						
    						$('#balanco').focus();
    					}</script></div>   						
EOT;
			} else {
				$sql = "SELECT * FROM T066
					LEFT JOIN D009 ON T066_D009_Id = D009_Id   
					LEFT JOIN D001 ON D009_D001_Id = D001_Id
					LEFT JOIN D004 ON T066_D004_Id = D004_Id 
					WHERE D009_Id = {$_REQUEST['scan']}";
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
					$.getJSON('./alocacao.php?opcao=produto&D004_Id=$D004_Id&callback=?', function(request) {
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
					$.getJSON('./alocacao.php?opcao=produto&scan={$res['T066_D009_Id']}&D004_Id=$D004_Id&callback=?', function(request) {
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
				<button class="btn" id="voltar">Voltar</button>
				
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#scan').unbind('keypress');
					$('#scan').bind('keypress', function(e) {
						if (e.keyCode == 13) {
					    	modal('Verificando...');
					    	$.getJSON('./alocacao.php?opcao=produto&scan=' + encodeURIComponent($('#scan').val()) + '&D004_Id=$D004_Id&callback=?', function(request) {
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
			
	case 'alocarProduto':
		if (isset($_REQUEST['scan'])) {
			$sql = "SELECT * FROM D004 WHERE D004_Id = '{$_REQUEST['scan']}';";
			$res1 = mysql_query($sql);
			if(mysql_num_rows($res1) > 0)	{
				$result2 = mysql_fetch_assoc($res1);
				$D004_Id = $result2['D004_Id'];
				$D004_Local = $result2['D004_Local'];
				//Locação 1-1
				if($result2['D004_Flag_Tipo'] == 1){
					$sql = "SELECT * FROM T066
							LEFT JOIN D009 ON T066_D009_Id = D009_Id 
							LEFT JOIN D004 ON T066_D004_Id = D004_Id 
							LEFT JOIN D001 ON D009_D001_Id = D001_Id 
							WHERE T066_D004_Id = '{$_REQUEST['scan']}';";
					$res = mysql_query($sql);
					$result = mysql_fetch_array($res);
					$D004_Id = $_REQUEST['scan'];
					//Tem produtos na locação
					if(mysql_num_rows($res) > 0)	{
					 // BALANÇO
						$resposta['data'] = <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
								$.getJSON('./alocacao.php?opcao=produto&D004_Id=$D004_Id&callback=?', function(request) {
			   	    				$('#scan').val('');
									$('#contentMenu').empty().html(request.data);
								});
							}</script></div>
EOT;
					//Não tem produtos na locação*/
					} else {
						$resposta['data'] = <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
							$('#codProduto').focus();
							$.getJSON('./alocacao.php?opcao=selecionarProduto&D004_Id=$D004_Id&D004_Local=$D004_Local&callback=?', function(request) {
				    				modalRemove();
				    				// Proximo Produto da lista
									$('#contentMenu').empty().html(request.data);
								});
							}</script></div>	
EOT;
							/*
							<div class="titulo"><strong style="font-size: 140%;"><br>{$result2['D004_Local']}<br> </br></strong></div>
							<table border="0" cellspacing="0">
							<tr><td><strong style="font-size: 120%;">Cod: <input type="text" id="codProduto" size="15">
							<button class="smallBtn" id="procurar">Procurar</button></td></tr>
							<tr><td><button class="btn" id="voltar">Voltar</button></tr></td>
EOT;
						if(isset($_REQUEST['codProduto'])){
							$sql = "SELECT * FROM D009
							LEFT JOIN D001 ON D009_D001_Id = D001_Id
							LEFT JOIN D049 ON D009_D049_Id = D049_Id
							LEFT JOIN D082 ON D049_D082_Id = D082_Id
							WHERE D001_Codigo_Produto = '{$_REQUEST['codProduto']}';";
							$res = mysql_query($sql);
							$result = mysql_fetch_array($res);
							if(mysql_num_rows($res) > 0){
								$resposta['data'] .= <<<EOT
								<tr><td><strong style="font-size: 120%;">{$result['D001_Descricao_Produto']}</td></tr>
EOT;
								while($row = mysql_fetch_array($res)){
									$resposta['data'] .= <<<EOT
									<tr class="selecionarMarca" id="D009_Id-{$result['D009_Id']}"><td>{$row['D082_Marca']}</td></tr>
EOT;
								}
							} else {
								$resposta['data'] .= <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
								modal('PRODUTO NÃO CADASTRADO');
								setTimeout("reload()",2000);
								function reload(){
									modalRemove();
									$.getJSON('./alocacao.php?opcao=alocarProduto&scan=$D004_Id&callback=?', function(request) {
										$('#contentMenu').empty().html(request.data);
									});
								}
								}</script></div>
EOT;
							}
						}
						
						$resposta['data'] .= <<<EOT
							</table>
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('.selecionarMarca').unbind('click').bind('click', function(e) {
			    				modal('Verificando...');
			    				var id = $(this).attr('id');
			    				id = id.replace('D009_Id-', '');
			    				var cod = $(this).attr('cod');
				    				$.getJSON('./alocacao.php?opcao=alterar&D004_Id=$D004_Id&novoLocal=S&D009_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
				    					modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
		    				});
		    			
							$('#procurar').unbind('click').bind('click', function() {
								modal('Verificando...');
		    					$.getJSON('./alocacao.php?opcao=alocarProduto&scan=$D004_Id&codProduto=' + encodeURIComponent($('#codProduto').val()) + '&callback=?', function(request) {
		    						modalRemove();
		    						// Proximo Produto da lista
									$('#contentMenu').empty().html(request.data);
								});
							});
								
							$('#voltar').unbind('click').bind('click', function() {
    				 			$.getJSON('./alocacao.php?opcao=alocarProduto&callback=?', function(request) {
									$('#contentMenu').empty().html(request.data);
								})
    						});
							}</script></div>					
EOT;
*/
					} 
	////////////////// LOCAÇÃO COM MAIS DE UM PRODUTO
				} else {					
					$sql = "SELECT * FROM T066
							LEFT JOIN D009 ON T066_D009_Id = D009_Id 
							LEFT JOIN D004 ON T066_D004_Id = D004_Id 
							LEFT JOIN D001 ON D009_D001_Id = D001_Id 
							WHERE T066_D004_Id = '{$_REQUEST['scan']}';";
					//echo $sql2;
					$res= mysql_query($sql);
					$result = mysql_fetch_array($res);
					//Tem produtos na locação
					if(mysql_num_rows($res) > 0){
					 // BALANÇO
						$resposta['data'] = <<<EOT
					 	<div class="alerta"><strong style="font-size: 140%;"><br>INFORME A OPERAÇÃO<br> </br></strong></div>
							<button class="bigBtn" id="balanco">Balanço de Produto</button>
							<button class="bigBtn" id="cadastro">Cadastrar Produto</button>
							<button class="btn" id="voltar">Voltar</button>
EOT;
						$resposta['data'] .= <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#balanco').unbind('click').bind('click', function() {
		    				 	modal('Verificando...');
		    					$.getJSON('./alocacao.php?opcao=produto&D004_Id=$D004_Id&tipo=1&callback=?', function(request) {
		    						modalRemove();
		    						// Proximo Produto da lista
									$('#contentMenu').empty().html(request.data);
								});
							});
							
							$('#cadastro').unbind('click').bind('click', function() {
		    				 	modal('Verificando...');
		    					$.getJSON('./alocacao.php?opcao=selecionarProduto&D004_Id=$D004_Id&D004_Local=$D004_Local&callback=?', function(request) {
				    				modalRemove();
				    				// Proximo Produto da lista
									$('#contentMenu').empty().html(request.data);
								});
							});
								
							$('#voltar').unbind('click').bind('click', function() {
    				 			$.getJSON('./alocacao.php?opcao=alocarProduto&callback=?', function(request) {
									$('#contentMenu').empty().html(request.data);
								})
    						});
							}</script></div>					
EOT;
					//Não tem produtos na locação*/
					} else {
						$resposta['data'] = <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
							$('#codProduto').focus();
							$.getJSON('./alocacao.php?opcao=selecionarProduto&D004_Id=$D004_Id&D004_Local=$D004_Local&callback=?', function(request) {
				    				modalRemove();
				    				// Proximo Produto da lista
									$('#contentMenu').empty().html(request.data);
								});
							}</script></div>	
EOT;
/*
							<div class="titulo"><strong style="font-size: 140%;"><br>{$result['D004_Local']}<br> </br></strong></div>
							<table border="0" cellspacing="0">
							<tr><td><strong style="font-size: 120%;">Cod: <input type="text" id="codProduto" size="15">
							<button class="smallBtn" id="procurar">Procurar</button></td></tr>
							<tr><td><button class="btn" id="voltar">Voltar</button></tr></td>
EOT;
						if(isset($_REQUEST['codProduto'])){
							$sql = "SELECT * FROM D009
							LEFT JOIN D001 ON D009_D001_Id = D001_Id
							LEFT JOIN D049 ON D009_D049_Id = D049_Id
							LEFT JOIN D082 ON D049_D082_Id = D082_Id
							WHERE D001_Codigo_Produto = '{$_REQUEST['codProduto']}';";
							$res = mysql_query($sql);
							$result = mysql_fetch_array($res);
							if(mysql_num_rows($res) > 0){
								$resposta['data'] .= <<<EOT
								<tr><td><strong style="font-size: 120%;">{$result['D001_Descricao_Produto']}</td></tr>
EOT;
								while($row = mysql_fetch_array($res)){
									$resposta['data'] .= <<<EOT
									<tr class="selecionarMarca" id="D009_Id-{$result['D009_Id']}"><td>{$row['D082_Marca']}</td></tr>
EOT;
								}
							} else {
								$resposta['data'] .= <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
								modal('PRODUTO NÃO CADASTRADO');
								setTimeout("reload()",2000);
								function reload(){
									modalRemove();
									$.getJSON('./alocacao.php?opcao=alocarProduto&scan=$D004_Id&callback=?', function(request) {
										$('#contentMenu').empty().html(request.data);
									});
								}
								}</script></div>
EOT;
							}
						}
						$resposta['data'] .= <<<EOT
							</table>
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('.selecionarMarca').unbind('click').bind('click', function(e) {
			    				modal('Verificando...');
			    				var id = $(this).attr('id');
			    				id = id.replace('D009_Id-', '');
			    				var cod = $(this).attr('cod');
				    				$.getJSON('./alocacao.php?opcao=alterar&D004_Id=$D004_Id&novoLocal=S&D009_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
				    					modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
		    				});
		    			
							$('#procurar').unbind('click').bind('click', function() {
								modal('Verificando...');
		    					$.getJSON('./alocacao.php?opcao=alocarProduto&scan=$D004_Id&codProduto=' + encodeURIComponent($('#codProduto').val()) + '&callback=?', function(request) {
		    						modalRemove();
		    						// Proximo Produto da lista
									$('#contentMenu').empty().html(request.data);
								});
							});
								
							$('#voltar').unbind('click').bind('click', function() {
    				 			$.getJSON('./alocacao.php?opcao=alocarProduto&callback=?', function(request) {
									$('#contentMenu').empty().html(request.data);
								})
    						});
							}</script></div>					
EOT;
*/
					}
				}
			} else {
				$resposta['data'] .= <<<EOT
				<div class="alerta" style="margin-top: 45%"><strong style="font-size: 140%;"><br>LOCAÇÃO NÃO CADASTRADA<br> </br></strong></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setTimeout("reload()",2000);
				function reload(){
					$.getJSON('./alocacao.php?opcao=alocarProduto&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
				}
				}</script></div>
EOT;
			}
		} else {
			$resposta['data'] .= <<<EOT
			<div class="alerta" style="margin-top: 30%">
				<strong style="font-size: 140%;"><br>LEIA UMA LOCAÇÃO<br> </br></strong>
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
				    	$.getJSON('./alocacao.php?opcao=alocarProduto&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
		
	case 'produtoInfo':
		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		if ($D009_Id) {
			$sql = "SELECT * FROM D009
							LEFT JOIN D001 ON D009_D001_Id = D001_Id
							LEFT JOIN D049 ON D049_Id=D009_D049_Id
							LEFT JOIN D082 ON D082_Id=D049_D082_Id
							WHERE D009_Id = '$D009_Id';";				
			$res = mysql_query($sql);
			if(mysql_num_rows($res) > 0){
				$result = mysql_fetch_array($res);
				$result['D009_Quantidade_Estoque'] = gCorrigeNumero($result['D009_Quantidade_Estoque']);
				$result['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($result['D009_Quantidade_Estoque_Liquido']);
				$resposta['data'] .= <<<EOT
					<table style="width: 100%; align="center" cellspacing="0">
						<tr><td style="text-align: center;">Produto:<strong style="font-size: 120%;"><br>{$result['D001_Descricao_Produto']}<br /></td></tr>
						<tr><td style="text-align: center;">Marca:<strong style="font-size: 120%;"><br />{$result['D082_Marca']}<br /></td></tr>
						<tr><td style="text-align: center;">Estoque<strong style="font-size: 120%;"><br />{$result['D009_Quantidade_Estoque']}<br /></td></tr>
						<tr><td style="text-align: center;">Estoque Liq.<strong style="font-size: 120%;"><br />{$result['D009_Quantidade_Estoque_Liquido']}<br /></td></tr>
					</table>
EOT;
				$resposta['data'] .= <<<EOT
					<table style="font-size: 10px; width: 80%; border: 1px solid black; margin: 3px; text-align: center;">
						<tr>
							<td style="font-weight: bold; border-right: 1px solid black; border-bottom: 1px solid black;">Locação</td>
							<td style="font-weight: bold; border-right: 1px solid black; border-bottom: 1px solid black;">Qtd</td>
							<td style="font-weight: bold; border-right: 1px solid black; border-bottom: 1px solid black;">Tipo</td>
							<td style="font-weight: bold; border-right: 1px solid black; border-bottom: 1px solid black;">Min</td>
							<td style="font-weight: bold; border-right: 1px solid black; border-bottom: 1px solid black;">Max</td>
							<td style="font-weight: bold; border-bottom: 1px solid black;">Agrup.</td>
						</tr>
EOT;
				$sqlT066 = "SELECT * FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '{$D009_Id}';";
				$resT066 = mysql_query($sqlT066);
				if(mysql_num_rows($resT066) > 0){
					while ($rowT066 = mysql_fetch_assoc($resT066)) {
						$rowT066['D004_Local'] = empty($rowT066['D004_Local']) ? 'DESCONHECIDO' : $rowT066['D004_Local'];
						$resposta['data'] .= <<<EOT
							<tr>
								<td style="border-right: 1px solid black;">{$rowT066['D004_Local']}</td>
EOT;
						if (empty($rowT066['D004_Id']) || empty($rowT066['D004_Flag_Tipo'])) {
							$resposta['data'] .= <<<EOT
									<td colspan="5">ANTIGA</td>
								</tr>
EOT;
						} else {
							$rowT066['T066_Quantidade_Estoque'] = gCorrigeNumero($rowT066['T066_Quantidade_Estoque']);
							$rowT066['D004_Flag_Tipo'] = empty($rowT066['D004_Flag_Tipo']) ? '-' : $rowT066['D004_Flag_Tipo'];
							$rowT066['T066_Minimo'] = gCorrigeNumero($rowT066['T066_Minimo']);
							$rowT066['T066_Maximo'] = gCorrigeNumero($rowT066['T066_Maximo']);
							$rowT066['T066_Agrupamento'] = gCorrigeNumero($rowT066['T066_Agrupamento']);
							$resposta['data'] .= <<<EOT
									<td style="border-right: 1px solid black;">{$rowT066['T066_Quantidade_Estoque']}</td>
									<td style="border-right: 1px solid black;">{$rowT066['D004_Flag_Tipo']}</td>
									<td style="border-right: 1px solid black;">{$rowT066['T066_Minimo']}</td>
									<td style="border-right: 1px solid black;">{$rowT066['T066_Maximo']}</td>
									<td>{$rowT066['T066_Agrupamento']}</td>
								</tr>
EOT;
						}
					}
				} else {
					$resposta['data'] .= <<<EOT
						<tr><td colspan="6">Sem nenhuma locação.</td></tr>
EOT;
				}
				$resposta['data'] .= '</table>';
				
				$resposta['data'] .= <<<EOT
					<button class="btn" style="padding: 5px;">Locação</button></td>
					<button class="btn" style="padding: 5px;">Balanço</button></td>
					<button class="btn" id="voltar" style="padding: 5px;">Voltar</button>
				
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#voltar').unbind('click').bind('click', function() {
     						$.getJSON('./alocacao.php?opcao=selecionarProduto&callback=?', function(request) {
								$('#contentMenu').empty().html(request.data);
							});
    					});
    					$('#codProduto').focus();
					}</script></div>
EOT;
			}
		}
		break;	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";