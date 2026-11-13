<!DOCTYPE html>
<html lang="en">
<body>
<div id="content">
    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow sidebarNavigation" data-sidebarClass="navbar-light">          
        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 navbar-toggle" data-toggle="collapse">
            <i class="fa fa-bars"></i>
        </button>
        
        <ul class="navbar-nav collapse navbar-collapse hide">
            <div class="color-bg">
                <img class="img-logo hide-logo circle" src="{$logo}">
                <div class="hide-empresa text-gray-600">{$empresaAtualNome}</div>
            </div>
            <li class="menu-li"><a href="/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true" class="menu">Produtos</a></li>
            {if $mostrarOrcamentos=='S'}
            <li class="menu-li"><a href="/v2b2b/b2b001/grid/gridB2BOrcamentos/?ajax=true" class="menu">Orçamentos</a></li>
            {/if}
            {if $mostrarPedidos=='S'}
            <li class="menu-li"><a href="/v2b2b/b2b001/grid/gridB2BPedidos/?ajax=true" class="menu">Pedidos</a></li>
            {/if}
            {if $mostrarNotasFiscais=='S'}
            <li class="menu-li"><a href="/v2b2b/b2b001/grid/gridB2BNotasFiscais/?ajax=true" class="menu">Notas Fiscais</a></li>
            {/if}
            {if $mostrarBoletos=='S'}
            <li class="menu-li"><a href="/v2b2b/b2b001/grid/gridB2BBoletos/?ajax=true" class="menu">Financeiro</a></li>
            {/if}
        </ul>

        <!-- Topbar Navbar -->
        <ul class="navbar-nav">
            <!-- Nav Item - Messages -->
            <li class="nav-item dropdown no-arrow mx-1">
<!--                  <a class="nav-link dropdown-toggle no-hover" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-envelope fa-fw"></i> -->
                    <!-- Counter - Messages -->
              <!--       <span class="badge badge-danger badge-counter">7</span>
                </a> -->
                <!-- Dropdown - Messages -->
                <!-- <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                    <h6 class="dropdown-header">Notificações</h6>
                    <a class="dropdown-item d-flex align-items-center no-hover" href="#">
                        <div class="dropdown-list-image mr-3">
                            <img class="rounded-circle" src="https://source.unsplash.com/fn_BT9fwg_E/60x60" alt="">
                            <div class="status-indicator bg-success"></div>
                        </div>
                        <div class="font-weight-bold">
                            <div class="text-truncate">Hi there! I am wondering if you can help me with a problem I've been having.</div>
                            <div class="small text-gray-500">Emily Fowler · 58m</div>
                        </div>
                    </a>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="dropdown-list-image mr-3">
                            <img class="rounded-circle" src="https://source.unsplash.com/AU4VPcFN4LE/60x60" alt="">
                            <div class="status-indicator"></div>
                        </div>
                        <div>
                            <div class="text-truncate">I have the photos that you ordered last month, how would you like them sent to you?</div>
                            <div class="small text-gray-500">Jae Chun · 1d</div>
                        </div>
                    </a>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="dropdown-list-image mr-3">
                            <img class="rounded-circle" src="https://source.unsplash.com/CS2uCrpNzJY/60x60" alt="">
                            <div class="status-indicator bg-warning"></div>
                        </div>
                        <div>
                            <div class="text-truncate">Last month's report looks great, I am very happy with the progress so far, keep up the good work!</div>
                            <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                        </div>
                    </a>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="dropdown-list-image mr-3">
                            <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="">
                            <div class="status-indicator bg-success"></div>
                        </div>
                        <div>
                            <div class="text-truncate">Am I a good boy? The reason I ask is because someone told me that people say this to all dogs, even if they aren't good...</div>
                            <div class="small text-gray-500">Chicken the Dog · 2w</div>
                        </div>
                    </a>
                    <a class="dropdown-item text-center text-blue-500" href="#">Abrir notificações</a>
                </div>
            </li> -->
            <!-- Nav Item - Shopping cart -->
            {if $mostrarCarrinho=='S'}
            <li>
                <div class="btn btn-outline-cart btn-sm float-right-header cart" id="divCarrinho">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="hide-text">Carrinho:&nbsp;</span>
                    <span class="cart-amunt" id="Carrinho">
                    {$itensCarrinho}
                    </span>
                </div>
<!--                 <a class="nav-link dropdown-toggle no-hover" href="/b2b/b2b001/grid/gridB2BCarrinho?ajax=true&buscaProduto=" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="fas fa-shopping-cart fa-fw"></span>
                    <span class="badge badge-danger badge-counter"  id="Carrinho">{$itensCarrinho}</span>
                </a> -->
                <!-- Dropdown - Shopping cart -->
                <!-- <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                    <h6 class="dropdown-header">Meu Carrinho</h6>
                        <a class="dropdown-item d-flex align-items-center no-hover" href="/b2b/b2b001/grid/gridB2BCarrinho?ajax=true&buscaProduto=" id="row{$tbodykey}">
                            <div class="dropdown-list-image mr-3">
                                {$Imagem}
                            </div>
                            <div class="font-weight-bold">
                                <div class="text-truncate">{$Descricao}</div>
                                <div class="medium text-green-bold-500">R${$Preco}</div>
                                <div class="small text-gray-500">{$Quantidade} qtde</div>
                            </div>
                        </a>
                    <a class="dropdown-item text-center no-hover text-green-bolder-500" href="/b2b/b2b001/grid/gridB2BCarrinho?ajax=true&buscaProduto=">TOTAL:{$subtotalCarrinho}</a>
                    <a class="dropdown-item text-center text-blue-500" href="/b2b/b2b001/grid/gridB2BCarrinho?ajax=true&buscaProduto=">Abrir meu carrinho</a>
                </div> -->
            </li>
            {else}
            <li></li>
            {/if}

            <div class="topbar-divider"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div style="height:auto;">
                        <div class="mr-2 d-none text-gray-600 small nomeCliente" style="width:100%">{$clienteAtualContatoNome}</div>
                        <div class="mr-2 d-none text-gray-600 small nomeCliente" style="width:100%">{$clienteAtualNome}</div>
                    </div>
                    <img class="img-profile rounded-circle" src="/hardness3/smarty/B2B/images/login.png">
                </a>
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="/v2b2b/b2b001/form/b2b001FormSenha?ajax=true">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Perfil
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/hardness3/outros/b2b/">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Sair
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- End of Topbar -->

    <!-- Início do carrossel -->
<!--                 <section class="welcome2 p-t-40 p-b-55">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="welcome2-inner m-t-60">
                                    <div class="welcome2-greeting">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section> -->
    <!-- Fim do carrossel -->

<!-- End of Content Wrapper -->
</body>

</html>
