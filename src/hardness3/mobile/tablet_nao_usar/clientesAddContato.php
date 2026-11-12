<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;

// Adicionar
$temErro = '';
if (isset($_REQUEST['D013_Nome_Contato'])) {
	$D013_Nome_Contato = strtoupper(mysql_real_escape_string($_REQUEST['D013_Nome_Contato']));
	$D013_E_Mail = strtoupper(mysql_real_escape_string($_REQUEST['D013_E_Mail']));
	$D013_DDD_Telefone_1 = strtoupper(mysql_real_escape_string($_REQUEST['D013_DDD_Telefone_1']));
	$D013_Telefone_1 = strtoupper(mysql_real_escape_string($_REQUEST['D013_Telefone_1']));
	$sql = "INSERT INTO D013 (D013_D024_Id, D013_Nome_Contato, D013_E_Mail, D013_DDD_Telefone_1, D013_Telefone_1) VALUES ('{$D024_Id}', '{$D013_Nome_Contato}', '{$D013_E_Mail}', '{$D013_DDD_Telefone_1}', '{$D013_Telefone_1}');";
	mysql_query($sql);
	
	if($_REQUEST['D013_Nome_Contato'] != ''){
	$sql = "UPDATE T003 SET T003_D013_Id = '{$g['mysqlLastId']}' WHERE T003_Id = '{$T003_Id}';";
	mysql_query($sql);
	}
	$script = <<<EOT
		$.mobile.changePage( "./clientes.php?T003_Id={$T003_Id}", { transition: 'slide'} );
EOT;
}
	
include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageClientesAddContato">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./clientesAddContato.php?T003_Id={$T003_Id}&D024_Id={$D024_Id}" method="post" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D013_Nome_Contato">
	                                Nome
	                            </label>
	                            <input name="D013_Nome_Contato" id="D013_Nome_Contato" placeholder="" value="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                            <label for="D013_E_Mail">
	                                E-mail
	                            </label>
	                            <input name="D013_E_Mail" id="D013_E_Mail" placeholder="" value="" type="text" />
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
				                <label for="D013_DDD_Telefone_1" style="float:left">
				                    Telefone
				                </label>
	                           	<input name="D013_DDD_Telefone_1" id="D013_DDD_Telefone_1" placeholder="" value="" type="text" style="width:40px; float: left;" />
	                            <input name="D013_Telefone_1" id="D013_Telefone_1" placeholder="" value="" type="text" style="float: left; width: 100px;" />     
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './clientes.php?T003_Id={$T003_Id}', { transition: 'slide'} );">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        <input value="Adicionar" type="submit" />
                    </div>
                </div>
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
	<script>
    	$( document ).undelegate("#pageClientesAddContato", "pageinit");
    	$( document ).delegate("#pageClientesAddContato", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
	</script>
EOT;
