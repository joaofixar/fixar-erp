<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');
require_once("bibliotecas/classes/CAD001.php");

$update = isset($_REQUEST['update']) && $_REQUEST['update'] ? true : false;
$botaoEnviar = $update ? 'Alterar' : 'Adicionar';
$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : '';

$sqlD024 = "SELECT * FROM D024 WHERE D024_Id='{$D024_Id}'";
$resD024 = mysql_query($sqlD024);
$rowD024 = mysql_fetch_assoc($resD024);

//Define combobox D024_Flag_Pessoa_Fisica_Juridica
$selectFlagPessoa = "";
$selectFlagPessoa .= "<option value=\"F\" ".($rowD024['D024_Flag_Pessoa_Fisica_Juridica'] == "F" ? 'selected="selected"' : '').">Física</option>";
$selectFlagPessoa .= "<option value=\"J\" ".($rowD024['D024_Flag_Pessoa_Fisica_Juridica'] == "J" ? 'selected="selected"' : '').">Jurídica</option>";
$selectFlagPessoa .= "<option value=\"I\" ".($rowD024['D024_Flag_Pessoa_Fisica_Juridica'] == "I" ? 'selected="selected"' : '').">Internacional</option>";

//Define combobox D024_Flag_Pessoa_Fisica_Juridica
$selectFlagContribuinte = "";
$selectFlagContribuinte .= "<option value=\"1\" ".($rowD024['D024_Flag_Contribuinte'] == "1" ? 'selected="selected"' : '').">Consumidor Final</option>";
$selectFlagContribuinte .= "<option value=\"0\" ".($rowD024['D024_Flag_Contribuinte'] == "0" ? 'selected="selected"' : '').">Não contribuinte</option>";
$selectFlagContribuinte .= "<option value=\"2\" ".($rowD024['D024_Flag_Contribuinte'] == "2" ? 'selected="selected"' : '').">Revenda</option>";
$selectFlagContribuinte .= "<option value=\"3\" ".($rowD024['D024_Flag_Contribuinte'] == "3" ? 'selected="selected"' : '').">Produtor rural</option>";
$selectFlagContribuinte .= "<option value=\"4\" ".($rowD024['D024_Flag_Contribuinte'] == "4" ? 'selected="selected"' : '').">Simples</option>";
$selectFlagContribuinte .= "<option value=\"5\" ".($rowD024['D024_Flag_Contribuinte'] == "5" ? 'selected="selected"' : '').">Industria</option>";

//Define combobox D024_D018_Id
$sql = "SELECT * FROM D018 ORDER BY D018_UF";
$sqlEstado = mysql_query($sql);
$selectEstado = "";
while($rowEstado = mysql_fetch_array($sqlEstado)){
	if($rowEstado['D018_Id'] == $rowD024['D024_D018_Id']){
		$selectEstado .= "<option selected=\"selected\" value=\"{$rowEstado['D018_Id']}\">
							 {$rowEstado['D018_UF']}
						    </option>";
	}else{
		$selectEstado .= "<option value=\"{$rowEstado['D018_Id']}\">
							 {$rowEstado['D018_UF']}
						    </option>";
	}
}

if($rowD024['D024_D020_Id'] > 0){
	$sql = "SELECT * FROM D020 ORDER BY D020_Nome_Cidade";
	$sqlCidade = mysql_query($sql);
	$selectCidade = "";
	while($rowCidade = mysql_fetch_array($sqlCidade)){
		if($rowCidade['D020_Id'] == $rowD024['D024_D020_Id']){
			$selectCidade .= "<option selected=\"selected\" value=\"{$rowCidade['D020_Id']}\">
								 {$rowCidade['D020_Nome_Cidade']}
							    </option>";
		}else{
			$selectCidade .= "<option value=\"{$rowCidade['D020_Id']}\">
								 {$rowCidade['D020_Nome_Cidade']}
							    </option>";
		}
	}
}

//Corrige a Data Nascimento para View
$rowD024['D024_Data_Fundacao'] = (isset($rowD024['D024_Data_Fundacao'])) ? gCorrigeData($rowD024['D024_Data_Fundacao']) : "";

