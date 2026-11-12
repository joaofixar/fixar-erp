<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
//Parametros de Pesquisa e Coluna da Pesquisa selecionada em requisições
$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/inventario';
if (!file_exists($arqFiltro)) {
	$pesquisa = array('busca' => '', 'ordem' => 'D001_Descricao_Produto', 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
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

//Vars de Edição no Grid
$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : '';
$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : '';
$T001_Quantidade = isset($_REQUEST['T001_Quantidade']) ? $_REQUEST['T001_Quantidade'] : '';

//Editar Dados das Colunas
if($T066_Id != ''){
	require_once("bibliotecas/classes/CAD002.php");
    $CAD002 = new CAD002();

	$T001_Quantidade = gCorrigeNumeroInverte($T001_Quantidade);

	$sqlD009 = mysql_query("SELECT D004_Id, D001_Codigo_Produto FROM D009
			LEFT JOIN D049 on D009_D049_Id=D049_Id
			LEFT JOIN D001 on D049_D001_Id=D001_Id
			LEFT JOIN T066 on T066_Id='{$T066_Id}'
			LEFT JOIN D004 on D004_Id=T066_D004_Id
			WHERE D009_Id = '{$D009_Id}'");
	$mD009 = mysql_fetch_array($sqlD009);

    $g['C007']['C007_Primeiro_Nome'] = substr($g['C007']['C007_Primeiro_Nome'], 0,10);
    $sql = <<<EOT
		INSERT INTO T001 (
			T001_D024_Id_Cliente,
			T001_D024_Id,
			T001_D009_Id,
			T001_C004_Id,
			T001_Codigo_Produto,
			T001_Data_Lancamento,
			T001_Descricao_Operacao,
			T001_Quantidade,
			T001_Flag_Complemento,
			T001_Flag_Operacao,
			T001_Valor_Preco_Unitario,
			T001_Observacao,
			T001_Valor_Custo_Unitario,
			T001_Flag_Atualiza_Custo,
			T001_Quantidade_Saldo,
			T001_T066_Id,
			T001_D004_Id
		) VALUES (
			'0',
			'0',
			'{$D009_Id}',
			'{$g['empresaAtual']}',
			'{$mD009['D001_Codigo_Produto']}',
			current_date(),
			'BL-TABLET ({$g['C007']['C007_Primeiro_Nome']})',
			'{$T001_Quantidade}',
			'',
			'B',
			'0.00000',
			'',
			'0.00000',
			'N',
			0,
			'{$T066_Id}',
			'{$mD009['D004_Id']}'
		)
EOT;
    mysql_query($sql);
    $erro = mysql_error();
    if (!empty($erro)) {
        die("Erro: " . $erro);
    }

    $CAD002->D001_reprocessa_historico(false, $D009_Id);
	$sql = <<<EOT
		UPDATE
			T066
		SET
			T066_Quantidade_Balanco = '0.00',
			T066_Data_Balanco = CURDATE(),
			T066_Frequencia_Apos_Balanco = 0,
			T066_Flag_Balanco = 'N',
			T066_C007_Id_Marcou_Balanco=''
		WHERE T066_Id = '{$T066_Id}'
EOT;
	mysql_query($sql);
	$erro = mysql_error();
	if (!empty($erro)) {
	    die("Erro: " . $erro);
	}
	$script = "alert('Balanço realizado com sucesso!');";
}

//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="D001_Descricao_Produto"'.(($pesquisa['colunaBusca'] == 'D001_Descricao_Produto') ? ' selected="selected"' : '').'> Descrição </option>';
$comboPesquisa .= '<option value="D082_Marca"'.(($pesquisa['colunaBusca'] == 'D082_Marca') ? ' selected="selected"' : '').'> Marca </option>';
$comboPesquisa .= '<option value="D001_Codigo_Produto"'.(($pesquisa['colunaBusca'] == 'D001_Codigo_Produto') ? ' selected="selected"' : '').'> Código </option>';

$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : false;

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageInventario">
EOT;

$etapa = 8;
include('inc.header.bar.php');
echo <<<EOT
    <div data-role="content">
    	<div class="ui-grid-a" style="margin-top: -10px; margin-bottom: 5px; ">
        <div class="ui-block-a" style="width: 20%;">
        </div>
	    <div class="ui-block-b" style="width: 60%;">
				<div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
            		<input placeholder="Procurar Produto" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarProduto">
            	</div>
            	<div style="float:left;">
            		<select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
				     	{$comboPesquisa}
				   </select>
			    </div>
            	<div style="float:left;">
			        <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './inventario.php', {transition: 'fade', type:'post', data: 'colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarProduto').val())} )" >
	            		Procurar
			        </a>
			    </div>
	    </div>
        <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px; clear:both;">
