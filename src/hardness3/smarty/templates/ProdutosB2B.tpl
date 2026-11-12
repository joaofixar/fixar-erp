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
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> 
    <link rel="stylesheet" href="/hardness3/smarty/B2B/css/jquery-ui.css">
    

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>   
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
    </div> <!-- End header area -->
    
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
                    <ul class="nav navbar-nav">
                        <li>
                            <input type="text" id='buscaProduto' placeholder="Buscar produtos..." value='{$buscaProduto}' class="campo_buscar">
                            <input type="submit" value="Buscar" id="botaoBuscar" class="botao_buscar">
                        </li>
                    </ul>
                </div>  
            </div>
        </div>
    </div> <!-- End mainmenu area -->  
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container" id="container">
           <div class="row">      
              {foreach from=$tbody key=tbodykey item=i}   
                {if is_numeric($tbodykey)}
		            <div class="col-md-3 col-sm-6" style="border:1px dashed #F4F4F4">
                        <div class="single-shop-product">  
                            {foreach from=$i.dados key=id item=ii}
                                {if $id==1}
                                    {$T269_Id = $ii}
                                {/if}
	                            {if $id==3}
                                    <div class="product-upper">
                                        <img src="{$ii}"></img>
                                    </div>
                                {/if}
                                {if $id==2}
                                    {$D009_Quantidade_Estoque = $ii}
                                    {continue}
                                {/if}
                                {if $id==4}
                                    <div class="descricao_produto">
                                        <h2 style="font-size:15px;">{$ii}</h2>
                                    </div> 
                                {/if}
                                {if $id==5}
                                    {if $D009_Quantidade_Estoque<=0}
                                        <div>
                                            <p style="color:red;text-align:center;padding-top:10px;">Produto indisponível</p>
                                        
                                        </div>    
                                    {else}
                                        <div>
                                            <p style="color:green;text-align:center;padding-top:10px;">Produto disponível</p>
                                           
                                        </div>
                                    {/if}
                                {/if}
                                {if $id==6}
                                    {if $mostrarPreco=='N'}
                                        {continue}
                                    {else}
                                        <div class="product-carousel-price" >
                                            <p class="preco_produto">R$ {$ii|number_format:2:",":"."}</p>
                                            {$preco = $ii}
                                        </div> 
                                    {/if}
                                {/if}			
                            {/foreach}	
                            {if $D009_Quantidade_Estoque<=0}
                                <div class="product-option-shop">
                                    <font class="add_to_cart_button_indisponivel">Indisponível</font>
                                    
                                </div> 
                            {else}					
                                {if $T269_Id > 0}						
                                    <div class="product-option-shop">
                                        <font class="add_to_cart_button_carrinho">Carrinho</font>
                                    </div>   
                                {else}
                                    <div class="product-option-shop">
                                        <button type="button" class="add_to_cart_button" id="{$i.pk.pkCampo}" preco="{$preco}">Comprar</button>
                                       
                                    </div> 
                                {/if}
                            {/if}	                       
                        </div>
                    </div>
                {/if}    
             {/foreach}
            </div>           
         
            <div class="row">
                <div class="col-md-12">
                    <div class="product-pagination text-center">
                        <nav>
                            <ul class="pagination">
                                <li>
                                    <a href="{$url}?ajax=true&totalPagina={$totalPagina}&pagina=1&buscaProduto={$buscaProduto}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                {for $i=$primeira to $ultima}                
                                    {if $i == $pagina}
                                        <li><a href="{$url}?ajax=true&totalPagina={$ultima}&pagina={$i}&buscaProduto={$buscaProduto}" style="font-weight:bold;background:lightyellow">{$i}</a></li>
                                    {else}
                                        <li><a href="{$url}?ajax=true&totalPagina={$ultima}&pagina={$i}&buscaProduto={$buscaProduto}">{$i}</a></li>
                                    {/if}
                                {/for}
                                <li>
                                    <a href="{$url}?ajax=true&totalpagina={$ultima}&pagina={$totalPagina}&buscaProduto={$buscaProduto}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>                        
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
    <script src="https://code.jquery.com/jquery.min.js"></script>
    
    <!-- Bootstrap JS form CDN -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    
    <!-- jQuery sticky menu -->
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    
    <!-- jQuery easing -->
    <script src="js/jquery.easing.1.3.min.js"></script>
    
    <!-- Main Script -->
    <script src="js/main.js"></script>
  </body>
</html>
