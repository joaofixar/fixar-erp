<?php
namespace hardness;

global $g,$sys;

/**
 * B2B - Grid - Business to Business
 **/

require_once('prog/v2b2b/b2b001/grid_func-callback.php');
require_once('prog/v2b2b/b2b001/grid_func-js.php');
require_once('prog/crm/crm001/grid_func-callback.php');
// require_once('bibliotecas/funcoesCallback/');
// require_once('prog/b2b/b2b001/grid_cabecalho.php');

// --------------- Valores utilizados em todas as telas do B2B-------------------------
/**Trazer dados do cliente/contato*/
$sqlD024 = mysql_query("SELECT D024_Nome_Empresa,
							   D013_Nome_Contato, 
							   C004_Nome_Abreviado 
						  FROM D024 
					 LEFT JOIN D013 ON D013_D024_Id=D024_Id AND D013_Id='{$g['clienteAtualContato']}'
					 LEFT JOIN C004 ON D024_C004_Id=C004_Id 
					 	 WHERE D024_Id='{$g['clienteAtual']}'");
$resD024 = mysql_fetch_array($sqlD024);

//Caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
if(strpos($resD024['D013_Nome_Contato'],"@")){
	$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
}else{
	//caso contrario pega o primeiro e ultimo nome da pessoa
	$temp = explode(" ",$resD024['D013_Nome_Contato']);
	$resD024['D013_Nome_Contato'] = $temp[0];
}
$resD024['D024_Nome_Empresa'] = substr($resD024['D024_Nome_Empresa'],0,20);

/*Trazer total de itens do carrinho*/
$T269 = mysql_query("SELECT count(*) FROM T269 LEFT JOIN D001 ON D001_Id=T269_D001_Id LEFT JOIN D003 ON D003_Id=D001_D003_Id WHERE T269_D024_Id='{$g['clienteAtual']}'");
$T269_Itens = mysql_fetch_array($T269);

//Variaveis usadas em todas as telas
$BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
$MostrarPreco = $g['C031']['HabilitarPreco'];
$MostrarPedidos=$g['C031']['MostrarPedidos'];
$MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
$MostrarBoletos=$g['C031']['MostrarBoletos'];
$MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
$tipoLoginUsuarioB2B=$g['tipoLoginUsuarioB2B'];
$PermitirVendedorEditarPrecoB2B=$g['C031']['PermitirVendedorEditarPrecoB2B'];
$PermitirVendedorExternoEditarPrecoB2B=$g['C031']['PermitirVendedorExternoEditarPrecoB2B'];
$PermitirRepresentanteEditarPrecoB2B=$g['C031']['PermitirRepresentanteEditarPrecoB2B'];
$logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}";


