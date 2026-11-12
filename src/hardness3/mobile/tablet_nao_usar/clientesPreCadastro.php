<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');


$update = isset($_REQUEST['update']) && $_REQUEST['update'] ? true : false;
$botaoEnviar = $update ? '<input value="Alterar" type="submit" onclick="clientesPreCadastroEnviarAlterar(); return false;"/>' : '<input value="Adicionar" type="submit" onclick="clientesPreCadastroEnviar(); return false;"/>';

$T003['D024_Nome_Empresa'] = (isset($_REQUEST['descricaoCliente']) ?  $_REQUEST['descricaoCliente'] : $T003['D024_Nome_Empresa']);
$T003['D024_Nome_Fantasia'] = (isset($_REQUEST['descricaoCliente']) ?  $_REQUEST['descricaoCliente'] : $T003['D024_Nome_Fantasia']);

//Define combobox D024_Flag_Pessoa_Fisica_Juridica
	$selectFlagPessoa = "";
	
	
	$selectFlagPessoa .= "<option value=\"F\" ".($T003['D024_Flag_Pessoa_Fisica_Juridica'] == "F" ? 'selected="selected"' : '').">Física</option>";
	$selectFlagPessoa .= "<option value=\"J\" ".($T003['D024_Flag_Pessoa_Fisica_Juridica'] == "J" ? 'selected="selected"' : '').">Jurídica</option>";
	$selectFlagPessoa .= "<option value=\"I\" ".($T003['D024_Flag_Pessoa_Fisica_Juridica'] == "I" ? 'selected="selected"' : '').">Internacional</option>";
			
	//Define combobox D024_Flag_Pessoa_Fisica_Juridica
	$selectFlagContribuinte = "";
	$selectFlagContribuinte .= "<option value=\"1\" ".($T003['D024_Flag_Contribuinte'] == "1" ? 'selected="selected"' : '').">Consumidor Final</option>";
	$selectFlagContribuinte .= "<option value=\"0\" ".($T003['D024_Flag_Contribuinte'] == "0" ? 'selected="selected"' : '').">Não contribuinte</option>";
	$selectFlagContribuinte .= "<option value=\"2\" ".($T003['D024_Flag_Contribuinte'] == "2" ? 'selected="selected"' : '').">Revenda</option>";
	$selectFlagContribuinte .= "<option value=\"3\" ".($T003['D024_Flag_Contribuinte'] == "3" ? 'selected="selected"' : '').">Produtor rural</option>";
	$selectFlagContribuinte .= "<option value=\"4\" ".($T003['D024_Flag_Contribuinte'] == "4" ? 'selected="selected"' : '').">Simples</option>";
	$selectFlagContribuinte .= "<option value=\"5\" ".($T003['D024_Flag_Contribuinte'] == "5" ? 'selected="selected"' : '').">Industria</option>";

	$selectFlagPreCadastro  = "";
	$selectFlagPreCadastro .= "<option value=\"S\" ".($T003['D024_Flag_Pre_Cadastro'] == "S" ? 'selected="selected"' : '').">Sim</option>";
	$selectFlagPreCadastro .= "<option value=\"N\" ".($T003['D024_Flag_Pre_Cadastro'] == "N" ? 'selected="selected"' : '').">Não</option>";
			
	//Define combobox D024_Estado
	$sql = "SELECT * FROM D018 ORDER BY D018_UF";
	$sqlEstado = mysql_query($sql);
	$selectEstado = "";
	while($rowEstado = mysql_fetch_array($sqlEstado)){								 
		if($rowEstado['D018_Id'] == $T003['D024_D018_Id']){
			$selectEstado .= "<option selected=\"selected\" value=\"{$rowEstado['D018_Id']}\">
								 {$rowEstado['D018_UF']}
							    </option>";
		}else{
			$selectEstado .= "<option value=\"{$rowEstado['D018_Id']}\">
								 {$rowEstado['D018_UF']}
							    </option>";
		}
	}

	if($T003['D024_D020_Id'] > 0){
		$sql = "SELECT * FROM D020 WHERE D020_D018_Id = '{$T003['D024_D018_Id']}' ORDER BY D020_Nome_Cidade";
		$sqlCidade = mysql_query($sql);
		$selectCidade = "";
		while($rowCidade = mysql_fetch_array($sqlCidade)){								 
			if($rowCidade['D020_Id'] == $T003['D024_D020_Id']){
				$selectCidade .= "<option selected=\"selected\" value=\"{$rowCidade['D020_Id']}\">{$rowCidade['D020_Nome_Cidade']}
								    </option>";
			}else{
				$selectCidade .= "<option value=\"{$rowCidade['D020_Id']}\">{$rowCidade['D020_Nome_Cidade']}</option>";
			}
		}
	}	
		
