<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

$script = "";
if (isset($_REQUEST['D024_Id'])) {
	$D024_Id = mysql_real_escape_string($_REQUEST['D024_Id']);
	if (isset($_REQUEST['remover'])) {
		$sql = "UPDATE T005 SET T005_D024_Id = '' WHERE T005_Id = '{$T005_Id}';";
		mysql_query($sql);
	} else {

        // BUSCA A CFOP PADRAO PARA O ORCAMENTO
		$sql = "SELECT D024_D006_Id, 
				       D024_D018_Id,
				       D024_Flag_Revenda_Consumidor,
				       D024_Flag_Contribuinte
				  FROM D024 WHERE D024_Id=" . $D024_Id;
		$result = mysql_query($sql);
		$aD024 = mysql_fetch_assoc($result);
		
		echo mysql_error();
		
		$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, 
				       C004_CFOP_Venda_Fora_Estado, 
				       C004_CFOP_ST_Venda_Fora_Estado, 
				       C004_CFOP_ST_Venda_Dentro_Estado, 
				       C004_CFOP_CONSUMO_Venda_Fora_Estado, 
				       C004_CFOP_CONSUMO_Venda_Dentro_Estado, 
				       C004_D018_Id 
				  FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
		$result = mysql_query($sql);
		$aC004 = mysql_fetch_assoc($result);
		
		echo mysql_error();
		
		$D006_Id = $aD024['D024_D006_Id'];
		
        if ($D006_Id<=0)
        {
			if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
			{
			    $D006_Id = $aC004['C004_CFOP_Venda_Fora_Estado'];
			}
			else
			{       
			    $D006_Id = $aC004['C004_CFOP_Venda_Dentro_Estado'];
			}	
			if ($aD024['D024_Flag_Contribuinte'] == '1')
			{
				if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
				{
				    $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Fora_Estado'];
				}
				else
				{
				    $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Dentro_Estado'];
				}	
			}
		}
		// FIM BUSCA CFOP

		// GRAVA CLIENTE E CFOP NO ORCAMENTO
		$sql = "UPDATE T005 SET T005_D006_Id = '{$D006_Id}', T005_D024_Id = '{$D024_Id}' WHERE T005_Id = '{$T005_Id}';";
		mysql_query($sql);

		// GRAVA CFOP NOS ITENS DO ORCAMENTO
		$sql = "UPDATE T006 SET T006_D006_Id = '{$D006_Id}' WHERE T006_T005_Id = '{$T005_Id}';";
		mysql_query($sql);
		
	}
}

if (isset($_REQUEST['D116_Id'])) {
	$D116_Id = mysql_real_escape_string($_REQUEST['D116_Id']);
	$sql = "UPDATE T005 SET T005_D116_Id = '{$D116_Id}' WHERE T005_Id = '{$T005_Id}';";
	mysql_query($sql);
}
if (isset($_REQUEST['D013_Id'])) {
	$D013_Id = mysql_real_escape_string($_REQUEST['D013_Id']);
	$sql = "UPDATE T005 SET T005_D013_Id = '{$D013_Id}' WHERE T005_Id = '{$T005_Id}';";
	mysql_query($sql);
}

if (isset($_REQUEST['T005_Observacao'])) {
	$_REQUEST['T005_Observacao'] = mysql_real_escape_string($_REQUEST['T005_Observacao']);
	$_REQUEST['T005_Hora_Inicio_Servico'] = mysql_real_escape_string($_REQUEST['T005_Hora_Inicio_Servico']);
	$_REQUEST['T005_Hora_Fim_Servico'] = mysql_real_escape_string($_REQUEST['T005_Hora_Fim_Servico']);
	$sql = "UPDATE T005 SET T005_Observacao = '{$_REQUEST['T005_Observacao']}',
					T005_Hora_Inicio_Servico = '{$_REQUEST['T005_Hora_Inicio_Servico']}',
					T005_Hora_Fim_Servico = '{$_REQUEST['T005_Hora_Fim_Servico']}',
					T005_Flag_Aguardando_Entrega = '{$_REQUEST['T005_Flag_Aguardando_Entrega']}'
					WHERE T005_Id = '{$T005_Id}'";
	mysql_query($sql);
	$uniqId=uniqid();
	$script = "alert('Dados salvos com sucesso!'); ";
	$script .= <<<EOT
	$.mobile.changePage( './clientes.php?T005_Id={$T005_Id}&refresh={$uniqId}', { transition: 'fade'} );
EOT;
}
//Select campo "Cliente aguardando Entrega?"
if($T005['T005_Flag_Aguardando_Entrega'] == 'S'){
	$Options = <<<EOT
		<option value='S' selected='selected'>Sim</option>
		<option value='N'>Não</option>
EOT;
}else{
	$Options = <<<EOT
		<option value='S'>Sim</option>
		<option value='N' selected='selected'>Não</option>
EOT;
	
}

