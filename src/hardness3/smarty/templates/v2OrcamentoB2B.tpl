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
                                    {assign var="`$thead.$count.value`" value="$ii"}
                                        {if $thead.$count.value == statusPedido || $thead.$count.value == idPedido || $thead.$count.value == infoPedido} 
                                            {assign var="count" value="`$count+1`"} 
                                           {continue} 
                                        {/if}
                                        {assign var="count" value="`$count+1`"}
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
                                    {if $i.dados[1] != "-"}
                                        {if $i.dados[7] == "Coletado" || $i.dados[7] == "Pedido Entregue"}
                                            {assign var="color" value="blue"}
                                        {elseif $i.dados[7] == "Pendente" || $i.dados[7] == "Emitir NF" || $i.dados[7] == "Financeiro" || $i.dados[7] == "Aguard.coleta" || $i.dados[7] == "Separando" || $i.dados[7] == "Imprimir"}
                                            {assign var="color" value="yellow"}
                                        {elseif $i.dados[7] == "Aprovado"}
                                            {assign var="color" value="green"}
                                        {elseif $i.dados[7] == "Negado" || $i.dados[7] == "Vencido"}
                                            {assign var="color" value="red"}
                                        {/if}
                                    {else}
                                        {assign var="color" value="none"}
                                    {/if}                                     

                                    <tr>                                       
                                    {if is_numeric($tbodykey)} 
                                        <td>{$i.dados[0]}</td>
                                        <td>{$i.dados[1]}</td>
                                        <td>{$i.dados[3]}</td>
                                        <td>R$ {$i.dados[4]}</td>
                                        <td>{$i.dados[5]}</td>
                                        <td>{$i.dados[6]}</td>
                                        <td>{$i.dados[7]}</td>
                                        <td>{$i.dados[10]}</td>
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
                                    {if $i.dados[1] != "-"}
                                        {assign var="status" value="{$i.dados[8]}"}
                                        {if $i.dados[7] == "Coletado" || $i.dados[7] == "Pedido Entregue"}
                                            {assign var="color" value="blue"}
                                        {elseif $i.dados[7] == "Pendente" || $i.dados[7] == "Emitir NF" || $i.dados[7] == "Financeiro" || $i.dados[7] == "Aguard.coleta" || $i.dados[7] == "Separando" || $i.dados[7] == "Imprimir"}
                                            {assign var="color" value="yellow"}
                                        {elseif $i.dados[7] == "Aprovado"}
                                            {assign var="color" value="green"}
                                        {elseif $i.dados[7] == "Negado" || $i.dados[7] == "Vencido"}
                                            {assign var="color" value="red"}
                                        {/if}
                                    {else}
                                        {assign var="status" value="sem pedido"}
                                        {assign var="color" value="grey"}
                                    {/if}   
                                <tr>
                                    {if is_numeric($tbodykey)}                                                                                                 
                                        <div class="card accordion" id="accordionExample">
                                            <div class="card-body-pedidos">
                                                <span class="m-0 font-weight-bold float-left-header">ORÇ. {$i.dados[0]}</span>                                                                 
                                                <div class="role {$color} hide-color">{$status}</div>
                                            </div>    
                                            <a href="#collapseCardExample{$count}" class="d-flex hover-gray card-header py-3" data-toggle="collapse" data-target="#collapseCardExample{$count}" aria-expanded="false" aria-controls="collapseCardExample{$count}">
                                                <span class="d-flex text-title-dta-sale">                                                                    
                                                    <span class="mr-5 ">Emissão: {$i.dados[2]}</span>                                                                      
                                                    <span class="mr-5 hide-txt ">{$i.dados[6]}</span>
                                                </span>                                                                     
                                                <span class="mr-2 font-weight-bold text-price">R${$i.dados[3]}</span>
                                            </a>
                                        </div>
                                        <div id="collapseCardExample{$count}" class="collapse background-details" aria-labelledby="collapseCardExample{$count}" data-parent="#accordionExample">
                                            <div class="detalhesPedido">
                                                ORÇAMENTO: <b>{$i.dados[0]}</b>
                                                <br>EMISSÃO: <b>{$i.dados[2]}</b>
                                                <br>VALOR TOTAL: <b>R${$i.dados[3]}</b>
                                                <br>PEDIDO: <b>{$i.dados[4]}</b>
                                                <br>VENDEDOR: <b>{$i.dados[6]}</b>
                                                <br>NF: <a href="#" class="nf-td"><b>{$i.dados[5]}</b></a>
                                            </div>
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
