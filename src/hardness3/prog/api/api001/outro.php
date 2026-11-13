<?php
namespace hardness;

require_once("bibliotecas/classes/API001PolicyAdmin.php");

if (!function_exists(__NAMESPACE__ . '\\API001PolicyJsonResponse')) {
function API001PolicyJsonResponse(bool $success, string $message = '', array $data = array(), int $statusCode = 200): void
	{
		if (!headers_sent()) {
			http_response_code($statusCode);
			header('Content-Type: application/json; charset=utf-8');
		}

		echo json_encode(array(
			'success' => $success,
			'message' => $message,
			'data' => $data,
		), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		exit;
	}
}

/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Transacao
	 */
	case 'API001OutroLinksApi':
        global $g;
        
        $sqlC007 = mysql_query("SELECT C007_Id,C007_Login,C007_Senha,md5(concat(C007_Login,':',C007_Senha)) as AUTH FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $C007 = mysql_fetch_assoc($sqlC007);
        $dominio = $_SERVER['HTTP_HOST'];
        $autenticacao = $C007['AUTH'];		
		$dataInicial = date('Y') . '-01-01';
		$dataFinal = date('Y') . '-12-31';

        $metodos = array(
            array("metodo" => "Clientes","link" =>"{$dominio}/api/rel/clientes?API_AUTH={$autenticacao}"),
            array("metodo" => "Empresas","link" =>"{$dominio}/api/rel/empresas?API_AUTH={$autenticacao}"),
            array("metodo" => "Transportadoras","link" =>"{$dominio}/api/rel/transportadoras?API_AUTH={$autenticacao}"),
            array("metodo" => "Produtos","link" =>"{$dominio}/api/rel/produtos?API_AUTH={$autenticacao}"),
            array("metodo" => "Portadoras à Receber","link" =>"{$dominio}/api/rel/portadoresReceber?API_AUTH={$autenticacao}"),
            array("metodo" => "Contas","link" =>"{$dominio}/api/rel/contas?API_AUTH={$autenticacao}"),
            array("metodo" => "Centro de Custo","link" =>"{$dominio}/api/rel/centroDeCustos?API_AUTH={$autenticacao}"),
            array("metodo" => "Portadores à Pagar","link" =>"{$dominio}/api/rel/portadoresPagar?API_AUTH={$autenticacao}"),
            array("metodo" => "Orçamentos","link" =>"{$dominio}/api/rel/orcamentos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Produtos Orçamentos","link" =>"{$dominio}/api/rel/orcamentosProdutos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Pedidos","link" =>"{$dominio}/api/rel/pedidos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            // array("metodo" => "Pedidos Detalhes","link" =>"{$dominio}/api/rel/pedidosDetalhes?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Pedidos Expedição","link" =>"{$dominio}/api/rel/pedidosExpedicao?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Produtos Pedidos","link" =>"{$dominio}/api/rel/pedidosProdutos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "NF's Saida","link" =>"{$dominio}/api/rel/notasSaida?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "NF's Saida Detalhes","link" =>"{$dominio}/api/rel/notasSaidaDetalhes?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "NF's Saida Produtos","link" =>"{$dominio}/api/rel/notasSaidaProdutos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Cotações","link" =>"{$dominio}/api/rel/cotacoes?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Produtos Cotações","link" =>"{$dominio}/api/rel/cotacoesProdutos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Ordens de Compra","link" =>"{$dominio}/api/rel/ordensDeCompra?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Produtos OC","link" =>"{$dominio}/api/rel/ordensDeCompraProdutos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Conhecimentos","link" =>"{$dominio}/api/rel/conhecimentos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
			array("metodo" => "NF's Entrada","link" =>"{$dominio}/api/rel/notasEntrada?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
			array("metodo" => "NF's Entrada Produtos","link" =>"{$dominio}/api/rel/notasEntradaProdutos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
			array("metodo" => "NF's Entrada Tributos","link" =>"{$dominio}/api/rel/notasEntradaProdutosTributos?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Contas à Receber","link" =>"{$dominio}/api/rel/contasReceber?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Contas à Receber Detalhes","link" =>"{$dominio}/api/rel/contasReceberDetalhes?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Contas à Pagar","link" =>"{$dominio}/api/rel/contasPagar?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Contas à Pagar Detalhes","link" =>"{$dominio}/api/rel/contasPagarDetalhes?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Lançamentos Bancários","link" =>"{$dominio}/api/rel/lancamentosBancarios?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Tipos CFOP","link" =>"{$dominio}/api/rel/tiposCFOP?API_AUTH={$autenticacao}"),
            array("metodo" => "CFOP's","link" =>"{$dominio}/api/rel/CFOP?API_AUTH={$autenticacao}"),
            array("metodo" => "Usuários","link" =>"{$dominio}/api/rel/usuarios?API_AUTH={$autenticacao}"),
            array("metodo" => "Vendedores Internos","link" =>"{$dominio}/api/rel/vendedoresInternos?API_AUTH={$autenticacao}"),
            array("metodo" => "Supervisores","link" =>"{$dominio}/api/rel/supervisores?API_AUTH={$autenticacao}"),
            array("metodo" => "Vendedores Externos","link" =>"{$dominio}/api/rel/vendedoresExternos?API_AUTH={$autenticacao}"),
            array("metodo" => "Supervisores 2","link" =>"{$dominio}/api/rel/supervisores2?API_AUTH={$autenticacao}"),
            array("metodo" => "Vendedores Substitutos","link" =>"{$dominio}/api/rel/vendedoresSubstitutos?API_AUTH={$autenticacao}"),
            array("metodo" => "Compradores Internos","link" =>"{$dominio}/api/rel/compradoresInternos?API_AUTH={$autenticacao}"),
            array("metodo" => "Compradores Externos","link" =>"{$dominio}/api/rel/compradoresExternos?API_AUTH={$autenticacao}"),
            array("metodo" => "Estoque","link" =>"{$dominio}/api/rel/estoque?API_AUTH={$autenticacao}"),
			array("metodo" => "Estoque Terceiros","link" =>"{$dominio}/api/rel/estoque_terceiros?API_AUTH={$autenticacao}"),
            array("metodo" => "RMA's","link" =>"{$dominio}/api/rel/RMA?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Custos RMA","link" =>"{$dominio}/api/rel/custoRMA?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Atividades","link" =>"{$dominio}/api/rel/atividades?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
            array("metodo" => "Assuntos das atividades","link" =>"{$dominio}/api/rel/assuntosAtividades?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
			array("metodo" => "Produtos Importação","link" =>"{$dominio}/api/rel/produtoImportacao?API_AUTH={$autenticacao}&DATAINICIAL={$dataInicial}&DATAFINAL={$dataFinal}"),
        );


    echo <<<EOT
		<style>
			table a:link {
		    color: #666;
		    font-weight: bold;
		    text-decoration:none;
			}
			table#t1 a:visited {
			    color: #999999;
			    font-weight:bold;
			    text-decoration:none;
			}
			table#t1 a:active,
			table#t1 a:hover {
			    color: #bd5a35;
			    text-decoration:underline;
			}
			table#t1 {
			    width: 98%;
			    font-family:Arial, Helvetica, sans-serif;
			    color:#666;
			    font-size:12px;
			    background:#eaebec;
			    margin:10px;
			    border:#ccc 1px solid;

			    -moz-border-radius:3px;
			    -webkit-border-radius:3px;
			    border-radius:3px;
			}
			table#t1 th {
			    padding:21px 25px 22px 25px;
			    border-top:1px solid #fafafa;
			    border-bottom:1px solid #e0e0e0;

			    background: #ededed;
			    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
			    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
			}
			table#t1 th:first-child{
			    text-align: left;
			    padding-left:20px;
			}
			table#t1 tr:first-child th:first-child{
			    -moz-border-radius-topleft:3px;
			    -webkit-border-top-left-radius:3px;
			    border-top-left-radius:3px;
			}
			table#t1 tr:first-child th:last-child{
			    -moz-border-radius-topright:3px;
			    -webkit-border-top-right-radius:3px;
			    border-top-right-radius:3px;
			}
			table#t1 tr{
			    text-align: left;
			    padding-left:10px;
			}
			table#t1 tr td:first-child{
			    text-align: left;
			    padding-left:20px;
			    border-left: 0;
			}
			table#t1 tr td {
			    padding:10px;
			    border-top: 1px solid #ffffff;
			    border-bottom:1px solid #e0e0e0;
			    border-left: 1px solid #e0e0e0;

			    background: #fafafa;
			    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
			    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
			}
			table#t1 tr.even td{
			    background: #f6f6f6;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
			    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
			}
			table#t1 tr:last-child td{
			    border-bottom:0;
			}
			table#t1 tr:last-child td:first-child{
			    -moz-border-radius-bottomleft:3px;
			    -webkit-border-bottom-left-radius:3px;
			    border-bottom-left-radius:3px;
			}
			table#t1 tr:last-child td:last-child{
			    -moz-border-radius-bottomright:3px;
			    -webkit-border-bottom-right-radius:3px;
			    border-bottom-right-radius:3px;
			}
			table#t1 tr:hover td{
			    background: #f2f2f2;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
			    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);
			}
		</style>

		<table cellspacing='0' id="t1">
		    <tr>
		        <th>Entidade</th>
		        <th style="width:0px;"></th>
		        <th>Rota GET</th>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><font color='red'>***Troque os valores de DATAINICIAL e DATAFINAL pela data escolhida no formato AAAA-MM-DD.</font></td>
			</tr>

