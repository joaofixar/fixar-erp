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
                {include file ="v2menu.tpl"}
                {include file ="v2search.tpl"}

                <!-- Begin Page Content -->
                <!-- Grid web -->
                <div class="container-fluid">                
                    <!-- Page Heading -->
                    <div class="table-responsive table-data grid-web">
                        <table class="table">
                             <thead>
                                <tr>
                                    {foreach from=$thead item=i}
                                        <th {$i.extra}>
                                            {$i.value}
                                        </th>
                                    {/foreach}
                                </tr>
                            </thead>
<!--                             {foreach from=$tbody key=tbodykey item=i} 
                            {/foreach}  -->
                            <tbody>
                                {assign var="count" value="0"} 
                                {foreach from=$tbody key=tbodykey item=i} 
                                    <!--verifica a cor do status-->
                                    {if $i.dados[8] == "Coletado" || $i.dados[8] == "Pedido Entregue"}
                                        {assign var="color" value="blue"}
                                    {elseif $i.dados[8] == "Pendente" || $i.dados[8] == "Emitir NF" || $i.dados[8] == "Financeiro" || $i.dados[8] == "Financeiro" || $i.dados[8] == "Aguard.coleta" || $i.dados[8] == "Separando" || $i.dados[8] == "Imprimir"}
                                        {assign var="color" value="yellow"}
                                    {elseif $i.dados[8] == "Aprovado"}
                                        {assign var="color" value="green"}
                                    {elseif $i.dados[8] == "Negado" || $i.dados[8] == "Vencido"}
                                        {assign var="color" value="red"}
                                    {/if}
                                    <!--verifica a cor do status-->

                                    <tr>                                       
                                    {if is_numeric($tbodykey)} 
                                        <td>{$i.dados[0]}</td><!--dados[0] Pedido no gridB2BPedidos-->
                                        <td>{$i.dados[1]}</td><!--dados[1] Orçamento no gridB2BPedidos-->
                                        <td>{$i.dados[2]}</td><!--dados[1] Ordem de Compra no gridB2BPedidos-->
                                        <td>{$i.dados[3]}</td><!--dados[3] Data de emissão no gridB2BPedidos-->
                                        <td>{$i.dados[4]}</td><!--dados[4] Valor no gridB2BPedidos-->
                                        <td>{$i.dados[5]}</td><!--dados[5] Valor no gridB2BPedidos-->
                                        <td>R$ {$i.dados[6]}</td><!--dados[6] NF no gridB2BPedidos-->
                                        <td>{$i.dados[7]}</td><!--dados[7] Vendedor no gridB2BPedidos-->
                                        <td><span class="role {$color}">{$i.dados[8]}</span></td><!--dados[8] status no gridB2BPedidos-->
                                        <td>{$i.dados[9]}</td><!--dados[9] Vendedor no gridB2BPedidos-->
                                        <td>{$i.dados[10]}</td><!--dados[10] Vendedor no gridB2BPedidos-->
                                    </tr>
                                    {/if}
                                {/foreach}
                            </tbody>
                        </table>
                    </div>

                    <!-- Grid mobile -->
                    <div class="table-responsive grid-mobile">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <tbody>
                                {assign var="count" value="0"} 
                                {foreach from=$tbody key=tbodykey item=i} 
                                <!--verifica a cor do status-->
                                {if $i.dados[8] == "Coletado" || $i.dados[8] == 'Pedido Entregue'}
                                    {assign var="color" value="blue"}
                                {elseif $i.dados[8] == 'Pendente' || $i.dados[8] == 'Emitir NF' || $i.dados[8] == 'Financeiro' || $i.dados[8] == 'Financeiro' || $i.dados[8] == 'Aguard.coleta' || $i.dados[8] == 'Separando' || $i.dados[8] == 'Imprimir'}
                                    {assign var="color" value="yellow"}
                                {elseif $i.dados[8] == 'Aprovado'}
                                    {assign var="color" value="green"}
                                {elseif $i.dados[8] == 'Negado' || $i.dados[8] == 'Vencido'}
                                    {assign var="color" value="red"}
                                {else}
                                    {assign var="color" value="yellow"}
                                {/if} 
                                <tr>
                                    {if is_numeric($tbodykey)}  <!--retira a linha do título-->                                                                                                 
                                        <div class="card accordion" id="accordionExample">
                                            <div class="card-body-pedidos">
                                                <span class="m-0 font-weight-bold float-left-header">PEDIDO {$i.dados[1]}</span> <!--dados[1] Pedido no gridB2BPedidos-->                                                                  
                                                <span class="role {$color}">{$i.dados[8]}</span><!--dados[8] Status no gridB2BPedidos--> 
                                            </div>    
                                            <a href="#collapseCardExample{$count}" class="d-flex hover-gray card-header py-3" data-toggle="collapse" data-target="#collapseCardExample{$count}" aria-expanded="false" aria-controls="collapseCardExample{$count}">
                                                <span class="d-flex text-title-dta-sale">                                                                    
                                                    <span class="mr-5 hide-txt">{$i.dados[7]}</span><!--dados[7] Transportadora no gridB2BPedidos-->                                                                      
                                                    <span class="mr-5 font-weight-bold">{$i.dados[5]}</span><!--dados[5] data de emissão no gridB2BPedidos--> 
                                                </span>                                                                     
                                                <span class="mr-2 font-weight-bold text-price">{$i.dados[6]}</span><!--dados[6] Valor no gridB2BPedidos--> 
                                            </a>
                                       
                                        </div>
                                        <div id="collapseCardExample{$count}" class="collapse background-details" aria-labelledby="collapseCardExample{$count}" data-parent="#accordionExample">
                                            <p class="detalhesPedido">
                                                EMISSÃO: <b>{$i.dados[5]}</b>
                                                <br>TRANSPORTADORA: <b>{$i.dados[7]}</b>
                                                <br>VENDEDOR: <b>{$i.dados[9]}</b>
                                                <br>ORÇAMENTO: <b>{$i.dados[2]}</b>
                                                <br>ORDEM DE COMPRA: <b>{$i.dados[4]}</b>
                                                <br>NOTA FISCAL: <a href="#" class="nf-td"><b>{$i.dados[3]}</b></a>
                                            </p>
                                        </div>
                                    {/if}
                                    {assign var="count" value="`$count+1`"}
                                {/foreach}
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {include file="v2pagination.tpl"}
                <!-- /.Page Content -->
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
