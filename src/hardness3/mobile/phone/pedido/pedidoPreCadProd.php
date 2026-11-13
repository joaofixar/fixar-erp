<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : false;
	
// Adicionar
$temErro = '';
if (isset($_REQUEST['D001_Codigo_Produto'])) {
	$D001_Codigo_Produto = strtoupper(mysql_real_escape_string($_REQUEST['D001_Codigo_Produto']));
	$D001_Descricao_Produto = strtoupper(mysql_real_escape_string($_REQUEST['D001_Descricao_Produto']));
	$D001_D037_Id = strtoupper(mysql_real_escape_string($_REQUEST['D001_D037_Id']));
	$D001_D005_Id = strtoupper(mysql_real_escape_string($_REQUEST['D001_D005_Id']));
	$D049_D082_Id = strtoupper(mysql_real_escape_string($_REQUEST['D049_D082_Id']));
	$D001_Flag_Tipo = strtoupper(mysql_real_escape_string($_REQUEST['D001_Flag_Tipo']));
	$D001_Flag_Ativo = strtoupper(mysql_real_escape_string($_REQUEST['D001_Flag_Ativo']));
	$D041_Valor_Base = $_REQUEST['D041_Valor_Base']; 
	$D049_Valor_Preco_Tabela = $_REQUEST['D049_Valor_Preco_Tabela'];
	if(empty($D041_Valor_Base)){
		$script = <<<EOT
		alert('O campo Preço não pode ser vazio.');
EOT;
	}else{
		$sql = <<<EOT
			INSERT INTO D001 (D001_Codigo_Produto, 
								D001_Flag_Pre_Cadastro, 
								D001_Descricao_Produto, 
								D001_D037_Id,
								D001_D005_Id,
								D001_Flag_Tipo,
								D001_Flag_Ativo) 
								VALUES 
								('{$D001_Codigo_Produto}', 
								'S', 
								'{$D001_Descricao_Produto}', 
								'{$D001_D037_Id}',
								'{$D001_D005_Id}',
								'{$D001_Flag_Tipo}',
								'S');
EOT;
		mysql_query($sql);
		//Pega o últmo Id inserido
		$D001_Id = $g['mysqlLastId'];
		//Insert D049
		$D082_Id = '9';
		mysql_query("insert into D049 (D049_D001_Id,D049_D082_Id,D049_Valor_Preco_Tabela) values ({$D001_Id},{$D082_Id},'{$D049_Valor_Preco_Tabela}')");
		$D049_Id = $g['mysqlLastId'];
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		$return = $CAD002->preCadastroMarca($D049_Id,$D001_Id,$D041_Valor_Base,true);
		// Adiciona produto no Pedido/troca
		$sql = "SELECT D009_Id FROM D009 WHERE D009_D001_Id = '{$D001_Id}' AND D009_C004_Id = '{$g['empresaAtual']}';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		if ($tipo == 'troca') {
			$script = <<<EOT
				$.mobile.changePage( "./pedidoAddProdutoTroca.php?T005_Id={$T005_Id}&D001_Id={$D001_Id}&D009_Id={$row['D009_Id']}", { transition: 'fade'} );
EOT;
		} else {
			$script = <<<EOT
				$.mobile.changePage( "./pedidoAddProduto.php?T005_Id={$T005_Id}&D001_Id={$D001_Id}&D009_Id={$row['D009_Id']}", { transition: 'fade'} );
EOT;
		}
	}
}

// Campos
$sql = "SELECT D001_Codigo_Produto FROM D001 ORDER BY FLOOR(D001_Codigo_Produto) DESC LIMIT 1";
$resultado = mysql_query($sql);
$row = mysql_fetch_array($resultado);
$codigoProduto = $row['D001_Codigo_Produto'] + 1;

$D001_D037_Id = gProcessaSelect(gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade'));
$D001_D005_Id = gProcessaSelect(gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal', 'order by D005_Classificacao_Fiscal'), true);
$D049_D082_Id = gProcessaSelect(gGeraSelect('D082', 'D082_Id', 'D082_Marca', ''));
$D001_Flag_Tipo = array(
	array('title' => 'Produzido', 'value'=>'PRODUZIDO', 'selected'=>true),
	array('title' => 'Comprado', 'value' =>'COMPRADO')
);
$D001_Flag_Tipo = gProcessaSelect($D001_Flag_Tipo);
$D001_Flag_Ativo = array(
	array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
	array('title' => 'Não', 'value' =>'N')
);
$D001_Flag_Ativo = gProcessaSelect($D001_Flag_Ativo);

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoPreCadProd">
EOT;
include('mobile/tablet/pedido/inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./pedidoPreCadProd.php?T005_Id={$T005_Id}&tipo={$tipo}" method="post" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D001_Codigo_Produto">
	                                Código Produto
	                            </label>
	                            <input name="D001_Codigo_Produto" readonly="readonly" id="D001_Codigo_Produto" value="{$codigoProduto}" placeholder="" type="text" />
	                        </fieldset>
	                    </div>
	             	</div>
                 </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D001_Descricao_Produto" style="width:9%;">
	                                Descrição
	                            </label>
	                            <input name="D001_Descricao_Produto" id="D001_Descricao_Produto" value="{$_REQUEST['descricaoProduto']}" placeholder="" type="text" style="width:60%;" />
	                        </fieldset>
	                    </div>
	             <div class="ui-grid-a">
                 	<div class="ui-block-a">
                 		<div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D049_Valor_Preco_Tabela">
	                                Custo
	                            </label>
	                            <input name="D049_Valor_Preco_Tabela" id="D049_Valor_Preco_Tabela" value="" placeholder="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D041_Valor_Base">
	                                Preço
	                            </label>
	                            <input name="D041_Valor_Base" id="D041_Valor_Base" value="" placeholder="" type="text" />
	                        </fieldset>
	                    </div>
	                    <!--
	                    <div data-role="fieldcontain">
	                        <label for="D001_D037_Id">
								Unidade
	                        </label>
							<select name="D001_D037_Id" id="D001_D037_Id">
								{$D001_D037_Id}
		                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <label for="D001_D005_Id">
								NCM
	                        </label>
							<select name="D001_D005_Id" id="D001_D005_Id">
								{$D001_D005_Id}
		                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <label for="D049_D082_Id">
								Marca
	                        </label>
							<select name="D049_D082_Id" id="D049_D082_Id">
								{$D049_D082_Id}
		                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <label for="D001_Flag_Tipo">
								Tipo
	                        </label>
							<select name="D001_Flag_Tipo" id="D001_Flag_Tipo">
								{$D001_Flag_Tipo}
		                    </select>
	                    </div>
	                    -->
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="fade" href="./pedido.php?T005_Id={$T005_Id}" data-ajax="false">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                    	<input value="Adicionar" type="submit" />
                    </div>
                </div>
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pagepedidoPreCadProd", "pageinit");
    	$( document ).delegate("#pagepedidoPreCadProd", "pageinit", function() {
EOT;

echo <<<EOT
			{$script}
    	});
    </script>
EOT;
