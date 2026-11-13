<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

$script="";
if (isset($_REQUEST['flagStatus'])) {
    $status = $_REQUEST['flagStatus'];

    if($status==3){
    	$and = " , T005_Flag_Status=8, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Data_Hora_Alteracao_Status_OS=CURRENT_TIMESTAMP() ";
    } else if ($status==2){
    	$and = " , T005_Data_Hora_Alteracao_Status_OS=CURRENT_TIMESTAMP() ";
    }else{
    	$and = " , T005_Flag_Status=0, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) , T005_Data_Hora_Alteracao_Status_OS=''";
    }
    $sql = "UPDATE T005 SET T005_Flag_Status_Servicos='{$status}' {$and} WHERE T005_Id = '{$T005['T005_Id']}'";
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

require_once("bibliotecas/classes/VEN002.php");
require_once("bibliotecas/classes/VEN012.php");
$VEN002 = new VEN002();
$VEN012 = new VEN012();

// Adiciona um novo Pedido
if ($T005_Id == 'new') {
	// cria um novo Pedido vazio
	$sql = <<<EOT
		INSERT INTO 
		T005 
			(T005_D024_Id, T005_D013_Id, T005_D116_Id, T005_D006_Id, T005_Flag_Revenda_Consumidor, T005_Data_Emissao, T005_C007_Id_Vendedor_Interno, T005_D021_Id, T005_Flag_Acp, T005_Forma_Pagamento, T005_Frete, T005_Prazo_Entrega, T005_Validade_Proposta, T005_Observacao, T005_C004_Id) 
		VALUES 
			('', '', '', '', '1', CURDATE(), '{$g['usuarioAtual']}', '', 6, '', '', '', '', '', '{$g['empresaAtual']}');
EOT;
	mysql_query($sql);
	$_REQUEST['T005_Id'] = $g['mysqlLastId'];
}

include('inc.conf.php');

// Remover Produto do Pedido
if (isset($_REQUEST['T006_Id'])) {
	$T006_Id = $_REQUEST['T006_Id'];
	$T005_Id = $_REQUEST['T005_Id'];
	$sql = "DELETE FROM T189 WHERE T189_T006_Id = '{$T006_Id}';";
	mysql_query($sql);
	//$VEN002->excluirProduto($T006_Id, $T005_Id);
	$VEN002->cancelarProduto($T006_Id, $T005_Id);
	$retorno = $VEN002->excluirControleProduto($T006_Id);
	if($retorno == true){
		mysql_query("UPDATE T005 SET T005_Flag_Status_Servicos = '0' WHERE T005_Id = '{$T005_Id}'");
	}
	$uniqId=uniqid();
	$script .= <<<EOT
	$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}{$acao}', { transition: 'fade'} );
EOT;
}

