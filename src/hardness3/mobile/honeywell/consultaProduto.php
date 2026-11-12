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
			    		$.getJSON('./consultaProduto.php?opcao=procura&callback=?', function(request) {
			    			modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
						$('#barraTop').show();
						$('#barraTopUsuario').empty().html('Usuário: ' + '{$rowUsuarioAtual['C007_Primeiro_Nome']}');
					} </script></div>		
EOT;
			} else {
				$resposta['data'] = <<<EOT
					<div>
	    			<img src="http://www.carsigma.com.br/images/logo.jpg" style="width: 100%; height: 70px;" />
	    			</div>
					<div class="alerta" style="margin-top: 30%;"><strong style="font-size: 120%;"><br>{$erro}<br> </br></strong></div>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						modal('Carregando...');
			    		$.getJSON('./consultaProduto.php?opcao=autentica&callback=?', function(request) {
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
					    	$.getJSON('./consultaProduto.php?opcao=autentica&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
					    		modalRemove();
					    		$('#scan').val('');
								$('#contentMenu').empty().html(request.data);
							});
						}
					});
				} </script></div>
				<div>
	    		<img src="http://www.carsigma.com.br/images/logo.jpg" style="width: 100%; height: 70px;" />
	    		</div>
				<div class="login" style="margin-top: 30%;"><strong style="font-size: 120%;"><br>LEIA O CODIGO DE ACESSO<br> </br></strong></div>
EOT;
		}
	break;
	
	case 'procura':
		$scan = $_REQUEST['scan'];
	  	if(!isset($scan)){
			$resposta['data'] .= <<<EOT
			<div class="alerta" style="margin-top: 35%; margin-left: 6px;">
				<div style="margin-top: 6px;"><strong style="font-size: 120%;"><br>LEIA O CÓDIGO DO PRODUTO<br> </br></strong></div>
			</div>
			<button style="margin-left: 6px; width: 94.5%;" class="voltarLarge" id="voltar">Voltar</button>
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
				    	modal('Verificando...');
				    	$.getJSON('./consultaProduto.php?opcao=procura&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
			$sqlProduto = mysql_query("SELECT * FROM D009 LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id WHERE (D001_Codigo_Produto = '{$scan}' OR D001_Codigo_Barras = '{$scan}') AND D009_C004_Id = '1'");
			if(mysql_num_rows($sqlProduto) > 0) {
				$produto = mysql_fetch_array($sqlProduto);
				$pedidos = mysql_query("SELECT count(T005_Id) as quantidadePedidos FROM T005 							
							LEFT JOIN D024 ON D024_Id=T005_D024_Id 
							LEFT JOIN T006 ON T006_T005_Id=T005_Id 
							LEFT JOIN D009 ON D009_Id=T006_D009_Id 
							LEFT JOIN D049 ON D049_Id=D009_D049_Id 
							LEFT JOIN T008 ON T008_T006_Id=T006_Id 
							LEFT JOIN T007 ON T007_Id=T008_T007_Id AND T007_Flag_Cancelada!='S' 							
							WHERE T006_D009_Id='{$produto['D009_Id']}' AND (T006_Quantidade>0 OR T006_Quantidade_Separacao>0) AND T007_Id IS NULL 
							AND T005_Flag_Status!=5 AND T005_Flag_Status!=26 AND T005_Flag_Status!='7' AND T005_Flag_Status!='8' AND T005_Flag_Status!='11'");
				$pedidos = mysql_fetch_array($pedidos);					
				$resposta['data'] = <<<EOT
					<button class="voltarLarge" style="margin-left: 9px;" id="voltar">Voltar</button>
					<div class="produto" style="margin-left: 9px; margin-top: 3px; font-size: 120%;">						
						<strong>{$produto['D001_Descricao_Produto']}</strong><br />
						<strong>ESTOQUE: {$produto['D009_Quantidade_Estoque']}</strong><br />
						<strong>ESTOQUE LIQUIDO: {$produto['D009_Quantidade_Estoque_Liquido']}</strong><br />
						<strong>PEDIDOS: {$pedidos['quantidadePedidos']}</strong><br />
					</div>
					<div style="margin-left: 5px;">					
EOT;
				$sqlT006 = "SELECT count(T005_Id) as qtde, T005_Id, D024_Nome_Empresa, C007_Primeiro_Nome,T207_Data, T207_Flag_Status FROM T207 							 
							LEFT JOIN T006 ON T006_Id=T207_T006_Id							 
							LEFT JOIN T005 ON T005_Id=T006_T005_Id
							LEFT JOIN D024 ON D024_Id=T005_D024_Id
							LEFT JOIN C007 ON C007_Id=T207_C007_Id
							LEFT JOIN T008 ON T008_T006_Id=T006_Id 
							LEFT JOIN T007 ON T007_Id=T008_T007_Id AND T007_Flag_Cancelada!='S' 							
							WHERE T207_D009_Id='{$produto['D009_Id']}' AND (T006_Quantidade>0 OR T006_Quantidade_Separacao>0) AND T007_Id IS NULL 
							AND T005_Flag_Status!=5 AND T005_Flag_Status!=26 AND T005_Flag_Status!='7' AND T005_Flag_Status!='8' AND T005_Flag_Status!='11'
							GROUP BY T207_Flag_Status, T005_Id ORDER BY T005_Id DESC";
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
						<table class="tabela" style="margin-left: -4px; width: 99.5%;" align="center" cellspacing="0" cellpadding="0">	
						<tr>
							<td class="lista">Qtde</td>
							<td class="lista">Pedido</td>
							<td class="lista">Data</td>
							<td class="lista">Status</td>							
						</tr>
EOT;
					while($row = mysql_fetch_array($sqlT006)){
						$data = gCorrigeData($row['T207_Data']);
						$data = substr($data, 0, 6) . substr($data, 8, 10);  
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
						$NomeEmpresa = substr($row['D024_Nome_Empresa'], 0, 25);
						$resposta['data'] .= <<<EOT
							<tr>
								<td class="lista">{$row['qtde']}</td>
								<td class="lista">{$row['T005_Id']}</td>
								<td class="lista">{$data}</td>
								<td class="lista">{$status}</td>
							</tr>
							<tr>
								<td colspan="3" style="font-style: italic; margin-top: -5px; text-align: center; font-size: 9px; text-align: left;">{$NomeEmpresa}</td>
								<td colspan="1" style="font-style: italic; margin-top: -5px; text-align: center; font-size: 9px; text-align: right;">{$row['C007_Primeiro_Nome']}</td>								
							</tr>				
EOT;
					}
				}													
				$resposta['data'] .= <<<EOT
						</table>
					</div>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#voltar').unbind('click').bind('click', function(e) {
							modal('Aguarde...');
    						$.getJSON('./consultaProduto.php?opcao=procura&callback=?', function(request) {
    						 	modalRemove();
    						 	$('#scan').val('');
    							$('#contentMenu').empty().html(request.data);
							});
							return false;
	    				});
				    } </script></div>
EOT;
			} else {
				$resposta['data'] = <<<EOT
					<div style="margin-top: 45%" class="titulo">
					<strong style="font-size: 140%;"><br>PRODUTO NÃO ENCONTRADO<br> </br></strong></div>			
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
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";