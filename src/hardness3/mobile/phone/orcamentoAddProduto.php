<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;

// Copiado de ven001/form_func-ajax/atualizarCamposProdutos/
$sql =	"SELECT T003_D024_Id from T003 WHERE T003_Id = '{$T003_Id}'";
$resultado = mysql_query($sql);
$row = mysql_fetch_assoc($resultado);

$sql = <<<EOT
SELECT  *
	   	from D001
	left join D049 on D049_D001_Id=D001_Id
	left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T003_D024_Id']}'
	left join D009 on D009_D049_Id=D049_Id
	left join D082 on D082_Id=D049_D082_Id
	left join D041 on D041_D049_Id=D049_Id
	left join D005 on D005_Id=D001_D005_Id
	   	WHERE D009_Id = '{$D009_Id}'
EOT;

$resultado = mysql_query($sql);
$row = mysql_fetch_assoc($resultado);
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
$cClausula.=" from T003 ";
$cClausula.=" left join D024 on D024_Id=T003_D024_Id ";
$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T003_C004_Id ";
$cClausula.=" left join C004 on C004_Id=T003_C004_Id ";
$cClausula.=" where T003_Id='{$T003_Id}'";
$T003=mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{
   return "$cErro/p/p$cClausula";
   exit;
}
$mT003=mysql_fetch_array($T003);
$sql = <<<EOT
	SELECT 
		D009_Id,
		D009_IPI('{$mT003['T003_C004_Id']}',D009_Id,'{$mT003['T003_D024_Id']}','{$mT003['T003_D006_Id']}','1','{$mT003['T003_Flag_Acp']}') as Aliquota_IPI 
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
if (isset($_REQUEST['descricao'])) {
	require_once("bibliotecas/classes/VEN001.php");
	$VEN001 = new VEN001();
		$row['T004_D009_Id'] = $D009_Id;
		$row['T004_T003_Id'] = $T003_Id;
		$row['T004_Descricao_Produto'] = strtoupper($_REQUEST['descricao']);
		$row['T004_Quantidade'] = $_REQUEST['quantidade'];
		$row['T004_Valor_Preco_Sem_Desconto_Unitario'] = $_REQUEST['preco'];
		$form = new formWrap($row);
		$retorno = $VEN001->incluirProduto($form);
		if ($retorno !== true) {
			$temErro = '<h3 style="text-align: center;">'.$retorno.'</h3>';
		} else {
			$script = <<<EOT
  				$.mobile.changePage( "./orcamento.php?T003_Id={$T003_Id}", { transition: 'slide'} );
EOT;
		}
	
}
$row['D001_Descricao_Produto'] = (isset($_REQUEST['descricao']) ? $_REQUEST['descricao'] : $row['D001_Descricao_Produto']); 
$qtdeProduto = (isset($_REQUEST['descricao']) ? $_REQUEST['quantidade'] : '1'); 

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageorcamentoAddProduto">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./orcamentoAddProduto.php?T003_Id={$T003_Id}&D009_Id={$D009_Id}" method="post" data-ajax="false">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput3" style="width:10%;">
	                                Produto
	                            </label>
	                            <input id="textinput3" placeholder="" value="{$row['D001_Codigo_Produto']}" type="text" style="opacity: 0.5;width:30%;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput4" style="width:10%;">
	                                Marca
	                            </label>
	                            <input id="textinput4" placeholder="" value="{$row['D082_Marca']}" type="text" style="opacity: 0.5;width:30%;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="descricao" style="width:10%;">
	                                Descrição
	                            </label>
	                            <input name="descricao" id="descricao" placeholder="" value="{$row['D001_Descricao_Produto']}" type="text" style="width:80%;"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="quantidade" style="width:10%;">
	                                Quantidade
	                            </label>
	                            <input name="quantidade" id="quantidade" placeholder="" value="{$qtdeProduto}" type="text" style="width:30%;"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="preco" style="width:10%;">
	                                Preço
	                            </label>
	                            <input name="preco" id="preco" placeholder="" value="{$row['D041_Valor_Base']}"  type="text" style="width:30%;"/>
	                        </fieldset>
	                    </div>
	                <!--
	                <div class="ui-block-b" style="width: 40%; text-align: center;">
	                	{$image}
	                </div>
	                -->
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}', { transition: 'slide'} );">
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
    	$( document ).undelegate("#pageorcamentoAddProduto", "pageinit");
    	$( document ).delegate("#pageorcamentoAddProduto", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