switch($g['r'][3]){
	
	case 'gridB2BPrincipal':
		global $g,$sys;
		$buscaProduto = $_GET['buscaProduto'];
	
		// $BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		// $MostrarPreco = $g['C031']['HabilitarPreco'];
		// $MostrarPedidos=$g['C031']['MostrarPedidos'];
		// $MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		// $MostrarBoletos=$g['C031']['MostrarBoletos'];
		// $MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		// $tipoLoginUsuarioB2B=$g['C031']['tipoLoginUsuarioB2B'];

		$pagina 	= (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url 	    = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 12;
		$inicio 	= ($quantidade * $pagina) - $quantidade;

		if(!empty($buscaProduto)){
			$buscaProduto = str_replace('/', ' ', $buscaProduto);
			$filtro = "and D001_Descricao_Produto like '%".str_replace(' ','%',trim($buscaProduto))."%'";
		}

		//Paginação
		$totalProdutos=mysql_query("SELECT count(*) 
							 FROM D009
						left join D049 on D049_Id=D009_D049_Id 
						left join D001 on D001_Id=D049_D001_Id 
		                left join D003 on D003_Id=D001_D003_Id 
							where D001_Flag_Ativo='S' 
							  and D001_Flag_Ecommerce='S'
			  				  and D009_C004_Id='{$g['empresaAtual']}'
							      {$filtro}");

		$mTotalProdutos = mysql_fetch_array($totalProdutos);	
		$totalPagina 	= ceil($mTotalProdutos[0]/$quantidade);
		$indiceInicio 	= ($pagina - 1) * $quantidade;

		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id produto', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T269_Id', 'colunaTitulo' => 'T269_Id'),
            //array('colunaValor' => 'D009_Quantidade_Estoque_B2B', 'colunaTitulo' => 'Estoque'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque'),
			array('colunaValor' => 'D003_Imagem', 'colunaTitulo' => 'Imagem', 'callback' => 'mostrarImagemB2B'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descricao','callback' => 'retornaDescricaoProdutoB2B'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preco'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id produto', 'colunaExibir' => false),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
        );
	
		/**
		* Definição da SQL
		*/
		$from = 'D009';
		$extra = <<<EOT
			left join D049 on D049_Id=D009_D049_Id 
			left join D082 on D082_Id=D049_D082_Id 
			left join D001 on D001_Id=D049_D001_Id 
			left join D003 on D003_Id=D001_D003_Id 
			left join T269 on T269_D009_Id=D009_Id and T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'		
			where D001_Flag_Ativo='S' 
			and D001_Flag_Ecommerce='S'
			and D009_C004_Id='{$g['empresaAtual']}'
			{$filtro}
			order by if(D009_Quantidade_Estoque_B2B>0,0,1),replace(D001_Descricao_Produto,D003_Nome_Linha,'') 
			LIMIT {$indiceInicio},{$quantidade}
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,"$quantidade.$inicio");

		// /**
		//  * Trazer total de itens do carrinho
		//  */
		// $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		// $T269_Itens = mysql_fetch_array($T269);

		// /**
		//  * Trazer dados do cliente
		//  */
		// $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato, C004_Nome_Abreviado FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id LEFT JOIN C004 ON D024_C004_Id=C004_Id WHERE D013_Id='{$g['clienteAtualContato']}'");
		// $resD024 = mysql_fetch_array($sqlD024);
		
		// //manipular nome do cliente
		// //caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		// if(strpos($resD024['D013_Nome_Contato'],"@")){
		// 	$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		// }else{
		// 	//caso contrario pega o primeiro e ultimo nome da pessoa
		// 	$temp = explode(" ",$resD024['D013_Nome_Contato']);
		// 	$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
		// }

		$primeira = $pagina-2;
		$ultima   = $pagina+2;

		//verificação da primeira e ultima pagina para não ficar acima do numero total de paginas ou menor que 0
		if($ultima>$totalPagina){
			$ultima = $totalPagina;
			$primeira = $ultima - 4;
		}

		if($primeira<=0){
			$primeira = 1;
			$ultima = $primeira+4;
		}

		if($ultima>$totalPagina){
			$ultima = $totalPagina;
		}

		if (!isset($pagina)){
			$pagina = 1;
		}

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('gridEstoque', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('mostrarCarrinho', 'S');
		$g['smarty']->assign('mostrarPreco', $MostrarPreco);
		$g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
		$g['smarty']->assign('buscaProduto', $buscaProduto);
		$g['smarty']->assign('empresaAtualNome', $resD024['C004_Nome_Abreviado']);
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('clienteAtualNome', $resD024['D024_Nome_Empresa']);
		$g['smarty']->assign('logo',$logo);

		//variaveis do menu
		$g['smarty']->assign('mostrarPedidos', $MostrarPedidos);
		$g['smarty']->assign('mostrarOrcamentos', $MostrarOrcamentos);
		$g['smarty']->assign('mostrarBoletos', $MostrarBoletos);
		$g['smarty']->assign('mostrarNotasFiscais', $MostrarNotasFiscais);

		//variaveis para paginação 
		$g['smarty']->assign('pagina',$pagina);
		$g['smarty']->assign('quantidade',$quantidade);
		$g['smarty']->assign('inicio',$inicio);
		$g['smarty']->assign('totalPagina',$totalPagina);
		$g['smarty']->assign('url',$url);
		$g['smarty']->assign('ultima',$ultima);
		$g['smarty']->assign('primeira',$primeira);
		$g['smarty']->assign('filtro',$filtro);

		// $g['smarty']->display('gridB2Bcols.tpl');
		$g['smarty']->display('v2ProdutosB2B.tpl');
		// $g['smarty']->display('ProdutosB2B.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('.comprar').click(function(data) {
					var id = $(this).attr('id').replace(/D009_Id-/, '');
					var preco = $(this).attr('preco');
					var objeto = $(this);
					$.getJSON('/v2b2b/b2b001/grid_func-ajax/incluir?ajax=true&preco=' + preco + '&D009_Id=' + id + '&callback=?', function(request) {
						$("#Carrinho").text(request.data);
						$(objeto).text( "Carrinho");
						$(objeto).css('color', '#374a56');
						$(objeto).css('background-color', '#f6c23e');
						$(objeto).css('border', '1px solid #f6c23e');
					});
				});
				$('#botaoBuscar').click(function(data) {
					var buscaProduto = $('#buscaProduto').val();
					window.location = '/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true&buscaProduto='+buscaProduto;
				});
				$('#buscaProduto').keypress(function (ev) {
					var keycode = (ev.keyCode ? ev.keyCode : ev.which);
					if (keycode == '13') {
						var buscaProduto = $('#buscaProduto').val();
						window.location = '/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true&buscaProduto='+buscaProduto;
					}
				});

				$('#buscaProduto').focus();
				$('#buscaProduto').select();

				$('#divCarrinho').click(function() {					
					window.location.href = "/v2b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
				});
			} </script></div>
