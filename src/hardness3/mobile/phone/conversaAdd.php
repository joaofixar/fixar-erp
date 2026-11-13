<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
$uniqId=uniqid();
$script="";
include('inc.header.php');


//Criar Conversa
/*if($btnEnviar!=''){
	$T191_Assunto = $_REQUEST['T191_Assunto'];
	$T192_Mensagem = $_REQUEST['T192_Mensagem'];
	$T192_C007_Id_Destinatario = $_REQUEST['T192_C007_Id_Destinatario'];
	//Insere a Conversa
	$sqlConversa = <<<EOT
	INSERT INTO T191 (T191_Assunto) 
		VALUES ('{$T191_Assunto}');
EOT;
	mysql_query($sqlConversa);
	
	//Insere Mensagem
	$sqlMensagem = <<<EOT
	INSERT INTO T192 (T192_T191_Id,
						T192_C007_Id,
						T192_C007_Id_Destinatario,
						T192_Data,
						T192_Mensagem) 
						VALUES 
						({$g['mysqlLastId']},
						{$g['usuarioAtual']},
						{$T192_C007_Id_Destinatario},
						CURRENT_TIMESTAMP(),
						'{$T192_Mensagem}');
EOT;
	mysql_query($sqlMensagem);
	$script .= "alert('Mensagem Enviada Com Sucesso!');";
	//$script .= "\$.mobile.changePage( './conversas.php', { transition: 'fade'} );";
}
*/

//Define combobox C007(Usuários)
$sql = "SELECT * FROM C007 ORDER BY C007_Primeiro_Nome";
$sqlUsuario = mysql_query($sql);
$selectUsuario = "";
while($rowUsuario = mysql_fetch_array($sqlUsuario)){								 
	$selectUsuario .= "<option value=\"{$rowUsuario['C007_Id']}\">
						 {$rowUsuario['C007_Primeiro_Nome']}
					    </option>";
}


echo <<<EOT
	<div data-role="dialog" id="conversaAdd">
		<div data-role="header" data-theme="d" style="margin: 2% auto 15px;">
			<h1>Nova Conversa</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form action="./conversas.php?acao=addConversa" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 100%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T191_Assunto">
	                                Assunto: 
	                            </label>
	                            	<input name="T191_Assunto" id="T191_Assunto" value="" type="text" />
	                        </fieldset>
	                     </div>
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T192_Mensagem">
	                                Mensagem:
	                            </label>
	                            	<textarea placeholder="" name="T192_Mensagem" id="T192_Mensagem" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset"></textarea>
	                        </fieldset>
	                     </div>
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                        	<div style="float:left; width:23%;">
		                            <label for="T192_C007_Id_Destinatario" class="ui-input-text">
		                                Para:
		                            </label>
	                            </div>
	                            <div style="width:63%; float:left;">
	                            	<select data-native-menu="true" name="T192_C007_Id_Destinatario" id="T192_C007_Id_Destinatario" >
	                        			<option value="" selected="selected">Selecione o Destinatário</option>
										{$selectUsuario}
		                    		</select>
		                    	</div>	
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
					<div class="ui-block-a">
						<a data-role="button" data-transition="slide" data-rel="back">
            				Voltar
        				</a>
        			</div>
                	<div class="ui-block-b">
	                    <input name="btnEnviar" value="Enviar" type="submit" data-ajax="false" />
                	</div>   
                </div>
            </form>    
		</div>
	</div>
	
	 <script>
    	$( document ).undelegate("#conversaAdd", "pageinit");
    	$( document ).delegate("#conversaAdd", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
