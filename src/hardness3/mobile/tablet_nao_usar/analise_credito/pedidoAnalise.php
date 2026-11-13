<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

$script="";
// Adiciona um novo Pedido
if ($T005_Id == 'new') {
	// cria um novo Pedido vazio
	$sql = <<<EOT
		INSERT INTO 
		T005 
			(T005_D024_Id, T005_D013_Id, T005_D116_Id, T005_D006_Id, T005_Flag_Revenda_Consumidor, T005_Data_Emissao, T005_C007_Id_Vendedor_Interno, T005_D021_Id, T005_Flag_Acp, T005_Forma_Pagamento, T005_Frete, T005_Prazo_Entrega, T005_Validade_Proposta, T005_Observacao, T005_C004_Id) 
		VALUES 
			('', '', '', '', '1', CURDATE(), '{$g['usuarioAtual']}', '', '', '', '', '', '', '', '{$g['empresaAtual']}');
EOT;
	mysql_query($sql);
	$_REQUEST['T005_Id'] = $g['mysqlLastId'];
}

include('inc.conf.php');


mysqli_query("call T005_Gravar_Totalizacao_4('$T005_Id')");
$T005=mysql_query("select * from T005 where T005_Id='$T005_Id'");
$T005=mysql_fetch_array($T005);


include('inc.conf.php');

$totalServicos = gCorrigeNumero($T005['T005_Valor_Total_Servicos']);
$totalProdutos = gCorrigeNumero($T005['T005_Valor_Total_Produtos']);
$totalTrocas = gCorrigeNumero($T005['T005_Valor_Troca']);
$totalGeral = gCorrigeNumero($T005['T005_Valor_Total']);
$totalDescontoManual = gCorrigeNumero($T005['T005_Valor_Desconto_Manual']);
//Calcula em percentual o desconto Manual
$valorTotalAntigo = $T005['T005_Valor_Total'] + $T005['T005_Valor_Desconto_Manual'];
if(!empty($valorTotalAntigo)){
	$descontoPercentual = gCorrigeNumero((($valorTotalAntigo - $T005['T005_Valor_Total']) / $valorTotalAntigo) * 100);   
	$descontoPercentual = ($descontoPercentual == "0,00" ? "" : "({$descontoPercentual}%)");
}
//Valor do campo Rentabilidade
$valorIPV = gCorrigeNumero($T005['T005_IPV']);


require_once("bibliotecas/classes/VEN002.php");
$VEN002 = new VEN002();

/*
$totalServicos = gCorrigeNumero($VEN002->calculaTotalServicos($T005));
$totalProdutos = gCorrigeNumero($T005['T005_Valor_Total_Produtos']);
$totalTrocas = gCorrigeNumero($T005['T005_Valor_Troca']);
$totalGeral = gCorrigeNumero($VEN002->calculaTotalGeral($T005_Id));
*/

// Remover Produto do Pedido
if (isset($_REQUEST['T006_Id'])) {
	$T006_Id = $_REQUEST['T006_Id'];
	$VEN002->excluirProduto($T006_Id);
	$uniqId=uniqid();
	$script .= <<<EOT
	$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );
EOT;
}

// Remover Troca do Pedido
if (isset($_REQUEST['T138_Id'])) {
	$T138_Id = mysql_real_escape_string($_REQUEST['T138_Id']);
	$sql = "DELETE FROM T138 WHERE T138_Id = '$T138_Id';";
	mysql_query($sql);
}

// Remover Serviço do Pedido
if (isset($_REQUEST['T147_Id'])) {
	$uniqId = uniqid();
	$T147_Id = $_REQUEST['T147_Id'];
	$sql = "DELETE FROM T147 WHERE T147_Id = '{$T147_Id}';";
	mysql_query($sql);
	$uniqId=uniqid();
	$script .= <<<EOT
	$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );
EOT;
}

