<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'produtos':
		$T005_Id = $_REQUEST['T005_Id'];
		$sql = <<<EOT
			SELECT T006_Id, T006_Codigo_Produto, T006_Descricao_Produto, D082_Marca, D037_Unidade, T006_Quantidade, T006_Quantidade_Estoque, D009_Quantidade_OC(D009_Id) as OC, 
			T006_Quantidade_Separacao,
			D009_Id
			FROM T006 
			left join D009 on D009_Id=T006_D009_Id left join D049 on D049_Id=D009_D049_Id left join D082 on D082_Id=D049_D082_Id left join D001 on D049_D001_Id=D001_Id left join T005 on T006_T005_Id=T005_Id 
			left join D006 on D006_Id=T006_D006_Id left join D037 on D037_Id=D001_D037_Id left join D005 on D005_Id=D009_D005_Id(D009_Id) 
			WHERE ((1=1)) AND (T006_T005_Id='{$T005_Id}' and (T006_T006_Id is null or T006_T006_Id<=0) and (T006_Quantidade>0) and T006_Flag_Divergencia!='S') ORDER BY T006_Flag_Tipo_Produto, T006_D009_Id, T006_Id
EOT;
		$res = mysql_query($sql);
		$resposta['data'] .= <<<EOT
			<div id="content" style="margin: 5px;"></div>
			<div class="alerta">LEIA O CÓDIGO DOS PRODUTOS:</div>
			<table cellspacing="1" style="width: 100%;">
				<tr>
					<th>Loc.</th>
					<th>Prod.</th>
					<th>Desc.</th>
					<th>Qtd</th>
					<th>Sep.</th>
				</td>
EOT;
		while ($row = mysql_fetch_assoc($res)) {
			// Locação
			$sql = "SELECT D004_Local FROM T066 left join D004 on D004_Id=T066_D004_Id WHERE T066_D009_Id='{$row['D009_Id']}';";
			$res2 = mysql_query($sql);
			$row2 = mysql_fetch_assoc($res2);
			// 
			$row['T006_Quantidade'] = number_format($row['T006_Quantidade'], 0);
			$row['T006_Quantidade_Separacao'] = number_format($row['T006_Quantidade_Separacao'], 0);
			$resposta['data'] .= <<<EOT
					<tr class="selecionarPedido" id="T005_Id-{$row['T005_Id']}">
						<td style="text-align: center;">{$row2['D004_Local']}</td>
						<td>{$row['T006_Codigo_Produto']}</td>
						<td>{$row['T006_Descricao_Produto']}</td>
						<td>{$row['T006_Quantidade']}</td>
						<td>{$row['T006_Quantidade_Separacao']}</td>
					</td>
EOT;
		}
		$resposta['data'] .= <<<EOT
			</table>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#scan').unbind('keypress');
				$('#scan').bind('keypress', function(e) {
					if (e.keyCode == 13) {
    					modal('Verificando...');
    					$.getJSON('./separacaoPedido.php?opcao=busca&T005_Id={$T005_Id}&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
    						modalRemove();
    						$('#scan').val('');
							$('#content').empty().html(request.data);
						});
					}
				});
				// (não perder o foco do campo scan)
    			$('#scan').focus();
			} </script></div>
EOT;
	break;
	
	case 'pedidos':
			$sql = <<<EOT
				SELECT T005_Id, T005_Estoque_BOX, D024_Nome_Empresa, T005_Data_Emissao, T005_Data_Saida, if(T005_Flag_ACP='1','NOTA FISCAL',if(T005_Flag_ACP='3','ACP',
				if(T005_Flag_ACP='5','NOTA D1',if(T005_Flag_ACP='2','NÃO FATURAR',if(T005_Flag_ACP='6','NF-e',if(T005_Flag_ACP='7','NFS-e','CUPOM FISCAL')))))) Tipo_Documento, T005_Observacao_1, 
				T005_D022_Id, D022_Nome_Empresa, D006_Descricao_Comercial, D006_Codigo_CFOP, T005_Data_Hora_Almoxarifado, 
				Liberou.C007_Primeiro_Nome as Nome_Liberou, Vendedor.C007_Primeiro_Nome as Nome_Vendedor, D020_Nome_Cidade, 
				T005_Status_Pedido(T005_Flag_Status,2) T005_Status, D018_UF, T005_Flag_Status, T005_Flag_Reenviado_Almoxarifado, T005_D024_Id, T005_D022_Id, D036_Tipo_Transporte, T007_Numero_Nota_Fiscal, 
				vendedor.C007_Primeiro_Nome as Nome_Vendedor, Separou.C007_Primeiro_Nome as Nome_Separou, group_concat(T007_Numero_Nota_Fiscal) as T007_Numero_Nota_Fiscal, T005_Valor_Total, 
				T005_Prazos(T005_Id) Prazo, T005_Data_Emissao, T005_Data_Saida, C004_Emite_NF_Caixa 
				FROM T005 
				left join D024 on D024_Id=T005_D024_Id left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id left join D021 on D021_Id=T005_D021_Id left join D022 on D022_Id=T005_D022_Id left join D006 on D006_Id=T005_D006_Id left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno left join C004 on C004_Id=T005_C004_Id left join D036 on T005_D036_Id=D036_Id left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S' left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id left join C007 as Separou on Separou.C007_Id=T005_Usuario_Pecas 
				WHERE ((1=1)) AND (T005_C004_Id='{$g['empresaAtual']}' and T005_Flag_Status='2') GROUP BY T005_Id ORDER BY T005_Id
