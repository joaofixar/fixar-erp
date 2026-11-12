<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');


$update = isset($_REQUEST['update']) && $_REQUEST['update'] ? true : false;
$botaoEnviar = $update ? 'Alterar' : 'Adicionar';

//Define combobox D024_Flag_Pessoa_Fisica_Juridica
	$selectFlagPessoa = "";
	
	
	$selectFlagPessoa .= "<option value=\"F\" ".($T005['D024_Flag_Pessoa_Fisica_Juridica'] == "F" ? 'selected="selected"' : '').">Física</option>";
	$selectFlagPessoa .= "<option value=\"J\" ".($T005['D024_Flag_Pessoa_Fisica_Juridica'] == "J" ? 'selected="selected"' : '').">Jurídica</option>";
	$selectFlagPessoa .= "<option value=\"I\" ".($T005['D024_Flag_Pessoa_Fisica_Juridica'] == "I" ? 'selected="selected"' : '').">Internacional</option>";
			
//Define combobox D024_Flag_Pessoa_Fisica_Juridica
	$selectFlagContribuinte = "";
	$selectFlagContribuinte .= "<option value=\"1\" ".($T005['D024_Flag_Contribuinte'] == "1" ? 'selected="selected"' : '').">Consumidor Final</option>";
	$selectFlagContribuinte .= "<option value=\"0\" ".($T005['D024_Flag_Contribuinte'] == "0" ? 'selected="selected"' : '').">Não contribuinte</option>";
	$selectFlagContribuinte .= "<option value=\"2\" ".($T005['D024_Flag_Contribuinte'] == "2" ? 'selected="selected"' : '').">Revenda</option>";
	$selectFlagContribuinte .= "<option value=\"3\" ".($T005['D024_Flag_Contribuinte'] == "3" ? 'selected="selected"' : '').">Produtor rural</option>";
	$selectFlagContribuinte .= "<option value=\"4\" ".($T005['D024_Flag_Contribuinte'] == "4" ? 'selected="selected"' : '').">Simples</option>";
	$selectFlagContribuinte .= "<option value=\"5\" ".($T005['D024_Flag_Contribuinte'] == "5" ? 'selected="selected"' : '').">Industria</option>";
			
//Define combobox D024_Estado
	$sql = "SELECT * FROM D018 ORDER BY D018_UF";
	$sqlEstado = mysql_query($sql);
	$selectEstado = "";
	while($rowEstado = mysql_fetch_array($sqlEstado)){								 
		if($rowEstado['D018_UF'] == $T005['D024_Estado']){
			$selectEstado .= "<option selected=\"selected\" value=\"{$rowEstado['D018_UF']}\">
								 {$rowEstado['D018_UF']}
							    </option>";
		}else{
			$selectEstado .= "<option value=\"{$rowEstado['D018_UF']}\">
								 {$rowEstado['D018_UF']}
							    </option>";
		}
		}
		
		
//Corrige a Data Nascimento para View
$T005['D024_Data_Fundacao'] = gCorrigeData($T005['D024_Data_Fundacao']);

	
// Adicionar
$temErro = '';
if (isset($_REQUEST['D024_Nome_Empresa'])) {
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
	$D024_Endereco = strtoupper(mysql_real_escape_string($_REQUEST['D024_Endereco']));
	$D024_Numero = strtoupper(mysql_real_escape_string($_REQUEST['D024_Numero']));
	$D024_Cidade = strtoupper(mysql_real_escape_string($_REQUEST['D024_Cidade']));
	$D024_Estado = strtoupper(mysql_real_escape_string($_REQUEST['D024_Estado']));
	$D024_Cep = strtoupper(mysql_real_escape_string($_REQUEST['D024_Cep']));
	$D024_Bairro = strtoupper(mysql_real_escape_string($_REQUEST['D024_Bairro']));
	$D024_Observacoes = strtoupper(mysql_real_escape_string($_REQUEST['D024_Observacoes']));
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
							D024_Endereco='{$D024_Endereco}',
							D024_Numero='{$D024_Numero}',
							D024_Cidade='{$D024_Cidade}',
							D024_Estado='{$D024_Estado}',
							D024_Cep='{$D024_Cep}',
							D024_Bairro='{$D024_Bairro}',
							D024_Observacoes='{$D024_Observacoes}'
		WHERE D024_Id='{$T005['T005_D024_Id']}'
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
								D024_Endereco,
								D024_Numero,
								D024_Cidade,
								D024_Estado,
								D024_Cep,
								D024_Bairro,
								D024_Observacoes) 
								VALUES 
								('{$D024_Nome_Empresa}', 
								'{$D024_Nome_Fantasia}', 
								'{$D024_Flag_Pessoa_Fisica_Juridica}', 
								'{$D024_Flag_Contribuinte}',
								'{$D024_Endereco}',
								'{$D024_Cnpj}',
								'{$D024_Cpf}',
								'{$D024_Rg}',
								'{$D024_Data_Fundacao}',
								'{$D024_DDD_Telefone_1}',
								'{$D024_Telefone_1}',
								'{$D024_Endereco}',
								'{$D024_Numero}',
								'{$D024_Cidade}',
								'{$D024_Estado}',
								'{$D024_Cep}',
								'{$D024_Bairro}',
								'{$D024_Observacoes}');