EOT;
//Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
$D001_Codigo_Produto = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D001_Codigo_Produto' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$D001_Descricao_Produto = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D001_Descricao_Produto' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$D082_Marca = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D082_Marca' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$D009_Quantidade_Estoque_Liquido = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D009_Quantidade_Estoque_Liquido' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
$D009_Quantidade_Estoque = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D009_Quantidade_Estoque' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

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
$D001_Codigo_Produto_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D001_Codigo_Produto') ? $iconeOrdenacao : '';
$D001_Descricao_Produto_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D001_Descricao_Produto') ? $iconeOrdenacao : '';
$D082_Marca_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D082_Marca') ? $iconeOrdenacao : '';
$D009_Quantidade_Estoque_Liquido_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D009_Quantidade_Estoque_Liquido') ? $iconeOrdenacao : '';
$D009_Quantidade_Estoque_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D009_Quantidade_Estoque') ? $iconeOrdenacao : '';
$D041_Valor_Base_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D041_Valor_Base') ? $iconeOrdenacao : '';

echo <<<EOT
            <li data-role="list-divider" role="heading">
	    		<table style="width: 100%";>
	    			<tr>
	    				<td style="width: 10%";><a href="inventario.php?coluna=D001_Codigo_Produto&tipoOrdenacao={$D001_Codigo_Produto}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Código{$D001_Codigo_Produto_Icone}</a></td>
	    				<td style="width: 45%";><a href="inventario.php?coluna=D001_Descricao_Produto&tipoOrdenacao={$D001_Descricao_Produto}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Descrição{$D001_Descricao_Produto_Icone}</a></td>
	    				<td style="width: 8%";><a href="inventario.php?coluna=D082_Marca&tipoOrdenacao={$D082_Marca}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Marca{$D082_Marca_Icone}</a></td>
	    				<td style="width: 8%"; align="right"><a href="inventario.php?coluna=D009_Quantidade_Estoque_Liquido&tipoOrdenacao={$D009_Quantidade_Estoque_Liquido}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Físico{$D009_Quantidade_Estoque_Liquido_Icone}</a></td>
	    				<td style="width: 8%"; align="right"><a href="inventario.php?coluna=D009_Quantidade_Estoque&tipoOrdenacao={$D009_Quantidade_Estoque}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Líquido{$D009_Quantidade_Estoque_Liquido_Icone}</a></td>
	    				<td style="width: 12%"; align="right">Quantidade</td>
	    				<td style="width: 8%"; align="right">&nbsp;</td>
	    			</tr>
	    		</table>
            </li>
EOT;

