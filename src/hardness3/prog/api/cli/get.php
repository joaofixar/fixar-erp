<?php
namespace hardness;
//funções gerais
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();
//funções pertinentes a api de clientes
require_once('bibliotecas/classes/API002.php');
$API002 = new API002();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'produtos':
		global $g;
		$where="";

		$retorno = $API002->auth();

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

	case 'estoqueQuantidade':
		global $g;
		$where="";

		$retorno = $API002->auth();

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'estoque_codigo_produto'),
            array('colunaValor' => 'sum(D009_Quantidade_Estoque)', 'colunaTitulo' => 'estoque_quantidade'),
		);

		//buscando variaveis
		$D178_C004_Id=$API001->getC004();
		// $D001_Codigo_Produto = $API001->retornarIdUrl($_SERVER['REQUEST_URI']);

		//montando clausula where
		$where = $API001->montarWhere($where,"D009_C004_Id","=",$D178_C004_Id);
		$where = $API001->montarWhere($where,"D001_Codigo_Produto","=",$D001_Codigo_Produto);
		$where = $API001->montarWhere($where,"D001_Flag_Ativo","=","S");

		// Definição da SQL
		$from = 'D001';

		$extra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id
			LEFT JOIN C004 ON D009_C004_Id=C004_Id
			{$where}
			GROUP BY D001_Codigo_Produto
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

	case 'estoqueDisponibilidade':
		global $g;
		$where="";

		$retorno = $API002->auth();

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'estoque_codigo_produto'),
            // array('colunaValor' => 'sum(D009_Quantidade_Estoque)', 'colunaTitulo' => 'estoque_disponibilidade_quantidade', 'callback' => 'tipoEstoque'),
            array('colunaValor' => 'API_estoque_disponibilidade(sum(D009_Quantidade_Estoque))', 'colunaTitulo' => 'estoque_disponibilidade'),
		);

		//buscando variaveis
		$D178_C004_Id=$API001->getC004();
		// $D001_Codigo_Produto = $API001->retornarIdUrl($_SERVER['REQUEST_URI']);

		//montando clausula where
		$where = $API001->montarWhere($where,"D009_C004_Id","=",$D178_C004_Id);
		$where = $API001->montarWhere($where,"D001_Codigo_Produto","=",$D001_Codigo_Produto);
		$where = $API001->montarWhere($where,"D001_Flag_Ativo","=","S");

		// Definição da SQL
		$from = 'D001';

		$extra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id
			LEFT JOIN C004 ON D009_C004_Id=C004_Id
			{$where}
			GROUP BY D001_Codigo_Produto
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