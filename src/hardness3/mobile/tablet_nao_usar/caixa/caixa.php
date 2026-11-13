<?php
namespace hardness;
 
include('conf.php');
include('inc.conf.php');

$script="";

 
$totalServicos = gCorrigeNumero($T005['T005_Valor_Total_Servicos']);
$totalProdutos = gCorrigeNumero($T005['T005_Valor_Total_Produtos']);
$totalTrocas = gCorrigeNumero($T005['T005_Valor_Troca']);
$totalGeral = gCorrigeNumero($T005['T005_Valor_Total']);
$totalDescontoManual = gCorrigeNumero($T005['T005_Valor_Desconto_Manual']);

//Alterar Status servicos Pedido
if (isset($_REQUEST['flagStatus'])) {
        $status = $_REQUEST['flagStatus'];
        $sql = "UPDATE T005 SET T005_Flag_Status_Servicos='{$status}' WHERE T005_Id = '{$T005['T005_Id']}'";
        mysql_query($sql);
        include('inc.conf.php');
        $uniqId = uniqid();
        $script .= <<<EOT
       \$.mobile.changePage( './caixa.php?T005_Id={$T005_Id['T005_Id']}&ajax={$uniqId}', { transition: 'fade'} );
EOT;
}
    //Define select de Status serviços
    $selectStatusServicos = "";
    $selectStatusServicos .= "<option value=\"1\" ".($T005['T005_Flag_Status_Servicos'] == "1" ? 'selected="selected"' : '').">O.S. Pendente</option>";
    $selectStatusServicos .= "<option value=\"2\" ".($T005['T005_Flag_Status_Servicos'] == "2" ? 'selected="selected"' : '').">O.S. Concluída</option>";
    $selectStatusServicos .= "<option value=\"3\" ".($T005['T005_Flag_Status_Servicos'] == "3" ? 'selected="selected"' : '').">O.S. Cancelada</option>";



//Calcula em percentual o desconto Manual
$valorTotalAntigo = $T005['T005_Valor_Total'] + $T005['T005_Valor_Desconto_Manual'];
if(!empty($valorTotalAntigo)){
	$descontoPercentual = gCorrigeNumero((($valorTotalAntigo - $T005['T005_Valor_Total']) / $valorTotalAntigo) * 100);   
	$descontoPercentual = ($descontoPercentual == "0,00" ? "" : "({$descontoPercentual}%)");
}
//Valor do campo Rentabilidade
$valorIPV = gCorrigeNumero($T005['T005_IPV']);

include('mobile/tablet/inc.header.php');

echo <<<EOT
	<div data-role="page" id="pagecaixa">
EOT;
$etapa = 2;
include('mobile/tablet/caixa/inc.header.bar.php');

//Tamanho do Campo do Cabeçalho de Pedidos
if($C007['C007_Flag_Vendedor'] == 'D'){
	$tamanhoCampo = '16';
}else{
	$tamanhoCampo = '20';
}
require_once("bibliotecas/classes/VEN002.php");
require_once("bibliotecas/classes/VEN012.php");
$VEN002 = new VEN002();
$VEN012 = new VEN012();


$monta = $VEN002->montarEmailPdf($T005_Id);
$checkList = $VEN012->montarChecklistPdf($T005_Id);
$ordemServico = $VEN012->montarOrdemServicoPdf($T005_Id);

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
                        <h2 style="margin: 0px;"  data-ajax="false">
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

           /*     </div>

                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div class="ui-block-a" style="width: 52%;">
                		<div style="float: left;">
						    <a data-role="button" data-inline="true" data-transition="fade" onclick="abrirPDF('{$T005_Id}')"  href="{$monta['AnexosWeb'][0]}" data-ajax="false" rel="external" target="_blank">
							    Imprimir
						    </a>                                                                             
						    <a data-role="button" data-inline="true" data-transition="fade" href="{$checkList}" data-ajax="false" rel="external" target="_blank">
							    Check list
						    </a>
						    <a data-role="button" data-inline="true" data-transition="fade" href="{$ordemServico}" data-ajax="false" rel="external" target="_blank">
							    Ordem Serviço
						    </a>
                    	</div>
                	</div>
                    <div class="ui-block-b" style="width: 35%;">
                        <div style="float:left;">
                            <select data-native-menu="true" onchange="gravarStatus(this)" name="T005_Flag_Status_Servicos" id="T005_Flag_Status_Servicos" data-ajax="true">
                                {$selectStatusServicos}
                            </select>
                        </div>
                    </div>
                    <div class="ui-block-c" style="width: 10%;">
                		<div style="float: left;">
						   <a data-ajax="false" data-role="button" data-transition="slide" href="../pedido/pedido.php?T005_Id={$T005_Id}">
							   <font color="green">Alterar</font>
						   </a>
                    	</div>
                	</div>
                </div>*/
