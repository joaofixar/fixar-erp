<?php
namespace hardness;

global $g;

/**
 * B2B - Grid - Business to Business
 **/

require_once('prog/v2b2b/b2b001/grid_func-callback.php');
require_once('prog/v2b2b/b2b001/grid_func-js.php');
require_once('prog/crm/crm001/grid_func-callback.php');
// require_once('bibliotecas/funcoesCallback/');
// require_once('prog/b2b/b2b001/grid_cabecalho.php');

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * 
	 */
	case 'gridB2BPrincipal':
 		global $g,$sys;
	
		$BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		$MostrarPreco = $g['C031']['HabilitarPreco'];
		$MostrarPedidos=$g['C031']['MostrarPedidos'];
		$MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		$MostrarBoletos=$g['C031']['MostrarBoletos'];
		$MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		$buscaProduto = $_GET['buscaProduto'];
		$pagina = (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade =12;
		$inicio = ($quantidade * $pagina) - $quantidade;

		if(!empty($buscaProduto)){
			$buscaProduto = str_replace('/', ' ', $buscaProduto);
			$filtro = "and D001_Descricao_Produto like '%".str_replace(' ','%',trim($buscaProduto))."%'";
		}

		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id produto', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T269_Id', 'colunaTitulo' => 'T269_Id'),
            array('colunaValor' => 'D009_Quantidade_Estoque_B2B', 'colunaTitulo' => 'Estoque'),
			array('colunaValor' => 'D003_Imagem', 'colunaTitulo' => 'Imagem', 'callback' => 'mostrarImagemB2B'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descricao'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo'),
			array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preco', 'callback' => 'retornaPrecoVendaB2B'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id produto', 'colunaExibir' => false)
        );
	
		/**
		* Definição da SQL
		*/
		$from = 'D009';
		$extra = <<<EOT
				  left join D049 on D049_Id=D009_D049_Id 
				  left join D001 on D001_Id=D049_D001_Id 
                  left join D003 on D003_Id=D001_D003_Id 
                  left join T269 on T269_D009_Id=D009_Id and T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'		
                  where D001_Flag_Ativo='S' 
				  and D001_Flag_Ecommerce='S'
				  and D009_C004_Id='{$g['empresaAtual']}'
                  {$filtro}
				  order by if(D009_Quantidade_Estoque_B2B>0,0,1),replace(D001_Descricao_Produto,D003_Nome_Linha,'')				 

EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,"$quantidade.$inicio");

		/**
		 * Trazer total de itens do carrinho
		 */
		$T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		$T269_Itens = mysql_fetch_array($T269);

		/**
		 * Trazer dados do cliente
		 */
		$sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id WHERE D013_Id='{$g['clienteAtualContato']}'");
		$resD024 = mysql_fetch_array($sqlD024);
		
		//manipular nome do cliente
		//caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		if(strpos($resD024['D013_Nome_Contato'],"@")){
			$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		}else{
			//caso contrario pega o primeiro e ultimo nome da pessoa
			$temp = explode(" ",$resD024['D013_Nome_Contato']);
			$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
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
		
		$totalPagina = ceil($mTotalProdutos[0]/$quantidade);
		$primeira    = $pagina-2;
		$ultima      = $pagina+2;

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
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('logo',"http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}");

		
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

		/**
		 * 
		 */
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

	/**
	 * Grid Principal
	 * 
	 */
	case 'gridB2BCarrinho':
		global $g;
		$BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		$MostrarPreco = $g['C031']['HabilitarPreco'];
		$MostrarPedidos=$g['C031']['MostrarPedidos'];
		$MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		$MostrarBoletos=$g['C031']['MostrarBoletos'];
		$MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];

		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id produto', 'colunaExibir' => false),
            array('colunaValor' => 'T269_Id', 'colunaTitulo' => 'Id produto', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D003_Imagem', 'colunaTitulo' => 'Imagem', 'callback' => 'mostrarImagemPequenaB2B'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descricao'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaExibir' => false),
			array('colunaValor' => 'T269_Preco', 'colunaTitulo' => 'Preco', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T269_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => 0),
			array('colunaValor' => 'T269_Preco_Total', 'colunaTitulo' => 'Subtotal', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id produto D001', 'colunaExibir' => false)
        );

		/**
		* Definição da SQL
		*/


		$from = 'T269';
		$extra = <<<EOT
                  left join D009 on D009_Id=T269_D009_Id
				  left join D049 on D049_Id=D009_D049_Id 
				  left join D001 on D001_Id=D049_D001_Id 
                  left join D003 on D003_Id=D001_D003_Id 
                  where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'	
                  group by D001_Id 
                  order by replace(D001_Descricao_Produto,D003_Nome_Linha,'')
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Trazer total de itens do carrinho
		 */
		$T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		$T269_Itens = mysql_fetch_array($T269);

		/**
		 * Somatória do subtotal
		 */
		$T269 = mysql_query("select sum(T269_Preco_Total) as subtotal from T269 left join D009 on D009_Id=T269_D009_Id where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		$mT269 = mysql_fetch_array($T269);
		$subtotal = 'R$ '. gCorrigeNumero($mT269['subtotal']);

		/**
		 * Trazer dados do cliente
		 */
		$sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id where D013_Id='{$g['clienteAtualContato']}'");
		$resD024 = mysql_fetch_array($sqlD024);



		//manipular nome do cliente
		//caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		if(strpos($resD024['D013_Nome_Contato'],"@")){
			$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		}else{
			//caso contrario pega o primeiro e ultimo nome da pessoa
			$temp = explode(" ",$resD024['D013_Nome_Contato']);
			$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
			
		}

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
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('subtotalCarrinho',$subtotal);
		$g['smarty']->assign('logo',"http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}");

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
								    $.getJSON('/v2b2b/b2b001/grid_func-ajax/gerarPedido?ajax=true&callback=?', function(request) {
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
							    $.getJSON('/v2b2b/b2b001/grid_func-ajax/gerarOrcamento?ajax=true&callback=?', function(request) {
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
					var id = $(this).attr('idProduto').replace(/T269_Id-/, '');
					var quantidade = $('#quantidade'+idProduto).val();	
					$.getJSON('/v2b2b/b2b001/grid_func-ajax/gravarQuantidade?ajax=true&T269_Id=' + id + '&quantidade=' + quantidade + '&callback=?', function(request) {
						$('#subtotalProduto'+idProduto).text(request.data['subTotalUnit']);
						$('#subtotalCarrinho').text(request.data['subTotal']);
						$('#totalCarrinho').text(request.data['Total']);
					});
				});	

				$('#divCarrinho').click( function() {
					window.location.href = "/v2b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
				});
				$('#maisProdutos').click( function() {					
					window.location.href = "/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true";				
				});
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
		$BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		$MostrarPreco = $g['C031']['HabilitarPreco'];
		$MostrarPedidos=$g['C031']['MostrarPedidos'];
		$MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		$MostrarBoletos=$g['C031']['MostrarBoletos'];
		$MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		$pagina = (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 15;
		$inicio = ($quantidade * $pagina) - $quantidade;


		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true), 
			array('colunaValor' => 'T005_T003_Id', 'colunaTitulo' => 'Orçamento'), 
			array('colunaValor' => 'T005_Ordem_Compra', 'colunaTitulo' => 'OC.  Cliente', 'colunaAlinhar' => 'center', 'colunaEditar' => true), 
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'), 
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right','style' => 'font-weight:bold;background:lightyellow;width:1%','callback' => 'valorTotalPedidoB2B'),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora', 'colunaExibir' => true),
			array('colunaValor' => 'group_concat(distinct T005_Status_Pedido(T005_Flag_Status,1), if(T005_Flag_Status=8, concat("<br/>", T005_Observacao_Pedido_Perdido), if(T005_Flag_Status=9, T005_Motivo_Nao_Liberado,""))) as Status2', 'colunaTitulo' => 'Status'),
			array('colunaValor' => 'group_concat(distinct T005_Status_Pedido(T005_Flag_Status,1), "<br/>", if(T005_Flag_Status=9, T005_Motivo_Nao_Liberado,"")) as Status3', 'colunaTitulo' => 'Status','colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ",") as NF', 'colunaTitulo' => 'NF', 'callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left'),
		);

		/**
		* Definição da SQL
		*/

		$from = 'T005';
		$extra = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
			left join D013 on D013_D024_Id=D024_Id
			left join T003 on T003_Id=T005_T003_Id
			left join D022 on D022_Id=T005_D022_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
			left join C007 as Liberou on Liberou.C007_Id=T005_Liberou_Alcada_C007_Id
			left join C007 as LiberouOrcamento on LiberouOrcamento.C007_Id=T003_Liberou_Alcada_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
			left join T007 on T007_T005_Id=T005_Id
			where (T005_C004_Id='{$g['empresaAtual']}') 
			and (D013_Id='{$g['clienteAtualContato']}') 
			and T005_Flag_Status!=8
			group by T005_Id 
			order by T005_Id desc 
			
EOT;
			//paginação
			$totalPedidos= mysql_query("select count(*) from  T005 
													left join D024 on D024_Id=T005_D024_Id
													left join D013 on D013_D024_Id=D024_Id
													left join T003 on T003_Id=T005_T003_Id
													left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
													left join T007 on T007_T005_Id=T005_Id
													where (T005_C004_Id='{$g['empresaAtual']}') 
													and (D013_Id='{$g['clienteAtualContato']}')");

			$mTotalPedidos = mysql_fetch_array($totalPedidos);
			$totalPagina = ceil($mTotalPedidos[0]/$quantidade);
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
			/**
			 * Trazer total de itens do carrinho
			 */
			 $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
			 $T269_Itens = mysql_fetch_array($T269);
			/**
			 * Trazer dados do cliente
			 */
			 $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id WHERE D013_Id = '{$g['clienteAtualContato']}'");
			 $resD024 = mysql_fetch_array($sqlD024);
	 


			//manipular nome do cliente
			//caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
			if(strpos($resD024['D013_Nome_Contato'],"@")){
				$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
			}else{
				//caso contrario pega o primeiro e ultimo nome da pessoa
				$temp = explode(" ",$resD024['D013_Nome_Contato']);
				$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
			}

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
			$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
			$g['smarty']->assign('logo',"http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}");
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
			$g['smarty']->assign('qtdeLinhas',$mTotalPedidos[0]);

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
		$BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		$MostrarPreco = $g['C031']['HabilitarPreco'];
		$MostrarPedidos=$g['C031']['MostrarPedidos'];
		$MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		$MostrarBoletos=$g['C031']['MostrarBoletos'];
		$MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		$pagina = (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 15;
		$inicio = ($quantidade * $pagina) - $quantidade;
		
		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'idPedido', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right','callback' => 'valorTotalOrcamentoB2B'),
			array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - R$",T005_Valor_Total) separator ", <br />") as Pedido', 'colunaTitulo' => 'Pedido'),
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
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_D024_Id=D024_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			left join T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
			left join T007 on T007_T005_Id=T005_Id
			where T003_C004_Id='{$g['empresaAtual']}' 
			and (D013_Id='{$g['clienteAtualContato']}') 
			group by T003_Id 
			order by T003_Id desc 
EOT;

		//paginação
		$totalOrcamentos= mysql_query("select count(*) from  T003
													left join D024 on D024_Id=T003_D024_Id
													left join D013 on D013_D024_Id=D024_Id
													left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
													left join T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
													left join T007 on T007_T005_Id=T005_Id
													where T003_C004_Id='{$g['empresaAtual']}' 
													and (D013_Id='{$g['clienteAtualContato']}')");

		$mTotalOrcamentos = mysql_fetch_array($totalOrcamentos);
		$totalPagina = ceil($mTotalOrcamentos[0]/$quantidade);
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

		/**
		 * Trazer total de itens do carrinho
		 */
		 $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
		 $T269_Itens = mysql_fetch_array($T269);
 
		/**
		 * Trazer dados do cliente
		 */
		 $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id WHERE D013_Id = '{$g['clienteAtualContato']}'");
		 $resD024 = mysql_fetch_array($sqlD024);
 


		//manipular nome do cliente
		//caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		if(strpos($resD024['D013_Nome_Contato'],"@")){
			$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		}else{
			//caso contrario pega o primeiro e ultimo nome da pessoa
			$temp = explode(" ",$resD024['D013_Nome_Contato']);
			$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
		}

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
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('logo',"http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}");
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
		$g['smarty']->assign('qtdeLinhas',$mTotalOrcamentos[0]);

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
		$BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		$MostrarPreco = $g['C031']['HabilitarPreco'];
		$MostrarPedidos=$g['C031']['MostrarPedidos'];
		$MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		$MostrarBoletos=$g['C031']['MostrarBoletos'];
		$MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		$pagina = (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 15;
		$inicio = ($quantidade * $pagina) - $quantidade;
		
		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaAlinhar' => 'center', 'style' => 'width:1%' , 'colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'font-weight:bold;width:1%', 'callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S'),
			array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'callback' => 'valorTotalNotasB2B', 'colunaMetrica' => 'S', 'style' => 'font-weight:bold;background:lightyellow'),			
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
			array('colunaValor' => 'T007_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorInternoNF', 'colunaEditarNivel' => 4, 'style' => 'width:1%;background:#f7f4d2'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'style' => 'background:#f7f4d2', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/

		$from = "T007";		
		$extra = <<<EOT
			left join T005 on T005_Id=T007_T005_Id
		    left join D024 on D024_Id=T007_D024_Id
            left join C007 as vendedor on vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		    left join D022 on D022_Id=T007_D022_Id
			where (T007_C004_Id='{$g['empresaAtual']}'
			and T007_Flag_Emitida_Recebida!='R')
			and (D024_Id='{$g['clienteAtual']}') 
			and ifnull(T007_Numero_Protocolo_Nfe,'')!=''
			group by T007_Id 
			order by T007_Id desc 
EOT;

		//paginação
		$totalNotasFiscais= mysql_query("select count(*) from T007
													left join T005 on T005_Id=T007_T005_Id
													left join D013 as Pedido on Pedido.D013_Id=T005_D013_Id
													left join D024 on D024_Id=T007_D024_Id
													left join D006 on D006_Id=T007_D006_Id
													left join C007 as vendedor on vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
													left join D022 on D022_Id=T007_D022_Id
													where (T007_C004_Id='{$g['empresaAtual']}'
													and T007_Flag_Emitida_Recebida!='R')
													and (D024_Id='{$g['clienteAtual']}')");

		$mTotalNotasFiscais = mysql_fetch_array($totalNotasFiscais);
		$totalPagina = ceil($mTotalNotasFiscais[0]/$quantidade);
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

		/**
		* Trazer total de itens do carrinho
		*/
		$T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
		$T269_Itens = mysql_fetch_array($T269);

		/**
		* Trazer dados do cliente
		*/
		$sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id WHERE D013_Id = '{$g['clienteAtualContato']}'");
		$resD024 = mysql_fetch_array($sqlD024);

		$titulo = "NOTAS FISCAIS";

		//manipular nome do cliente
		//caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		if(strpos($resD024['D013_Nome_Contato'],"@")){
			$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		}else{
			//caso contrario pega o primeiro e ultimo nome da pessoa
			$temp = explode(" ",$resD024['D013_Nome_Contato']);
			$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
		}

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('logo',"http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}");
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
		$g['smarty']->assign('qtdeLinhas',$mTotalNotasFiscais[0]);

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
		$BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
		$MostrarPreco = $g['C031']['HabilitarPreco'];
		$MostrarPedidos=$g['C031']['MostrarPedidos'];
		$MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
		$MostrarBoletos=$g['C031']['MostrarBoletos'];
		$MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
		$pagina = (isset($_GET['pagina'])) ?(int)$_GET['pagina'] : 1;
		$url = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'], '?'));	
		$quantidade = 15;
		$inicio = ($quantidade * $pagina) - $quantidade;
		
		/**
		* Definição do Grid (Tabela) B2B - Seguir padrão de colunas: Id produto, Id compra, Descrição produto, Imagem, Nome grupo e Estoque.
		*/
		$grid = array(
			array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'Id','colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Boleto', 'colunaExibir' => true,'callback' => 'campoMostrarBoletos'),
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'T002_Valor_Duplicata', 'colunaTitulo' => 'Total', 'callback'=>'valorTotalBoletosB2B'),
			array('colunaValor' => 'T002_Data_Emissao', 'colunaTitulo' => 'Data emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Vencimento', 'colunaTitulo' => 'Data Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Data Baixa', 'callback' => 'gCorrigeData')	
		);

		/**
		* Definição da SQL
		*/

		$from = 'T002';
		$extra = <<<EOT
			left join D024 on D024_Id=T002_D024_Id
			left join D013 on D013_Id=D013_D024_Id
			left join T117 on T002_T117_Id=T117_Id
			left join D007 on D007_Id=T117_D007_Id			
	 		where D024_Id='{$g['clienteAtual']}'
	 		and T002_T002_Id_Agrupado <= 0
			group by T002_Id 
			order by T002_Id desc 
EOT;

		//paginação
		$totalBoletos= mysql_query("select count(*) from  T002
												left join D024 on D024_Id=T002_D024_Id
												left join D013 on D013_Id=D013_D024_Id
												left join T117 on T002_T117_Id=T117_Id
												left join D007 on D007_Id=T117_D007_Id			
										 		where D024_Id='{$g['clienteAtual']}'
												");

		$mTotalBoletos = mysql_fetch_array($totalBoletos);
		$totalPagina = ceil($mTotalBoletos[0]/$quantidade);
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

		/**
		* Trazer total de itens do carrinho
		*/
		$T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
		$T269_Itens = mysql_fetch_array($T269);

		/**
		* Trazer dados do cliente
		*/
		$sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id WHERE D013_Id = '{$g['clienteAtualContato']}'");
		$resD024 = mysql_fetch_array($sqlD024);

		$titulo="BOLETOS";

		//manipular nome do cliente
		//caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		if(strpos($resD024['D013_Nome_Contato'],"@")){
			$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		}else{
			//caso contrario pega o primeiro e ultimo nome da pessoa
			$temp = explode(" ",$resD024['D013_Nome_Contato']);
			$resD024['D013_Nome_Contato'] = $temp[0]; // . " " . $temp[count($temp)-1];
		}

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('logo',"http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}");
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
	
