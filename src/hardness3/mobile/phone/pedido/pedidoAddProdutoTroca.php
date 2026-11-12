<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;

// Copiado de ven001/form_func-ajax/atualizarCamposProdutos/
$sql =	"SELECT T005_D024_Id from T005 WHERE T005_Id = '{$T005_Id}'";
$resultado = mysql_query($sql);
$row = mysql_fetch_assoc($resultado);

$sql = <<<EOT
SELECT  *
	   	from D001
	left join D049 on D049_D001_Id=D001_Id
	left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T005_D024_Id']}'
	left join D009 on D009_D049_Id=D049_Id
	left join D082 on D082_Id=D049_D082_Id
	left join D041 on D041_D049_Id=D049_Id
	left join D005 on D005_Id=D001_D005_Id
	   	WHERE D009_Id = '{$D009_Id}'
EOT;

$resultado = mysql_query($sql);
$row = mysql_fetch_assoc($resultado);
//Verifica se o valor base é zero, para deixar o input em branco
$row['D041_Valor_Base'] = number_format($row['D041_Valor_Base'],2,',','.');
$row['D041_Valor_Base'] = ($row['D041_Valor_Base'] == "0,00" ? "" : $row['D041_Valor_Base']);
// Aliquota IPI
//$row['D005_Aliquota_IPI'] = number_format($row['D005_Aliquota_IPI'],2,',','.');
$cClausula =" select *, ";
$cClausula.=" D053_ST_ICMS_Interno, 
			  D053_ST_ICMS_Inter_Estadual, 
			  D053_Flag_ST, 
			  D018_Id, 
			  C004_D018_Id, 
			  C004_CFOP_ST_Venda_Dentro_Estado, 
			  C004_CFOP_ST_Venda_Fora_Estado,
			  C004_CFOP_CONSUMO_Venda_Dentro_Estado,
			  C004_CFOP_CONSUMO_Venda_Fora_Estado,
			  D024_Flag_Contribuinte ";
$cClausula.=" from T005 ";
$cClausula.=" left join D024 on D024_Id=T005_D024_Id ";
$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T005_C004_Id ";
$cClausula.=" left join C004 on C004_Id=T005_C004_Id ";
$cClausula.=" where T005_Id='{$T005_Id}'";
$T005=mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{
   return "$cErro/p/p$cClausula";
   exit;
}
$mT005=mysql_fetch_array($T005);
$sql = <<<EOT
	SELECT 
		D009_Id,
		D009_IPI('{$mT005['T005_C004_Id']}',D009_Id,'{$mT005['T005_D024_Id']}','{$mT005['T005_D006_Id']}','1','{$mT005['T005_Flag_Acp']}') as Aliquota_IPI 
	FROM 
		D009 
	where D009_Id='{$D009_Id}'
EOT;
$result = mysql_query($sql);
$row2 = mysql_fetch_assoc($result);
$row['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');

// Imagem
$sql = "SELECT * FROM T172 WHERE (T172_D001_Id = '{$D001_Id}') LIMIT 0,1;";
$res3 = mysql_query($sql);
if (mysql_numrows($res3) > 0) {
	$row3 = mysql_fetch_assoc($res3);
	$idFoto = $row3['T172_Id'];
	$idProduto = $D001_Id;
	$pathinfo = pathinfo($row3['T172_Nome_Arquivo']);
	$cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
	$cArquivoWeb = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
	if (file_exists($cArquivo))
	{
		$image = "<img src='{$cArquivoWeb}' style='max-width: 300px; max-height: 300px;' />";
	}
} else {
	$image = '';
}

// Adicionar
$temErro = '';
if (isset($_REQUEST['T138_Quantidade'])) {
	require_once("bibliotecas/classes/VEN002.php");
	$VEN002 = new VEN002();
	$row['T138_Quantidade'] = strtoupper($_REQUEST['T138_Quantidade']);
	$row['T138_Observacao'] = strtoupper($_REQUEST['T138_Observacao']);
	$row['T138_Valor_Preco_Unitario'] = strtoupper($_REQUEST['T138_Valor_Preco_Unitario']);
	//$row['T138_Valor_Preco_Unitario'] = gCorrigeNumeroInverte($row['T138_Valor_Preco_Unitario']);
	$row['T138_Operacao'] = strtoupper($_REQUEST['T138_Operacao']);
	$row['T138_T005_Id'] = $T005_Id;
	$row['T138_C004_Id'] = $g['empresaAtual'];
	$row['T138_D009_Id'] = $D009_Id;
	$form = new formWrap($row);
	$retorno = $VEN002->incluirProdutoTroca($form);
	if ($retorno !== true) {
		$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
	} else {
		$script = <<<EOT
  			$.mobile.changePage( "./pedido.php?T005_Id={$T005_Id}", { transition: 'slide'} );
EOT;
	}
}

$T138_Operacao = array(
	array('title' => 'Parte Pagamento', 'value' => 'P', 'selected' => true),
	array('title' => 'Troca', 'value' => 'T'),
);
$T138_Operacao = gProcessaSelect($T138_Operacao);

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoAddProdutoTroca">
EOT;
include('mobile/tablet/pedido/inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./pedidoAddProdutoTroca.php?T005_Id={$T005_Id}&D009_Id={$D009_Id}" method="post" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput3">
	                                Produto
	                            </label>
	                            <input id="textinput3" placeholder="" value="{$row['D001_Codigo_Produto']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput4">
	                                Marca
	                            </label>
	                            <input id="textinput4" placeholder="" value="{$row['D082_Marca']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="descricao">
	                                Descrição
	                            </label>
	                            <input name="descricao" id="descricao" placeholder="" value="{$row['D001_Descricao_Produto']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T138_Quantidade">
	                                Quantidade
	                            </label>
	                            <input name="T138_Quantidade" id="T138_Quantidade" placeholder="" value="1" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T138_Valor_Preco_Unitario">
	                                Valor
	                            </label>
	                            <input name="T138_Valor_Preco_Unitario" id="T138_Valor_Preco_Unitario" placeholder="" value="{$row['D041_Valor_Base']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <label for="T138_Operacao">
								Operação
	                        </label>
							<select name="T138_Operacao" id="T138_Operacao">
								{$T138_Operacao}
		                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T138_Observacao">
	                                Observação
	                            </label>
	                            <input name="T138_Observacao" id="T138_Observacao" placeholder="" value="" type="text" />
	                        </fieldset>
	                    </div>
	                </div>
	                <div class="ui-block-b" style="width: 40%; text-align: center;">
	                	{$image}
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}', { transition: 'slide'} );">
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
    	$( document ).undelegate("#pagepedidoAddProdutoTroca", "pageinit");
    	$( document ).delegate("#pagepedidoAddProdutoTroca", "pageinit", function() {
EOT;

echo <<<EOT
			{$script}
    	});
    </script>
EOT;