EOT;

	break;

	case 'gridB2BCarrinho':
		global $g;
		// $BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		// $MostrarPreco = $g['C031']['HabilitarPreco'];
		// $MostrarPedidos=$g['C031']['MostrarPedidos'];
		// $MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		// $MostrarBoletos=$g['C031']['MostrarBoletos'];
		// $MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		// $tipoLoginUsuarioB2B=$g['tipoLoginUsuarioB2B'];
		// $PermitirVendedorEditarPrecoB2B=$g['C031']['PermitirVendedorEditarPrecoB2B'];
		// $PermitirVendedorExternoEditarPrecoB2B=$g['C031']['PermitirVendedorExternoEditarPrecoB2B'];
		// $PermitirRepresentanteEditarPrecoB2B=$g['C031']['PermitirRepresentanteEditarPrecoB2B'];

		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id produto', 'colunaExibir' => false),
            array('colunaValor' => 'T269_Id', 'colunaTitulo' => 'Id produto', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D003_Imagem', 'colunaTitulo' => 'Imagem', 'callback' => 'mostrarImagemPequenaB2B'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descricao','callback' => 'retornaDescricaoProdutoB2B'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaExibir' => false),
			array('colunaValor' => 'T269_Preco', 'colunaTitulo' => 'Preco', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T269_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => 0),
			array('colunaValor' => 'T269_Preco_Total', 'colunaTitulo' => 'Subtotal', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id produto D001', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T269';
		$extra = <<<EOT
			left join D009 on D009_Id=T269_D009_Id 
			left join D049 on D049_Id=D009_D049_Id 
			left join D082 on D082_Id=D049_D082_Id 
			left join D001 on D001_Id=D049_D001_Id 
			left join D003 on D003_Id=D001_D003_Id 
			WHERE T269_D024_Id='{$g['clienteAtual']}' AND T269_D013_Id='{$g['clienteAtualContato']}'	
			GROUP BY D001_Id 
			ORDER BY replace(D001_Descricao_Produto,D003_Nome_Linha,'')
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		// /**
		//  * Trazer total de itens do carrinho
		//  */
		// $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		// $T269_Itens = mysql_fetch_array($T269);

		/**
		 * Somatória do subtotal
		 */
		$T269 = mysql_query("select sum(T269_Preco_Total) as subtotal from T269 left join D009 on D009_Id=T269_D009_Id where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		$mT269 = mysql_fetch_array($T269);
		$subtotal = 'R$ '. gCorrigeNumero($mT269['subtotal']);

		// /**
		//  * Trazer dados do cliente
		//  */
		// $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato, C004_Nome_Abreviado FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id LEFT JOIN C004 ON D024_C004_Id=C004_Id WHERE D013_Id='{$g['clienteAtualContato']}'");
		// $resD024 = mysql_fetch_array($sqlD024);

		// //manipular nome do cliente
		// //caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		// if(strpos($resD024['D013_Nome_Contato'],"@")){
		// 	$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		// }else{
		// 	//caso contrario pega o primeiro e ultimo nome da pessoa
		// 	$temp = explode(" ",$resD024['D013_Nome_Contato']);
		// 	$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
		// }

		/**
		* Geração: Grid
		*/

		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('botaoOrcaPedido', $BotaoPedidoOrcamento);
		$g['smarty']->assign('mostrarCarrinho', 'S');
		$g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
		$g['smarty']->assign('mostrarPreco', $MostrarPreco);
		$g['smarty']->assign('tipoLoginUsuarioB2B', $tipoLoginUsuarioB2B);
		$g['smarty']->assign('PermitirVendedorEditarPrecoB2B', $PermitirVendedorEditarPrecoB2B);
		$g['smarty']->assign('PermitirVendedorExternoEditarPrecoB2B', $PermitirVendedorExternoEditarPrecoB2B);
		$g['smarty']->assign('PermitirRepresentanteEditarPrecoB2B', $PermitirRepresentanteEditarPrecoB2B);
		$g['smarty']->assign('empresaAtualNome', $resD024['C004_Nome_Abreviado']);
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('clienteAtualNome', $resD024['D024_Nome_Empresa']);
		$g['smarty']->assign('subtotalCarrinho',$subtotal);
		$g['smarty']->assign('logo',$logo);

		//variaveis do menu
		$g['smarty']->assign('mostrarPedidos', $MostrarPedidos);
		$g['smarty']->assign('mostrarOrcamentos', $MostrarOrcamentos);
		$g['smarty']->assign('mostrarBoletos', $MostrarBoletos);
		$g['smarty']->assign('mostrarNotasFiscais', $MostrarNotasFiscais);
	

		// $g['smarty']->display('CarrinhoB2B.tpl');
		$g['smarty']->display('v2CarrinhoB2B.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$(function () {
					$("#container #excluir").click(function() {
						var id = $(this).attr('idProduto').replace(/T269_Id-/, '');
						var row = $(this).attr('row');
						var objeto = $(this);
					  	$( function() {
							$( "#dialog-confirmEx" ).dialog({
								resizable: false,
								height: "auto",
								width: 300,
								modal: true,
								buttons: {
									"Sim": function() {
										$.getJSON('/v2b2b/b2b001/grid_func-ajax/excluir?ajax=true&excluir=' + id +'&callback=?', function(request) {	
											$("#Carrinho").text(request.data['carrinho']);
											if(request.data['carrinho']==0){
												$("#botao").text("");
											}
											$('#subtotalCarrinho').text(request.data['subTotal']);
											$('#totalCarrinho').text(request.data['Total']);
											$("#"+row).animate({'line-height':0},400).hide(1);
											 location.reload(true);	
										});															  
										$( this ).dialog( "close" );
									},
									Não: function() {
										$( this ).dialog( "close" );
									}
								}
							});
						});
					});
				});

				$(function () {
					$("#gerarPedido").click(function() {
						var observacao = $('#inputObservacoes').val();
						$( function() {
						    $( "#dialog-confirmP" ).dialog({
								resizable: false,
								height: "auto",
								width: 300,
								modal: true,
								buttons: {
								"Gerar": function() {
									$("#gerarPedido").prop("disabled", true);
									$("#gerarPedido").text('Aguarde, gerando pedido...');
									    $.getJSON('/v2b2b/b2b001/grid_func-ajax/gerarPedido?ajax=true&observacaoComercial=' + encodeURIComponent(observacao) + '&callback=?', function(request) {
										if ("{$g['C031']['FaturarPedidoAutomaticamenteB2B']}" == 'S') {
											faturarPedido(request.T005_Id); 
										}
								    	if(request.data==true){
								    		$( "#mensagemPedidoComEmail" ).dialog({ 
												resizable: false,
											    height: "auto",
											    width: 300,
											    modal: true,
												buttons: {
											        Ok: function() {
											          $( this ).dialog( "close" );
											          window.location.href = "/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true";	
											        }
									  			}
								  			});
								    	}else{
								    		$( "#mensagemPedidoSemEmail" ).dialog({ 
												resizable: false,
											    height: "auto",
											    width: 300,
											    modal: true,
												buttons: {
											        Ok: function() {
											          $( this ).dialog( "close" );
											          window.location.href = "/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true";	
											        }
									  			}
									  		});
										}	
									});
									$( this ).dialog( "close" );
								    }
								},
								Cancelar: function() {
								  	$( this ).dialog( "close" );
								}	
						    });
						});
					});
				});

				$(function () {
				  $("#gerarOrcamento").click(function() {
					var observacao = $('#inputObservacoes').val();
				    $( function() {
					    $( "#dialog-confirmO" ).dialog({
					      resizable: false,
					      height: "auto",
					      width: 300,
					      modal: true,
					      buttons: {
					        "Gerar": function() {
								$("#gerarOrcamento").prop("disabled", true);
								$("#gerarOrcamento").text('Aguarde, gerando orçamento...');
								$("#gerarOrcamento").css("background-color","green");
							    $.getJSON('/v2b2b/b2b001/grid_func-ajax/gerarOrcamento?ajax=true&observacaoComercial=' + encodeURIComponent(observacao) + '&callback=?', function(request) {
							    	if(request.data==true){
							    		$( "#mensagemOrcamentoComEmail" ).dialog({ 
											resizable: false,
										    height: "auto",
										    width: 300,
										    modal: true,
											buttons: {
										        Ok: function() {
										          $( this ).dialog( "close" );
										          window.location.href = "/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true";	
										        }
								  			}
							  			});
							    	}else{
							    		$( "#mensagemOrcamentoSemEmail" ).dialog({ 
											resizable: false,
										    height: "auto",
										    width: 300,
										    modal: true,
											buttons: {
										        Ok: function() {
										          $( this ).dialog( "close" );
										          window.location.href = "/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true";	
										        }
								  			}
								  		});
							    	}	
								});
								$( this ).dialog( "close" );
							    }
							},
								Cancelar: function() {
								  	$( this ).dialog( "close" );
								}	
						    });
						});
					});
				});

				$('#container input').focusout( function(data) {
					var idProduto = $(this).attr('idProduto');
					var tipoInput = $(this).attr('tipoInput');
					var id = $(this).attr('idProduto').replace(/T269_Id-/, '');

					if(tipoInput=="qtde"){
						var quantidade = $('#quantidade'+idProduto).val();	
						$.getJSON('/v2b2b/b2b001/grid_func-ajax/gravarQuantidade?ajax=true&T269_Id=' + id + '&quantidade=' + quantidade + '&callback=?', function(request) {
							$('#subtotalProduto'+idProduto).text(request.data['subTotalUnit']);
							$('#subtotalCarrinho').text(request.data['subTotal']);
							$('#totalCarrinho').text(request.data['Total']);
						});
					}else if(tipoInput=="preco"){
						var novoPreco = $('#precoInput'+idProduto).val();
						$.getJSON('/v2b2b/b2b001/grid_func-ajax/alterarPreco?ajax=true&T269_Id=' + id + '&novoPreco=' + novoPreco + '&callback=?', function(request) {
							$('#subtotalProduto'+idProduto).text(request.data['subTotalUnit']);
							$('#subtotalCarrinho').text(request.data['subTotal']);
							$('#totalCarrinho').text(request.data['Total']);
							$('#precoInput'+idProduto).val(request.data['precoNovo']);
						});
					}
				});
				
				$('#divCarrinho').click( function() {
					window.location.href = "/v2b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
				});
				$('#maisProdutos').click( function() {					
					window.location.href = "/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true";				
				});

                function faturarPedido(r_acaoId) {
				    $.getJSON('/ven/ven002/form_func-ajax/validarCamposNFe/?ajax=true&T005_Id=' + encodeURIComponent(r_acaoId) + '&callback=?', function(request) {
				    	if (request.code === false) {
							alert('PEDIDO NÃO FOI FATURADO! ' + request.data);
						}else if(request.code){
						    $.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&T005_Id=' + encodeURIComponent(r_acaoId) + '&acao=faturar&callback=?', function(request) {
								if (request.code === true) {
										$.getJSON('/ven/ven002/form_func-ajax/faturarPedido/?ajax=true&T005_Id=' + encodeURIComponent(r_acaoId) + '&callback=?', function(request) {
											if (request.code === false){
												alert('PEDIDO NÃO FOI FATURADO! ' + request.data);
											}
										});
								} else {
									$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&T005_Id=' + encodeURIComponent(r_acaoId) + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=faturar' + '&callback=?', function(request2) {														
										alert('PEDIDO NÃO FOI FATURADO! ' + request.data);
									});
								}
							});
						}
					});
				}				

	         } </script></div>
	
