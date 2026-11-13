<?php
namespace hardness;

include('conf.php');

include('inc.conf.php');

//Parametro de Pesquisa e Coluna da Pesquisa selecionada em requisições
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/servicos';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'T147_Id DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
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
$comboPesquisa .= '<option value="D116_Modelo"'.(($pesquisa['colunaBusca'] == 'D116_Modelo') ? ' selected="selected"' : '').'> Veículo </option>';
$comboPesquisa .= '<option value="T005_Data_Emissao"'.(($pesquisa['colunaBusca'] == 'T005_Data_Emissao') ? ' selected="selected"' : '').'> Data </option>';
$comboPesquisa .= '<option value="T005_Id"'.(($pesquisa['colunaBusca'] == 'T005_Id') ? ' selected="selected"' : '').'> Pedido </option>';
$comboPesquisa .= '<option value="T147_Titulo"'.(($pesquisa['colunaBusca'] == 'T147_Titulo') ? ' selected="selected"' : '').'> Serviço </option>';
$comboPesquisa .= '<option value="D024_Nome_Empresa"'.(($pesquisa['colunaBusca'] == 'D024_Nome_Empresa') ? ' selected="selected"' : '').'> Cliente </option>';
$comboPesquisa .= '<option value="C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'C007_Primeiro_Nome') ? ' selected="selected"' : '').'> Vendedor </option>';


$script="";

include('inc.header.php');

echo <<<EOT
	<div data-role="page" id="pageRequisicoes">
EOT;

$etapa = 3;
include('inc.header.bar.php');


echo <<<EOT
        	<div data-role="content">                
                <div class="ui-grid-a" style="margin-top: -10px; margin-bottom: 5px; ">
	                <div class="ui-block-a" style="width: 20%;">
	                </div>
		            <div class="ui-block-b" style="width: 60%;">     		
							<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
	                    		<input placeholder="Procurar Serviço" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarServico">
	                    	</div>    
	                    	<div style="float:left;">
	                    		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
							     	{$comboPesquisa}
							   </select>
						    </div>		           	
	                    	<div style="float:left;">
						        <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './servicos.php', {transition: 'fade', type:'post', data: 'colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarServico').val())} )" >
				            		Procurar
						        </a>
						    </div>		
		            </div>
        	    	<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px; clear:both;">
EOT;

// Servicos
$sql = <<<EOT
	SELECT T147_Id,
	T005_Data_Emissao, 
	concat(T147_Titulo,' ',T147_Descricao) T147_Titulo,
	D024_Nome_Empresa, 
	C007_Primeiro_Nome,
	T005_Hora_Inicio_Servico, 
	T005_Hora_Fim_Servico,
	T147_Hora_Inicio,
	T147_Hora_Fim,
	T147_Observacao,
	T147_Status,
	D116_Placa,
	D116_Modelo,
	D116_Cor,
	T005_Flag_Aguardando_Entrega,
	T005_Id
	FROM T005 
	LEFT JOIN T147 on T147_T005_Id=T005_Id 
	LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
	LEFT JOIN D024 on D024_Id=T005_D024_Id
	LEFT JOIN D116 on D116_Id=T005_D116_Id
	WHERE (
	(1=1)
EOT;
	if($C007['C007_Flag_Vendedor'] == 'XXX'){
		$sql .= " AND T005_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}'";
}
	//Verifica Se foi requisitada a pesquisa
	if(isset($pesquisa['busca']) && $pesquisa['busca']!=''){
		//Se a Coluna foi Selecionada faz pesquisa pela coluna, senão faz pesquisa geral
		if(isset($pesquisa['colunaBusca']) && $pesquisa['colunaBusca']!=''){
			//Se a Coluna Escolhida = Data
			if($pesquisa['colunaBusca']=='T005_Data_Emissao'){
				$sql .= "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
			}else if($pesquisa['colunaBusca']=='D116_Modelo'){
				$sql .= " AND ({$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%' OR (D116_Placa LIKE '%{$pesquisa['busca']}%' OR REPLACE(REPLACE(D116_Placa, '-', ''), ' ', '') LIKE REPLACE(REPLACE('%{$pesquisa['busca']}%', '-', ''), ' ', '')))";
			}else{
				$sql .= " AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
			}	
		}else{
			$sql .= " AND (date_format(T005_Data_Emissao,'%d/%m/%Y') LIKE '%{$pesquisa['busca']}%' OR T147_Titulo LIKE '%{$pesquisa['busca']}%' OR C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%' OR D024_Nome_Empresa LIKE '%{$pesquisa['busca']}%' OR D116_Modelo LIKE '%{$pesquisa['busca']}%'  OR (REPLACE(REPLACE(D116_Placa, '-', ''), ' ', '') LIKE REPLACE(REPLACE('%{$pesquisa['busca']}%', '-', ''), ' ', '')))";		
		}
	}
$sql .= <<<EOT
	)
	GROUP BY T005_Id
	ORDER BY {$pesquisa['ordem']}
	LIMIT 100
