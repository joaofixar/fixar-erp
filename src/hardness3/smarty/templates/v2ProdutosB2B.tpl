<!DOCTYPE html>
<html lang="en">
{include file ="v2header.tpl" title="$titulo"}

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                {include file="v2menu.tpl"}
                <!-- Breadcrumb -->
                <div class="container-breadcrumb">
                    <div class="row">
                        <div class="col-md-6 breadc-m">
                            <div class="au-breadcrumb3">
                                <div class="au-breadcrumb-left hide-breadcrumb">
                                  <!--   <span class="au-breadcrumb-span">Você está aqui:</span>
                                   <ul class="list-unstyled list-inline au-breadcrumb__list">
                                        <li class="list-inline-item">
                                            <a href="/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true" class="btn-outline-bread">INÍCIO</a>
                                        </li>
                                        <li class="list-inline-item">{$titulo}</li>
                                    </ul> -->
                                </div>
                            </div>
                        </div>
                        <!-- Search -->
                        <div class="col-md-6">
                            <div class="au-breadcrumb-right form-header form-header2">
                                <input type="text" id='buscaProduto' placeholder="Buscar produtos..." value='{$buscaProduto}' class="au-input au-input--w435">
                                <button type="submit" id="botaoBuscar" class="au-btn--submit">
                                <i class="zmdi zmdi-search"></i></button>
                            </div>
                        </div>
                        <!-- fim search -->
                    </div>
                </div>
                <!-- fim breadcrumb -->
                <div class="container-fluid">
                    <!-- Content Row -->
                    <div class="row">
                    {foreach from=$tbody key=tbodykey item=i}    
                        {if is_numeric($tbodykey)}
                         {assign var="count" value="0"}    
                            <!-- Retira os títulos dos grid e começa a pegar a partir dos dados                                                -->
                            {foreach from=$i.dados key=id item=ii}                                                                      
                                {assign var="`$thead.$count.value`" value="$ii"} 
                                {assign var="count" value="`$count+1`"}
                            {/foreach} 
                            <!-- Product -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-primary shadow h-auto">
                                    <div class="card-body">
                                        {foreach from=$i.dados key=id item=ii}
                                            {if $id==1}
                                                {$T269_Id = $ii}
                                            {/if}
                                            {if $id==4}
                                            <div class="img-box">
                                                <img class="product-img" src="{$ii}"></img>
                                            </div>
                                            {/if}
                                            {if $id==3}
                                                {$D009_Quantidade_Estoque = $ii}
                                                {continue}
                                            {/if}
                                            {if $id==5}
                                            <div class="thumb-content-description box-description">
                                                    <font class="description-product">{$ii}</font>
                                            </div>
                                            {/if}
                                            {if $id==7}
                                                {if $mostrarPreco=='N'}
                                                    <div class="thumb-content">
                                                        <p class="item-price">Preço indisponível</p>
                                                    </div>
                                                {else}
                                                    <div class="thumb-content">
                                                        <p class="item-price">R$ {$ii|number_format:2:",":"."}</p>
                                                        {$Preco = $ii}
                                                    </div>
                                                {/if}
                                            {/if} 
                                        {/foreach} 
                                        {if $D009_Quantidade_Estoque<=0}       
                                            <div class="thumb-content">
                                                <a href="#" class="btn btn-unavailable">INDISPONÍVEL</a>
                                            </div>
                                        {else}                  
                                            {if $T269_Id > 0}
                                                <div class="thumb-content">
                                                    <a href="#" class="btn btn-cart">CARRINHO</a>
                                                </div>
                                            {else}
                                                <div class="thumb-content">
                                                    <button type="button" class="btn comprar" id="{$i.pk.pkCampo}" preco="{$Preco}">COMPRAR</a>
                                                </div>
                                            {/if}
                                        {/if}
                                    </div>
                                </div>
                            </div>  
                        {/if}
                    {/foreach}                  
                    </div>
                    <!-- /.row -->
                    {include file="v2pagination.tpl"}
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

           {include file="v2footer.tpl"}
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

</body>

</html>
