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
				$_SESSION['Usuario_Atual']=$usuarioAtual['C007_Id'];
				$resUsuarioAtual = mysql_query($sqlUsuarioAtual);
				$rowUsuarioAtual = @mysql_fetch_array($resUsuarioAtual);
				
				$resposta['data'] = <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						modal('Carregando...');
			    		$.getJSON('./separacaoPedidoLista.php?opcao=pedidos&callback=?', function(request) {
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
			    		$.getJSON('./separacaoPedidoLista.php?opcao=autentica&callback=?', function(request) {
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
					    	$.getJSON('./separacaoPedidoLista.php?opcao=autentica&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
			    	$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
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
				<div class="produto" style="margin: 3px 0 3px 9px; font-size: 120%; padding: 3px 0 3px 0;">
					<strong>{$res['D001_Codigo_Produto']}</strong><br />
					<strong>{$res['D001_Descricao_Produto']}</strong><br />
					<strong>{$res['D082_Marca']}</strong><br />									
				</div>
				<div class="alerta" style="margin-left: 9px; margin-bottom: 1px; width: 92.5%;"><strong style="font-size: 120%;"><br>LEIA O CODIGO DE BARRAS<br> </br></strong></div>
				<button class="voltarLarge" style="margin-left: 9px;" id="voltar">Voltar</button>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#voltar').unbind('click').bind('click', function(e) {
						modal('Verificando...');
				    	$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
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
			    				$.getJSON('./separacaoPedidoLista.php?opcao=codigo&D001_Id={$D001_Id}&T005_Id={$T005_Id}&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
		$T185_Id = $_REQUEST['T185_Id'];
		$T006_Id = $_REQUEST['T006_Id'];
		
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
		
		$T006 = <<<EOT
			SELECT * FROM T006 
			LEFT JOIN D009 on D009_Id=T006_D009_Id 
			LEFT JOIN T185 ON T185_T006_Id=T006_Id
			LEFT JOIN T066 ON T066_Id=T185_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN D001 ON D001_Id=D009_Id   
			WHERE T006_T005_Id = '{$T005_Id}' AND (T185_Qtd_Separado != 0 OR T185_Tipo_Divergencia > 0) 
			AND T185_Qtd_Para_Separar IS NOT NULL ORDER BY T185_Id DESC LIMIT 1
EOT;
		$resT006 = mysql_query($T006);
		$result = mysql_fetch_array($resT006);
		if(isset($_REQUEST['voltar'])){
			$sql = "UPDATE T185 SET T185_Qtd_Separado = '0', T185_Tipo_Divergencia = 0 WHERE T185_Id = '{$result['T185_Id']}';";
			mysql_query($sql);
			$sql = "UPDATE T006 SET T006_Flag_Divergencia = 'N' WHERE T006_Id = '{$result['T006_Id']}';";
			mysql_query($sql);
		}
		$res1 = mysql_query($sql2);
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
			AND T185_Qtd_Para_Separar IS NOT NULL ORDER BY T185_Qtd_Separado ASC
EOT;
		$resProdutos = mysql_query($sqlProdutos);
		
/*		$sqlEntrada = <<<EOT
			SELECT * FROM T138
			LEFT JOIN D009 on D009_Id=T006_D009_Id 
			LEFT JOIN T185 ON T185_T006_Id=T006_Id
			LEFT JOIN T066 ON T066_Id=T185_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			WHERE 
			T006_T005_Id = '{$T005_Id}'
			AND T185_Qtd_Para_Separar IS NOT NULL ORDER BY T185_Id ASC
EOT;
		$resEntrada = mysql_query($sqlEntrada);*/
		if(mysql_numrows($resProdutos) == 0){
			//Após todos os produtos serem separados ou notificados com divergencia a separacao do pedido é finalizada
			//$finalizarSeparacao = mysql_query("UPDATE T005 SET T005_Flag_Status = '10', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = '{$T005_Id}'");
			$resposta['data'] .= <<<EOT
				<div style="margin-top: 20%" class="titulo">
					<strong style="font-size: 140%;"><br>Sem produtos para separar</br> <br /></strong>
				</div>		
EOT;
		}
		$resposta['data'] .= <<<EOT
				<button class="voltar" style="width: 48.2%;" id="retornarMenu">Voltar</button>				
				<button class="voltar" style="width: 48.2%;" id="incluirProduto">Incluir<br />Produto</button>
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
					color: #000;					
				}
				</style>
				<table class="tabela" style="width: 98%; margin-left: 2px;" align="center" cellspacing="0" cellpadding="0">			
EOT;
		while($row = mysql_fetch_array($resProdutos)) {
			$separado = number_format($row['T185_Qtd_Separado'], 2);
			$quantidade = gCorrigeNumero($row['T006_Quantidade']);
			if(empty($row['D001_Codigo_Barras'])){
				$resposta['data'] .= <<<EOT
					<tr style="margin-top: -1px;" D001_Id="{$row['D001_Id']}">
						<td class="lista" style="color: blue; height: 35px;">
							<strong>{$row['T006_Descricao_Produto']}</strong><br> QTDE: {$quantidade}
							<input id="D001_Id" type="hidden" value="{$row['D001_Id']}">
						</td>
					</tr>
EOT;
			} else { 
				if($row['T185_Qtd_Separado'] == $row['T006_Quantidade']){
					$resposta['data'] .= <<<EOT
						<tr style="margin-top: -1px;">
							<td class="lista" style="color: gray;">
								<strong>{$row['T006_Descricao_Produto']}</strong><br> COD: {$row['D001_Codigo_Barras']}<br>QTDE: {$row['T006_Quantidade']}  - SEPARADO: {$separado}
							</td>
						</tr>
EOT;
				} else {
					$resposta['data'] .= <<<EOT
						<tr style="margin-top: -1px;">
							<td class="lista">
								<strong>{$row['T006_Descricao_Produto']}</strong><br> COD: {$row['D001_Codigo_Barras']}<br>QTDE: {$row['T006_Quantidade']}  - SEPARADO: {$separado}
							</td>
						</tr>
EOT;
				}
			}
		}
		$resposta['data'] .= <<<EOT
			</table>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#retornarMenu').unbind('click').bind('click', function() {
    				 window.location="./menu.php";
    			});

				$('#incluirProduto').unbind('click').bind('click', function(e) {
					modal('Verificando...');					
    				$.getJSON('./separacaoPedidoLista.php?opcao=incluirProduto&T005_Id={$T005_Id}&callback=?', function(request) {
    					modalRemove();
    					$('#contentMenu').empty().html(request.data);
					});
    			});

    			$('tr').unbind('click').unbind('click').bind('click', function(e) {
    				var id = $(this).closest('tr').attr('D001_Id');
    				if(id){
						modal('Verificando...');					
	    				$.getJSON('./separacaoPedidoLista.php?opcao=codigo&D001_Id=' + encodeURIComponent(id) + '&T005_Id={$T005_Id}&callback=?', function(request) {
	    					modalRemove();
	    					$('#contentMenu').empty().html(request.data);
						});
					}
    			});
    			
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
	    				modal('Verificando...');
	    				$.getJSON('./separacaoPedidoLista.php?opcao=separar&T005_Id={$T005_Id}&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
				<div style="margin-top: 20px;"><strong style="font-size: 120%;">LEIA O CÓDIGO DO PEDIDO<br> <br /></strong></div>
			</div>
			<button style="margin-left: 6px; width: 94.5%;" class="voltarLarge" id="voltar">Voltar</button>
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
				    	modal('Verificando...');
				    	$.getJSON('./separacaoPedidoLista.php?opcao=pedidos&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
						$('#barraTop').val();
						$('#barraTopPedido').html('Pedido: {$T005_Id}');						
		    			modal('Pedido {$T005_Id}');
		    			$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
		    				modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
				    } </script></div>
EOT;
			} else {
				$resposta['data'] .= <<<EOT
					<div style="margin-top: 45%" class="tituloBlue">
					<strong style="font-size: 140%;"><br>Pedido não disponível<br>para separar<br> </br></strong></div>			
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
	
	case 'separar':
		$scan = $_REQUEST['scan'];
		$codigo = isset($_REQUEST['codigo']) ? $_REQUEST['codigo'] : false;
		$T005_Id = $_REQUEST['T005_Id'];
		$T185_Id = isset($_REQUEST['T185_Id']) ? $_REQUEST['T185_Id'] : false;
		if(isset($_REQUEST['quantidade'])){
			$T006_Id = $_REQUEST['T006_Id'];			
			$T185 = "SELECT T185_Qtd_Para_Separar, T185_Qtd_Separado, T185_Qtd_Devolvido FROM T185 WHERE T185_Id = '{$T185_Id}'";
			$T185 = mysql_query($T185);
			$resT185 = mysql_fetch_array($T185);
			$T185_Quantidade = $resT185['T185_Qtd_Separado'];
			if($_REQUEST['quantidade'] == 0){
				$quantidade = $resT185['T185_Qtd_Separado'] - 1;		
				mysql_query("UPDATE T185 SET T185_Qtd_Separado = '{$quantidade}' WHERE T185_Id = '{$T185_Id}';");
				mysql_query("INSERT INTO T205 (
								T205_T006_Id, T205_C007_Id, T205_Quantidade, T205_Data, T205_Operacao
								) VALUES (
								'{$T006_Id}', '{$g['usuarioAtual']}', '+1', CURDATE(), 'E'
							)");
				//Atualiza o controle do produto T207
				mysql_query("UPDATE T207 SET T207_Flag_Status = '4', T207_C007_Id = '{$g['usuarioAtual']}' WHERE T207_Flag_Status = '2' AND T207_T006_Id = '{$T006_Id}' LIMIT 1");
			} else if($_REQUEST['quantidade'] == 1){
				$quantidade = $resT185['T185_Qtd_Separado'] + 1;
				if($resT185['T185_Qtd_Devolvido'] > 0){
					$calcular = $resT185['T185_Qtd_Devolvido'] - 1;
					mysql_query("UPDATE T185 SET T185_Qtd_Separado = '{$quantidade}', T185_Qtd_Devolvido = '{$calcular}' WHERE T185_Id = '{$T185_Id}';");
				} else {
					mysql_query("UPDATE T185 SET T185_Qtd_Separado = '{$quantidade}' WHERE T185_Id = '{$T185_Id}';");
				}
				mysql_query("INSERT INTO T205 (
								T205_T006_Id, T205_C007_Id, T205_Quantidade, T205_Data, T205_Operacao
								) VALUES (
								'{$T006_Id}', '{$g['usuarioAtual']}', '-1', CURDATE(), 'S'
							)");
				//Atualiza o controle do produto T207
				mysql_query("UPDATE T207 SET T207_Flag_Status = '2', T207_C007_Id = '{$g['usuarioAtual']}' WHERE (T207_Flag_Status = '1' OR T207_Flag_Status = '4') AND T207_T006_Id = '{$T006_Id}' LIMIT 1");
			}
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
					if(!isset($_REQUEST['quantidade']) && $row['T006_Quantidade_Separacao'] < $row['T006_Quantidade']){
						$T185 = "SELECT T185_Qtd_Separado, T185_Qtd_Devolvido FROM T185 WHERE T185_Id = '{$row['T185_Id']}'";
						$T185 = mysql_query($T185);
						$resT185 = mysql_fetch_array($T185);
						$quantidade = $resT185['T185_Qtd_Separado'] + 1;
						if($resT185['T185_Qtd_Devolvido'] > 0){
							$calcular = $resT185['T185_Qtd_Devolvido'] - 1;
							mysql_query("UPDATE T185 SET T185_Qtd_Separado = '{$quantidade}', T185_Qtd_Devolvido = '{$calcular}' WHERE T185_Id = '{$row['T185_Id']}';");
						} else {
							mysql_query("UPDATE T185 SET T185_Qtd_Separado = '{$quantidade}' WHERE T185_Id = '{$row['T185_Id']}';");
						}
						mysql_query("INSERT INTO T205 (
										T205_T006_Id, T205_C007_Id, T205_Quantidade, T205_Data, T205_Operacao
										) VALUES (
										'{$row['T006_Id']}', '{$g['usuarioAtual']}', '-1', CURDATE(), 'S'
									)");
						//Atualiza o controle do produto T207
						mysql_query("UPDATE T207 SET T207_Flag_Status = '2', T207_C007_Id = '{$g['usuarioAtual']}' WHERE (T207_Flag_Status = '1' OR T207_Flag_Status = '4') AND T207_T006_Id = '{$row['T006_Id']}' LIMIT 1");
						$separou = $quantidade;
					}		
					//VERIFICAÇÃO DAS QUANTIDADES ATUALIZADO
					$T185 = "SELECT T185_Qtd_Para_Separar, T185_Qtd_Separado FROM T185 WHERE T185_Id = '{$row['T185_Id']}'";
					$T185 = mysql_query($T185);
					$resT185 = mysql_fetch_array($T185);
					//produto lido esta na lista
					$encontrado = true;
					$resposta['data'] = <<<EOT
						<div id="tooltip" style="width: 89.35%; margin-left: 9px; margin-bottom: 3px;" class="alerta"><strong style="font-size: 100%;">LEIA O PRODUTO PARA SEPARAR</strong><br /></div>
						<div class="produto" style="width: 89%; font-size: 120%; padding-top: 3px; margin-left: 9px;">
							<!--<strong style="font-size: 115%;">{$row['D001_Codigo_Produto']}</strong><br />-->
							<strong>{$row['D001_Descricao_Produto']}</strong><br />
							<strong>{$row['D082_Marca']}</strong><br />
							<strong>ESTOQUE: {$row['D009_Quantidade_Estoque']}</strong>
							<table align="center">
							<tr>
							<td>
							<strong style="font-size: 125%;">QTDE<br />{$row['T006_Quantidade']}</strong><br />
							</td>
							<td style="width: 20px;"></td>
							<td>
							<strong style="font-size: 125%;">SEPARADO<br />{$separou}</strong><br />
							</td>
							</tr>
							</table>
						</div>
						<div style="margin-left: 6px;">	
EOT;
				if($separou > 0){
					$resposta['data'] .= <<<EOT
							<button class="redLarge" id="diminuir">Devolver Produto</button>
EOT;
				}				
					$resposta['data'] .= <<<EOT
							<button class="voltar" id="voltar">Voltar</button>
							<button class="voltar" style="margin-left: 4px"id="historico">Histórico</button>
						</div>
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var separar = '{$resT185['T185_Qtd_Para_Separar']}';
							var separado = '{$resT185['T185_Qtd_Separado']}';
							var codigoProduto = '{$row['D001_Codigo_Barras']}';
							$('#scan').unbind('keypress');
							$('#scan').bind('keypress', function(e) {
								if (e.keyCode == 13) {
									if(separado < separar){
										if(codigoProduto == $('#scan').val()) {
								    		modal('Verificando...');
									    	$.getJSON('./separacaoPedidoLista.php?opcao=separar&scan=' + encodeURIComponent($('#scan').val()) + '&T005_Id={$T005_Id}&quantidade=1&T185_Id={$row['T185_Id']}&codigo={$row['D001_Codigo_Barras']}&T006_Id={$row['T006_Id']}&callback=?', function(request) {
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
										modal('Este produto ja está separado!');
										setTimeout(function() {
											$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
							    				modalRemove();
												$('#contentMenu').empty().html(request.data);
											});
										}, 1800);
									}
								}
							});
							$('#scan').focus();
    						
    						
    						$('#diminuir').unbind('click').bind('click', function(e) {
								if (confirm('Confirmar?')) {
									modal('Verificando...');
    								$.getJSON('./separacaoPedidoLista.php?opcao=separar&scan={$scan}&T005_Id={$T005_Id}&quantidade=0&T185_Id={$row['T185_Id']}&T006_Id={$row['T006_Id']}&callback=?', function(request) {
    								 	modalRemove();
    								 	$('#scan').val('');
    									$('#contentMenu').empty().html(request.data);
									});
								}
								return false;
		    				});

    						$('#voltar').unbind('click').bind('click', function(e) {
								modal('Aguarde...');
    							$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
    							 	modalRemove();
    							 	$('#scan').val('');
    								$('#contentMenu').empty().html(request.data);
								});
								return false;
		    				});
		    				
		    				$('#historico').unbind('click').bind('click', function(e) {
								modal('Aguarde...');
    							$.getJSON('./separacaoPedidoLista.php?opcao=historico&T006_Id={$row['T006_Id']}&T005_Id={$T005_Id}&scan={$scan}&callback=?', function(request) {
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
							$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
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
			$sqlT006 = "SELECT count(T005_Id) as qtde, T005_Id, C007_Primeiro_Nome, T207_Flag_Status FROM T207 							 
						LEFT JOIN T006 ON T006_Id=T207_T006_Id							 
						LEFT JOIN T005 ON T005_Id=T006_T005_Id
						LEFT JOIN C007 ON C007_Id=T207_C007_Id
						WHERE T207_T006_Id='{$_REQUEST['T006_Id']}'";
			$sqlT006 = mysql_query($sqlT006);
			if(mysql_num_rows($sqlT006) > 0){
				$resposta['data'] .= <<<EOT
					<style>
						.tabela {
							background-color: #F6F6F6; 
							border: 0px solid #8F859C;
							border-top: 1px solid #444;
							margin-top: 2px;
							padding: 1px;
							align: center;
						}
						.lista {
							border-top: 1px solid #8F859C;
							padding: 1px;
							margin-left: 0px;
							text-align: center;			
						}
					</style>						
					<button class="voltar" style="margin-left: 8px;" id="voltar">Voltar</button>
					<button class="voltar" style="margin-left: 8px;" id="detalhes">Detalhes</button>
					<table class="tabela" style="margin-left: -4px; width: 99.5%;" align="center" cellspacing="0" cellpadding="0">	
					<tr>
						<td class="lista">Qtde</td>
						<td class="lista">Usuario</td>
						<td class="lista">Status</td>							
					</tr>
EOT;
				while($row = mysql_fetch_array($sqlT006)){
					if($row['T207_Flag_Status'] == 1){
						$status  = 'NÃO ENTREGUE';
					} else if($row['T207_Flag_Status'] == 2){
						$status = 'ENTREGUE';
					} else if($row['T207_Flag_Status'] == 3){
						$status = 'AGUARD. OS.';
					} else if($row['T207_Flag_Status'] == 4){
						$status = 'DEVOLVIDO';
					} else if($row['T207_Flag_Status'] == 5){
						$status = 'CANCELADO';
					}
					$resposta['data'] .= <<<EOT
						<tr>
							<td class="lista">{$row['qtde']}</td>
							<td class="lista">{$row['C007_Primeiro_Nome']}</td>
							<td class="lista">{$status}</td>
						</tr>
						<tr>
							<td colspan="3" style="font-style: italic; margin-top: -5px; text-align: center; font-size: 9px; text-align: left;">{$row['D024_Nome_Empresa']}</td>								
						</tr>				
EOT;
				}
			}
			$resposta['data'] .= <<<EOT
				</table>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#voltar').unbind('click').bind('click', function(e) {
						modal('Aguarde...');
    					$.getJSON('./separacaoPedidoLista.php?opcao=separar&T005_Id={$_REQUEST['T005_Id']}&scan={$_REQUEST['scan']}&quantidade=noValue&callback=?', function(request) {
    					 	modalRemove();
    					 	$('#scan').val('');
    						$('#contentMenu').empty().html(request.data);
						});
						return false;
    				});

    				$('#detalhes').unbind('click').bind('click', function(e) {
						modal('Aguarde...');
    					$.getJSON('./separacaoPedidoLista.php?opcao=detalhes&T006_Id={$_REQUEST['T006_Id']}&T005_Id={$_REQUEST['T005_Id']}&scan={$_REQUEST['scan']}&quantidade=noValue&callback=?', function(request) {
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

	case 'detalhes':
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
    					$.getJSON('./separacaoPedidoLista.php?opcao=historico&T006_Id={$_REQUEST['T006_Id']}&T005_Id={$_REQUEST['T005_Id']}&scan={$_REQUEST['scan']}&quantidade=noValue&callback=?', function(request) {
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
			WHERE T006_T005_Id = '{$T005_Id}' AND (T185_Qtd_Separado IS NULL OR T185_Qtd_Separado = 0) 
			AND T185_Qtd_Para_Separar IS NOT NULL ORDER BY T185_Id ASC");
			while($row = mysql_fetch_array($T006)){
				if($row['D001_Codigo_Produto'] == $scan || $row['D001_Codigo_Barras'] == $scan){
					//produto lido esta na lista
					$encontrado = true;
					$resposta['data'] = <<<EOT
						<div class="produto" style="font-size: 120%;">
							<strong style="font-size: 120%;">{$row['D001_Codigo_Produto']}</strong><br />
							<strong style="font-size: 120%;">{$row['D001_Descricao_Produto']}</strong><br />
							<strong style="font-size: 120%;">{$row['D082_Marca']}</strong><br />
							<strong style="font-size: 120%;">Estoque: {$row['D009_Quantidade_Estoque']}</strong><br />
							<button class="bigBtn" id="separacaoPedidoQtdProdSubmit">Separar: {$row['T006_Quantidade']}</button>
							<button class="bigBtn" id="separacaoPedidoDivergencia">Qtd Insuficiente</button>
							<button class="bigBtn" id="separacaoPedidoVoltar">Voltar</button>
							<input id="separacaoPedidoListaQtdProdId" type="hidden" value="{$row['T006_Id']}">
						</div>
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');
    						$('#separacaoPedidoQtdProdSubmit').unbind('click').bind('click', function() {
    							if (confirm('Separar {$row['T006_Quantidade']} itens?')) {
    				 				modal('Verificando...');
    								$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&T185_Id={$row['T185_Id']}&T006_Id={$row['T006_Id']}&separar={$row['T006_Quantidade']}&callback=?', function(request) {
    									modalRemove();
    									$('#scan').val('');
    									$('#contentMenu').empty().html(request.data);
									});
								}
								return false;
    						});
    						
    						$('#separacaoPedidoDivergencia').unbind('click').bind('click', function(e) {
								if (confirm('Confirmar?')) {
									modal('Verificando...');
    								$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id=' + encodeURIComponent($('#separacaoPedidoListaQtdProdId').val()) + '&T185_Id={$row['T185_Id']}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&divergencia=2&callback=?', function(request) {
    								 	modalRemove();
    								 	$('#scan').val('');
    									$('#contentMenu').empty().html(request.data);
									});
								}
								return false;
		    				});

    						$('#separacaoPedidoVoltar').unbind('click').bind('click', function(e) {
								modal('Aguarde...');
    							$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id=' + encodeURIComponent($('#separacaoPedidoListaQtdProdId').val()) + '&T185_Id={$row['T185_Id']}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&callback=?', function(request) {
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
							$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
			    				modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
						}
					} </script></div>
EOT;
			}
		}
	break;

	case 'incluirProduto':
		$scan = $_REQUEST['scan'];
		$T005_Id = $_REQUEST['T005_Id'];
		if(isset($scan)) {
			$sql = <<<EOT
			SELECT  *
				   	from D001
				left join D049 on D049_D001_Id=D001_Id
				left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T005_D024_Id']}'
				left join D009 on D009_D049_Id=D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D041 on D041_D049_Id=D049_Id
				left join D005 on D005_Id=D001_D005_Id		
				   	WHERE (D001_Codigo_Barras = '{$scan}' 
				   	OR D001_Codigo_Produto = '{$scan}')
					AND D009_C004_Id = '1'
EOT;
			$resultado = mysql_query($sql);
			if(mysql_num_rows($resultado) > 0) {				
				$row = mysql_fetch_assoc($resultado);
				$verificarProduto = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id = '{$T005_Id}' AND T006_D009_Id = '{$row['D009_Id']}'");
				//Produto ja esta no pedido
				if(mysql_num_rows($verificarProduto) > 0){
					$resposta['data'] = <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {								
		    				modal('Produto ja esta no pedido!');		    				
					    	setTimeout(function(){
						    	$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
						    		modalRemove();						    		
									$('#contentMenu').empty().html(request.data);
								});
							}, 1500);
						}</script></div>
EOT;
				} else {
					//Verifica se foi setada alguma quantidade
					if(!isset($_REQUEST['quantidade'])){
						$resposta['data'] = <<<EOT
							<div class="produto" style="margin-left: 6px; width: 94.5%; margin-top: 3px; font-size: 120%; padding: 5px 0;">						
								<strong>{$row['D001_Descricao_Produto']}</strong><br />
								<strong>ESTOQUE: {$row['D009_Quantidade_Estoque']}</strong><br />
								<strong>ESTOQUE LIQUIDO: {$row['D009_Quantidade_Estoque_Liquido']}</strong><br />
								<strong>QUANTIDADE: <input type="text" id="quantidadeProduto" name="quantidadeProduto"/></strong><br />
							</div>
							<button style="margin-left: 6px; width: 94.5%;" class="voltarLarge" id="incluir">Incluir</button>
							<button style="margin-left: 6px; width: 94.5%;" class="voltarLarge" id="voltar">Voltar</button>
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#quantidadeProduto').focus();
								$('#incluir').unbind('click').bind('click', function(e) {
									if (confirm('Deseja incluir este produto no pedido?')) {
								    	modal('Verificando...');
								    	var quantidade = $('#quantidadeProduto').val();							    	
								    	$.getJSON('./separacaoPedidoLista.php?opcao=incluirProduto&scan={$row['D001_Codigo_Produto']}&quantidade=' + encodeURIComponent(quantidade) + '&T005_Id={$T005_Id}&callback=?', function(request) {
								    		modalRemove();
											$('#contentMenu').empty().html(request.data);
										});
									}
									return false;
								});
								
								$('#voltar').unbind('click').bind('click', function() {
				    				 modal('Verificando...');
							    	$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}}&callback=?', function(request) {
							    		modalRemove();					    	
										$('#contentMenu').empty().html(request.data);
									});
				    			});
							}</script></div>
EOT;
					} else {			
						$D009_Id = $row['D009_Id'];
						$row['D041_Valor_Base'] = number_format($row['D041_Valor_Base'],2,',','.');
						$row['D041_Valor_Base'] = ($row['D041_Valor_Base'] == "0,00" ? "" : $row['D041_Valor_Base']);
						// Aliquota IPI
						//$row['D005_Aliquota_IPI'] = number_format($row['D005_Aliquota_IPI'],2,',','.');
						$cClausula =" select *, ";
						$cClausula.=" D053_ST_ICMS_Interno, 
									  D053_ST_ICMS_Inter_Estadual, 
									  D053_Flag_ST, 
									  D018_Id, 
									  C004_D018_Id,
									  T005_Flag_ACP, 
									  C004_CFOP_ST_Venda_Dentro_Estado, 
									  C004_CFOP_ST_Venda_Fora_Estado,
									  C004_CFOP_CONSUMO_Venda_Dentro_Estado,
									  C004_CFOP_CONSUMO_Venda_Fora_Estado,
									  D024_Flag_Contribuinte,
									  T005_Flag_Lista_Preco,
									  T005_D024_Id ";
						$cClausula.=" from T005 ";
						$cClausula.=" left join D024 on D024_Id=T005_D024_Id ";
						$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
						$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T005_C004_Id ";
						$cClausula.=" left join C004 on C004_Id=T005_C004_Id ";
						$cClausula.=" where T005_Id='{$T005_Id}'";
						$T005=mysql_query($cClausula);
						$cErro=mysql_error();
						if (!empty($cErro)) {
						   return "$cErro/p/p$cClausula";
						   exit;
						}
						$mT005=mysql_fetch_array($T005);
						$sql = <<<EOT
							SELECT 
								D009_Id,
								D009_IPI('{$mT005['T005_C004_Id']}',D009_Id,'{$mT005['T005_D024_Id']}','{$mT005['T005_D006_Id']}','1','{$mT005['T005_Flag_ACP']}') as Aliquota_IPI 
							FROM 
								D009 
							where D009_Id='{$D009_Id}'
EOT;
						$result = mysql_query($sql);
						$row2 = mysql_fetch_assoc($result);
						$row['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');

						require_once('bibliotecas/classes/CAD002.php');
						$CAD002 = new CAD002();
						if(!empty($mT005['T005_Flag_Lista_Preco'])){
							$row['T006_Valor_Preco_Original'] = $CAD002->retornaPrecoVenda($row['D049_Id'], $mT005['T005_Flag_Lista_Preco'], null);
						} else {
							$row['T006_Valor_Preco_Original'] = $CAD002->retornaPrecoVenda($row['D049_Id'], null, $mT005['T005_D024_Id']);
						}
						// Adicionar
						require_once("bibliotecas/classes/VEN002.php");
						$VEN002 = new VEN002();
						$row['T006_Valor_Preco_Sem_Desconto_Unitario'] = '0,00';
						$row['T006_D009_Id'] = $D009_Id;
						$row['T006_T005_Id'] = $T005_Id;
						$row['T006_Descricao_Produto'] = strtoupper($row['D001_Descricao_Produto']);
						$row['T006_Quantidade'] = $_REQUEST['quantidade'];				
						$form = new formWrap($row);
						$retorno = $VEN002->incluirProduto($form, 2);
						mysql_query("UPDATE T005 SET T005_Flag_Status_Servicos = '0' WHERE T005_Id = '{$T005_Id}'");
						if($retorno != true){
							$resposta['data'] = <<<EOT
								<div class="alerta" style="margin-top: 20%; margin-left: 6px;">
									<div style="margin-top: 20px;"><strong style="font-size: 120%;">O PRODUTO NÃO PODE SER<br> INCLUIDO NO PEDIDO <br /></strong></div>
								</div>
								<button style="margin-left: 6px; width: 94.5%;" class="voltarLarge" id="voltar">Voltar</button>
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {								
									$('#voltar').unbind('click').bind('click', function() {
					    				 modal('Verificando...');
								    	$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
								    		modalRemove();					    	
											$('#contentMenu').empty().html(request.data);
										});
					    			});
								}</script></div>
EOT;
						} else {
							//$VEN002->gerarControleProdutosPedido($T005_Id);
							$resposta['data'] = <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							    	modal('Produto incluido com Sucesso!');
							    	setTimeout(function(){
								    	$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
								    		modalRemove();						    		
											$('#contentMenu').empty().html(request.data);
										});
									}, 1500);
								}</script></div>
EOT;
						}
					}
				}
			} else {
				$resposta['data'] = <<<EOT
					<div class="alerta" style="margin-top: 20%; margin-left: 6px;">
						<div style="margin-top: 20px;"><strong style="font-size: 120%;">PRODUTO NÃO ENCONTRADO<br> <br /></strong></div>
					</div>
					<button style="margin-left: 6px; width: 94.5%;" class="voltarLarge" id="voltar">Voltar</button>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#scan').unbind('keypress');
						$('#scan').bind('keypress', function(e) {
							if (e.keyCode == 13) {
						    	modal('Verificando...');
						    	$.getJSON('./separacaoPedidoLista.php?opcao=incluirProduto&scan=' + encodeURIComponent($('#scan').val()) + '&T005_Id={$T005_Id}&callback=?', function(request) {
						    		modalRemove();
						    		$('#scan').val('');
									$('#contentMenu').empty().html(request.data);
								});
							}
						});
						$('#scan').focus();
						
						$('#voltar').unbind('click').bind('click', function() {
		    				 modal('Verificando...');
					    	$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}}&callback=?', function(request) {
					    		modalRemove();					    	
								$('#contentMenu').empty().html(request.data);
							});
		    			});
					}</script></div>