// Adicionar
$temErro = '';
if (isset($_REQUEST['D024_Nome_Empresa'])) {
	/*
	 * Usar restrição em todos os campos
	 * $array = array('D024_Nome_Empresa', 'D024_Nome_Fantasia');
	foreach ($array as $varTmp) {
		$$varTmp = strtoupper(mysql_real_escape_string($_REQUEST[$varTmp]));
		$erro = $g['conf']->restricaoIncluirUpdate($varTmp, $_REQUEST[$varTmp]);
		if ($erro !== true) {
			$temErro .= $erro . "<br />";
		}
	}*/
	$D024_Nome_Empresa = strtoupper(mysql_real_escape_string($_REQUEST['D024_Nome_Empresa']));
	$D024_Nome_Fantasia = strtoupper(mysql_real_escape_string($_REQUEST['D024_Nome_Fantasia']));
	$D024_Flag_Pessoa_Fisica_Juridica = strtoupper(mysql_real_escape_string($_REQUEST['D024_Flag_Pessoa_Fisica_Juridica']));
	$D024_Flag_Contribuinte = strtoupper(mysql_real_escape_string($_REQUEST['D024_Flag_Contribuinte']));
	$D024_E_Mail = mysql_real_escape_string($_REQUEST['D024_E_Mail']);
	$D024_Cnpj = strtoupper(mysql_real_escape_string($_REQUEST['D024_Cnpj']));
	$D024_Cpf = strtoupper(mysql_real_escape_string($_REQUEST['D024_Cpf']));
	$D024_Rg = strtoupper(mysql_real_escape_string($_REQUEST['D024_Rg']));
	$D024_Data_Fundacao = mysql_real_escape_string(gCorrigeDataInverte($_REQUEST['D024_Data_Fundacao']));
	$D024_DDD_Telefone_1 = strtoupper(mysql_real_escape_string($_REQUEST['D024_DDD_Telefone_1']));
	$D024_Telefone_1 = strtoupper(mysql_real_escape_string($_REQUEST['D024_Telefone_1']));
	$D024_DDD_Telefone_2 = strtoupper(mysql_real_escape_string($_REQUEST['D024_DDD_Telefone_2']));
	$D024_Telefone_2 = strtoupper(mysql_real_escape_string($_REQUEST['D024_Telefone_2']));
	$D024_Endereco = strtoupper(mysql_real_escape_string($_REQUEST['D024_Endereco']));
	$D024_Numero = strtoupper(mysql_real_escape_string($_REQUEST['D024_Numero']));
	$D024_Complemento = strtoupper(mysql_real_escape_string($_REQUEST['D024_Complemento']));
	$D024_D020_Id = strtoupper(mysql_real_escape_string($_REQUEST['D024_D020_Id']));
	$D024_D018_Id = strtoupper(mysql_real_escape_string($_REQUEST['D024_D018_Id']));
	$D024_Cep = strtoupper(mysql_real_escape_string($_REQUEST['D024_Cep']));
	$D024_Bairro = strtoupper(mysql_real_escape_string($_REQUEST['D024_Bairro']));
	$D024_Observacoes = strtoupper(mysql_real_escape_string($_REQUEST['D024_Observacoes']));
	$D024_Data_Cadastro = ($rowD024['D024_Data_Cadastro'] != '0000-00-00 00:00:00') ? $rowD024['D024_Data_Cadastro'] : date('Y-m-d');
	if(empty($temErro)){
		if ($update) {
			$sql = <<<EOT
			UPDATE D024 SET D024_Nome_Empresa='{$D024_Nome_Empresa}',
								D024_Nome_Fantasia='{$D024_Nome_Fantasia}', 
								D024_Flag_Pessoa_Fisica_Juridica='{$D024_Flag_Pessoa_Fisica_Juridica}', 
								D024_Flag_Contribuinte='{$D024_Flag_Contribuinte}',
								D024_E_Mail='{$D024_E_Mail}',
								D024_Cnpj='{$D024_Cnpj}',
								D024_Cpf='{$D024_Cpf}',
								D024_Rg='{$D024_Rg}',
								D024_Data_Fundacao='{$D024_Data_Fundacao}',
								D024_DDD_Telefone_1='{$D024_DDD_Telefone_1}',
								D024_Telefone_1='{$D024_Telefone_1}',
								D024_DDD_Telefone_2='{$D024_DDD_Telefone_2}',
								D024_Telefone_2='{$D024_Telefone_2}',
								D024_Endereco='{$D024_Endereco}',
								D024_Numero='{$D024_Numero}',
								D024_Complemento='{$D024_Complemento}',
								D024_D020_Id='{$D024_D020_Id}',
								D024_D018_Id='{$D024_D018_Id}',
								D024_Cep='{$D024_Cep}',
								D024_Bairro='{$D024_Bairro}',
								D024_Observacoes='{$D024_Observacoes}',
								D024_Data_Cadastro='{$D024_Data_Cadastro}',
								D024_Flag_Cliente='S',
								D024_Flag_Lista_Preco='1'
			WHERE D024_Id='{$D024_Id}'
EOT;
			mysql_query($sql);
			//Verifica se os dados obrigatórios estão preenchidos e altera flag pré cadastro para N
			$CAD001 = new CAD001();
			$CAD001->alterarClientePreCadastro($D024_Id);
			$script .= "alert('Alteração realizada com sucesso.');";
		} else {
			$sql = <<<EOT
				INSERT INTO D024 (D024_Nome_Empresa,
								D024_Nome_Fantasia,
								D024_Flag_Pessoa_Fisica_Juridica,
								D024_Flag_Contribuinte,
								D024_E_Mail,
								D024_Cnpj,
								D024_Cpf,
								D024_Rg,
								D024_Data_Fundacao,
								D024_DDD_Telefone_1,
								D024_Telefone_1,
								D024_DDD_Telefone_2,
								D024_Telefone_2,
								D024_Endereco,
								D024_Numero,
								D024_Complemento,
								D024_D020_Id,
								D024_D018_Id,
								D024_Cep,
								D024_Bairro,
								D024_Observacoes,
								D024_Data_Cadastro,
								D024_Flag_Cliente,
								D024_Flag_Lista_Preco,
								D024_Flag_Pre_Cadastro)
								VALUES
								('{$D024_Nome_Empresa}',
								'{$D024_Nome_Fantasia}',
								'{$D024_Flag_Pessoa_Fisica_Juridica}',
								'{$D024_Flag_Contribuinte}',
								'{$D024_E_Mail}',
								'{$D024_Cnpj}',
								'{$D024_Cpf}',
								'{$D024_Rg}',
								'{$D024_Data_Fundacao}',
								'{$D024_DDD_Telefone_1}',
								'{$D024_Telefone_1}',
								'{$D024_DDD_Telefone_2}',
								'{$D024_Telefone_2}',
								'{$D024_Endereco}',
								'{$D024_Numero}',
								'{$D024_Complemento}',
								'{$D024_D020_Id}',
								'{$D024_D018_Id}',
								'{$D024_Cep}',
								'{$D024_Bairro}',
								'{$D024_Observacoes}',
								CURDATE(),
								'S',
								'1',
								'S');
EOT;
			mysql_query($sql);
			$D024_Id = $g['mysqlLastId'];
			//Verifica se os dados obrigatórios estão preenchidos e altera flag pré cadastro para N
			$CAD001 = new CAD001();
			$CAD001->alterarClientePreCadastro($D024_Id);
			$script .= "alert('Cliente cadastrado com sucesso.');";
		//$sql = "UPDATE T003 SET T003_D024_Id = '{$g['mysqlLastId']}' WHERE T003_Id = '{$T003_Id}';";
		//mysql_query($sql);
		}
		if(isset($_REQUEST['acao']) && $_REQUEST['acao'] = 'caixa'){
			$script .= <<<EOT
				$.mobile.changePage( "./pedido/clientes.php?T005_Id={$_REQUEST['T005_Id']}{$acao}", { transition: 'fade'} );
EOT;
		} else {
			$script .= <<<EOT
				$.mobile.changePage( "./clientesLista.php", { transition: 'fade'} );
EOT;
		}
	}
}

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageClientesPreCadastro">
EOT;
include('inc.header.bar.php');

echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./clientesEditar.php?D024_Id={$D024_Id}&update={$update}{$acao}&T005_Id={$_REQUEST['T005_Id']}" method="post" data-ajax="false">
                 <div>
                 	<div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Nome_Empresa">
						            Nome
						        </label>
						        <input name="D024_Nome_Empresa" id="D024_Nome_Empresa" value="{$rowD024['D024_Nome_Empresa']}" placeholder="" value="" type="text" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Nome_Fantasia">
						            Fantasia
						        </label>
						        <input name="D024_Nome_Fantasia" id="D024_Nome_Fantasia" value="{$rowD024['D024_Nome_Fantasia']}" placeholder="" value="" type="text" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <label for="D024_Flag_Pessoa_Fisica_Juridica">
								Pessoa
						    </label>
							<select name="D024_Flag_Pessoa_Fisica_Juridica" id="D024_Flag_Pessoa_Fisica_Juridica">
								{$selectFlagPessoa}
						    </select>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <label for="D024_Flag_Contribuinte">
						        Tipo
						    </label>
						    <select data-native-menu="false" name="D024_Flag_Contribuinte" id="D024_Flag_Contribuinte">
						    	{$selectFlagContribuinte}
						    </select>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_E_Mail">
						            E-mail
						        </label>
						        <input name="D024_E_Mail" id="D024_E_Mail"  placeholder="" value="{$rowD024['D024_E_Mail']}" type="text" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Cnpj">
						            CNPJ
						        </label>
						        <input name="D024_Cnpj" id="D024_Cnpj" placeholder="" value="{$rowD024['D024_Cnpj']}" type="text" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Cpf">
						            CPF
						        </label>
						        <input name="D024_Cpf" id="D024_Cpf" placeholder="" value="{$rowD024['D024_Cpf']}" type="text" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Rg">
						            RG
						        </label>
						        <input name="D024_Rg" id="D024_Rg" placeholder="" value="{$rowD024['D024_Rg']}" type="text" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Data_Fundacao">
						            Nascimento
						        </label>
						        <div style="display: inline-block; width: 62%;">
						        <input name="D024_Data_Fundacao" id="D024_Data_Fundacao" value="{$rowD024['D024_Data_Fundacao']}" placeholder="" type="date" data-role="datebox" data-options='{"mode": "calbox", "dateFormat": "dd/mm/YYYY"}' />
						        </div>
						     </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup" style="margin-bottom: 4px;">
						        <label for="D024_DDD_Telefone_1" style="float:left; margin-top: 4px;">
						            Telefone 1
						        </label>
						       	<input name="D024_DDD_Telefone_1" id="D024_DDD_Telefone_1" placeholder="" value="{$rowD024['D024_DDD_Telefone_1']}" type="text" style="width:40px; float: left; margin-left: 10px;" />
						        <input name="D024_Telefone_1" id="D024_Telefone_1" placeholder="" value="{$rowD024['D024_Telefone_1']}" type="text" style="float: left; width: 100px; margin-left: 5px;" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup" style="margin-bottom: 4px;">
						        <label for="D024_DDD_Telefone_2" style="float:left; margin-top: 4px;">
						            Telefone 2
						        </label>
						       	<input name="D024_DDD_Telefone_2" id="D024_DDD_Telefone_2" placeholder="" value="{$rowD024['D024_DDD_Telefone_2']}" type="text" style="width:40px; float: left; margin-left: 10px;" />
						        <input name="D024_Telefone_2" id="D024_Telefone_2" placeholder="" value="{$rowD024['D024_Telefone_2']}" type="text" style="float: left; width: 100px; margin-left: 5px;" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Endereco" style="float: left;">
						            Endereço
						        </label>
						        <input name="D024_Endereco" id="D024_Endereco" placeholder="" value="{$rowD024['D024_Endereco']}" type="text" style="float: left;" />
						        <div style="clear: both;">
						        <label for="D024_Numero" style="float: left; margin-top: 8px;">
						        	Número
						        </label>
								<input name="D024_Numero" id="D024_Numero" placeholder="" value="{$rowD024['D024_Numero']}" type="text" style="float: left; margin-left: 10px; width: 40%; margin-top: 4px;" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Complemento">
						            Complemento
						        </label>
						        <input name="D024_Complemento" id="D024_Complemento" placeholder="" value="{$rowD024['D024_Complemento']}" type="text" style="float: left;" />
							</fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Bairro">
						            Bairro
						        </label>
						        <input name="D024_Bairro" id="D024_Bairro" placeholder="" value="{$rowD024['D024_Bairro']}" type="text" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
							<label for="D024_D018_Id">
							    Estado
							</label>
							<select data-native-menu="true" name="D024_D018_Id" id="D024_D018_Id" onchange="alteraEstado(this); return false;">
								{$selectEstado}
							</select>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <label for="D024_D020_Id">
						        Cidade
						    </label>
						    <select data-native-menu="true" name="D024_D020_Id" id="D024_D020_Id">
						    	{$selectCidade}
						    </select>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Cep">
						            CEP
						        </label>
						        <input name="D024_Cep" id="D024_Cep" placeholder="" value="{$rowD024['D024_Cep']}" type="text" />
						    </fieldset>
						</div>
						<div data-role="fieldcontain" style="padding: 2px 8px;">
						    <fieldset data-role="controlgroup">
						        <label for="D024_Observacoes">
						            Observação
						        </label>
						        <textarea placeholder="" name="D024_Observacoes" id="D024_Observacoes" value="{$rowD024['D024_Observacoes']}" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">{$rowD024['D024_Observacoes']}</textarea>
						    </fieldset>
						</div>
	                </div>
	                <div>
	                	<ul data-role="listview" style="margin-top: 10px; clear:both;">
	                		<li data-role="list-divider" role="heading">
			    				<table style="width: 100%";>
			    					<tr>
			    						<td style="width: 5%"; align="center">Contatos</td>
			    					</tr>
			    				</table>
		                    </li>