// Incluir/Alterar Desconto Manual
if (isset($_REQUEST['alterarDesconto'])) {
	$vendaAVista = false;
	$parcelas = mysql_query("SELECT * FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
	if(mysql_num_rows($parcelas) == 1){
		$parcelas = mysql_fetch_assoc($parcelas);
		if($parcelas['T090_Prazos_Dias'] == 0){
			$vendaAVista = true;
		}
	}
	$travarDescontoSuperiorAlcada = $g['C031']['travarDescontoSuperiorAlcada'];
	if($g['C007']['C007_Flag_Vendedor'] == 'D'){
		$travarDescontoSuperiorAlcada = 'N';
	} else if($g['C007']['C007_Flag_Vendedor'] == 'S'){
		$descontoLiberadoAlcada = ($vendaAVista) ? $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista'] : $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisor'];
	} else {
		$descontoLiberadoAlcada = ($vendaAVista) ? $g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista'] : $g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisor'];
	}
	//Verifica se a trava de percentual de desconto está ativa e se o percentual aplicado é superior a alçada do usuário
	$descontoExistente = ($T005['T005_Valor_Desconto_Manual']/$T005['T005_Valor_Total_Produtos_Sem_Desconto'])*100; //Desconto aplicado no preço dos produtos é zerado sempre, então verifico apenas o desconto manual
	$descontoAplicado = gCorrigeNumeroInverte($_REQUEST['percentualDesconto']);
	if (isset($_REQUEST['descontoValorTotal'])) {
		$descontoExistente = ($T005['T005_Valor_Desconto']/$T005['T005_Valor_Total_Produtos_Sem_Desconto'])*100; //Se o desconto for manual, verifico o percentual de desconto existente aplicado nos produtos
		$descontoAplicado = (gCorrigeNumeroInverte($_REQUEST['descontoValorTotal'])/$T005['T005_Valor_Total_Produtos_Sem_Desconto'])*100;
	} else if (isset($_REQUEST['valorDescontoReais'])) {
		$descontoAplicado = (gCorrigeNumeroInverte($_REQUEST['valorDescontoReais'])/$T005['T005_Valor_Total_Produtos_Sem_Desconto'])*100;
	} else if (isset($_REQUEST['valorTotal'])) {
	   	$descontoAplicado = (($T005['T005_Valor_Total_Produtos_Sem_Desconto']-gCorrigeNumeroInverte($_REQUEST['valorTotal']))/$T005['T005_Valor_Total_Produtos_Sem_Desconto'])*100;
    }

    $totalDescontoAplicado = $descontoExistente + $descontoAplicado;
	if($travarDescontoSuperiorAlcada == 'N' || ($travarDescontoSuperiorAlcada == 'S' && $totalDescontoAplicado <= $descontoLiberadoAlcada)){
		//Desconto em reais
		if (isset($_REQUEST['valorDescontoReais'])) {
			$retorno = $VEN002->VEN002aplicarDescontoReais($T005_Id, $_REQUEST['valorDescontoReais']);
		}

		//Desconto em percentual
		if (isset($_REQUEST['percentualDesconto'])) {
			$retorno = $VEN002->VEN002aplicarDesconto($T005_Id, $_REQUEST['percentualDesconto'], 'tabela');
		}

		//Definir valor total
		if (isset($_REQUEST['valorTotal'])) {
			$retorno = $VEN002->VEN002aplicarDescontoTotal($T005_Id, $_REQUEST['valorTotal']);
		}

		//Desconto no valor total
		if (isset($_REQUEST['descontoValorTotal'])) {
			$descontoValorTotal = gCorrigeNumeroInverte($_REQUEST['descontoValorTotal']);
			mysql_query("UPDATE T005 SET T005_Valor_Desconto_Manual='{$descontoValorTotal}' WHERE T005_Id = '{$T005_Id}'");
		}
		$script .= <<<EOT
			$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}{$acao}&ajax=true', { transition: 'fade'} );
EOT;
	} else {
		$script .= <<<EOT
	        alert('O desconto aplicado é maior que o permitido para sua alçada!');
EOT;
	}
include('inc.conf.php');
}
//$monta = $VEN002->montarEmailPdf($T005_Id);

mysqli_query("call T005_Gravar_Totalizacao_4('$T005_Id')");
$T005=mysql_query("select * from T005 where T005_Id='$T005_Id'");
$T005=mysql_fetch_array($T005);

include('inc.conf.php');

$totalServicos = gCorrigeNumero($T005['T005_Valor_Total_Servicos']);
$totalProdutos = gCorrigeNumero($T005['T005_Valor_Total_Produtos_Sem_Desconto']);
$totalTrocas = gCorrigeNumero($T005['T005_Valor_Troca']);
$totalGeral = gCorrigeNumero($T005['T005_Valor_Total']);
$totalDescontoManual = gCorrigeNumero($T005['T005_Valor_Desconto_Manual']);