EOT;

		echo <<<EOT
			<div id="dialog-confirmP" title="Gerar Pedido?" style="display:none;">
				<p>Deseja gerar pedido?</p>
			</div>
			<div id="dialog-confirmO" title="Gerar Orçamento?" style="display:none;">
				<p>Deseja gerar orçamento?</p>
			</div>
			<div id="dialog-confirmEx" title="Excluir" style="display:none;">
				<p>Deseja realmente excluir?</p>
			</div>

			<div id="mensagemPedidoComEmail" title="Pedido gerado" style="display:none;">
				<p><strong>Pedido gerado com sucesso</strong></p>
				<p style="color:green;font-size:12px;">*Email de confirmação enviado com sucesso!</p>
			</div>

			<div id="mensagemPedidoSemEmail" title="Pedido gerado" style="display:none;">
				<p><strong>Pedido gerado com sucesso</strong></p>
				<p style="color: var(--BGVermelho);font-size:12px;">*Não foi possível enviar o email de confirmação.</p>
			</div>

			<div id="mensagemOrcamentoComEmail" title="Orçamento gerado" style="display:none;">
				<p><strong>Orçamento gerado com sucesso</strong></p>
				<p style="color:green;font-size:12px;">*Email de confirmação enviado com sucesso!</p>
			</div>
			<div id="mensagemOrcamentoSemEmail" title="Orçamento gerado" style="display:none;">
				<p><strong>Orçamento gerado com sucesso</strong></p>
				<p style="color: var(--BGVermelho);font-size:12px;">*Não foi possível enviar o email de confirmação.</p>
			</div>