include('inc.conf.php');

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageClientes">
EOT;
$etapa = 3;
include('inc.header.bar.php');
echo <<<EOT
	<div data-role="content">
       	<div data-role="collapsible" data-collapsed="false" data-theme="c" data-content-theme="c">
			<h3>Clientes</h3>
				<div class="ui-grid-a">
EOT;
if (empty($T005['T005_D024_Id'])) {
	echo <<<EOT
					<div class="ui-block-a" style="margin-top: 13px;">
						<input placeholder="Digite o Nome do cliente ou Placa do Veículo ou CPF" data-type="search" class="ui-input-text ui-body-c" id="procurarCliente">
					</div>
					<div class="ui-block-b">
						<a data-inline="true" data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" onclick="$.mobile.changePage( './clientesProcurar.php?T005_Id={$T005_Id}&pesquisa=' + encodeURIComponent($('#procurarCliente').val()), { transition: 'slide'} );">
			            	Procurar
				        </a>
				        <a data-inline="true" data-role="button" data-transition="fade" data-icon="plus" data-iconpos="left" href="./clientesPreCadastro.php?T005_Id={$T005_Id}" data-ajax="false">
			            	Novo Cliente
				        </a>
					</div>
EOT;
} else {
	echo <<<EOT
					<div class="ui-block-a" style="margin-top: 13px;">
						<input class="ui-input-text ui-body-c" disabled="disabled" style="opacity: 1; text-transform:none;" value="{$T005['D024_Nome_Empresa']}">
					</div>
					<div class="ui-block-b">
						<a data-inline="true" data-role="button" data-transition="fade" data-icon="gear" data-iconpos="left" href="./clientesPreCadastro.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}&update=true" data-ajax="false">
			            	Editar Cliente
				        </a>
						<a data-inline="true" data-role="button" data-transition="fade" data-icon="delete" data-iconpos="left" href="./clientes.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}&remover=true" data-ajax="false">
			            	Remover
				        </a>
					</div>
EOT;
}
echo <<<EOT
				</div>
		</div>
		<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c">
			<h3>Observação e Previsão entrega veículo</h3>
			<form action="clientes.php?T005_Id={$T005_Id}" method="POST" data-ajax="false">	
				<div data-role="fieldcontain">
					<label for="T005_Observacao" style="width: 10%;">
	                    <h3>Observação</h3>
		            </label>
					<textarea name="T005_Observacao" id="T005_Observacao">{$T005['T005_Observacao']}</textarea>
				</div>
				<h3 style="padding-top:2%;">Previsão entrega veículo</h>
				<div data-role="fieldcontain">
					<label for="T005_Hora_Inicio_Servico" style="width: 16%;">
	                    Hora Início
		            </label>
					<input id="T005_Hora_Inicio_Servico" name="T005_Hora_Inicio_Servico" placeholder="" value="{$T005['T005_Hora_Inicio_Servico']}" type="text"  />
				</div>
				<div data-role="fieldcontain">
					<label for="T005_Hora_Fim_Servico" style="width: 16%;">
	                    Hora Fim
		            </label>
					<input id="T005_Hora_Fim_Servico" name="T005_Hora_Fim_Servico" placeholder="" value="{$T005['T005_Hora_Fim_Servico']}" type="text" />
				</div>
				<div data-role="fieldcontain">
					<label for="T005_Flag_Aguardando_Entrega" style="width: 16%;">
	                    Cliente Aguardando?
		            </label>
		            <select data-native-menu="true" name="T005_Flag_Aguardando_Entrega" id="T005_Flag_Aguardando_Entrega">
		                {$Options}
		            </select>
		        </div>
		            <input id="textinput3" data-inline="true" type="submit" data-role="button" data-transition="fade" data-icon="more" data-iconpos="left" data-ajax="false" value="Salvar" />
			</form>
		</div>
		<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c">
			<h3>Veículos</h3>
				<div class="ui-grid-a">
					<div class="ui-block-a">
