<?php
namespace hardness;

include('conf.php');

include('inc.conf.php');

$script="";

include('inc.header.php');

echo <<<EOT
	<div data-role="page" id="pageConversas">
EOT;

$etapa = 5;
include('inc.header.bar.php');

$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : '';

//Incluir Conversa
if($acao=='addConversa'){
	$uniqId = uniqid();
	$T191_Assunto = $_REQUEST['T191_Assunto'];
	$T192_Mensagem = $_REQUEST['T192_Mensagem'];
	$T192_C007_Id_Destinatario = $_REQUEST['T192_C007_Id_Destinatario'];
	//Insere a Conversa
	$sqlConversa = <<<EOT
	INSERT INTO T191 (T191_Assunto, T191_Flag_Lido, T191_C007_Id_Lido, T191_C007_Id_De, T191_C007_Id_Para) 
		VALUES ('{$T191_Assunto}', 'N', '{$g['usuarioAtual']}', '{$g['usuarioAtual']}', {$T192_C007_Id_Destinatario});
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
    
    $sqlUpdateLido = "UPDATE T191 SET T191_Flag_Lido = 'N' WHERE T191_Id = {$g['mysqlLastId']} ;";
    mysql_query($sqlUpdateLido);

	$script .= "alert('Mensagem Enviada Com Sucesso!');";
	$script .= "\$.mobile.changePage( './conversas.php?ajax=true&refresh={$uniqId}', { transition: 'fade'} );";
}


//Parametros de Pesquisa e Coluna da Pesquisa selecionada em requisições
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/conversas';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'T192_Data DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
	file_put_contents($arqFiltro, serialize($pesquisa));
}
$pesquisa = unserialize(file_get_contents($arqFiltro));
if (isset($_REQUEST['pesquisa'])) {
	$pesquisa['busca'] = $_REQUEST['pesquisa'];
	file_put_contents($arqFiltro, serialize($pesquisa));
}
if (isset($_REQUEST['colunaPesquisa'])) {
	$pesquisa['colunaBusca'] = $_REQUEST['colunaPesquisa'];
	file_put_contents($arqFiltro, serialize($pesquisa));
}
if (isset($_REQUEST['coluna'])) {
	$pesquisa['tipoOrdenacao'] = $_REQUEST['tipoOrdenacao'];
	$pesquisa['coluna'] = $_REQUEST['coluna'];
    $pesquisa['ordem'] = $_REQUEST['coluna'] . isset($_REQUEST['tipoOrdenacao']) ? ' ' . $_REQUEST['coluna'] . ' ' . $_REQUEST['tipoOrdenacao'] : ' DESC';
    file_put_contents($arqFiltro, serialize($pesquisa));
}

//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="T192_Data"'.(($pesquisa['colunaBusca'] == 'T192_Data') ? ' selected="selected"' : '').'> Data </option>';
$comboPesquisa .= '<option value="T191_Assunto"'.(($pesquisa['colunaBusca'] == 'T191_Assunto') ? ' selected="selected"' : '').'> Assunto </option>';
$comboPesquisa .= '<option value="DE.C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'DE.C007_Primeiro_Nome') ? ' selected="selected"' : '').'> De </option>';
$comboPesquisa .= '<option value="PARA.C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'PARA.C007_Primeiro_Nome') ? ' selected="selected"' : '').'> Para </option>';