// Incluir/Alterar Desconto Manual
if (isset($_REQUEST['alterarDescontoManual'])) {
	if (isset($_REQUEST['T005_Valor_Desconto_Manual'])) {
		$T005_Valor_Desconto_Manual = gCorrigeNumeroInverte($_REQUEST['T005_Valor_Desconto_Manual']);
		$sql = "UPDATE T005 SET T005_Valor_Desconto_Manual='{$T005_Valor_Desconto_Manual}' WHERE T005_Id = '{$T005_Id}'";
		mysql_query($sql);
		$script .= <<<EOT
		$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&ajax=true', { transition: 'fade'} );
EOT;
	}
	if (isset($_REQUEST['Valor_Percentual'])) {
		$percentualDigitado = gCorrigeNumeroInverte($_REQUEST['Valor_Percentual']);
		$desconto = $percentualDigitado/100;
		//T005_Valor_Total é o valor inicial do Pedido
		$valor = $T005['T005_Valor_Total'] + $T005['T005_Valor_Desconto_Manual'];
		$valor_descontado = $valor * $desconto;
		
		$sql = "UPDATE T005 SET T005_Valor_Desconto_Manual='{$valor_descontado}' WHERE T005_Id = '{$T005_Id}'";
		mysql_query($sql);
		$script .= <<<EOT
		$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&ajax=true', { transition: 'fade'} );
EOT;
	}
	if (isset($_REQUEST['Valor_Total'])) {
		$valorTotalDigitado = gCorrigeNumeroInverte($_REQUEST['Valor_Total']);
		$desconto = $valorTotalDigitado;
		$valor = $T005['T005_Valor_Total'] + $T005['T005_Valor_Desconto_Manual'];
		$valor_descontado = $valor - $desconto;
		$sql = "UPDATE T005 SET T005_Valor_Desconto_Manual='{$valor_descontado}' WHERE T005_Id = '{$T005_Id}'";
		mysql_query($sql);
		$script .= <<<EOT
		$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&ajax=true', { transition: 'fade'} );
EOT;
	}
include('inc.conf.php');
}
$monta = $VEN002->montarEmailPdf($T005_Id);