EOT;
		$count=0;
        foreach($metodos as $key=>$metodo){
			$count++;
			echo <<<EOT
				<tr  style="cursor:pointer;" onclick="copyToClipboard('{$count}');">
					<td>{$metodo['metodo']}</td>
					<td id="{$count}x"><tag><u>Copiar</u></tag></td>
					<td id="{$count}"><rota{$count}>{$metodo['link']}</rota{$count}></td>
                </tr>
EOT;
        }

        echo <<<EOT
        </table>
EOT;

	break;

	case 'API001PolicyStatus':
	case 'API001PolicyAccessStatus':
	case 'API001PolicyUnlock':
	case 'API001PolicyLoadTable':
	case 'API001PolicyPreview':
	case 'API001PolicySave':
	case 'API001PolicyRemoveTable':
	case 'API001TokenStatus':
	case 'API001TokenCreate':
	case 'API001TokenSetStatus':
		$policyAdmin = new API001PolicyAdmin();
		$handler = new \RestApi\Admin\PolicyAdminAjaxHandler();
		$response = $handler->handle($g['r'][3], $policyAdmin, $_REQUEST);
		API001PolicyJsonResponse(
			$response['success'],
			$response['message'],
			$response['data'],
			$response['status_code']
		);
	break;

	case 'API001ExecutarProcesso' :

		// Classes
		require_once('bibliotecas/classes/API001.php');
		$API001 = new API001();
		
		require_once('bibliotecas/funcoesGerais.php');		

		$C033 = mysql_query("SELECT C033_Login,
   								    C033_Senha,
								    C033_Token,
									C033_Url
							   FROM C033
							  WHERE C033_Id = '{$r_C033_Id}'");
		$mC033 = mysql_fetch_assoc($C033);

		$login = $mC033['C033_Login'];
		$senha = $mC033['C033_Senha'];
		$token = $mC033['C033_Token'];
		$url   = $mC033['C033_Url'];

		$C037 = mysql_query("SELECT C037_Id, 
		                            C037_Nome, 
									C037_Codigo
							   FROM C037
							  WHERE C037_Id = '{$r_acaoId}'");
		$mC037 = mysql_fetch_assoc($C037);

		$codigo = $mC037['C037_Codigo'];
		$codigo = str_replace("<?php","",$codigo);

		if(!empty($r_requisicaoPura))
		{
			eval($codigo);
		}
		else
		{
			echo "<p style='font-size:13px; padding:10px'>";
			echo eval($codigo);
			echo "</p>";
		}


	break;

}

if(empty($r_requisicaoPura))
{
	echo <<<EOT
	<script>
	function copyToClipboard(element) 
	{
		var temp = $("<input>");
		$("body").append(temp);
		temp.val($('rota' + element).text()).select();
		document.execCommand("copy");
		temp.remove();
		$('tag').html('<u>Copiar</u>');
		$('#'+element+'x').html('<tag><u>Copiar</u><br /><font style="color:green">copiado</font></tag>');
	}
	</script>
	EOT;
}


