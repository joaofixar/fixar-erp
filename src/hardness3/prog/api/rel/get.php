<?php
namespace hardness;

//funções gerais
require_once('bibliotecas/classes/APIMobile.php');
$API001 = new API001();
//funções pertinentes a api de relatórios
require_once('bibliotecas/classes/API003.php');
$API003 = new API003();
global $r_generateAPI;
$r_generateAPI=true;

// Se tiver o banco espelhado configurado no confUsuario.php, faz a conexão com para buscar os dados da API
if(isset($confUsuarioEspelhado)){
	require_once('bibliotecas/conexaoBancoEspelhado.php');
} 

/**
 * Ações
 */
switch($g['r'][3]) {
  case 'estoque_terceiros':

    $retorno = $API003->auth($_GET['API_AUTH'], true);

    if(is_array($retorno)){
      echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
      die();
    }

    $grid = array(
      array('colunaValor' => 'd001.D001_Id',                 'colunaTitulo' => 'D001_Id'),
      array('colunaValor' => 'd009_C004_Id',                 'colunaTitulo' => 'D009_C004_Id'),
      array('colunaValor' => 't066.T066_Id',                 'colunaTitulo' => 'T066_Id'),
      array('colunaValor' => 'd001.D001_Codigo_Produto',     'colunaTitulo' => 'D001_Codigo_Produto'),
      array('colunaValor' => 'd001.D001_Descricao_Produto',  'colunaTitulo' => 'D001_Descricao_Produto'),
      array('colunaValor' => 'd009.D009_Id',                 'colunaTitulo' => 'D009_Id'),
      array('colunaValor' => 't066.T066_Quantidade_Estoque', 'colunaTitulo' => 'T066_Quantidade_Estoque'),
      array('colunaValor' => 't001.T001_D024_Id',            'colunaTitulo' => 'T001_D024_Id')
    );

    //  Definição da SQL
    $from = 'D004';

    $extra = "LEFT JOIN D146 ON D146_Id      = D004_D146_Id
              LEFT JOIN T066 ON T066_D004_Id = D004_Id
              LEFT JOIN t001 on T001_T066_Id = T066_Id
              LEFT JOIN D009 ON D009_Id      = T066_D009_Id
              LEFT JOIN D049 ON D049_Id      = D009_D049_Id
              LEFT JOIN D001 ON D001_Id      = D049_D001_Id
              WHERE T001_D024_Id                 <> 0
                AND T066.T066_Quantidade_Estoque > 0

              GROUP BY T066_Id, T001_D024_Id";

    list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 0.00, false, false, false, 45000, true);

    //validando se retornaram dados da query
    $semDados = $API001->tratarRetornoVazio($geradoDados);

    if(is_array($semDados)){
      echo json_encode($semDados, JSON_UNESCAPED_UNICODE); 
      die();
    }

    echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

    die();

    break;

	case 'clientes':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'codigo_Fornecedor'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'nome_cliente'),
			array('colunaValor' => 'D024_Flag_Pessoa_Fisica_Juridica', 'colunaTitulo' => 'tipo_pessoa_cliente'),
			array('colunaValor' => 'D024_Flag_Contribuinte', 'colunaTitulo' => 'tipo_contribuinte_cliente'),
			array('colunaValor' => 'D030_Nome_Pais', 'colunaTitulo' => 'pais_cliente'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF_cliente'),
			array('colunaValor' => 'D018_Estado', 'colunaTitulo' => 'estado_cliente'),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'cidade_cliente'),
			array('colunaValor' => 'D024_Bairro', 'colunaTitulo' => 'bairro_cliente'),
			array('colunaValor' => 'D024_Cep', 'colunaTitulo' => 'cep_cliente', 'callback' => 'tratarCEPCliente'),
			array('colunaValor' => 'D024_Numero', 'colunaTitulo' => 'endereco_numero_cliente'),			
			array('colunaValor' => 'D024_C007_Vendedor_Interno', 'colunaTitulo' => 'codigo_vendedor_interno'),			
			array('colunaValor' => 'D024_Flag_Fornecedor', 'colunaTitulo' => 'flag_fornecedor'),
			array('colunaValor' => 'D024_Flag_Cliente', 'colunaTitulo' => 'flag_cliente'),
			array('colunaValor' => 'D024_Flag_Ativo', 'colunaTitulo' => 'cadastro_ativo'),
			array('colunaValor' => 'REPLACE(REPLACE(REPLACE(D024_CNPJ,".",""),"/",""),"-","")', 'colunaTitulo' => 'numero_cnpj'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D024_Nome_Empresa","!=",' ');
		$where = $API001->montarWhere($where,"IFNULL(D024_D020_Id,0)",">","0");
		$where = $API001->montarWhere($where,"IFNULL(D024_D018_Id,0)",">","0");

		//  Definição da SQL
		$from = 'D024';

		$extra = <<<EOT
			LEFT JOIN D018 ON D018_Id = D024_D018_Id
			LEFT JOIN D020 ON D020_Id = D024_D020_Id
			LEFT JOIN D030 ON D030_Id = D024_D030_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'empresas':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'C004_Nome_Empresa', 'colunaTitulo' => 'nome_empresa'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'cidade_empresa'),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'UF_empresa'),
		);

		//  Definição da SQL
		$from = 'C004';

		$extra = <<<EOT
			LEFT JOIN D018 ON D018_Id = C004_D018_Id
			LEFT JOIN D020 ON D020_Id = C004_D020_Id
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'transportadoras':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D022_Id', 'colunaTitulo' => 'codigo_transportadora'),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'nome_transportadora'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'cidade_transportadora'),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'UF_transportadora'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D022_Nome_Empresa","!=",' ');
		$where = $API001->montarWhere($where,"IFNULL(D022_D020_Id,0)",">","0");
		$where = $API001->montarWhere($where,"IFNULL(D022_D018_Id,0)",">","0");

		//  Definição da SQL
		$from = 'D022';

		$extra = <<<EOT
			LEFT JOIN D018 ON D018_Id = D022_D018_Id
			LEFT JOIN D020 ON D020_Id = D022_D020_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'produtos':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'codigo_produto'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'descricao_produto'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'marca_produto'),
			array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'departamento_produto'),
			array('colunaValor' => 'D060_Descricao_Grupo', 'colunaTitulo' => 'setor_produto'),
			array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'grupo_produto'),
			array('colunaValor' => 'D002_Descricao_Produto', 'colunaTitulo' => 'sub_grupo_produto'),
			array('colunaValor' => 'if(D049_Data_Cadastro="0000-00-00","",D049_Data_Cadastro)', 'colunaTitulo' => 'data_cadastro_produto'),
			array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'peso_unitario_liquido'),
			array('colunaValor' => 'D001_Peso_Unitario_Bruto', 'colunaTitulo' => 'peso_unitario_bruto'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'unidade'),
			array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'quantidade_embalagem_compra'),
			array('colunaValor' => 'D001_Qte_Embalagem', 'colunaTitulo' => 'quantidade_embalagem_venda'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D001_Codigo_Produto","!=",' ');
		$where = $API001->montarWhere($where,"D001_Descricao_Produto","!="," ");
		$where = $API001->montarWhere($where,"D001_Flag_Ativo","=","S");

		//  Definição da SQL
		$from = 'D001';

		$extra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D037 ON D001_D037_Id = D037_Id
			LEFT JOIN D082 ON D082_id=D049_D082_Id
			LEFT JOIN C008 ON C008_Id=D001_C008_Id
			LEFT JOIN D060 ON D060_Id=D001_D060_Id
			LEFT JOIN D003 ON D003_Id=D001_D003_Id    
			LEFT JOIN D002 ON D002_Id=D001_D002_Id
			{$where}
			GROUP BY D001_Codigo_Produto
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'portadoresReceber':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D027_Id', 'colunaTitulo' => 'id_portador_receber'),
			array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'nome_portador_receber'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D027_Portador","!=",' ');

		//  Definição da SQL
		$from = 'D027';

		$extra = <<<EOT
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'contas':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D014_Id', 'colunaTitulo' => 'id_conta'),
			array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'nome_conta'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D014_SubConta","!=",' ');

		//  Definição da SQL
		$from = 'D014';

		$extra = <<<EOT
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'centroDeCustos':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D073_Id', 'colunaTitulo' => 'id_centro_custo'),
			array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'nome_centro_custo'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D073_Nome","!=",' ');
		$where = $API001->montarWhere($where,"D073_Flag_Ativo","=",'S');

		//  Definição da SQL
		$from = 'D073';

		$extra = <<<EOT
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'portadoresPagar':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D031_Id', 'colunaTitulo' => 'id_portador_pagar'),
			array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'nome_portador_pagar'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D031_Portador","!=",' ');

		//  Definição da SQL
		$from = 'D031';

		$extra = <<<EOT
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'notasSaida':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'id_nf_saida'),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'numero_nf_saida'),
			array('colunaValor' => 'codigo_usuario.C007_Id', 'colunaTitulo' => 'codigo_usuario'),
			array('colunaValor' => 'codigo_vendedor_interno.C007_Id', 'colunaTitulo' => 'codigo_vendedor_interno'),
			array('colunaValor' => 'codigo_vendedor_externo.C007_Id', 'colunaTitulo' => 'codigo_vendedor_externo'),
			array('colunaValor' => 'codigo_supervisor.C007_Id', 'colunaTitulo' => 'codigo_supervisor'),
			array('colunaValor' => 'codigo_supervisor2.C007_Id', 'colunaTitulo' => 'codigo_supervisor2'),
			array('colunaValor' => 'codigo_vendedor_substituto.C007_Id', 'colunaTitulo' => 'codigo_vendedor_substituto'),
			array('colunaValor' => 'T007_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'if(T007_Data_Emissao="0000-00-00","",T007_Data_Emissao)', 'colunaTitulo' => 'data_emissao_nf_saida'),
			array('colunaValor' => 'format(T007_Valor_Total,2,"pt_BR")', 'colunaTitulo' => 'valor_total_nf_saida'),
			array('colunaValor' => 'T007_D006_Id', 'colunaTitulo' => 'id_cfop_cabecalho'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T007_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T007_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T007_Flag_Entrada_Saida","=",'S');
		$where = $API001->montarWhere($where,"T007_Flag_Cancelada","=",'N');
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//Definição da SQL
		$from = 'T007';

		$extra = <<<EOT
			LEFT JOIN C007 AS codigo_usuario ON codigo_usuario.C007_Id=T007_C007_Id
			LEFT JOIN C007 AS codigo_vendedor_interno ON codigo_vendedor_interno.C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS codigo_supervisor ON codigo_supervisor.C007_Id=codigo_vendedor_interno.C007_C007_Id
			LEFT JOIN C007 AS codigo_supervisor2 ON codigo_supervisor2.C007_Id=codigo_vendedor_interno.C007_C007_Id_2
			LEFT JOIN C007 AS codigo_vendedor_externo ON codigo_vendedor_externo.C007_Id=T007_C007_Id_Vendedor_Externo
			LEFT JOIN C007 AS codigo_vendedor_substituto ON codigo_vendedor_substituto.C007_Id=codigo_vendedor_interno.C007_Id_Vendedor_Substituto
			{$where}
EOT;

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'notasSaidaDetalhes':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'id_nf_saida'),
			array('colunaValor' => 'T007_Prazos(T007_Id)', 'colunaTitulo' => 'prazo_nf_saida'),
			array('colunaValor' => 'T007_Prazo_Medio', 'colunaTitulo' => 'prazo_medio_nf_saida'),
			array('colunaValor' => 'format(T007_Valor_Frete,2,"pt_BR")', 'colunaTitulo' => 'valor_frete_nf_saida'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T007_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T007_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T007_Flag_Entrada_Saida","=",'S');
		$where = $API001->montarWhere($where,"T007_Flag_Cancelada","!=",'S');
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//Definição da SQL
		$from = 'T007';

		$extra = <<<EOT
			LEFT JOIN C007 AS codigo_usuario ON codigo_usuario.C007_Id=T007_C007_Id
			LEFT JOIN C007 AS codigo_vendedor_interno ON codigo_vendedor_interno.C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS codigo_supervisor ON codigo_supervisor.C007_Id=codigo_vendedor_interno.C007_C007_Id
			LEFT JOIN C007 AS codigo_supervisor2 ON codigo_supervisor2.C007_Id=codigo_vendedor_interno.C007_C007_Id_2
			LEFT JOIN C007 AS codigo_vendedor_externo ON codigo_vendedor_externo.C007_Id=T007_C007_Id_Vendedor_Externo
			LEFT JOIN C007 AS codigo_vendedor_substituto ON codigo_vendedor_substituto.C007_Id=codigo_vendedor_interno.C007_Id_Vendedor_Substituto
			{$where}
EOT;

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'notasSaidaProdutos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'id_item_nf_saida'),
			array('colunaValor' => 'T008_T007_Id', 'colunaTitulo' => 'id_nf_saida'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'format(T008_Quantidade,5,"pt_BR")', 'colunaTitulo' => 'quantidade_nf_saida'),
			array('colunaValor' => 'format(T008_Valor_Preco_Unitario,10,"pt_BR")', 'colunaTitulo' => 'valor_unitario_nf_saida'),
			array('colunaValor' => 'format(T008_Percentual_Margem,2,"pt_BR")', 'colunaTitulo' => 'percentual_margem_nf_saida'),
			array('colunaValor' => 'format(T008_Valor_Total_Preco,2,"pt_BR")', 'colunaTitulo' => 'valor_total_nf_saida'),
			array('colunaValor' => 'format(T008_IPV,4,"pt_BR")', 'colunaTitulo' => 'ipv_produto'),
			array('colunaValor' => 'D006_Flag_Venda_Compra_Outros', 'colunaTitulo' => 'tipo_cfop'),
			array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'id_cfop'),
			array('colunaValor' => 'T008_T006_Id', 'colunaTitulo' => 'id_item_pedido'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T007_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T007_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T007_Flag_Entrada_Saida","=",'S');
		$where = $API001->montarWhere($where,"T007_Flag_Cancelada","!=",'S');
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T008';

		$extra = <<<EOT
			LEFT JOIN T007 ON T007_Id=T008_T007_Id
			LEFT JOIN D009 ON D009_Id=T008_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T008_D006_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'orcamentosProdutos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'id_item_orcamento'),
			array('colunaValor' => 'T004_T003_Id', 'colunaTitulo' => 'numero_orcamento'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'format(T004_Quantidade,5,"pt_BR")', 'colunaTitulo' => 'quantidade_orcamento'),
			array('colunaValor' => 'format(T004_Quantidade_Confirmada,3,"pt_BR")', 'colunaTitulo' => 'quantidade_confirmada_orcamento'),
			array('colunaValor' => 'format(T004_Valor_Preco_Unitario,10,"pt_BR")', 'colunaTitulo' => 'valor_unitario_orcamento'),
			array('colunaValor' => 'format(T004_Valor_Total_Preco,2,"pt_BR")', 'colunaTitulo' => 'valor_total_orcamento'),
			array('colunaValor' => 'format(T004_Valor_Custo_Unitario,5,"pt_BR")', 'colunaTitulo' => 'valor_custo_unitario_orcamento'),
			array('colunaValor' => 'D006_Flag_Venda_Compra_Outros', 'colunaTitulo' => 'tipo_cfop'),
			array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'id_cfop'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T003_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T003_Data_Emissao","<=",$dataFinal);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T004';

		$extra = <<<EOT
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			LEFT JOIN D009 ON D009_Id=T004_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T004_D006_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'orcamentos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'numero_orcamento'),
			array('colunaValor' => 'T003_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T003_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'codigo_usuario.C007_Id', 'colunaTitulo' => 'codigo_usuario'),
			array('colunaValor' => 'codigo_vendedor_interno.C007_Id', 'colunaTitulo' => 'codigo_vendedor_interno'),
			array('colunaValor' => 'codigo_vendedor_externo.C007_Id', 'colunaTitulo' => 'codigo_vendedor_externo'),
			array('colunaValor' => 'codigo_supervisor.C007_Id', 'colunaTitulo' => 'codigo_supervisor'),
			array('colunaValor' => 'codigo_supervisor2.C007_Id', 'colunaTitulo' => 'codigo_supervisor2'),
			array('colunaValor' => 'codigo_vendedor_substituto.C007_Id', 'colunaTitulo' => 'codigo_vendedor_substituto'),
			array('colunaValor' => 'if(T003_Data_Emissao="0000-00-00","",T003_Data_Emissao)', 'colunaTitulo' => 'data_emissao_orcamento'),
			array('colunaValor' => 'format(T003_Valor_Total,2,"pt_BR")', 'colunaTitulo' => 'valor_total_orcamento'),
			array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaTitulo' => 'status_orcamento','callback' => 'campoStatusOrcamento'),
			array('colunaValor' => 'D047_Descricao_Motivo', 'colunaTitulo' => 'motivo_cancelamento'),
		);

		//montando clausula where
		$where = gInsertExtraWhere($where, "T003_Data_Emissao>='$dataInicio'");
		$where = gInsertExtraWhere($where, "T003_Data_Emissao<='$dataFinal'");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T003';

		$extra = <<<EOT
			LEFT JOIN C007 AS codigo_usuario ON codigo_usuario.C007_Id=T003_C007_Id
			LEFT JOIN C007 AS codigo_vendedor_interno ON codigo_vendedor_interno.C007_Id=T003_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS codigo_supervisor ON codigo_supervisor.C007_Id=codigo_vendedor_interno.C007_C007_Id
			LEFT JOIN C007 AS codigo_supervisor2 ON codigo_supervisor2.C007_Id=codigo_vendedor_interno.C007_C007_Id_2
			LEFT JOIN C007 AS codigo_vendedor_externo ON codigo_vendedor_externo.C007_Id=T003_C007_Id_Vendedor_Externo
			LEFT JOIN C007 AS codigo_vendedor_substituto ON codigo_vendedor_substituto.C007_Id=codigo_vendedor_interno.C007_Id_Vendedor_Substituto
			LEFT JOIN D047 ON D047_Id = T003_D047_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'pedidos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'numero_pedido'),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T005_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'T005_D022_Id', 'colunaTitulo' => 'codigo_transportadora'),
			array('colunaValor' => 'D036_Tipo_Transporte', 'colunaTitulo' => 'tipo_transporte_pedido'),
			array('colunaValor' => 'codigo_usuario.C007_Id', 'colunaTitulo' => 'codigo_usuario'),
			array('colunaValor' => 'codigo_vendedor_interno.C007_Id', 'colunaTitulo' => 'codigo_vendedor_interno'),
			array('colunaValor' => 'codigo_vendedor_externo.C007_Id', 'colunaTitulo' => 'codigo_vendedor_externo'),
			array('colunaValor' => 'codigo_supervisor.C007_Id', 'colunaTitulo' => 'codigo_supervisor'),
			array('colunaValor' => 'codigo_supervisor2.C007_Id', 'colunaTitulo' => 'codigo_supervisor2'),
			array('colunaValor' => 'codigo_vendedor_substituto.C007_Id', 'colunaTitulo' => 'codigo_vendedor_substituto'),
			array('colunaValor' => 'if(T005_Data_Emissao="0000-00-00","",T005_Data_Emissao)', 'colunaTitulo' => 'data_emissao_pedido'),
			array('colunaValor' => 'T005_Canal_Vendas_Ecommerce', 'colunaTitulo' => 'canal_venda_pedido'),
			array('colunaValor' => 'T005_Nome_Status', 'colunaTitulo' => 'status_pedido'),
			array('colunaValor' => 'format(T005_Valor_Total,2,"pt_BR")', 'colunaTitulo' => 'valor_total_pedido'),
			array('colunaValor' => 'T005_Ordem_Compra', 'colunaTitulo' => 'ordem_compra_cliente'),
			array('colunaValor' => 'T005_Percentual_Estoque_Disponivel', 'colunaTitulo' => 'percentual_estoque_disponivel'),
			array('colunaValor' => 'T005_Observacao_Comercial', 'colunaTitulo' => 'observacao_comercial'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T005_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T005_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"(T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)",false);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T005';

		$extra = <<<EOT
			LEFT JOIN D036 ON T005_D036_Id=D036_Id
			LEFT JOIN C007 AS codigo_usuario ON codigo_usuario.C007_Id=T005_C007_Id
			LEFT JOIN C007 AS codigo_vendedor_interno ON codigo_vendedor_interno.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS codigo_supervisor ON codigo_supervisor.C007_Id=codigo_vendedor_interno.C007_C007_Id
			LEFT JOIN C007 AS codigo_supervisor2 ON codigo_supervisor2.C007_Id=codigo_vendedor_interno.C007_C007_Id_2
			LEFT JOIN C007 AS codigo_vendedor_externo ON codigo_vendedor_externo.C007_Id=T005_C007_Id_Vendedor_Externo
			LEFT JOIN C007 AS codigo_vendedor_substituto ON codigo_vendedor_substituto.C007_Id=codigo_vendedor_interno.C007_Id_Vendedor_Substituto
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);
		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'pedidosDetalhes':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'numero_pedido'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T005_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T005_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"(T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)",false);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T005';

		$extra = <<<EOT
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);
		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'pedidosProdutos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'numero_pedido'),
			array('colunaValor' => 'T006_T004_Id', 'colunaTitulo' => 'id_item_orcamento'),
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'id_item_pedido'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'format(T006_Quantidade,5,"pt_BR")', 'colunaTitulo' => 'quantidade_pedido'),
			array('colunaValor' => 'format(T006_Valor_Preco_Unitario,10,"pt_BR")', 'colunaTitulo' => 'valor_unitario_pedido'),
			array('colunaValor' => 'format(T006_Valor_Total_Preco,2,"pt_BR")', 'colunaTitulo' => 'valor_total_pedido'),
			array('colunaValor' => 'format(T006_Valor_Frete_Unitario,10,"pt_BR")', 'colunaTitulo' => 'valor_frete_unitario_pedido'),
			array('colunaValor' => 'format(T006_Valor_Total_Custo,2,"pt_BR")', 'colunaTitulo' => 'valor_custo_total_pedido'),
			array('colunaValor' => 'format(T006_Valor_Custo_Unitario,5,"pt_BR")', 'colunaTitulo' => 'valor_custo_unitario_pedido'),
			array('colunaValor' => 'format(T006_Valor_Preco_Original ,10,"pt_BR")', 'colunaTitulo' => 'valor_preco_tabela_pedido'),
			array('colunaValor' => 'format(T006_Percentual_Desconto,2,"pt_BR")', 'colunaTitulo' => 'percentual_desconto_produto_pedido'),
			array('colunaValor' => 'format(T006_Valor_Custo_Unitario,5,"pt_BR")', 'colunaTitulo' => 'valor_custo_unitario_pedido'),
			array('colunaValor' => 'format(T006_Preco_Venda_Ecommerce,5,"pt_BR")', 'colunaTitulo' => 'valor_preco_venda_ecommerce_pedido'),
			array('colunaValor' => 'format(T006_Preco_Tabela_Ecommerce,5,"pt_BR")', 'colunaTitulo' => 'valor_preco_tabela_ecommerce_pedido'),
			array('colunaValor' => 'format(T006_Valor_Desconto_Ecommerce,5,"pt_BR")', 'colunaTitulo' => 'valor_desconto_ecommerce_pedido'),
			array('colunaValor' => 'format(T006_IPV,4,"pt_BR")', 'colunaTitulo' => 'ipv_produto'),
			array('colunaValor' => 'format(T006_Percentual_Margem,2,"pt_BR")', 'colunaTitulo' => 'percentual_margem'),
			array('colunaValor' => 'D006_Flag_Venda_Compra_Outros', 'colunaTitulo' => 'tipo_cfop'),
			array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'id_cfop'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T005_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T005_Data_Emissao","<=",$dataFinal);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T006';

		$extra = <<<EOT
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN D009 ON D009_Id=T006_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T006_D006_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'pedidosExpedicao':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'numero_pedido'),
			array('colunaValor' => 'T007_Codigo_Rastreio', 'colunaTitulo' => 'codigo_rastreio_pedido'),
			array('colunaValor' => 'if(T005_Data_Hora_Almoxarifado="0000-00-00","",T005_Data_Hora_Almoxarifado)', 'colunaTitulo' => 'data_almoxarifado_pedido'),
			array('colunaValor' => '(SELECT IF(T178_Data="0000-00-00","",T178_Data) FROM T178 WHERE T178_T005_Id=T005_Id AND T178_Descricao="Coletado" ORDER BY T178_Id DESC LIMIT 1)', 'colunaTitulo' => 'data_coleta_pedido'),
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'id_nota'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T005_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T005_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"(T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)",false);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T005';

		$extra = <<<EOT
			LEFT JOIN D036 ON T005_D036_Id=D036_Id
			LEFT JOIN T007 ON T007_T005_Id=T005_Id
			LEFT JOIN C007 AS codigo_usuario ON codigo_usuario.C007_Id=T005_C007_Id
			LEFT JOIN C007 AS codigo_vendedor_interno ON codigo_vendedor_interno.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS codigo_supervisor ON codigo_supervisor.C007_Id=codigo_vendedor_interno.C007_C007_Id
			LEFT JOIN C007 AS codigo_supervisor2 ON codigo_supervisor2.C007_Id=codigo_vendedor_interno.C007_C007_Id_2
			LEFT JOIN C007 AS codigo_vendedor_externo ON codigo_vendedor_externo.C007_Id=T005_C007_Id_Vendedor_Externo
			LEFT JOIN C007 AS codigo_vendedor_substituto ON codigo_vendedor_substituto.C007_Id=codigo_vendedor_interno.C007_Id_Vendedor_Substituto
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);
		
		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'cotacoes':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'numero_cotacao'),
			array('colunaValor' => 'T218_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T218_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'codigo_comprador_interno.C007_Id', 'colunaTitulo' => 'codigo_comprador_interno'),
			array('colunaValor' => 'codigo_comprador_externo.C007_Id', 'colunaTitulo' => 'codigo_comprador_externo'),
            array('colunaValor' => 'codigo_supervisor.C007_Id', 'colunaTitulo' => 'codigo_supervisor'),
            array('colunaValor' => 'codigo_supervisor2.C007_Id', 'colunaTitulo' => 'codigo_supervisor2'),
			array('colunaValor' => 'if(T218_Data_Emissao="0000-00-00","",T218_Data_Emissao)', 'colunaTitulo' => 'data_emissao_cotacao'),
			array('colunaValor' => 'T003_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'id_vendedor'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T218_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T218_Data_Emissao","<=",$dataFinal);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T218';

		$extra = <<<EOT
			LEFT JOIN C007 AS codigo_comprador_interno ON codigo_comprador_interno.C007_Id=T218_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS codigo_comprador_externo ON codigo_comprador_externo.C007_Id=T218_C007_Id_Vendedor_Externo
			LEFT JOIN C007 AS codigo_supervisor ON codigo_supervisor.C007_Id=codigo_comprador_interno.C007_C007_Id
			LEFT JOIN C007 AS codigo_supervisor2 ON codigo_supervisor2.C007_Id=codigo_comprador_interno.C007_C007_Id_2
			LEFT JOIN T219 ON T219_T218_Id=T218_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			{$where}
			GROUP BY T218_Id
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'cotacoesProdutos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T219_T218_Id', 'colunaTitulo' => 'numero_cotacao'),
			array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'id_item_cotacao'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'format(T219_Quantidade,5,"pt_BR")', 'colunaTitulo' => 'quantidade_cotacao'),
			array('colunaValor' => 'format(T219_Valor_Preco_Unitario,5,"pt_BR")', 'colunaTitulo' => 'valor_unitario_cotacao'),
			array('colunaValor' => 'format(T219_Valor_Total_Preco,2,"pt_BR")', 'colunaTitulo' => 'valor_total_cotacao'),
			array('colunaValor' => 'D006_Flag_Venda_Compra_Outros', 'colunaTitulo' => 'tipo_cfop'),
			array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'id_cfop'),
			array('colunaValor' => 'T219_T004_Id', 'colunaTitulo' => 'id_item_orcamento'),
			array('colunaValor' => 'T219_T006_Id', 'colunaTitulo' => 'id_item_pedido'),
			array('colunaValor' => 'T003_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'id_vendedor'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T218_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T218_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T219_Flag_Cancelado","=","N");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T219';

		$extra = <<<EOT
			LEFT JOIN T218 ON T218_Id=T219_T218_Id
			LEFT JOIN D009 ON D009_Id=T219_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T219_D006_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'ordensDeCompra':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'numero_oc'),
			array('colunaValor' => 'T224_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T224_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'codigo_comprador_interno.C007_Id', 'colunaTitulo' => 'codigo_comprador_interno'),
			array('colunaValor' => 'codigo_comprador_externo.C007_Id', 'colunaTitulo' => 'codigo_comprador_externo'),
            array('colunaValor' => 'codigo_supervisor.C007_Id', 'colunaTitulo' => 'codigo_supervisor'),
            array('colunaValor' => 'codigo_supervisor2.C007_Id', 'colunaTitulo' => 'codigo_supervisor2'),
			array('colunaValor' => 'if(T224_Data_Emissao="0000-00-00","",T224_Data_Emissao)', 'colunaTitulo' => 'data_emissao_oc'),
			array('colunaValor' => 'T003_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'id_vendedor'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T224_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T224_Data_Emissao","<=",$dataFinal);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T224';

		$extra = <<<EOT
			LEFT JOIN C007 AS codigo_comprador_interno ON codigo_comprador_interno.C007_Id=T224_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS codigo_comprador_externo ON codigo_comprador_externo.C007_Id=T224_C007_Id_Vendedor_Externo
			LEFT JOIN C007 AS codigo_supervisor ON codigo_supervisor.C007_Id=codigo_comprador_interno.C007_C007_Id
			LEFT JOIN C007 AS codigo_supervisor2 ON codigo_supervisor2.C007_Id=codigo_comprador_interno.C007_C007_Id_2
			LEFT JOIN T225 ON T225_T224_Id=T224_Id
			LEFT JOIN T219 ON T225_T219_Id=T219_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			{$where}
			GROUP BY T224_Id
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'ordensDeCompraProdutos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'numero_oc'),
			array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'id_item_oc'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'format(T225_Quantidade,5,"pt_BR")', 'colunaTitulo' => 'quantidade_oc'),
			array('colunaValor' => 'format(T225_Valor_Preco_Unitario,5,"pt_BR")', 'colunaTitulo' => 'valor_unitario_oc'),
			array('colunaValor' => 'format(T225_Valor_Total_Preco,2,"pt_BR")', 'colunaTitulo' => 'valor_total_oc'),
			array('colunaValor' => 'D006_Flag_Venda_Compra_Outros', 'colunaTitulo' => 'tipo_cfop'),
			array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'id_cfop'),
			array('colunaValor' => 'T225_T219_Id', 'colunaTitulo' => 'id_item_cotacao'),
			array('colunaValor' => 'T003_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'id_vendedor'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T224_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T224_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T225_Flag_Cancelada","=","N");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T225';

		$extra = <<<EOT
			LEFT JOIN T224 ON T224_Id=T225_T224_Id
			LEFT JOIN D009 ON D009_Id=T225_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T225_D006_Id
			LEFT JOIN T219 ON T225_T219_Id=T219_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			{$where}
			GROUP BY T225_Id
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'conhecimentos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'id_conhecimento'),
			array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'numero_conhecimento'),
			array('colunaValor' => 'T084_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'codigo_usuario.C007_Id', 'colunaTitulo' => 'codigo_usuario'),
			array('colunaValor' => 'T084_D022_Id', 'colunaTitulo' => 'codigo_transportadora'),
			array('colunaValor' => 'if(T084_Data_Emissao="0000-00-00","",T084_Data_Emissao)', 'colunaTitulo' => 'data_emissao_conhecimento'),
			array('colunaValor' => 'format(T084_Valor_Conhecimento,2,"pt_BR")', 'colunaTitulo' => 'valor_total_conhecimento'),
			array('colunaValor' => 'T084_Tipo_Nota','colunaTitulo' => 'tipo_nota'),
			array('colunaValor' => 'if(T084_Data_Lancamento="0000-00-00","",T084_Data_Lancamento)', 'colunaTitulo' => 'data_lancamento'),
			array('colunaValor' => 'group_concat(ifnull(T007_Id,""),ifnull(T013_Id,""))', 'colunaTitulo' => 'notas'),
			array('colunaValor' => 'T084_C004_Id', 'colunaTitulo' => 'id_empresa'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T084_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T084_Data_Emissao","<=",$dataFinal);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T084';

		$extra = <<<EOT
			LEFT JOIN C007 AS codigo_usuario ON codigo_usuario.C007_Id=T084_C007_Id
			LEFT JOIN T085 ON T084_Id=T085_T084_Id
		    LEFT JOIN T013 ON T013_Id=T085_T013_Id
		    LEFT JOIN T007 ON T007_Id=T085_T007_Id
			{$where}
			GROUP BY T084_Id
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'notasEntrada':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		//--------------------------------- Notas de entrada no T013------------------------------
		$grid = array(
			array('colunaValor' => '(T013_Id+10000000)', 'colunaTitulo' => 'id_nf_entrada'),
			array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'num_nf_entrada'),
			array('colunaValor' => 'T013_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'codigo_usuario.C007_Id', 'colunaTitulo' => 'codigo_usuario'),
			array('colunaValor' => 'T013_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'if(T013_Data_Emissao="0000-00-00","",T013_Data_Emissao)', 'colunaTitulo' => 'data_emissao_nf_entrada'),
			array('colunaValor' => 'if(T013_Data_Entrada="0000-00-00","",T013_Data_Entrada)', 'colunaTitulo' => 'data_entrada_nf_entrada'),
			array('colunaValor' => 'vendedor.C007_Id', 'colunaTitulo' => 'id_vendedor'),
			array('colunaValor' => 'format(T013_Valor_Total,2,"pt_BR")', 'colunaTitulo' => 'valor_total_nf_entrada'),
			array('colunaValor' => 'T013_D006_Id', 'colunaTitulo' => 'id_cfop_cabecalho'),
		);

		//montando clausula where
		$where = "";
		$where = $API001->montarWhere($where,"T013_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T013_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T013_Flag_Cancelada","=",'N');
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T013';

		$extra = <<<EOT
			LEFT JOIN C007 AS codigo_usuario ON codigo_usuario.C007_Id=T013_C007_Id
			LEFT JOIN D006 ON D006_Id=T013_D006_Id
			LEFT JOIN T014 ON T014_T013_Id=T013_Id
			LEFT JOIN T225 ON T225_Id=T014_T225_Id
			LEFT JOIN T219 ON T225_T219_Id=T219_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id
			{$where}
			GROUP BY T013_Id
EOT;
		list($geradoSqlT013, $geradoDadosT013) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//--------------------------------- Notas de entrada no T007------------------------------
		$grid = array(
			array('colunaValor' => 'T007ENT.T007_Id', 'colunaTitulo' => 'id_nf_entrada'),
			array('colunaValor' => 'T007ENT.T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'num_nf_entrada'),
			array('colunaValor' => 'T007ENT.T007_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'codigo_usuario.C007_Id', 'colunaTitulo' => 'codigo_usuario'),
			array('colunaValor' => 'T007ENT.T007_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'if(T007ENT.T007_Data_Emissao="0000-00-00","",T007ENT.T007_Data_Emissao)', 'colunaTitulo' => 'data_emissao_nf_entrada'),
			array('colunaValor' => 'if(T007ENT.T007_Data_Emissao="0000-00-00","",T007ENT.T007_Data_Emissao)', 'colunaTitulo' => 'data_entrada_nf_entrada'),
			array('colunaValor' => 'vendedor.C007_Id', 'colunaTitulo' => 'id_vendedor'),
		);

		//montando clausula where
		$where = "";
		$where = $API001->montarWhere($where,"T007ENT.T007_Flag_Entrada_Saida","=",'E');
		$where = $API001->montarWhere($where,"T007ENT.T007_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T007ENT.T007_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T007ENT.T007_Flag_Cancelada","=",'N');
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T007 as T007ENT';

		$extra = <<<EOT
			LEFT JOIN C007 AS codigo_usuario ON codigo_usuario.C007_Id=T007ENT.T007_C007_Id
			LEFT JOIN D006 ON D006_Id=T007ENT.T007_D006_Id
			LEFT JOIN T008 AS T008ENT ON T008ENT.T008_T007_Id=T007ENT.T007_Id
			LEFT JOIN T008 AS T008VEN ON T008VEN.T008_Id=T008ENT.T008_T008_Id
			LEFT JOIN T007 AS T007VEN ON T007VEN.T007_Id=T008VEN.T008_T007_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T007VEN.T007_C007_Id_Vendedor_Interno
			{$where}
			GROUP BY T007ENT.T007_Id
EOT;
		list($geradoSqlT007, $geradoDadosT007) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		$geradoDados = array_merge($geradoDadosT013,$geradoDadosT007);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'notasEntradaProdutos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		
				
		//--------------------------------- Produtos das Notas de entrada no T013------------------------------

		$grid = array(
			array('colunaValor' => '(T014_T013_Id+10000000)', 'colunaTitulo' => 'id_nf_entrada'),
			array('colunaValor' => '(T014_Id+10000000)', 'colunaTitulo' => 'id_item_nf_entrada'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'format(T014_Quantidade,3,"pt_BR")', 'colunaTitulo' => 'quantidade_nf_entrada'),
			array('colunaValor' => 'format(T014_Valor_Custo_Unitario,10,"pt_BR")', 'colunaTitulo' => 'valor_unitario_nf_entrada'),
			array('colunaValor' => 'format(T014_Valor_Total_Custo,2,"pt_BR")', 'colunaTitulo' => 'valor_total_nf_entrada'),
			array('colunaValor' => 'format(T014_Valor_Frete,3,"pt_BR")', 'colunaTitulo' => 'valor_frete_produto_nf_entrada'),
			array('colunaValor' => 'D006_Flag_Venda_Compra_Outros', 'colunaTitulo' => 'tipo_cfop'),
			array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'id_cfop'),
			array('colunaValor' => 'vendedor.C007_Id', 'colunaTitulo' => 'id_vendedor'),
		);

		//montando clausula where
		$where = "";
		$where = $API001->montarWhere($where,"T013_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T013_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T013_Flag_Cancelada","=",'N');
		//$where = $API001->montarWhere($where,"ifnull(D001_D060_Id,0)",">",0);
		//$where = $API001->montarWhere($where,"ifnull(D001_C008_Id,0)",">",0);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T014';

		$extra = <<<EOT
			LEFT JOIN T013 ON T013_Id=T014_T013_Id
			LEFT JOIN D009 ON D009_ID=T014_D009_Id
			LEFT JOIN D049 ON D049_ID=D009_D049_Id
			LEFT JOIN D001 ON D001_ID=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T014_D006_Id
			LEFT JOIN T225 ON T225_Id=T014_T225_Id
			LEFT JOIN T219 ON T225_T219_Id=T219_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id
			{$where}
			GROUP BY T014_Id
EOT;
		
		list($geradoSqlT013, $geradoDadosT013) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//---------------------------------Produtos das Notas de entrada no T007------------------------------
		$grid = array(
			array('colunaValor' => 'T007ENT.T007_Id', 'colunaTitulo' => 'id_nf_entrada'),
			array('colunaValor' => 'T008ENT.T008_Id', 'colunaTitulo' => 'id_item_nf_entrada'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'format(T008ENT.T008_Quantidade,3,"pt_BR")', 'colunaTitulo' => 'quantidade_nf_entrada'),
			array('colunaValor' => 'format(T008ENT.T008_Valor_Preco_Sem_Desconto_Unitario,10,"pt_BR")', 'colunaTitulo' => 'valor_unitario_nf_entrada'),
			array('colunaValor' => 'format(T008ENT.T008_Valor_Total_Preco_Sem_Desconto,2,"pt_BR")', 'colunaTitulo' => 'valor_total_nf_entrada'),
			array('colunaValor' => 'format(T008ENT.T008_Valor_Total_Frete,3,"pt_BR")', 'colunaTitulo' => 'valor_frete_produto_nf_entrada'),
			array('colunaValor' => 'D006_Flag_Venda_Compra_Outros', 'colunaTitulo' => 'tipo_cfop'),
			array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'id_cfop'),
		);

		//montando clausula where
		$where = "";
		$where = $API001->montarWhere($where,"T007ENT.T007_Flag_Entrada_Saida","=",'E');
		$where = $API001->montarWhere($where,"T007ENT.T007_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T007ENT.T007_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T007ENT.T007_Flag_Cancelada","=",'N');
		//$where = $API001->montarWhere($where,"D006_Flag_Entrada_Saida ","=",'E');
		//$where = $API001->montarWhere($where,"ifnull(D001_D060_Id,0)",">",0);
		//$where = $API001->montarWhere($where,"ifnull(D001_C008_Id,0)",">",0);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T008 AS T008ENT';

		$extra = <<<EOT
			LEFT JOIN T007 AS T007ENT ON T007ENT.T007_Id=T008ENT.T008_T007_Id
			LEFT JOIN D009 ON D009_Id=T008ENT.T008_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T008ENT.T008_D006_Id
			LEFT JOIN T008 AS T008VEN ON T008VEN.T008_Id=T008ENT.T008_T008_Id
			LEFT JOIN T007 AS T007VEN ON T007VEN.T007_Id=T008VEN.T008_T007_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T007VEN.T007_C007_Id_Vendedor_Interno
			{$where}
			GROUP BY T008ENT.T008_Id
EOT;
		list($geradoSqlT007, $geradoDadosT007) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		$geradoDados = array_merge($geradoDadosT013,$geradoDadosT007);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'notasEntradaProdutosTributos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		//--------------------------------- Produtos das Notas de entrada no T013------------------------------

		$grid = array(
			array('colunaValor' => '(T014_Id+10000000)', 'colunaTitulo' => 'id_item_nf_entrada'),
			array('colunaValor' => 'format(T014_Valor_ICMS_Substituicao,2,"pt_BR")', 'colunaTitulo' => 'valor_icms_st_nf_entrada'),
			array('colunaValor' => 'format(T014_Valor_IPI,2,"pt_BR")', 'colunaTitulo' => 'valor_ipi_nf_entrada'),
			array('colunaValor' => 'format(T014_Valor_ICMS,2,"pt_BR")', 'colunaTitulo' => 'Valor_ICMS_nf_entrada'),
		);

		//montando clausula where
		$where = "";
		$where = $API001->montarWhere($where,"T013_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T013_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T013_Flag_Cancelada","=",'N');
		//$where = $API001->montarWhere($where,"ifnull(D001_D060_Id,0)",">",0);
		//$where = $API001->montarWhere($where,"ifnull(D001_C008_Id,0)",">",0);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T014';

		$extra = <<<EOT
			LEFT JOIN T013 ON T013_Id=T014_T013_Id
			LEFT JOIN D009 ON D009_ID=T014_D009_Id
			LEFT JOIN D049 ON D049_ID=D009_D049_Id
			LEFT JOIN D001 ON D001_ID=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T014_D006_Id
			LEFT JOIN T225 ON T225_Id=T014_T225_Id
			LEFT JOIN T219 ON T225_T219_Id=T219_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id
			{$where}
			GROUP BY T014_Id
EOT;

		list($geradoSqlT013, $geradoDadosT013) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//--------------------------------- Produtos das Notas de entrada no T007------------------------------

		$grid = array(
			array('colunaValor' => 'T008ENT.T008_Id', 'colunaTitulo' => 'id_item_nf_entrada'),
			array('colunaValor' => 'format(T008ENT.T008_Valor_ICMS_Substituicao,2,"pt_BR")', 'colunaTitulo' => 'valor_icms_st_nf_entrada'),
			array('colunaValor' => 'format(T008ENT.T008_Valor_IPI,2,"pt_BR")', 'colunaTitulo' => 'valor_ipi_nf_entrada'),
			array('colunaValor' => 'format(T008ENT.T008_Valor_ICMS,2,"pt_BR")', 'colunaTitulo' => 'valor_ICMS_nf_entrada'),
		);

		//montando clausula where
		$where = "";
		$where = $API001->montarWhere($where,"T007ENT.T007_Flag_Entrada_Saida","=",'E');
		$where = $API001->montarWhere($where,"T007ENT.T007_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T007ENT.T007_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T007ENT.T007_Flag_Cancelada","=",'N');
		//$where = $API001->montarWhere($where,"ifnull(D001_D060_Id,0)",">",0);
		//$where = $API001->montarWhere($where,"ifnull(D001_C008_Id,0)",">",0);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T008 AS T008ENT';

		$extra = <<<EOT
			LEFT JOIN T007 AS T007ENT ON T007ENT.T007_Id=T008ENT.T008_T007_Id
			LEFT JOIN D009 ON D009_Id=T008ENT.T008_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D006 ON D006_Id=T008ENT.T008_D006_Id
			LEFT JOIN T008 AS T008VEN ON T008VEN.T008_Id=T008ENT.T008_T008_Id
			LEFT JOIN T007 AS T007VEN ON T007VEN.T007_Id=T008VEN.T008_T007_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T007VEN.T007_C007_Id_Vendedor_Interno
			{$where}
			GROUP BY T008ENT.T008_Id
EOT;

		list($geradoSqlT007, $geradoDadosT007) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		$geradoDados = array_merge($geradoDadosT013,$geradoDadosT007);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'contasReceber':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'id_receber'),
			array('colunaValor' => 'T002_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T002_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'T002_T007_Id', 'colunaTitulo' => 'id_NF'),
			array('colunaValor' => 'T007_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'codigo_vendedor_interno'),
			array('colunaValor' => 'T002_D014_Id', 'colunaTitulo' => 'id_conta'),
			array('colunaValor' => 'T002_D027_Id', 'colunaTitulo' => 'id_portador_receber'),
			array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'num_duplicata_receber'),
			array('colunaValor' => 'if(T002_Data_Emissao="0000-00-00","",T002_Data_Emissao)', 'colunaTitulo' => 'data_emissao_receber'),
			array('colunaValor' => 'if(T002_Data_Vencimento="0000-00-00","",T002_Data_Vencimento)', 'colunaTitulo' => 'data_vencimento_receber'),
			array('colunaValor' => 'if(T002_Data_Recebimento="0000-00-00","",T002_Data_Recebimento)', 'colunaTitulo' => 'data_recebimento'),
			array('colunaValor' => 'format(T002_Valor_Total,2,"pt_BR")', 'colunaTitulo' => 'valor_receber'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'grupo_receber'),
			array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'conta_receber'),
			array('colunaValor' => 'REPLACE(D085_Mensagem,"\n","")', 'colunaTitulo' => 'mensagem_cobranca'),
			// array('colunaValor' => 'datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento)', 'colunaTitulo' => 'atraso_recebimento'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T002_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T002_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T002_Flag_Cancelada","=","N");
		$where = $API001->montarWhere($where,"IF(T002_T002_Id_Agrupado > 0,'S','N')","=","N");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T002';

		$extra = <<<EOT
			LEFT JOIN T007 ON T007_Id=T002_T007_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN D032 ON D032_Id=T002_D032_Id
			LEFT JOIN D014 ON D014_Id=T002_D014_Id
			LEFT JOIN D085 ON T002_Id=D085_T002_Id AND D085_Id = (SELECT MAX(D085_Id) FROM D085 Where D085_T002_Id = T002_Id)
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'contasReceberDetalhes':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T002_Id', 'colunaTitulo' => 'id_receber'),
			array('colunaValor' => 'format(T002_Valor_Juros,2,"pt_BR")', 'colunaTitulo' => 'valor_juros_receber'),
			array('colunaValor' => 'format(T002_Valor_Taxas,2,"pt_BR")', 'colunaTitulo' => 'valor_taxas_receber'),
			array('colunaValor' => 'format(T002_Valor_Desconto,2,"pt_BR")', 'colunaTitulo' => 'valor_desconto_receber'),
			array('colunaValor' => 'format(T002_Valor_Saldo,2,"pt_BR")', 'colunaTitulo' => 'valor_saldo_receber'),
			array('colunaValor' => 'format(T002_Percentual_Comissao,2,"pt_BR")', 'colunaTitulo' => 'percentual_comissao_receber'),
			array('colunaValor' => 'format(T002_Valor_Comissao,5,"pt_BR")', 'colunaTitulo' => 'valor_comissao_receber'),
			array('colunaValor' => 'format(T002_Valor_Duplicata,2,"pt_BR")', 'colunaTitulo' => 'valor_sem_juros_receber'),
			array('colunaValor' => 'format(T002_Valor_Recebido,2,"pt_BR")', 'colunaTitulo' => 'valor_recebido_receber'),
			array('colunaValor' => 'if(T002_Data_Previsao_Recebimento="0000-00-00","",T002_Data_Previsao_Recebimento) ', 'colunaTitulo' => 'data_previsao_recebimento'),
			array('colunaValor' => 'datediff(if(T002_Data_Recebimento=\'0000-00-00\',curdate(),T002_Data_Recebimento),T002_Data_Vencimento)', 'colunaTitulo' => 'dias_atraso_receber'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T002_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T002_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T002_Flag_Cancelada","=","N");
		$where = $API001->montarWhere($where,"IF(T002_T002_Id_Agrupado > 0,'S','N')","=","N");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T002';

		$extra = <<<EOT
			LEFT JOIN T007 ON T007_Id=T002_T007_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'atividades':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$dataInicio = $dataInicio." 00:00:00";
		$dataFinal = $dataFinal." 23:59:59";

		$grid = array(
			array('colunaValor' => 'T259_Id', 'colunaTitulo' => 'id_atividade'),
			array('colunaValor' => 'T259_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T259_C007_Id', 'colunaTitulo' => 'codigo_usuario'),
			array('colunaValor' => 'T259_Data_Hora', 'colunaTitulo' => 'ultimo_contato_atividade'),
            array('colunaValor' => 'TIMESTAMPDIFF(MINUTE, T259_Data_Hora_Inicio, IF(T259_Data_Hora=T259_Data_Hora_Inicio, CURRENT_TIMESTAMP, T259_Data_Hora))', 'colunaTitulo' => 'duracao_atividade','callback' => 'gProcessaTempo'),
            array('colunaValor' => 'T259_Assunto', 'colunaTitulo' => 'assuntos_atividade','callback' => 'assuntoTarefa'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T259_Data_Hora",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T259_Data_Hora","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T259_Flag_Excluido","=","N");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T259';

		$extra = <<<EOT
			LEFT JOIN C007 on C007_Id=T259_C007_Id
			LEFT JOIN D024 on D024_Id=T259_D024_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'assuntosAtividades':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$dataHoraInicio = $dataInicio." 00:00:00";
		$dataHoraFinal = $dataFinal." 23:59:59";

		$grid = array(
			array('colunaValor' => 'D085_T259_Id', 'colunaTitulo' => 'id_atividade'),
			array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => 'mensagem_assunto'),
			array('colunaValor' => 'D085_Data', 'colunaTitulo' => 'data_assunto'),
			array('colunaValor' => 'D085_Hora', 'colunaTitulo' => 'hora_assunto'),
			array('colunaValor' => 'T259_Assunto', 'colunaTitulo' => 'tipo_assunto','callback' => 'assuntoTarefaTipo'),
			array('colunaValor' => 'D085_Entidade', 'colunaTitulo' => 'entidade_assunto'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D085_Data",">=",$dataInicio);
		$where = $API001->montarWhere($where,"D085_Data","<=",$dataFinal);
		//$where = $API001->montarWhere($where,"T259_Data_Hora",">=",$dataHoraInicio);
		//$where = $API001->montarWhere($where,"T259_Data_Hora","<=",$dataHoraFinal);
		//$where = $API001->montarWhere($where,"IFNULL(D085_T259_Id,0)",">",0);
		$where = $API001->montarWhere($where,"T259_Flag_Excluido","=","N");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'D085';

		$extra = <<<EOT
			LEFT JOIN T259 on T259_Id=D085_T259_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'contasPagar':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'id_pagar'),
			array('colunaValor' => 'T015_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T015_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'T015_T013_Id', 'colunaTitulo' => 'id_nf_recebida'),
			array('colunaValor' => 'T015_D031_Id', 'colunaTitulo' => 'id_conta'),
			array('colunaValor' => 'T015_D014_Id', 'colunaTitulo' => 'id_portador_pagar'),
			array('colunaValor' => 'T015_D073_Id', 'colunaTitulo' => 'id_centro_custo'),
			array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'numero_duplicata_pagar'),
			array('colunaValor' => 'if(T015_Data_Emissao="0000-00-00","",T015_Data_Emissao)', 'colunaTitulo' => 'data_emissao_pagar'),
			array('colunaValor' => 'if(T015_Data_Vencimento="0000-00-00","",T015_Data_Vencimento)', 'colunaTitulo' => 'data_vencimento_pagar'),
			array('colunaValor' => 'if(T015_Data_Pagamento="0000-00-00","",T015_Data_Pagamento)', 'colunaTitulo' => 'data_pagamento'),
			array('colunaValor' => 'format(T015_Valor_Total,2,"pt_BR")', 'colunaTitulo' => 'valor_pagar'),
			array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'grupo_pagar'),
			array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'conta_pagar'),
			array('colunaValor' => 'datediff(if(T015_Data_Pagamento=\'0000-00-00\',curdate(),T015_Data_Pagamento),T015_Data_Vencimento)', 'colunaTitulo' => 'atraso_pagamento'),

		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T015_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T015_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T015_Flag_Cancelada","=","N");
		$where = $API001->montarWhere($where,"IF(T015_T015_Id_Agrupado > 0,'S','N')","=","N");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);


		//  Definição da SQL
		$from = 'T015';

		$extra = <<<EOT
			LEFT JOIN D032 ON T015_D032_Id=D032_Id
			LEFT JOIN D014 ON T015_D014_Id=D014_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'contasPagarDetalhes':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'id_pagar'),
			array('colunaValor' => 'format(T015_Valor_Juros,2,"pt_BR")', 'colunaTitulo' => 'valor_juros_pagar'),
			array('colunaValor' => 'format(T015_Valor_Duplicata,2,"pt_BR")', 'colunaTitulo' => 'valor_sem_juros_pagar'),
			array('colunaValor' => 'format(T015_Valor_Desconto,2,"pt_BR")', 'colunaTitulo' => 'valor_desconto_pagar'),
			array('colunaValor' => 'if(T015_Data_Previsao_Pagamento="0000-00-00","",T015_Data_Previsao_Pagamento)', 'colunaTitulo' => 'data_previsao_pagamento'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T015_Data_Emissao",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T015_Data_Emissao","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T015_Flag_Cancelada","=","N");
		$where = $API001->montarWhere($where,"IF(T015_T015_Id_Agrupado > 0,'S','N')","=","N");
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T015';

		$extra = <<<EOT
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'lancamentosBancarios':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T019_Id', 'colunaTitulo' => 'id_lancamento'),
			array('colunaValor' => 'T019_D024_Id', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T019_D014_Id', 'colunaTitulo' => 'id_conta'),
			array('colunaValor' => 'T019_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'T019_D027_Id', 'colunaTitulo' => 'id_portador_receber'),
			array('colunaValor' => 'T019_D031_Id', 'colunaTitulo' => 'id_portador_pagar'),
			array('colunaValor' => 'T019_Referente', 'colunaTitulo' => 'descricao_lancamento'),
			array('colunaValor' => 'T019_Complemento', 'colunaTitulo' => 'complemento_lancamento'),
			array('colunaValor' => 'if(T019_Data_Lancamento="0000-00-00","",T019_Data_Lancamento)', 'colunaTitulo' => 'data_lancamento'),
			array('colunaValor' => 'format(T019_Valor_Lancamento,2,"pt_BR")', 'colunaTitulo' => 'valor_lancamento'),
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'nome_banco'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T019_Data_Lancamento",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T019_Data_Lancamento","<=",$dataFinal);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T019';

		$extra = <<<EOT
			LEFT JOIN D007 ON D007_Id = T019_D007_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'tiposCFOP':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D006_Flag_Venda_Compra_Outros', 'colunaTitulo' => 'tipo_cfop'),
			array('colunaValor' => 'D006_Descricao', 'colunaTitulo' => 'descricao_tipo_cfop'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"IFNULL(D006_Flag_Venda_Compra_Outros,'')","!="," ");

		//  Definição da SQL
		$from = 'D006';

		$extra = <<<EOT
			{$where}
			GROUP BY D006_Flag_Venda_Compra_Outros
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'CFOP':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D006_Id', 'colunaTitulo' => 'id_cfop'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'codigo_cfop'),
			array('colunaValor' => 'D006_Descricao', 'colunaTitulo' => 'descricao_cfop'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"IFNULL(D006_Flag_Venda_Compra_Outros,'')","!="," ");

		//  Definição da SQL
		$from = 'D006';

		$extra = <<<EOT
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'RMA':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		$grid = array(
			array('colunaValor' => 'T182_Id', 'colunaTitulo' => 'id_rma'),
			array('colunaValor' => 'if(T182_Data="0000-00-00","",T182_Data)', 'colunaTitulo' => 'data_abertura_rma'),
			array('colunaValor' => 'if(T182_Data_Conclusao="0000-00-00","",T182_Data_Conclusao)', 'colunaTitulo' => 'data_conclusao_rma'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'T182_Quantidade', 'colunaTitulo' => 'quantidade_rma'),
			array('colunaValor' => 'D164_Tipo', 'colunaTitulo' => 'tipo_rma'),
			array('colunaValor' => 'T182_D024_Id_Cliente', 'colunaTitulo' => 'codigo_cliente'),
			array('colunaValor' => 'T182_D024_Id_Fornecedor', 'colunaTitulo' => 'codigo_fornecedor'),
			array('colunaValor' => 'format(T182_Preco,2,"pt_BR")', 'colunaTitulo' => 'preco_rma'),
			array('colunaValor' => 'D162_Status', 'colunaTitulo' => 'status_rma'),
			array('colunaValor' => 'T182_NF_Venda', 'colunaTitulo' => 'numero_nf_saida'),
			array('colunaValor' => 'T182_Pedido', 'colunaTitulo' => 'pedido'),
			array('colunaValor' => 'TransCli.D022_Nome_Empresa', 'colunaTitulo' => 'transportadora_nf_saida'),
			array('colunaValor' => 'T182_Data_Conclusao_Cliente', 'colunaTitulo' => 'data_conclusao_cliente'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'data_emissao_nota'),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'data_emissao_pedido'),
			array('colunaValor' => 'D199_Descricao', 'colunaTitulo' => 'origem_atendimento'),
			array('colunaValor' => 'D197_Motivo', 'colunaTitulo' => 'motivo'),
			array('colunaValor' => 'T005_Canal_Vendas_Ecommerce', 'colunaTitulo' => 'canal_vendas'),
			array('colunaValor' => 'C004_Id', 'colunaTitulo' => 'id_empresa'),			
			array('colunaValor' => 'T182_C007_Id', 'colunaTitulo' => 'id_usuario'),			
			array('colunaValor' => 'T182_Data_Previsao_Fechamento', 'colunaTitulo' => 'data_previsao_fechamento'),			
			array('colunaValor' => 'format((select sum(T312_Valor) from T312 where T312_T182_Id=T182_Id),2,"pt_BR")', 'colunaTitulo' => 'total_custo'),
			array('colunaValor' => 'T182_Campos->Protocolo_Marketplace', 'colunaTitulo' => 'protocolo_marketplace'),
			array('colunaValor' => 'T182_Campos->Protocolo_MAXBOT', 'colunaTitulo' => 'protocolo_maxbot'),						 
			array('colunaValor' => 'T182_Campos->Protocolo_Reclame_Aqui', 'colunaTitulo' => 'protocolo_reclame_aqui'),						 			
			array('colunaValor' => 'T182_Data_Retorno_Cliente', 'colunaTitulo' => 'data_retorno_cliente'),	
			array('colunaValor' => 'format((select sum(T312_Valor) from T312 where T312_T182_Id=T182_Id and T312_Flag_Reversao = "S"),2,"pt_BR")', 'colunaTitulo' => 'total_revertido'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T182_Data",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T182_Data","<=",$dataFinal);
		$where = gInsertExtraWhere($where, "$extraFiltro", true);

		//  Definição da SQL
		$from = 'T182';

		$extra = <<<EOT
			LEFT JOIN D009 ON D009_Id=T182_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D162 ON D162_Id=T182_D162_Id
			LEFT JOIN D164 ON D164_Id=T182_D164_Id
			LEFT JOIN T005 on T005_Id=T182_Pedido
			LEFT JOIN T007 on T007_Id = IF(T182_T007_Id = 0, T005_T007_Id, T182_T007_Id)
			LEFT JOIN D022 as TransCli on TransCli.D022_Id=T007_D022_Id
			LEFT JOIN D199 on D199_Id = T182_D199_Id
			LEFT JOIN D197 on D197_Id = T182_D197_Id
			LEFT JOIN C004 on C004_Id = T182_C004_Id			
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'usuarios':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'codigo_usuario'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'nome_usuario'),
		);

		//  Definição da SQL
		$from = 'C007';

		$extra = <<<EOT
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'vendedoresInternos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'codigo_vendedor_interno'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'nome_vendedor_interno'),
			array('colunaValor' => 'C007_Flag_Vendedor', 'colunaTitulo' => 'tipo_cadastro'),
			array('colunaValor' => 'format(C007_Cota_Vendas_Mensal,2,"pt_BR")', 'colunaTitulo' => 'cota_vendas_mensal'),
			array('colunaValor' => 'C007A_Ramal', 'colunaTitulo' => 'ramal_vendedor_interno'),
		);

		//  Definição da SQL
		$from = 'C007';

		$extra = <<<EOT
			LEFT JOIN C007A ON C007A_C007_Id=C007_Id
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'supervisores':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'codigo_supervisor'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'nome_supervisor'),
		);

		//  Definição da SQL
		$from = 'C007';

		$extra = <<<EOT
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'vendedoresExternos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'codigo_vendedor_externo'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'nome_vendedor_externo'),
		);

		//  Definição da SQL
		$from = 'C007';

		$extra = <<<EOT
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'supervisores2':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'codigo_supervisor2'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'nome_supervisor2'),
		);

		//  Definição da SQL
		$from = 'C007';

		$extra = <<<EOT
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}	

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'vendedoresSubstitutos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'codigo_vendedor_substituto'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'nome_vendedor_substituto'),
		);

		//  Definição da SQL
		$from = 'C007';

		$extra = <<<EOT
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'compradoresInternos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'codigo_comprador_interno'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'nome_comprador_interno'),
		);

		//  Definição da SQL
		$from = 'C007';

		$extra = <<<EOT
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'compradoresExternos':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'codigo_comprador_externo'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'nome_comprador_externo'),
		);

		//  Definição da SQL
		$from = 'C007';

		$extra = <<<EOT
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'estoque':
		$retorno = $API003->auth($_GET['API_AUTH'], true);

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'sku_produto'),
			array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'format(D009_Quantidade_Estoque,2,"pt_BR")', 'colunaTitulo' => 'quantidade_estoque_fisico'),
			array('colunaValor' => 'format(D009_Quantidade_Estoque_Liquido,2,"pt_BR")', 'colunaTitulo' => 'quantidade_estoque_liquido'),
			array('colunaValor' => 'format(D009_Valor_Custo_Unitario,5,"pt_BR")', 'colunaTitulo' => 'valor_custo_ICF'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"D001_Codigo_Produto","!=",' ');
		$where = $API001->montarWhere($where,"D001_Descricao_Produto","!="," ");
		$where = $API001->montarWhere($where,"D001_Flag_Ativo","=","S");

		//  Definição da SQL
		$from = 'D001';

		$extra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id
			{$where}
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);

		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'produtoImportacao':

		$retorno = $API003->auth($_GET['API_AUTH'], true);

		$dataInicio = $_GET['DATAINICIAL']; 
		$dataFinal = $_GET['DATAFINAL'];
		$extraFiltro = extraFiltro();		

		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'id_produto'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'sku_produto'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'descricao_produto'),
			array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'id_empresa'),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'id_produto_empresa'),
			array('colunaValor' => "T007_Numero_Nota_Fiscal", 'colunaTitulo' => 'numero_nota_fiscal_importacao'),
			array('colunaValor' => "T007_Data_Emissao", 'colunaTitulo' => 'emissao_nota_fiscal_importacao'),
			array('colunaValor' => "T075_Numero_DI", 'colunaTitulo' => 'numero_declaracao_importacao'),
			array('colunaValor' => 'format(T075_cotacao_ME,4,"pt_BR")', 'colunaTitulo' => 'cotacao_me'),
			array('colunaValor' => 'format(T055_Valor_Custo_Unitario,4,"pt_BR")', 'colunaTitulo' => 'valor_unitario_me'),
			array('colunaValor' => 'format(T001_Valor_Custo_Unitario,4,"pt_BR")', 'colunaTitulo' => 'valor_custo_compra'),
			array('colunaValor' => 'format(D009_Valor_Custo_Ultima_Compra,4,"pt_BR")', 'colunaTitulo' => 'valor_custo_ultima_compra'),
			array('colunaValor' => 'format(D009_Valor_Custo_Medio_Unitario,4,"pt_BR")', 'colunaTitulo' => 'valor_custo_medio_unitario'),
			array('colunaValor' => 'format(D009_Valor_Preco_Tabela,4,"pt_BR")', 'colunaTitulo' => 'valor_custo_manual'),
			array('colunaValor' => 'format(T055_Valor_Total_Frete,4,"pt_BR")', 'colunaTitulo' => 'valor_frete'),
			array('colunaValor' => 'format(T055_Peso_Kg_Liquido_Unitario,4,"pt_BR")', 'colunaTitulo' => 'peso_produto_declaracao_importacao'),
			array('colunaValor' => 'format( T001_Quantidade,2,"pt_BR")', 'colunaTitulo' => 'quantidade_produto'),
		);

		//montando clausula where
		$where = $API001->montarWhere($where,"T001_Data_Lancamento",">=",$dataInicio);
		$where = $API001->montarWhere($where,"T001_Data_Lancamento","<=",$dataFinal);
		$where = $API001->montarWhere($where,"T001_Flag_Operacao","=","E");

		$where = $API001->montarWhere($where,"T001_T055_Id","!="," ");
		$where = $API001->montarWhere($where,"D001_Flag_Ativo","=","S");
		$where = $API001->montarWhere($where,"D001_Codigo_Produto","!="," ");
		$where = $API001->montarWhere($where,"D001_Descricao_Produto","!="," ");

		//  Definição da SQL
		$from = 'T001';

		$extra = <<<EOT
			LEFT JOIN D009 ON T001_D009_Id = D009_Id
			LEFT JOIN D049 ON D009_D049_Id = D049_Id
			LEFT JOIN D001 ON D049_D001_Id = D001_Id
			LEFT JOIN T055 ON T001_T055_Id = T055_Id
			LEFT JOIN T075 ON T055_T075_Id = T075_Id
			LEFT JOIN T007 ON T007_T075_Id = T075_Id
			{$where}
			GROUP BY T001_Id
			ORDER BY D009_Id ASC
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true);
		
		//validando se retornaram dados da query
		$semDados = $API001->tratarRetornoVazio($geradoDados);
		if(is_array($semDados)){
			echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'produtoImportacaoPorData':


	break;

	case 'orcamento':
		//validacão do token
		// $retorno = $API003->auth($_GET['API_AUTH'], true);

		// if(is_array($retorno)){
		// 	echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
		// 	die();
		// }
		//validacão do token

		require_once('bibliotecas/classes/SMTOrcamento.php');
		$SMTOrcamento = new SMTOrcamento();

		echo $SMTOrcamento->OrcamentoPolux();

		break;

		case 'custoRMA':
			$retorno = $API003->auth($_GET['API_AUTH'], true);
	
			if(is_array($retorno)){
				echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
				die();
			}
	
			$dataInicio = $_GET['DATAINICIAL']; 
			$dataFinal = $_GET['DATAFINAL'];
			$extraFiltro = extraFiltro();			
	
			$grid = array(					
				array('colunaValor' => 'T312_T182_Id', 'colunaTitulo' => 'id_rma'),						
				array('colunaValor' => 'D198_Descricao', 'colunaTitulo' => 'descricao'),			
				array('colunaValor' => 'format(T312_Valor,2,"pt_BR")', 'colunaTitulo' => 'valor'),						
				array('colunaValor' => 'T312_Flag_Reversao', 'colunaTitulo' => 'revertido'),		
			);
	
			$where = $API001->montarWhere($where,"T182_Data",">=",$dataInicio);
			$where = $API001->montarWhere($where,"T182_Data","<=",$dataFinal);
			$where = gInsertExtraWhere($where, "$extraFiltro", true);
	
	
			//  Definição da SQL
			$from = 'T312';
	
			$extra = <<<EOT
				LEFT JOIN D198 ON D198_Id = T312_D198_Id
				LEFT JOIN T182 ON T182_Id = T312_T182_Id
				{$where}					
	EOT;
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra,0.00,false,false,false,45000,true); //Retornando erro		
			
			//validando se retornaram dados da query		
			$semDados = $API001->tratarRetornoVazio($geradoDados);
			if(is_array($semDados)){
				echo json_encode($semDados, JSON_UNESCAPED_UNICODE);
				die();
			}
	
			echo json_encode($geradoDados, JSON_UNESCAPED_UNICODE);
	
			die();
	
		break;
	
	case 'AppApi':
		$retorno = $API003->auth($_GET['API_AUTH'], true);
	
		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$codigoApp = $_GET['codigoApp'];
		$json = executarCodigo($codigoApp);
		echo $json;
		die();
	break;
	
	default:
		if (!empty($g['r'][3])) {
			$API001->retorno404("{$g['r'][3]}");
		}

}

function extraFiltro(){
	$parametros = explode("?",$_SERVER["REQUEST_URI"]);
	$parametros = explode("&",$parametros[1]);

	$extraFiltro = array();
	foreach ($parametros as $key => $value) {
		$value = " $value ";
		if (!strpos($value,'DATAINICIAL') && !strpos($value,'DATAFINAL') && !strpos($value,'API_AUTH')) {
			$extra = explode("=",$value);
			$extraComIN = strpos($extra[1],"IN(");
			if($extraComIN !== false){
				// filtrar mais de uma informação no campo com IN
				$extraFiltro[] = " {$extra[0]} " . trim($extra[1]);
			} else {
				$extraFiltro[] = " {$extra[0]} = '" . trim($extra[1]) ."' ";
			}
		}
	}
	$extraFiltro = implode(" AND ", $extraFiltro);

	return $extraFiltro;
}