//Verifica alçadas e define o Botão Imprimir e enviar e-mail.
$sqlValorParcelas=mysql_query("SELECT SUM(T090_Valor_Parcela) as Valor_Total FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
$rowValorParcelas=mysql_fetch_array($sqlValorParcelas);
$diferenca = abs($T005['T005_Valor_Total'] - $rowValorParcelas['Valor_Total']);
if(($diferenca > '0.03') && (!empty($rowValorParcelas['Valor_Total']))){
	$botaoImprimir = <<<EOT
	<a data-role="button" data-inline="true" data-transition="fade" onclick="alert('Valor total das parcelas geradas não está de acordo com o valor total do Pedido. Favor gerar parcelas novamente.')" data-ajax="false" rel="external" target="_blank">
		Imprimir
	</a>
EOT;
	$botaoEmail = <<<EOT
	<a data-inline="true" data-role="button" data-transition="fade"  href="./pedidoEnviarEmail.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}" data-ajax="false">
		E-mail
	</a>
EOT;
}else{
	$retorno = $VEN002->verificaAlcada($T005_Id,true);
	
	$ret=mysql_query("SELECT AVG(T090_Prazos_Dias) as Prazo_Medio, COUNT(T090_Id) as Total_Parcelas FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
	$T090=mysql_fetch_array($ret);				
	$prazoMedio=$T090['Prazo_Medio'];
	$totalParcelas=$T090['Total_Parcelas'];
	$descontopedido = ($T005['T005_Valor_Desconto_Manual']*100)/($T005['T005_Valor_Desconto_Manual']+$T005['T005_Valor_Total']);
	
	$tipoAlcada = $g['conf']->ini('usarAlcadaTipo');
	if($tipoAlcada == '1'){
		//Calcula Desconto por alçada
		$descontoVendedor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
		$descontoSupervisor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));
		//Calcula prazo médio por alçada
		$prazoMedioAlcadaVendedor = $VEN002->calculaPrazoMedioAlcadaVendedor($T005_Id,true);
		$prazoMedioAlcadaSupervisor = $VEN002->calculaPrazoMedioAlcadaSupervisor($T005_Id,true);
  		//Verificações de alçada
  		$alcadaNome = "";
		if(($descontopedido <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor)){
			$alcadaUpdate = "V";
			if(($retorno !== true) && ($retorno !== false)){
				$alcadaNome = "(VEN)";
			}
		} 
		elseif(($descontopedido <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)){
			$alcadaUpdate = "S";
			if(($retorno !== true) && ($retorno !== false)){
				$alcadaNome = "(SUP)";
			}
		}
		else{
			 $alcadaUpdate = "D";
			 if(($retorno !== true) && ($retorno !== false)){
				$alcadaNome = "(DIR)";
			}
		}
	}elseif($tipoAlcada == '2'){
		
		$regrasUsuario=$g['conf']->ini('regrasVendedorPorParcelas');		
		$descontoMaximoAlcada = 0;			
		foreach ($regrasUsuario as $regra){
			if ($prazoMedio<=$regra['pm'] && $numeroParcelas<=$regra['parcelas']){
				$descontoMaximoAlcada=$regra['desconto'];				
				break;
			}
		}			
		$descontoMaximoAlcadaVendedor=$descontoMaximoAlcada; 
		
		
		$regrasUsuario=$g['conf']->ini('regrasSupervisorPorParcelas');			
		$descontoMaximoAlcada = 0;			
		foreach ($regrasUsuario as $regra){
			if ($prazoMedio<=$regra['pm'] && $numeroParcelas<=$regra['parcelas']){
				$descontoMaximoAlcada=$regra['desconto'];				
				break;
			}
		}			
		$descontoMaximoAlcadaSupervisor=$descontoMaximoAlcada; 
		
		$descontopedido = number_format($descontopedido,2); 
		$descontoMaximoAlcadaVendedor = gCorrigeNumeroInverte($descontoMaximoAlcadaVendedor);
		$descontoMaximoAlcadaSupervisor = gCorrigeNumeroInverte($descontoMaximoAlcadaSupervisor);  
		
		if ($descontopedido  <= $descontoMaximoAlcadaVendedor){
			$alcadaUpdate = "V";
			if(($retorno !== true) && ($retorno !== false)){ 
				$alcadaNome = "(VEN)";
			}
		}
		elseif($descontopedido  <= $descontoMaximoAlcadaSupervisor){
			$alcadaUpdate = "S"; 
			if(($retorno !== true) && ($retorno !== false)){
				$alcadaNome = "(SUP)";
			}
		}
		else{
			 $alcadaUpdate = "D";
			 if(($retorno !== true) && ($retorno !== false)){
			 	$alcadaNome = "(DIR)";			
			 }
		}				
			
			
			
	}
	
	if ($retorno === true) {
		if((!empty($rowValorParcelas['Valor_Total'])) || ($rowValorParcelas['Valor_Total'] <= 0 && $T005['T005_Valor_Total'] <= 0)){
			//verificar o que está errado, pois não está fazendo o update
			$tagButtonImprimir = "onclick=\"abrirPDF('{$T005_Id}')\"  href=\"{$monta['AnexosWeb'][0]}\"";
			//$tagButtonImprimir = "href=\"{$monta['AnexosWeb'][0]}\"";
			$tagButtonEmail="href=\"./pedidoEnviarEmail.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}\"";
		}else{
			$tagButtonImprimir = "onclick=\"alert('Ainda não foram geradas parcelas para este Pedido. Favor definir forma de pagamento!')\"";
			$tagButtonEmail= "onclick=\"alert('Ainda não foram geradas parcelas para este Pedido. Favor definir forma de pagamento!')\"";
		}
	} elseif($retorno === false) {
		$tagButtonImprimir = "onclick=\"alert('Você não tem permissão para gerar Pedidos!')\"";
		$tagButtonEmail= "onclick=\"alert('Você não tem permissão para gerar Pedidos!')\"";;
	} else {
		if((!empty($rowValorParcelas['Valor_Total'])) || ($rowValorParcelas['Valor_Total'] <= 0 && $T005['T005_Valor_Total'] <= 0)){
			$tagButtonImprimir = "onclick=\"\$.mobile.changePage( './pedidoPedirLiberacao.php?T005_Id={$T005_Id}&alcadaUpdate={$alcadaUpdate}', { transition: 'fade'} );\"";
			$tagButtonEmail= "onclick=\"\$.mobile.changePage( './pedidoPedirLiberacao.php?T005_Id={$T005_Id}&alcadaUpdate={$alcadaUpdate}', { transition: 'fade'} );\"";
		}else{
			$tagButtonImprimir = "onclick=\"alert('Ainda não foram geradas parcelas para este Pedido. Favor definir forma de pagamento!')\"";
			$tagButtonEmail= "onclick=\"alert('Ainda não foram geradas parcelas para este Pedido. Favor definir forma de pagamento!')\"";
		}
	}
	
	/*$botaoImprimir = <<<EOT
	<a data-role="button" data-inline="true" data-transition="fade" {$tagButtonImprimir} data-ajax="false" rel="external" target="_blank">
		Imprimir
	</a>
EOT;
	$botaoEmail = <<<EOT
	<a data-inline="true" data-role="button" data-transition="fade" {$tagButtonEmail} data-ajax="false">
		E-mail
	</a>
EOT;*/
}

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedido">
EOT;
$etapa = 2;
include('inc.header.bar.php');

