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
	case 'imp004Cabecalho':
		// Select
		$sql = <<<EOT
		SELECT 
			*,
			if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
		FROM 
			T293
		left join D024 on T293_D024_Id=D024_Id
		left join D020 on D024_D020_Id=D020_Id
		left join D018 on D020_D018_Id=D018_Id
		left join C007 on C007_Id=D024_C007_Vendedor_Interno
		left join D030 on D024_D030_Id=D030_Id
		WHERE
			(T293_Id='$r_acaoId')

EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}
		
		$dataEmissao = gCorrigeData($row['T293_Data_Emissao']);
		$dataPrevisao = gCorrigeData($row['T293_Data_Previsao']);
		
        $urlSRM = <<<EOT
            onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'SRM - Fornecedor', '/srm/srm001/content/srm001ContentPrincipalSRM/', '&D024_Id=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', 'auto');"
EOT;

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all" width='100%'>
					<tr class="linhaDesc" style="cursor:pointer;" {$urlSRM}>
						<td>Quotation</td>
						<td>Fornecedor</td>
						<td>Cidade</td>
						<td>UF</td>
						<td>Emissão</td>
						<td>Previsão</td>
						<td>NF</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['T293_Id']}</td>
						<td>{$row['D024_Nome_Empresa']}</td>
						<td>{$row['D020_Nome_Cidade']}</td>
						<td>{$row['D018_UF']}</td>
						<td>{$dataEmissao}</td>
						<td>{$dataPrevisao}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'imp004CabecalhoItensOC':
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
	            <button type="button" id="imp004Importar" style="margin:3px 8px;">Importar Produtos</button>
	            <button type="button" id="imp004DefinirQuantidade" style="margin:3px 8px;">Preencher quantidade</button>
	            <button type="button" id="imp004LimparQuantidade" style="margin:3px 8px;">Limpar quantidade</button>
	        </div>
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			    $('#imp004Importar').unbind().bind('click', function(data) {
		            var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/imp/imp004/grid_func-ajax/imp004ImportarItensOC/?T293_Id=' + encodeURIComponent('{$r_acaoId}') + '&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&ajax=true&callback=?', function(request) {
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

				$('#imp004DefinirQuantidade').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/imp/imp004/grid_func-ajax/imp004DefinirQuantidadeItensOC/?quantidade=total&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
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
			
				$('#imp004LimparQuantidade').bind('click', function(data) {
		            var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/imp/imp004/grid_func-ajax/imp004DefinirQuantidadeItensOC/?quantidade=zero&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
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

	case 'imp004ManualCSVCliente':

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

	case 'imp004CabecalhoGerarPO':
	    
		// Select
		$sql = <<<EOT
		SELECT 
			*
		FROM 
			D024
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		WHERE
			(D024_Id='{$r_D024_Id}')
EOT;
		$resultado = mysql_query($sql);
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
						<td width='400px'>Fornecedor</td>
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
        $D024_Id=$r_acaoId;
    break;	

	case 'imp004AnaliseQuotation':
		global $g, $confUsuario;        

		$T293_Id = $r_acaoId;    
			
		$T293 =  <<<EOT
				SELECT T293_Id,
					T293_Data_Emissao,
					D024_Nome_Empresa,
					D024_Endereco,
					D024_Numero,
					D024_Bairro,
					D024_Cep,
					D020_Nome_Cidade,
					D018_UF,
					D024_Cnpj,
					D024_Cpf,
					D024_Inscricao_Estadual,
					comprador.C007_Primeiro_Nome as compradorC007_Primeiro_Nome,
					comprador.C007_Email as compradorC007_Email,
					T293_cotacao_ME,
					T293_Valor_Frete,
					T293_Flag_Transporte_DI,
					D030_Nome_Pais
				FROM T293 
			LEFT JOIN D024 ON D024_Id=T293_D024_Id 
			LEFT JOIN D020 ON D020_Id=D024_D020_Id
			LEFT JOIN D018 ON D018_Id=D024_D018_Id 
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=T293_C007_Id
			LEFT JOIN D030 ON D030_Id=D024_D030_Id
				WHERE T293_Id='{$T293_Id}';
EOT;
    
		$T293   = mysql_query($T293);
		$error  = mysql_error();
		if(!empty($error)){
			return $error;
		}        
		
		$mT293 = mysql_fetch_array($T293);
		
		$html .= <<<EOT
		<style>
			.body {
				margin: 0px 0px 0px 0px;
				background: white;
				background-color: white;
			}
			.moldura {
				font-size: 12px;
				background:white;
				cellspacing:0px;
				width:100%;
				padding: 0px;
				margin: 0px 0px 0px 0px;
			}           
			.moldura td {
				background:white;
				padding:1px 1px 1px 1px;
			}           
			.tabela {
				cellspacing:0px;
				font-size:12px;
				padding: 2px 2px 2px 2px;
			}           
			.tdBorda {
				border-right:1px solid silver;
				border-bottom:1px dotted silver;
				background:white;
				font-size:12px;
			}           
			.tdBordaR {
				font-size:12px;
				background:white;
				border-bottom:1px dotted silver;
			}           
			.tdBordaL {
				border-right:1px solid silver;
				border-bottom:1px dotted silver;
				background:white;
				font-size:12px;
			}           
			.tdTitulo {
				border-right:1px solid silver;
				font-size:12px;
				background:lightblue;
			}           
			.tdTituloR {
				font-size:12px;
				background:lightblue;
			}           
			.tdTituloL {
				border-right:1px solid silver;
				font-size:12px;
				background:lightblue;
			}           
			table {
				font-family: Arial;
			}  
		</style>
EOT;
    
		$mT293['T293_Data_Emissao'] = gCorrigeData($mT293['T293_Data_Emissao']);
		$T293_Numero_PO = str_pad($T293_Id, 6, "0", STR_PAD_LEFT);
		
		$html .= <<<EOT
		<body bgcolor="white">
		<table class="moldura">            
			<tr>
				<td colspan="3" style="background:white;font-size:18px;font-weight:bold;padding:5px 5px 5px 5px;border-bottom:1px solid Black" align="center">
					ANÁLISE QUOTATION
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table class="tabela" style="width:1000px">
						<tr>
							<td width="30%">
								<table class="tabela" width="100%">
									<tr>
										<td><b>{$mT293['D024_Nome_Empresa']}</b></td>
									</tr>
									<tr>
										<td>{$mT293['D024_Endereco']}, {$mT293['D024_Numero']}</td>
									</tr>
									<tr>
										<td><b>{$mT293['D020_Nome_Cidade']} - {$mT293['D030_Nome_Pais']}</td>
									</tr>
								</table>
							</td>
							<td width="25%">
								<table class="tabela" width="100%">                                    
									<tr>
										<td>
											<table class="tabela">
												<tr>
													<td><b><u>COMPRADOR:</u></b></td>
												</tr>
												<tr>
													<td>{$mT293['compradorC007_Primeiro_Nome']}</td>
												</tr>
												<tr>
													<td>{$mT293['compradorC007_Email']}</td>
												</tr>
											</table>                    
										</td>
									</tr>
								</table>
							</td>
							<td>
								<table>
									<tr>
										<td style="font-size:18px" align="center">
											<b>QUOTATION<br>Nº {$T293_Numero_PO}<br>{$mT293['T293_Data_Emissao']}<b>
										</td>
									</tr>
								</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
EOT;

    
		$T294 = <<<EOT
				SELECT D001_Id,
					T294_Codigo_Produto,
					T294_Descricao_Produto,
					T294_Quantidade,
					T294_Valor_Custo_Unitario,
					T294_Valor_Total_Custo,
					D037_Unidade,
					D005_Classificacao_Fiscal,
					D006_Codigo_CFOP,
					T294_Aliquota_Imposto_Importacao,
					T294_Aliquota_IPI,
					T294_Aliquota_PIS,
					T294_Aliquota_COFINS,
					T294_Peso_Kg_Liquido_Unitario,
					D009_Valor_Custo_Ultima_Compra,
					D009_Quantidade_Estoque,
					D009_Quantidade_Comprar_Calculada,
					D009_Quantidade_Consumo ,
					D009_Quantidade_Minima_Estoque,
					D009_Quantidade_Maxima_Estoque,
					D009_Frequencia_Venda,
					D009_Clientes_Consumo,
					D009_Frequencia_Venda_Meses,
					T293_Id,
					T293_Data_Emissao,
					D024_Nome_Empresa,
					T294_D009_Id,
					T294_Valor_Custo_Final_Unitario,
					D001_Descricao_Produto
				FROM T294 
			LEFT JOIN D009 ON D009_Id=T294_D009_Id 
			LEFT JOIN T293 ON T293_Id=T294_T293_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D003 ON D003_Id=D001_D003_Id
			LEFT JOIN T144 ON T144_D001_Id=D001_Id
			LEFT JOIN D037 ON D037_Id=T294_D037_Id
			LEFT JOIN D005 ON D005_Id=T294_D005_Id
			LEFT JOIN D006 ON D006_Id=T294_D006_Id
			LEFT JOIN T001 on T001_Id=D009_T001_Id
			LEFT JOIN D024 ON D024_Id=T001_D024_Id
				WHERE T294_T293_Id = '{$T293_Id}' 
			GROUP BY T294_Id
			ORDER BY D009_Id ASC, IF('{$T293_Id}'=T293_Id,1,2) ASC
EOT;
		$T294  = mysql_query($T294); 
		$error = mysql_error();
		if(!empty($error)){
			return $error;
		}        
    
		$pesoTotal = mysql_query("SELECT SUM(T294_Peso_Kg_Liquido_Unitario) as pesoTotal
									FROM T294
								WHERE T294_T293_Id = '{$T293_Id}'");
		$mPesoTotal = mysql_fetch_array($pesoTotal);

		// valor das despesas para usar na analise
		$T299 = mysql_query("SELECT T299_Valor,
									D191_Descricao_Despesa
							   FROM T299
						  LEFT JOIN D191 ON D191_Id = T299_D191_Id
							  WHERE T299_T293_Id = '{$T293_Id}'");

		while($mT299 = mysql_fetch_array($T299)){

			if($mT299['D191_Descricao_Despesa'] == 'FRETE INTERNACIONAL'){
				$valorTotalFrete = $mT299['T299_Valor'];
			}
		};
		
		/**
		* TABELA PRODUTOS
		*/
		$html .= <<<EOT
			<tr>
				<td colspan="3" style="padding:0px 0px 0px 0px;border-top:1px solid Black" >
					<table cellspacing="0" cellpadding="4px" width="100%" height="100%" style="border:0px;padding:2px 2px 2px 2px">
EOT;
    	$count = 1;
    
		$html .= <<<EOT
			<tr>
				<td align="center" style="background:#C9DFEA" class="tdTitulo">
					Produto
				</td>
				<td align="center" style="background:#C9DFEA" class="tdTitulo">
					Quant
				</td>
				<td align="right" style="background:#C9DFEA" class="tdTitulo">
					Preço FOB
				</td>
				<td align="right" style="background:#C9DFEA" class="tdTitulo">
					Total FOB
				</td>
				<td align="center" style="background:#C9DFEA;width:50px" class="tdTitulo">
					Cot.ME
				</td>
				<td align="right" style="background:#C9DFEA" class="tdTitulo">
					Frete<br>Unitário ME
				</td>
				<td align="right" style="background:#C9DFEA;width:80px" class="tdTitulo">
					Total<br>Frete ME
				</td>
				<td align="center" style="background:#C9DFEA" class="tdTitulo">
					Und
				</td>
				<td align="center" style="background:#C9DFEA" class="tdTitulo">
					NCM
				</td>
				<td align="center" style="background:#C9DFEA" class="tdTitulo">
					%II
				</td>
				<td align="center" style="background:#C9DFEA" class="tdTitulo">
					%IPI
				</td>
				<td align="center" style="background:#C9DFEA" class="tdTitulo">
					%PIS
				</td>
				<td align="center" style="background:#C9DFEA" class="tdTitulo">
					%COFINS
				</td>
				<td align="center" style="background:#C9DFEA;width:1%" class="tdTitulo">
					Mod.Frete
				</td>
				<td align="center" style="background:#C9DFEA;width:60px" class="tdTitulo">
					Custo
				</td>
				<td align="center" style="background:#C9DFEA;width:60px" class="tdTitulo">
					Fator Importação
				</td>
				<td align="center" style="background:#C9DFEA;width:60px" class="tdTitulo">
					Estoque
				</td>
				<td align="center" style="background:#C9DFEA;width:60px" class="tdTitulo">
					Comprar
				</td>
				<td class="tdTitulo" align="center" style="background:#C9DFEA;">
					Vendido
				</td>
				<td class="tdTitulo" align="center" style="background:#C9DFEA;">
					Mínimo
				</td>
				<td class="tdTitulo" align="center" style="background:#C9DFEA;">
					Máximo
				</td>
				<td class="tdTitulo" align="center" style="background:#C9DFEA;">
					Freq
				</td>
				<td class="tdTitulo" align="center" style="background:#C9DFEA;">
					Client
				</td>
				<td class="tdTitulo" align="center" style="background:#C9DFEA;">
					Freq Mes
				</td>
			</tr>
EOT;
    
    	$mT293['T293_cotacao_ME']  = gCorrigeNumero($mT293['T293_cotacao_ME']);
    
		while($mT294 = mysql_fetch_array($T294)){
			$mT294['sobra'] = gCorrigeNumero($mT294['T294_Valor_Preco_Venda_Negociado']-$mT294['T294_Valor_Preco_Venda']);
			if ($mT294['T294_Valor_Preco_Venda_Negociado']<=0){
				$mT294['sobra'] = 0;
			}
		
			$valorFreteUnitario                              = gCorrigeNumero($valorTotalFrete * ($mT294['T294_Peso_Kg_Liquido_Unitario'] / $mPesoTotal['pesoTotal']));
			$fatorImportacao							     = gCorrigeNumero($mT294['D009_Valor_Custo_Ultima_Compra'] / $mT294['T294_Valor_Custo_Unitario']);
			$mT294['T294_Quantidade']                        = gCorrigeNumero($mT294['T294_Quantidade']);
			$mT294['T294_Valor_Custo_Unitario']              = gCorrigeNumero($mT294['T294_Valor_Custo_Unitario']);
			$mT294['T294_Valor_Total_Custo']                 = gCorrigeNumero($mT294['T294_Valor_Total_Custo']);
			$mT294['T294_Aliquota_IPI']                      = gCorrigeNumero($mT294['T294_Aliquota_IPI']);
			$mT294['T294_Aliquota_PIS']                      = gCorrigeNumero($mT294['T294_Aliquota_PIS']);
			$mT294['T294_Aliquota_COFINS']                   = gCorrigeNumero($mT294['T294_Aliquota_COFINS']);
			$mT294['T294_Aliquota_Imposto_Importacao']       = gCorrigeNumero($mT294['T294_Aliquota_Imposto_Importacao']);
			$mT294['D009_Quantidade_Estoque']                = gCorrigeNumero($mT294['D009_Quantidade_Estoque']);
			$mT294['D009_Quantidade_Consumo']                = gCorrigeNumero($mT294['D009_Quantidade_Consumo']);
			$mT294['D009_Quantidade_Comprar_Calculada']      = gCorrigeNumero($mT294['D009_Quantidade_Comprar_Calculada']);
			$mT294['T294_Valor_Preco_Venda']                 = gCorrigeNumero($mT294['T294_Valor_Preco_Venda']);
			$mT294['T294_Valor_Frete_Unitario']              = gCorrigeNumero($mT294['T294_Valor_Frete_Unitario']);
			$mT294['D009_Quantidade_Minima_Estoque']         = gCorrigeNumero($mT294['D009_Quantidade_Minima_Estoque']);
			$mT294['D009_Quantidade_Maxima_Estoque']         = gCorrigeNumero($mT294['D009_Quantidade_Maxima_Estoque']);
			$mT294['T294_Valor_Custo_Final_Unitario']        = gCorrigeNumero($mT294['T294_Valor_Custo_Final_Unitario']);
		
			$observacaoItem='<font></font>';
			if (!empty($mT294['T294_Observacao'])) {
				$observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> '.$mT294['T294_Observacao'].'</font><br>';
			}
			//$corLinha = ($mT294['T294_T293_Id'] == $T293_Id) ? 'style="background-color: #DDD;"' : '';
			
			$corLinha="background:white;";
		
			$html .=<<<EOT
			<tr onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique() , '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$mT294['D001_Id']}') + '&tabela=D001', [1000,600]);">
				<td align="left" style="{$corLinha}max-width:400px;min-width:450px;width:450px;border-top:1px solid silver;cursor: pointer;" class="tdBorda" width="300px">
					<b><u>{$mT294['T294_Codigo_Produto']} - {$mT294['D001_Descricao_Produto']}</u></b>
				</td>
				<td align="center" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					<b>{$mT294['T294_Quantidade']}</b>
				</td>
				<td align="right" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					<b>{$mT294['T294_Valor_Custo_Unitario']}</b>
				</td>
				<td align="right" class="tdBorda" style='padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$mT294['T294_Valor_Total_Custo']}
				</td>
				<td align="center" class="tdBorda" style='padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$mT293['T293_cotacao_ME']}
				</td>
				<td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$valorFreteUnitario}
				</td>
				<td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					-
				</td>
				<td align="center" class="tdBorda" style='background-color: #BADCFC;padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$mT294['D037_Unidade']}
				</td>
				<td align="center" class="tdBorda" style='background-color: #BADCFC;padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$mT294['D005_Classificacao_Fiscal']}
				</td>
				<td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$mT294['T294_Aliquota_Imposto_Importacao']}
				</td>
				<td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$mT294['T294_Aliquota_IPI']}
				</td>
				<td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$mT294['T294_Aliquota_PIS']}
				</td>
				<td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
					{$mT294['T294_Aliquota_COFINS']}
				</td>
				<td align="center" class="tdBorda" style="background-color:  var(--celula-amarelo); color: var(--letra2); padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
					-
				</td>
				<td align="center" class="tdBorda" style="background-color:  var(--celula-amarelo); color: var(--letra2); padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
					{$mT294['T294_Valor_Custo_Final_Unitario']}
				</td>
				<td align="center" class="tdBorda" style="background-color:  var(--celula-amarelo); color: var(--letra2); padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
					{$fatorImportacao}
				</td>
				<td align="center" class="tdBorda" style=" padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
					{$mT294['D009_Quantidade_Estoque']}
				</td>
				<td align="center" class="tdBorda" style=" padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
					{$mT294['D009_Quantidade_Comprar_Calculada']}
				</td>
				<td align="center" class="tdBorda" style=" padding: 5px 5px 5px 5px;border-top:1px solid silver">
					{$mT294['D009_Quantidade_Consumo']}
				</td>
				<td align="center" class="tdBorda" style=" padding: 5px 5px 5px 5px;border-top:1px solid silver">
					{$mT294['D009_Quantidade_Minima_Estoque']}
				</td>
				<td align="center" class="tdBorda" style=" padding: 5px 5px 5px 5px;border-top:1px solid silver">
					{$mT294['D009_Quantidade_Maxima_Estoque']}
				</td>
				<td align="center" class="tdBorda" style=" padding: 5px 5px 5px 5px;border-top:1px solid silver">
					{$mT294['D009_Frequencia_Venda']}
				</td>
				<td align="center" class="tdBorda" style=" padding: 5px 5px 5px 5px;border-top:1px solid silver">
					{$mT294['D009_Clientes_Consumo']}
				</td>
				<td align="center" class="tdBorda" style=" padding: 5px 5px 5px 5px;border-top:1px solid silver">
					{$mT294['D009_Frequencia_Venda_Meses']}
				</td>
			</tr>
EOT;
           
			$T055 = <<<EOT
				SELECT D001_Id,
					T055_Codigo_Produto,
					T055_Descricao_Produto,
					T055_Quantidade,
					T055_Valor_Custo_Unitario,
					T055_Valor_Total_Custo,
					D037_Unidade,
					D005_Classificacao_Fiscal,
					T055_Aliquota_Imposto_Importacao,
					T055_Aliquota_IPI,
					T055_Aliquota_PIS,
					T055_Aliquota_COFINS,
					T055_Peso_Kg_Liquido_Unitario,
					D009_Valor_Custo_Ultima_Compra,
					D009_Quantidade_Estoque,
					D009_Quantidade_Comprar_Calculada,
					D009_Quantidade_Consumo ,
					D009_Quantidade_Minima_Estoque,
					D009_Quantidade_Maxima_Estoque,
					D009_Frequencia_Venda,
					D009_Clientes_Consumo,
					D009_Frequencia_Venda_Meses,
					T075_Id,
					T075_Numero_DI,
					T007_Data_Emissao,
					T007_Numero_Nota_Fiscal,
					D024_Nome_Empresa,
					D024_Id,
					T055_D009_Id,
					T075_Cotacao_ME,
					T075_Valor_Frete,
					T075_Flag_Transporte_DI,
					T055_Valor_Custo_Final_Unitario
				FROM T055 
			LEFT JOIN D009 ON D009_Id=T055_D009_Id 
			LEFT JOIN T075 ON T075_Id=T055_T075_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D037 ON D037_Id=T055_D037_Id
			LEFT JOIN D005 ON D005_Id=T055_D005_Id
			LEFT JOIN D024 ON D024_Id=T075_D024_Id
			LEFT JOIN T008 ON T055_Id=T008_T055_Id
			LEFT JOIN T007 ON T007_Id=T008_T007_Id
				WHERE T055_D009_Id = '{$mT294['T294_D009_Id']}' 
				AND T007_Id IS NOT NULL
				AND T007_Flag_Cancelada != 'S'
			GROUP BY T055_Id
			ORDER BY T075_Id DESC LIMIT 5
EOT;
    
			$T055 = mysql_query($T055); 
			echo mysql_error();
		
			while ($mT055=mysql_fetch_array($T055)){
		
				$tiposFrete = array('1' => 'Marítima',
									'2' => 'Fluvial',
									'3' => 'Lacustre',
									'4' => 'Aérea',
									'5' => 'Postal',
									'6' => 'Ferroviária',
									'7' => 'Rodoviária',
									'8' => 'Conduto / Rede Transmissão',
									'9' => 'Meios Próprios',
									'10' => 'Entrada / Saída ficta',
									'11' => 'Courier',
									'12' => 'Handcarry');
				$T075_Flag_Transporte_DI = $tiposFrete["{$mT055['T075_Flag_Transporte_DI']}"];
		
				$pesoTotalDI = mysql_query("SELECT SUM(T055_Peso_Kg_Liquido_Unitario) as pesoTotalDI
												FROM T055
											WHERE T055_T075_Id = '{$mT055['T075_Id']}'");
				$mPesoTotalDI = mysql_fetch_array($pesoTotalDI);
		
				$valorFreteUnitarioDI                            = gCorrigeNumero($mT055['T075_Valor_Frete'] * ($mT055['T055_Peso_Kg_Liquido_Unitario'] / $mPesoTotalDI['pesoTotalDI']));
				$fatorImportacaoDI							     = gCorrigeNumero($mT055['D009_Valor_Custo_Ultima_Compra'] / $mT055['T055_Valor_Custo_Unitario']);
				$mT055['T055_Quantidade']                        = gCorrigeNumero($mT055['T055_Quantidade']);
				$mT055['T055_Valor_Custo_Unitario']              = gCorrigeNumero($mT055['T055_Valor_Custo_Unitario']);
				$mT055['T055_Valor_Total_Custo']                 = gCorrigeNumero($mT055['T055_Valor_Total_Custo']);
				$mT055['T055_Aliquota_IPI']                      = gCorrigeNumero($mT055['T055_Aliquota_IPI']);
				$mT055['T055_Aliquota_PIS']                      = gCorrigeNumero($mT055['T055_Aliquota_PIS']);
				$mT055['T055_Aliquota_COFINS']                   = gCorrigeNumero($mT055['T055_Aliquota_COFINS']);
				$mT055['T055_Aliquota_Imposto_Importacao']       = gCorrigeNumero($mT055['T055_Aliquota_Imposto_Importacao']);
				$mT055['D009_Quantidade_Estoque']                = gCorrigeNumero($mT055['D009_Quantidade_Estoque']);
				$mT055['D009_Quantidade_Consumo']                = gCorrigeNumero($mT055['D009_Quantidade_Consumo']);
				$mT055['D009_Quantidade_Comprar_Calculada']      = gCorrigeNumero($mT055['D009_Quantidade_Comprar_Calculada']);
				$mT055['T055_Valor_Preco_Venda']                 = gCorrigeNumero($mT055['T055_Valor_Preco_Venda']);
				$mT055['T055_Valor_Frete_Unitario']              = gCorrigeNumero($mT055['T055_Valor_Frete_Unitario']);
				$mT055['D009_Quantidade_Minima_Estoque']         = gCorrigeNumero($mT055['D009_Quantidade_Minima_Estoque']);
				$mT055['D009_Quantidade_Maxima_Estoque']         = gCorrigeNumero($mT055['D009_Quantidade_Maxima_Estoque']);
				$mT055['D009_Valor_Custo_Ultima_Compra']         = gCorrigeNumero($mT055['D009_Valor_Custo_Ultima_Compra']);
				$mT055['T055_Valor_Custo_Final_Unitario']        = gCorrigeNumero($mT055['T055_Valor_Custo_Final_Unitario']);
				$mT055['T075_Cotacao_ME']     				     = gCorrigeNumero($mT055['T075_Cotacao_ME']);
				$mT055['T075_Valor_Frete']     				     = gCorrigeNumero($mT055['T075_Valor_Frete']);
				$mT055['T007_Data_Emissao']					     = gCorrigeData($mT055['T007_Data_Emissao']);
				$mT055['D024_Nome_Empresa']                      = substr($mT055['D024_Nome_Empresa'],0,20);
				
				if ($corLinha=="background:white;"){
					$corLinha='background:#F4F4F4;';
				} else {
					$corLinha="background:white;";
				}
				$html .= "<tr>";
				$html .=<<<EOT
					<td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
						DI: {$mT055['T075_Numero_DI']} - ({$mT055['D024_Id']}) {$mT055['D024_Nome_Empresa']} (NF {$mT055['T007_Numero_Nota_Fiscal']} - {$mT055['T007_Data_Emissao']})
					</td>
					<td align="center" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px'>
						<b>{$mT055['T055_Quantidade']}</b>
					</td>
					<td align="right" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px'>
						<b>{$mT055['T055_Valor_Custo_Unitario']}</b>
					</td>					
					<td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
						{$mT055['T055_Valor_Total_Custo']}
					</td>
					<td align="center" class="tdBorda" style="{$corLinha}padding: 5px 5px 5px 5px">
						{$mT055['T075_Cotacao_ME']}
					</td>
					<td align="right" class="tdBorda" style="{$corLinha}padding: 5px 5px 5px 5px">
						{$valorFreteUnitarioDI}
					</td>
					<td align="right" class="tdBorda" style="{$corLinha}padding: 5px 5px 5px 5px">
						{$mT055['T075_Valor_Frete']}
					</td>
					<td align="center" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
						{$mT055['D037_Unidade']}
					</td>
					<td align="center" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
						{$mT055['D005_Classificacao_Fiscal']}
					</td>
					<td align="center" class="tdBorda" style="{$corLinha}padding: 5px 5px 5px 5px">
						{$mT055['T055_Aliquota_Imposto_Importacao']}
					</td>
					<td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
						{$mT055['T055_Aliquota_IPI']}
					</td>
					<td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
						{$mT055['T055_Aliquota_PIS']}
					</td>
					<td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
						{$mT055['T055_Aliquota_COFINS']}
					</td>
					<td align="center" class="tdBorda" style="background-color:  var(--celula-amarelo); color: var(--letra2); padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
						{$T075_Flag_Transporte_DI}
					</td>
					<td align="center" class="tdBorda" style="background-color:  var(--celula-amarelo); color: var(--letra2); padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
						{$mT055['T055_Valor_Custo_Final_Unitario']}
					<td align="center" class="tdBorda" style="background-color:  var(--celula-amarelo); color: var(--letra2); padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
						{$fatorImportacaoDI}
					</td>
					<td align="left" style='{$corLinha}padding: 5px 5px 5px 5px' class="tdBordaR" colspan=30>
						-
					</td>
				</tr>
EOT;
			}
		
		}
    
		$html .= <<<EOT
		</table>
				
				</td>
			</tr>
			<tr>
				<td colspan="3" style="padding:0px 0px 0px 0px;border-top:1px solid black;">
    
EOT;
    
		/**
		* PDF - TOTAIS
		*/
		
		$T294 = mysql_query("SELECT SUM(T294_Valor_Total_Custo) as T294_Valor_Total_Custo,
									SUM(T294_Valor_Imposto_Importacao) as T294_Valor_Imposto_Importacao,
									SUM(T294_Valor_IPI) as T294_Valor_IPI,
									SUM(T294_Valor_PIS) as T294_Valor_PIS,
									SUM(T294_Valor_COFINS) as T294_Valor_COFINS,
									SUM(T294_Valor_Total_Preco_Nota_Fiscal) as T294_Valor_Total_Preco_Nota_Fiscal,
									T293_Flag_Transporte_DI, 
									T293_Data_Previsao
							FROM T294
						LEFT JOIN T293 ON T293_Id = T294_T293_Id
							WHERE T294_T293_Id = '{$T293_Id}'");
		
		$mT294 = mysql_fetch_array($T294);
		$mT294['T294_Valor_Total_Custo']    			= gCorrigeNumero($mT294['T294_Valor_Total_Custo']);
		$mT294['T294_Valor_Imposto_Importacao'] 		= gCorrigeNumero($mT294['T294_Valor_Imposto_Importacao']);
		$mT294['T294_Valor_IPI']       					= gCorrigeNumero($mT294['T294_Valor_IPI']);
		$mT294['T294_Valor_PIS']       					= gCorrigeNumero($mT294['T294_Valor_PIS']);
		$mT294['T294_Valor_Total_Preco_Nota_Fiscal']    = gCorrigeNumero($mT294['T294_Valor_Total_Preco_Nota_Fiscal']);
		$mT294['T294_Valor_COFINS']       	    		= gCorrigeNumero($mT294['T294_Valor_COFINS']);
		$mT294['T293_Data_Previsao']            		= gCorrigeData($mT294['T293_Data_Previsao']);
		$valorTotalFrete                                = gCorrigeNumero($valorTotalFrete);        
		
		$tiposFrete = array('1' => 'Marítima',
							'2' => 'Fluvial',
							'3' => 'Lacustre',
							'4' => 'Aérea',
							'5' => 'Postal',
							'6' => 'Ferroviária',
							'7' => 'Rodoviária',
							'8' => 'Conduto / Rede Transmissão',
							'9' => 'Meios Próprios',
							'10' => 'Entrada / Saída ficta',
							'11' => 'Courier',
							'12' => 'Handcarry');
		$T293_Frete = $tiposFrete["{$mT294['T293_Flag_Transporte_DI']}"];
		
		$html .= <<<EOT
		<table class="tabela" cellspacing="0px" cellpadding="4px">
				<tr>
					<td align="left" width="105"></td><td align="left"></td>
					<td align="left" width="105">Total Produtos: </td><td align="left"><b>ME {$mT294['T294_Valor_Total_Custo']}</b></td>
				</tr>
				<tr>
					<td align="left" width="105">Frete: </td><td align="left"><b>{$T293_Frete}</b></td>
					<td align="left" width="105">Valor Frete: </td><td  align="left"><b>ME {$valorTotalFrete}</b></td>
				</tr>
				<tr>
					<td align="left" width="105">Prev.Chegada: </td><td align="left" width="330"><b>{$mT294['T293_Data_Previsao']}</b></td>
					<td align="left" width="105">Valor IPI: </td><td  align="left"><b>R$ {$mT294['T294_Valor_IPI']}</b></td>
				</tr>
				<tr>
					<td align="left" width="105"></td><td align="left"></td>
					<td align="left" width="105">Valor II: </td><td  align="left"><b>R$ {$mT294['T294_Valor_Imposto_Importacao']}</b></td>
				</tr>
				<tr>
					<td align="left" width="105"></td><td align="left" width="330px"></td>
					<td align="left" width="105">Valor PIS: </td><td align="left"><b>R$ {$mT294['T294_Valor_PIS']}</b></td>
				</tr>
				<tr>
					<td align="left" width="105"></td><td align="left"></td>
					<td align="left" width="105">Valor COFINS: </td><td align="left"><b>R$ {$mT294['T294_Valor_COFINS']}</b></td>
				</tr>      
				<tr>
					<td align="left" width="105"></td><td align="left"></td>
					<td align="left" width="105">Total Produtos NF: </td><td align="left"><b>R$ {$mT294['T294_Valor_Total_Preco_Nota_Fiscal']}</b></td>
				</tr>  
		</table>	
	
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$(window).unload( function () { opener.recarregarContent(); } );
		}</script></div>    
EOT;
    		 
    	echo $html;
    
	break;
}
?>