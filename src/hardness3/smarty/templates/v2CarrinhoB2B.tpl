<!DOCTYPE html>
<html lang="en">

{include file ="v2header.tpl" title="Carrinho"}

<script type="text/javascript">
    function mascaraValor(valor) {
       var valor = valor.toFixed(2);
       var valor = valor.toString().replace('.',',');      
       var valor = "R$ "+valor;    
       return valor;            
    }
</script>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                {include file="v2menu.tpl"}
                <!-- Filtro -->
                <!-- Fim Filtro -->

                <!-- Begin Page Content -->
                <div class="container">
                    <div class="card shopping-cart">
                        <div class="card-header bg-dark text-light">
                            <span class="mycart">Meu carrinho</span>
                            <button class="btn btn-outline-info btn-sm float-right-header" button id="maisProdutos">Continuar comprando</button>
                        </div>
                        <div class="card-body" id="container">
                            {foreach from=$tbody key=tbodykey item=i}
                                {if is_numeric($tbodykey)}
                                <div class="row-cart" id="row{$tbodykey}">
                                {assign var="count" value="0"}    
                                <!-- Retira os títulos dos grid e começa a pegar a partir dos dados                                                -->
                                    {foreach from=$i.dados key=id item=ii}                                                                      
                                        {assign var="`$thead.$count.value`" value="$ii"} 
                                        {assign var="count" value="`$count+1`"}
                                    {/foreach}  
                                   
                                    <div class="trash float-right-header">
                                        <button type="button" id='excluir' idProduto="{$i.pk.pkCampo}" row="row{$tbodykey}" class="btn btn-outline-danger btn-xs float-right-header button_excluir" tabindex="999">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="item-cart-1">
                                        <span class="img-responsive" id="produto-imagem">
                                            <img src={$Imagem}>
                                        </span>  
                                    </div>                                                         
                                    <span class="product-name desc-cart">{$Descricao}</span>
                                    <div class="item-cart-2">
                                        {* Preco unitario *}
                                        {if $Preco && $mostrarPreco=='N'}
                                            <div class="product-price">indisponível</div>
                                        {else}
                                            {if $tipoLoginUsuarioB2B=="R" && $PermitirRepresentanteEditarPrecoB2B=="S"}
                                               R$ <input type='text' tipoInput="preco" id="precoInput{$i.pk.pkCampo}" idProduto="{$i.pk.pkCampo}" value="{$Preco}" class="custom-size-btn" style="text-align:right;width:120px;">
                                            {elseif $tipoLoginUsuarioB2B=="VI" && $PermitirVendedorEditarPrecoB2B=="S"}
                                               R$ <input type='text' tipoInput="preco" id="precoInput{$i.pk.pkCampo}" idProduto="{$i.pk.pkCampo}" value="{$Preco}" class="custom-size-btn" style="text-align:right;width:120px;">
                                            {elseif $tipoLoginUsuarioB2B=="VE" && $PermitirVendedorExternoEditarPrecoB2B=="S"}
                                               R$ <input type='text' tipoInput="preco" id="precoInput{$i.pk.pkCampo}" idProduto="{$i.pk.pkCampo}" value="{$Preco}" class="custom-size-btn" style="text-align:right;width:120px;">
                                            {else}
                                                <div class="product-price">R$ {$Preco} x </div>
                                            {/if}
                                            <span class="displayN" id="preco{$i.pk.pkCampo}">R$ {$PrecoOriginal}</span>
                                        {/if} 
                                        {* Quantidade *}
                                        <input type='number' tipoInput="qtde" id="quantidade{$i.pk.pkCampo}" idProduto="{$i.pk.pkCampo}" value="{$Quantidade}" min="1" class="custom-size-btn">
                                        {* Subtotal *}
                                        {if $mostrarPreco=='N'}
                                            <span class="price-shopping-cart" class="valor_produto">indisponível</span>
                                        {else}
                                            <span class="product-subprice" id="subtotalProduto{$i.pk.pkCampo}">R$ {$Subtotal}</span> 
                                        {/if} 
                                    </div>                      
                                </div>   
                                {/if}       
                            {/foreach}  
                            <div class="frete-total">
                                <div class="div-observacao">
                                    <label class="btn-sm float-left-header frete">Observação frete / pagamento</label>
                                    <textarea id="inputObservacoes" rows="4" class="form-observacoes form-control"></textarea>
                                </div>
                                {if $mostrarPreco=='N'}
                                <div class="div-total">
                                    <h3 class="total-price">Preço indisponível</h3>
                                </div>
                                {else}
                                    <div class="div-total">
                                        <h3 class="total-price" id="totalCarrinho">Total: {$subtotalCarrinho}</h3>
                                        {if $botaoOrcaPedido=='1' && $itensCarrinho>0}
                                            <td id="botao" colspan="6"> <button type="button" id='gerarOrcamento' class="btn btn-success float-right-header botaoOrcamentoPedido">Gerar Orçamento</button></td>
                                        {elseif $botaoOrcaPedido=='2' && $itensCarrinho>0}
                                            <td id="botao" colspan="6"> <button type="button" id='gerarPedido' class="btn btn-success float-right-header botaoOrcamentoPedido">Gerar Pedido</button></td>
                                        {/if}
                                    </div>
                                {/if}
                            </div>                         
                        </div>
                        
                    </div>
                </div>                            
                <!-- /.Page Content -->
                <br><br><br>
            </div>
            <!-- End of Main Content -->
        </div>
    <!-- End of Content Wrapper -->

    <!-- Footer -->
    {include file="v2footer.tpl"}
    <!-- End of Footer -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
</body>

</html>