//Tamanho do Campo do Cabeçalho de Pedidos
if($C007['C007_Flag_Vendedor'] == 'D'){
	$tamanhoCampo = '16';
}else{
	$tamanhoCampo = '20';
}
echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-d" style="margin: 10px 0 5px 0; border: 1px solid #CCCCCC; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3); padding: 8px 0;">
                    <div class="ui-block-a" style="border-right: 2px solid #CCCCCC; text-align: center; width:{$tamanhoCampo}%;">
                        <h4 style="margin: 0px;">
                            Produtos
                        </h4>
                        <h2 style="margin: 0px;">
                            R$ {$totalProdutos}
                        </h2>
                    </div>
                    <div class="ui-block-b" style="border-right: 2px solid #CCCCCC; text-align: center; width:{$tamanhoCampo}%;">
                        <h4 style="margin: 0px;">
                            Serviços
                        </h4>
                        <h2 style="margin: 0px;">
                            R$ {$totalServicos}
                        </h2>
                    </div>
                    <div class="ui-block-c" style="border-right: 2px solid #CCCCCC; text-align: center; width:{$tamanhoCampo}%;">
                        <h4 style="margin: 0px;">
                            Trocas
                        </h4>
                        <h2 style="margin: 0px;">
                            R$ {$totalTrocas}
                        </h2>
                    </div>
                    <div class="ui-block-d" style="border-right: 2px solid #CCCCCC; text-align: center; width:19%;">
                        <h4 style="margin: 0px;">
                            Desconto
                        </h4>
                        <h2 style="margin: 0px; text-decoration:underline;" data-ajax="false">
                            R$ {$totalDescontoManual} <font style="font-size:16px;font-weight:normal;">{$descontoPercentual}</font>
                        </h2>
                        
                    </div>
                    <div class="ui-block-e" style="text-align: center; width:{$tamanhoCampo}%;">
                        <h4 style="margin: 0px;">
                            Total
                        </h4>
                        <h2 style="margin: 0px;">
                            R$ {$totalGeral} <font style="font-size:16px;font-weight:normal;">{$alcadaNome}</font>
                        </h2>
                    </div>
EOT;
if($C007['C007_Flag_Vendedor'] == 'D'){
         		echo <<<EOT
                    <div class="ui-block-e" style="border-left: 2px solid #CCCCCC; text-align: center; width:{$tamanhoCampo}%;"">
                        <h4 style="margin: 0px;">
                            Rentabilidade
                        </h4>
                        <h2 style="margin: 0px;">
                            {$valorIPV}
                        </h2>
                    </div>
EOT;
}
echo <<<EOT
           
                </div>
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div class="ui-block-a">
                		<div style="float: left;">
	                       <!-- {$botaoImprimir}
	                        {$botaoEmail}-->
