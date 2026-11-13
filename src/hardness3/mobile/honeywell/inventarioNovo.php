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
			    		$.getJSON('./inventarioNovo.php?opcao=procura&callback=?', function(request) {
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
			    		$.getJSON('./inventarioNovo.php?opcao=autentica&callback=?', function(request) {
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
					    	$.getJSON('./inventarioNovo.php?opcao=autentica&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
	
	case 'procura':
		$scan = $_REQUEST['scan'];
		if(isset($_REQUEST['alterar'])){
			$D009 = mysql_query("SELECT * FROM D009 LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id WHERE D009_Id = '{$_REQUEST['D009_Id']}' AND D009_C004_Id = '1'");
			$D009 = mysql_fetch_array($D009);
			$codigoProduto = $D009['D001_Codigo_Produto'];	
			$sql = "INSERT INTO T001 (
			T001_D009_Id,
			T001_Codigo_Produto,
			T001_Quantidade, 
			T001_C004_Id, 
			T001_Data_Lancamento, 
			T001_Descricao_Operacao, 
			T001_Flag_Operacao
			)VALUES(
			'{$_REQUEST['D009_Id']}',
			'{$codigoProduto}',
			'{$_REQUEST['quantidade']}',
			'1',
			CURDATE(),
			'Inventário Coletor',
			'B'								
			);";
			mysql_query($sql);
		}
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
				    	$.getJSON('./inventarioNovo.php?opcao=procura&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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
				$resposta['data'] = <<<EOT
					<div class="alerta" style="width: 92%; height: 15%; margin-left: 9px;">
						<div><strong style="font-size: 120%;">INSIRA O VALOR DA QUANTIDADE DE ESTOQUE</strong></div>
					</div>
					<div class="produto" style="margin-left: 9px; margin-top: 3px; font-size: 120%; padding: 3px 0;">						
						<strong>{$produto['D001_Descricao_Produto']}</strong><br />
						<strong>ESTOQUE: {$produto['D009_Quantidade_Estoque']}</strong><br />
						<strong>ESTOQUE LIQUIDO: {$produto['D009_Quantidade_Estoque_Liquido']}</strong><br />
						<strong style="font-size: 120%;">Quantidade </strong><input type="text" id="quantidade">
					</div>
					<div style="margin-left: 6px;">
						<button class="voltar" style="width: 46.5%;" id="alterar">Alterar</button>
						<button class="voltar" style="width: 46.5%;" id="voltar">Voltar</button>
					</div>										
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#quantidade').focus();
						$('#alterar').unbind('click').bind('click', function(e){
							modal('Aguarde...');
    						$.getJSON('./inventarioNovo.php?opcao=procura&alterar=true&D009_Id={$produto['D009_Id']}&quantidade=' + encodeURIComponent($('#quantidade').val()) + '&callback=?', function(request) {
    						 	modalRemove();
    						 	$('#scan').val('');
    							$('#contentMenu').empty().html(request.data);
							});
							return false;
						});
						$('#voltar').unbind('click').bind('click', function(e){
							modal('Aguarde...');
    						$.getJSON('./inventarioNovo.php?opcao=procura&callback=?', function(request) {
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