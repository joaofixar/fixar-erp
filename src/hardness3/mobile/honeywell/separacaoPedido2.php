<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'produtos':
		$T005_Id = $_REQUEST['T005_Id'];
		$T185_Id = $_REQUEST['T185_Id'];
		$T005_C007_Id = $_REQUEST['T005_C007_Id'];
		$T006_Id = $_REQUEST['T006_Id'];
		
		if(isset($_REQUEST['box'])){
			$sql = "UPDATE T005 SET T005_Flag_Status = '10', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Estoque_Box = '{$_REQUEST['box']}' WHERE T005_Id = '{$T005_Id}'";			
			$sql = mysql_query($sql);
			$resposta['data'] = <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modalRemove();
				modal('O PEDIDO FOI SEPARADO');
				setTimeout("reload()",1000);
				function reload(){
					modalRemove();
					window.location="./menu.php";
				}
			} </script></div>
EOT;
		}
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
		
		$sql2 = <<<EOT
			SELECT * FROM T006 
			LEFT JOIN D009 on D009_Id=T006_D009_Id 
			LEFT JOIN T185 ON T185_T006_Id=T006_Id
			LEFT JOIN T066 ON T066_Id=T185_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN D001 ON D001_Id=D009_Id   
			WHERE 
			T006_T005_Id = '{$T005_Id}' AND (T185_Qtd_Separado != 0 OR T185_Tipo_Divergencia > 0)  AND T185_Qtd_Para_Separar IS NOT NULL ORDER BY T185_Id DESC LIMIT 1
EOT;
		$res1 = mysql_query($sql2);
		$result = mysql_fetch_array($res1);
		if(isset($_REQUEST['voltar'])){
			$sql = "UPDATE T185 SET T185_Qtd_Separado = '0', T185_Tipo_Divergencia = 0 WHERE T185_Id = '{$result['T185_Id']}';";
			mysql_query($sql);
			$sql = "UPDATE T006 SET T006_Flag_Divergencia = 'N' WHERE T006_Id = '{$result['T006_Id']}';";
			mysql_query($sql);
		}
		$res1 = mysql_query($sql2);
		// OR T006_Quantidade_Separacao != 0
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
		//$res = mysql_fetch_array($res);
		//echo $res['D001_Codigo_Produto'];
		if(mysql_numrows($res)> 0){ 
			$row = mysql_fetch_assoc($res);
			if(!empty($row['T066_Id'])){
				$row['T006_Quantidade'] = number_format($row['T006_Quantidade'], 0);
				$row['T006_Quantidade_Separacao'] = number_format($row['T006_Quantidade_Separacao'], 0);
				$quantidade = gCorrigeNumero($row['T006_Quantidade']);
				//echo $row['T005_Id'];
				$resposta['data'] .= <<<EOT
				<table style="width: 100%; font-size: 120%;" align="center">
					<tr id="T005_Id-{$row['T005_Id']}"></tr>
						<tr align="center"><td>Produto: <strong style="font-size: 120%;"><br>{$row['T006_Descricao_Produto']}</strong></td></tr>
						<tr align="center"><td>Quantidade: <strong style="font-size: 120%;"><br>{$quantidade}</strong></td></tr>
					<tr>
				</table>
				<button class="bigBtn" id="separacaoPedido2NaoEncontrado">Não Encontrei</button>
EOT;
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
		    				$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&voltar=1&callback=?', function(request) {
	  							modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
						}
		    		});
		    		
			   		$('#separacaoPedido2NaoEncontrado').unbind('click').bind('click', function(e) {
						if (confirm('Confirmar?')) {   
							modal('Verificando...');
			   				$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id={$row['T006_Id']}&T185_Id={$row['T185_Id']}&divergencia=1&callback=?', function(request) {
	    						modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
						}
			   		});
						
					$('#scan').unbind('keypress');
					$('#scan').bind('keypress', function(e) {
						if (e.keyCode == 13) {
		    				modal('Verificando...');
		    				$.getJSON('./separacaoPedido2.php?opcao=busca&T005_Id={$T005_Id}&scan=' + encodeURIComponent($('#scan').val()) + '&D004_Id={$row['D004_Id']}&callback=?', function(request) {
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
			} else {
				$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id={$row['T006_Id']}&T185_Id={$row['T185_Id']}&divergencia=3&callback=?', function(request) {
	    				$('#contentMenu').empty().html(request.data);
					});
				}
				</script>
				</div>
EOT;
			}
		} else {
			// é necessário ler o box?
			if ($g['C031']['informarBoxNaSeparacao'] == 'S') {
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
			    		$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id=$T005_Id&box=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
			    			modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
					}
				});
				$('#scan').focus();
				}</script></div>
