<?php
namespace hardness;
/**
  * Processamento de ações AJAX
 */
$buscaProduto = $_GET['buscaProduto'];
$D009_Id = $_GET['D009_Id'];
$carrinho = $_GET['carrinho'];
$excluir = $_GET['excluir'];
$quantidade = $_GET['quantidade'];
$T269_Id = $_GET['T269_Id'];
$preco = $_GET['preco'];
$novoPreco = $_GET['novoPreco'];
$observacaoComercial = $_GET['observacaoComercial'];

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'incluir':
		global $g;

		$quantidade = $g['C031']['QuantidadePadraoCarrinho'];	

		$T269 = mysql_query("select T269_Id from T269 where T269_D009_Id='{$D009_Id}' and T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		if (mysql_num_rows($T269)<=0){
			mysql_query("insert into T269 (T269_D009_Id,T269_Quantidade,T269_D024_Id,T269_C007_Id,T269_D013_Id,T269_Preco,T269_Preco_Antigo) values ('{$D009_Id}','{$quantidade}','{$g['clienteAtual']}','{$g['clienteAtualVendedor']}','{$g['clienteAtualContato']}','{$preco}','{$preco}')");
		}
		$total = mysql_query("select count(*) from T269 where T269_D024_Id='{$g['clienteAtual']}' AND T269_D013_Id='{$g['clienteAtualContato']}'"); 
		$mTotal = mysql_fetch_array($total);
		$resposta['code'] = true;
		$resposta['data'] = $mTotal[0];
	break;

	case 'excluir':
		global $g;
		mysql_query("delete from T269 where T269_Id={$excluir}");
		$total = mysql_query("select count(*) from T269 where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		$mTotal = mysql_fetch_array($total);

		$T269 = mysql_query("select sum(T269_Preco_Total) as subtotal from T269 where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
		$mT269 = mysql_fetch_array($T269);
		$subTotal = gCorrigeNumero($mT269['subtotal']);

		$erro = mysql_error();
		if(empty($erro)){
			$resposta['code'] = true;
			$resposta['data']['carrinho'] = $mTotal[0];
			$resposta['data']['mensagem'] = "Quantidade gravada com sucesso!";
			$resposta['data']['subTotal'] ="R$ ". $subTotal;
			$resposta['data']['Total'] ="R$ ". $subTotal;			
		}else{
			$resposta['code'] = false;
			$resposta['data']['mensagem'] = "Erro ao gravar quantidade!<br>" . $erro;
			$resposta['data']['subTotal'] = "";
			$resposta['data']['Total'] = "";
		}		

	break;

	case 'gravarQuantidade':
		global $g;
		mysql_query("update T269 set T269_Quantidade = '{$quantidade}' where T269_Id='{$T269_Id}'");

		$T269 = mysql_query("select T269_Preco_Total as subtotal from T269 where T269_Id='{$T269_Id}'");
		$mT269 = mysql_fetch_array($T269);
		$subTotalUnit = gCorrigeNumero($mT269['subtotal']);

		$T269 = mysql_query("select sum(T269_Preco_Total) as subtotal from T269 left join D009 on D009_Id=T269_D009_Id where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'	");
		$mT269 = mysql_fetch_array($T269);
		$subTotal = gCorrigeNumero($mT269['subtotal']);

		$erro = mysql_error();
		if(empty($erro)){
			$resposta['code'] = true;
			$resposta['data']['mensagem'] = "Quantidade gravada com sucesso!";
			$resposta['data']['subTotalUnit'] ="R$ ". $subTotalUnit;
			$resposta['data']['subTotal'] ="R$ ". $subTotal;
			$resposta['data']['Total'] ="R$ ". $subTotal;
		}else{
			$resposta['code'] = false;
			$resposta['data']['mensagem'] = "Erro ao gravar quantidade!<br>" . $erro;
			$resposta['data']['subTotalUnit'] = "";
			$resposta['data']['subTotal'] = "";
			$resposta['data']['Total'] = "";
		}	

	break;

	case 'alterarPreco':
		global $g;

		if(preg_match("/[a-zA-Z]/",$novoPreco)=="1"){
			$resposta['code'] = false;
			$resposta['data']['mensagem'] = "Preço inválido";
			$resposta['data']['subTotal'] = "";
			$resposta['data']['precoNovo'] = '';
		}else{
			$novoPreco = gCorrigeNumeroInverte($novoPreco);
			$resposta['code'] = false;
			$resposta['data']['mensagem'] = "Preço válido.";
			mysql_query("UPDATE T269 SET T269_Preco = '{$novoPreco}' WHERE T269_Id='{$T269_Id}'");

			$T269 = mysql_query("SELECT T269_Preco_Total AS subtotal,T269_Preco FROM T269 WHERE T269_Id='{$T269_Id}'");
			$mT269 = mysql_fetch_array($T269);
			$subTotalUnit = gCorrigeNumero($mT269['subtotal']);
			$novoPreco = gCorrigeNumero($mT269['T269_Preco'],2);

			$T269 = mysql_query("SELECT SUM(T269_Preco_Total) AS subtotal FROM T269 left join D009 on D009_Id=T269_D009_Id WHERE T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'	");
			$mT269 = mysql_fetch_array($T269);
			$subTotal = gCorrigeNumero($mT269['subtotal']);

			$erro = mysql_error();
			if(empty($erro)){
				$resposta['code'] = true;
				$resposta['data']['mensagem'] = "Preço alterado com sucesso!";
				$resposta['data']['subTotalUnit'] ="R$ ". $subTotalUnit;
				$resposta['data']['subTotal'] ="R$ ". $subTotal;
				$resposta['data']['Total'] ="R$ ". $subTotal;
				$resposta['data']['precoNovo'] = $novoPreco;
			}else{
				$resposta['code'] = false;
				$resposta['data']['mensagem'] = "Erro ao gravar preço!<br>" . $erro;
				$resposta['data']['subTotalUnit'] = "";
				$resposta['data']['subTotal'] = "";
				$resposta['data']['Total'] = "";
				$resposta['data']['precoNovo'] = '';
			}
		}			

	break;

	case 'gerarPedido':
		global $g;

		require_once('bibliotecas/classes/B2B001.php');
		$B2B001 = new B2B001();

		$informacoesExtras = array();
		$informacoesExtras = array('observacaoComercial'=>$observacaoComercial);

		//Verifica os pedidos
		$retorno = $B2B001->B2BGerarPedido($informacoesExtras);

		if ($retorno[0] === false) {
			$resposta['code'] = $retorno[0];
			$resposta['data'] = $retorno[1];
			$resposta['T005_Id'] = $retorno[2];
		}else if($retorno[0] === true){
			mysql_query("delete from T269 where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
			$resposta['code'] = $retorno[0];
			$resposta['data'] = $retorno[1];
			$resposta['T005_Id'] = $retorno[2];
		}
	break;

	case 'gerarOrcamento':
		global $g;

		require_once('bibliotecas/classes/B2B001.php');
		$B2B001 = new B2B001();

		$informacoesExtras = array();
		$informacoesExtras = array('observacaoComercial'=>$observacaoComercial);

		$retorno = $B2B001->B2BGerarOrcamento($informacoesExtras);

		if ($retorno[0] === false) {
			$resposta['code'] = $retorno[0];
			$resposta['data']['email'] = $retorno[1];
		}else if($retorno[0] === true){
			mysql_query("delete from T269 where T269_D024_Id='{$g['clienteAtual']}' and T269_D013_Id='{$g['clienteAtualContato']}'");
			$resposta['code'] = $retorno[0];
			$resposta['data'] = $retorno[1];
		}
	break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";