//Corrige a Data Nascimento para View
$T003['D024_Data_Fundacao'] = (isset($T003['D024_Data_Fundacao'])) ? gCorrigeData($T003['D024_Data_Fundacao']) : "";

	
// Adicionar
$temErro = '';
if (isset($_REQUEST['D024_Nome_Empresa'])) { 
	/*
	 * Usar restrição em todos os campos
	 * $array = array('D024_Nome_Empresa', 'D024_Nome_Fantasia');
}
	foreach ($array as $varTmp) {
		$$varTmp = strtoupper(mysql_real_escape_string($_REQUEST[$varTmp]));
		$erro = $g['conf']->restricaoIncluirUpdate($varTmp, $_REQUEST[$varTmp]);
		if ($erro !== true) {
			$temErro .= $erro . "<br />"; 
		}	
		*/

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
	$D024_Flag_Pre_Cadastro = strtoupper(mysql_real_escape_string($_REQUEST['D024_Flag_Pre_Cadastro']));
	
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
								D024_Flag_Pre_Cadastro='{$D024_Flag_Pre_Cadastro}'
			WHERE D024_Id='{$T003['T003_D024_Id']}'
EOT;
	mysql_query($sql);
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
								'S');
EOT;
		mysql_query($sql);
		$sql = "UPDATE T003 SET T003_D024_Id = '{$g['mysqlLastId']}' WHERE T003_Id = '{$T003_Id}';";
		mysql_query($sql);
	}
		$script = <<<EOT
		$.mobile.changePage( "./clientes.php?T003_Id={$T003_Id}", { transition: 'fade'} );
EOT;
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
                 <form action="./clientesPreCadastro.php?T003_Id={$T003_Id}&update={$update}" method="post" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a" style="width:60%;">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Nome_Empresa">
	                                Nome
	                            </label>
	                            <input name="D024_Nome_Empresa" id="D024_Nome_Empresa" value="{$T003['D024_Nome_Empresa']}" placeholder="" value="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Nome_Fantasia">
	                                Fantasia
	                            </label>
	                            <input name="D024_Nome_Fantasia" id="D024_Nome_Fantasia" value="{$T003['D024_Nome_Fantasia']}" placeholder="" value="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <label for="D024_Flag_Pessoa_Fisica_Juridica">
								Pessoa
	                        </label>
							<select name="D024_Flag_Pessoa_Fisica_Juridica" id="D024_Flag_Pessoa_Fisica_Juridica">
								{$selectFlagPessoa}
		                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <label for="D024_Flag_Contribuinte">
	                            Tipo
	                        </label>
	                        <select data-native-menu="false" name="D024_Flag_Contribuinte" id="D024_Flag_Contribuinte">
	                        	{$selectFlagContribuinte}
		                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_E_Mail">
	                                E-mail
	                            </label>
	                            <input name="D024_E_Mail" id="D024_E_Mail"  placeholder="" value="{$T003['D024_E_Mail']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Cnpj">
	                                CNPJ
	                            </label>
	                            <input name="D024_Cnpj" id="D024_Cnpj" placeholder="" value="{$T003['D024_Cnpj']}" onChange="validaCpfCnpj('cnpj', this, '{$T003_Id}');" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Cpf">
	                                CPF
	                            </label>
	                            <input name="D024_Cpf" id="D024_Cpf" placeholder="" value="{$T003['D024_Cpf']}" type="text" onChange="validaCpfCnpj('cpf', this, '{$T003_Id}');" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Rg">
	                                RG
	                            </label>
	                            <input name="D024_Rg" id="D024_Rg" placeholder="" value="{$T003['D024_Rg']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Data_Fundacao">
	                                Nascimento
	                            </label>
	                            <div style="display: inline-block; width: 62%;">
	                            <input name="D024_Data_Fundacao" id="D024_Data_Fundacao" value="{$T003['D024_Data_Fundacao']}" placeholder="" type="date" data-role="datebox" data-options='{"mode": "calbox", "dateFormat": "dd/mm/YYYY"}' />
	                            </div>
	                         </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
				                <label for="D024_DDD_Telefone_1" style="float:left">
				                    Telefone 1
				                </label>
	                           	<input name="D024_DDD_Telefone_1" id="D024_DDD_Telefone_1" placeholder="" value="{$T003['D024_DDD_Telefone_1']}" type="text" style="width:40px; float: left;" />
	                            <input name="D024_Telefone_1" id="D024_Telefone_1" placeholder="" value="{$T003['D024_Telefone_1']}" type="text" style="float: left; width: 100px;" />     
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
				                <label for="D024_DDD_Telefone_2" style="float:left">
				                    Telefone 2
				                </label>
	                           	<input name="D024_DDD_Telefone_2" id="D024_DDD_Telefone_2" placeholder="" value="{$T003['D024_DDD_Telefone_2']}" type="text" style="width:40px; float: left;" />
	                            <input name="D024_Telefone_2" id="D024_Telefone_2" placeholder="" value="{$T003['D024_Telefone_2']}" type="text" style="float: left; width: 100px;" />     
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
				                <label for="D024_Endereco" style="float: left;">
				                    Endereço
				                </label>
	                            <input name="D024_Endereco" id="D024_Endereco" placeholder="" value="{$T003['D024_Endereco']}" type="text" style="float: left; width:55%;" />
				                <label for="D024_Numero" style="float: left;width:5%;">
				                	N°:
				                </label>
								<input name="D024_Numero" id="D024_Numero" placeholder="" value="{$T003['D024_Numero']}" type="text" style="float: left;width:10%;" />     
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
				                <label for="D024_Complemento" style="float: left;">
				                    Complemento
				                </label>
	                            <input name="D024_Complemento" id="D024_Complemento" placeholder="" value="{$T003['D024_Complemento']}" type="text" style="float: left; width:40%;" />
							</fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Bairro">
	                                Bairro
	                            </label>
	                            <input name="D024_Bairro" id="D024_Bairro" placeholder="" value="{$T003['D024_Bairro']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <label for="D024_D018_Id">
	                            Estado
	                        </label>
	                        <select data-native-menu="true" name="D024_D018_Id" id="D024_D018_Id" onchange="alteraEstado(this); return false;">
	                        	{$selectEstado}
		                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
							<label for="D024_D020_Id">
	                            Cidade
	                        </label>
	                        <select data-native-menu="true" name="D024_D020_Id" id="D024_D020_Id">
	                        	{$selectCidade}
		                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Cep">
	                                CEP
	                            </label>
	                            <input name="D024_Cep" id="D024_Cep" placeholder="" value="{$T003['D024_Cep']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Observacoes">
	                                Observação
	                            </label>
	                            <textarea placeholder="" name="D024_Observacoes" id="D024_Observacoes" value="{$T003['D024_Observacoes']}" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">{$T003['D024_Observacoes']}</textarea>
	                        </fieldset>
	                    </div>