EOT;
if($C007['C007_Flag_Vendedor'] == 'D'){
	           echo <<<EOT
	           				<a data-role="button" data-inline="true" onclick="\$.mobile.changePage( './analiseLiberarPedido.php?T005_Id={$T005_Id}', { transition: 'slide' });">
                           		 <font color="green">Aprovar</font>
                        	</a>
	                        <a data-role="button" data-inline="true" data-transition="fade" href="./analisePedidoAprovacao.php?T005_Id={$T005_Id}" >
								<font color="red">Negar</font>
	                        </a>
	                        
EOT;
}
				echo <<<EOT
							
                    	</div>
                	</div>
                   <!-- <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarProduto" onclick="$.mobile.changePage( './pedidoProcurarProduto.php?T005_Id={$T005_Id}&pesquisa=' + encodeURIComponent($('#procurarProduto').val()), { transition: 'slide'} );" data-ajax="false">
			            		Adicionar
				            </a>
				        </div>
                    	<div style="width: 70%; float:right; margin-top: 14px;">
                    		<input placeholder="Procurar Produto" data-type="search" class="ui-input-text ui-body-c" id="procurarProduto">
                    	</div>
                    </div>-->
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 0%";></td>
	    						<td style="width: 6%";>Código</td>
	    						<td style="width: 50%";>Produto venda</td>
	    						<td style="width: 10%";>Marca</td>
	    						<td style="width: 10%"; align=right>Quantidade</td>
	    						<td style="width: 15%"; align=right>Preço R$</td>
	    						<td style="width: 15%"; align=right>Total R$</td>
	    					</tr>
	    				</table>
                    </li>
EOT;

