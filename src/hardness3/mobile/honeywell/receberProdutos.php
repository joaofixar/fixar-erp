<?php
namespace hardness;

include('conf.php');
global $g;

$scan = $_REQUEST['scan'];

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'selecionaProd':
		$T013_Id = $_REQUEST['T013_Id'];
		$sqlT014 = <<<EOT
		SELECT * FROM T014
		left join T013 on T013_Id=T014_T013_Id
		left join T012 on T012_Id=T014_T012_Id
		left join D009 on D009_Id=T014_D009_Id
		left join D049 on D049_Id=D009_D049_Id
		left join D001 on D001_Id=D049_D001_Id
		left join D037 on D037_Id=D001_D037_Id
		left join D082 on D082_Id=D049_D082_Id
		left join T001 on T001_T014_Id=T014_Id
		left join D006 on D006_Id=T014_D006_Id
		where T014_T013_Id='$T013_Id' AND (T014_Quantidade_Alocada < T014_Quantidade)
		order by T014_Id LIMIT 1
EOT;
		$resultT014 = mysql_query($sqlT014);
		$linhaT014 = mysql_fetch_array($resultT014);
		$aSeparar = $linhaT014['T014_Quantidade'] - $linhaT014['T014_Quantidade_Alocada'];
		$aSeparar = number_format($aSeparar,3);
		$resposta['data'] .= <<<EOT
		<div style="width:100%;">
			<div style="margin:0 auto;text-align:center;">
				Código:<br /><strong>{$linhaT014['T014_Codigo_Produto']}</strong><br />
				Descrição:<br /><strong>{$linhaT014['T014_Descricao_Produto']}</strong><br />
				Marca:<br /><strong>{$linhaT014['D082_Marca']}</strong><br />
				Quantidade Nota:<br /><strong>Total: {$linhaT014['T014_Quantidade']} - A separar: {$aSeparar}</strong><br />
				Estoque Geral:<br /><strong>{$linhaT014['D009_Quantidade_Estoque']}</strong>
			</div>
		</div>
		<div class="alerta" style="margin-top: 10%">
			<strong style="font-size: 140%;"><br />LEIA A LOCAÇÃO<br> <br /></strong>
		</div>
		<input type="button" class="btn" id="voltar" value="Voltar" style="width:95%;margin-left:5px;"/>
		
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#scan').unbind('keypress');
			$('#scan').bind('keypress', function(e) {
				if (e.keyCode == 13) {
			    	modal('Verificando...');
			    	$.getJSON('./receberProdutos.php?opcao=verificaLocacao&scan=' + encodeURIComponent($('#scan').val()) + 
			    											     '&D009_Id=' + encodeURIComponent('{$linhaT014['D009_Id']}') +
			    											     '&T013_Id=' + encodeURIComponent('{$T013_Id}') +
			    											     '&T014_Id=' + encodeURIComponent('{$linhaT014['T014_Id']}') + 
			    											     '&callback=?', function(request) {
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
	break;
	
	case 'verificaLocacao':
		$D004_Id= (isset($_REQUEST['scan'])) ? $_REQUEST['scan'] : $_REQUEST['D004_Id'];
		$D009_Id=$_REQUEST['D009_Id'];
		$T013_Id=$_REQUEST['T013_Id'];
		$T014_Id=$_REQUEST['T014_Id'];
		
		/**
		 * Consultado se existe uma locação do produto para a locação lida.
		 */
		$sqlT066 = <<<EOT
		SELECT * FROM T066 WHERE T066_D004_Id='{$D004_Id}' AND T066_D009_Id='{$D009_Id}';
EOT;
		// echo $sqlT066;
		// die();
		$resultT066 = mysql_query($sqlT066);
		
		if(mysql_num_rows($resultT066) > 0){
			$linhaT066 = mysql_fetch_array($resultT066);
			/**
			 * CASO EXISTA, IRÁ MOSTAR NOVAMENTE O PRODUTO, PORÉM AGORA 
			 * COM O ESTOQUE LOCAL E INFORMAR A QUANTIDADE A SER GUARDADA
			 */
			$sqlT014 = <<<EOT
				SELECT * FROM T014
				left join T013 on T013_Id=T014_T013_Id
				left join T012 on T012_Id=T014_T012_Id
				left join D009 on D009_Id=T014_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D037 on D037_Id=D001_D037_Id
				left join D082 on D082_Id=D049_D082_Id
				left join T001 on T001_T014_Id=T014_Id
				left join D006 on D006_Id=T014_D006_Id
				where T014_Id='{$T014_Id}' AND (T014_Quantidade_Alocada < T014_Quantidade)
				order by T014_Id LIMIT 1
EOT;
				$resultT014 = mysql_query($sqlT014);
				$linhaT014 = mysql_fetch_array($resultT014);
				$aSeparar = $linhaT014['T014_Quantidade'] - $linhaT014['T014_Quantidade_Alocada'];
				$aSeparar = number_format($aSeparar,3);
				$resposta['data'] .= <<<EOT
				<div style="width:100%;">
					<div style="margin:0 auto; text-align:center">
						Código:<br /><strong>{$linhaT014['T014_Codigo_Produto']}</strong><br />
						Descrição:<br /><strong>{$linhaT014['T014_Descricao_Produto']}</strong><br />
						Marca:<br /><strong>{$linhaT014['D082_Marca']}</strong><br />
						Quantidade Nota:<br /><strong>Total: {$linhaT014['T014_Quantidade']} - A separar: {$aSeparar}</strong><br />
						Estoque Geral:<br /><strong>{$linhaT014['D009_Quantidade_Estoque']}</strong><br />
						Estoque Local:<br /><strong>{$linhaT066['T066_Quantidade_Estoque']}</strong><br />
						Informe a quantidade:
					</div>
				</div>
				<input type="text" name="qntRecebida" id="qntRecebida" style="width:95%;margin-left:5px;"/><br />
				<input type="button" class="btn" id="salvar" value="Salvar" style="width:95%;margin-left:5px;"/>
				<input type="button" class="btn" id="voltar" value="Voltar" style="width:95%;margin-left:5px;"/>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#salvar').unbind('click').bind('click', function(e) {
				    		//clearTimeout(refreshMenu);
				    		modal('Carregando...');
				    		$.getJSON('./receberProdutos.php?opcao=gravarLocacao&qntRecebida=' + $('#qntRecebida').val() +
				    		                                             '&D009_Id=' + '{$D009_Id}' +
				    		                                             '&D004_Id=' + '{$D004_Id}' +
				    		                                             '&T066_Id=' + '{$linhaT066['T066_Id']}' +
				    		                                             '&T014_Id=' + '{$T014_Id}' +    
				    		                                             '&callback=?', function(request) {
				    			modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
				    	});
						$('#voltar').unbind('click').bind('click', function(e) {
				    		//clearTimeout(refreshMenu);
				    		modal('Carregando...');
				    		$.getJSON('./divergenciaEstoque.php?opcao=selecionaProd&T013_Id=' + '{$T013_Id}' + '&callback=?', function(request) {
				    			modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
				    	});
				    }</script></div>
					
EOT;
			
		} else {
			/**
			 * CASO NÃO EXISTA UMA LOCAÇÃO, IRÁ FAZER UMA CONSULTA NA
			 * D004 E VERIFICAR A FLAG SE PERMITE MAIS QUE UM PRODUTO OU NÃO
			 */ 
			 
			 $sqlD004 = "SELECT * FROM D004 WHERE D004_Id='{$D004_Id}'";
			 $resultD004 = mysql_query($sqlD004);
			 $linhaD004 = mysql_fetch_array($resultD004);
			 
			 if(mysql_num_rows($resultD004) > 0){
				 if($linhaD004['D004_Flag_Tipo'] == 2){
				 	
				 	/**
					 * PERMITE MAIS QUE UM PRODUTO
					 * 
					 * Faz uma requisição para solicitar se é para vincular o produto para aquela locação
					 */
				 	$resposta['data'] .= <<<EOT
				 	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				 		modal('Carregando...');
			    		$.getJSON('./receberProdutos.php?opcao=verificaQntProdLocal&D009_Id=' + '{$D009_Id}' +
			    		                                             '&D004_Id=' + '{$D004_Id}' +
			    		                                             '&T066_Id=' + '{$linhaT066['T066_Id']}' +
			    		                                             '&T014_Id=' + '{$T014_Id}' +
			    		                                             '&T013_Id=' + '{$T013_Id}' +    
			    		                                             '&callback=?', function(request) {
			    			modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
			 		}</script></div>
EOT;
				 // NÃO PERMITE MAIS QUE UM PRODUTO
				 } else if($linhaD004['D004_Flag_Tipo'] == 1) {
				 	
				 	$sqlT066 = "SELECT * FROM T066 WHERE T066_D004_Id='$D004_Id'";
					$resultT066 = mysql_query($sqlT066);
					
					// VERIFICANDO SE JÁ EXISTE UM PRODUTO NO LOCAL
					if(mysql_num_rows($resultT066) > 0){
						// Msg erro 
						$resposta['data'] .= <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								alert("Já existe um produto nesse local.");
								$.getJSON('./receberProdutos.php?opcao=selecionaProd&T013_Id=' + encodeURIComponent('{$T013_Id}') + '&callback=?', function(request) {
						    		modalRemove();
						    		$('#scan').val('');
									$('#contentMenu').empty().html(request.data);
								});
							}</script></div>
EOT;
					} else {
						// Permite Vincular um novo produto
						$resposta['data'] .= <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								modal('Carregando...');
						    		$.getJSON('./receberProdutos.php?opcao=verificaQntProdLocal&D009_Id=' + '{$D009_Id}' +
						    		                                             '&D004_Id=' + '{$D004_Id}' +
						    		                                             '&T066_Id=' + '{$linhaT066['T066_Id']}' +
						    		                                             '&T014_Id=' + '{$T014_Id}' +
						    		                                             '&T013_Id=' + '{$T013_Id}' +    
						    		                                             '&callback=?', function(request) {
						    			modalRemove();
										$('#contentMenu').empty().html(request.data);
									});
							}</script></div>
EOT;
					}
				} else {
					// A D004_Flag_Tipo está NULL
					$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						alert("Erro. Por favor contato o suporte técnico (D004_Flag_Tipo = NULL)");
						$.getJSON('./receberProdutos.php?opcao=selecionaProd&T013_Id=' + '{$T013_Id}' +    
			    		                                             '&callback=?', function(request) {
			    			modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>
EOT;
				}
			 } else {
			 	
				// Não existe o local
			 	$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert("Local não encontrado!");
					$.getJSON('./receberProdutos.php?opcao=selecionaProd&T013_Id=' + encodeURIComponent('{$T013_Id}') + '&callback=?', function(request) {
			    		modalRemove();
			    		$('#scan').val('');
						$('#contentMenu').empty().html(request.data);
					});
				}</script></div>
EOT;
			 }
		}
	break;
	
	case 'gravarLocacao':
		$D004_Id=$_REQUEST['D004_Id'];
		$D009_Id=$_REQUEST['D009_Id'];
		$T066_Id=$_REQUEST['T066_Id'];
		$T014_Id=$_REQUEST['T014_Id'];
		$qntRecebida=$_REQUEST['qntRecebida'];
		
		$sqlT014 = "SELECT * FROM T014 WHERE T014_Id='{$T014_Id}'";
		$resultT014 = mysql_query($sqlT014);
		$linhaT014 = mysql_fetch_array($resultT014);
		$qnt = $linhaT014['T014_Quantidade_Alocada'] + $qntRecebida;
		
		// Verifica se a quantidade digitada é maior que a quantidade da nota
		if($qnt > $linhaT014['T014_Quantidade']){
				$resposta['data'] .= <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert("A quantidade digitada e maior que a quantidade disponível na nota!");
					$.getJSON('./receberProdutos.php?opcao=verificaLocacao&D004_Id=' + encodeURIComponent('{$D004_Id}') + 
			    											     '&D009_Id=' + encodeURIComponent('{$D009_Id}') +
			    											     '&T013_Id=' + encodeURIComponent('{$T013_Id}') +
			    											     '&T014_Id=' + encodeURIComponent('{$linhaT014['T014_Id']}') + 
			    											     '&callback=?', function(request) {
			    		modalRemove();
			    		$('#scan').val('');
						$('#contentMenu').empty().html(request.data);
					});
				}</script></div>
EOT;
			} else {
				$sqlT001 = "SELECT * FROM T001 WHERE T001_T014_Id='{$T014_Id}'";
				$resultT001 = mysql_query($sqlT001);
				$linhaT001 = mysql_fetch_array($resultT001);
				$sqlT186 = <<<EOT
				INSERT INTO T186 (T186_T066_Id,
								  T186_T001_Id,
								  T186_Quantidade)
								  VALUES
								  ('{$T066_Id}',
								   '{$linhaT001['T001_Id']}',
								   '{$qntRecebida}');
EOT;
				$resultT186 = mysql_query($sqlT186);
				if($resultT186){
					$upT014 = "UPDATE T014 SET T014_Quantidade_Alocada='{$qnt}' WHERE T014_Id='{$T014_Id}'";
					$resultUpT014 = mysql_query($upT014);
					if($resultUpT014){
						$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							alert("Produto alocado com sucesso!");
							$.getJSON('./menuReceberProdutos.php?callback=?', function(request) {
								$('#contentMenu').empty().html(request.data);
							});
						}</script></div>
EOT;
					}
				} else {
					// ERRO
					$resposta['data'] .= <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						alert("Erro. Por favor, contate o suporte técnico! (insert T186, row:308)");
						$.getJSON('./menuReceberProdutos.php?callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
EOT;
				}
			}
	break;
	
	case 'verificaQntProdLocal':
		$D009_Id=$_REQUEST['D009_Id'];
		$D004_Id=$_REQUEST['D004_Id'];
		$T066_Id=$_REQUEST['T066_Id'];
		$T014_Id=$_REQUEST['T014_Id'];
		$T013_Id=$_REQUEST['T013_Id'];
		
		$sqlD004 = "SELECT D004_Descricao FROM D004 WHERE D004_Id='{$D004_Id}'";
		$resultD004 = mysql_query($sqlD004);
		$linhaD004 = mysql_fetch_array($resultD004);
		
		$sqlT014 = <<<EOT
				SELECT * FROM T014
				left join T013 on T013_Id=T014_T013_Id
				left join T012 on T012_Id=T014_T012_Id
				left join D009 on D009_Id=T014_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D037 on D037_Id=D001_D037_Id
				left join D082 on D082_Id=D049_D082_Id
				left join T001 on T001_T014_Id=T014_Id
				left join D006 on D006_Id=T014_D006_Id
				where T014_Id='{$T014_Id}'
				order by T014_Id LIMIT 1
EOT;
				$resultT014 = mysql_query($sqlT014);
				$linhaT014 = mysql_fetch_array($resultT014);
		
		$resposta['data'] .= <<<EOT
			<div style="width:100%">
				<div style="margin:0 auto; text-align:center;">
					Código:<br /><strong>{$linhaT014['T014_Codigo_Produto']}</strong><br />
					Descrição:<br /><strong>{$linhaT014['T014_Descricao_Produto']}</strong><br />
					Marca:<br /><strong>{$linhaT014['D082_Marca']}</strong><br />
					Quantidade:<br /><strong>{$linhaT014['T014_Quantidade']}</strong><br />
					Estoque Geral:<br /><strong>{$linhaT014['D009_Quantidade_Estoque']}</strong><br />
					Locação:<br /><strong>{$D004_Id}</strong><br />
					Descrição:<br /><strong>{$linhaD004['D004_Descricao']}</strong><br />
					Não existe esse produto nessa locação. Vincular Produto?
				</div>
			</div>
			<input type="button" class="btn" id="vincularSim" value="Sim" style="width:95%;margin-left:5px;"/>
			<input type="button" class="btn" id="vincularNao" value="Não" style="width:95%;margin-left:5px;"/>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#vincularSim').unbind('click').bind('click', function(e) {
		    		//clearTimeout(refreshMenu);
		    		modal('Carregando...');
		    		$.getJSON('./receberProdutos.php?opcao=vincularProduto&D009_Id=' + '{$D009_Id}' +
		    		                                             '&D004_Id=' + '{$D004_Id}' +
		    		                                             '&T066_Id=' + '{$linhaT066['T066_Id']}' +
		    		                                             '&T014_Id=' + '{$linhaT014['T014_Id']}' +    
		    		                                             '&callback=?', function(request) {
		    			modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
		    	});
				$('#vincularNao').unbind('click').bind('click', function(e) {
		    		//clearTimeout(refreshMenu);
		    		modal('Carregando...');
		    		$.getJSON('./receberProdutos.php?opcao=selecionaProd&T013_Id=' + '{$T013_Id}' + '&callback=?', function(request) {
		    			modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
		    	});
			    }</script></div>
EOT;
		
	break;
	
	case 'vincularProduto':
		$D009_Id=$_REQUEST['D009_Id'];
		$D004_Id=$_REQUEST['D004_Id'];
		$T066_Id=$_REQUEST['T066_Id'];
		$T014_Id=$_REQUEST['T014_Id'];
		$T013_Id=$_REQUEST['T013_Id'];
		
		$sqlT066 = <<<EOT
					INSERT INTO T066 (T066_D004_Id,
									  T066_D009_Id)
									  VALUES
									  ('{$D004_Id}',
									   '{$D009_Id}');
EOT;
		$resultT066 = mysql_query($sqlT066);
		if($resultT066){
			// locação inserida, redireciona para (verificaLocacao) informar a quantidade a ser guardada do produto na locação
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$.getJSON('./receberProdutos.php?opcao=verificaLocacao&D009_Id=' + encodeURIComponent('{$D009_Id}') +
	    		                                             '&D004_Id=' + encodeURIComponent('{$D004_Id}') +
	    		                                             '&T014_Id=' + encodeURIComponent('{$T014_Id}') +
	    		                                             '&callback=?', function(request) {
	    			modalRemove();
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
		} else {
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				alert("Erro ao vincular produto, tente novamente.");
				$.getJSON('./menuReceberProdutos.php?callback=?', function(request) {
					$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";