EOT;
			}
		} else {
			$resposta['data'] = <<<EOT
				<div class="alerta" style="margin-top: 20%; margin-left: 6px;">
					<div style="margin-top: 20px;"><strong style="font-size: 120%;">LEIA O CÓDIGO DO PRODUTO<br>PARA INCLUIR NO PEDIDO<br> <br /></strong></div>
				</div>
				<button style="margin-left: 6px; width: 94.5%;" class="voltarLarge" id="voltar">Voltar</button>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#scan').unbind('keypress');
					$('#scan').bind('keypress', function(e) {
						if (e.keyCode == 13) {
					    	modal('Verificando...');
					    	$.getJSON('./separacaoPedidoLista.php?opcao=incluirProduto&scan=' + encodeURIComponent($('#scan').val()) + '&T005_Id={$T005_Id}&callback=?', function(request) {
					    		modalRemove();
					    		$('#scan').val('');
								$('#contentMenu').empty().html(request.data);
							});
						}
					});
					$('#scan').focus();
					
					$('#voltar').unbind('click').bind('click', function() {
	    				 modal('Verificando...');
				    	$.getJSON('./separacaoPedidoLista.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
				    		modalRemove();					    	
							$('#contentMenu').empty().html(request.data);
						});
	    			});
				}</script></div>
EOT;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";