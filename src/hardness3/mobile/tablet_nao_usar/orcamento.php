<?php
namespace hardness;

include('conf.php');

include('inc.conf.php');
$script="";
// Adiciona um novo orçamento
if ($T003_Id == 'new') {
	// cria um novo orçamento vazio
	$sql = <<<EOT
		INSERT INTO T003 (T003_D024_Id, T003_D013_Id, T003_D116_Id, T003_D006_Id, T003_Flag_Revenda_Consumidor, T003_Data_Emissao, T003_C007_Id_Vendedor_Interno, T003_D021_Id, T003_Flag_Acp, T003_Forma_Pagamento, T003_Frete, T003_Prazo_Entrega, T003_Validade_Proposta, T003_Observacao, T003_C004_Id, T003_Casas_Decimais_Preco, T003_Flag_Destino_Produto)
		VALUES ('{$_REQUEST['D024_Id']}', '', '', '', '1', CURDATE(), '{$g['usuarioAtual']}', '', '', '', '', '', '', '', '{$g['empresaAtual']}', '2', '{$_REQUEST['T003_Flag_Destino_Produto']}');
EOT;
	mysql_query($sql);
	$_REQUEST['T003_Id'] = $g['mysqlLastId'];
	$lastIdOrcamento = $g['mysqlLastId'];

	if(isset($_REQUEST['D024_Id']) && !isset($_REQUEST['T003_Flag_Destino_Produto'])){
		$result = mysql_query("SELECT D024_Flag_Contribuinte FROM D024 WHERE D024_Id='{$_REQUEST['D024_Id']}'");
		$aD024 = mysql_fetch_assoc($result);

		if($g['C031']['definirDestinoVendaTipoCliente'] == 'S'){
			switch ($aD024['D024_Flag_Contribuinte']) {
				case '5':
					$T003_Flag_Destino_Produto = '1';
					break;
				case '2':
					$T003_Flag_Destino_Produto = '2';
					break;
				case '1':
					$T003_Flag_Destino_Produto = '3';
					break;
			}
		}

		// GRAVA CLIENTE E CFOP NO ORCAMENTO
		$sql = "UPDATE T003 SET T003_Flag_Destino_Produto = '{$T003_Flag_Destino_Produto}' WHERE T003_Id = '{$lastIdOrcamento}';";
		mysql_query($sql);
	}
}

if($T003_Id != 'new'){
	$lastIdOrcamento = $_REQUEST['T003_Id'];
}

include('inc.conf.php');

mysqli_query("call T003_Gravar_Totalizacao_4('$T003_Id')");
$T003=mysql_query("select * from T003 where T003_Id='$T003_Id'");
$T003=mysql_fetch_array($T003);

$totalServicos = gCorrigeNumero($T003['T003_Valor_Total_Servicos']);
$totalProdutos = gCorrigeNumero($T003['T003_Valor_Total_Produtos_Sem_Desconto']);
$totalTrocas = gCorrigeNumero($T003['T003_Valor_Troca']);
$totalGeral = gCorrigeNumero($T003['T003_Valor_Total']);

$totalDesconto  = gCorrigeNumero($T003['T003_Valor_Desconto']+$T003['T003_Valor_Desconto_Manual']);
$percentualDesconto = gCorrigeNumero((($T003['T003_Valor_Desconto']+$T003['T003_Valor_Desconto_Manual'])/$T003['T003_Valor_Total_Produtos_Sem_Desconto'])*100);
$percentualDesconto = "({$percentualDesconto} %)";

require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

// Remover Produto do orçamento
if (isset($_REQUEST['T004_Id'])) {
	$T004_Id = $_REQUEST['T004_Id'];
	$flagTipoRequisicao = $_REQUEST['tipoRequisicao'];
	$VEN001->excluirProduto($T004_Id);
	//$VEN001->cancelarProduto($T004_Id);

	$uniqId=uniqid();
	//Setar item excluido no detalhe da requisição de compra caso o item excluido esteja relacionado a uma requisição cancelada
	$sqlT189 = <<<EOT
		DELETE FROM T189 WHERE T189_T004_Id='{$T004_Id}'
EOT;
	mysql_query($sqlT189);
	$script .= <<<EOT
	$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );
EOT;
}

