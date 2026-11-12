<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Orçamento
	 * Chamado em: ven001TabEditarOrcamento
	 */
	case 'imp003Cabecalho':
		// Select
		$sql = <<<EOT
		SELECT 
			*,
			if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
		FROM 
			T075
		left join D024 on T075_D024_Id=D024_Id
		left join D020 on D024_D020_Id=D020_Id
		left join D018 on D020_D018_Id=D018_Id
		left join C007 on C007_Id=D024_C007_Vendedor_Interno
		left join D030 on D024_D030_Id=D030_Id
		left join T007 on T007_T075_Id=T075_Id and T007_Flag_Cancelada!='S'
		WHERE
			(T075_Id='$r_acaoId')

EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}
		
		$dataEmissao = gCorrigeData($row['T075_Data_Emissao_DI']);
		$dataPrevisao = gCorrigeData($row['T075_Data_Previsao']);
		
        $urlSRM = <<<EOT
            onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'SRM - Fornecedor', '/srm/srm001/content/srm001ContentPrincipalSRM/', '&D024_Id=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', 'auto');"
EOT;

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all" width='100%'>
					<tr class="linhaDesc" style="cursor:pointer;" {$urlSRM}>
						<td>DI</td>
						<td>P.O.</td>
						<td>Fornecedor</td>
						<td>Cidade</td>
						<td>UF</td>
						<td>Emissão</td>
						<td>Previsão</td>
						<td>NF</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['T075_Numero_DI']}</td>
						<td>{$row['T075_Ordem_Compra']}</td>
						<td>{$row['D024_Nome_Empresa']}</td>
						<td>{$row['D020_Nome_Cidade']}</td>
						<td>{$row['D018_UF']}</td>
						<td>{$dataEmissao}</td>
						<td>{$dataPrevisao}</td>
						<td>{$row['T007_Numero_Nota_Fiscal']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'imp003CabecalhoItensOC':
		// Select
		$resultado = mysql_query("SELECT D024_Id, D024_Nome_Empresa, D018_UF, D020_Nome_Cidade FROM D024 LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE D024_Id='{$r_D024_Id}'");
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}
		
		$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 50);
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);
        
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Código</td>
						<td width='400px'>Cliente</td>
						<td width='150px'>Cidade</td>
						<td width='150px'>Estado</td>
						<td></td>
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['D024_Id']}</td>
						<td>{$D024_Nome_Empresa}</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;
	    $novoIdTelaForm = uniqid();
        
        $T006 = mysql_query("select T005_Id,
									sum(T006_Quantidade*T006_Valor_Preco_Sem_Desconto_Unitario) as totalProdutos,
									sum(T006_Quantidade_Separacao*T006_Valor_Preco_Sem_Desconto_Unitario) as totalSeparado,
									sum(if(T006_Quantidade_Estoque<T006_Quantidade,T006_Quantidade_Estoque,T006_Quantidade)*T006_Valor_Preco_Sem_Desconto_Unitario) as totalEstoque,
									sum(T006_Quantidade_Dividir*T006_Valor_Preco_Sem_Desconto_Unitario) as totalDividir
							   from T006 LEFT JOIN T005 on T005_Id=T006_T005_Id WHERE T005_D024_Id='{$D024_Id}' and T006_Quantidade>0 and T005_C004_Id='{$g['empresaAtual']}' 
							   AND (T005_Flag_Status='' or T005_Flag_Status='0' or T005_Flag_Status='26' or T005_Flag_Status='9' or T005_Flag_Status='14') 
							   AND T005_Flag_Dividido != 'S' ORDER BY T005_Id DESC LIMIT 1");
        $mT006 = mysql_fetch_array($T006);
		echo mysql_error();

        $sql="SELECT SUM(T005_Valor_Total_Produtos),T005_Valor_Total_Servicos,T005_Valor_Troca,T005_Valor_Desconto_Manual,T005_Valor_Total, T005_Id FROM T005 WHERE T005_D024_Id={$D024_Id}";
        $sqlResult = mysql_query($sql);
        $row = mysql_fetch_array($sqlResult);
        $T005_Id = $mT006['T005_Id'];
        $totalProdutos = gCorrigeNumero($mT006['totalProdutos']);
        $totalSeparado = gCorrigeNumero($mT006['totalSeparado']);
		$totalEstoque = gCorrigeNumero($mT006['totalEstoque']);
        $totalDividir = gCorrigeNumero($mT006['totalDividir']);
        $totalServicos = gCorrigeNumero($row['T005_Valor_Total_Servicos']);
        $totalTroca = gCorrigeNumero($row['T005_Valor_Troca']);
        $totalDesconto = gCorrigeNumero($row['T005_Valor_Desconto_Manual']);
        $totalGeral = gCorrigeNumero($row['T005_Valor_Total']);
        $novoIdTelaForm = uniqid();
        echo <<<EOT
	        <div class="buttonsBottomTop">
	            <button type="button" id="imp003Importar" style="margin:3px 8px;">Importar Produtos</button>
	            <button type="button" id="imp003DefinirQuantidade" style="margin:3px 8px;">Preencher quantidade</button>
	            <button type="button" id="imp003LimparQuantidade" style="margin:3px 8px;">Limpar quantidade</button>
	        </div>
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			    $('#imp003Importar').unbind().bind('click', function(data) {
		            var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/grid_func-ajax/imp003ImportarItensOC/?T075_Id=' + encodeURIComponent('{$r_acaoId}') + '&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&ajax=true&callback=?', function(request) {
								if (request.code) {
		 							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    dialogConfirm('Ok', request.data, \$buttons);
								    divRefresh('{$r_divRefresh}', true);
								    divRefresh('{$r_divDI}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#' + '{$g['divId']}').hideLoading();
								reloadUI('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Conclusão', 'Deseja importar os produtos?', \$buttons);
		        });

				$('#imp003DefinirQuantidade').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/grid_func-ajax/imp003DefinirQuantidadeItensOC/?quantidade=total&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
								$('#' + '{$g['divId']}').hideLoading();
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', "Operação realizada com sucesso!", \$buttons);
								    divRefresh('{$r_divRefresh}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    dialogConfirm('Erro', request.data, \$buttons);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atanção!', 'Este processo ira preencher a quantidade á importar. Você tem certeza?', \$buttons);
				});		
			
				$('#imp003LimparQuantidade').bind('click', function(data) {
		            var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/grid_func-ajax/imp003DefinirQuantidadeItensOC/?quantidade=zero&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
								$('#' + '{$g['divId']}').hideLoading();
								if (request.code) {
		 							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    dialogConfirm('Ok', "Operação realizada com sucesso!", \$buttons);
								    divRefresh('{$r_divRefresh}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    dialogConfirm('Erro', request.data, \$buttons);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atanção!', 'Este processo ira zerar a quantidade á dividir. Você tem certeza?', \$buttons);
		        });
			} </script></div>
