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
                            <h2 id="form" class="titulo_Pagina">Alteração de senha</h2>
                        </li>
                    </ul>
                </div>  
            </div>
        </div>
    </div>

    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">            
            <div class="col-md-10" id="form_novasenha">
                <form  action="{$href}" method="POST" id="form" name="form">
                    <input name="ajax" value="true" type="hidden">
                    <input name="operacao" value="alterar" type="hidden">
                    <table {$tableAlign} class="tabelaFormulario" cellpadding="0" cellspacing="0">
                        <tbody>                     
                            {foreach from=$table key=id item=j}
                                <tr>

                                    <td class="form-label">
                                        <!-- <span class="idLabel-{$id}">{$j.label}</span> -->
                                        <label class="idLabel-{$id}">{$j.label}</label>
                                    </td>
                                    <td class="idForm" style="border:none;" idform="{$id}">
                                        <input name="{$j.name}" type="{$j.type}" class="idForm" id="{$id}">
                                    </td>
                                </tr>
                            {/foreach}                                            
                            <tr>
                                <div class="buttonsBottom"> 
                                    <div class="buttonsLeft">
                                        <td style="padding-top:10px;padding-left:30px;">{$botoesEsq}</td>
                                    </div>  
                                    <div class="buttonsRight">
                                        <td style="padding-top:10px;padding-left:80px;">{$botoesDir}</td>
                                    </div>                  
                                </div>     
                            </tr>        
                        </tbody>
                    </table>
                </form>
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
   
  </body>
</html>