EOT;
			$res = mysql_query($sql);
			$resposta['data'] .= <<<EOT
				<div class="alerta">SELECIONE UM PEDIDO PARA SEPARAR:</div>
				<table cellspacing=1>
					<tr>
						<th>Pedido</th>
						<th>Cliente</th>
						<th>Emissão</th>
					</td>
EOT;
			while ($row = mysql_fetch_assoc($res)) {
				$row['T005_Data_Emissao'] = gCorrigeData($row['T005_Data_Emissao']);
				$resposta['data'] .= <<<EOT
						<tr class="selecionarPedido" id="T005_Id-{$row['T005_Id']}">
							<td style="text-align: center;">{$row['T005_Id']}</td>
							<td>{$row['D024_Nome_Empresa']}</td>
							<td>{$row['T005_Data_Emissao']}</td>
						</td>
EOT;
			}
			$resposta['data'] .= <<<EOT
				</table>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    				$('.selecionarPedido').unbind('click').bind('click', function(e) {
    					modal('Verificando...');
    					var id = $(this).attr('id');
    					id = id.replace('T005_Id-', '');
    					$.getJSON('./separacaoPedido.php?opcao=produtos&T005_Id=' + encodeURIComponent(id) + '&callback=?', function(request) {
    						modalRemove();
							$('#contentMenu').empty().html(request.data);
						});
    				});
			    } </script></div>
EOT;
	break;
	
	case 'busca':
		$T005_Id = $_REQUEST['T005_Id'];
		$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
		if (isset($_REQUEST['scan'])) {
			if ($T006_Id) {
					$_REQUEST['separar'] = mysql_real_escape_string(gCorrigeNumeroInverte($_REQUEST['separar']));
					$sql = "UPDATE T006 SET T006_Quantidade_Separacao = '{$_REQUEST['separar']}' WHERE T006_Id = '{$T006_Id}';";
					mysql_query($sql);
			}
			$sql = <<<EOT
			SELECT D001_Codigo_Produto, D001_Descricao_Produto, D009_Quantidade_Estoque, D082_Marca, T006_Quantidade, T006_Id, T006_Quantidade_Separacao FROM T066 
			left join D004 on D004_Id=T066_D004_Id 
			left join D009 on D009_Id=T066_D009_Id 
			LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			LEFT JOIN D082 ON D082_Id=D049_D082_Id 
			LEFT JOIN T006 ON T006_T005_Id = '{$T005_Id}' AND T006_D009_Id=D009_Id
			WHERE D004_Id = '{$_REQUEST['scan']}';
EOT;
			$res = mysql_query($sql);
			if (mysql_numrows($res) == 0) {
				$resposta['data'] = <<<EOT
					<strong>Código de produto não encontrado. ({$_REQUEST['scan']})</strong>
EOT;
			} else {
				$row = mysql_fetch_assoc($res);
				$row['T006_Quantidade'] = gCorrigeNumero($row['T006_Quantidade']);
				$row['T006_Quantidade_Separacao'] = gCorrigeNumero($row['T006_Quantidade_Separacao']);
				$resposta['data'] .= <<<EOT
					<div class="alerta">PRODUTO:</div>
					Código:<br /><strong>{$row['D001_Codigo_Produto']}</strong><br />
					Descrição:<br /><strong>{$row['D001_Descricao_Produto']}</strong><br />
					Marca:<br /><strong>{$row['D082_Marca']}</strong><br />
					Estoque:<br /><strong>{$row['D009_Quantidade_Estoque']}</strong><br />
EOT;
				if ($T006_Id) {
					$resposta['data'] .= <<<EOT
						Separado:<br /><strong>{$row['T006_Quantidade_Separacao']}</strong><br />
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#scan').focus();
					    } </script></div>
EOT;
				} else {
					$resposta['data'] .= <<<EOT
						<br />
						<input id="separacaoPedidoQtdProd" value="{$row['T006_Quantidade']}" type="text" />
						<input id="separacaoPedidoQtdProdSubmit" type="button" value="Separar">
						<input id="separacaoPedidoQtdProdId" type="hidden" value="{$row['T006_Id']}">
EOT;
					$resposta['data'] .= <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#separacaoPedidoQtdProd').bind('keypress', function(e) {
								if (e.keyCode == 13) {
									$('#separacaoPedidoQtdProdSubmit').trigger('click');
								}
							});
    						$('#separacaoPedidoQtdProdSubmit').unbind('click').bind('click', function() {
    				 			modal('Verificando...');
    							$.getJSON('./separacaoPedido.php?opcao=busca&T005_Id={$T005_Id}&scan=' + encodeURIComponent('{$_REQUEST['scan']}') + '&T006_Id=' + encodeURIComponent($('#separacaoPedidoQtdProdId').val()) + '&separar=' + encodeURIComponent($('#separacaoPedidoQtdProd').val()) + '&callback=?', function(request) {
    								modalRemove();
    								$('#scan').val('');
									$('#content').empty().html(request.data);
								});
								return false;
    						});
					    } </script></div>
EOT;
				}
			}
		} else {
			$resposta['data'] = "<strong>Código de produto não encontrado.</strong>";
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";