<?php
namespace hardness;

include('conf.php');
//print_r($_SESSION);

require_once('bibliotecas/classes/EST001.php');
$EST001 = new EST001;

$scan = $_REQUEST['scan'];

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	
	case 'cabecalho':
		$sqlT029 = <<<EOT
		INSERT INTO T029 (T029_C004_Id,
						  T029_C007_Id,
						  T029_Data,
						  T029_Descricao,
						  T029_Flag_Ok)
						  VALUES
						  ('{$g['empresaAtual']}',
						  '{$g['usuarioAtual']}',
						  CURDATE(),
						  'D.E. COLETOR',
						  'N');
EOT;
		$resultT029 = mysql_query($sqlT029);
		if($resultT029){
			$T029_Id = $g['mysqlLastId'];
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				modal('Carregando...');
		    	$.getJSON('./balanco.php?opcao=lerLocacao&T029_Id=' + encodeURIComponent('{$T029_Id}') + '&callback=?', function(request) {
		    		modalRemove();
		    		$('#contentMenu').empty().html(request.data);
				});
			}</script></div>
EOT;
		} else {
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				alert("Erro ao inserir a D.E.");
				window.location = "./index.php";
			}</script></div>
EOT;
		}
		
	break;
	
    case 'lerLocacao':
		$T029_Id = $_REQUEST['T029_Id'];

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
							$.getJSON('./balanco.php?opcao=produto&T029_Id=$T029_Id&D004_Id=$D004_Id&callback=?', function(request) {
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
	    				$.getJSON('./balanco.php?opcao=lerLocacao&callback=?', function(request) {
	    					modalRemove();
	    					// Proximo Produto da lista
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>					
EOT;
				}

			} else {
				$sql = <<<EOT
					SELECT T029_Id
					  FROM T029
					 WHERE T029_Id='{$T029_Id}'
EOT;
				$res = mysql_query($sql);
				$row = mysql_fetch_array($res);

				$resposta['data'] .= <<<EOT
				<p align=center>
				DE:<br /><strong>{$row['T029_Id']}</strong><br />
				</p>
				<p align=center>
				Local:<br /><strong>{$row['D004_Local']}</strong><br />
				</p>
				<div class="alerta" style="margin-top: 45%;"><strong style="font-size: 140%;"><br>LOCAÇÃO NÃO ENCONTRADA<br> </br></strong></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					modal('LOCAÇÃO NÃO ENCONTRADA');	
					setTimeout("reload()",2000);
					function reload(){
						modalRemove();
						$.getJSON('./balanco.php?opcao=lerLocacao&T029_Id='+{$T029_Id}+'&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}
				}</script></div>
EOT;
			}

		} else {
			$sql = <<<EOT
				SELECT T029_Id
				  FROM T029
				 WHERE T029_Id='{$T029_Id}'
EOT;
			$res = mysql_query($sql);
			$row = mysql_fetch_array($res);

			$resposta['data'] .= <<<EOT
			<p align=center>
			DE:<br /><strong>{$row['T029_Id']}</strong><br />
			</p>
			<div class="alerta">
				<strong style="font-size: 140%;"><br />Leia uma locação<br /><br /></strong>
			</div>
			<p>
			<button class="bigBtn" id="finalizar">Finalizar</button>
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
						modal('Verificando...');
						$.getJSON('./balanco.php?opcao=lerLocacao&T029_Id=$T029_Id&D004_Id=$D004_Id&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
						    modalRemove();
						    $('#scan').val('');
							$('#contentMenu').empty().html(request.data);
						});
					}
				});
				$('#scan').focus();
				
				$('#voltar').unbind('click').bind('click', function(e) {
				    modal('Carregando...');
				    $.getJSON('./menu.php?callback=?', function(request) {
				    	modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				});
				$('#finalizar').unbind('click').bind('click', function(e) {
				    modal('Carregando...');
				    $.getJSON('./balanco.php?opcao=finalizar&T029_Id='+{$T029_Id}+'&callback=?', function(request) {
				    	modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				});
			}</script></div>
EOT;
		}
		break;

	case 'produto':
		$T029_Id = $_REQUEST['T029_Id'];
		$D004_Id = $_REQUEST['D004_Id'];
  		if(!isset($scan)){
			$resposta['data'] .= <<<EOT
		    	<p align='center'>
					Divergência de estoque:<strong>{$T029_Id}</strong><br />
				</p>
				<div class="alerta" style="margin-top: 10%">
					<strong style="font-size: 140%;"><br />LEIA O PRODUTO<br> <br /></strong>
				</div>
				<p>
				<button class="btn" id="trocaLocacao">Trocar Locação</button>
				<button class="btn" id="finalizar">Finalizar</button>
EOT;
			$resposta['data'] .= <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
				    	modal('Verificando...');
				    	$.getJSON('./balanco.php?opcao=produto&D004_Id=$D004_Id&scan=' + encodeURIComponent($('#scan').val()) + '&T029_Id=' + encodeURIComponent('{$T029_Id}') + '&callback=?', function(request) {
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
    			
    			$('#finalizar').unbind('click').bind('click', function(e) {
				    modal('Carregando...');
				    $.getJSON('./balanco.php?opcao=finalizar&T029_Id='+{$T029_Id}+'&callback=?', function(request) {
				    	modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				});
				
				$('#trocaLocacao').unbind('click').bind('click', function(e) {
				    modal('Carregando...');
				    $.getJSON('./balanco.php?opcao=lerLocacao&T029_Id='+{$T029_Id}+'&callback=?', function(request) {
				    	modalRemove();
						$('#contentMenu').empty().html(request.data);
					});
				});
			}</script></div>
EOT;
		  }else{
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
			if (mysql_numrows($res) == 0) {
				$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					modalRemove();
					modal('PRODUTO NÃO ENCONTRADO');
					setTimeout("reload()",1000);
					function reload(){
						modalRemove();
			    		$.getJSON('./balanco.php?opcao=produto&D004_Id=$D004_Id&T029_Id=' + encodeURIComponent('{$T029_Id}') + '&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}
				} </script></div>
EOT;
			} else {
				$row = mysql_fetch_assoc($res);
				
				$sqlT066 = "SELECT * FROM T066 WHERE T066_D004_Id='{$D004_Id}' AND T066_D009_Id='{$row['D009_Id']}'";
				$resultT066 = mysql_query($sqlT066);
				if(mysql_num_rows($resultT066) > 0){
					$linhaT066 = mysql_fetch_array($resultT066);
					$resposta['data'] = <<<EOT
				    <div style="align:center;width:100%;text-align:center;">
				    	<table style="margin:0 auto;border:0;padding:0 important;">
				    		<tr>
				    			<td>Código:</td>
				    		</tr>
				    		<tr>
				    			<td><b>{$row['D001_Codigo_Produto']}</b></td>
				    		</tr>
				    		<tr>
				    			<td>Descrição:</td>
				    		</tr>
				    		<tr>
				    			<td><b>{$row['D001_Descricao_Produto']}</b></td>
				    		</tr>
				    		<tr>
				    			<td>Marca:</td>
				    		</tr>
				    		<tr>
				    			<td><b>{$row['D082_Marca']}</b></td>
				    		</tr>
				    		<tr>
				    			<td>Estoque:</td>
				    		</tr>
				    		<tr>
				    			<td><b>{$row['D009_Quantidade_Estoque']}</b></td>
				    		</tr>
				    	</table>
				    	Informe a quantidade:
					</div>
					<input type="text" name="qntEmp" id="qntDivEst" style="width:95%;margin-left:5px;" value="1"/><br />
					<input type="button" class="btn" id="salvar" value="Salvar" style="width:95%;margin-left:5px;"/>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						(function(){
							$('#qntDivEst').focus()
						})();
						$('#salvar').unbind('click').bind('click', function(e) {
				    		//clearTimeout(refreshMenu);
				    		modal('Carregando...');
				    		$.getJSON('./balanco.php?opcao=salvaProd&qntDivEst=' + $('#qntDivEst').val() +
				    		                                             '&D009_Id=' + '{$row['D009_Id']}' +
				    		                                             '&T029_Id=' + '{$T029_Id}' + 
				    		                                             '&D004_Id=' + '{$D004_Id}' + 
				    		                                             '&T066_Id=' + '{$linhaT066['T066_Id']}' +
				    		                                             '&credita=true' + 
				    		                                             '&callback=?', function(request) {
				    			modalRemove();
								$('#contentMenu').empty().html(request.data);
							});
				    	});
						
						$('#qntDivEst').bind('keypress', function(e) {
							if (e.keyCode == 13) {
					    		//clearTimeout(refreshMenu);
					    		modal('Carregando...');
					    		$.getJSON('./balanco.php?opcao=salvaProd&qntDivEst=' + $('#qntDivEst').val() +
					    		                                             '&D009_Id=' + '{$row['D009_Id']}' +
					    		                                             '&T029_Id=' + '{$T029_Id}' + 
					    		                                             '&D004_Id=' + '{$D004_Id}' + 
					    		                                             '&T066_Id=' + '{$linhaT066['T066_Id']}' +
					    		                                             '&credita=true' + 
					    		                                             '&callback=?', function(request) {
					    			modalRemove();
									$('#contentMenu').empty().html(request.data);
								});
							}
						});
				    }</script></div>
					
EOT;
				}else{
					$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					modalRemove();
					modal('PRODUTO NÃO ENCONTRADO NA LOCAÇÂO');
					setTimeout("reload()",1000);
					function reload(){
						modalRemove();
			    		$.getJSON('./balanco.php?opcao=produto&D004_Id=$D004_Id&T029_Id=' + encodeURIComponent('{$T029_Id}') + '&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}
				} </script></div>
EOT;
				}
			}
		}
	break;
	
	case 'salvaProd';
		$T029_Id = $_REQUEST['T029_Id'];
		$D009_Id = $_REQUEST['D009_Id'];
		$D004_Id = $_REQUEST['D004_Id'];
		$T066_Id = $_REQUEST['T066_Id'];
		$qnt = $_REQUEST['qntDivEst'];
		//$debCre = ($_REQUEST['debita'] == true) ? "S" : "E";
		
		// B = Balanço
		$debCre = "B";
		
		$sqlT030 = "SELECT * FROM T030 WHERE T030_T029_Id='{$T029_Id}' AND T030_D009_Id='{$D009_Id}'";
		$resultT030 = mysql_query($sqlT030);
		
		if(mysql_num_rows($resultT030) > 0){
			$linhaT030 = mysql_fetch_array($resultT030);
			$qntTotal = $linhaT030['T030_Quantidade'] + $qnt;
			
			
			$upT030 = "UPDATE T030 SET T030_Quantidade='{$qntTotal}', T030_Flag_Entrada_Saida='{$debCre}' WHERE T030_Id='{$linhaT030['T030_Id']}'";
			$resultT030 = mysql_query($upT030);
			
			if($resultT030){
				$resposta['data'] = <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$.getJSON('./balanco.php?opcao=produto&D004_Id=$D004_Id&T029_Id=' + '{$T029_Id}' +'&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>		
EOT;
			} else {
				$resposta['data'] = <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						alert("Houve algum erro ao cadastrar o produto. Por favor, tente novamente.");
						$.getJSON('./balanco.php?opcao=produto&D004_Id=$D004_Id&T029_Id=' + '{$T029_Id}' +'&callback=?', function(request) {
							$('#contentMenu').empty().html(request.data);
						});
					}</script></div>		
EOT;
			}
			
		}else{
			$sqlT030 = <<<EOT
				INSERT INTO T030 (T030_D009_Id,
								  T030_T029_Id,
								  T030_C004_Id,
								  T030_C007_Id,
								  T030_Quantidade,
								  T030_Flag_Entrada_Saida,
								  T030_Data_Inclusao,
								  T030_D004_Id)
							  VALUES
							  	('{$D009_Id}',
							  	 '{$T029_Id}',
							  	 '{$g['empresaAtual']}',
							  	 '{$g['usuarioAtual']}',
							  	 '{$qnt}',
							  	 '{$debCre}',
							  	 CURRENT_TIMESTAMP(),
							  	 '{$D004_Id}');
EOT;
				$retorno = $sqlT030;
				//$sqlT029 = "UPDATE T029 SET T029_Flag_Ok='S' WHERE T029_Id='{$T029_Id}'";
				$retorno .= $sqT029;
				//$resultT029 = mysql_query($sqlT029);
				$resultT030 = mysql_query($sqlT030);
				
				
				// inserindo no histórico do produto
				
				if($debCre == "E"){
					$op = "COLETOR - CREDITA";
				}else{
					$op = "COLETOR - DEBITA";
				}
				
				// $sqlT001 = "INSERT INTO T001 (
						// T001_D009_Id,
						// T001_Quantidade, 
						// T001_C004_Id, 
						// T001_Data_Lancamento, 
						// T001_Descricao_Operacao, 
						// T001_Flag_Operacao
						// )VALUES(
						// {$D009_Id},
						// {$qnt},
						// {$g['empresaAtual']},
						// CURDATE(),
						// '{$op}',
						// '{$debCre}'								
					// );";
						// mysql_query($sqlT001);
				// $T001_Id = $g['mysqlLastId'];
				// $retorno .= $sqlT001;
// 				
				// $sqlT186 = "INSERT INTO T186 (
						// T186_T066_Id,
						// T186_T001_Id, 
						// T186_Quantidade
						// )VALUES(
						// '{$T066_Id}',
						// '{$T001_Id}',
						// '{$qnt}'			
					// );";
						// mysql_query($sqlT186);
				// $retorno .= $sqlT186;
				
				if($resultT030){
					$resposta['data'] = <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							alert("Produto cadastrado com sucesso");
							$.getJSON('./balanco.php?opcao=produto&D004_Id=$D004_Id&T029_Id=' + '{$T029_Id}' +'&callback=?', function(request) {
								$('#contentMenu').empty().html(request.data);
							});
						}</script></div>		
EOT;
				} else {
					$resposta['data'] = <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							alert("Houve algum erro ao cadastrar o produto. Por favor, tente novamente.");
							$.getJSON('./balanco.php?opcao=produto&D004_Id=$D004_Id&T029_Id=' + '{$T029_Id}' +'&callback=?', function(request) {
								$('#contentMenu').empty().html(request.data);
							});
						}</script></div>		
EOT;
				}
		}
		
	break;
	
	case 'finalizar':
		$T029_Id=$_REQUEST['T029_Id'];
		$retorno = $EST001->atualizarEstoque($T029_Id);
		
		if($retorno === true){
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert("Estoque atualizado com sucesso.");
					window.location = './menu.php';
				}</script></div>		
EOT;
		} else {
			$resposta['data'] = <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					alert('{$retorno}');
					$.getJSON('./balanco.php?opcao=produto&T029_Id='+{$T029_Id}+'&callback=?', function(request) {
						$('#contentMenu').empty().html(request.data);
					});
				}</script></div>		
EOT;
		}
		break;
	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
