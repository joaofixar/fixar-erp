<?php
namespace hardness;

include('conf.php');

include('inc.conf.php');

$script="";

include('inc.header.php');

echo <<<EOT
	<div data-role="page" id="pageRequisicoes">
EOT;

$etapa = 5;
include('inc.header.bar.php');

//Var
$T191_Id = isset($_REQUEST['T191_Id']) ? $_REQUEST['T191_Id'] : '';
$T191_Flag_Status = isset($_REQUEST['T191_Flag_Status']) ? $_REQUEST['T191_Flag_Status'] : '';

//Altera o Status da Conversa
if($T191_Flag_Status != ''){
	$sqlUpdateStatus = "UPDATE T191 SET T191_Flag_status = '{$T191_Flag_Status}' WHERE T191_Id = {$T191_Id} ;";
	mysql_query($sqlUpdateStatus);
	$script .= "alert('Alteração do Status Realizada com sucesso!')";
}

//Define combobox T191_Flag_Status
$sqlStatus = "SELECT T191_Flag_Status,T191_C007_Id_De FROM T191 left join T192 on T192_T191_Id=T191_Id WHERE T191_Id = '{$T191_Id}'";
$resStatus = mysql_query($sqlStatus);
$mT191 = mysql_fetch_assoc($resStatus);

$comboStatus = '';
$comboStatus .= '<option value="P"'.(($mT191['T191_Flag_Status'] == 'P') ? ' selected="selected"' : '').'> Pendente </option>';
$comboStatus .= '<option value="C"'.(($mT191['T191_Flag_Status'] == 'C') ? ' selected="selected"' : '').'> Concluído </option>';


//Parametro de Mensagem e o Destinatario da Mensagem 
$mensagem = isset($_REQUEST['mensagem']) ? $_REQUEST['mensagem'] : '';
$destinatarioMensagem = isset($_REQUEST['destinatario']) ? $_REQUEST['destinatario'] : '';



//Envia a Mensagem
if($mensagem != '' && $destinatarioMensagem!=''){
	$sqlMensagem = "INSERT INTO T192 (T192_T191_Id, T192_C007_Id, T192_C007_Id_Destinatario, T192_Data, T192_Mensagem) VALUES ({$T191_Id}, {$g['usuarioAtual']}, {$mT191['T191_C007_Id_De']}, CURRENT_TIMESTAMP(), '{$mensagem}');";
	mysql_query($sqlMensagem);
    $sqlUpdateLido = "UPDATE T191 SET T191_Flag_Lido = 'N', T191_C007_Id_Lido = {$g['usuarioAtual']} WHERE T191_Id = {$T191_Id} ;";
    mysql_query($sqlUpdateLido);
}
else
{
	$sqlUpdateLido = "UPDATE T191 SET T191_Flag_Lido = 'S', T191_C007_Id_Lido = {$g['usuarioAtual']} WHERE T191_Id = {$T191_Id} AND T191_Flag_Lido != 'S' AND T191_C007_Id_Lido != {$g['usuarioAtual']} ;";
	mysql_query($sqlUpdateLido);
	
}

//Define combobox Destinatário da mensagem
	$sql = "SELECT * FROM C007 ORDER BY C007_Nome";
	$sqlUsuarios = mysql_query($sql);
	$comboDestinatario = "";
	while($rowDestinatario = mysql_fetch_array($sqlUsuarios)){								 
			$comboDestinatario .= "<option value=\"{$rowDestinatario['C007_Id']}\">
								 {$rowDestinatario['C007_Primeiro_Nome']}
							    </option>";
	}
		
