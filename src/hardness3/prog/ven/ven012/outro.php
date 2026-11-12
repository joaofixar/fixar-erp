<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'ven012ReciboNotaFiscal':
		// Qual template usar?
		$docNome = 'reciboNotaFiscal.tpl';
		
		// Classes
		include 'bibliotecas/classes/VEN012.php';
		$VEN012 = new VEN012();
		
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		
		// $sql = "SELECT T002_Data_Emissao_Promissoria FROM T002 WHERE T002_Id='{$T002_Id}'";
		// $result = mysql_query($sql);
		// $linha = mysql_fetch_array($result);
		// if(empty($linha['T002_Data_Emissao_Promissoria'])){
			// $sql = "UPDATE T002 SET T002_Data_Emissao_Promissoria=CURDATE() where T002_Id='{$T002_Id}'";
			// $result = mysql_query($sql);
		// }
		
		// Atribuir os elementos do template (veja documentação dentro do próprio template)
		//$g['smarty']->assign('titulo', $FIN001->getVar('PROMISSORIA_TITULO'));
		$g['smarty']->assign('header', $VEN012->gerarGeraHeader($T007_Id));
		$g['smarty']->assign('containerTop', $VEN012->gerarContainerTop($T007_Id));
		$g['smarty']->assign('containerCenter', $VEN012->gerarCenterContainer($T007_Id));
		// $g['smarty']->assign('conteudoPromissoria', $VEN012->gerarConteudoPromissoria($T007_Id));
		// $g['smarty']->assign('valorPromissoria', $VEN012->gerarvalorPromissoria($T007_Id));
		// Template da empresa sobrescreve o templete padrão
		echo gProcessaDocumento($docNome);
	break;
	
	/**
	 * Cabeçalho Orçamento
	 * Chamado em: ven001TabEditarOrcamento
	 */
	case 'ven012CabecalhoNota':
		// Select
		$sql = <<<EOT
		SELECT 
			*,
			if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
		FROM 
			T005
		left join D024 on T005_D024_Id=D024_Id
		left join D020 on D024_D020_Id=D020_Id
		left join D018 on D020_D018_Id=D018_Id
		left join C007 on C007_Id=D024_C007_Vendedor_Interno
		left join D030 on D024_D030_Id=D030_Id
		left join C004 on C004_Id=T005_C004_Id
		WHERE
			(T005_Id='$r_T005_Id')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}
		
		$dataEmissao = gCorrigeData($row['T005_Data_Emissao']);
		$valor = gCorrigeNumero($row['T005_Valor_Total']);
        $row['D024_Nome_Empresa'] = substr($row['D024_Nome_Empresa'],0,30);
        $row['C004_Nome_Empresa'] = substr($row['C004_Nome_Empresa'],0,30);
        
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='100px'>Pedido</td>
						<td width='350px'>Cliente</td>
						<td>Valor</td>
						<td>Empresa</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['T005_Id']}</td>
						<td>{$row['D024_Nome_Empresa']}</td>
						<td>{$valor}</td>
						<td>{$row['C004_Nome_Empresa']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
    
    case 'ven012Totalizadores':
	    
	    // Select
		$sql = <<<EOT
		SELECT 
			*,
			if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
		FROM 
			T005
		left join D024 on T005_D024_Id=D024_Id
		left join D020 on D024_D020_Id=D020_Id
		left join D018 on D020_D018_Id=D018_Id
		left join C007 on C007_Id=D024_C007_Vendedor_Interno
		left join D030 on D024_D030_Id=D030_Id
		left join C004 on C004_Id=T005_C004_Id
		WHERE
			(T005_Id='$r_T005_Id')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}
		
		$dataEmissao = gCorrigeData($row['T005_Data_Emissao']);
		$valor = gCorrigeNumero($row['T005_Valor_Total']);
		$row['C004_Nome_Empresa'] = substr($row['C004_Nome_Empresa'], 0, 40);
		if(strlen($row['C004_Nome_Empresa']) == 30){
			$row['C004_Nome_Empresa'] = $row['C004_Nome_Empresa']."...";
		}
		
		// Print
        $novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all" width="100%">
					<tr class="linhaDesc">
						<td>Pedido</td>
						<td style="width:500px;"><a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent({$row['D024_Id']}) + '&tabela=D024', [960,583]);" style="padding: 5px 200px 5px 5px;">Cliente</a></td>
						<td>Valor</td>
						<td>Empresa</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['T005_Id']}</td>
						<td><a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent({$row['D024_Id']}) + '&tabela=D024', [960,583]);" style="padding: 5px 50px 5px 5px;">{$row['D024_Nome_Empresa']}</a></td>
						<td>{$valor}</td>
						<td>{$row['C004_Nome_Empresa']}</td>
					</tr>
				</table>
			</div>