// Produtos
$sql = <<<EOT
	SELECT 
		T006_Id, T006_Codigo_Produto, T006_Descricao_Produto, D082_Marca, T006_Quantidade, T006_Valor_Preco_Unitario, T006_Valor_Total_Preco, D009_Id, D001_Id,D001_Flag_Pre_Cadastro
	FROM T006
		LEFT JOIN D009 on D009_Id=T006_D009_Id LEFT JOIN D049 on D049_Id=D009_D049_Id 
		LEFT JOIN D001 on D001_Id=D049_D001_Id LEFT JOIN D082 on D082_Id=D049_D082_Id 
		LEFT JOIN D037 on D037_Id=T006_D037_Id LEFT JOIN D005 on D005_Id=T006_D005_Id 
		LEFT JOIN D006 on D006_Id=T006_D006_Id
	WHERE 
		T006_T005_Id = '{$T005_Id}';
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$sql = "SELECT * FROM T172 WHERE (T172_D001_Id = '{$row['D001_Id']}') LIMIT 0,1;";
	$res2 = mysql_query($sql);
	if (mysql_numrows($res2) > 0) {
		$row2 = mysql_fetch_assoc($res2);
		$idFoto = $row2['T172_Id'];
		$idProduto = $row['D001_Id'];
		$pathinfo = pathinfo($row2['T172_Nome_Arquivo']);
		$cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
		$cArquivoWeb = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$image = "<img src='{$cArquivoWeb}' style='width: 100px; height: 100px;' />";
		}
	} else {
		$image = '';
	}
	$row['T006_Quantidade'] = number_format($row['T006_Quantidade'], 0);
	$row['T006_Valor_Preco_Unitario'] = gCorrigeNumero($row['T006_Valor_Preco_Unitario']);
	$row['T006_Valor_Total_Preco'] = gCorrigeNumero($row['T006_Valor_Total_Preco']);
	$row['T006_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['T006_Descricao_Produto']} (PRÉ)" : $row['T006_Descricao_Produto']);
	//Verifica se o Item já foi cotado
	$sql = "SELECT T189_Id FROM t006 LEFT JOIN T189 ON t189_T006_Id = T006_Id WHERE t006_Id='{$row['T006_Id']}' AND T189_Id IS NOT NULL";
	$verificaCotacao = mysql_query($sql);
	if(mysql_num_rows($verificaCotacao)>0){
		$row['T006_Descricao_Produto'] = $row['T006_Descricao_Produto'] . "<font color='blue'> - COMP</font>";
	}
	
	echo <<<EOT
		<li data-theme="c" data-icon="false">
	    <a href="#" data-ajax="false" data-transition="slide">
	    	<table style="width: 100%";>
	    		<tr>
	    			<td style="width: 0%";>{$image}</td>
	    			<td style="width: 6%";>{$row['T006_Codigo_Produto']}</td>
	    			<td style="width: 50%";>{$row['T006_Descricao_Produto']}</td>
	    			<td style="width: 10%";>{$row['D082_Marca']}</td>
	    			<td style="width: 10%"; align=right>{$row['T006_Quantidade']}</td>
	    			<td style="width: 15%"; align=right>{$row['T006_Valor_Preco_Unitario']}</td>
	    			<td style="width: 15%"; align=right>{$row['T006_Valor_Total_Preco']}</td>
	    		</tr>
	    	</table>
	    </a>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<!--<div class="ui-block-a">
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarServico" onclick="$.mobile.changePage( './pedidoProcurarServico.php?T005_Id={$T005_Id}&pesquisa=' + encodeURIComponent($('#procurarServico').val()), { transition: 'slide'} );">
			            		Adicionar
							</a>
				        </div>
                    	<div style="width: 70%; float:right; margin-top: 14px;">
                    		<input placeholder="Procurar Serviço" data-type="search" class="ui-input-text ui-body-c" id="procurarServico">
                    	</div>
                    </div>-->
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 6%"; align="center">Código</td>
	    						<td style="width: 44%";>Serviço</td>
	    						<td style="width: 40%";>Produto</td>
	    						<td style="width: 10%"; align="right">Preço R$</td>
	    					</tr>
	    				</table>
                    </li>
EOT;

// Serviços
$sql = <<<EOT
	SELECT 
		T147_Id, T147_Valor_Unitario, T147_Titulo, T147_Descricao, D001_Descricao_Produto, T147_Tempo_Estimado, T147_D110_Id, T147_D112_Id, T147_D116_Id 
	FROM T147 
	LEFT JOIN T006 ON T006_Id=T147_T006_Id 
	LEFT JOIN D009 ON D009_Id=T006_D009_Id 
	LEFT JOIN D049 ON D049_Id=D009_D049_Id
	LEFT JOIN D001 ON D001_Id=D049_D001_Id 
	WHERE T147_T005_Id='{$T005_Id}'
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$row['T147_Valor_Unitario'] = gCorrigeNumero($row['T147_Valor_Unitario']);
	$row['T147_Tempo_Estimado'] = gCorrigeTempo($row['T147_Tempo_Estimado']);
	$row['D001_Descricao_Produto'] = empty($row['D001_Descricao_Produto']) ? '-' : "Produto ({$row['D001_Descricao_Produto']})";
	echo <<<EOT
		<li data-theme="c" data-icon="false">
	    <a href="#" data-transition="slide">
	    	<table style="width: 100%";>
	    		<tr>
	    			<td style="width: 6%"; align="center">{$row['T147_Id']}</td>
	    			<td style="width: 44%";>{$row['T147_Titulo']} {$row['T147_Descricao']}</td>
	    			<td style="width: 40%";>{$row['D001_Descricao_Produto']}</td>
	    			<td style="width: 10%"; align="right">{$row['T147_Valor_Unitario']}</td>
	    		</tr>
	    	</table>
	    </a>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<!--<div class="ui-block-a">
                		<div style="float: left;">
                    	</div>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarProdutoTroca" onclick="$.mobile.changePage( './pedidoProcurarProduto.php?T005_Id={$T005_Id}&tipo=troca&pesquisa=' + encodeURIComponent($('#procurarProdutoTroca').val()), { transition: 'slide'} );" data-ajax="false">
			            		Adicionar
							</a>
				        </div>
                    	<div style="width: 70%; float:right; margin-top: 14px;">
                    		<input placeholder="Procurar produto para troca" data-type="search" class="ui-input-text ui-body-c" id="procurarProdutoTroca">
                    	</div>
                    </div>
                </div>-->
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    	<table style="width: 100%";>
	    		<tr>
	    			<td style="width: 6%"; align="center">Código</td>
	    			<td style="width: 50%";>Produto troca</td>
	    			<td style="width: 10%";>Marca</td>
	    			<td style="width: 10%"; align="right">Quantidade</td>
	    			<td style="width: 15%"; align="right">Preço R$</td>
	    			<td style="width: 15%"; align="right">Total R$</td>
	    		</tr>
	    	</table>
                    </li>
EOT;

// Trocas
$sql = <<<EOT
	SELECT 
		D001_Codigo_Produto,D001_Flag_Pre_Cadastro, D001_Descricao_Produto, D082_Marca, T138_Id, T138_Quantidade, T138_Valor_Preco_Unitario, T138_Operacao, T138_Observacao, D001_Id, D009_Id
	FROM T138 LEFT JOIN D009 on D009_Id=T138_D009_Id LEFT JOIN D049 on D049_Id=D009_D049_Id LEFT JOIN D001 on D001_Id=D049_D001_Id LEFT JOIN D082 on D082_Id=D049_D082_Id 
	WHERE T138_T005_Id = '{$T005_Id}'
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$row['T138_Quantidade'] = gCorrigeNumero($row['T138_Quantidade']);
	$row['T138_Valor_Preco_Unitario'] = gCorrigeNumero($row['T138_Valor_Preco_Unitario']);
	$total = gCorrigeNumero($row['T138_Quantidade'] * $row['T138_Valor_Preco_Unitario']);
	$row['D001_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['D001_Descricao_Produto']} (PRÉ)" : $row['D001_Descricao_Produto']);
	
	echo <<<EOT
		<li data-theme="c" data-icon="false">
	    <a href="#" data-ajax="false" data-transition="slide">
	    	<table style="width: 100%";>
	    		<tr>
	    			<td style="width: 6%"; align="center">{$row['D001_Codigo_Produto']}</td>
	    			<td style="width: 50%";>{$row['D001_Descricao_Produto']}</td>
	    			<td style="width: 10%";>{$row['D082_Marca']}</td>
	    			<td style="width: 10%"; align="right">{$row['T138_Quantidade']}</td>
	    			<td style="width: 15%"; align="right">{$row['T138_Valor_Preco_Unitario']}</td>
	    			<td style="width: 15%"; align="right">{$total}</td>
	    		</tr>
	    	</table>
	    </a>
	    </li>
EOT;
}

$sqlAnalista = "SELECT C007_Id,C007_Primeiro_Nome FROM C007 WHERE C007_Id={$T005['T005_Liberou_Alcada_C007_Id']}";
$resAnalista = mysql_query($sqlAnalista);
$rowAnalista = mysql_fetch_array($resAnalista);

echo <<<EOT
                </ul>
EOT;
if (!empty($rowAnalista['C007_Id'])) {
			echo <<<EOT
			    
	                <div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c" style="width:600px;margin-top:70px;">
						<h3>Análise</h3>
							<div class="ui-grid-a">
								<div data-role="fieldcontain">
									<label for="C007_Primeiro_Nome" style="width: 15%;">
					                    Analista
						            </label>
									<input id="C007_Primeiro_Nome" name="C007_Primeiro_Nome" placeholder="" value="{$rowAnalista['C007_Primeiro_Nome']}" type="text"  style="width: 80%;"/>
								</div>
								<div data-role="fieldcontain">
									<label for="T005_Flag_Status_pedido" style="width: 15%;">
					                    Status
						            </label>
									<input id="T005_Flag_Status_pedido" name="T005_Flag_Status_pedido" placeholder="" value="{$statuspedido}" type="text"  style="width: 80%;"/>
								</div>
								<div data-role="fieldcontain">
									<label for="T005_Liberou_Alcada_Observacao_Nao_Liberado" style="width: 15%;">
					                    Observação
						            </label>
									<textarea name="T005_Liberou_Alcada_Observacao_Nao_Liberado" id="T005_Liberou_Alcada_Observacao_Nao_Liberado" style="width: 80%;">{$T005['T005_Liberou_Alcada_Observacao_Nao_Liberado']}</textarea>
								</div> 
								
								
EOT;
}
		echo <<<EOT
			</div>
EOT;

include('/mobile/tablet/inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	function abrirPDF(T005_Id) {
			$.getJSON('./tablet_func-ajax.php?opcaoAcao=updateT005_Flag_PDF_Impresso&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
    			//
			});	
		}
    	$( document ).undelegate("#pagepedido", "pageinit");
    	$( document ).delegate("#pagepedido", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