$totalDesconto  = gCorrigeNumero($T005['T005_Valor_Desconto']+$T005['T005_Valor_Desconto_Manual']);
$percentualDesconto = gCorrigeNumero((($T005['T005_Valor_Desconto']+$T005['T005_Valor_Desconto_Manual'])/$T005['T005_Valor_Total_Produtos_Sem_Desconto'])*100);
$percentualDesconto = "({$percentualDesconto} %)";

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedido">
		<script>
			function imprimirPedido() {
				$.mobile.showPageLoadingMsg();
				$.getJSON('./tablet_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&callback=?', function(request) {
					$.mobile.hidePageLoadingMsg();
					if (request.code == 'erro' || request.code == 'analise') {
						$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
					} else if (request.code) {
						$.mobile.showPageLoadingMsg();
						$.getJSON('./tablet_func-ajax.php?opcaoAcao=ven002ImprimirPedido&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&callback=?', function(request) {
							$.mobile.hidePageLoadingMsg();
							if(request.code === true){
								window.open(request.data, '_blank');
							} else {
								$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
							}
						});
					} else {
						$('<div>').simpledialog2({
							animate: false, mode: 'button',
							headerText: 'Notificação', headerClose: false,
							buttonPrompt: request.data,
							buttons : {
								'Sim': {
									click: function () {
										$.mobile.showPageLoadingMsg();
										$.getJSON('./tablet_func-ajax.php?opcaoAcao=enviarParaAnalise&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&acao=imprimir&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
											$.mobile.hidePageLoadingMsg();
											if (request2.code) {
												$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
											} else {
												$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
											}
										});
										setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300);
									}
								},
								'Não': {
									click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); },
									icon: "delete"
								}
							}
						});
					}
				});
				return false;
	    	}

	    	function emailPedido(){
	    		$.mobile.showPageLoadingMsg();
	    		$.getJSON('./tablet_func-ajax.php?opcaoAcao=verificarParcelas&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&callback=?', function(request1) {
					$.mobile.hidePageLoadingMsg();
					if(request1.code){
						$.mobile.showPageLoadingMsg();
						$.getJSON('./tablet_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&acao=enviarEmail&callback=?', function(request) {
							$.mobile.hidePageLoadingMsg();
							if (request.code == 'erro' || request.code == 'analise') {
								$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
							} else if (request.code) {
								$.mobile.changePage('./pedidoEnviarEmail.php?T005_Id={$T005_Id}', { transition: 'fade'} );
							} else {
								$('<div>').simpledialog2({
									animate: false, mode: 'button',
									headerText: 'Notificação', headerClose: false,
									buttonPrompt: request.data,
									buttons : {
										'Sim': {
											click: function () {
												$.mobile.showPageLoadingMsg();
												$.getJSON('./tablet_func-ajax.php?opcaoAcao=enviarParaAnalise&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&acao=email&callback=?', function(request2) {
													$.mobile.hidePageLoadingMsg();
													if (request2.code) {
														$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
													} else {
														$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
													}
												});
												setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300);
											}
										},
										'Não': {
											click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); },
											icon: "delete"
										}
									}
								});
							}
						});
					} else {
						$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request1.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
					}
				});
				return false;
	    	}
		</script>
EOT;
$etapa = 2;
include('mobile/tablet/inc.header.bar.php');
include('inc.header.page.php');

//Tamanho das colunas do Cabeçalho de Pedidos
$qtdeColunas = 5;
if($g['C031']['habilitarServicos'] == 'N') {
	$exibirServicos = 'display: none;';
	$qtdeColunas--;
} else {
	$exibirServicos = '';
}

if($g['C031']['habilitarTrocas'] == 'N') {
	$exibirTrocas = 'display: none;';
	$qtdeColunas--;
} else {
	$exibirTrocas = '';
}
$tamanhoColuna = floor(100/$qtdeColunas);
$tamanhoColuna -= 0.5;

echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-d" style="margin: 10px 0 5px 0; border: 1px solid #CCCCCC; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3); padding: 8px 0;">
                    <div class="ui-block-a" style="border-right: 2px solid #CCCCCC; text-align: center; width: {$tamanhoColuna}%;">
                        <h4 style="margin: 0px;">
                            Produtos
                        </h4>
                        <h2 style="margin: 0px;">
                            R$ {$totalProdutos}
                        </h2>
                    </div>
                    <div class="ui-block-b" style="border-right: 2px solid #CCCCCC; text-align: center; width: {$tamanhoColuna}%; {$exibirServicos}">
                        <h4 style="margin: 0px;">
                            Serviços
                        </h4>
                        <h2 style="margin: 0px;">
                            R$ {$totalServicos}
                        </h2>
                    </div>
                    <div class="ui-block-c" style="border-right: 2px solid #CCCCCC; text-align: center; width: {$tamanhoColuna}%; {$exibirTrocas}">
                        <h4 style="margin: 0px;">
                            Trocas
                        </h4>
                        <h2 style="margin: 0px;">
                            R$ {$totalTrocas}
                        </h2>
                    </div>
                    <div class="ui-block-d" style="border-right: 2px solid #CCCCCC; text-align: center; width: {$tamanhoColuna}%;">
                        <h4 style="margin: 0px;">
                            Desconto
                        </h4>
						<h2 style="margin: 0px; text-decoration:underline;" onclick="\$.mobile.changePage( './pedidoGravarDescontoManual.php?T005_Id={$T005_Id}{$acao}', { transition: 'fade'} );" data-ajax="false">
                            R$ {$totalDesconto} <font style="font-size:16px;font-weight:normal;">{$percentualDesconto}</font>
                        </h2>
                    </div>
                    <div class="ui-block-e" style="text-align: center; width: {$tamanhoColuna}%;">
                        <h4 style="margin: 0px;">
                            Total
                        </h4>
                        <h2 style="margin: 0px;">
                            R$ {$totalGeral} <font style="font-size:16px;font-weight:normal;">{$alcadaNome}</font>
                        </h2>
                    </div>
                </div>
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div class="ui-block-a">
                		<div style="float: left;">
                			<a data-role="button" data-inline="true" data-transition="fade" onclick="imprimirPedido(); return false;" data-ajax="false" rel="external" target="_blank">Imprimir</a>
	                        <a data-inline="true" data-role="button" data-transition="fade" onclick="emailPedido(); return false;" data-ajax="false">E-mail</a>
                    	</div>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarProduto" onclick="$.mobile.changePage( './pedidoProcurarProduto.php?T005_Id={$T005_Id}{$acao}', { transition: 'slide'} );" data-ajax="false">
			            		Adicionar
				            </a>
				        </div>
                    	<!--<div style="width: 70%; float:right; margin-top: 8px;">
                    		<input placeholder="Procurar Produto" data-type="search" class="ui-input-text ui-body-c" id="procurarProduto">
                    	</div>-->
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%;">
	    					<tr>
	    						<td style="width: 0%;"></td>
	    						<td style="width: 6%;">Código</td>
	    						<td style="width: 42%;">Produto venda</td>
	    						<td style="width: 12%;" align=center>Marca</td>
	    						<td style="width: 8%;" align=center>Estoque</td>
	    						<td style="width: 8%;" align=center>Quantidade</td>
	    						<td style="width: 12%;" align=right>Preço R$</td>
	    						<td style="width: 12%;" align=right>Total R$</td>
	    					</tr>
	    				</table>
                    </li>
EOT;

