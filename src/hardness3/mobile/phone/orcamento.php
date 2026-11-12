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
		VALUES ('{$_REQUEST['D024_Id']}', '', '', '', '1', CURDATE(), '{$g['usuarioAtual']}', '', '6', '', '', '', '', '', '{$g['empresaAtual']}', '2', '{$_REQUEST['T003_Flag_Destino_Produto']}');
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
$percentualDesconto = gCorrigeNumero((($T003['T003_Valor_Desconto']+$T003['T003_Valor_Desconto_Manual'])/$T003['T003_Valor_Total_Produtos_Sem_Desconto'])*100, 1);
$percentualDesconto = "({$percentualDesconto} <span style=\"font-size: 10px;\">%</span>)";

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
			$retorno = $VEN001->VEN001aplicarDescontoReais('T003', $T003_Id, 'T004', $_REQUEST['valorDescontoReais']);
		}

		//Desconto em percentual
		if (isset($_REQUEST['percentualDesconto'])) {
			$retorno = $VEN001->VEN001aplicarDesconto('T003', $T003_Id, 'T004', $_REQUEST['percentualDesconto'], 'tabela');
		}

		//Definir valor total
		if (isset($_REQUEST['valorTotal'])) {
	    	$retorno = $VEN001->VEN001aplicarDescontoTotal('T003', $T003_Id, 'T004', $_REQUEST['valorTotal']);
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
	    		$.getJSON('./phone_func-ajax.php?opcaoAcao=verificarParcelas&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request1) {
					$.mobile.hidePageLoadingMsg();
					if(request1.code){
						$.mobile.showPageLoadingMsg();
						$.getJSON('./phone_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request) {
							$.mobile.hidePageLoadingMsg();
							if (request.code == 'erro' || request.code == 'analise') {
								$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
							} else if (request.code) {
								$.mobile.showPageLoadingMsg();
								$.getJSON('./phone_func-ajax.php?opcaoAcao=ven001ImprimirOrcamento&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request) {
									$.mobile.hidePageLoadingMsg();
									if(request.code === true){
										window.open(request.data);
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
												$.getJSON('./phone_func-ajax.php?opcaoAcao=enviarParaAnalise&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
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
	    		$.getJSON('./phone_func-ajax.php?opcaoAcao=verificarParcelas&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request1) {
					$.mobile.hidePageLoadingMsg();
					if(request1.code){
						$.mobile.showPageLoadingMsg();
						$.getJSON('./phone_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&callback=?', function(request) {
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
												$.getJSON('./phone_func-ajax.php?opcaoAcao=enviarParaAnalise&T003_Id=' + encodeURIComponent("{$T003_Id}") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
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

$tamanhoColuna = floor(100/3);
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
                        <h3 style="margin: 0px; text-decoration:underline;" onclick="\$.mobile.changePage( './orcamentoGravarDescontoManual.php?T003_Id={$T003_Id}', { transition: 'fade'} );" data-ajax="false">
                            {$totalDesconto} <font style="font-size:12px;font-weight:normal;">{$percentualDesconto}</font>
                        </h3>
                    </div>
                    <div class="ui-block-e" style="text-align: center; width: {$tamanhoColuna}%;">
                        <h4 style="margin: 0px;">
                            Total R$
                        </h4>
                        <h3 style="margin: 0px;">
                            {$totalGeral} <font style="font-size:12px;font-weight:normal;">{$alcadaNome}</font>
                        </h3>
                    </div>
                </div>
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div style="text-align: center;">
                		<div style="float: left;">
	                        <a data-role="button" data-inline="true" data-transition="fade" onclick="imprimirOrcamento(); return false;" data-ajax="false" rel="external" target="_blank">Imprime</a>
	                    </div>
	                    <div style="float: left;">
	                    	<a data-inline="true" data-role="button" data-transition="fade" onclick="emailOrcamento(); return false;" data-ajax="false">Mail</a>
                    	</div>
                    	<div style="float:left;">
                    		<a data-role="button"data-inline="true" data-transition="slide"  id="adicionarProduto" onclick="$.mobile.changePage( './orcamentoProcurarProduto.php?T003_Id={$T003_Id}', { transition: 'slide'} );" data-ajax="false">Novo</a> 
				        </div>
                    </div>
                </div>
                <ul data-role="listview" style="margin-top: 1px;">
EOT;
                // data-icon="plus" data-iconpos="notext"  margin-top: 4px; 

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
				$row['T004_Quantidade'] = gCorrigeNumero($row['T004_Quantidade']);
				$row['T004_Valor_Preco_Unitario'] = gCorrigeNumero($row['T004_Valor_Preco_Unitario']);
				$row['T004_Valor_Total_Preco'] = gCorrigeNumero($row['T004_Valor_Total_Preco']);
				$row['T004_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['T004_Descricao_Produto']} (PRÉ)" : $row['T004_Descricao_Produto']);

				if($row['T004_Flag_Cancelado']=='S'){
					$row['T004_Descricao_Produto'] = $row['T004_Descricao_Produto']." <font color='red'><b> CANCELADO</b></font>";
				}

				$row['D009_Quantidade_Estoque_Liquido'] = (empty($row['D009_Quantidade_Estoque_Liquido']) ? '0,00' : gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']));
				echo <<<EOT
					<li data-theme="c" data-icon="false">
					    <a class="clean" href="./orcamentoDelProduto.php?T003_Id={$T003_Id}&D001_Id={$row['D001_Id']}&D009_Id={$row['D009_Id']}&T004_Id={$row['T004_Id']}" data-ajax="false" data-transition="slide">
					    	<div style="width: 100%; white-space: nowrap; {$corLinha}">
			    				<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">{$row['T004_Codigo_Produto']}&nbsp;{$row['T004_Descricao_Produto']}</div>
			    				<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px;">Estoque: {$row['D009_Quantidade_Estoque_Liquido']}&nbsp; - &nbsp;Qtde: {$row['T004_Quantidade']}</div>
			    				<div style="width: 100%; clear: both; text-align: left; margin: 0px;">Unit. R$: {$row['T004_Valor_Preco_Unitario']}&nbsp; - &nbsp;Total R$: {$row['T004_Valor_Total_Preco']}</div>
					    	</div>
					    </a>
				    </li>
EOT;
			}
			echo "</ul>";
			$sqlAnalista = "SELECT C007_Id,C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$T003['T003_Liberou_Alcada_C007_Id']}'";
			$resAnalista = mysql_query($sqlAnalista);
			$rowAnalista = mysql_fetch_array($resAnalista);
			if (!empty($rowAnalista['C007_Id'])) {
				echo <<<EOT
	                <div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c" style="margin-top: 20px;">
						<h3>Análise</h3>
						<div>
							<div data-role="fieldcontain" style="padding: 2px 8px;">
								<label for="C007_Primeiro_Nome">
					                Analista
					            </label>
								<input id="C007_Primeiro_Nome" name="C007_Primeiro_Nome" placeholder="" value="{$rowAnalista['C007_Primeiro_Nome']}" type="text" />
							</div>
							<div data-role="fieldcontain" style="padding: 2px 8px;">
								<label for="T003_Flag_Status_Orcamento">
					                Status
					            </label>
								<input id="T003_Flag_Status_Orcamento" name="T003_Flag_Status_Orcamento" placeholder="" value="{$statusOrcamento}" type="text" />
							</div>
							<div data-role="fieldcontain" style="padding: 2px 8px;">
								<label for="T003_Liberou_Alcada_Observacao_Nao_Liberado">
					                Observação
					            </label>
								<textarea name="T003_Liberou_Alcada_Observacao_Nao_Liberado" id="T003_Liberou_Alcada_Observacao_Nao_Liberado">{$T003['T003_Liberou_Alcada_Observacao_Nao_Liberado']}</textarea>
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
mysql_free_result($result);
mysql_free_result($parcelas);
mysql_free_result($D024);
mysql_free_result($res);
mysql_free_result($res2);
mysql_free_result($resAnalista);
mysql_free_result($T003);






