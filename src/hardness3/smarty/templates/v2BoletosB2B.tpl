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
                                    {if $i.dados[9] == ""}
                                        {assign var="baixa" value="Aberto"}
                                        {assign var="color" value="yellow"}
                                    {else}
                                        {assign var="baixa" value="{$i.dados[9]}"}
                                        {assign var="color" value="blue"}
                                    {/if}   
                                    <tr>                                       
                                    {if is_numeric($tbodykey)} 
                                        <td>{$i.dados[0]}</td>
                                        <td>{$i.dados[3]}</td>
                                        <td>{$i.dados[4]}</td>
                                        <td>{$i.dados[5]}</td>
                                        <td>R$ {$i.dados[6]}</td>
                                        <td>{$i.dados[7]}</td>
                                        <td>{$i.dados[8]}</td>
                                        <td><span class="role {$color}">{$baixa}</span></td>
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
                                <!--verifica a cor da baixa-->
                                    {if $i.dados[9] == ""}
                                        {assign var="baixa" value="Aberto"}
                                        {assign var="color" value="yellow"}
                                    {else}
                                        {assign var="baixa" value="Baixa: {$i.dados[9]}"}
                                        {assign var="color" value="blue"}
                                    {/if}   
                                <tr>
                                    {if is_numeric($tbodykey)}  <!--retira a linha do título-->                                                                                                 
                                        <div class="card accordion" id="accordionExample">
                                            <div class="card-body-pedidos">
                                                <span class="m-0 font-weight-bold float-left-header">Título: {$i.dados[3]}</span>                                                                  
                                                <span class="role {$color}">{$baixa}</span>
                                            </div>    
                                            <a href="#collapseCardExample{$count}" class="d-flex hover-gray card-header py-3" data-toggle="collapse" data-target="#collapseCardExample{$count}" aria-expanded="false" aria-controls="collapseCardExample{$count}">
                                                <span class="d-flex text-title-dta-sale">                                                                    
                                                    <span class="mr-5 ">Vencimento:<br>{$i.dados[7]}</span>                                                                      
                                                    <span class="mr-5 hide-txt ">Emissão:<br>{$i.dados[6]}</span>
                                                </span>                                                                     
                                                <span class="mr-2 font-weight-bold text-price">{$i.dados[6]}</span>
                                            </a>
                                       
                                        </div>
                                        <div id="collapseCardExample{$count}" class="collapse background-details" aria-labelledby="collapseCardExample{$count}" data-parent="#accordionExample">
                                            <p class="detalhesPedido">
                                                <br>BOLETO: <b>{$i.dados[4]}</b>
                                                <br>PORTADOR: <b>{$i.dados[5]}</b>
                                                <br>EMISSÃO: <b>{$i.dados[7]}</b>
                                                <br>VENCIMENTO: <b>{$i.dados[8]}</b>
                                                <br>BAIXA: <b>{$i.dados[9]}</b>
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