EOT;
			$sqlD013 = "SELECT D013_Id,D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1 FROM D013 where D013_D024_Id='{$D024_Id}' ORDER BY D013_Id;";
			$resD013 = mysql_query($sqlD013);
			while ($rowD013 = mysql_fetch_assoc($resD013)) {
echo <<<EOT

		                    <li data-theme="c" data-icon="false">
					    		{$rowD013['D013_Nome_Contato']} - ({$rowD013['D013_DDD_Telefone_1']}) {$rowD013['D013_Telefone_1']}
						    </li>
EOT;
			}
	if(isset($_REQUEST['acao'])){
		$acaoVoltar = "../phone/pedido/clientes.php?T005_Id={$_REQUEST['T005_Id']}&acao=caixa";
	} else {
		$acaoVoltar = "./clientesLista.php";
	}

echo <<<EOT

	                	</ul>
	                </div>
	            </div>
                <div class="ui-grid-a" style="margin-top: 15px;">
EOT;
	if($D024_Id > 0){
		echo <<<EOT
	                <div>
	                    <a data-role="button" data-transition="fade" onclick="$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Nova venda', headerClose: true, buttonPrompt: 'Selecione o tipo da venda', buttons : { 'Venda': { click: function () { $.mobile.changePage( './orcamento.php?T003_Id=new&D024_Id={$D024_Id}', { transition: 'fade'} ); }, icon: '', theme: 'c' }, 'Bonificação': { click: function () { $.mobile.changePage( './orcamento.php?T003_Id=new&D024_Id={$D024_Id}&T003_Flag_Destino_Produto=5', { transition: 'fade'} ); }, icon: '', theme: 'c' }, 'Troca': { click: function () { $.mobile.changePage( './orcamento.php?T003_Id=new&D024_Id={$D024_Id}&T003_Flag_Destino_Produto=6', { transition: 'fade'} ); }, icon: '', theme: 'c' } } });">
	                        Nova venda
	                    </a>
	                </div>
EOT;
	}
	echo <<<EOT
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="fade" onclick="\$.mobile.changePage('{$acaoVoltar}', { transition: 'fade'} );">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        <input value="{$botaoEnviar}" type="submit" onclick="clientesPreCadastroEnviar(); return false;"/>
                    </div>
                </div>
                </form>
            </div>
EOT;
$disableForm = "";
if (!isset($g['c029Ids'][103])) {
	$disableForm = "$('form').bind('submit',function(e){e.preventDefault();}); $('input').attr('disabled', 'disabled'); $('select').attr('disabled', 'disabled'); $('textarea').attr('disabled', 'disabled');";
}
echo <<<EOT
    </div>
    <script>
    	{$disableForm}

    	function clientesPreCadastroEnviar() {
    		$.ajax({
				url: 'clientesPreCadastro_validarAlteracao.php?callback=?',
				dataType: 'json',
				type: 'GET',
				cache: false,
				data: 'D024_Id={$D024_Id}&' + $("form").serialize(),
				success: function(data) {
					if (data.code == true) {
						$("form").submit();
					} else {
						alert(data.data);
					}
					return false;
				}
			});
			return false;
    	}

    	function alteraEstado(combobox){
    		$.getJSON('carregaCampoCidade.php?D024_D018_Id=' + encodeURIComponent(combobox.value) + '&callback=?', function(request) {
				if (request.code) {
					$('#D024_D020_Id').html(request.data);
				}
			});
    	}
    	$( document ).undelegate("#pageClientesPreCadastro", "pageinit");
    	$( document ).delegate("#pageClientesPreCadastro", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