EOT;
	break;

	case 'gridB2BPedidos':
		global $g;
		// $BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		// $MostrarPreco = $g['C031']['HabilitarPreco'];
		// $MostrarPedidos=$g['C031']['MostrarPedidos'];
		// $MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		// $MostrarBoletos=$g['C031']['MostrarBoletos'];
		// $MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		// $tipoLoginUsuarioB2B=$g['C031']['tipoLoginUsuarioB2B'];

		$pagina		= (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url 		= substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 15;
		$inicio     = ($quantidade * $pagina) - $quantidade;

		$where = '';
		if($g['clienteAtualContato'] == '99999' AND $g['clienteAtualVendedor'] > 0){
			$where = "  AND D024_Id='{$g['clienteAtual']}'
			 	        AND T005_C007_Id_Vendedor_Interno = " . $g['clienteAtualVendedor'];
		}else{
			$where = " AND (D013_Id='{$g['clienteAtualContato']}') ";
		}

		//paginação
		$totalPedidos= mysql_query("SELECT count(*) 
									  FROM T005 
								 LEFT JOIN D024 on D024_Id=T005_D024_Id
								 LEFT JOIN D013 on D013_D024_Id=D024_Id
								 LEFT JOIN T003 on T003_Id=T005_T003_Id
								 LEFT JOIN C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
								 LEFT JOIN T007 on T007_T005_Id=T005_Id
									 WHERE (T005_C004_Id='{$g['empresaAtual']}') 
									       {$where} 
									   AND T005_Flag_Status!=8
								  GROUP BY T005_Id");			
			
		$mTotalPedidos = mysql_num_rows($totalPedidos);
		$totalPagina   = ceil($mTotalPedidos/$quantidade);
		$indiceInicio  = ($pagina - 1) * $quantidade;

		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true), 
			array('colunaValor' => 'T005_T003_Id', 'colunaTitulo' => 'Orçamento'), 
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ",") as NF', 'colunaTitulo' => 'NF', 'callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'T005_Ordem_Compra', 'colunaTitulo' => 'OC.  Cliente', 'colunaAlinhar' => 'center', 'colunaEditar' => true), 
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'), 
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora', 'colunaExibir' => true),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as Status', 'colunaTitulo' => 'Status', 'colunaExibir' => true),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left'),
		);

		/**
		* Definição da SQL
		*/

		$from = 'T005';
		$extra = <<<EOT
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			LEFT JOIN D013 on D013_D024_Id=D024_Id
			LEFT JOIN T003 on T003_Id=T005_T003_Id
			LEFT JOIN D022 on D022_Id=T005_D022_Id
			LEFT JOIN C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
			LEFT JOIN C007 as Liberou on Liberou.C007_Id=T005_Liberou_Alcada_C007_Id
			LEFT JOIN C007 as LiberouOrcamento on LiberouOrcamento.C007_Id=T003_Liberou_Alcada_C007_Id
			LEFT JOIN C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			LEFT JOIN C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
			LEFT JOIN C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
			LEFT JOIN T007 on T007_T005_Id=T005_Id
			WHERE (T005_C004_Id={$g['empresaAtual']} AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null))
			{$where} 
			AND T005_Flag_Status!=8
			GROUP BY T005_Id 
			ORDER BY T005_Data_Emissao DESC, T005_Id DESC
			LIMIT {$indiceInicio},{$quantidade}
EOT;

			$primeira    = $pagina-2;
			$ultima      = $pagina+2;
		
			if($totalPagina<=0){
				$totalPagina=1;
			}
			//verificação da primeira e ultima pagina para não ficar acima do numero total de paginas ou menor que 0
			if($ultima>$totalPagina){
				$ultima = $totalPagina;
				$primeira = $ultima - 4;
			}

			if($primeira<=0){
				$primeira = 1;
				$ultima = $primeira+4;
			}

			if($ultima>$totalPagina){
				$ultima = $totalPagina;
			}

			if (!isset($pagina)){
				$pagina = 1;
			}

			/**
			* Geração: Monta o SQL e retorna o dados
			*/
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,"$quantidade.$inicio");
					// die($geradoSql);
			// /**
			//  * Trazer total de itens do carrinho
			//  */
			//  $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
			//  $T269_Itens = mysql_fetch_array($T269);

			// /**
			//  * Trazer dados do cliente
			//  */
			// $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato, C004_Nome_Abreviado FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id LEFT JOIN C004 ON D024_C004_Id=C004_Id WHERE D013_Id='{$g['clienteAtualContato']}'");
			// $resD024 = mysql_fetch_array($sqlD024);
	 
			// //manipular nome do cliente
			// //caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
			// if(strpos($resD024['D013_Nome_Contato'],"@")){
			// 	$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
			// }else{
			// 	//caso contrario pega o primeiro e ultimo nome da pessoa
			// 	$temp = explode(" ",$resD024['D013_Nome_Contato']);
			// 	$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
			// }

			$titulo = "PEDIDOS";
			/**
			* Geração: Grid
			*/
			$gridTabela = $g['ui']->grid($grid, $geradoDados);
			$g['smarty']->assign('thead', $gridTabela['thead']);
			$g['smarty']->assign('tbody', $gridTabela['tbody']);
			$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			$g['smarty']->assign('gridId', md5($g['r_']));
			$g['smarty']->assign('mostrarCarrinho', 'N');
			$g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
			$g['smarty']->assign('mostrarPreco', $MostrarPreco);
			$g['smarty']->assign('empresaAtualNome', $resD024['C004_Nome_Abreviado']);
			$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
			$g['smarty']->assign('clienteAtualNome', $resD024['D024_Nome_Empresa']);
			$g['smarty']->assign('logo',$logo);
			$g['smarty']->assign('titulo',$titulo);

			//variaveis do menu
			$g['smarty']->assign('mostrarPedidos', $MostrarPedidos);
			$g['smarty']->assign('mostrarOrcamentos', $MostrarOrcamentos);
			$g['smarty']->assign('mostrarBoletos', $MostrarBoletos);
			$g['smarty']->assign('mostrarNotasFiscais', $MostrarNotasFiscais);
			
			//variaveis para paginação 
			$g['smarty']->assign('pagina',$pagina);
			$g['smarty']->assign('quantidade',$quantidade);
			$g['smarty']->assign('inicio',$inicio);
			$g['smarty']->assign('totalPagina',$totalPagina);
			$g['smarty']->assign('url',$url);
			$g['smarty']->assign('ultima',$ultima);
			$g['smarty']->assign('primeira',$primeira);
			$g['smarty']->assign('filtro',$filtro);
			$g['smarty']->assign('qtdeLinhas',$mTotalPedidos);

			//chamando o template do smarty
			// $g['smarty']->display('gridB2BTemplate.tpl');
			$g['smarty']->display('v2PedidosB2B.tpl');

			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#divCarrinho').click( function() {					
						window.location.href = "/v2b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
					});
				} </script></div>
