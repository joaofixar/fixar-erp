<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');


//vars

$update = isset($_REQUEST['update']) && $_REQUEST['update'] ? true : false;
	
// Aplicar as Configurações Alteradas
$temErro = '';
if (isset($_REQUEST['C007_Email'])) {
	$C007_Email = strtolower(mysql_real_escape_string($_REQUEST['C007_Email']));
	$C007_Email_SMTP_Servidor = strtolower(mysql_real_escape_string($_REQUEST['C007_Email_SMTP_Servidor']));
	$C007_Email_SMTP_Porta = strtolower(mysql_real_escape_string($_REQUEST['C007_Email_SMTP_Porta']));
	$C007_Email_SMTP_Autenticacao = strtolower(mysql_real_escape_string($_REQUEST['C007_Email_SMTP_Autenticacao']));
	$C007_Email_POP_Servidor = strtolower(mysql_real_escape_string($_REQUEST['C007_Email_POP_Servidor']));
	$C007_Email_SMTP_Usuario = strtolower(mysql_real_escape_string($_REQUEST['C007_Email_SMTP_Usuario']));
	$C007_Email_SMTP_Senha = strtolower(mysql_real_escape_string($_REQUEST['C007_Email_SMTP_Senha']));
	$C007_Email_Assinatura = mysql_real_escape_string($_REQUEST['C007_Email_Assinatura']);
	//Update
	if ($update) {
	$sql = <<<EOT
		UPDATE C007 SET C007_Email='{$C007_Email}',
							C007_Email_SMTP_Servidor='{$C007_Email_SMTP_Servidor}', 
							C007_Email_SMTP_Porta='{$C007_Email_SMTP_Porta}', 
							C007_Email_SMTP_Autenticacao='{$C007_Email_SMTP_Autenticacao}',
							C007_Email_POP_Servidor='{$C007_Email_POP_Servidor}',
							C007_Email_SMTP_Usuario='{$C007_Email_SMTP_Usuario}',
							C007_Email_SMTP_Senha='{$C007_Email_SMTP_Senha}',
							C007_Email_Assinatura='{$C007_Email_Assinatura}'
		WHERE C007_Id='{$C007['C007_Id']}'
EOT;
	}
	mysql_query($sql);
	$script = <<<EOT
		$.mobile.changePage( "./orcamentoEnviarEmail.php?T003_Id={$T003_Id}", { transition: 'fade'} );
EOT;
}else{
	mysql_query($sql);
	$script = "";
}
		




include('inc.header.php');
echo <<<EOT
    <style>
        input{
            text-transform:lowercase;
        }
    </style>
	<div data-role="page" id="pageClientesPreCadastro">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./orcamentoEnviarEmailConfigurar.php?T003_Id={$T003_Id}&update={$update}" method="post" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="C007_Email">
	                                E-mail
	                            </label>
	                            <input name="C007_Email" id="C007_Email" value="{$C007['C007_Email']}" placeholder="" value="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="C007_Email_SMTP_Servidor">
	                                Servidor SMTP
	                            </label>
	                            <input name="C007_Email_SMTP_Servidor" id="C007_Email_SMTP_Servidor" value="{$C007['C007_Email_SMTP_Servidor']}" placeholder="" value="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="C007_Email_SMTP_Porta">
	                                Porta SMTP
	                            </label>
	                            <input name="C007_Email_SMTP_Porta" id="C007_Email_SMTP_Porta"  placeholder="" value="{$C007['C007_Email_SMTP_Porta']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="C007_Email_SMTP_Autenticacao">
	                                Autenticação
	                            </label>
	                            <input name="C007_Email_SMTP_Autenticacao" id="C007_Email_SMTP_Autenticacao" placeholder="" value="{$C007['C007_Email_SMTP_Autenticacao']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="C007_Email_POP_Servidor">
	                               Servidor IMAP/POP
	                            </label>
	                            <input name="C007_Email_POP_Servidor" id="C007_Email_POP_Servidor" placeholder="" value="{$C007['C007_Email_POP_Servidor']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="C007_Email_SMTP_Usuario">
	                                Usuário
	                            </label>
	                            <input name="C007_Email_SMTP_Usuario" id="C007_Email_SMTP_Usuario" placeholder="" value="{$C007['C007_Email_SMTP_Usuario']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="C007_Email_SMTP_Senha">
	                                Senha
	                            </label>
	                            <input name="C007_Email_SMTP_Senha" id="C007_Email_SMTP_Senha" placeholder="" value="{$C007['C007_Email_SMTP_Senha']}" type="password" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="C007_Email_Assinatura">
	                                Assinatura
	                            </label>
	                            <textarea placeholder="" name="C007_Email_Assinatura" id="C007_Email_Assinatura" value="{$C007['C007_Email_Assinatura']}" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">{$C007['C007_Email_Assinatura']}</textarea>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="fade" onclick="\$.mobile.changePage( './orcamentoEnviarEmail.php?T003_Id={$T003_Id}', { transition: 'fade'} );">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        <input value="Salvar" name="btnSalvar" type="submit" data-ajax="false"/>
                    </div>
                </div>
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageClientesPreCadastro", "pageinit");
    	$( document ).delegate("#pageClientesPreCadastro", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
if (isset($_REQUEST['btnSalvar'])) {
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
}