EOT;
if (empty($T005['T005_D024_Id'])) {
	echo <<<EOT
						<h3>Selecione um cliente.</h3>
EOT;
} else {
	$sql = "SELECT D116_Id,D116_Placa,D116_Modelo,D116_Cor,D133_Modelo,D134_Cor FROM D116 LEFT JOIN D133 ON D133_Id=D116_D133_Id LEFT JOIN D134 ON D134_Id=D116_D134_Id WHERE D116_D024_Id='{$T005['T005_D024_Id']}' AND D116_Placa != '' AND D116_Placa IS NOT NULL ORDER BY D116_Id;";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$checked = ($T005['T005_D116_Id'] == $row['D116_Id']) ? ' checked="checked"' : '';
		echo <<<EOT
						<input type="radio" id="T005_D116_Id-{$row['D116_Id']}" name="T005_D116_Id-{$row['D116_Id']}" value="{$row['D116_Id']}"{$checked} onclick="$.mobile.changePage( './clientes.php?T005_Id={$T005_Id}&D116_Id={$row['D116_Id']}', { transition: 'fade'} );" />
						<label for="T005_D116_Id-{$row['D116_Id']}">{$row['D116_Placa']} - {$row['D116_Modelo']} - {$row['D116_Cor']}</label> 
EOT;
	}
	echo <<<EOT
						<a data-inline="true" data-role="button" data-transition="fade" data-icon="plus" data-iconpos="left" href="./clientesAddVeiculo.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}" data-ajax="false">
							Novo Veículo
						</a>
EOT;
}
echo <<<EOT
					</div>
					<div class="ui-block-b">
					</div>
				</div>
		</div>
		<div data-role="collapsible" data-collapsed="true" data-theme="c" data-content-theme="c">
			<h3>Contatos</h3>
					<div class="ui-grid-a">
					<div class="ui-block-a">
EOT;
if (empty($T005['T005_D024_Id'])) {
	echo <<<EOT
						<h3>Selecione um cliente.</h3>
EOT;
} else {
	$sql = "SELECT D013_Id,D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1 FROM D013 where D013_D024_Id='{$T005['T005_D024_Id']}' ORDER BY D013_Id;";
	$res = mysql_query($sql);
	
	$tmp = 0;
	while ($row = mysql_fetch_assoc($res)) {
		$checked = ($T005['T005_D013_Id'] == $row['D013_Id']) ? ' checked="checked"' : '';
		echo <<<EOT
						<input type="radio" id="T005_D013_Id-{$row['D013_Id']}" name="T005_D013_Id-{$row['D013_Id']}" value="{$row['D013_Id']}"{$checked} onclick="$.mobile.changePage( './clientes.php?T005_Id={$T005_Id}&D013_Id={$row['D013_Id']}', { transition: 'fade'} );" />
						<label for="T005_D013_Id-{$row['D013_Id']}">{$row['D013_Nome_Contato']} - ({$row['D013_DDD_Telefone_1']}){$row['D013_Telefone_1']}</label> 
EOT;
	}
	echo <<<EOT
						<a data-inline="true" data-role="button" data-transition="fade" data-icon="plus" data-iconpos="left" href="./clientesAddContato.php?T005_Id={$T005_Id}&D024_Id={$T005['T005_D024_Id']}" data-ajax="false">
							Novo Contato
						</a>
EOT;
}
echo <<<EOT
					</div>
					<div class="ui-block-b">
					</div>
		</div>		
	</div>
	</div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageClientes", "pageinit");
    	$( document ).delegate("#pageClientes", "pageinit", function() {
			{$script}
    	});
    </script>
EOT;