echo <<<EOT
      	<div data-role="content">
        	<div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px; ">
	            <div class="ui-block-b" style="width: 100%;">     		
						<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
                           	<textarea placeholder="Enviar mensagem" name="mensagem" id="mensagem" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset"></textarea>
                    	</div> 	           	
                    	<div style="float:left;" valign="bottom">
                    	    <br />
					        <a data-role="button" data-transition="slide" data-inline="true" data-icon="forward" data-iconpos="left" onclick="\$.mobile.changePage( './conversaMensagens.php', {transition: 'fade', type:'post', data: 'destinatario=' + encodeURIComponent($('#destinatario').val()) + '&mensagem=' + encodeURIComponent($('#mensagem').val()) + '&T191_Id=' + {$T191_Id}} )" >
			            		Enviar
					        </a>
					    </div>
					    <div style="float:right;">
					        <a data-role="button" data-transition="slide" data-inline="true" onclick="\$.mobile.changePage( './conversaMensagens.php', {transition: 'fade', type:'post', data: 'T191_Id=' + {$T191_Id} + '&T191_Flag_Status=' + encodeURIComponent($('#T191_Flag_Status').val())})" >
			            		Alterar
					        </a>
					    </div>
					    <div style="float:right;">
					        <select name="T191_Flag_Status" id="T191_Flag_Status" data-inline="true">
						     	{$comboStatus}
						   </select>
					    </div>
					    <div style="float:right;">
					    	<br />
					    	Status atual > 
					    </div>
					    		
	            </div>
EOT;


// Requisições
$sql = <<<EOT
	SELECT T192_Id, 
	T192_T191_Id, 
	T191_C007_Id_De,
	T192_C007_Id,
	T192_Data,
	T192_Mensagem,
	DE.C007_Nome de,
	PARA.C007_Nome para
	FROM T192 
	LEFT JOIN T191 ON T191_Id=T192_T191_Id
	LEFT JOIN C007 AS DE ON DE.C007_Id=T191_C007_Id_De 
	LEFT JOIN C007 AS PARA ON PARA.C007_Id=T191_C007_Id_Para 
	WHERE (T191_C007_Id_De='{$g['usuarioAtual']}' OR T191_C007_Id_Para='{$g['usuarioAtual']}')
	AND T192_T191_Id='{$T191_Id}'
	ORDER BY T192_Data 
	LIMIT 100
EOT;
$res = mysql_query($sql);
echo mysql_error();
while ($row = mysql_fetch_assoc($res)) {
	$row['T192_Data'] = gCorrigeData($row['T192_Data']);
	
	if($row['T191_Flag_Status'] == 'P'){
		$row['T191_Flag_Status'] = "Pendente";
	} else if($row['T191_Flag_Status'] == 'C'){
		$row['T191_Flag_Status'] = "Concluído";
	}
//Altera o tema da linha se a mensagem não for do usuário logado
	if($g['usuarioAtual']!=$row['T192_C007_Id'])
	{
		$cor='#F5F7DF';
	}else{
		$cor='#ECECFF';
	}
	$row['de']=ucwords(strtolower($row['de']));
		echo <<<EOT
        	<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px; clear:both;">
			<li data-theme="c" style='background:$cor' data-icon="false">
				    <table style="width: 100%";>
	    				<tr>
	    					<td style="width: 50%;white-space:normal"><font style='font-weight:normal'>{$row['de']} disse</font> {$row['T192_Mensagem']}</td>
	    					<td style="width: 8%;white-space:normal" valign="top">{$row['T192_Data']}</td>
	    				</tr>
	    				<tr>
	    					<td style="width: 12%;white-space:normal;font-weight:normal;font-size:11px"; colspan=5 align=right><i>{$row['T189_Detalhes']}</i></td>
	    				</tr>
				    </table>
		    </li> 
			</ul>
EOT;
}

// Fim DIV 'content'
echo <<<EOT
		</div>
	</div>
EOT;

include('inc.footer.php');

echo <<<EOT
    <script>
    	$( document ).undelegate("#pageRequisicoes", "pageinit");
    	$( document ).delegate("#pageRequisicoes", "pageinit", function() {
			{$script}
    	});
    </script>
EOT;