echo <<<EOT
      	<div data-role="content">
        	<div class="ui-grid-a" style="margin-top: -10px; margin-bottom: 5px; ">
             <div class="ui-block-a" style="width: 20%;">
                    <div>
                    	<a data-role="button" data-inline="true" data-transition="fade" onclick="\$.mobile.changePage( './conversaAdd.php?', { transition: 'fade'} );" data-ajax="false">
								Novo assunto
		                </a>
				    </div>
                </div>
	            <div class="ui-block-b" style="width: 60%;">     		
						<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input placeholder="Procurar Conversa" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarConversa">
                    	</div>    
                    	 <div style="float:left;">
                    		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
						     	{$comboPesquisa}
						   </select>
					    </div>		           	
                    	<div style="float:left;">
					        <a data-role="button" data-transition="slide" data-inline="true" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './conversas.php', {transition: 'fade', type:'post', data: 'colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarConversa').val())} )" >
			            		Procurar
					        </a>
					    </div>		
	            </div>
        	
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px; clear:both;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
EOT;
					//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
					$T192_Data = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T192_Data' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
					$T191_Assunto = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T191_Assunto' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
					$DE_C007_Primeiro_Nome = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'DE.C007_Primeiro_Nome' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
					$PARA_C007_Primeiro_Nome = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'PARA.C007_Primeiro_Nome' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
					$T191_Flag_Status = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T191_Flag_Status' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
					
					//Define Icone de Ordenação na coluna do Grid
					//Define a imagem
					if(isset($pesquisa['coluna']) && $pesquisa['tipoOrdenacao'] == 'DESC'){
						$iconeOrdenacao = "<img src='/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/arrow-down-3.png'></img>";
					}else if(isset($pesquisa['coluna']) && $pesquisa['tipoOrdenacao'] == 'ASC'){
						$iconeOrdenacao = "<img src='/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/arrow-up-3.png'></img>";
					}else{
						$iconeOrdenacao = '';
					}
					//Verifica qual coluna foi selecionada para mostrar o icone apenas na coluna selecionada
					$T192_Data_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T192_Data') ? $iconeOrdenacao : '';
					$T191_Assunto_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T191_Assunto') ? $iconeOrdenacao : '';
					$DE_C007_Primeiro_Nome_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'DE.C007_Primeiro_Nome') ? $iconeOrdenacao : '';
					$PARA_C007_Primeiro_Nome_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'PARA.C007_Primeiro_Nome') ? $iconeOrdenacao : '';
					$T191_Flag_Status_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T191_Flag_Status') ? $iconeOrdenacao : '';
echo <<<EOT
	    						<td style="width: 8%"><a href="conversas.php?coluna=T192_Data&tipoOrdenacao={$T192_Data}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T192_Data_Icone}</a></td>
	    						<td style="width: 48%;padding:0px 7px 0px 0px;" align="left"><a href="conversas.php?coluna=T191_Assunto&tipoOrdenacao={$T191_Assunto}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Assunto{$T191_Assunto_Icone}</a></td>
	    						<td style="width: 10%" align="left"><a href="conversas.php?coluna=DE.C007_Primeiro_Nome&tipoOrdenacao={$DE_C007_Primeiro_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">De{$DE_C007_Primeiro_Nome_Icone}</a></td>
	    						<td style="width: 10%" align="left"><a href="conversas.php?coluna=PARA.C007_Primeiro_Nome&tipoOrdenacao={$PARA_C007_Primeiro_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Para{$PARA_C007_Primeiro_Nome_Icone}</a></td>
	    						<td style="width: 10%" align="center">Mensagens</td>
	    						<td style="width: 10%"><a href="conversas.php?coluna=T191_Flag_Status&tipoOrdenacao={$T191_Flag_Status}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Status{$T191_Flag_Status_Icone}</a></td>
	    					</tr>
	    				</table>
                    </li>
EOT;


if (empty($pesquisa['ordem']))
{
    $pesquisa['ordem']='T192_Data desc';
}

// Requisições
$sql = <<<EOT
	SELECT T191_Id, 
	T191_Flag_Status, 
	T191_Assunto,
	T191_Flag_Lido,
	(select count(*) from T192 where T192_T191_Id=T191_Id) Total_Mensagens, 
	T192_Data,
	T191_C007_Id_Para,
	DE.C007_Primeiro_Nome de,
	PARA.C007_Primeiro_Nome para,
	T192_Mensagem
	FROM T191 
	LEFT JOIN T192 ON T192_T191_Id = T191_Id
	LEFT JOIN C007 AS DE ON DE.C007_Id=T191_C007_Id_De 
	LEFT JOIN C007 AS PARA ON PARA.C007_Id=T191_C007_Id_Para 
	WHERE (
	(1=1)
EOT;
$sql .= " AND (T191_C007_Id_De='{$g['usuarioAtual']}' OR T191_C007_Id_Para='{$g['usuarioAtual']}') AND T192_Id = (select T192_Id from T192 where T192_T191_Id=T191_Id order by T192_Id desc limit 1) ";
	//Verifica Se foi requisitada a pesquisa
	if(isset($pesquisa['busca']) && $pesquisa['busca']!=''){
		//Se a Coluna foi Selecionada faz pesquisa pela coluna, senão fz pesquisa geral
		if(isset($pesquisa['colunaBusca']) && $pesquisa['colunaBusca']!=''){
			//Se a Coluna Escolhida = Data
			if($pesquisa['colunaBusca']=='T192_Data'){
				$sql .= "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
			}else{
				$sql .= " AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
			}	
		}else{
			$sql .= " AND (date_format(T192_Data,'%d/%m/%Y')  LIKE '%{$pesquisa['busca']}%' OR T191_Id LIKE '%{$pesquisa['busca']}%' OR DE.C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%' OR PARA.C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%' OR T191_Assunto LIKE '%{$pesquisa['busca']}%')";		
		}
	}
$sql .= <<<EOT
	)
	GROUP BY T191_Id
	ORDER BY T191_Flag_Status desc, {$pesquisa['ordem']} 
	LIMIT 100