// Produtos
$sql = <<<EOT
	SELECT
		T006_Id, T006_Codigo_Produto, T006_Descricao_Produto, D082_Marca, T006_Quantidade, T006_Valor_Preco_Unitario, T006_Valor_Total_Preco, D009_Id, D001_Id,D001_Flag_Pre_Cadastro, D009_Quantidade_Estoque_Liquido,T006_Flag_Cancelado
	FROM T006
		LEFT JOIN D009 on D009_Id=T006_D009_Id LEFT JOIN D049 on D049_Id=D009_D049_Id
		LEFT JOIN D001 on D001_Id=D049_D001_Id LEFT JOIN D082 on D082_Id=D049_D082_Id
		LEFT JOIN D037 on D037_Id=T006_D037_Id LEFT JOIN D005 on D005_Id=T006_D005_Id
		LEFT JOIN D006 on D006_Id=T006_D006_Id
	WHERE
		T006_T005_Id = '{$T005_Id}'
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
	//$row['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
	$row['T006_Quantidade'] = number_format($row['T006_Quantidade'], 0);
	$row['T006_Valor_Preco_Unitario'] = gCorrigeNumero($row['T006_Valor_Preco_Unitario']);
	$row['T006_Valor_Total_Preco'] = gCorrigeNumero($row['T006_Valor_Total_Preco']);
	$row['T006_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['T006_Descricao_Produto']} (PRÉ)" : $row['T006_Descricao_Produto']);

	if($row['T006_Flag_Cancelado']=='S'){
		$row['T006_Descricao_Produto'] = $row['T006_Descricao_Produto']." <font color='red'><b> CANCELADO</b></font>";
	}

	//Verifica se o Item já foi cotado
	$sql = "SELECT * FROM T189 WHERE T189_T006_Id='{$row['T006_Id']}'";
	$verificaCotacao = mysql_query($sql);
	$resT189 = mysql_fetch_array($verificaCotacao);

	if(mysql_num_rows($verificaCotacao) > 0){
		$sqlT011 = <<<EOT
		SELECT T011_Id
			FROM T174
			LEFT JOIN T173 ON T173_Id=T174_T173_Id
			LEFT JOIN D024 ON D024_Id=T173_D024_Id
			LEFT JOIN T010 ON T010_Id=T173_T010_Id
			LEFT JOIN T059 ON T059_Id=T174_T059_Id
			LEFT JOIN T189 ON T189_Id=T059_T189_Id
			LEFT JOIN T012 ON T012_T174_Id=T174_Id or T012_T189_Id=T189_Id
			LEFT JOIN T011 ON T011_Id=T012_T011_Id
			WHERE T059_T189_Id='{$resT189['T189_Id']}' AND T011_Id IS NOT NULL
EOT;
		$resT011 = mysql_query($sqlT011);
		$resT011 = mysql_fetch_array($resT011);
		$T011_Id = $resT011[0];
	}

	$corLinha = '';
	$T207 = mysql_query("SELECT * FROM T207 WHERE T207_T006_Id = '{$row['T006_Id']}' AND T207_Flag_Status = '3'");
	if(mysql_num_rows($T207) > 0){
		$corLinha = 'color: #FF7700;';
	}
	$T204 = mysql_query("SELECT  * FROM T204 WHERE T204_T006_Id = '{$row['T006_Id']}'");
	if(mysql_num_rows($T204) > 0){
		$row['T006_Descricao_Produto'] .= " <span style='color: green;'>Agregados</span>";
	}
    $row['D009_Quantidade_Estoque_Liquido'] = (empty($row['D009_Quantidade_Estoque_Liquido']) ? '0.00' : gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']));
	echo <<<EOT
		<li data-theme="c" data-icon="false">
	    <a href="./pedidoDelProduto.php?T005_Id={$T005_Id}&D001_Id={$row['D001_Id']}&D009_Id={$row['D009_Id']}&T006_Id={$row['T006_Id']}{$acao}" data-ajax="false" data-transition="slide">
	    	<table style="width: 100%; {$corLinha}">
	    		<tr>
	    			<td style="white-space: normal !important; width: 0%;">{$image}</td>
	    			<td style="white-space: normal !important; width: 6%;">{$row['T006_Codigo_Produto']}</td>
	    			<td style="white-space: normal !important; width: 42%;">{$row['T006_Descricao_Produto']}</td>
	    			<td style="white-space: normal !important; width: 12%;" align=center>{$row['D082_Marca']}</td>
	    			<td style="white-space: normal !important; width: 8%;" align=center>{$row['D009_Quantidade_Estoque_Liquido']}</td>
	    			<td style="white-space: normal !important; width: 8%;" align=center>{$row['T006_Quantidade']}</td>
	    			<td style="white-space: normal !important; width: 12%;" align=right>{$row['T006_Valor_Preco_Unitario']}</td>
	    			<td style="white-space: normal !important; width: 12%;" align=right>{$row['T006_Valor_Total_Preco']}</td>
	    	</table>
	    </a>
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
	    			<td style="width: 49%";>{$row['D001_Descricao_Produto']}</td>
	    			<td style="width: 8%"; align=center>{$row['D082_Marca']}</td>
	    			<td style="width: 8%"; align=center></td>
	    			<td style="width: 8%"; align=center>{$row['qtdeProduto']}</td>
	    			<td style="width: 15%"; align=right>CANCELADO</td>
	    			<td style="width: 15%"; align=right>CANCELADO</td>
	    		</tr>
	    	</table>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
                <div class="ui-grid-a" style="margin-top: -10px; margin-bottom: 5px; {$exibirServicos}">
                	<div class="ui-block-a">
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarServico" onclick="$.mobile.changePage( './pedidoProcurarTipoServico.php?T005_Id={$T005_Id}{$acao}&pesquisa=' + encodeURIComponent($('#procurarServico').val()), { transition: 'slide'} );">
			            		Adicionar
							</a>
				        </div>
                    	<!--<div style="width: 70%; float:right; margin-top: 8px;">
                    		<input placeholder="Procurar Serviço" data-type="search" class="ui-input-text ui-body-c" id="procurarServico">
                    	</div>-->
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px; {$exibirServicos}">
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
		T147_Id, T147_Valor_Unitario, T147_Titulo, T147_Descricao, D001_Descricao_Produto, T147_Tempo_Estimado, T147_D110_Id, T147_D112_Id, T147_D116_Id, T147_Flag_Cancelado
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
	$cancelado='';
	if($row['T147_Flag_Cancelado']=='S'){
		$cancelado='<font color="red"><b> Cancelado</b></font>';
	}
	echo <<<EOT
		<li data-theme="c" data-icon="false">
	    <a onclick="\$.mobile.changePage( './pedidoDelServico.php?T005_Id={$T005_Id}&T147_Id={$row['T147_Id']}{$acao}', { transition: 'slide'} );" data-transition="slide">
	    	<table style="width: 100%";>
	    		<tr>
	    			<td style="width: 6%"; align="center">{$row['T147_Id']}</td>
	    			<td style="width: 44%";>{$row['T147_Titulo']} {$row['T147_Descricao']}{$cancelado}</td>
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
                <div class="ui-grid-a" style="margin-top: -10px; margin-bottom: 5px; {$exibirTrocas}">
                	<div class="ui-block-a">
                		<div style="float: left;">
                    	</div>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarProdutoTroca" onclick="$.mobile.changePage( './pedidoProcurarProduto.php?T005_Id={$T005_Id}{$acao}&tipo=troca&pesquisa=' + encodeURIComponent($('#procurarProdutoTroca').val()), { transition: 'slide'} );" data-ajax="false">
			            		Adicionar
							</a>
				        </div>
                    	<!--<div style="width: 70%; float:right; margin-top: 8px;">
                    		<input placeholder="Procurar produto para troca" data-type="search" class="ui-input-text ui-body-c" id="procurarProdutoTroca">
                    	</div>-->
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px; {$exibirTrocas}">
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
	    <a href="./pedidoDelProdutoTroca.php?T005_Id={$T005_Id}&D001_Id={$row['D001_Id']}&D009_Id={$row['D009_Id']}&T138_Id={$row['T138_Id']}{$acao}" data-ajax="false" data-transition="slide">
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
$rowAnalista = @mysql_fetch_array($resAnalista);

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
							</div>
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
    	$( document ).undelegate("#pagepedido", "pageinit");
    	$( document ).delegate("#pagepedido", "pageinit", function() {
		{$script}
    	});
    </script>
EOT;
