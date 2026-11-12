<?php
namespace hardness;

include('conf.php');

global $g;

$scan = $_REQUEST['scan'];

$resposta = array('code' => true, 'data' => '');

function incluirProduto($campo, $T003_Id){
	require_once('bibliotecas/classes/CAD002.php');
	$CAD002 = new CAD002();
	//$campo é um array com todas as informações do produto
	
	$T004_Percentual_Desconto = 0;
	$T004_Valor_Desconto_Unitario = 0;
	$T004_Valor_Preco_Unitario = $campo['D041_Valor_Base'];
	$T004=mysql_query("SELECT T004_Item FROM T004 WHERE T004_T003_Id='$T003_Id' ORDER BY T004_Item DESC LIMIT 1");
    $mT004=mysql_fetch_array($T004);
    $T004_Item = $mT004['T004_Item']+1;
    
	$listPreco = mysql_query("SELECT T003_Flag_Lista_Preco FROM T003 WHERE T003_Id='{$T003_Id}'");
    $mLista_Preco = mysql_fetch_array($listPreco);
    
    if(!empty($mLista_Preco['T003_Flag_Lista_Preco'])){
		$Preco_Tabelado = $CAD002->retornaPrecoVenda($mT004['D049_Id'], $mLista_Preco['T003_Flag_Lista_Preco'], null);
	}else{
		$Preco_Tabelado = $CAD002->retornaPrecoVenda($mT004['D049_Id'], null, $D024_Id);
	}
	$Preco_Tabelado = gCorrigeNumeroInverte($Preco_Tabelado);

    if($Preco_Tabelado>0) {
        $T004_Percentual_Desconto = ((1-($T004_Valor_Preco_Unitario/$Preco_Tabelado))*100);
        $T004_Valor_Desconto_Unitario = (($Preco_Tabelado*($T004_Valor_Preco_Unitario/100)));
    } else {
    	$Preco_Tabelado=$T004_Valor_Preco_Unitario;
    }
    
    $D005 = mysql_query("SELECT * FROM D005 WHERE D005_Id = '{$campo['D001_D005_Id']}'");
    $resD005 = mysql_fetch_array($D005);
    
    $cClausula =" select *, ";
	$cClausula.=" D053_ST_ICMS_Interno, 
	              D053_ST_ICMS_Inter_Estadual, 
	              D053_Flag_ST, 
	              D018_Id, 
	              C004_D018_Id, 
	              C004_CFOP_ST_Venda_Dentro_Estado, 
	              C004_CFOP_ST_Venda_Fora_Estado,
	              C004_CFOP_CONSUMO_Venda_Dentro_Estado,
	              C004_CFOP_CONSUMO_Venda_Fora_Estado,
	              T003_Flag_Lista_Preco,
	              D024_Flag_Contribuinte ";
	$cClausula.=" from T003 ";
	$cClausula.=" left join D024 on D024_Id=T003_D024_Id ";
	$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
	$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T003_C004_Id ";
	$cClausula.=" left join C004 on C004_Id=T003_C004_Id ";
	$cClausula.=" where T003_Id='{$T003_Id}'";
	$T003=mysql_query($cClausula);
    $mT003=mysql_fetch_array($T003);
	$sql = <<<EOT
		SELECT 
			D009_Id, 
			D009_IPI('{$mT003['T003_C004_Id']}',D009_Id,'{$mT003['T003_D024_Id']}','{$mT003['T003_D006_Id']}','1','{$mT003['T003_Flag_Acp']}') as Aliquota_IPI 
		FROM 
			D009 
		where D009_Id='{$D009_Id}'
EOT;
	/**
	 * DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
	 * 
	 * $row['D041_Valor_Base'] = number_format($row['D041_Valor_Base'],2,',','.');
	 */
	if(isset($mT003['T003_Flag_Lista_Preco'])){
		//die("D049: ".$row['D049_Id']." Lista: ".$mT003['T003_Flag_Lista_Preco']);
		$campo['D041_Valor_Base'] = $CAD002->retornaPrecoVenda($campo['D049_Id'], $mT003['T003_Flag_Lista_Preco'], null);
	}else{
		$campo['D041_Valor_Base'] = $CAD002->retornaPrecoVenda($campo['D049_Id'], null, $mT003['T003_D024_Id']);
	}
 
	
	$result = mysql_query($sql);
	$row2 = mysql_fetch_assoc($result);
	$campo['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');
    
	
	$cClausula  = " INSERT INTO T004 (";
    $cClausula .= " T004_C004_Id, ";
    $cClausula .= " T004_Item, ";
    $cClausula .= " T004_Codigo_Produto, ";
    $cClausula .= " T004_Descricao_Produto, ";
    $cClausula .= " T004_T003_Id, ";
    $cClausula .= " T004_D009_Id, ";
    $cClausula .= " T004_D037_Id, ";
    $cClausula .= " T004_D082_Id, ";
    $cClausula .= " T004_Quantidade, ";
    $cClausula .= " T004_Valor_Custo_Unitario, ";
    $cClausula .= " T004_Valor_Preco_Unitario, ";
    $cClausula .= " T004_Valor_Preco_Sem_Desconto_Unitario, ";
    $cClausula .= " T004_Valor_Preco_Original, ";
    $cClausula .= " T004_Valor_Preco_Original_Bruto, ";  
    $cClausula .= " T004_Percentual_Desconto, ";
    $cClausula .= " T004_Valor_Desconto_Unitario, ";
    $cClausula .= " T004_Percentual_Desconto_Bruto, ";
    $cClausula .= " T004_Valor_Desconto_Unitario_Bruto, ";
    $cClausula .= " T004_IPV, ";
    $cClausula .= " T004_Peso_Unitario, ";
    $cClausula .= " T004_D005_Id,";
    $cClausula .= " T004_Classificacao_Fiscal";    
    $cClausula .= " ) VALUES ( ";
    $cClausula .= " '1', ";
    $cClausula .= " '$T004_Item', ";
    $cClausula .= " '{$campo['D001_Codigo_Produto']}', ";
    $cClausula .= " '{$campo['D001_Descricao_Produto']}', ";
    $cClausula .= " '$T003_Id', ";
    $cClausula .= " '{$campo['D009_Id']}', ";
    $cClausula .= " '{$campo['D001_D037_Id']}', ";
    $cClausula .= " '{$campo['D049_D082_Id']}', ";
    $cClausula .= " '1', ";
    $cClausula .= " '{$campo['D009_Valor_Custo_Unitario']}', ";
    $cClausula .= " '{$campo['D041_Valor_Base']}', ";
    $cClausula .= " '{$Preco_Tabelado}', ";
    $cClausula .= " '{$Preco_Tabelado}', ";
    $cClausula .= " '{$Preco_Tabelado}', ";  
    $cClausula .= " '$T004_Percentual_Desconto', ";
    $cClausula .= " '$T004_Valor_Desconto_Unitario', ";
    $cClausula .= " '$T004_Percentual_Desconto', ";
    $cClausula .= " '$T004_Valor_Desconto_Unitario', ";  
    $cClausula .= " '$T004_Valor_Preco_Unitario/{$campo['D009_Valor_Custo_Unitario']}', ";
    $cClausula .= " '{$campo['D001_Peso_Unitario_Kg']}', ";
    $cClausula .= " '{$campo['D001_D005_Id']}',";
    $cClausula .= " '{$resD005['D005_Classificacao_Fiscal']}')";
	
    mysql_query($cClausula);
	return true;
}

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
			    		$.getJSON('./orcamento.php?opcao=orcamento&callback=?', function(request) {
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
			    		$.getJSON('./orcamento.php?opcao=autentica&callback=?', function(request) {
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
					    	$.getJSON('./orcamento.php?opcao=autentica&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
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

	case 'orcamento':
		$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : null; 
		if($_REQUEST['incluir'] == true){
			$T003 = "INSERT INTO T003 (
									T003_C007_Id_Vendedor_Interno,
									T003_C004_Id,
									T003_Data_Emissao,
									T003_Flag_Status_Orcamento
								) VALUES (
									'{$g['usuarioAtual']}',
									'1',
									CURDATE(),
									'I')";
			$T003 = mysql_query($T003);
			$T003_Id = $g['mysqlLastId'];
			$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {					
					$('#barraTopPedido').html('Orçamento: {$T003_Id}');
				} </script></div>		
EOT;
		}
		if($_REQUEST['remover'] == true){
			mysql_query("DELETE FROM T004 WHERE T004_Id = '{$_REQUEST['T004_Id']}'");
		}
		if(!isset($T003_Id)){
			if($_REQUEST['finalizar'] == true){
				mysql_query("UPDATE T003 SET T003_Flag_Status_Orcamento = 'P' WHERE T003_Id = '{$_REQUEST['idOrcamento']}'");
			}
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {					
					$('#barraTopPedido').html('Orçamento: {$T003_Id}');
				} </script></div>	
				<div style="margin-left: 5px; margin-top: 3px;">
					<div class="tituloBlue" style="font-size: 16px">
						<strong><div style="margin-top: 30px;">MENU ORÇAMENTO</div></strong>
					</div>
					<div style="margin-left: 6px;">
						<button class="menuBtn" id="incluir">Incluir<br /> Orçamento</button>
						<button class="menuBtn" id="consultar">Consultar<br /> Orçamentos</button>
						<button class="menuBtn" id="voltar">Voltar</button>
					</div>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {					
					$('#barraTopPedido').empty();
					$('#incluir').unbind('click').bind('click', function() {
						modal('Incluindo cabeçalho do orçamento...');
	    				 $.getJSON('./orcamento.php?opcao=orcamento&incluir=true&callback=?', function(request) {
				    		modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
	    			});
	    			
	    			$('#consultar').unbind('click').bind('click', function() {
						modal('Verificando...');
	    				 $.getJSON('./orcamento.php?opcao=consulta&callback=?', function(request) {
				    		modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
	    			});
					
					$('#voltar').unbind('click').bind('click', function() {
	    				 window.location="./menu.php";
	    			});
				}</script></div>
EOT;
		} else {
			$resposta['data'] .= <<<EOT
				<div class="alerta" style="margin-bottom: 3px; width: 97.1%; margin-left: 3px"><strong style="font-size: 100%;">LEIA UM PRODUTO PARA ADICIONAR</strong><br /></div>
				<div style="margin-left: 0px; margin-top: -3px;">
					<button class="voltar" style="width: 48.2%;" id="finalizar">Finalizar<br /> Orçamento</button>						
					<button class="voltar" style="width: 48.2%;" id="voltar">Voltar</button>
				</div>				
EOT;
			$produtos = mysql_query("SELECT D001_Codigo_Produto, D001_Descricao_Produto, D082_Marca, T004_Quantidade FROM T004 
									LEFT JOIN D009 ON T004_D009_Id = D009_Id LEFT JOIN D049 ON D009_D049_Id = D049_Id
									LEFT JOIN D001 ON D049_D001_Id = D001_Id LEFT JOIN D082 ON D049_D082_Id = D082_Id
									WHERE T004_T003_Id = '{$T003_Id}'");
			if(mysql_num_rows($produtos) > 0){
				$resposta['data'] .= <<<EOT
					<style>
						.tabela {
							background-color: #F6F6F6; 
							border: 0px solid #8F859C;
							border-top: 1px solid #444;
							margin-top: 3px;
							padding: 1px;
					}
						.lista {
							border-top: 1px solid #8F859C;
							padding: 2px;
							margin-left: 2px;
							text-align: center;			
						}		align: center;
						
					</style>
					<table class="tabela" style="width: 98%;" align="center" cellspacing="0" cellpadding="0">	
					<tr>
						<td class="lista">Codigo</td>
						<td class="lista">Produto</td>
						<td class="lista">Marca</td>
						<td class="lista">Qtde</td>							
					</tr>	
EOT;

				while($row = mysql_fetch_array($produtos)){
					$produto = substr($row['D001_Descricao_Produto'], 0, 15);
					$resposta['data'] .= <<<EOT
						<tr>
							<td class="lista">{$row['D001_Codigo_Produto']}</td>
							<td class="lista">{$produto}</td>
							<td class="lista">{$row['D082_Marca']}</td>
							<td class="lista">{$row['T004_Quantidade']}</td>							
						</tr>	
EOT;
				}
			}
			
			$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {			
					$('#finalizar').unbind('click').bind('click', function() {
						modal('Finalizando Orçamento...');
	    				 $.getJSON('./orcamento.php?opcao=orcamento&finalizar=true&idOrcamento={$T003_Id}&callback=?', function(request) {
				    		modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
	    			});
					
					$('#voltar').unbind('click').bind('click', function() {
	    				 $.getJSON('./orcamento.php?opcao=orcamento&callback=?', function(request) {
				    		$('#contentMenu').empty().html(request.data);
						});
	    			});
    				
	    			$('#scan').unbind('keypress');
					$('#scan').bind('keypress', function(e) {
						if (e.keyCode == 13) {
				    		modal('Verificando...');
					    	$.getJSON('./orcamento.php?opcao=incluirProduto&scan=' + encodeURIComponent($('#scan').val()) + '&T003_Id={$T003_Id}&callback=?', function(request) {
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
	break;
	
	case 'incluirProduto':
		$T003_Id = $_REQUEST['T003_Id'];
		$scan = $_REQUEST['scan'];				
		if(isset($scan)) {
			$D009 = mysql_query("SELECT * FROM D009
			LEFT JOIN D049 ON D009_D049_Id = D049_Id
			LEFT JOIN D001 ON D049_D001_Id = D001_Id 
			LEFT JOIN D082 ON D049_D082_Id = D082_Id
			LEFT JOIN D041 ON D041_D049_Id = D049_Id
			WHERE (D001_Codigo_Barras = '{$scan}' OR D001_Codigo_Produto = '{$scan}')
			AND D009_C004_Id = '1'");
			if(mysql_num_rows($D009) > 0){
				$row = mysql_fetch_array($D009);
				//VERIFICA SE O PRODUTO JA ESTA INSERIDO NO ORÇAMENTO
				$checkT004 = mysql_query("SELECT * FROM T004 WHERE T004_T003_Id = '{$T003_Id}' AND T004_D009_Id = '{$row['D009_Id']}'");				 
				if(mysql_num_rows($checkT004) > 0){
					$T004 = mysql_fetch_array($checkT004);
					if(isset($_REQUEST['diminuir'])){
						$quantidade = $T004['T004_Quantidade'] - 1;
						if($T004['T004_Quantidade'] > 0){
							mysql_query("UPDATE T004 SET T004_Quantidade = '{$quantidade}' WHERE T004_Id = '{$T004['T004_Id']}'");
						}
					} else {
						$quantidade = $T004['T004_Quantidade'] + 1;
						mysql_query("UPDATE T004 SET T004_Quantidade = '{$quantidade}' WHERE T004_Id = '{$T004['T004_Id']}'");
					}
				} else {
					incluirProduto($row, $T003_Id);
					$quantidade = 1;					
				}
				$resposta['data'] = <<<EOT
					<div class="alerta" style="margin-bottom: 3px; margin-left: 9px; width: 92.3%;"><strong style="font-size: 100%;">LEIA NOVAMENTE PARA<br /> AUMENTAR A QUANTIDADE</strong><br /></div>			
					<div class="produto" style="font-size: 120%; margin-left: 9px;">
						<strong style="font-size: 120%;">{$row['D001_Codigo_Produto']}</strong><br />
						<strong style="font-size: 120%;">{$row['D001_Descricao_Produto']}</strong><br />
						<strong style="font-size: 120%;">{$row['D082_Marca']}</strong><br />
						<!--<strong style="font-size: 120%;">Estoque: {$row['D009_Quantidade_Estoque']}</strong><br />-->
						<strong style="font-size: 120%;">Quantidade: {$quantidade}</strong><br />
					</div>
						<div style="margin-left: 6px;">
							<button class="voltar" style="width: 46.5%;" id="diminuir">Diminuir</button>
							<button class="voltar" style="width: 46.5%;" id="remover">Remover do<br /> Orçamento</button>
						</div>
						<button class="voltarLarge"  style="margin-left: 9px;" id="voltar">Voltar</button>							
					
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
						$('#scan').unbind('keypress');
						$('#scan').bind('keypress', function(e) {
							if (e.keyCode == 13) {
								//if($('#scan').val() == '{$scan}'){
						    		modal('Verificando...');
							    	$.getJSON('./orcamento.php?opcao=incluirProduto&scan=' + encodeURIComponent($('#scan').val()) + '&T003_Id={$T003_Id}&callback=?', function(request) {
							    		modalRemove();
							    		$('#scan').val('');
										$('#contentMenu').empty().html(request.data);
									});
								//} else {
								//	modal('Produto Incorreto!');
								//	setTimeout(function() {										
						    	//		modalRemove();
						    	//		$('#scan').val('');
								//	}, 1000);						
								//}
							}
						});
						$('#scan').focus(); 
											
    					$('#diminuir').unbind('click').bind('click', function(e) {
							if (confirm('Confirmar?')) {
								modal('Verificando...');
    							$.getJSON('./orcamento.php?opcao=incluirProduto&T003_Id={$T003_Id}&scan={$scan}&diminuir=true&callback=?', function(request) {
    							 	modalRemove();
    							 	$('#scan').val('');
    								$('#contentMenu').empty().html(request.data);
								});
							}
							return false;
	    				});
	    				
	    				$('#remover').unbind('click').bind('click', function(e) {
							if (confirm('Confirmar?')) {
								modal('Verificando...');
    							$.getJSON('./orcamento.php?opcao=orcamento&T003_Id={$T003_Id}&T004_Id={$T004['T004_Id']}&remover=true&callback=?', function(request) {
    							 	modalRemove();
    							 	$('#scan').val('');
    								$('#contentMenu').empty().html(request.data);
								});
							}
							return false;
	    				});

    					$('#voltar').unbind('click').bind('click', function(e) {
							modal('Aguarde...');
    						$.getJSON('./orcamento.php?opcao=orcamento&T003_Id={$T003_Id}&callback=?', function(request) {
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
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						modal('Produto não encontrado');
						setTimeout("reload()",1250);
						function reload(){
							$.getJSON('./orcamento.php?opcao=orcamento&T003_Id={$T003_Id}&callback=?', function(request) {
			    				modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
						}
					} </script></div>
EOT;
			} 
		}
	break;
	
	case 'consulta':
		$T003 = mysql_query("SELECT * FROM T003 LEFT JOIN C007 ON C007_Id = T003_C007_Id_Vendedor_Interno WHERE T003_D024_Id IS NULL AND T003_Flag_Status_Orcamento = 'I'");
		$resposta['data'] .= <<<EOT
			<style>
				.tabela {
					background-color: #F6F6F6; 
					border: 0px solid #8F859C;
					border-top: 1px solid #444;
					margin-top: 3px;
					padding: 1px;
				}
				.lista {
					border-top: 1px solid #8F859C;
					padding: 6px;
					margin-left: 2px;
					text-align: center;			
					align: center;
				}				
			</style>
			<button class="voltarLarge" style="margin-left: 9px;" id="voltar">Voltar</button>
			<table class="tabela" style="width: 98%;" align="center" cellspacing="0" cellpadding="0">	
			<tr>
				<td class="lista">Pedido</td>
				<td class="lista">Data</td>
				<td class="lista">Produtos</td>
				<td class="lista">Vendedor</td>							
			</tr>	
EOT;
		
		while($row = @mysql_fetch_array($T003)){
			$vendedor = substr($row['C007_Primeiro_Nome'], 0, 10);
			$data = gCorrigeData($row['T003_Data_Emissao']);
			$T004 = mysql_query("SELECT COUNT(*) as itens FROM T004 WHERE T004_T003_Id = '{$row['T003_Id']}'");
			$T004 = mysql_fetch_array($T004);
			$valor = gCorrigeNumero($row['T003_Valor_Total']);
			$resposta['data'] .= <<<EOT
				<tr id="{$row['T003_Id']}">
					<td class="lista" style="width: 40px;">{$row['T003_Id']}</td>
					<td class="lista">{$data}</td>
					<td class="lista">{$T004['itens']}</td>
					<td class="lista">{$vendedor}</td>							
				</tr>
EOT;
		}
		$resposta['data'] .= <<<EOT
			</table>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    			$('tr').unbind('click').unbind('click').bind('click', function(e) {
    				var id = $(this).closest('tr').attr('id');
    				$('#barraTopPedido').html('Orçamento: ' + id);
    				if(id){
						modal('Verificando...');					
	    				$.getJSON('./orcamento.php?opcao=orcamento&T003_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
	    					modalRemove();
	    					$('#contentMenu').empty().html(request.data);
						});
					}
    			});
			
    			$('#voltar').unbind('click').bind('click', function(e) {
					modal('Aguarde...');
    				$.getJSON('./orcamento.php?opcao=orcamento&callback=?', function(request) {
    				 	modalRemove();
    				 	$('#scan').val('');
    					$('#contentMenu').empty().html(request.data);
					});
					return false;
    			});
		    } </script></div>			
EOT;
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";