<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;

$D116_Placa = isset($_REQUEST['D116_Placa']) ? strtoupper(mysql_real_escape_string($_REQUEST['D116_Placa'])) : '';
$D116_Modelo = isset($_REQUEST['D116_Modelo']) ? strtoupper(mysql_real_escape_string($_REQUEST['D116_Modelo'])) : '';
$D116_Cor = isset($_REQUEST['D116_Cor']) ? strtoupper(mysql_real_escape_string($_REQUEST['D116_Cor'])) : '';

// Adicionar
$temErro = '';
if (isset($_REQUEST['D116_Placa'])) {
	//$D116_D133_Id = strtoupper(mysql_real_escape_string($_REQUEST['D116_D133_Id']));
	//$D116_D134_Id = strtoupper(mysql_real_escape_string($_REQUEST['D116_D134_Id']));

	//Sql que gravava os campos de Modelo e Cor pré cadastrados, mas por enquanto serão dois campos text mesmo.
	//$sql = "INSERT INTO D116 (D116_D024_Id, D116_Placa, D116_D133_Id, D116_D134_Id) VALUES ('{$D024_Id}', '{$D116_Placa}', '{$D116_D133_Id}', '{$D116_D134_Id}');";

	if(!empty($D116_Placa) && !empty($D116_Modelo) && !empty($D116_Cor)){
		$sql = "INSERT INTO D116 (D116_D024_Id, D116_Placa, D116_Modelo, D116_Cor) VALUES ('{$D024_Id}', '{$D116_Placa}', '{$D116_Modelo}', '{$D116_Cor}');";
		mysql_query($sql);
		
		if($_REQUEST['D116_Placa'] != ''){
		$sql = "UPDATE T003 SET T003_D116_Id = '{$g['mysqlLastId']}' WHERE T003_Id = '{$T003_Id}';";
		mysql_query($sql);
		}
		$script = <<<EOT
			$.mobile.changePage( "./clientes.php?T003_Id={$T003_Id}", { transition: 'slide'} );
EOT;
	}else{
		$script = <<<EOT
			alert('Você deve informar a Placa, Modelo e Cor do Veículo!');
EOT;
	}
}

/*
//Define combobox do Modelo
	$sqlModelo = mysql_query("SELECT D133_Id,D133_Modelo FROM D133 order by D133_Modelo");
	$selectModelo = "";
	while($rowModelo = mysql_fetch_array($sqlModelo)){
			$selectModelo .= "<option value=\"{$rowModelo['D133_Id']}\">
								 {$rowModelo['D133_Modelo']}
							    </option>";
	}
//Define combobox da Cor
	$sqlCor = mysql_query("SELECT D134_Id,D134_Cor FROM D134 order by D134_Cor");
	$selectCor = "";
	while($rowCor = mysql_fetch_array($sqlCor)){
			$selectCor .= "<option value=\"{$rowCor['D134_Id']}\">
								 {$rowCor['D134_Cor']}
							  </option>";
	}
*/
	
include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageClientesAddVeiculo">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./clientesAddVeiculo.php?T003_Id={$T003_Id}&D024_Id={$D024_Id}" method="post" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D116_Placa">
	                                Placa
	                            </label>
	                            <input name="D116_Placa" id="D116_Placa" placeholder="" value="{$D116_Placa}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                            <label for="D116_Modelo">
	                                Modelo
	                            </label>
	                            <input name="D116_Modelo" id="D116_Modelo" placeholder="" value="{$D116_Modelo}" type="text" />
	                            <!--
	                            <select data-native-menu="true" name="D116_D133_Id" id="D116_D133_Id">
		                        	{$selectModelo}
		                    	</select>
		                    	-->
	                    </div>
	                    <div data-role="fieldcontain">
	                            <label for="D116_Cor">
	                                Cor
	                            </label>
	                            <input name="D116_Cor" id="D116_Cor" placeholder="" value="{$D116_Cor}" type="text" />
	                            <!--
	                            <select data-native-menu="true" name="D116_D134_Id" id="D116_D134_Id">
		                        	{$selectCor}
		                    	</select>
		                    	-->
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './clientes.php?T003_Id={$T003_Id}', { transition: 'slide'} );">
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
    	$( document ).undelegate("#pageClientesAddVeiculo", "pageinit");
    	$( document ).delegate("#pageClientesAddVeiculo", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
	</script>
EOT;
