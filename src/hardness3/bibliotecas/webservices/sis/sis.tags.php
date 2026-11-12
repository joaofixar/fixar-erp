<?php
namespace hardness;
/* Tags do XML */
/*
 * produtos 			-> produtos 						<<< node com os produtos do pedido
 * produto 				-> produto 							<<< cada node com a identificacao do produdo
 * cpf_cnpj 			-> cliente_documento 				<<< retorna o CPF/CNPJ (somente numeros)
 * inscricaoEstadual 	-> cliente_ie_rg_documento 			<<< retorna a IE/RG (somente numeros)
 * pedido_total_produto ->									<<< valor total produto sem o desconto
 * frete				-> pedido_total_frete				<<< valor final do frete
 * 
 * */
$tag['export']['filtro'] 					= 'pedido'; // O que for retornado estara dentro dessa tag, ela serve para percorrer o XML e verificar se retornou alguma coisa na consulta ou deu erro
//$tag['export']['cliente'] 					= 'cliente'; // Tag PRINCIPAL que sera percorrida no foreach para mostrar os dados do "cliente"
$tag['export']['pedido'] 					= 'pedido'; // Tag PRINCIPAL que sera percorrida no foreach para mostrar os "pedidos"
$tag['export']['produtos'] 					= 'produto'; // Tag PRINCIPAL que sera percorrida no foreach para mostrar os PRODUTOS DA NOTA

$export['cliente']['nome'] 					= 'cliente_nome';
$export['cliente']['nomeFantasia'] 			= 'cliente_nome'; /* o sistema SIS nao retorna um nome fantasia */
$export['cliente']['documento']			 	= 'cliente_ie_rg_documento';
$export['cliente']['endereco'] 				= 'cliente_endereco';
$export['cliente']['numero'] 				= 'cliente_numero';
$export['cliente']['complemento'] 			= 'cliente_complemento';
$export['cliente']['bairro'] 				= 'cliente_bairro';
$export['cliente']['cidade'] 				= 'cliente_cidade';
$export['cliente']['estado'] 				= 'cliente_estado'; // RS,SC,PR,SP ...
$export['cliente']['pais'] 					= 'pais'; /* o sistema SIS nao retorna um pais */
$export['cliente']['cep']					= 'cliente_cep';
$export['cliente']['email'] 				= 'cliente_email';
$export['cliente']['telefone1']				= 'cliente_telefone_1'; /* Para configurar o corretamente telefone procurar @TELEFONE em class.exportacao.php */
$export['cliente']['telefone2']				= 'cliente_telefone_2'; /* Para configurar o corretamente telefone procurar @TELEFONE em class.exportacao.php */
$export['cliente']['rg_ie']					= 'cliente_ie_rg'; /* Campo "bool", retorna RG se o documento for RG ou ?? se for InscricaoEstadual  */


$export['pedido']['idPedido'] 				= 'pedido_codigo';
$export['pedido']['dataPedido'] 			= 'pedido_data_hora';
$export['pedido']['status'] 				= 'pedido_status';

$export['pedido']['cpf_cnpj'] 				= 'cliente_documento';
$export['pedido']['desconto'] 				= 'pedido_total_desconto_acrescimo_valor';
$export['pedido']['frete'] 					= 'pedido_total_frete';
$export['pedido']['observacao'] 			= 'pedido_info_obs_cliente';

$export['pedido']['idProduto'] 				= 'produto_codigo';
//$export['pedido']['idItemVenda'] 			= 'idItemVenda';
$export['pedido']['codigo'] 				= 'produto_referencia'; /* REFERENCIA *** */
$export['pedido']['descricao'] 				= 'produto_nome';
//$export['pedido']['codigo'] 				= 'produto_codigo'; /* REFERENCIA *** */
$export['pedido']['quantidade'] 			= 'produto_quantidade';
$export['pedido']['valorUnitario'] 			= 'produto_preco';
$export['pedido']['valorTotalPedido']		= 'pedido_total_produto';






/* Aqui e o endereco de entrega, como a venda foi feita por ecommerce */
/* O endereco de entrega tem que ser o do cadastro do cliente */
/* Caso nao seja, tera que mudar aqui, a tag do XML e a funcao cadastro_client */
/* Para mudar no cadastro_client, tera que ser feita uma consulta no XML do pedido */
/* Para que se obtenha o endereco de entrega. */
/* (pode variar e Ecommerce, no caso do WebStorm, o endereco de entrega esta junto com o pedido) */

$export['pedido']['entrega']['endereco'] 		= $export['cliente']['endereco'];
$export['pedido']['entrega']['numero'] 			= $export['cliente']['numero'];
$export['pedido']['entrega']['complemento'] 	= $export['cliente']['complemento'];
$export['pedido']['entrega']['bairro'] 			= $export['cliente']['bairro'];
$export['pedido']['entrega']['cidade'] 			= $export['cliente']['cidade'];
$export['pedido']['entrega']['estado'] 			= $export['cliente']['estado'];
$export['pedido']['entrega']['pais'] 			= $export['cliente']['pais'];
$export['pedido']['entrega']['cep'] 			= $export['cliente']['cep'];
$export['pedido']['entrega']['telefone']	 	= $export['cliente']['telefone1'];

?>