echo <<<EOT
			</div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 0%";></td>
	    						<td style="width: 6%";>Código</td>
	    						<td style="width: 50%";>Produtos</td>
	    						<td style="width: 10%";>Marca</td>
	    						<td style="width: 10%"; align=center>Quantidade</td>
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
	$corLinha = '';
	$T207 = mysql_query("SELECT * FROM T207 WHERE T207_T006_Id = '{$row['T006_Id']}' AND T207_Flag_Status = '3'");
	if(mysql_num_rows($T207) > 0){
		$corLinha = 'color: #880088;';
	}
	
	echo <<<EOT
		<li data-theme="c" data-icon="false">
	    	<table style="width: 100%; {$corLinha}">
	    		<tr>
	    			<td style="width: 0%";>{$image}</td>
	    			<td style="width: 6%";>{$row['T006_Codigo_Produto']}</td>
	    			<td style="width: 50%";>{$row['T006_Descricao_Produto']}</td>
	    			<td style="width: 10%";>{$row['D082_Marca']}</td>
	    			<td style="width: 10%"; align=center>{$row['T006_Quantidade']}</td>
	    			<td style="width: 15%"; align=right>{$row['T006_Valor_Preco_Unitario']}</td>
	    			<td style="width: 15%"; align=right>{$row['T006_Valor_Total_Preco']}</td>
	    		</tr>
	    	</table>
	    </li>
EOT;
}


//PRODUTOS CANCELADOS
$T207 = mysql_query("SELECT D001_Codigo_Produto, D001_Id, D001_Descricao_Produto, D082_Marca, count(T207_D009_Id) as qtdeProduto FROM T207 
			LEFT JOIN D009 on D009_Id=T207_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			WHERE T207_T006_Id = '' 
			AND T207_T005_Id = '{$T005_Id}'
			AND T207_Flag_Status = '5'
			GROUP BY T207_D009_Id");
while ($row = mysql_fetch_assoc($T207)) {
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
	echo <<<EOT
		<li data-theme="c" data-icon="false">
	    	<table style="width: 100%; color: red;">
	    		<tr>
	    			<td style="width: 0%";>{$image}</td>
	    			<td style="width: 6%";>{$row['D001_Codigo_Produto']}</td>
	    			<td style="width: 50%";>{$row['D001_Descricao_Produto']}</td>
	    			<td style="width: 10%";>{$row['D082_Marca']}</td>
	    			<td style="width: 10%"; align=center>{$row['qtdeProduto']}</td>
	    			<td style="width: 15%"; align=right>CANCELADO</td>
	    			<td style="width: 15%"; align=right>CANCELADO</td>
	    		</tr>
	    	</table>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 6%"; align="center">Código</td>
	    						<td style="width: 44%";>Serviços</td>
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
	    	<table style="width: 100%";>
	    		<tr>
	    			<td style="width: 6%"; align="center">{$row['T147_Id']}</td>
	    			<td style="width: 44%";>{$row['T147_Titulo']} {$row['T147_Descricao']}</td>
	    			<td style="width: 40%";>{$row['D001_Descricao_Produto']}</td>
	    			<td style="width: 10%"; align="right">{$row['T147_Valor_Unitario']}</td>
	    		</tr>
	    	</table>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    	<table style="width: 100%";>
	    		<tr>
	    			<td style="width: 6%"; align="center">Código</td>
	    			<td style="width: 50%";>Trocas</td>
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
	FROM T138 
	LEFT JOIN D009 on D009_Id=T138_D009_Id 
	LEFT JOIN D049 on D049_Id=D009_D049_Id 
	LEFT JOIN D001 on D001_Id=D049_D001_Id
	LEFT JOIN D082 on D082_Id=D049_D082_Id
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
	    	<table style="width: 100%;">
	    		<tr>
	    			<td style="width: 6%"; align="center">{$row['D001_Codigo_Produto']}</td>
	    			<td style="width: 50%";>{$row['D001_Descricao_Produto']}</td>
	    			<td style="width: 10%";>{$row['D082_Marca']}</td>
	    			<td style="width: 10%"; align="right">{$row['T138_Quantidade']}</td>
	    			<td style="width: 15%"; align="right">{$row['T138_Valor_Preco_Unitario']}</td>
	    			<td style="width: 15%"; align="right">{$total}</td>
	    		</tr>
	    	</table>
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

include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	function abrirPDF(T005_Id) {
			$.getJSON('./tablet_func-ajax.php?opcaoAcao=updateT005_Flag_PDF_Impresso&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
    			//
			});	
		}
        function gravarStatus(elem){
            var Status = elem.value;
            $.mobile.changePage( './caixa.php?T005_Id={$T005['T005_Id']}&flagStatus=' + Status, { transition: 'fade'} );
        }
    	$( document ).undelegate("#pagecaixa", "pageinit");
    	$( document ).delegate("#pagecaixa", "pageinit", function() {
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