EOT;
	break;

	case 'imp003ManualCSVCliente':

        echo <<<EOT
        
        <style>
            .tabelaManual{
                border:1px solid black;
                border-colapse: colapse;    
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabelaManual{               
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }   
        </style>

        <div style="padding-left:10px;">
        <h2>Modelo de documento CSV</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o formato do modelo do documento para a importação!
        <br/>-O documento deve ter <strong>23 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
        <br/>-Os nomes das colunas devem estar necessariamente na primeira linha(1) do documento e ter a mesma grafia do documento modelo, elas devem estar em <strong>MAIÚSCULO</strong>.
        <br/>-A extensão do documento deve ser CSV (Delimitado por vírgulas)(*.csv)
        <br/>-Ao final do documento não deve haver nenhuma linha em branco. 
        <br/>-Os campos com * são obrigatórios.
        <br/>-O campo <strong>UNIDADE</strong> deve ser preenchido conforme o nome que está no cadastro.
        <br/>-O campo <strong>NCM</strong> deve ser preenchido conforme o número que está no cadastro
        <br/>-O campo <strong>ME</strong> deve ser preenchido com o número 1 ou 2. Sendo 1-SIM ou 2-Não. 
        <br/>-O campo <strong>LOCAL</strong> deve ser preenchido conforme o nome que está no cadastro.        
        </p>
        </div>
        <table cellspacing=1px bgcolor="black">     
            <tr>  
            	<td class="linhaTabelaManual" > *PRODUTO </td> 
            	<td class="linhaTabelaManual" > QUANTIDADE </td>
            	<td class="linhaTabelaManual" > UNIDADE </td>
            	<td class="linhaTabelaManual" > NCM </td>
                <td class="linhaTabelaManual" > PRECO UNITARIO </td> 
                <td class="linhaTabelaManual" > ME </td>     
                <td class="linhaTabelaManual" > PESO UNITARIO </td>
                <td class="linhaTabelaManual" > II </td>
                <td class="linhaTabelaManual" > IPI </td>
                <td class="linhaTabelaManual" > PIS </td>
                <td class="linhaTabelaManual" > COFINS </td>
                <td class="linhaTabelaManual" > ADICAO </td>
                <td class="linhaTabelaManual" > ITEM </td>
                <td class="linhaTabelaManual" > LOCAL </td>
                <td class="linhaTabelaManual" > AC_DRAWBACK </td>
                <td class="linhaTabelaManual" > LI </td>
                <td class="linhaTabelaManual" > ICMS </td>
                <td class="linhaTabelaManual" > ICMS PRESUMIDO </td> 
                <td class="linhaTabelaManual" > REDUCAO ICMS </td> 
                <td class="linhaTabelaManual" > REDUCAO ICMS PRESUMIDO </td> 
                <td class="linhaTabelaManual" > ICMS PIS/COFINS </td> 
                <td class="linhaTabelaManual" > ICMS CREDITO PRESUMIDO </td>                 
            </tr>
            <tr>         
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>             
            </tr>
            <tr>          
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>              
            </tr>
            <tr>           
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>              
            </tr>
            <tr>        
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">-</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td> 
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>            
            </tr>   
        </table>
EOT;
 
    break;

	case 'Imp003OutroCabecalhoItemXML':
		// Select

		$T055 = mysql_query("SELECT T055_Descricao_Produto,
									T055_Codigo_Produto,
									D005_Classificacao_Fiscal,
									T055_Valor_Adicao,
									T055_Adicao_Item,
									T055_Quantidade,
									T055_Valor_Custo_Unitario
							  FROM T055
						 LEFT JOIN D009 ON D009_Id = T055_D009_Id
						 LEFT JOIN D049 ON D049_Id = D009_D049_Id
						 LEFT JOIN D001 ON D001_Id = D049_D001_Id
						 LEFT JOIN D005 ON D005_Id = D001_D005_Id
						     WHERE T055_Id = $r_T055_Id");
		
		$mT055 = mysql_fetch_array($T055);

		// Registro vazios = FALTA
		foreach ($mT055 as &$mT055_) {
			if (empty($mT055_)) { 
				$mT055_ = '<span style="color: var(--BGVermelho);">-</span>';
			}
		}

		$mT055['T055_Valor_Custo_Unitario'] = number_format($mT055['T055_Valor_Custo_Unitario'],2,',','.');
		
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='10px'>Adição</td>
						<td width='10px'>Item Adição</td>
						<td width='30px'>Código</td>
						<td width='380px'>Descrição</td>
						<td width='10px'>NCM</td>
						<td width='10px'>Qtde</td>
						<td width='10px'>Custo</td>
					</tr>
					<tr class="linhaVal">
						<td>{$mT055['T055_Valor_Adicao']}</td>
						<td>{$mT055['T055_Adicao_Item']}</td>
						<td>{$mT055['T055_Codigo_Produto']}</td>
						<td>{$mT055['T055_Descricao_Produto']}</td>
						<td>{$mT055['D005_Classificacao_Fiscal']}</td>
						<td>{$mT055['T055_Quantidade']}</td>
						<td>{$mT055['T055_Valor_Custo_Unitario']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
?>