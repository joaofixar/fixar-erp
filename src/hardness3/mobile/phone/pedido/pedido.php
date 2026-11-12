<?php

namespace hardness;

include('conf.php');
include('inc.conf.php');

$script="";

require_once("bibliotecas/classes/VEN001.php");
require_once("bibliotecas/classes/VEN002.php");
require_once("bibliotecas/classes/VEN012.php");
$VEN002 = new VEN002();
$VEN012 = new VEN012();
$VEN001 = new VEN001();

//$monta = $VEN002->montarEmailPdf($T005_Id);
// Adiciona um novo Pedido
if ($T005_Id == 'new') {
	// cria um novo Pedido vazio
	$sql = <<<EOT
		INSERT INTO T005 (T005_D024_Id, T005_D013_Id, T005_D116_Id, T005_D006_Id, T005_Flag_Revenda_Consumidor, T005_Data_Emissao, T005_C007_Id_Vendedor_Interno, T005_D021_Id, T005_Flag_Acp, T005_Forma_Pagamento, T005_Frete, T005_Prazo_Entrega, T005_Validade_Proposta, T005_Observacao, T005_C004_Id) 
		VALUES ('', '', '', '', '1', CURDATE(), '{$g['usuarioAtual']}', '', 6, '', '', '', '', '', '{$g['empresaAtual']}');
EOT;
	mysql_query($sql);
	$_REQUEST['T005_Id'] = $g['mysqlLastId'];
}

include('inc.conf.php');