EOT;
	    
        $T005_Id = $r_T005_Id;
        $sql="SELECT T005_Valor_Total_Produtos,T005_Valor_Total_Servicos,T005_Valor_Troca,T005_Valor_Desconto_Manual,T005_Valor_Total FROM T005 WHERE T005_Id={$T005_Id}";
        $sqlResult = mysql_query($sql);
        $row = mysql_fetch_array($sqlResult);
        
        $totalProdutos = gCorrigeNumero($row['T005_Valor_Total_Produtos']);
        $totalServicos = gCorrigeNumero($row['T005_Valor_Total_Servicos']);
        $totalTroca = gCorrigeNumero($row['T005_Valor_Troca']);
        $totalDesconto = gCorrigeNumero($row['T005_Valor_Desconto_Manual']);
        $totalGeral = gCorrigeNumero($row['T005_Valor_Total']);
        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="buttonsBottomTop">
            <button type="button" id="ven012DividirPedidoBtn" style="margin:8px;">Dividir Pedido</button>
            <button type="button" id="ven012GerarNFMarcados" style="margin:8px;">Gerar NF</button>
            <button type="button" id="ven012ConcluirNF" style="margin:8px;">Concluir</button>
            <button type="button" id="ven012MudarFilial">Trocar empresa</button>
            <button type="button" id="ven012DuplicarPedido">Duplicar</button>
            <span class="item" style="text-align:right">Produtos<br><input type="text" value="{$totalProdutos}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Serviço<br><input type="text" value="{$totalServicos}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Trocas<br><input type="text" value="{$totalTroca}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Desconto<br><input type="text" value="{$totalDesconto}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Total<br><input type="text" value="{$totalGeral}" class="whiteBg" id="" size="11" readonly></span>
            <div style="clear:both;"></div>
        </div>
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {        
            $('#ven012DividirPedidoBtn').unbind('click').bind('click', function(data) {
                $.getJSON('/ven/ven012/grid_func-ajax/ven012VerificaItensDividir/?T005_Id=' + encodeURIComponent('{$T005_Id}') + '&ajax=true&callback=?', function(request) {
                    if(request.code){
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/ven/ven012/content/ven012ContentSelecionarEmpresa/', '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}'), [400,200]);
                    }else{
                        var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                        dialogConfirm('Erro', request.data, \$buttons);
                    }
                });
            });
	    
	    $('#ven012MudarFilial').unbind('click').bind('click', function(data) {
            $.getJSON('/ven/ven012/grid_func-ajax/ven012VerificaItensMudarFIlial/?T005_Id=' + encodeURIComponent('{$T005_Id}') + '&ajax=true&callback=?', function(request) {
                if(request.code){
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/ven/ven012/content/ven012ContentSelecionarEmpresa/', '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&mudarFilial=true', [400,200]);
                }else{
                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                    dialogConfirm('Erro', 'Não há itens neste pedido', \$buttons);
                }
            });
        });
            
        $('#ven012GerarNFMarcados').unbind('click').bind('click', function(data) {
            $.getJSON('/ven/ven012/grid_func-ajax/ven012VerificaItensGerarNF/?T005_Id=' + encodeURIComponent('{$T005_Id}') + '&ajax=true&callback=?', function(request) {
                if(request.code){
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/ven/ven012/content/ven012ContentSelecionarEmpresa/', '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&gerarNF=true', [400,200]);
                }else{
                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                    dialogConfirm('Erro', 'Não há itens neste pedido', \$buttons);
                }
            });
        });
        
        $('#ven012ConcluirNF').unbind('click').bind('click', function(data) {
            var \$buttons = {
				"Sim": function() {
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/ven/ven012/grid_func-ajax/ven012ConcluirPedido/?T005_Id=' + encodeURIComponent('{$T005_Id}') + '&ajax=true&callback=?', function(request) {
						if (request.code) {
 							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    dialogConfirm('Ok', request.data, \$buttons);
						    divRefresh('{$g['divId']}', true);
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
			dialogConfirm('Conclusão', 'Você tem certeza?', \$buttons);
        });
		$('#ven012DuplicarPedido').unbind('click').bind('click', function(data) {
            var \$buttons = {
				"Sim": function() {
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/ven/ven012/grid_func-ajax/ven012DuplicarPedido/?T005_Id=' + encodeURIComponent('{$T005_Id}') + '&ajax=true&callback=?', function(request) {
						if (request.code) {
 							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    dialogConfirm('Ok', request.data, \$buttons);
						    divRefresh('{$g['divId']}', true);
						    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + request.T005_Id_Novo, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(request.T005_Id_Novo) + '&tabela=T005', [1000,615]);            
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
			dialogConfirm('Duplicar Pedido', 'Você tem certeza que deseja duplicar o pedido?', \$buttons);
        });
} </script></div>
EOT;
    break;

    case 'ven012TotalizadoresDividirEmpresa':
	    
	    // Select
		// Select
		$sql = <<<EOT
		SELECT 
			*
		FROM 
			D024
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		WHERE
			(D024_Id='{$r_acaoId}')
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
        $D024_Id=$r_acaoId;
    break;
}
?>