EOT;
		mysql_query($sql);
		$sql = "UPDATE T005 SET T005_D024_Id = '{$g['mysqlLastId']}' WHERE T005_Id = '{$T005_Id}';";
		mysql_query($sql);
	}
		$script = <<<EOT
		$.mobile.changePage( "./clientes.php?T005_Id={$T005_Id}", { transition: 'fade'} );
EOT;
}



include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageClientesPreCadastro">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./clientesPreCadastro.php?T005_Id={$T005_Id}&update={$update}" method="post" data-ajax="false">
                 <div class="ui-grid-a">
                 	<div class="ui-block-a">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Nome_Empresa">
	                                Nome
	                            </label>
	                            <input name="D024_Nome_Empresa" id="D024_Nome_Empresa" value="{$T005['D024_Nome_Empresa']}" placeholder="" value="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Nome_Fantasia">
	                                Fantasia
	                            </label>
	                            <input name="D024_Nome_Fantasia" id="D024_Nome_Fantasia" value="{$T005['D024_Nome_Fantasia']}" placeholder="" value="" type="text" />
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
	                            <input name="D024_E_Mail" id="D024_E_Mail"  placeholder="" value="{$T005['D024_E_Mail']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Cnpj">
	                                CNPJ
	                            </label>
	                            <input name="D024_Cnpj" id="D024_Cnpj" placeholder="" value="{$T005['D024_Cnpj']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Cpf">
	                                CPF
	                            </label>
	                            <input name="D024_Cpf" id="D024_Cpf" placeholder="" value="{$T005['D024_Cpf']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Rg">
	                                RG
	                            </label>
	                            <input name="D024_Rg" id="D024_Rg" placeholder="" value="{$T005['D024_Rg']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Data_Fundacao">
	                                Nascimento
	                            </label>
	                            <div style="display: inline-block; width: 62%;">
	                            <input name="D024_Data_Fundacao" id="D024_Data_Fundacao" value="{$T005['D024_Data_Fundacao']}" placeholder="" type="date" data-role="datebox" data-options='{"mode": "calbox", "dateFormat": "dd/mm/YYYY"}' />
	                            </div>
	                         </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
				                <label for="D024_DDD_Telefone_1" style="float:left">
				                    Telefone
				                </label>
	                           	<input name="D024_DDD_Telefone_1" id="D024_DDD_Telefone_1" placeholder="" value="{$T005['D024_DDD_Telefone_1']}" type="text" style="width:40px; float: left;" />
	                            <input name="D024_Telefone_1" id="D024_Telefone_1" placeholder="" value="{$T005['D024_Telefone_1']}" type="text" style="float: left; width: 100px;" />     
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
				                <label for="D024_Endereco" style="float: left;">
				                    Endereço
				                </label>
	                            <input name="D024_Endereco" id="D024_Endereco" placeholder="" value="{$T005['D024_Endereco']}" type="text" style="float: left; width:40%;" />
				                <label for="D024_Numero" style="float: left;width:5%;">
				                	N°:
				                </label>
								<input name="D024_Numero" id="D024_Numero" placeholder="" value="{$T005['D024_Numero']}" type="text" style="float: left;width:13%;" />     
	                        </fieldset>
	                    </div>
	                     <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
				                <label for="D024_Cidade" style="float: left;">
				                    Cidade
				                </label>
	                            <input name="D024_Cidade" id="D024_Cidade" placeholder="" value="{$T005['D024_Cidade']}" type="text" style="float: left; width:40%;" />
							</fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
		                        <label for="D024_Estado">
		                            Estado
		                        </label>
		                        <select data-native-menu="true" name="D024_Estado" id="D024_Estado">
		                        	{$selectEstado}
			                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Cep">
	                                CEP
	                            </label>
	                            <input name="D024_Cep" id="D024_Cep" placeholder="" value="{$T005['D024_Cep']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Bairro">
	                                Bairro
	                            </label>
	                            <input name="D024_Bairro" id="D024_Bairro" placeholder="" value="{$T005['D024_Bairro']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Observacoes">
	                                Observação
	                            </label>
	                            <textarea placeholder="" name="D024_Observacoes" id="D024_Observacoes" value="{$T005['D024_Observacoes']}" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">{$T005['D024_Observacoes']}</textarea>
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="fade" onclick="\$.mobile.changePage( './clientes.php?T005_Id={$T005_Id}', { transition: 'fade'} );">
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
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	function clientesPreCadastroEnviar() {
    		$.ajax({
				url: '../clientesPreCadastro_validar.php?callback=?',
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
    	$( document ).undelegate("#pageClientesPreCadastro", "pageinit");
    	$( document ).delegate("#pageClientesPreCadastro", "pageinit", function() {
EOT;

echo <<<EOT
			{$script}
    	});
    </script>
EOT;