EOT;
	break;

	case 'gridB2BOrcamentos':
		global $g;
		// $BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		// $MostrarPreco = $g['C031']['HabilitarPreco'];
		// $MostrarPedidos=$g['C031']['MostrarPedidos'];
		// $MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		// $MostrarBoletos=$g['C031']['MostrarBoletos'];
		// $MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		// $tipoLoginUsuarioB2B=$g['C031']['tipoLoginUsuarioB2B'];

		$pagina 	= (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url 		= substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 15;
		$inicio 	= ($quantidade * $pagina) - $quantidade;

		$where = '';
		if($g['clienteAtualContato'] == '99999' AND $g['clienteAtualVendedor'] > 0){
			$where = "  AND D024_Id='{$g['clienteAtual']}'
			 	        AND T003_C007_Id_Vendedor_Interno = " . $g['clienteAtualVendedor'];
			
		}else{
			$where = " AND (D013_Id='{$g['clienteAtualContato']}') ";
		}

		//paginação
		$totalOrcamentos= mysql_query("SELECT count(*) 
										 FROM  T003
									LEFT JOIN D024 on D024_Id=T003_D024_Id
									LEFT JOIN D013 on D013_D024_Id=D024_Id
									LEFT JOIN C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
									LEFT JOIN T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
									LEFT JOIN T007 on T007_T005_Id=T005_Id
										WHERE T003_C004_Id='{$g['empresaAtual']}' 
									  	      {$where} 
							  		 GROUP BY T003_Id 
									 ORDER BY T003_Id DESC");

		$mTotalOrcamentos = mysql_num_rows($totalOrcamentos);
		$totalPagina      = ceil($mTotalOrcamentos/$quantidade);
		$indiceInicio     = ($pagina - 1) * $quantidade;
		
		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'idPedido', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right','callback' => 'valorTotalOrcamentoB2B'),
			array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - R$ ",T005_Valor_Total) separator ", <br />") as Pedido', 'colunaTitulo' => 'Pedido'),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S'),
			array('colunaValor' => 'IFNULL(T005_Status_Pedido (T005_Flag_Status, 1), "")AS statuspedido', 'colunaTitulo' => 'statusPedido'),
			array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")) separator ", <br />") as Pedido', 'colunaTitulo' => 'infoPedido'),
		);

		/**
		* Definição da SQL
		*/

		$from = 'T003';
		$extra = <<<EOT
			LEFT JOIN D024 on D024_Id=T003_D024_Id
			LEFT JOIN D013 on D013_D024_Id=D024_Id
			LEFT JOIN C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			LEFT JOIN T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
			LEFT JOIN T007 on T007_T005_Id=T005_Id
			WHERE T003_C004_Id='{$g['empresaAtual']}' 
			{$where} 
			GROUP BY T003_Id 
			ORDER BY T003_Id DESC 
			LIMIT {$indiceInicio},{$quantidade}
EOT;

		$primeira    = $pagina-2;
		$ultima      = $pagina+2;
		
		if($totalPagina<=0){
			$totalPagina=1;
		}
		
		//verificação da primeira e ultima pagina para não ficar acima do numero total de paginas ou menor que 0
		if($ultima>$totalPagina){
			$ultima = $totalPagina;
			$primeira = $ultima - 4;
		}

		if($primeira<=0){
			$primeira = 1;
			$ultima = $primeira+4;
		}

		if($ultima>$totalPagina){
			$ultima = $totalPagina;
		}

		if (!isset($pagina)){
			$pagina = 1;
		}

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,"$quantidade.$inicio");

		// /**
		//  * Trazer total de itens do carrinho
		//  */
		//  $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
		//  $T269_Itens = mysql_fetch_array($T269);
 
		// /**
		//  * Trazer dados do cliente
		//  */
		// $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato, C004_Nome_Abreviado FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id LEFT JOIN C004 ON D024_C004_Id=C004_Id WHERE D013_Id='{$g['clienteAtualContato']}'");
		// $resD024 = mysql_fetch_array($sqlD024);
 
		// //manipular nome do cliente
		// //caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		// if(strpos($resD024['D013_Nome_Contato'],"@")){
		// 	$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		// }else{
		// 	//caso contrario pega o primeiro e ultimo nome da pessoa
		// 	$temp = explode(" ",$resD024['D013_Nome_Contato']);
		// 	$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
		// }

		$titulo="ORÇAMENTOS";

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
		$g['smarty']->assign('empresaAtualNome', $resD024['C004_Nome_Abreviado']);
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('clienteAtualNome', $resD024['D024_Nome_Empresa']);
		$g['smarty']->assign('logo',$logo);
		$g['smarty']->assign('titulo',$titulo);

		//variaveis do menu
		$g['smarty']->assign('mostrarPedidos', $MostrarPedidos);
		$g['smarty']->assign('mostrarOrcamentos', $MostrarOrcamentos);
		$g['smarty']->assign('mostrarBoletos', $MostrarBoletos);
		$g['smarty']->assign('mostrarNotasFiscais', $MostrarNotasFiscais);
	
		//variaveis para paginação 
		$g['smarty']->assign('pagina',$pagina);
		$g['smarty']->assign('quantidade',$quantidade);
		$g['smarty']->assign('inicio',$inicio);
		$g['smarty']->assign('totalPagina',$totalPagina);
		$g['smarty']->assign('url',$url);
		$g['smarty']->assign('ultima',$ultima);
		$g['smarty']->assign('primeira',$primeira);
		$g['smarty']->assign('filtro',$filtro);
		$g['smarty']->assign('qtdeLinhas',$mTotalOrcamentos);

		// $g['smarty']->display('gridB2BTemplate.tpl');
		$g['smarty']->display('v2OrcamentoB2B.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#divCarrinho').click( function() {					
					window.location.href = "/v2b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
				});
			} </script></div>
