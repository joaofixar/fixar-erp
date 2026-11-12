<!DOCTYPE html>

<html lang="en">
  <head>
   <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>B2B </title>
    
    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,700,600' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,100' rel='stylesheet' type='text/css'>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/hardness3/smarty/B2B/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/hardness3/smarty/B2B/css/font-awesome.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/hardness3/smarty/B2B/css/owl.carousel.css">
    <link rel="stylesheet" href="/hardness3/smarty/B2B/style.css">
    <link rel="stylesheet" href="/hardness3/smarty/B2B/css/responsive.css">
    <link rel="stylesheet" href="/hardness3/smarty/B2B/css/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css" type="text/css">
       
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> 
    <script src="js/owl.carousel.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
  <body>   
    
      <script type="text/javascript">
        function mascaraValor(valor) {
           var valor = valor.toFixed(2);
           var valor = valor.toString().replace('.',',');      
           var valor = "R$ "+valor;    
           return valor;            
        }
    </script>

    <div class="header-area">
        <div class="container">
            <div class="row">
            <!--Esse menu serve para mobile, ao diminuir a tela ele fica essas três linhas-->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div> 
                <div class="col-md-16">
                    <div class="navbar-collapse collapse" style="padding:0.5px;">
                        <ul class="nav navbar-nav" style="font-size:13px;">
                            <li><a href="/b2b/b2b001/grid/gridB2BPrincipal/?ajax=true"><i class="fa fa-cube"></i>&nbsp;Produtos</a></li>
                            {if $mostrarOrcamentos=='S'}
                                <li><a href="/b2b/b2b001/grid/gridB2BOrcamentos/?ajax=true"><i class="fa fa-calculator"></i>&nbsp;Orçamentos</a></li>
                            {/if}
                            {if $mostrarPedidos=='S'}
                                 <li><a href="/b2b/b2b001/grid/gridB2BPedidos/?ajax=true"><i class="fa fa-list"></i>&nbsp;Pedidos</a></li>
                            {/if}
                            {if $mostrarNotasFiscais=='S'}
                                <li><a href="/b2b/b2b001/grid/gridB2BNotasFiscais/?ajax=true"><i class="fa fa-list"></i>&nbsp;Notas fiscais</a></li>
                            {/if}
                            {if $mostrarBoletos=='S'}
                                <li><a href="/b2b/b2b001/grid/gridB2BBoletos/?ajax=true"><i class="fa fa-list"></i>&nbsp;Boletos</a></li>
                            {/if}
                            
                            <li><a href="/b2b/b2b001/grid/gridB2BCarrinho?ajax=true&buscaProduto="><i class="fa fa-shopping-cart"></i>&nbsp;Carrinho</a></li>                   
                            <li><a href="/hardness3/outros/loginb2b/"><i class="fa fa-sign-out"></i>&nbsp;Sair</a></li>  
                            <li class="minha_conta"><a href="/b2b/b2b001/form/b2b001FormSenha?ajax=true" style="width:300px;"><i class="fa fa-user"></i>&nbsp;{$clienteAtualContatoNome}</a></li>                      
                        </ul>
                    </div>
                </div>
            </div>
        </div>        
    </div>  <!-- End header area -->
    
    <div class="site-branding-area">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="logo">
                        <h1><a href="/b2b/b2b001/grid/gridB2BPrincipal/?ajax=true"><img src="{$logo}"></a></h1>
                    </div>
                </div>
                
                <div class="col-sm-6">
                    <div class="shopping-item" id="divCarrinho" style="cursor: pointer;">
                       Itens:&nbsp;
                       <span class="cart-amunt" id="Carrinho">
                         {$itensCarrinho}
                       </span>
                       <i class="fa fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End site branding area -->

    <div class="mainmenu-area">
        <div class="container">
            <div class="row">
                <div class="user-menu">
                    <ul class="nav navbar-nav" >
                        <li>
                            <h2 id="form" class="titulo_Pagina">Carrinho</h2>
                        </li>
                    </ul>
                </div>  
            </div>
        </div>
    </div>
     <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">            
            <div class="row">           
            <div style="padding-left:13px;padding-bottom:5px;" class="botao_escolher"><button id="maisProdutos" class="button_Gerar_Pedido_Orcamento" style="padding-bottom:10px;">Escolher + produtos</button></div>     
                <div class="col-md-12">
                    <div class="product-content-right">
                        <div class="woocommerce">
                           <table class="shop_table cart" id="container">
                                <thead>
                                    <tr>
                                        <th class="product-remove">&nbsp;</th>
                                        <th class="product-thumbnail"></th>
                                        <th class="product-name">Produto</th>
                                        <th class="product-price">Preço</th>
                                        <th class="product-quantity">Qtde</th>
                                        <th class="product_price_subtotal">Sub<br/>total</th>
                                    </tr>
                                </thead>
                                <tbody style="weight:70%;height:50px;"> 
                                    {if $itensCarrinho==0}
                                        <td class="cart_item" style="border-bottom: solid #DDD 0.5px" colspan="6">
                                           &nbsp;
                                        </td>    
                                    {/if}   
                                    {assign var="subTotal" value="0"}
                                    {foreach from=$tbody key=tbodykey item=i}
                                        <tr class="cart_item" id="row{$tbodykey}" style="border-bottom: solid #DDD 0.5px">
                                            {if is_numeric($tbodykey)}                                                		
                                                {assign var="count" value="0"}
                                                <td class="product-remove">
                                                    <button type="button" id='excluir' idProduto="{$i.pk.pkCampo}" class="button_excluir" row="row{$tbodykey}" tabindex="999">X</button> 
                                                </td>
                                                {foreach from=$i.dados key=id item=ii}																		
                                                    {assign var="`$thead.$count.value`" value="$ii"}
                                                    {if $thead.$count.value == T269_Id || $thead.$count.value == Quantidade} {assign var="count" value="`$count+1`"} {continue} {/if}
                                                    {assign var="count" value="`$count+1`"}
                                                {/foreach}	
                                                <td class="product-name" id="produto-imagem">
                                                    <img src="{$Imagem}"/>
                                                </td>                                                           
                                                <td class="product-name">
                                                    <div style='float:left;padding-left:10px;text-align: center;' class="nome_produto">{$Descricao}</div>
                                                </td>
                                                {if $Preco && $mostrarPreco=='N'}
                                                    <td class="product-price">
                                                        <div style='padding-left:10px;' class="valor_produto">Preço <br/>indisponível</div>
                                                    </td>
                                                {else}
                                                    <td class="product-price">
                                                        <div style='padding-left:10px;' class="valor_produto"> R$ {$Preco}</div>
                                                        <div id="preco{$i.pk.pkCampo}" style='float:left;padding-left:10px;display:none;'>R$ {$PrecoOriginal}</div>
                                                    </td> 
                                                {/if}                                                   
                                                <td class="product-quantity">
                                                    <div class="quantity buttons_added">
                                                        <input type='number' id="quantidade{$i.pk.pkCampo}" style="text-align:center;" idProduto="{$i.pk.pkCampo}"value="{$Quantidade}" min="1" class="input-text qty text">
                                                        <!-- <button type="button" id="gravar" idProduto="{$i.pk.pkCampo}" class="button_confirmação_qtde">Ok</button> -->
                                                    </div>
                                                </td>	
                                                {if $mostrarPreco=='N'}
                                                    <td class="product-price" style="width:5%;"  class="valor_produto"> Preço <br/>indisponível </td>
                                                {else}
                                                    <td class="product_price_subtotal">
                                                        <div style='padding-left:10px;' id="subtotalProduto{$i.pk.pkCampo}" class="valor_produto">R$ {$Subtotal}</div>
                                                    </td> 
                                                {/if}                         
                                            {/if}
                                        </tr>			
                                    {/foreach}
                                    <tr>
                                        {if $botaoOrcaPedido=='1' && $itensCarrinho>0}
                                            <td id="botao" colspan="6"> <button type="button" id='gerarOrcamento' class="button_Gerar_Pedido_Orcamento">Gerar Orçamento</button></td>
                                        {elseif $botaoOrcaPedido=='2' && $itensCarrinho>0}
                                            <td id="botao" colspan="6"> <button type="button" id='gerarPedido' class="button_Gerar_Pedido_Orcamento">Gerar Pedido</button></td>
                                        {/if}
                                    </tr>
                                </tbody>
                            </table>                          

                            <div class="cart-collaterals">
                                <div class="cart_totals ">
                                    <h2>TOTAL</h2>
                                    <table cellspacing="0">
                                        <tbody>
                                            <tr class="cart-subtotal">
                                                <th style="text-align:center;">SUBTOTAL</th>
                                                 {if $mostrarPreco=='N'}
                                                    <td class="product-price" style="width:5%;text-align:center;"  class="valor_produto"> Preço <br/>indisponível </td>
                                                {else}
                                                    <td><div class="amount" id="subtotalCarrinho" style="font-size:15px;text-align:center;"> {$subtotalCarrinho} </div></td>
                                                {/if}
                                            </tr>

                                            <tr class="shipping">
                                                <th style="text-align:center;">FRETE</th>
                                                <td style="text-align:center;"> - </td>
                                            </tr>

                                            <tr class="order-total">
                                                <th style="text-align:center;">TOTAL</th>
                                                 {if $mostrarPreco=='N'}
                                                    <td class="product-price" style="width:5%;text-align:center;"  class="valor_produto"> Preço <br/>indisponível </td>
                                                {else}
                                                    <td><strong><div class="amount" id="totalCarrinho" style="font-size:15px;text-align:center;"> {$subtotalCarrinho} </div></strong> </td>
                                                {/if}
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>                        
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    <div class="footer-top-area">
       <!-- <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="footer-about-us">
                        <h2>u<span>Stora</span></h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perferendis sunt id doloribus vero quam laborum quas alias dolores blanditiis iusto consequatur, modi aliquid eveniet eligendi iure eaque ipsam iste, pariatur omnis sint! Suscipit, debitis, quisquam. Laborum commodi veritatis magni at?</p>
                        <div class="footer-social">
                            <a href="#" target="_blank"><i class="fa fa-facebook"></i></a>
                            <a href="#" target="_blank"><i class="fa fa-twitter"></i></a>
                            <a href="#" target="_blank"><i class="fa fa-youtube"></i></a>
                            <a href="#" target="_blank"><i class="fa fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">User Navigation </h2>
                        <ul>
                            <li><a href="">My account</a></li>
                            <li><a href="">Order history</a></li>
                            <li><a href="">Wishlist</a></li>
                            <li><a href="">Vendor contact</a></li>
                            <li><a href="">Front page</a></li>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">Categories</h2>
                        <ul>
                            <li><a href="">Mobile Phone</a></li>
                            <li><a href="">Home accesseries</a></li>
                            <li><a href="">LED TV</a></li>
                            <li><a href="">Computer</a></li>
                            <li><a href="">Gadets</a></li>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-newsletter">
                        <h2 class="footer-wid-title">Newsletter</h2>
                        <p>Sign up to our newsletter and get exclusive deals you wont find anywhere else straight to your inbox!</p>
                        <div class="newsletter-form">
                            <input type="email" placeholder="Type your email">
                            <input type="submit" value="Subscribe">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom-area">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="copyright">
                       <p>&copy; 2015 uCommerce. All Rights Reserved. <a href="http://www.freshdesignweb.com" target="_blank">freshDesignweb.com</a></p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="footer-card-icon">
                        <i class="fa fa-cc-discover"></i>
                        <i class="fa fa-cc-mastercard"></i>
                        <i class="fa fa-cc-paypal"></i>
                        <i class="fa fa-cc-visa"></i>
                    </div>
                </div>
            </div>
        </div>-->
    </div>
   
    <!-- Latest jQuery form server -->
    <!--<script src="https://code.jquery.com/jquery.min.js"></script>-->
    
    <!-- Bootstrap JS form CDN -->
    <!-- <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>-->
    
    <!-- jQuery sticky menu -->
    <!-- <script src="js/owl.carousel.min.js"></script>-->
    <!-- <script src="js/jquery.sticky.js"></script>-->
    
    <!-- jQuery easing -->
    <!-- <script src="js/jquery.easing.1.3.min.js"></script>-->
    
    <!-- Main Script -->
   <!--  <script src="js/main.js"></script> -->
  </body>
</html>