require_once("bibliotecas/classes/VEN002.php");
$VEN002 = new VEN002();


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
			$retorno = $VEN001->VEN001aplicarDescontoReais('T005', $T005_Id, 'T006', $_REQUEST['valorDescontoReais']);
		}

		//Desconto em percentual
		if (isset($_REQUEST['percentualDesconto'])) {
			$retorno = $VEN001->VEN001aplicarDesconto('T005', $T005_Id, 'T006', $_REQUEST['percentualDesconto'], 'tabela');
		}

		//Definir valor total
		if (isset($_REQUEST['valorTotal'])) {
			$retorno = $VEN001->VEN001aplicarDescontoTotal('T005', $T005_Id, 'T006', $_REQUEST['valorTotal']);
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
$percentualDesconto = gCorrigeNumero((($T005['T005_Valor_Desconto']+$T005['T005_Valor_Desconto_Manual'])/$T005['T005_Valor_Total_Produtos_Sem_Desconto'])*100, 1);
$percentualDesconto = "({$percentualDesconto} <span style=\"font-size: 10px;\">%</span>)";

//Restrição alterar pedido
$restricao = ($g['conf']->restricaoTravar('incluir', 'T006_Id', '', 'T005_Id', $T005_Id) === true && isset($g['c029Ids'][213])) ? '' : 'disabled="disabled"';

include('mobile/phone/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedido">
		<script>
			function imprimirPedido() {
				$.mobile.showPageLoadingMsg();
				$.getJSON('./phone_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&callback=?', function(request) {
					$.mobile.hidePageLoadingMsg();
					if (request.code == 'erro' || request.code == 'analise') {
						$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
					} else if (request.code) {
						$.mobile.showPageLoadingMsg();
						$.getJSON('./phone_func-ajax.php?opcaoAcao=ven002ImprimirPedido&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&callback=?', function(request) {
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
										$.getJSON('./phone_func-ajax.php?opcaoAcao=enviarParaAnalise&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&acao=imprimir&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
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
	    		$.getJSON('./phone_func-ajax.php?opcaoAcao=verificarParcelas&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&callback=?', function(request1) {
					$.mobile.hidePageLoadingMsg();
					if(request1.code){
						$.mobile.showPageLoadingMsg();
						$.getJSON('./phone_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&acao=enviarEmail&callback=?', function(request) {
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
												$.getJSON('./phone_func-ajax.php?opcaoAcao=enviarParaAnalise&T005_Id=' + encodeURIComponent("{$T005_Id}") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&acao=email&callback=?', function(request2) {
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
include('mobile/phone/inc.header.bar.php');
include('inc.header.page.php');

$tamanhoColuna = floor(100/3);
$tamanhoColuna -= 0.5;

echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-d" style="border: 1px solid #CCCCCC; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3); padding: 8px 0;">
                    <div class="ui-block-a" style="border-right: 2px solid #CCCCCC; text-align: center; width: {$tamanhoColuna}%;">
                        <h4 style="margin: 0px;">
                            Produtos R$
                        </h4>
                        <h3 style="margin: 0px;">
                            {$totalProdutos}
                        </h3>
                    </div>
                    <div class="ui-block-d" style="border-right: 2px solid #CCCCCC; text-align: center; width: {$tamanhoColuna}%;">
                        <h4 style="margin: 0px;">
                            Desconto R$
                        </h4>
						<h3 style="margin: 0px; text-decoration:underline;" onclick="\$.mobile.changePage( './pedidoGravarDescontoManual.php?T005_Id={$T005_Id}{$acao}', { transition: 'fade'} );" data-ajax="false">
                            {$totalDesconto} <font style="font-size:12px;font-weight:normal;">{$percentualDesconto}</font>
                        </h3>
                    </div>
                    <div class="ui-block-e" style="text-align: center; width: {$tamanhoColuna}%;">
                        <h4 style="margin: 0px;">
                            Total R$
                        </h4>
                        <h3 style="margin: 0px;">
                            {$totalGeral} <font style="font-size:16px;font-weight:normal;">{$alcadaNome}</font>
                        </h3>
                    </div>
                </div>
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div style="text-align: center;">
                		<div style="float: left;">
	                       	<a data-role="button" data-inline="true" data-transition="fade" onclick="imprimirPedido(); return false;" data-ajax="false" rel="external" target="_blank">Imprime</a>
				        </div>
	                    <div style="float: left;">
	                        <a data-inline="true" data-role="button" data-transition="fade" onclick="emailPedido(); return false;" data-ajax="false" data-iconpos="left">Mail</a>
				        </div>
                    	<div style="float: left;">
                    		<a data-role="button"data-inline="true" data-transition="slide"  id="adicionarProduto" onclick="$.mobile.changePage( './pedidoProcurarProduto.php?T005_Id={$T005_Id}{$acao}', { transition: 'slide'} );" data-ajax="false">Novo</a> 
				        </div>
                    </div>
                </div>
                <ul data-role="listview" style="margin-top: 1px;">
EOT;

// Produtos
$sql = <<<EOT
	SELECT
		T006_Id, T006_Codigo_Produto, T006_Descricao_Produto, D082_Marca, T006_Quantidade, T006_Quantidade_Estoque,T006_Valor_Preco_Unitario, T006_Valor_Total_Preco, D009_Id, D001_Id,D001_Flag_Pre_Cadastro, D009_Quantidade_Estoque_Liquido,T006_Flag_Cancelado
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
	$row['T006_Quantidade'] = gCorrigeNumero($row['T006_Quantidade']);
	$row['T006_Quantidade_Estoque'] = gCorrigeNumero($row['T006_Quantidade_Estoque']);
	$row['T006_Valor_Preco_Unitario'] = gCorrigeNumero($row['T006_Valor_Preco_Unitario']);
	$row['T006_Valor_Total_Preco'] = gCorrigeNumero($row['T006_Valor_Total_Preco']);
	$row['T006_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['T006_Descricao_Produto']} (PRÉ)" : $row['T006_Descricao_Produto']);

	if($row['T006_Flag_Cancelado']=='S'){
		$row['T006_Descricao_Produto'] = $row['T006_Descricao_Produto']." <font color='red'><b> CANCELADO</b></font>";
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
    echo <<<EOT
		<li data-theme="c" data-icon="false">
	    <a href="./pedidoDelProduto.php?T005_Id={$T005_Id}&D001_Id={$row['D001_Id']}&D009_Id={$row['D009_Id']}&T006_Id={$row['T006_Id']}{$acao}" data-ajax="false" data-transition="slide">
	    	<div style="width: 100%; white-space: nowrap; {$corLinha}">
				<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">{$row['T006_Codigo_Produto']}&nbsp;{$row['T006_Descricao_Produto']}</div>
				<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px;">Estoque: {$row['T006_Quantidade_Estoque']}&nbsp; - &nbsp;Qtde: {$row['T006_Quantidade']}</div>
				<div style="width: 100%; clear: both; text-align: left; margin: 0px;">Unit. R$: {$row['T006_Valor_Preco_Unitario']}&nbsp; - &nbsp;Total R$: {$row['T006_Valor_Total_Preco']}</div>
	    	</div>
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
	                <div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c" style="margin-top:20px;">
						<h3>Análise</h3>
							<div>
								<div data-role="fieldcontain" style="padding: 2px 8px;">
									<label for="C007_Primeiro_Nome">
					                    Analista
						            </label>
									<input id="C007_Primeiro_Nome" name="C007_Primeiro_Nome" placeholder="" value="{$rowAnalista['C007_Primeiro_Nome']}" type="text" />
								</div>
								<div data-role="fieldcontain" style="padding: 2px 8px;">
									<label for="T005_Flag_Status_pedido">
					                    Status
						            </label>
									<input id="T005_Flag_Status_pedido" name="T005_Flag_Status_pedido" placeholder="" value="{$statuspedido}" type="text" />
								</div>
								<div data-role="fieldcontain" style="padding: 2px 8px;">
									<label for="T005_Liberou_Alcada_Observacao_Nao_Liberado">
					                    Observação
						            </label>
									<textarea name="T005_Liberou_Alcada_Observacao_Nao_Liberado" id="T005_Liberou_Alcada_Observacao_Nao_Liberado">{$T005['T005_Liberou_Alcada_Observacao_Nao_Liberado']}</textarea>
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
EOT;

echo <<<EOT
		{$script}
    	});
    </script>
EOT;
