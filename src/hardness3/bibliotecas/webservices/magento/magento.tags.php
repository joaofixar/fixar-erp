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
$tag['export']['filtro'] 						= $dom->getElementsByTagName('orders')->item(0);
$tag['export']['produtos'] 						= $dom->getElementsByTagName('items');
$tag['export']['produtos_texto'] 				= 'item';
$tag['export']['pedido'] 						= $dom->getElementsByTagName('orders');

$export['cliente']['nome'] 						= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('firstname')->item(0)->nodeValue . ' ' . 
												  $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('lastname')->item(0)->nodeValue;

$export['cliente']['nomeFantasia'] 				= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('firstname')->item(0)->nodeValue;
$export['cliente']['endereco'] 					= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('street')->item(0)->nodeValue;
$export['cliente']['cidade'] 					= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('city')->item(0)->nodeValue;
$export['cliente']['cep'] 						= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('postcode')->item(0)->nodeValue;
$export['cliente']['estado'] 					= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('region')->item(0)->nodeValue;; // Não existe estado no XML
$export['cliente']['pais'] 						= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('country_id')->item(0)->nodeValue;
$export['cliente']['telefone1']					= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('telephone')->item(0)->nodeValue;
$export['cliente']['documento']			 		= '';
$export['cliente']['numero'] 					= '';
$export['cliente']['complemento'] 				= '';
$export['cliente']['bairro'] 					= '';
$export['cliente']['email'] 					= $dom->getElementsByTagName('order')->item(0)->getElementsByTagName('customer_email')->item(0)->nodeValue;
$export['cliente']['telefone2']					= ''; 	  //* Para configurar o corretamente telefone procurar @TELEFONE em class.exportacao.php */
$export['cliente']['rg_ie']						= '';     //Campo "bool", retorna RG se o documento for RG ou ?? se for InscricaoEstadual  

$export['pedido']['codigo'] 					= $dom->getElementsByTagName('order')->item(0)->getElementsByTagName('entity_id')->item(0)->nodeValue;
$export['pedido']['quantidade'] 				= $dom->getElementsByTagName('item')->item(0)->getElementsByTagName('qty_ordered')->item(0)->nodeValue;
$export['pedido']['idPedido'] 					= $dom->getElementsByTagName('order')->item(0)->getElementsByTagName('increment_id')->item(0)->nodeValue;
//$tag['export']['cliente'] 					= 'cliente'; // Tag PRINCIPAL que sera percorrida no foreach para mostrar os dados do "cliente"
$export['pedido']['dataPedido'] 				= $dom->getElementsByTagName('order')->item(0)->getElementsByTagName('created_at')->item(0)->nodeValue;
$export['pedido']['status'] 					= '';
$export['pedido']['cpf_cnpj'] 					= $dom->getElementsByTagName('order')->item(0)->getElementsByTagName('cpfcnpj')->item(0)->nodeValue; // TEM QUE VIR UMA TAG COM CPF OU CNPJ DO MAGENTO
$export['pedido']['desconto'] 					= $dom->getElementsByTagName('order')->item(0)->getElementsByTagName('discount_amount')->item(0)->nodeValue;
$export['pedido']['frete'] 						= $dom->getElementsByTagName('order')->item(0)->getElementsByTagName('shipping_amount')->item(0)->nodeValue;
$export['pedido']['observacao'] 				= '';
$export['pedido']['idProduto'] 					= $dom->getElementsByTagName('item')->item(0)->getElementsByTagName('product_id')->item(0)->nodeValue;
$export['pedido']['idItemVenda'] 				= $dom->getElementsByTagName('item')->item(0)->getElementsByTagName('item_id')->item(0)->nodeValue;
$export['pedido']['descricao'] 					= $dom->getElementsByTagName('item')->item(0)->getElementsByTagName('description')->item(0)->nodeValue;
$export['pedido']['valorUnitario'] 				= $dom->getElementsByTagName('item')->item(0)->getElementsByTagName('price')->item(0)->nodeValue;
$export['pedido']['valorTotalPedido']			= $dom->getElementsByTagName('order')->item(0)->getElementsByTagName('grand_total')->item(0)->nodeValue;

/* Aqui e o endereco de entrega, como a venda foi feita por ecommerce */
/* O endereco de entrega tem que ser o do cadastro do cliente */
/* Caso nao seja, tera que mudar aqui, a tag do XML e a funcao cadastro_client */
/* Para mudar no cadastro_client, tera que ser feita uma consulta no XML do pedido */
/* Para que se obtenha o endereco de entrega. */
/* (pode variar e Ecommerce, no caso do WebStorm, o endereco de entrega esta junto com o pedido) */

$export['pedido']['entrega']['endereco'] 		= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('street')->item(0)->nodeValue;
$export['pedido']['entrega']['numero'] 			= '';
$export['pedido']['entrega']['complemento'] 	= '';
$export['pedido']['entrega']['bairro'] 			= '';
$export['pedido']['entrega']['cidade'] 			= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('city')->item(0)->nodeValue;
$export['pedido']['entrega']['estado'] 			= '';
$export['pedido']['entrega']['pais'] 			= '';
$export['pedido']['entrega']['cep'] 			= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('postcode')->item(0)->nodeValue;;
$export['pedido']['entrega']['telefone']	 	= $dom->getElementsByTagName('shipping')->item(0)->getElementsByTagName('telephone')->item(0)->nodeValue;;

?>