EOT;
					if(!empty($update)){
						echo <<<EOT
						<div data-role="fieldcontain">
	                        <label for="D024_Flag_Pre_Cadastro">
	                            Pré Cadastro?
	                        </label>
	                        <select data-native-menu="false" name="D024_Flag_Pre_Cadastro" id="D024_Flag_Pre_Cadastro">
	                        	{$selectFlagPreCadastro}
		                    </select>
	                    </div>
EOT;
					}
	                    
	            echo <<<EOT
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="fade" onclick="\$.mobile.changePage( './clientes.php?T003_Id={$T003_Id}', { transition: 'fade'} );">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        {$botaoEnviar}
                    </div>
                </div>
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
	    function validaCpfCnpj(cpfCnpj, valor, T003_Id){
	    	var valorDigitado = $(valor).val();
	    	$.getJSON('./tablet_func-ajax.php?opcaoAcao=validaClienteExistente&cpfCnpj=' + encodeURIComponent(cpfCnpj) + '&valor=' + encodeURIComponent(valorDigitado) + '&callback=?', function(request) {
	    		if(request.code == true){
	    			var D024_Id = request.data;
	    			$.mobile.changePage( './associarCliente.php?D024_Id=' + encodeURIComponent(D024_Id) + '&T003_Id=' + encodeURIComponent(T003_Id), { transition: 'fade'} );
	    		}
			});	
	    }
    	function clientesPreCadastroEnviar() {
    		$.ajax({
				url: 'clientesPreCadastro_validar.php?callback=?',
				dataType: 'json',
				type: 'GET',
				cache: false,
				data: 'T003_Id={$T003_Id}&' + $("form").serialize(),
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
    	function clientesPreCadastroEnviarAlterar() {
    		$.ajax({
				url: 'clientesPreCadastro_validarAlteracao.php?callback=?',
				dataType: 'json',
				type: 'GET',
				cache: false,
				data: 'T003_Id={$T003_Id}&' + $("form").serialize(),
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