// Incluir/Alterar Desconto Manual
if (isset($_REQUEST['alterarDesconto'])) {
	$vendaAVista = false;
	$parcelas = mysql_query("SELECT * FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
	if(mysql_num_rows($parcelas) == 1){
		$parcelas = mysql_fetch_assoc($parcelas);
		if($parcelas['T089_Prazos_Dias'] == 0){
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
	$descontoExistente = ($T003['T003_Valor_Desconto_Manual']/$T003['T003_Valor_Total_Produtos_Sem_Desconto'])*100; //Desconto aplicado no preço dos produtos é zerado sempre, então verifico apenas o desconto manual
	$descontoAplicado = gCorrigeNumeroInverte($_REQUEST['percentualDesconto']);
	if (isset($_REQUEST['descontoValorTotal'])) {
		$descontoExistente = ($T003['T003_Valor_Desconto']/$T003['T003_Valor_Total_Produtos_Sem_Desconto'])*100; //Se o desconto for manual, verifico o percentual de desconto existente aplicado nos produtos
		$descontoAplicado = (gCorrigeNumeroInverte($_REQUEST['descontoValorTotal'])/$T003['T003_Valor_Total_Produtos_Sem_Desconto'])*100;
	} else if (isset($_REQUEST['valorDescontoReais'])) {
		$descontoAplicado = (gCorrigeNumeroInverte($_REQUEST['valorDescontoReais'])/$T003['T003_Valor_Total_Produtos_Sem_Desconto'])*100;
	} else if (isset($_REQUEST['valorTotal'])) {
	   	$descontoAplicado = (($T003['T003_Valor_Total_Produtos_Sem_Desconto']-gCorrigeNumeroInverte($_REQUEST['valorTotal']))/$T003['T003_Valor_Total_Produtos_Sem_Desconto'])*100;
    }

    $totalDescontoAplicado = $descontoExistente + $descontoAplicado;
	if($travarDescontoSuperiorAlcada == 'N' || ($travarDescontoSuperiorAlcada == 'S' && $totalDescontoAplicado <= $descontoLiberadoAlcada)){
		//Desconto em reais
		if (isset($_REQUEST['valorDescontoReais'])) {
			$retorno = $VEN001->VEN001aplicarDescontoReais($T003_Id, $_REQUEST['valorDescontoReais']);
		}

		//Desconto em percentual
		if (isset($_REQUEST['percentualDesconto'])) {
			$retorno = $VEN001->VEN001aplicarDesconto($T003_Id, $_REQUEST['percentualDesconto'], 'tabela');
		}

		//Definir valor total
		if (isset($_REQUEST['valorTotal'])) {
	    	$retorno = $VEN001->VEN001aplicarDescontoTotal($T003_Id, $_REQUEST['valorTotal']);
	    }

	    //Desconto no valor total
	    if (isset($_REQUEST['descontoValorTotal'])) {
	        $descontoValorTotal = gCorrigeNumeroInverte($_REQUEST['descontoValorTotal']);
	        mysql_query("UPDATE T003 SET T003_Valor_Desconto_Manual='{$descontoValorTotal}' WHERE T003_Id = '{$T003_Id}'");
	    }
	    $script .= <<<EOT
	        $.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&ajax=true', { transition: 'fade'} );
EOT;
	} else {
		$script .= <<<EOT
	        alert('O desconto aplicado é maior que o permitido para sua alçada!');
EOT;
	}
include('inc.conf.php');
}
include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageOrcamento">
		<script>
			function imprimirOrcamento() {
	    		$.mobile.showPageLoadingMsg();
	    		$.getJSON('./tablet_func-ajax.php?opcaoAcao=verificarParcelas&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request1) {
					$.mobile.hidePageLoadingMsg();
					if(request1.code){
						$.mobile.showPageLoadingMsg();
						$.getJSON('./tablet_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request) {
							$.mobile.hidePageLoadingMsg();
							if (request.code == 'erro' || request.code == 'analise') {
								$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
							} else if (request.code) {
								$.mobile.showPageLoadingMsg();
								$.getJSON('./tablet_func-ajax.php?opcaoAcao=ven001ImprimirOrcamento&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request) {
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
												$.getJSON('./tablet_func-ajax.php?opcaoAcao=enviarParaAnalise&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
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

	    	function emailOrcamento(){
	    		$.mobile.showPageLoadingMsg();
	    		$.getJSON('./tablet_func-ajax.php?opcaoAcao=verificarParcelas&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request1) {
					$.mobile.hidePageLoadingMsg();
					if(request1.code){
						$.mobile.showPageLoadingMsg();
						$.getJSON('./tablet_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request) {
							$.mobile.hidePageLoadingMsg();
							if (request.code == 'erro' || request.code == 'analise') {
								$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
							} else if (request.code) {
								$.mobile.changePage('./orcamentoEnviarEmail.php?T003_Id={$T003_Id}&D024_Id={$T003['T003_D024_Id']}', { transition: 'fade'} );
							} else {
								$('<div>').simpledialog2({
									animate: false, mode: 'button',
									headerText: 'Notificação', headerClose: false,
									buttonPrompt: request.data,
									buttons : {
										'Sim': {
											click: function () {
												$.mobile.showPageLoadingMsg();
												$.getJSON('./tablet_func-ajax.php?opcaoAcao=enviarParaAnalise&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
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
include('inc.header.bar.php');
include('inc.header.page.php');

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

//Verifica se o Cliente foi informado no Oçamento
$D024 = mysql_query("SELECT * FROM d024 LEFT JOIN t003 ON T003_D024_Id = D024_Id WHERE T003_Id = {$lastIdOrcamento}");
if((mysql_num_rows($D024)<=0)){
    echo "<h2 align='center' style='color: red; margin-top: 85px;'><b>Informe o Cliente!</b></h2>";
    die();
}
if($T003['T003_Flag_Cortesia']=='S'){
    echo "<h2 align='center' style='color: red; margin-top: 4px; margin-bottom:-5px;'><b>* CORTESIA *</b></h2>";
}
echo <<<EOT
        	<div data-role="content" style="text-align: center;">
                <div class="ui-grid-d" style="margin: 0px 0 5px 0; border: 1px solid #CCCCCC; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3); padding: 8px 0;">
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
                        <h2 style="margin: 0px; text-decoration:underline;" onclick="\$.mobile.changePage( './orcamentoGravarDescontoManual.php?T003_Id={$T003_Id}', { transition: 'fade'} );" data-ajax="false">
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
	                        <a data-role="button" data-inline="true" data-transition="fade" onclick="imprimirOrcamento(); return false;" data-ajax="false" rel="external" target="_blank">Imprimir</a>
	                        <a data-inline="true" data-role="button" data-transition="fade" onclick="emailOrcamento(); return false;" data-ajax="false">E-mail</a>
                    	</div>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarProduto" onclick="$.mobile.changePage( './orcamentoProcurarProduto.php?T003_Id={$T003_Id}', { transition: 'slide'} );" data-ajax="false">
			            		Adicionar
				            </a>
				        </div>
                    	<!--<div style="width: 70%; float:right; margin-top: 14px;">
                    		<input placeholder="Procurar Produto" data-type="search" class="ui-input-text ui-body-c" id="procurarProduto">
                    	</div>-->
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 0%";></td>
	    						<td style="width: 6%";>Código</td>
	    						<td style="width: 42%";>Produto venda</td>
	    						<td style="width: 12%"; align=center>Marca</td>
	    						<td style="width: 8%"; align=center>Estoque</td>
	    						<td style="width: 8%"; align=center>Quantidade</td>
	    						<td style="width: 12%"; align=right>Preço R$</td>
	    						<td style="width: 12%"; align=right>Total R$</td>
	    					</tr>
	    				</table>
                    </li>
EOT;

				// Produtos
				$sql = <<<EOT
					SELECT
						T004_Id, T004_Codigo_Produto, T004_Descricao_Produto, D082_Marca, T004_Quantidade, T004_Valor_Preco_Unitario, T004_Valor_Total_Preco, D009_Id, D001_Id,D001_Flag_Pre_Cadastro, D009_Quantidade_Estoque_Liquido, T004_Flag_Cancelado
					FROM T004
						LEFT JOIN D009 on D009_Id=T004_D009_Id LEFT JOIN D049 on D049_Id=D009_D049_Id
						LEFT JOIN D001 on D001_Id=D049_D001_Id LEFT JOIN D082 on D082_Id=D049_D082_Id
						LEFT JOIN D037 on D037_Id=T004_D037_Id LEFT JOIN D005 on D005_Id=T004_D005_Id
						LEFT JOIN D006 on D006_Id=T004_D006_Id LEFT JOIN T006 on T006_T004_Id=T004_Id
					WHERE
						T004_T003_Id = '{$T003_Id}';
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
				$row['T004_Quantidade'] = number_format($row['T004_Quantidade'], 0);
				$row['T004_Valor_Preco_Unitario'] = gCorrigeNumero($row['T004_Valor_Preco_Unitario']);
				$row['T004_Valor_Total_Preco'] = gCorrigeNumero($row['T004_Valor_Total_Preco']);
				$row['T004_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['T004_Descricao_Produto']} (PRÉ)" : $row['T004_Descricao_Produto']);

				if($row['T004_Flag_Cancelado']=='S'){
					$row['T004_Descricao_Produto'] = $row['T004_Descricao_Produto']." <font color='red'><b> CANCELADO</b></font>";
				}

				//Verifica se o Item já foi cotado
				$sql = "SELECT * FROM T189 LEFT JOIN T004 ON T189_T004_Id = T004_Id WHERE T004_Id='{$row['T004_Id']}'";
				$verificaCotacao = mysql_query($sql);
				$resT189 = mysql_fetch_array($verificaCotacao);
					if(mysql_num_rows($verificaCotacao) > 0) {
						if($resT189['T189_Flag_Status'] == 'C'){
							if($row['T004_Valor_Preco_Unitario'] <= 0) {
								$row['T004_Valor_Preco_Unitario'] = "<font color='blue'>COTADO</font>";
								$row['T004_Valor_Total_Preco'] = "<font color='blue'>COTADO</font>";
							}
						}
						if($resT189['T189_Flag_Status'] == 'N'){
							if($row['T004_Valor_Preco_Unitario'] <= 0) {
								$row['T004_Valor_Preco_Unitario'] = "<font color='blue'>COTANDO</font>";
								$row['T004_Valor_Total_Preco'] = "<font color='blue'>COTANDO</font>";
							}
						}
					}
				    $row['D009_Quantidade_Estoque_Liquido'] = (empty($row['D009_Quantidade_Estoque_Liquido']) ? '0.00' : $row['D009_Quantidade_Estoque_Liquido']);
				echo <<<EOT
					<li data-theme="c" data-icon="false">
				    	<a class="clean" href="./orcamentoDelProduto.php?T003_Id={$T003_Id}&D001_Id={$row['D001_Id']}&D009_Id={$row['D009_Id']}&T004_Id={$row['T004_Id']}" data-ajax="false" data-transition="slide">
				    	<table style="width: 100%";>
				    		<tr>
				    			<td style="white-space: normal !important; width: 0% !important;">{$image}</td>
				    			<td style="white-space: normal !important; width: 6% !important;">{$row['T004_Codigo_Produto']}</td>
				    			<td style="white-space: normal !important; width: 42% !important;">{$row['T004_Descricao_Produto']}</td>
				    			<td style="white-space: normal !important; width: 12% !important;" align=center>{$row['D082_Marca']}</td>
				    			<td style="white-space: normal !important; width: 8% !important;" align=center>{$row['D009_Quantidade_Estoque_Liquido']}</td>
				    			<td style="white-space: normal !important; width: 8% !important;" align=center>{$row['T004_Quantidade']}</td>
				    			<td style="white-space: normal !important; width: 12% !important;" align=right>{$row['T004_Valor_Preco_Unitario']}</td>
				    			<td style="white-space: normal !important; width: 12% !important;" align=right>{$row['T004_Valor_Total_Preco']}</td>
				    		</tr>
				    	</table>
				    	</a>
				    </li>
EOT;
			}
			echo <<<EOT
                </ul>
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px; {$exibirServicos}">
                	<div class="ui-block-a">
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarServico" onclick="$.mobile.changePage( './orcamentoProcurarTipoServico.php?T003_Id={$T003_Id}&pesquisa=' + encodeURIComponent($('#procurarServico').val()), { transition: 'slide'} );">
			            		Adicionar
							</a>
				        </div>
                    	<!--<div style="width: 70%; float:right; margin-top: 14px;">
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
					T145_Id, T145_Valor_Unitario, T145_Titulo, T145_Descricao, D001_Descricao_Produto, T145_Tempo_Estimado, T145_D110_Id, T145_D112_Id, T145_D116_Id
				FROM T145
				LEFT JOIN T004 ON T004_Id=T145_T004_Id
				LEFT JOIN D009 ON D009_Id=T004_D009_Id
				LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				WHERE T145_T003_Id='{$T003_Id}'
EOT;
			$res = mysql_query($sql);
			while ($row = mysql_fetch_assoc($res)) {
				$row['T145_Valor_Unitario'] = gCorrigeNumero($row['T145_Valor_Unitario']);
				$row['T145_Tempo_Estimado'] = gCorrigeTempo($row['T145_Tempo_Estimado']);
				$row['D001_Descricao_Produto'] = empty($row['D001_Descricao_Produto']) ? '-' : "Produto ({$row['D001_Descricao_Produto']})";
				echo <<<EOT
					<li data-theme="c" data-icon="false">
					    <a onclick="\$.mobile.changePage( './orcamentoDelServico.php?T003_Id={$T003_Id}&T145_Id={$row['T145_Id']}', { transition: 'slide'} );" data-transition="slide">
					    	<table style="width: 100%";>
					    		<tr>
					    			<td style="width: 6%"; align="center">{$row['T145_Id']}</td>
					    			<td style="width: 44%";>{$row['T145_Titulo']} {$row['T145_Descricao']}</td>
					    			<td style="width: 40%";>{$row['D001_Descricao_Produto']}</td>
					    			<td style="width: 10%"; align="right">{$row['T145_Valor_Unitario']}</td>
					    		</tr>
					    	</table>
					    </a>
				    </li>
EOT;
}
echo <<<EOT
                </ul>
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px; {$exibirTrocas}">
                	<div class="ui-block-a">
                		<div style="float: left;">
                    	</div>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarProdutoTroca" onclick="$.mobile.changePage( './orcamentoProcurarProduto.php?T003_Id={$T003_Id}&tipo=troca&pesquisa=' + encodeURIComponent($('#procurarProdutoTroca').val()), { transition: 'slide'} );" data-ajax="false">
			            		Adicionar
							</a>
				        </div>
                    	<!--<div style="width: 70%; float:right; margin-top: 14px;">
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
				WHERE T138_T003_Id = '{$T003_Id}'
EOT;
				$res = mysql_query($sql);
				while ($row = mysql_fetch_assoc($res)) {
					$row['T138_Quantidade'] = gCorrigeNumero($row['T138_Quantidade']);
					$row['T138_Valor_Preco_Unitario'] = gCorrigeNumero($row['T138_Valor_Preco_Unitario']);
					$total = gCorrigeNumero($row['T138_Quantidade'] * $row['T138_Valor_Preco_Unitario']);
					$row['D001_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['D001_Descricao_Produto']} (PRÉ)" : $row['D001_Descricao_Produto']);
					
					echo <<<EOT
						<li data-theme="c" data-icon="false">
						    <a href="./orcamentoDelProdutoTroca.php?T003_Id={$T003_Id}&D001_Id={$row['D001_Id']}&D009_Id={$row['D009_Id']}&T138_Id={$row['T138_Id']}" data-ajax="false" data-transition="slide">
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
			$sqlAnalista = "SELECT C007_Id,C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$T003['T003_Liberou_Alcada_C007_Id']}'";
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
									<label for="T003_Flag_Status_Orcamento" style="width: 15%;">
					                    Status
						            </label>
									<input id="T003_Flag_Status_Orcamento" name="T003_Flag_Status_Orcamento" placeholder="" value="{$statusOrcamento}" type="text"  style="width: 80%;"/>
								</div>
								<div data-role="fieldcontain">
									<label for="T003_Liberou_Alcada_Observacao_Nao_Liberado" style="width: 15%;">
					                    Observação
						            </label>
									<textarea name="T003_Liberou_Alcada_Observacao_Nao_Liberado" id="T003_Liberou_Alcada_Observacao_Nao_Liberado" style="width: 80%;">{$T003['T003_Liberou_Alcada_Observacao_Nao_Liberado']}</textarea>
								</div>
							</div>
						</div>
EOT;
			}
			echo <<<EOT
					</div>
EOT;

include('inc.footer.php');
if(empty($rowAnalista['C007_Id'])){
	echo <<<EOT
		</div>
	</div>
EOT;
}
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageOrcamento", "pageinit");
    	$( document ).delegate("#pageOrcamento", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;

/*

$('<div>').simpledialog2({
	animate: false, mode: 'button',
	headerText: 'Notificação', headerClose: false,
	buttonPrompt: 'Ainda não foram geradas parcelas para este orçamento. Favor definir forma de pagamento!',
	buttons : {
		'OK': {
			click: function () {
				setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300);
			}
		}
	}
});

$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });


*/