EOT;

$res = mysql_query($sql);

echo mysql_error();

//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
$T005_Data_Emissao = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Data_Emissao' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$T005_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T005_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$D116_Modelo = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D116_Modelo' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$D024_Nome_Empresa = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Nome_Empresa' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$C007_Primeiro_Nome = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'C007_Primeiro_Nome' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$T147_Hora_Inicio = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T147_Hora_Inicio' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$T147_Hora_Fim = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T147_Hora_Fim' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

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
$T005_Data_Emissao_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Data_Emissao') ? $iconeOrdenacao : '';
$T005_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T005_Id') ? $iconeOrdenacao : '';
$D116_Modelo_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D116_Modelo') ? $iconeOrdenacao : '';
$D024_Nome_Empresa_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Nome_Empresa') ? $iconeOrdenacao : '';
$C007_Primeiro_Nome_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'C007_Primeiro_Nome') ? $iconeOrdenacao : '';
$T147_Hora_Inicio_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T147_Hora_Inicio') ? $iconeOrdenacao : '';
$T147_Hora_Fim_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T147_Hora_Fim') ? $iconeOrdenacao : '';

if ($C007['C007_Flag_Vendedor'] == 'D') {
	echo <<<EOT
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 7%"><a href="servicos.php?coluna=T005_Data_Emissao&tipoOrdenacao={$T005_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T005_Data_Emissao_Icone}</a></td>
	    						<td style="width: 7%" align=center><a href="servicos.php?coluna=T005_Id&tipoOrdenacao={$T005_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Pedido{$T005_Id_Icone}</a></td>
	    						<td style="width: 30%"><a href="servicos.php?coluna=D116_Modelo&tipoOrdenacao={$D116_Modelo}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Veículo{$D116_Modelo_Icone}</a></td>
	    						<td style="width: 36%"><a href="servicos.php?coluna=D024_Nome_Empresa&tipoOrdenacao={$D024_Nome_Empresa}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Empresa_Icone}</a></td>
	    						<td style="width: 9%"><a href="servicos.php?coluna=C007_Primeiro_Nome&tipoOrdenacao={$C007_Primeiro_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Vendedor{$C007_Primeiro_Nome_Icone}</a></td>
	    						<td style="width: 6%" align=center><a href="servicos.php?coluna=T147_Hora_Inicio&tipoOrdenacao={$T147_Hora_Inicio}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Inicio{$T147_Hora_Inicio_Icone}</a></td>
	    						<td style="width: 6%" align=center><a href="servicos.php?coluna=T147_Hora_Fim&tipoOrdenacao={$T147_Hora_Fim}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Fim{$T147_Hora_Fim_Icone}</a></td>
	    					</tr>
	    				</table>
                    </li>
EOT;
}
else
{
	echo <<<EOT
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 7%"><a href="servicos.php?coluna=T005_Data_Emissao&tipoOrdenacao={$T005_Data_Emissao}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T005_Data_Emissao_Icone}</a></td>
	    						<td style="width: 7%" align=center><a href="servicos.php?coluna=T005_Id&tipoOrdenacao={$T005_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Pedido{$T005_Id_Icone}</a></td>
	    						<td style="width: 30%"><a href="servicos.php?coluna=D116_Modelo&tipoOrdenacao={$D116_Modelo}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Veículo{$D116_Modelo_Icone}</a></td>
	    						<td style="width: 36%"><a href="servicos.php?coluna=D024_Nome_Empresa&tipoOrdenacao={$D024_Nome_Empresa}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Empresa_Icone}</a></td>
	    						<td style="width: 9%"><a href="servicos.php?coluna=C007_Primeiro_Nome&tipoOrdenacao={$C007_Primeiro_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Vendedor{$C007_Primeiro_Nome_Icone}</a></td>
	    						<td style="width: 6%" align=center><a href="servicos.php?coluna=T147_Hora_Inicio&tipoOrdenacao={$T147_Hora_Inicio}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Inicio{$T147_Hora_Inicio_Icone}</a></td>
	    						<td style="width: 6%" align=center><a href="servicos.php?coluna=T147_Hora_Fim&tipoOrdenacao={$T147_Hora_Fim}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Fim{$T147_Hora_Fim_Icone}</a></td>
	    					</tr>
	    				</table>
                    </li>