EOT;
			} else {
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			    		modal('Verificando...');
			    		$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id=$T005_Id&box=0&callback=?', function(request) {
			    			modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>
EOT;
			}
		}
	break;
	
	case 'pedidos':
			$sql = <<<EOT
				SELECT 
					T005_Id, T005_Estoque_BOX, D024_Nome_Empresa, T005_Data_Emissao, T005_Data_Saida, if(T005_Flag_ACP='1','NOTA FISCAL',if(T005_Flag_ACP='3','ACP', if(T005_Flag_ACP='5','NOTA D1',if(T005_Flag_ACP='2','NÃO FATURAR',if(T005_Flag_ACP='6','NF-e',if(T005_Flag_ACP='7','NFS-e','CUPOM FISCAL'))))))
				 	Tipo_Documento, T005_Observacao_1, T005_D022_Id, D022_Nome_Empresa, D006_Descricao_Comercial, D006_Codigo_CFOP, T005_Data_Hora_Almoxarifado, 
					Liberou.C007_Primeiro_Nome as Nome_Liberou, Vendedor.C007_Primeiro_Nome as Nome_Vendedor, D020_Nome_Cidade,	T005_Status_Pedido(T005_Flag_Status,2)
				 	T005_Status, D018_UF, T005_Flag_Status, T005_Flag_Reenviado_Almoxarifado, T005_D024_Id, T005_D022_Id, D036_Tipo_Transporte, T007_Numero_Nota_Fiscal, 
					vendedor.C007_Primeiro_Nome as Nome_Vendedor, Separou.C007_Primeiro_Nome as Nome_Separou, group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal,
				 	T005_Valor_Total, T005_Prazos(T005_Id) Prazo, T005_Data_Emissao, T005_Data_Saida, C004_Emite_NF_Caixa,
				 	T005_C007_Id_Separou
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
				WHERE ((1=1)) AND T005_C004_Id='{$g['empresaAtual']}' AND ((T005_Flag_Status='2' AND T005_C007_Id_Separou='{$g['usuarioAtual']}') OR (T005_Flag_Status='1')) 
				GROUP BY T005_Id ORDER BY T005_Flag_Status DESC, T005_Id LIMIT 1
EOT;
			//echo $sql;
			$res = mysql_query($sql);
			$status = mysql_fetch_array($res);
			$T005_C007_Id = $status['T005_C007_Id_Separou'];
			$T005_Id = $status['T005_Id'];
			if($status['T005_Flag_Status'] == 2){
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#barraTopPedido').html('Pedido: {$T005_Id}');
		    			modal('Pedido {$T005_Id}');
		    			$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&T005_C007_Id={$T005_C007_Id}&callback=?', function(request) {
		    				modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
				    } </script></div>
EOT;
			} else if($status['T005_Flag_Status'] == 1) {
				mysql_query("UPDATE T005 SET T005_C007_Id_Separou = {$g['usuarioAtual']} WHERE T005_Id = {$T005_Id}");
				mysql_query("UPDATE T005 SET T005_Flag_Status = '2', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id ='$T005_Id'");
				$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#barraTopPedido').html('Pedido: {$T005_Id}');
		    			modal('Pedido {$T005_Id}');
		    			$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
		    				modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
				    } </script></div>
EOT;
			} else {
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
	
	case 'busca':
		$T005_Id = $_REQUEST['T005_Id'];
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
				T006_T005_Id = '{$T005_Id}' AND (T185_Qtd_Separado IS NULL OR T185_Qtd_Separado = 0) AND T006_Flag_Divergencia = 'N' AND T185_Qtd_Para_Separar IS NOT NULL LIMIT 1
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
				    				$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&scan={$D004_Id}&callback=?', function(request) {
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
					    				$.getJSON('./separacaoPedido2.php?opcao=busca&T185_Id={$row['T185_Id']}&T005_Id=$T005_Id&D049_Id=$D049_Id&scan=' + encodeURIComponent('{$scan}') + '&novo=S&callback=?', function(request) {
					    					modalRemove();
					    					$('#scan').val('');
											$('#contentMenu').empty().html(request.data);
										});
									} else {
										modal('Carregando...');
	    								$.getJSON('./separacaoPedido2.php?opcao=pedidos&callback=?', function(request) {
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
						$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&callback=?', function(request) {
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
							<input id="separacaoPedido2QtdProdId" type="hidden" value="{$row['T006_Id']}">
						</div>
EOT;
						//echo $row['T185_Id'];
						$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').unbind('keypress');
    						$('#separacaoPedidoQtdProdSubmit').unbind('click').bind('click', function() {
    							if (confirm('Separar {$row['T006_Quantidade']} itens?')) {
    				 				modal('Verificando...');
    								$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&T185_Id={$row['T185_Id']}&T006_Id={$row['T006_Id']}&separar={$row['T006_Quantidade']}&callback=?', function(request) {
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
    								$.getJSON('./separacaoPedido2.php?opcao=produtos&T005_Id={$T005_Id}&T006_Id=' + encodeURIComponent($('#separacaoPedido2QtdProdId').val()) + '&T185_Id={$row['T185_Id']}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&divergencia=2&callback=?', function(request) {
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