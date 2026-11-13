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

//Vars de Edição no Grid
$T066_Id 			= isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : '';
$D009_Id 		    = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : '';
$T001_Quantidade    = isset($_REQUEST['T001_Quantidade']) ? $_REQUEST['T001_Quantidade'] : '';
$T001_Flag_Operacao = isset($_REQUEST['T001_Flag_Operacao']) ? $_REQUEST['T001_Flag_Operacao'] : '';

//Editar Dados das Colunas
if($T066_Id != ''){
	require_once("bibliotecas/classes/CAD002.php");
    $CAD002 = new CAD002();


	$T001_Quantidade = gCorrigeNumeroInverte($T001_Quantidade);
	if($T001_Flag_Operacao == 'E'){
		$descricaoOperacao = 'ENT-TABLET';
	} elseif($T001_Flag_Operacao == 'S'){
		$descricaoOperacao = 'SAI-TABLET';;
	} else{
		$descricaoOperacao = 'BL-TABLET';
	}
	
	$sqlD009 = mysql_query("SELECT D004_Id, 
								   D001_Codigo_Produto,
								   T066_Id,
								   T066_Quantidade_Estoque 
							  FROM D009
						 LEFT JOIN D049 ON D009_D049_Id=D049_Id
						 LEFT JOIN D001 ON D049_D001_Id=D001_Id
						 LEFT JOIN T066 ON T066_Id='{$T066_Id}'
						 LEFT JOIN D004 ON D004_Id=T066_D004_Id
							 WHERE D009_Id = '{$D009_Id}'");
	$mD009 = mysql_fetch_array($sqlD009);

	$estoqueNegativo = 'N';
	if($T001_Flag_Operacao == 'S' && (($mD009['T066_Quantidade_Estoque'] - $T001_Quantidade) < 0)){
		$estoqueNegativo = 'S';
	}

	if($estoqueNegativo == 'N'){
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
				'{$descricaoOperacao} ({$g['C007']['C007_Primeiro_Nome']})',
				'{$T001_Quantidade}',
				'',
				'{$T001_Flag_Operacao}',
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
	
		$T001_Id = $g['mysqlLastId'];
	
		$dados = array('T001_Id' 				 => $T001_Id, 				   
					   'T001_Quantidade'         => $T001_Quantidade,
					   'T066_Quantidade_Estoque' => $mD009['T066_Quantidade_Estoque'],
					   'T001_Flag_Operacao'      => $T001_Flag_Operacao);	
		
		$CAD002->triggerAposInsercaoT001($dados);
		$CAD002->D001_reprocessa_historico(false, $D009_Id, 50, true);
	
		$sql = <<<EOT
			UPDATE T066
			   SET T066_Quantidade_Balanco      = '0.00',
				   T066_Data_Balanco            = CURDATE(),
				   T066_Frequencia_Apos_Balanco = 0,
				   T066_Flag_Balanco            = 'N',
				   T066_C007_Id_Marcou_Balanco  = ''
			 WHERE T066_Id = '{$T066_Id}'
EOT;
		mysql_query($sql);
		$erro = mysql_error();
		if (!empty($erro)) {
			die("Erro: " . $erro);
		}
		$script = "alert('Balanço realizado com sucesso!');";
	}

	unset($_REQUEST['T001_Quantidade']);
	unset($_REQUEST['T001_Flag_Operacao']);
	unset($_REQUEST['D009_Id']);
	unset($_REQUEST['D009_Id']);
	unset($_REQUEST['T066_Id']);	
}

$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : false;

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageInventario">
EOT;

$uniqid  = uniqid();
$uniqid2 = uniqid();
$etapa   = 8;
include('inc.header.bar.php');
echo <<<EOT
    <div data-role="content">
        <div style="margin-top: -10px; margin-bottom: 5px; ">
            <div>
                <div style="width: 85%; float:left; margin-top: 8px; margin-bottom: 5px;">
                    <input placeholder="Procurar Produto" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarProduto">
                </div>
                <div style="float:left; margin-top: 4px;">
                    <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="notext" id="searchButton"></a>
                </div>
            </div>
            <ul data-role="listview" style="margin-top: 1px; clear:both;">

    <script>
		var uniqId  = '{$uniqid}';
		var uniqId2 = '{$uniqid2}';

		// Substiu os eventos por id unico para quando recarregar a pagina não dar problema
		document.getElementById('procurarProduto').id = 'procurarProduto_' + uniqId;
		document.getElementById('searchButton').id = 'searchButton_'+uniqId2;
		
        function searchProduct() {
            var pesquisa = encodeURIComponent(document.getElementById('procurarProduto_' + uniqId).value);
            window.location.href = './inventario.php?pesquisa=' + pesquisa;
        }

        document.getElementById('procurarProduto_' + uniqId).addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchProduct();
            }
        });

        document.getElementById('searchButton_' + uniqId2).addEventListener('click', function() {
            searchProduct();
        });
    </script>
