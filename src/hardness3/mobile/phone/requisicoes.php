<?php
namespace hardness;

include('conf.php');

include('inc.conf.php');

$uniqId=uniqid();

//Parametro de Pesquisa e Coluna da Pesquisa selecionada em requisições
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/requisicoes';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'T189_Data_Criado DESC', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
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


$script="";

include('inc.header.php');

echo <<<EOT
	<div data-role="page" id="pageRequisicoes">
EOT;

$etapa = 2;
include('inc.header.bar.php');



//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="T189_Data_Criado"'.(($pesquisa['colunaBusca'] == 'T189_Data_Criado') ? ' selected="selected"' : '').'> Data </option>';
$comboPesquisa .= '<option value="T189_Descricao_Produto"'.(($pesquisa['colunaBusca'] == 'T189_Descricao_Produto') ? ' selected="selected"' : '').'> Descrição </option>';
$comboPesquisa .= '<option value="C007_Primeiro_Nome"'.(($pesquisa['colunaBusca'] == 'C007_Primeiro_Nome') ? ' selected="selected"' : '').'> Vendedor </option>';

// Remover requisição
if (isset($_REQUEST['T189_Id'])) {
	$T189_Id = $_REQUEST['T189_Id'];
	$sql = "DELETE FROM T189 WHERE T189_Id = '{$T189_Id}';";
	mysql_query($sql);
	/*$uniqId=uniqid();
	$script .= <<<EOT
	$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );
EOT;*/
}

echo <<<EOT
      	<div data-role="content">
        	<div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px; ">
                <div class="ui-block-a" style="width: 20%;">
                    <div>
                    	<a data-role="button" data-inline="true" data-transition="fade" href="./requisicoesCompraProduto.php" data-ajax="false">
								Requisitar Compra
		                </a>
				    </div>
                </div>
	            <div class="ui-block-b" style="width: 60%;">     		
						<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    		<input placeholder="Procurar Requisição" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarOrcamentoLista">
                    	</div>    
                    	 <div style="float:left;">
                    		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
						     	{$comboPesquisa}
						   </select>
					    </div>		           	
                    	<div style="float:left;">
					        <a data-role="button" data-transition="slide" data-inline="true" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './requisicoes.php', {transition: 'fade', type:'post', data: 'colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarOrcamentoLista').val())} )" >
			            		Procurar
					        </a>
					    </div>		
	            </div>
        	
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px; clear:both;">
EOT;

		//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
		$T189_Data_Criado = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T189_Data_Criado' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T189_Descricao_Produto = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T189_Descricao_Produto' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T189_Quantidade = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T189_Quantidade' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$T189_Flag_Tipo = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'T189_Flag_Tipo' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
		$C007_Nome = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'C007_Nome' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

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
		$T189_Data_Criado_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T189_Data_Criado') ? $iconeOrdenacao : '';
		$T189_Descricao_Produto_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T189_Descricao_Produto') ? $iconeOrdenacao : '';
		$T189_Quantidade_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T189_Quantidade') ? $iconeOrdenacao : '';
		$T189_Flag_Tipo_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'T189_Flag_Tipo') ? $iconeOrdenacao : '';
		$C007_Nome_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'C007_Nome') ? $iconeOrdenacao : '';
		