EOT;
$res = mysql_query($sql);
echo mysql_error();
while ($row = mysql_fetch_assoc($res)) {
	
	$sql = <<<EOT
		SELECT
		T192_Data,
		T191_C007_Id_Para,
		DE.C007_Primeiro_Nome de,
		PARA.C007_Primeiro_Nome para,
		ULTIMO.C007_Primeiro_Nome ultimo,
		T192_Mensagem
		FROM T191 
		LEFT JOIN T192 ON T192_T191_Id = T191_Id
		LEFT JOIN C007 AS DE ON DE.C007_Id=T191_C007_Id_De
		LEFT JOIN C007 AS PARA ON PARA.C007_Id=T191_C007_Id_Para 
		LEFT JOIN C007 AS ULTIMO ON ULTIMO.C007_Id=T192_C007_Id 
		WHERE T192_T191_Id='{$row['T191_Id']}'
		ORDER BY T192_Id desc
		LIMIT 1
EOT;
	$res_ultima = mysql_query($sql);
	echo mysql_error();
	$row_ultima = mysql_fetch_assoc($res_ultima);

	$row['T192_Data'] = gCorrigeData($row['T192_Data']);
	$row['T191_Assunto'] = strtoupper($row['T191_Assunto']);
	$row_ultima['C007_Primeiro_Nome']=ucwords(strtolower($row_ultima['C007_Primeiro_Nome']));
	$row_ultima['T192_Data'] = gCorrigeData($row_ultima['T192_Data']);
	
	if($row['T191_Flag_Status'] == 'P'){
		$row['T191_Flag_Status'] = "Pendente";
        $cor = "#585858";
		$cor_status="#585858";
	} else if($row['T191_Flag_Status'] == 'C'){
		$row['T191_Flag_Status'] = "Concluído";
		$cor="#C0C0C0";
		$cor_status="#C0C0C0";
	}

	if($row['T191_Flag_Lido'] == 'N')
	{
		$cor="blue";
		$cor_status="blue";
	}

		echo <<<EOT
					<li data-theme="c" data-icon="false">
				<a href="./conversaMensagens.php?T191_Id={$row['T191_Id']}&T192_C007_Id_Destinatario={$row['T191_C007_Id_Para']}" data-ajax="false" data-transition="slide">
				    <table style="width: 100%;color:$cor;">
	    				<tr>
	    					<td style="width: 8%;white-space:normal" valign=top rowspan=2>{$row['T192_Data']}</td>
	    					<td style="width: 48%;padding:0px 7px 0px 0px;" align="left" valign="top">{$row['T191_Assunto']}</td>
	    					<td style="width: 10%;white-space:normal" align="left" valign="top">{$row['de']}</td>
	    					<td style="width: 10%;white-space:normal" align="left" valign="top">{$row['para']}</td>
	    					<td style="width: 10%;white-space:normal" align="center" valign="top">({$row['Total_Mensagens']})</td>
	    					<td style="width: 10%;white-space:normal; color:$cor_status" valign="top">{$row['T191_Flag_Status']}</td>
	    				</tr>
	    				<tr>
	    					<td style="white-space:normal;font-weight:normal;"; colspan=5 align=left>Última> {$row_ultima['ultimo']} disse: <i>{$row_ultima['T192_Mensagem']} ({$row_ultima['T192_Data']})</i></td>
	    				</tr>
				    </table>
	    		</a>
		    </li>
EOT;
}

// Fim DIV 'content'
echo <<<EOT
			</ul>
		</div>
	</div>
EOT;

include('inc.footer.php');

echo <<<EOT
    <script>
    	$( document ).undelegate("#pageConversas", "pageinit");
    	$( document ).delegate("#pageConversas", "pageinit", function() {
			{$script}
    	});
    </script>
EOT;
