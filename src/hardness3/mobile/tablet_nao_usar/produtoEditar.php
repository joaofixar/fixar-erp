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


// Alterar
$temErro = '';
if (isset($_REQUEST['preco'])) {
	$D041_ValorBase = $_REQUEST['preco'];
	$sqlUpdate = <<<EOT
		UPDATE D041
			SET D041_Valor_Base = {$D041_ValorBase}
			WHERE D041_Id = {$row['D041_Id']}
EOT;
	mysql_query($sqlUpdate);
	$script .="alert('Alteração realizada com sucesso!');";
	$script .= <<<EOT
  		$.mobile.changePage( "./produtos.php?T003_Id={$T003_Id}", { transition: 'slide'} );
EOT;
}

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageprodutoEditar">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./produtoEditar.php?T003_Id={$T003_Id}&D009_Id={$D009_Id}" method="post" data-ajax="false">
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
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './produtos.php?T003_Id={$T003_Id}', { transition: 'slide'} );">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        <input value="Alterar" type="submit" />
                    </div>
                </div>
                </form>
                </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageprodutoEditar", "pageinit");
    	$( document ).delegate("#pageprodutoEditar", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