if ($C007['C007_Flag_Vendedor'] == 'D') {
	echo <<<EOT
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 8%"><a href="requisicoes.php?coluna=T189_Data_Criado&tipoOrdenacao={$T189_Data_Criado}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T189_Data_Criado_Icone}</a></td>
	    						<td style="width: 67%"><a href="requisicoes.php?coluna=T189_Descricao_Produto&tipoOrdenacao={$T189_Descricao_Produto}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Descrição Produto{$T189_Descricao_Produto_Icone}</a></td>
	    						<td style="width: 7%;padding:0px 12px 0px 0px;" align="right"><a href="requisicoes.php?coluna=T189_Quantidade&tipoOrdenacao={$T189_Quantidade}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Qnt{$T189_Quantidade_Icone}</a></td>
	    						<td style="width: 10%"><a href="requisicoes.php?coluna=T189_Flag_Tipo&tipoOrdenacao={$T189_Flag_Tipo}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Tipo{$T189_Flag_Tipo_Icone}</a></td>
	    						<td style="width: 10%"><a href="requisicoes.php?coluna=C007_Nome&tipoOrdenacao={$C007_Nome}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Usuário{$C007_Nome_Icone}</a></td>
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
	    						<td style="width: 8%"><a href="requisicoes.php?coluna=T189_Data_Criado&tipoOrdenacao={$T189_Data_Criado}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Data{$T189_Data_Criado_Icone}</a></td>
	    						<td style="width: 77%"><a href="requisicoes.php?coluna=T189_Descricao_Produto&tipoOrdenacao={$T189_Descricao_Produto}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Descrição Produto{$T189_Descricao_Produto_Icone}</a></td>
	    						<td style="width: 7%;padding:0px 12px 0px 0px;" align="right"><a href="requisicoes.php?coluna=T189_Quantidade&tipoOrdenacao={$T189_Quantidade}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Qnt{$T189_Quantidade_Icone}</a></td>
	    						<td style="width: 10%"><a href="requisicoes.php?coluna=T189_Flag_Tipo&tipoOrdenacao={$T189_Flag_Tipo}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Tipo{$T189_Flag_Tipo_Icone}</a></td>
	    					</tr>
	    				</table>
                    </li>
EOT;
}

// Requisições
$sql = <<<EOT
	SELECT T189_Id, 
	T189_Data_Criado, 
	T189_Descricao_Produto, 
	T189_Observacao, 
	T189_C007_Id, 
	C007_Nome,
	T189_Quantidade,
	T189_Detalhes, 
	T189_T004_Id, 
	T189_T006_Id, 
	T189_D009_Id, 
	T189_Flag_Tipo 
	FROM T189 
	LEFT JOIN C007 ON C007_Id=T189_C007_Id 
	WHERE (
	(1=1)
EOT;
	if($C007['C007_Flag_Vendedor'] != 'D'){
		$sql .= " AND T189_C007_Id='{$g['usuarioAtual']}'";
}
	//Verifica Se foi requisitada a pesquisa
	if(isset($pesquisa['busca']) && $pesquisa['busca']!=''){
		//Se a Coluna foi Selecionada faz pesquisa pela coluna, senão fz pesquisa geral
		if(isset($pesquisa['colunaBusca']) && $pesquisa['colunaBusca']!=''){
			//Se a Coluna Escolhida = Data
			if($pesquisa['colunaBusca']=='T189_Data_Criado'){
				$sql .= "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
			}else{
				$sql .= " AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
			}	
		}else{
			$sql .= " AND (T189_Data_Criado LIKE '%{$pesquisa['busca']}%' OR T189_Descricao_Produto LIKE '%{$pesquisa['busca']}%' OR C007_Primeiro_Nome LIKE '%{$pesquisa['busca']}%' OR T189_Flag_Tipo LIKE '%{$pesquisa['busca']}%')";		
		}
	}
$sql .= <<<EOT
	)
	ORDER BY {$pesquisa['ordem']}
	LIMIT 100
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$row['T189_Data_Criado'] = gCorrigeData($row['T189_Data_Criado']);
	
	if($row['T189_Flag_Tipo'] == 'C'){
		$row['T189_Flag_Tipo'] = "Cotar";
	} else if($row['T189_Flag_Tipo'] == 'O'){
		$row['T189_Flag_Tipo'] = "Orçamento";
	} else if($row['T189_Flag_Tipo'] == 'P'){
		$row['T189_Flag_Tipo'] = "Pedido";
	} else if($row['T189_Flag_Tipo'] == 'E'){
		$row['T189_Flag_Tipo'] = "Reposição";
	} else if($row['T189_Flag_Tipo'] == 'R'){
		$row['T189_Flag_Tipo'] = "Cancelado";
	}
	$row['T189_Quantidade'] = number_format($row['T189_Quantidade'], 2, ',', ' ');
	