// Produtos
$pesquisa_ = explode(' ', $pesquisa['busca']);
if (count($pesquisa_) == 0) {
	$extra = <<<EOT
		(
		(D009_Id LIKE '%{$pesquisa['busca']}%') OR
		(D001_Codigo_Produto LIKE '%{$pesquisa['busca']}%') OR
		(D001_Descricao_Produto LIKE '%{$pesquisa['busca']}%') OR
		(D082_Marca LIKE '%{$pesquisa['busca']}%') OR
		(D009_Quantidade_Estoque_Liquido LIKE '%{$pesquisa['busca']}%') OR
		(D001_Descricao_Ingles LIKE '%{$pesquisa['busca']}%')
		)
EOT;
} else {
	$tmp = '(';
	foreach ($pesquisa_ as $word) {
		if ($tmp == '(') {
			$tmp .= "[%] LIKE '%{$word}%'";
		} else {
			$tmp .= " AND [%] LIKE '%{$word}%'";
		}
	}
	$tmp .= ')';
	$extra = '(';
	$extra .= str_replace('[%]', 'D009_Id', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D001_Codigo_Produto', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D001_Descricao_Produto', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D082_Marca', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D009_Quantidade_Estoque_Liquido', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D001_Descricao_Ingles', $tmp);
	$extra .= ')';
}

$sql = <<<EOT
	SELECT D001_Id, D009_Id, T066_Id, D001_Flag_Pre_Cadastro, D001_Codigo_Produto, D001_Descricao_Produto, D082_Marca, D009_Quantidade_Estoque, D009_Quantidade_Estoque_Liquido, D001_Descricao_Ingles, T066_Quantidade_Balanco
	FROM D001
	left join D049 on D049_D001_Id=D001_Id
	left join D009 on D009_D049_Id=D049_Id
	left join D082 on D082_Id=D049_D082_Id
	left join D037 on D037_Id=D001_D037_Id
	left join T066 on T066_D009_Id=D009_Id
	WHERE {$extra}
EOT;
	//Verifica Se foi requisitada a pesquisa
	if(isset($pesquisa['busca']) && $pesquisa['busca']!=''){
		//Se a Coluna foi Selecionada faz pesquisa pela coluna, senão fz pesquisa geral
		if(isset($pesquisa['colunaBusca']) && $pesquisa['colunaBusca']!=''){
			$sql .= " AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
		}
	}
$sql .= <<<EOT
	AND (D009_C004_Id = '{$g['empresaAtual']}' and D001_Flag_Ativo='S')
	GROUP BY T066_Id
	ORDER BY {$pesquisa['ordem']} LIMIT 0,100
EOT;
$res = mysql_query($sql);
if(mysql_num_rows($res) == '0'){
	echo "<span style='margin-left:40%; font-weight:bold; font-size:20px;'>Produto não encontrado</span>";
}
while ($row = mysql_fetch_assoc($res)) {
	$row['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
	$row['D009_Quantidade_Estoque'] = gCorrigeNumero($row['D009_Quantidade_Estoque']);
	$row['D001_Descricao_Produto'] = ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['D001_Descricao_Produto']} (PRÉ)" : $row['D001_Descricao_Produto']);
	echo <<<EOT
		<li data-theme="c" data-icon="false" style="padding:0px 0px 0px 0px">
			<table style="width: 100%;padding:0px 15px 0px 15px"; border=0px cellspacing=0px >
				<tr>
					<td style="width: 10%";>{$row['D001_Codigo_Produto']}</td>
					<td style="width: 45%;white-space:normal";>{$row['D001_Descricao_Produto']}</td>
					<td style="width: 8%";>{$row['D082_Marca']}</td>
					<td style="width: 8%; padding-right:20px;"; align="right">{$row['D009_Quantidade_Estoque']}</td>
					<td style="width: 8%; padding-right:20px;"; align="right">{$row['D009_Quantidade_Estoque_Liquido']}</td>
					<td style="width: 12%"; align="right"><input name="T001_Quantidade" data-inline="true" id="T001_Quantidade_{$row['T066_Id']}" placeholder="" value=""  type="text"  style="width:60%; float:right; text-align:right"/></td>
					<td style="width: 8%"; align="right"><a onclick="\$.mobile.changePage('./inventario.php', {transition: 'fade', type:'post', data: 'T001_Quantidade=' + encodeURIComponent($('#T001_Quantidade_{$row['T066_Id']}').val()) + '&T066_Id={$row['T066_Id']}&D009_Id={$row['D009_Id']}'})" ><input id="preco" type="button" data-inline="true" data-role="button" value=">>" style="width:15%; float:left;"></a></td>
				</tr>
			</table>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
        </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageInventario", "pageinit");
    	$( document ).delegate("#pageInventario", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