EOT;

// Produtos
if($g['C031']['buscaProdutoInventiarioTablet'] == 1){

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
} else {
	$extra = "D001_Codigo_Produto = '{$pesquisa['busca']}'";
}


$sql = <<<EOT
	SELECT D001_Id, 
		   D009_Id,
		   T066_Id,
		   D001_Flag_Pre_Cadastro,
		   D001_Codigo_Produto,
		   D001_Descricao_Produto,
		   D082_Marca,
		   D009_Quantidade_Estoque,
		   D009_Quantidade_Estoque_Liquido,
		   D001_Descricao_Ingles,
		   T066_Quantidade_Balanco,
		   T066_Quantidade_Estoque,
		   T066_Quantidade_Estoque_Liquido,
		   D004_Local,
		   coalesce(D009_Quantidade_Estoque - (D009_Quantidade_Separado_3(D009_Id))) as estoqueReal
	  FROM D001
 left join D049 on D049_D001_Id=D001_Id
 left join D009 on D009_D049_Id=D049_Id
 left join D082 on D082_Id=D049_D082_Id
 left join D037 on D037_Id=D001_D037_Id
 left join T066 on T066_D009_Id=D009_Id
 left join D004 on T066_D004_Id=D004_Id
	 WHERE {$extra}
	   AND D004_Flag_Somar_Estoque_Fisico != 'N'
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
	$uniqueIdProduto = uniqid();
	
	$row['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
	$row['D009_Quantidade_Estoque'] 		= gCorrigeNumero($row['D009_Quantidade_Estoque']);
	$row['T066_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['T066_Quantidade_Estoque_Liquido']);
	$row['T066_Quantidade_Estoque'] 		= gCorrigeNumero($row['T066_Quantidade_Estoque']);
	$row['estoqueReal'] 					= gCorrigeNumero($row['estoqueReal']);
	$row['D001_Descricao_Produto']			= ($row['D001_Flag_Pre_Cadastro'] == "S" ? "{$row['D001_Descricao_Produto']} (PRÉ)" : $row['D001_Descricao_Produto']);
	
	if($estoqueNegativo == 'S' && ($T066_Id == $row['T066_Id'])){
		// Só entra neste IF se enviar uma operação de saída e o estoque for ficar negativo
		$operacao = "";
		$operacao .= "<option value=\"E\">Entrada</option>";
		$operacao .= "<option value=\"B\">Balanço</option>";
		$operacao .= "<option value=\"S\" selected>Saída</option>";

		$style = 'color: #FF0000;';

		echo <<<EOT
		<script>
			alert("Produto {$row['D001_Codigo_Produto']} - Não é permitido deixar o estoque negativo (Locação {$row['D004_Local']})")
		</script>
EOT;
	} else {
		$operacao = "";
		$operacao .= "<option value=\"E\">Entrada</option>";
		$operacao .= "<option value=\"B\">Balanço</option>";
		$operacao .= "<option value=\"S\">Saída</option>";

		$style = 'color: #000000;';
	}

	echo <<<EOT
		<li data-theme="c" data-icon="false" style="padding: 8px;">
			<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal; {$style}">{$row['D001_Codigo_Produto']}&nbsp;- &nbsp;{$row['D001_Descricao_Produto']}&nbsp;- &nbsp;{$row['D082_Marca']}</div>
			<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">Local: {$row['D004_Local']}&nbsp;- Físico: {$row['T066_Quantidade_Estoque']}&nbsp;- &nbsp;Líquido: {$row['T066_Quantidade_Estoque_Liquido']}</div>
			<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal; font-weight: bold; color: red;">Total: Físico: {$row['D009_Quantidade_Estoque']}&nbsp;- &nbsp;Líquido: {$row['D009_Quantidade_Estoque_Liquido']}&nbsp;- Estoque Real: {$row['estoqueReal']} </div>
			<div style="display: flex;width: 100%; align-items: center;">
			<select name="T001_Flag_Operacao" id="T001_Flag_Operacao_{$uniqueIdProduto}" style="flex: 1;">{$operacao}</select>
				<input name="T001_Quantidade" data-inline="true" id="T001_Quantidade_{$uniqueIdProduto}" placeholder="Quantidade" value=""  type="text"  style="flex: 1; text-align:right; margin-top: 8px;"/>
				<a onclick="\$.mobile.changePage('./inventario.php', {transition: 'fade', type:'post', data: 'T001_Quantidade=' + encodeURIComponent($('#T001_Quantidade_{$uniqueIdProduto}').val()) + '&T001_Flag_Operacao=' + encodeURIComponent($('#T001_Flag_Operacao_{$uniqueIdProduto}').val()) + '&T066_Id={$row['T066_Id']}&D009_Id={$row['D009_Id']}'})"><input id="preco" type="button" data-inline="true" data-role="button" value=">>"></a>
			</div>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
        </div>
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