//Verificação Se Item já foi cotado
$sqlT059 = <<<EOT
	SELECT T174_Id,
	       D024_Nome_Empresa,
	       T174_Valor_Custo_Unitario,
	       T174_IPV,
	       T174_Flag_Indisponivel,
	       T174_Prazo_Entrega_Dias,
	       T174_Preco_Venda,
	       T174_Observacao,
	       T189_Descricao_Produto,
	       T059_Descricao_Produto,
	       T011_Id,
	       T011_Prazo_Entrega
	  FROM T174
 LEFT JOIN T173 ON T173_Id=T174_T173_Id
 LEFT JOIN D024 ON D024_Id=T173_D024_Id 
 LEFT JOIN T010 ON T010_Id=T173_T010_Id 
 LEFT JOIN T059 ON T059_Id=T174_T059_Id
 LEFT JOIN T189 ON T189_Id=T059_T189_Id
 LEFT JOIN T012 ON T012_T174_Id=T174_Id or T012_T189_Id=T189_Id 
 LEFT JOIN T011 ON T011_Id=T012_T011_Id
	 WHERE T059_T189_Id='{$row['T189_Id']}'
	 AND T174_Flag_Excluido='N'
EOT;
$resT059 = mysql_query($sqlT059);
echo mysql_error();
$rowsT059 = mysql_numrows($resT059);

//Verificação Se Item já foi para ordem de compra
$sqlT012 = <<<EOT
	SELECT T012_Id FROM T012 WHERE T012_T189_Id='{$row['T189_Id']}'
EOT;
$resT012 = mysql_query($sqlT012);
$rowsT012 = mysql_numrows($resT012);
$retorno='';
if($rowsT059 > 0){
	while ($mT059=mysql_fetch_array($resT059))
	{
		   $mT059['T059_Descricao_Produto'] = str_replace(trim($mT059['T189_Descricao_Produto']), '', trim($mT059['T059_Descricao_Produto']));
           $mT059['D024_Nome_Empresa']=substr($mT059['D024_Nome_Empresa'],0,10);
		   if ($mT059[T174_Flag_Indisponivel]=='S')
		   {
	           $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</font><font color='red'> INDISPONÍVEL </font>";
		   }
		   else if ($mT059[T174_Valor_Custo_Unitario]>0)
		   {
               if($C007['C007_Flag_Vendedor'] == 'D'){
         	      $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</font><font color='green'> R$ ".number_format($mT059[T174_Valor_Custo_Unitario],2,',','.')." x ".number_format($mT059[T174_IPV],2,',','.')." = R$ ".number_format($mT059[T174_Preco_Venda],2,',','.')."</font>";
			   }else{
     	          $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='#D96C00' style='font-weight:normal'><i>{$mT059['T059_Descricao_Produto']}</font><font color='green'> R$ ".number_format($mT059[T174_Preco_Venda],2,',','.')."</font>";
			   }
     	       if (!empty($mT059[T174_Prazo_Entrega_Dias]))
     	       {
     	           $retorno .= " <font color='blue' style='font-weight:normal'>Ent:</font> <font color='green'>{$mT059['T174_Prazo_Entrega_Dias']}</font>";
			   }
		   }
           else
           {
     	       $retorno .= " <br><font color='blue' style='font-weight:normal'>{$mT059['D024_Nome_Empresa']}: </font><font color='blue'> COTANDO </font>";
		   }
		   if (!empty($mT059[T174_Observacao]))
		   {
		       $retorno .= " <font color='#D96C00' style='font-weight:normal'><i>{$mT059['T174_Observacao']}</font>";
		   }
   	       if (!empty($mT059[T011_Id]))
   	       {
   	           $retorno .= " <font color='#880088' style='font-weight:bold'>OC {$mT059['T011_Id']} {$mT059['T011_Prazo_Entrega']}</font>";
    	   }                             
	}
}else if($rowsT012 > 0){
	$retorno = " - <font color='blue'>(OC)</font>";
}else{
	$retorno = "<br><font color='#585858' style='font-weight:normal'><i>Aguardando comprador...</i></font>";
}
	// Busca observacao do cabecalho da cotacao
	$sqlT010 = <<<EOT
		SELECT T010_Observacao
		  FROM T189
	 LEFT JOIN T059 ON T059_T189_Id=T189_Id
	 LEFT JOIN T010 ON T010_Id=T059_T010_Id
		 WHERE T059_T189_Id='{$row['T189_Id']}'
      GROUP BY T189_Id
