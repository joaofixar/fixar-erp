<!DOCTYPE html>
<html lang="en">
{include file ="v2header.tpl" title="$titulo"}

<body>
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                {include file ="v2menu.tpl"}

                <!-- Begin Page Content -->
                <div class="container">
                    <div class="col-lg-6 center">
                        <div class="text-center">
                            <h1 class="text-gray-900 mb-4 pass-alter">Perfil</h1>
                        </div>
                        {* Trazendo os dados do perfil logado *}
                        <div class="user form-group" cellpadding="0" cellspacing="0"> 
                            {foreach from=$dadosPerfil key=id item=j}    
                                <div class="idForm" >
                                    <span class="idLabel">{$j.label}</span>
                                    <input type="text" value="{$j.valor}" class="idForm form-control form-control-user dadosPerfil" disabled=true>
                                </div>
                            {/foreach}
                        </div>
                        {if $tipoLoginUsuarioB2B=="C"}
                            <div class="text-center">
                                <h1 class="text-gray-900 mb-4 pass-alter">Alterar senha</h1>
                            </div>
                            <form class="user form-group" action="{$href}" method="POST" id="form" name="form">
                                <input name="ajax" value="true" type="hidden">
                                <input name="operacao" value="alterar" type="hidden">
                                <div {$tableAlign} class="tabelaFormulario" cellpadding="0" cellspacing="0">
                                    <div>                     
                                        {foreach from=$table key=id item=j}
                                            <div>
                                                <div class="form-label">
                                                    <!-- <span class="idLabel-{$id}">{$j.label}</span> -->
                                                </div>
                                                <br>
                                                <div class="idForm" idform="{$id}">
                                                    <input name="{$j.name}" type="{$j.type}" placeholder="{$j.label}"class="idForm form-control form-control-user" id="{$id}">
                                                </div>
                                            </div>
                                        {/foreach}                                  
                                        <div>
                                            <div class="buttonsBottom"> 
                                                {$botoesEsq}
                                                {$botoesDir}
                                            </div>     
                                        </div>        
                                    </div>
                                </div>
                            </form>
                        {/if}   
                    </div>
                </div>                               
                <!-- /.Page Content -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
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