EOT;
}
	
while ($row = mysql_fetch_assoc($res)) {
	
	$row['T005_Data_Emissao'] = gCorrigeData($row['T005_Data_Emissao']);

	//Formata Campo DateTime Para Horas:Minutos
    if (!empty($row['T147_Hora_Inicio']))
    { 
		//Hora Inicio
		$T147_Hora_Inicio = explode(' ', $row['T147_Hora_Inicio']);
		$T147_Hora_Inicio = $T147_Hora_Inicio[1];
		$T147_Hora_Inicio = gCorrigeTempo($T147_Hora_Inicio);
	} 
	   
    if (!empty($row['T147_Hora_Fim']))
    { 
		//Hora Fim
		$T147_Hora_Fim = explode(' ', $row['T147_Hora_Fim']);
		$T147_Hora_Fim = $T147_Hora_Fim[1];
		$T147_Hora_Fim = gCorrigeTempo($T147_Hora_Fim); 
	}
		
    // Cliente
    $veiculo=$row['D116_Modelo'].'-'.$row['D116_Cor'].'-'.$row['D116_Placa'].'<br />';
    $row['D024_Nome_Empresa']=substr($row['D024_Nome_Empresa'],0,60);

	$cor='#000000';
    $cor_aguardando = 'orange';
	if ($row['T147_Status']=='C')
	{
		$cor='#C0C0C0';
		$cor_aguardando=$cor;
	}

    if ($row[T005_Flag_Aguardando_Entrega]=='S')
    {                                                                                                                                      
        $row['D024_Nome_Empresa'].=" <font style='font-weight:normal;font-size:12px;color:$cor_aguardando''><i>Na loja</i></font>";
	}
	
	
	if ($C007['C007_Flag_Vendedor'] == 'D') {
	// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
				<a href="./servicoEditar.php?T147_Id={$row['T147_Id']}" data-ajax="false" data-transition="slide">
				    <table style="width: 100%";>
	    				<tr style='color:$cor'>
	    					<td style="width: 7%;white-space:normal" valign=top>{$row['T005_Data_Emissao']}</td>
	    					<td style="width: 7%;white-space:normal" valign=top align=center>{$row['T005_Id']}</td>
	    					<td style="width: 30%;white-space:normal" valign=top>{$veiculo}</td>
	    					<td style="width: 36%;white-space:normal" valign=top>{$row['D024_Nome_Empresa']}</td>
	    					<td style="width: 9%;white-space:normal" valign=top>{$row['C007_Primeiro_Nome']}</td>
	    					<td style="width: 5%;white-space:normal" align=center valign=top>{$row['T005_Hora_Inicio_Servico']}</td>
	    					<td style="width: 5%;white-space:normal" align=center valign=top>{$row['T005_Hora_Fim_Servico']}</td>
	    				</tr>
				    </table>
EOT;
	} else {
		echo <<<EOT
			<li data-theme="c" data-icon="false">
				<a href="./servicoEditar.php?T147_Id={$row['T147_Id']}" data-ajax="false" data-transition="slide">
				    <table style="width: 100%";>
	    				<tr style='color:$cor'>
	    					<td style="width: 7%;white-space:normal" valign=top>{$row['T005_Data_Emissao']}</td>
	    					<td style="width: 7%;white-space:normal" valign=top align=center>{$row['T005_Id']}</td>
	    					<td style="width: 30%;white-space:normal" valign=top>{$veiculo}</td>
	    					<td style="width: 36%;white-space:normal" valign=top>{$row['D024_Nome_Empresa']}</td>
	    					<td style="width: 9%;white-space:normal" valign=top>{$row['C007_Primeiro_Nome']}</td>
	    					<td style="width: 5%;white-space:normal" align=center valign=top>{$row['T005_Hora_Inicio_Servico']}</td>
	    					<td style="width: 5%;white-space:normal" align=center valign=top>{$row['T005_Hora_Fim_Servico']}</td>
	    				</tr>
	    		    </table>
EOT;
   }

	// Servicos
	$sql = <<<EOT
		SELECT T147_Id,
		T005_Data_Emissao, 
		concat(T147_Titulo,' ',T147_Descricao) T147_Titulo,
		D024_Nome_Empresa, 
		C007_Primeiro_Nome,
		T005_Hora_Inicio_Servico, 
		T005_Hora_Fim_Servico,
		T147_Hora_Inicio,
		T147_Hora_Fim,
		T147_Observacao,
		T147_Status,
		D116_Placa,
		D116_Modelo,
		D116_Cor,
		T005_Flag_Aguardando_Entrega,
		T005_Id
		FROM T147 
		LEFT JOIN T005 on T005_Id=T147_T005_Id 
		LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
		LEFT JOIN D024 on D024_Id=T005_D024_Id
		LEFT JOIN D116 on D116_Id=T005_D116_Id
		WHERE T005_Id='{$row['T005_Id']}' 
EOT;

	$res_servico = mysql_query($sql);

	echo mysql_error();

		// Servicos
	$sql = <<<EOT
		SELECT T006_Id,
		T005_Data_Emissao, 
		D024_Nome_Empresa, 
		C007_Primeiro_Nome,
		T006_Descricao_Produto, 
		T005_Id
		FROM T006 
		LEFT JOIN T005 on T005_Id=T006_T005_Id 
		LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
		LEFT JOIN D024 on D024_Id=T005_D024_Id
		WHERE T005_Id='{$row['T005_Id']}' 
EOT;

	$res_produtos = mysql_query($sql);

	echo mysql_error();

	echo <<<EOT
      <table width="99%">
        <tr>
          <td width="50%" valign=top>
	      	<table style="width: 100%" valign=top>
				<tr style='color:$cor'>
					<td valign=top>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><i>Serviços</i></u></td>
					</tr>
EOT;
	while ($row_servico = mysql_fetch_assoc($res_servico)) 
	{
		    if (!empty($row_servico['T147_Hora_Inicio']))
		    { 
				//Hora Inicio
				$T147_Hora_Inicio = explode(' ', $row_servico['T147_Hora_Inicio']);
				$T147_Hora_Inicio = $T147_Hora_Inicio[1];
				$T147_Hora_Inicio = gCorrigeTempo($T147_Hora_Inicio);
			} 
			   
		    if (!empty($row_servico['T147_Hora_Fim']))
		    { 
				//Hora Fim
				$T147_Hora_Fim = explode(' ', $row_servico['T147_Hora_Fim']);
				$T147_Hora_Fim = $T147_Hora_Fim[1];
				$T147_Hora_Fim = gCorrigeTempo($T147_Hora_Fim); 
			}

		    if ($row_servico['T147_Status']=='C')
		    {
				$row_servico['T147_Titulo'].=" <font style='font-weight:bold;color:green'>CONCLUÍDO {$T147_Hora_Inicio} {$T147_Hora_Fim}</font></font>";
		    }
		    if ($row_servico['T147_Status']=='XXX')
		    {
				$row_servico['T147_Titulo'].=" <font style='color:red;font-weight:bold'>PENDENTE</font></font>";
		    }
		    if ($row_servico['T147_Status']=='E')
		    {
				$row_servico['T147_Titulo'].=" <font style='color:blue;font-weight:bold'>EXECUTANDO</font></font>";
		    }
		    
			$row_servico['T147_Titulo'].="<br /><font style='color:blue;font-weight:normal'><i>{$row_servico['T147_Observacao']}</i></font>";
    		echo <<<EOT
				<tr style='color:$cor'>
					<td style="width: 38%;white-space:normal;font-weight:normal" valign=top><ul><li type=disc>{$row_servico['T147_Titulo']}</ul></td>
				</tr>
EOT;

	}

		echo <<<EOT
   		    </table>
   		  </td>
   		  <td width="50%" valign=top>
	      	<table style="width: 100%" valign=top>
				<tr style='color:$cor'>
					<td valign=top>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><i>Produtos</i></u></td>
				</tr>
EOT;
	while ($row_produtos = mysql_fetch_assoc($res_produtos)) 
	{
    		echo <<<EOT
				<tr style='color:$cor'>
					<td style="width: 38%;white-space:normal;font-weight:normal" valign=top><ul><li type=disc>{$row_produtos['T006_Descricao_Produto']}</ul></td>
				</tr>
EOT;

	}

	echo <<<EOT
   		    </table>
   		  </td>
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