EOT;

	break;

	case 'gridB2BNotasFiscais':
		global $g;
		// $BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		// $MostrarPreco = $g['C031']['HabilitarPreco'];
		// $MostrarPedidos=$g['C031']['MostrarPedidos'];
		// $MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		// $MostrarBoletos=$g['C031']['MostrarBoletos'];
		// $MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		// $tipoLoginUsuarioB2B=$g['C031']['tipoLoginUsuarioB2B'];

		$pagina 	= (isset($_GET['pagina'])) ? (int)$_GET['pagina'] : 1;
		$url 		= substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 15;
		$inicio 	= ($quantidade * $pagina) - $quantidade;

		$where = '';
		if($g['clienteAtualContato'] == '99999' AND $g['clienteAtualVendedor'] > 0){
			$where = "  AND T007_C007_Id_Vendedor_Interno = " . $g['clienteAtualVendedor'];
		}else{
			$where = " AND (D013_Id='{$g['clienteAtualContato']}') ";
		}

		
		//paginação
		$totalNotasFiscais= mysql_query("SELECT count(*) 
										   FROM T007
									  LEFT JOIN T005 on T005_Id=T007_T005_Id
									  LEFT JOIN D013 as Pedido on Pedido.D013_Id=T005_D013_Id
									  LEFT JOIN D024 on D024_Id=T007_D024_Id
									  LEFT JOIN D006 on D006_Id=T007_D006_Id
									  LEFT JOIN C007 as vendedor on vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
									  LEFT JOIN D022 on D022_Id=T007_D022_Id
									  	  WHERE (T007_C004_Id='{$g['empresaAtual']}'
										 	AND T007_Flag_Emitida_Recebida!='R')
											AND (D024_Id='{$g['clienteAtual']}') 
											AND ifnull(T007_Numero_Protocolo_Nfe,'')!=''
											    $where
									   GROUP BY T007_Id 
									   ORDER BY T007_Id DESC");

		$mTotalNotasFiscais = mysql_num_rows($totalNotasFiscais);
		$totalPagina 	    = ceil($mTotalNotasFiscais/$quantidade);
		$indiceInicio       = ($pagina - 1) * $quantidade;

		
		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaAlinhar' => 'center', 'style' => 'width:1%' , 'colunaExibir' => false),
            array('colunaValor' => 'IF(T007_Flag_ACP=6,\'NF-e\', \'NFC-e\') as Tipo', 'colunaTitulo' => 'Tipo'),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S'),
			array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'colunaMetrica' => 'S', 'style' => 'font-weight:bold;background:var(--celula-amarelo-claro)'),			
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'FlagAcp', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = "T007";		
		$extra = <<<EOT
			LEFT JOIN T005 on T005_Id=T007_T005_Id
			LEFT JOIN D024 on D024_Id=T007_D024_Id
			LEFT JOIN D013 on D013_D024_Id=D024_Id
            LEFT JOIN C007 as vendedor on vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		    LEFT JOIN D022 on D022_Id=T007_D022_Id
			WHERE (T007_C004_Id='{$g['empresaAtual']}'
			AND T007_Flag_Emitida_Recebida!='R')
			AND (D024_Id='{$g['clienteAtual']}') 
			AND T007_Flag_Cancelada!='S'
			AND ifnull(T007_Numero_Protocolo_Nfe,'')!=''
			$where
			GROUP BY T007_Id 
			ORDER BY T007_Id DESC 
			LIMIT {$indiceInicio},{$quantidade}
EOT;

		$primeira    = $pagina-2;
		$ultima      = $pagina+2;
		
		if($totalPagina<=0){
			$totalPagina=1;
		}

		//verificação da primeira e ultima pagina para não ficar acima do numero total de paginas ou menor que 0
		if($ultima>$totalPagina){
			$ultima = $totalPagina;
			$primeira = $ultima - 4;
		}

		if($primeira<=0){
			$primeira = 1;
			$ultima = $primeira+4;
		}

		if($ultima>$totalPagina){
			$ultima = $totalPagina;
		}

		if (!isset($pagina)){
			$pagina = 1;
		}

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,"$quantidade.$inicio");

		// /**
		// * Trazer total de itens do carrinho
		// */
		// $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
		// $T269_Itens = mysql_fetch_array($T269);

		// /**
		// * Trazer dados do cliente
		// */
		// $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato, C004_Nome_Abreviado FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id LEFT JOIN C004 ON D024_C004_Id=C004_Id WHERE D013_Id='{$g['clienteAtualContato']}'");
		// $resD024 = mysql_fetch_array($sqlD024);

		// $titulo = "NOTAS FISCAIS";

		// //manipular nome do cliente
		// //caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		// if(strpos($resD024['D013_Nome_Contato'],"@")){
		// 	$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		// }else{
		// 	//caso contrario pega o primeiro e ultimo nome da pessoa
		// 	$temp = explode(" ",$resD024['D013_Nome_Contato']);
		// 	$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
		// }

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
		$g['smarty']->assign('empresaAtualNome', $resD024['C004_Nome_Abreviado']);
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('clienteAtualNome', $resD024['D024_Nome_Empresa']);
		$g['smarty']->assign('logo',$logo);
		$g['smarty']->assign('titulo',$titulo);

		//variaveis do menu
		$g['smarty']->assign('mostrarPedidos', $MostrarPedidos);
		$g['smarty']->assign('mostrarOrcamentos', $MostrarOrcamentos);
		$g['smarty']->assign('mostrarBoletos', $MostrarBoletos);
		$g['smarty']->assign('mostrarNotasFiscais', $MostrarNotasFiscais);

		//variaveis para paginação 
		$g['smarty']->assign('pagina',$pagina);
		$g['smarty']->assign('quantidade',$quantidade);
		$g['smarty']->assign('inicio',$inicio);
		$g['smarty']->assign('totalPagina',$totalPagina);
		$g['smarty']->assign('url',$url);
		$g['smarty']->assign('ultima',$ultima);
		$g['smarty']->assign('primeira',$primeira);
		$g['smarty']->assign('filtro',$filtro);
		$g['smarty']->assign('qtdeLinhas',$mTotalNotasFiscais);

		// $g['smarty']->display('gridB2BTemplate.tpl');
		$g['smarty']->display('v2NotasFiscaisB2B.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#divCarrinho').click( function() {					
					window.location.href = "/v2b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
				});
			} </script></div>
