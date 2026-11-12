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
                                    <tr>                                       
                                    {if is_numeric($tbodykey)} 
                                        <td>{$i.dados[0]}</td>
                                        <td>{$i.dados[2]}</td>
                                        <td>{$i.dados[3]}</td>
                                        <td>{$i.dados[4]}</td>
                                        <td>{$i.dados[5]}</td>
                                        <td>R$ {$i.dados[6]}</td>
                                        <td>{$i.dados[7]}</td>
                                        <td>{$i.dados[8]}</td>
                                        <td>{$i.dados[9]}</td>
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
                                <tr>
                                    {if is_numeric($tbodykey)}  <!--retira a linha do título-->                                                                                                 
                                        <div class="card accordion" id="accordionExample">
                                            <div class="card-body-pedidos">
                                                <span class="m-0 font-weight-bold float-left-header">{$i.dados[2]} {$i.dados[3]}</span> <!--dados[0] Pedido no gridB2BPedidos-->                                                                  
                                            </div>    
                                            <a href="#collapseCardExample{$count}" class="d-flex hover-gray card-header py-3" data-toggle="collapse" data-target="#collapseCardExample{$count}" aria-expanded="false" aria-controls="collapseCardExample{$count}">
                                                <span class="d-flex text-title-dta-sale">                                                                    
                                                    <span class="mr-5 ">{$i.dados[5]}</span><!--dados[5] Transportadora no gridB2BPedidos-->                                                                      
                                                </span>                                                                     
                                                <span class="mr-2 font-weight-bold text-price">{$i.dados[6]}</span><!--dados[6] Valor no gridB2BPedidos--> 
                                            </a>
                                       
                                        </div>
                                        <div id="collapseCardExample{$count}" class="collapse background-details" aria-labelledby="collapseCardExample{$count}" data-parent="#accordionExample">
                                            <p class="detalhesPedido">
                                                PEDIDO: <b>{$i.dados[4]}</b>
                                                <br>TRANSPORTADORA: <b>{$i.dados[7]}</b>
                                                <br>VENDEDOR: <b>{$i.dados[8]}</b>
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
