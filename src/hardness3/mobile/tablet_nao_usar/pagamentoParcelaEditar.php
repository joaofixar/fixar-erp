<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');
include('inc.header.php');

// Vars
$T089_Id = isset($_REQUEST['T089_Id']) ? $_REQUEST['T089_Id'] : false;
$sqlParcela = <<<EOT
	SELECT T089_Id, T089_Numero_Parcela, T089_Prazos_Dias, T089_Data_Vencimento, T089_Valor_Parcela, T089_D027_Id, D027_Portador, D027_Flag_Tipo FROM T089 left join D027 on D027_Id=T089_D027_Id WHERE T089_Id = '{$T089_Id}' LIMIT 0,100;
EOT;
$resParcela = mysql_query($sqlParcela);
$rowParcela = mysql_fetch_assoc($resParcela);
$rowParcela['T089_Data_Vencimento'] = gCorrigedata($rowParcela['T089_Data_Vencimento']);
$rowParcela['T089_Valor_Parcela'] = gCorrigeNumero($rowParcela['T089_Valor_Parcela']);


//Define combobox do Portador
	$sqlPortador = mysql_query("SELECT D027_Id,D027_Portador FROM D027 where D027_Flag_Prazo='S' order by D027_Portador");
	$selectPortador = "";
	while($rowPortador = mysql_fetch_array($sqlPortador)){
		if($rowPortador['D027_Id'] == $rowParcela['T089_D027_Id']){
			$selectPortador .= "<option selected=\"selected\" value=\"{$rowPortador['D027_Id']}\">
								 {$rowPortador['D027_Portador']}
							    </option>";
		}else{
			$selectPortador .= "<option value=\"{$rowPortador['D027_Id']}\">
								 {$rowPortador['D027_Portador']}
							    </option>";
		}
	}
     
//Define o formulário
if ($_REQUEST['prazosDias'] == true) {
	$nameInput = "T089_Prazos_Dias";
	$valueInput = $rowParcela['T089_Prazos_Dias'];
	$labelInput = "Prazo em dias";
} else if ($_REQUEST['valorParcela'] == true) {
	$nameInput = "T089_Valor_Parcela";
	//Deixar gCorrigeNumeroInverte() por enquanto, pois o Ipad formata automaticamente o campo, mas caso formate o campo via função, o Ipad nao lê o valor.
	$valueInput = gCorrigeNumeroInverte($rowParcela['T089_Valor_Parcela']);
	$labelInput = "Valor Parcela";
}
                                
if($_REQUEST['Portador'] == true)
{
	$nameInput = "T089_D027_Id";
	$valueInput = $rowParcela['D027_Portador'];
	$labelInput = "Portador";
	$input= <<<EOT
	<select data-native-menu="true" name="{$nameInput}" id="{$nameInput}">
		{$selectPortador}
	</select>
EOT;
}
elseif($_REQUEST['dataVencimento'] == true)
{
	$nameInput = "T089_Data_Vencimento";
	$valueInput = $rowParcela['T089_Data_Vencimento'];
	$labelInput = "Data de Vencimento";
	$input = <<<EOT
	<div style="display: inline-block; width: 60%;">
		<input name="{$nameInput}" id="{$nameInput}" value="{$valueInput}" placeholder="" type="date" data-role="datebox" data-options='{"mode": "calbox", "dateFormat": "dd/mm/YYYY"}' />
	</div>
EOT;
}
else
{
	$input= <<<EOT
	<input name="{$nameInput}" id="{$nameInput}" value="{$valueInput}" type="text" />
EOT;
}

echo <<<EOT
	<div data-role="dialog" id="pagamentoParcelaEditar">
		<div data-role="header" data-theme="d">
			<h1>Editar Parcela</h1>
		</div>
		<div data-role="content" data-theme="c">
			{$temErro}
			<form action="./pagamentoGrid.php?T089_Id={$T089_Id}&campoAlterado={$nameInput}&T003_Id={$T003_Id}" method="POST" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width: 60%;">
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="numeroParcelaExibir">
	                                {$labelInput}
	                            </label>
	                            	{$input}
	                            	<input name="alterarParcela" id="alterarParcela" value="alterarParcela" placeholder="" type="hidden"/>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="slide" data-rel="back">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        <input value="Alterar" type="submit" />
                    </div>
                </div>
                </form>    
		</div>
	</div>
	
	 <script>
    	$( document ).undelegate("#pagamentoParcelaEditar", "pageinit");
    	$( document ).delegate("#pagamentoParcelaEditar", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