EOT;

	break;

	case 'gridB2BBoletos':
		global $g;
		// $BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		// $MostrarPreco = $g['C031']['HabilitarPreco'];
		// $MostrarPedidos=$g['C031']['MostrarPedidos'];
		// $MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		// $MostrarBoletos=$g['C031']['MostrarBoletos'];
		// $MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		// $tipoLoginUsuarioB2B=$g['C031']['tipoLoginUsuarioB2B'];

		$pagina 	= (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url 		= substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 15;
		$inicio 	= ($quantidade * $pagina) - $quantidade;

		//paginação
		$totalBoletos= mysql_query("SELECT count(*) 
									  FROM T002
								 LEFT JOIN D024 ON D024_Id=T002_D024_Id
								 LEFT JOIN T117 ON T002_T117_Id=T117_Id
								 LEFT JOIN D007 ON D007_Id=T117_D007_Id			
									 WHERE D024_Id = '{$g['clienteAtual']}'");
		
		$mTotalBoletos = mysql_fetch_array($totalBoletos);
		$totalPagina   = ceil($mTotalBoletos[0]/$quantidade);
		$indiceInicio  = ($pagina - 1) * $quantidade;
		
		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'Id','colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Título'),
			array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Boleto', 'colunaExibir' => true,'callback' => 'campoMostrarBoletos'),
			array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData')	
		);

		/**
		* Definição da SQL
		*/

		$from = 'T002';
		$extra = <<<EOT
			LEFT JOIN D024 on D024_Id=T002_D024_Id
			LEFT JOIN T117 on T002_T117_Id=T117_Id
			LEFT JOIN D007 on D007_Id=T117_D007_Id	
			LEFT JOIN D027 on D027_Id=T002_D027_Id		
	 		WHERE D024_Id='{$g['clienteAtual']}'
	 		and ifnull(T002_T002_Id_Agrupado,0) <= 0
			GROUP BY T002_Id 
			ORDER BY T002_Id DESC 
			LIMIT {$indiceInicio},{$quantidade}
EOT;

		$primeira    = $pagina-2;
		$ultima      = $pagina+2;
		
		if($totalPagina<=0){
			$totalPagina=1;
		}
		
		//verificação da primeira e ultima pagina para não ficar acima do numero total de paginas ou menor que 0
		if($ultima>$totalPagina){
			$ultima = $totalPagina;
			$primeira = $ultima - 4;
		}

		if($primeira<=0){
			$primeira = 1;
			$ultima = $primeira+4;
		}

		if($ultima>$totalPagina){
			$ultima = $totalPagina;
		}

		if (!isset($pagina)){
			$pagina = 1;
		}

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,"$quantidade.$inicio");

		// /**
		// * Trazer total de itens do carrinho
		// */
		// $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
		// $T269_Itens = mysql_fetch_array($T269);

		// /**
		// * Trazer dados do cliente
		// */
		// $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato, C004_Nome_Abreviado FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id LEFT JOIN C004 ON D024_C004_Id=C004_Id WHERE D013_Id='{$g['clienteAtualContato']}'");
		// $resD024 = mysql_fetch_array($sqlD024);

		// $titulo="BOLETOS";

		// //manipular nome do cliente
		// //caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		// if(strpos($resD024['D013_Nome_Contato'],"@")){
		// 	$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		// }else{
		// 	//caso contrario pega o primeiro e ultimo nome da pessoa
		// 	$temp = explode(" ",$resD024['D013_Nome_Contato']);
		// 	$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
		// }

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
		$g['smarty']->assign('empresaAtualNome', $resD024['C004_Nome_Abreviado']);
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('clienteAtualNome', $resD024['D024_Nome_Empresa']);
		$g['smarty']->assign('logo',$logo);
		$g['smarty']->assign('titulo',$titulo);

		//variaveis do menu
		$g['smarty']->assign('mostrarPedidos', $MostrarPedidos);
		$g['smarty']->assign('mostrarOrcamentos', $MostrarOrcamentos);
		$g['smarty']->assign('mostrarBoletos', $MostrarBoletos);
		$g['smarty']->assign('mostrarNotasFiscais', $MostrarNotasFiscais);

		//variaveis para paginação 
		$g['smarty']->assign('pagina',$pagina);
		$g['smarty']->assign('quantidade',$quantidade);
		$g['smarty']->assign('inicio',$inicio);
		$g['smarty']->assign('totalPagina',$totalPagina);
		$g['smarty']->assign('url',$url);
		$g['smarty']->assign('ultima',$ultima);
		$g['smarty']->assign('primeira',$primeira);
		$g['smarty']->assign('filtro',$filtro);
		$g['smarty']->assign('qtdeLinhas',$mTotalBoletos[0]);

		// $g['smarty']->display('gridB2BTemplate.tpl');
		$g['smarty']->display('v2BoletosB2B.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#divCarrinho').click( function() {					
					window.location.href = "/v2b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
				});
			} </script></div>
EOT;

	break;

}
	
