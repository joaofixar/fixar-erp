<?php
namespace hardness;
//funções gerais e funções pertinentes a api do mobile
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'produtos':
		global $g;
		$where="";

		$retorno = $API001->auth($_GET['API_AUTH']);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'produto_id'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'produto_codigo'),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'produto_codigo_ean'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'produto_descricao'),
            array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'produto_peso_liquido'),
            array('colunaValor' => 'D001_Peso_Unitario_Bruto', 'colunaTitulo' => 'produto_peso_bruto'),
			array('colunaValor' => 'D003_Imagem', 'colunaTitulo' => 'produto_url_imagem', 'callback' => 'mostrarImagemB2B'),
			array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'produto_grupo'),
        );

		//buscando variaveis
		// $D001_Codigo_Produtox = $API001->tratarUrl($_SERVER['REQUEST_URI'],"D001");

		//montando clausula where
		$where = $API001->montarWhere($where,"D001_Codigo_Produto","=",$D001_Codigo_Produto);
		$where = $API001->montarWhere($where,"D001_Flag_Ativo","=","S");

		//  Definição da SQL
		$from = 'D001';

		$extra = <<<EOT
			LEFT JOIN D003 ON D003_Id=D001_D003_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,30000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		$array = array();
		$array['produtos']=$geradoDados;
		echo json_encode($array, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'produtoEmpresa':
		global $g;
		$where="";

		$retorno = $API001->auth($_GET['API_AUTH']);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$C004_Id = $API001->getC004();
		$API001->validaEmpresa($C004_Id);

		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'produto_id_cabecalho'),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'produto_id_empresa'),
			array('colunaValor' => 'T269_Id', 'colunaTitulo' => 'produto_id_carrinho'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'produto_codigo'),
            array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'produto_codigo_ean'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'produto_descricao'),
            array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'produto_peso_liquido'),
            array('colunaValor' => 'D001_Peso_Unitario_Bruto', 'colunaTitulo' => 'produto_peso_bruto'),
			array('colunaValor' => 'D003_Imagem', 'colunaTitulo' => 'produto_url_imagem', 'callback' => 'mostrarImagemB2B'),
			array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'produto_grupo'),
			array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'produto_preco_1', 'callback' => 'retornaPrecoVendaB2B'),
			array('colunaValor' => 'D009_Quantidade_Estoque_B2B', 'colunaTitulo' => 'produto_quantidade_estoque_B2B'),
			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'produto_quantidade_estoque'),
        );

		//buscando variaveis
		$D001_Descricao_Produto = urldecode($_GET['produto_descricao']);
		$D001_Flag_Ecommerce = $API001->getEcommerce();
		$pagina = (isset($_GET['pagina'])) ? (int) $_GET['pagina'] : 1;
		$quantidade = (isset($_GET['quantidade'])) ? (int) $_GET['quantidade'] : 12;
		$inicio = ($quantidade * $pagina) - $quantidade;

		//montando clausula where
		$where = $API001->montarWhere($where,"D001_Flag_Ativo","=","S");
		$where = $API001->montarWhere($where,"D009_C004_Id","=",$C004_Id);
		$where = $API001->montarWhere($where,"D001_Flag_Ecommerce","=",$D001_Flag_Ecommerce);
		$where = $API001->montarWhere($where,"D001_Descricao_Produto","LIKE","%".$D001_Descricao_Produto."%");

		//  Definição da SQL
		$from = 'D001';

		$extra = <<<EOT
			LEFT JOIN D003 ON D003_Id=D001_D003_Id
			LEFT JOIN D049 ON D001_Id=D049_D001_Id 
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D009 ON D049_Id=D009_D049_Id
			LEFT JOIN T269 ON T269_D009_Id=D009_Id AND T269_D024_Id='{$g['clienteAtual']}' AND T269_D013_Id='{$g['clienteAtualContato']}'
			{$where}
			order by if(D009_Quantidade_Estoque_B2B>0,0,1),replace(D001_Descricao_Produto,D003_Nome_Linha,'')
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,"$quantidade.$inicio",false,false,false,30000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		$array = array();
		$array['produtos']=$geradoDados;
		echo json_encode($array, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'configuracoesC031':
		global $g;
		$where="";

		$retorno = $API001->auth($_GET['API_AUTH']);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
            array('colunaValor' => 'C031_Id', 'colunaTitulo' => 'configuracao_Id'),
            array('colunaValor' => 'C031_Campo', 'colunaTitulo' => 'configuracao_campo'),
            array('colunaValor' => 'C031_Valor_Padrao', 'colunaTitulo' => 'configuracao_valor_padrao'),
            array('colunaValor' => 'C031_Valor', 'colunaTitulo' => 'configuracao_valor_atual'),
            array('colunaValor' => 'C031_Observacao', 'colunaTitulo' => 'configuracao_observacao'),
		);

		//buscando variaveis
		$C031_C004_Id=$API001->getC004();
		$C031_Campo = urldecode($_GET['configuracao_campo']);

		//montando clausula where
		$where = $API001->montarWhere($where,"C031_C004_Id","=",$C031_C004_Id);
		$where = $API001->montarWhere($where,"C031_Campo","LIKE","%".$C031_Campo."%");

		// Definição da SQL
		$from = 'C031';

		$extra = <<<EOT
			{$where}