EOT;
	$resT010 = mysql_query($sqlT010);
	echo mysql_error();
	$rowsT010 = mysql_fetch_array($resT010);
    if (!empty($rowsT010[T010_Observacao]))
    {
        $retorno .= "<br><font color='#D96C00' style='font-weight:normal'><i>{$rowsT010['T010_Observacao']}</font>";
    }
  
    if ($C007['C007_Flag_Vendedor'] == 'D') {
	// GRID: Diretor
		echo <<<EOT
			<li data-theme="c" data-icon="false">
				<a onclick="\$.mobile.changePage( './requisicoesCompraProduto.php', {transition: 'slide', type:'get', data: 'T189_Id={$row['T189_Id']}'})" data-ajax="false" data-transition="slide">
				    <table style="width: 100%";>
	    				<tr>
	    					<td style="width: 8%;white-space:normal"; valign=top>{$row['T189_Data_Criado']}</td>
	    					<td style="width: 67%;white-space:normal";>{$row['T189_Descricao_Produto']}{$retorno}</td>
	    					<td style="width: 7%;padding:0px 15px 0px 0px;" align="right">{$row['T189_Quantidade']}</td>
	    					<td style="width: 10%;white-space:normal";>{$row['T189_Flag_Tipo']}</td>
	    					<td style="width: 10%;white-space:normal";>{$row['C007_Nome']}</td>
	    				</tr>
	    				<tr>
	    					<td style="width: 12%;white-space:normal;font-weight:normal;font-size:11px"; colspan=5 align=right><i>{$row['T189_Detalhes']}</i></td>
	    				</tr>
				    </table>
	    		</a>
		    </li>
EOT;
	} else {
		echo <<<EOT
			<li data-theme="c" data-icon="false">
				<a onclick="\$.mobile.changePage( './requisicoesCompraProduto.php', {transition: 'slide', type:'get', data: 'T189_Id={$row['T189_Id']}'})" data-ajax="false" data-transition="slide">
				    <table style="width: 100%";>
	    				<tr>
	    					<td style="width: 8%;white-space:normal" valign=top>{$row['T189_Data_Criado']}</td>
	    					<td style="width: 77%;white-space:normal">{$row['T189_Descricao_Produto']}{$retorno}</td>
	    					<td style="width: 7%;padding:0px 15px 0px 0px;" align="right">{$row['T189_Quantidade']}</td>
	    					<td style="width: 10%;white-space:normal">{$row['T189_Flag_Tipo']}</td>
	    				</tr>
	    				<tr>
	    					<td style="width: 12%;white-space:normal;font-weight:normal;font-size:11px"; colspan=4 align=right><i>{$row['T189_Detalhes']}</i></td>
	    				</tr>
				    </table>
	    		</a>
		    </li>
EOT;
   }


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
    	$( document ).undelegate("#pageRequisicoes", "pageinit");
    	$( document ).delegate("#pageRequisicoes", "pageinit", function() {
			{$script}
    	});
    </script>
EOT;
