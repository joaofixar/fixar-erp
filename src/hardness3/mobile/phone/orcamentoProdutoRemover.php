<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$T004_Id = isset($_REQUEST['T004_Id']) ? $_REQUEST['T004_Id'] : false;
$uniqId=uniqid();

echo <<<EOT
	<div data-role="dialog">
EOT;
	//Verifica se o item esta relacionado a alguma requisicao de compra
	$sqlT189 = <<<EOT
	SELECT * FROM T189
		LEFT JOIN T059 ON T059_T189_Id=T189_Id
	WHERE T189_T004_Id='{$T004_Id}'
EOT;
	$resT189 = mysql_query($sqlT189);
	$rowT189 = mysql_fetch_assoc($resT189);
	$rowsT189 = mysql_numrows($resT189);
	$flagTipoRequisicao = $rowT189['T189_Flag_Tipo'];
	if($rowsT189 <= 0 || $flagTipoRequisicao=='R'){
echo <<<EOT
		<div data-role="header" data-theme="d">
			<h1>Remover Produto?</h1>
		</div>
		<div data-role="content" data-theme="c">
			<p>Deseja remover este produto do orçamento?</p>
			<a data-role="button" data-theme="b" onclick="\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&T004_Id={$T004_Id}&refresh={$uniqId}&tipoRequisicao={$flagTipoRequisicao}', { transition: 'slide'} );">Sim</a>
			<a data-role="button" data-rel="back" data-theme="c">Não</a>    
		</div>
	</div>
EOT;
	}else{
echo <<<EOT
		<div data-role="header" data-theme="d">
			<h1>Não foi possível Remover o Produto</h1>
		</div>
		<div data-role="content" data-theme="c">
				<p>Não foi possivel excluir, este item possui uma requisição de compra!</p>
				<a data-role="button" data-rel="back" data-theme="c">Ok</a>    
			</div>
		</div>
		
EOT;
	}