EOT;

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,30000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}
		$array = array();
		$array['configuracoes'] = $geradoDados;
		echo json_encode($array, JSON_UNESCAPED_UNICODE);

		die();
	break;

	case 'empresasContato':
		global $g;
		$where="";

		$retorno = $API001->auth($_GET['API_AUTH']);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'cliente_codigo'),
			array('colunaValor' => 'D024_C004_Id', 'colunaTitulo' => 'cliente_empresa_codigo'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'cliente_nome'),
            array('colunaValor' => 'D024_Cnpj', 'colunaTitulo' => 'cliente_Cnpj'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'cliente_UF'),
            array('colunaValor' => 'D013_Id', 'colunaTitulo' => 'cliente_id_contato'),
        );

		//buscando variaveis
		// $D001_Codigo_Produto = $API001->retornarIdUrl($_SERVER['REQUEST_URI']);
		$D013_E_Mail=$API001->getEmailApp();
		
		//montando clausula where
		$where = $API001->montarWhere($where,"D013_E_Mail","=",$D013_E_Mail);
		$where = $API001->montarWhere($where,"D013_Flag_Ecommerce","=","S");

		//  Definição da SQL
		$from = 'D024';

		$extra = <<<EOT
			LEFT JOIN D013 ON D013_D024_Id=D024_Id
			LEFT JOIN D018 ON D024_D018_Id=D018_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,30000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		$array = array();
		$array['empresas']=$geradoDados;
		echo json_encode($array, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'carrinho':
		global $g;
		$where="";

		$retorno = $API001->auth($_GET['API_AUTH']);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'T269_Id', 'colunaTitulo' => 'carrinho_produto_id'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'produto_id_cabecalho'),
			array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'produto_codigo_ean'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'produto_descricao'),
			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'carrinho_produto_quantidade_estoque'),
			array('colunaValor' => 'D003_Imagem', 'colunaTitulo' => 'produto_url_imagem', 'callback' => 'mostrarImagemB2B'),
			array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'produto_grupo'),
			array('colunaValor' => 'T269_Preco', 'colunaTitulo' => 'carrinho_produto_preco', 'callback' => 'retornaPrecoCarrinhoB2B'),
			array('colunaValor' => 'T269_Quantidade', 'colunaTitulo' => 'carrinho_produto_quantidade'),
			array('colunaValor' => 'T269_Preco_Total', 'colunaTitulo' => 'carrinho_preco_total'),
		);

		//buscando variaveis
		$D024_Id = $API001->getD024_Id();
		$D013_Id = $API001->getD013_Id();

		//montando clausula where
		$where = $API001->montarWhere($where, "T269_D024_Id", "=", $g['clienteAtual']);
		$where = $API001->montarWhere($where, "T269_D013_Id", "=", $g['clienteAtualContato']);

		//  Definição da SQL
		$from = 'T269';

		$extra = <<<EOT
			LEFT JOIN D009 ON D009_Id=T269_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			LEFT JOIN D003 ON D003_Id=D001_D003_Id 
			{$where}
			GROUP BY D001_Id
			ORDER BY REPLACE(D001_Descricao_Produto,D003_Nome_Linha,'')
EOT;

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,30000,true);
		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		$array = array();
		$array['carrinho']=$geradoDados;
		
		// Somatória do subtotal e quantidade
		$T269 = mysql_query("SELECT SUM(T269_Preco_Total) AS subtotal,
		 							COUNT(*) as quantidade
							FROM T269
							WHERE T269_D024_Id='{$g['clienteAtual']}' AND T269_D013_Id='{$g['clienteAtualContato']}'");
		$mT269 = mysql_fetch_array($T269);

		$array['quantidade_total'] = $mT269['quantidade'];
		$array['valor_total'] = $mT269['subtotal'];

		echo json_encode($array, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'validaLogin':		 
		global $g;

		$retorno = $API001->auth($_GET['API_AUTH']);
		
		if (!is_array($retorno)) {
			header('HTTP/1.0 200 Ok');
			$retorno = array();
			$retorno['code'] = 200;
			$retorno['mensagem'] = "Login válido!";
		}
		$array = array();
		$array['validacao'] = $retorno;
		echo json_encode($array, JSON_UNESCAPED_UNICODE);
		die();
	break;

	case 'quantidadeCarrinho':
		global $g;
		$where="";

		$retorno = $API001->auth($_GET['API_AUTH']);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'COUNT(*)', 'colunaTitulo' => 'carrinho_quantidade_total'),
        );

		//buscando variaveis
		$D024_Id = $API001->getD024_Id();
		$D013_Id = $API001->getD013_Id();
		
		//montando clausula where
		$where = $API001->montarWhere($where, "T269_D024_Id", "=", $g['clienteAtual']);
		$where = $API001->montarWhere($where, "T269_D013_Id", "=", $g['clienteAtualContato']);

		//  Definição da SQL
		$from = 'T269';

		$extra = <<<EOT
			LEFT JOIN D001 ON D001_Id=T269_D001_Id 
			LEFT JOIN D003 ON D003_Id=D001_D003_Id 
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,30000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'valorTotalCarrinho':
		global $g;
		$where="";

		$retorno = $API001->auth($_GET['API_AUTH']);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'sum(T269_Preco_Total)', 'colunaTitulo' => 'carrinho_valor_total'),
        );

		//buscando variaveis
		$D024_Id = $API001->getD024_Id();
		$D013_Id = $API001->getD013_Id();
		
		//montando clausula where
		$where = $API001->montarWhere($where, "T269_D024_Id", "=", $g['clienteAtual']);
		$where = $API001->montarWhere($where, "T269_D013_Id", "=", $g['clienteAtualContato']);

		//  Definição da SQL
		$from = 'T269';

		$extra = <<<EOT
			LEFT JOIN D001 ON D001_Id=T269_D001_Id 
			LEFT JOIN D003 ON D003_Id=D001_D003_Id 
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,30000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	default:
		$API001->retorno404();
}