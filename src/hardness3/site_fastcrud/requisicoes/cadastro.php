<?php
set_time_limit(0);

session_start();
$footerJS = <<<EOT
	$(document).ready(function() {
EOT;

//Valida se pelo menos um módulo foi selecionado
/*
$modulos = (!empty($_REQUEST['modulos'])) ? explode(",", $_REQUEST['modulos']) : array();
if(count($modulos) <= 0){
	$footerJS .= <<<EOT
		alert("Para prosseguir é necessário selecionar pelo menos um módulo do sistema.");
		window.location = "http://fabio.site/solucoes";
EOT;
} else {
	$modulosSelecionados = '';
	$arrayModulos = array(
		'1' => 'Nota Fiscal Eletronica - NFe (Emissão de Notas)',
		'2' => 'Vendas - (Orçamento, Pedido)',
		'3' => 'Estoque - (NFe Entrada, Divergência de Estoque)',
		'4' => 'Contas a Pagar',
		'5' => 'Contas a Receber',
		'6' => 'Caixa / Bancos (Lançamentos Bancários)',
		'7' => 'Boletos',
		'8' => 'Remessa Bancária',
		'9' => 'Retorno Bancário'
	);

	foreach($modulos as $id){
		$modulosSelecionados .= '<span class="glyphicon glyphicon-ok"></span> '.$arrayModulos[$id].'<br />';
	}
	$preco = calcularPreco($modulos);
	$preco['valorTotal'] = gCorrigeNumero($preco['valorTotal']);
	if($_REQUEST['buy'] == false){ 
		$preco['valorTotal'] = 'Teste Grátis';
	} else {
		$preco['valorTotal'] = 'Total: R$ '.$preco['valorTotal'];
	}
	$footerJS .= <<<EOT
		$('#spanTotal').html('{$preco['valorTotal']}');
		$('#modulosSelecionados').html('{$modulosSelecionados}');
EOT;
}
$modulos = implode(",", $modulos);
*/
if(!empty($_POST)){
	$resposta = array('code' => true, 'data' => array(), 'erro' => '', 'boleto' => '');
	//Valida os dados do formulário
	$erros = array();

	if(!empty($_REQUEST['captcha'])){
		$key = 'hard123';
		$captchaInformado = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $_REQUEST['captcha'], MCRYPT_MODE_CBC, md5(md5($key))));
		//Verifica se o captcha está correto
		if($_SESSION['generatedCode'] != $captchaInformado){
			$erros['captcha'] = "O código informado não confere.";			
		} else {
			$mensagemPadrao = "Este campo deve ser preenchido.";
			if(!empty($_REQUEST['D024_Inscricao_Estadual'])){	$_REQUEST['D024_Inscricao_Estadual'] = mysql_real_escape_string($_REQUEST['D024_Inscricao_Estadual']); 	} else { $erros['D024_Inscricao_Estadual'] 	= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_Nome_Empresa'])) 		{	$_REQUEST['D024_Nome_Empresa'] = strtoupper(mysql_real_escape_string($_REQUEST['D024_Nome_Empresa'])); 	} else { $erros['D024_Nome_Empresa'] 		= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_CPF_Responsavel'])) 	{	$_REQUEST['D024_CPF_Responsavel'] = mysql_real_escape_string($_REQUEST['D024_CPF_Responsavel']); 		} else { $erros['D024_CPF_Responsavel'] 	= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_Endereco'])) 			{	$_REQUEST['D024_Endereco'] = strtoupper(mysql_real_escape_string($_REQUEST['D024_Endereco'])); 			} else { $erros['D024_Endereco'] 			= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_Numero'])) 			{	$_REQUEST['D024_Numero'] = mysql_real_escape_string($_REQUEST['D024_Numero']); 							} else { $erros['D024_Numero'] 				= $mensagemPadrao; }
			if($_REQUEST['D024_D018_Id'] > 0)				{	$_REQUEST['D024_D018_Id'] = mysql_real_escape_string($_REQUEST['D024_D018_Id']); 						} else { $erros['D024_D018_Id'] 			= $mensagemPadrao; }
			if($_REQUEST['D024_D020_Id'] > 0)				{	$_REQUEST['D024_D020_Id'] = mysql_real_escape_string($_REQUEST['D024_D020_Id']); 						} else { $erros['D024_D020_Id'] 			= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_Bairro'])) 			{	$_REQUEST['D024_Bairro'] = strtoupper(mysql_real_escape_string($_REQUEST['D024_Bairro'])); 				} else { $erros['D024_Bairro'] 				= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_Cep'])) 				{	$_REQUEST['D024_Cep'] = mysql_real_escape_string($_REQUEST['D024_Cep']); 								} else { $erros['D024_Cep'] 				= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_E_Mail'])) 			{	$_REQUEST['D024_E_Mail'] = strtoupper(mysql_real_escape_string($_REQUEST['D024_E_Mail'])); 				} else { $erros['D024_E_Mail'] 				= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_DDD_Telefone'])) 		{	$_REQUEST['D024_DDD_Telefone'] = mysql_real_escape_string($_REQUEST['D024_DDD_Telefone']); 				} else { $erros['D024_DDD_Telefone'] 		= "&nbsp;"; }
			if(!empty($_REQUEST['D024_Telefone'])) 			{	$_REQUEST['D024_Telefone'] = mysql_real_escape_string($_REQUEST['D024_Telefone']); 						} else { $erros['D024_Telefone'] 			= $mensagemPadrao; }
			if(!empty($_REQUEST['pacote'])) 				{	$_REQUEST['pacote'] = mysql_real_escape_string($_REQUEST['pacote']); 									} else { $erros['pacote'] 					= $mensagemPadrao; }
			//if(!empty($_REQUEST['D024_Dias_Vencimento'])) 	{	$_REQUEST['D024_Dias_Vencimento'] = mysql_real_escape_string($_REQUEST['D024_Dias_Vencimento']); 		} else { $erros['D024_Dias_Vencimento'] 	= $mensagemPadrao; }
			if(!empty($_REQUEST['login'])) 					{	$_REQUEST['login'] = mysql_real_escape_string($_REQUEST['login']); 										} else { $erros['login'] 					= $mensagemPadrao; }
			$_REQUEST['D024_Cargo_Responsavel'] = strtoupper($_REQUEST['D024_Cargo_Responsavel']);
			$_REQUEST['D024_Contador'] = strtoupper($_REQUEST['D024_Contador']);
			if(!empty($_REQUEST['D024_Cnpj'])){
				$_REQUEST['D024_Cnpj'] = mysql_real_escape_string($_REQUEST['D024_Cnpj']);
				$D024 = mysql_query("SELECT * FROM D024 WHERE D024_Cnpj = '{$_REQUEST['D024_Cnpj']}'", $conNOTA);
				if(mysql_num_rows($D024) > 0){
					$resposta['code'] = false;
					$resposta['erro'] = "Este CNPJ já esta cadastrado em nossa base.";
					echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
					die();
				}
			} else {
				$erros['D024_Cnpj'] = $mensagemPadrao;
			}
		}
	} else {
		$erros['captcha'] = "O código de verificação deve ser informado.";		
	}
	//if(!empty($_REQUEST['Dominio'])){
	//	$_REQUEST['Dominio'] = trim($_REQUEST['Dominio']);
	//	$retorno = verificarDominioValido($_REQUEST['Dominio']);
	//	if($retorno !== true){
	//		$erros['Dominio'] = $retorno;
	//	}
	//} else {
	//	$erros['Dominio'] = $mensagemPadrao;
	//}
	if(!empty($erros)){
		//Caso ocorra algum erro, geramos um novo captcha
		$key = 'hard123';
		$captcha = substr(md5(time().'hardness'),0,5);
		$_SESSION['generatedCode'] = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $captcha, MCRYPT_MODE_CBC, md5(md5($key))));

		$resposta['code'] = false;
		$resposta['data'] = $erros;
	} else {
		//$_REQUEST['Dominio'], $_REQUEST['login'], $_REQUEST['senha'], $_REQUEST['D024_E_Mail']
		$retorno = criarConta($_REQUEST);
		if($retorno !== true){
			$resposta['code'] = false;
			$resposta['erro'] = $retorno;
		}
	}
	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	die();
} else {
	$captcha = substr(md5(time().'hardness'),0,5);
	$key = 'hard123';
	$_SESSION['generatedCode'] = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $captcha, MCRYPT_MODE_CBC, md5(md5($key))));
}
// Header
$headerHeadTitle = 'Nova Conta - Cadastro no Sistema';
include('requisicoes/header.php');

echo <<<EOT
<div id="cadastro" class="container conteudo">
<div class="col-xs-12">
	<div class="row">
		<div class="col-md-5">
			<div class="page-header">
				<h3 class="modulo" id="sistema" style="color:#1F4C74">NF-e, Estoque e Financeiro</h3>
			</div>
			<p><ul>
				<li><span class='glyphicon glyphicon-ok'></span> Emissão NF-e e NFC-e.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Entrada de NF-e e NFC-e (Compras, devoluções, etc..).</li>
				<li><span class='glyphicon glyphicon-ok'></span> Cadastro de clientes, fornecedores e transportadoras.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Cadastro de produtos.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Estoque (Entradas, Saídas, Balanços e Inventário).</li>
				<li><span class='glyphicon glyphicon-ok'></span> Emissão de boleto bancário (com ou sem registro).</li>
				<li><span class='glyphicon glyphicon-ok'></span> Remessa e retorno bancário.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Contas a pagar e a receber.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Controle bancário.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Sistema on-line 100% WEB (Em Nuvem).</li>
				<li><span class='glyphicon glyphicon-ok'></span> Suporte técnico ilimitado.</li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
			</ul></p>
		    <h2 class="contact" style="color:#5CB85C"><b>R$ 149,00 por mês</b></h2>
		</div>
		<div class="col-md-7">
			<div class="page-header">
				<h3 class="modulo" id="sistema" style="color:#1F4C74">Vendas, NF-e, Estoque, Financeiro e Compras</h3>
			</div>
			<p><ul>
				<li><span class='glyphicon glyphicon-ok'></span> Orçamento e pedido de venda.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Cotação e ordem de compra.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Emissão NF-e e NFC-e.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Entrada de NF-e e NFC-e (Compras, devoluções, etc..).</li>
				<li><span class='glyphicon glyphicon-ok'></span> Cadastro de clientes, fornecedores e transportadoras.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Cadastro de produtos.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Estoque (Entradas, Saídas, Balanços e Inventário).</li>
				<li><span class='glyphicon glyphicon-ok'></span> Emissão de boleto bancário (com ou sem registro).</li>
				<li><span class='glyphicon glyphicon-ok'></span> Remessa e retorno bancário.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Contas a pagar e a receber.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Controle bancário.</li>
				<li><span class='glyphicon glyphicon-ok'></span> Sistema on-line 100% WEB (Em Nuvem).</li>
				<li><span class='glyphicon glyphicon-ok'></span> Suporte técnico ilimitado.</li>
			</ul></p>
		    <h2 class="contact" style="color:#5CB85C"><b>R$ 239,00 por mês</b></h2>
		</div>
	</div>
    <div class="row" style="margin-top: 25px;">
		<div class="col-md-3">
			<a href="/static/img/demo5.png" data-lightbox="nota_fiscal" title="Nota fiscal"><img src="/static/img/demo5.png" class=screen style="width: 100%;" onclick></a>
		</div>
		<div class="col-md-3">
			<a href="/static/img/demo2.png" data-lightbox="nota_fiscal" title="Cadastro produto"><img src="/static/img/demo2.png" style="width: 100%;" class=screen onclick></a>
		</div>
		<div class="col-md-3">
			<a href="/static/img/demo1.png" data-lightbox="nota_fiscal" title="Cadastro clientes"><img src="/static/img/demo1.png" style="width: 100%;" class=screen onclick></a>
		</div>
		<div class="col-md-3">
			<a href="/static/img/demo4.png" data-lightbox="nota_fiscal" title="Consulta produtos"><img src="/static/img/demo4.png" style="width: 100%;" class=screen onclick></a>
		</div> 
		<!--<div class="col-md-3">
			<a href="/static/img/demo5.png" data-lightbox="nota_fiscal" title="Cadastro cliente"><img src="/static/img/demo5.png" class=screen style="width: 100%;" onclick></a>
		</div>
		<div class="col-md-3">
			<a href="/static/img/demo2.png" data-lightbox="nota_fiscal" title="Cadastro produto"><img src="/static/img/demo2.png" style="width: 100%;" class=screen onclick></a>
		</div>
		<div class="col-md-3">
			<a href="/static/img/demo3.png" data-lightbox="nota_fiscal" title="Nota fiscal"><img src="/static/img/demo3.png" style="width: 100%;" class=screen onclick></a>
		</div>
		<div class="col-md-3">
			<a href="/static/img/demo4.png" data-lightbox="nota_fiscal" title="Consulta produtos"><img src="/static/img/demo4.png" style="width: 100%;" class=screen onclick></a>
		</div> -->
	</div> 
	<p style="text-align: justify;">
	<h3 class="contact"  style="color:#1F4C74">Compre e comece a usar agora mesmo:</h3>
	<p>Utilize o formulario abaixo para criar o cadastro de sua empresa.<br />
	Será gerado o boleto bancário da primeira mensalidade com vencimento para 07 dias.<br />
	Os planos básicos permitem acesso para até 5 usuários.</br />
	Caso queira falar consco, acesse a opção CONTATO do site.<br />
	Os campos com * são obrigatórios.</p>
	<div id="erros"></div>
	<div class="col-md-8">
	<form role="form" id="formCadastro" />	
		<!-- <div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" for="Dominio" class="control-label">* Endereço</label>
			</div>
			<div class="col-md-5">
				<div class="input-group">
					<input class="form-control" type="text" name="Dominio" id="Dominio" size="18" maxlength="18" placeholder="nome_empresa" onblur="validaDominioEmpresa(this);" />
					<span class="input-group-addon">.hardness.com.br</span>
				</div>
			</div>
		</div>-->
		<!-- <div class="input-group">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" for="Dominio" class="control-label">* Endereço</label>
			</div>
			<div class="col-md-4">
				<span class="input-group-addon" style="width: 14%; height: 34px; padding-top: 8px;">www.</span>
				<input class="form-control" type="text" name="Dominio" id="Dominio" size="18" maxlength="18" placeholder="nome_empresa" style="width: 46%; float: left; border-radius: 0px;" onblur="validaDominioEmpresa(this);" />
				<span class="input-group-addon" style="height: 34px;">.hardness.com.br</span>
			</div>
		</div> -->
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" for="D024_Cnpj" class="control-label">* CNPJ</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Cnpj" id="D024_Cnpj" size="18" maxlength="18" style="text-transform: uppercase; width: 160px;" onblur="validaCNPJ(this);" onKeyPress="return(formatarCNPJ('7',this,event));" placeholder="00.000.000/0000-00" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Inscricao_Estadual">* Insc.Estadual</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Inscricao_Estadual" id="D024_Inscricao_Estadual" size="18" maxlength="14" style="text-transform: uppercase; width: 160px;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Nome_Empresa">* Razão Social</label>
			</div>
			<div class="col-md-8">
				<input class="form-control" type="text" name="D024_Nome_Empresa" id="D024_Nome_Empresa" size="60" maxlength="60" style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Endereco">* Endereço</label>
			</div>
			<div class="col-md-8">
				<input class="form-control" type="text" name="D024_Endereco" id="D024_Endereco" size="60" maxlength="60" style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Numero">* Numero</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Numero" id="D024_Numero" size="14" maxlength="14" style="text-transform: uppercase; width: 100px;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Complemento">Complemento</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Complemento" id="D024_Complemento" size="20" maxlength="20" style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_D018_Id">* UF</label>
			</div>
			<div class="col-md-5">
EOT;

$cClausula  = " select ";
$cClausula .= " D018_Id, ";
$cClausula .= " CONCAT(D018_UF, ' - ', D018_Estado) ";
$cClausula .= " from D018 ";
$cClausula .= " order by D018_UF,D018_Estado ";
$D018=mysql_query($cClausula, $conNOTA);
echo mysql_error();
echo "<select name='D024_D018_Id' id='D024_D018_Id' onChange=\"atualizarComboCidades(this);\"  class=\"form-control\">";
echo "<option value=''>Selecione o Estado</option>";
while ($mD018=mysql_fetch_array($D018)) {
	echo "<option value='{$mD018['D018_Id']}'>{$mD018[1]}</option>";
}
echo "</select>";

echo <<<EOT
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_D020_Id">* Cidade</label>
			</div>
			<div class="col-md-5">
				<select name="D024_D020_Id" id="D024_D020_Id" class="form-control">
					<option value= "">Selecione a Cidade</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Bairro">* Bairro</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Bairro" id="D024_Bairro" size="30" maxlength="30"  style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Cep">* CEP</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Cep" id="D024_Cep" size="9" maxlength="9"  style="text-transform: uppercase; width: 160px;" placeholder="00000-000" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_DDD_Telefone">* Telefone</label>
			</div>
			<div class="col-md-2">
				<input class="form-control" type="text" name="D024_DDD_Telefone" id="D024_DDD_Telefone" value='' size="2" maxlength="2"  style="text-transform: uppercase;" placeholder="DDD" />
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Telefone" id="D024_Telefone" value='' size="12" maxlength="12"  style="text-transform: uppercase; width: 160px;" placeholder="Telefone" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_CPF_Responsavel">* CPF do Responsável</label>
			</div>
			<div class="col-md-5" style="text-align: right;">
				<input class="form-control" type="text" name="D024_CPF_Responsavel" id="D024_CPF_Responsavel" size="14" maxlength="14"  style="text-transform: uppercase; width: 130px;" onblur="validaCPF(this);" onKeyPress="return(formatarCPF('8',this,event));" placeholder="000.000.000-00" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_E_Mail">* E-mail</label>
			</div>
			<div class="col-md-7">
				<input class="form-control" type="text" name="D024_E_Mail" id="D024_E_Mail" size="60" maxlength="60"  style="text-transform: lowercase;"  placeholder="email@empresa.com.br" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Cargo_Responsavel">Cargo</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Cargo_Responsavel" id="D024_Cargo_Responsavel" size="20" maxlength="20"  style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="pacote">* Pacote</label>
			</div>
			<div class="col-md-5">
				<select class="form-control" name="pacote" id="pacote">
					<option value="">Selecione um pacote</option>
					<option value="1">Pacote 1 - R$ 149,00 por mês</option>
					<option value="2">Pacote 2 - R$ 239,00 por mês</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Contador">Nome Contador</label>
			</div>
			<div class="col-md-4">
				<input class="form-control" type="text" name="D024_Contador" id="D024_Contador" size="50" maxlength="50"  style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="D024_Telefone_Contador">Telefone Contador</label>
			</div>
			<div class="col-md-4">
				<input class="form-control" type="text" name="D024_Telefone_Contador" id="D024_Telefone_Contador" size="14" maxlength="14"  style="text-transform: uppercase;" placeholder="(DDD) TELEFONE" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="login">* Usuário</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="login" id="login" size="50" maxlength="50" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="senha">* Senha</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="password" name="senha" id="senha" size="30" maxlength="30" placeholder="Digite uma senha" onkeyup="validaSenha();" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: right;">
				<label style="text-align: right;" class="control-label" for="senha_confirma">* Confirmar Senha</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="password" name="senha_confirma" id="senha_confirma" size="30" maxlength="30"  placeholder="Digite a senha novamente" onkeyup="validaSenha();" />
			</div>
		</div>
		<div class="row" style="text-align: right; margin-top: 10px;">
			<div class="col-md-4">
				<label style="text-align: right; margin-top: 20px;" class="control-label" for="senha_confirma">Código de verificação</label><br>				
			</div>
			<div class="col-md-6">
				<span style="float: left;">Digite o código no campo abaixo.</span><br>
				<input class="form-control" type="text" name="captcha" id="captcha" size="5" maxlength="5" style="width: 80px; float: left;" />
				<span style="float: left; margin: 10px 0 0 10px;" class="captchaImage"><br>
			</div>
			<div class="col-md-5"></div>
		</div>
		<div class="row" style="text-align: center;">
			<button type="submit" id="submit" class="btn btn-default btn-lg btn-primary" style="margin: 20px;">Comprar</button><br />
		</div>
	</form>
	</div>
</div>
<div class="col-xs-4">
	<!--<div class="affix col-xs-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				Modulos Selecionados
			</div>
			<div class="panel-body" id="modulosSelecionados">
				-
			</div>
			<div class="panel-footer">
					<button class="btn btn-primary" id="voltar">Adicionar/Remover Modulos</button>
				<div style="float: right; margin-top: 5px;">
					<span id="spanTotal"></span>
				</div>
			</div>
		</div>
	</div>-->
</div>
</div>
</div>
EOT;
// Footer
$footerJS .= <<<EOT
	$('#voltar').bind('click', function(){
		window.location = '/solucoes/?modulos=' + encodeURIComponent('{$modulos}');
	});
	$("#formCadastro").submit(function() {
		var retorno = validaCamposFormulario();
		$('#submit').attr('disabled', 'disabled').html('Aguarde, processando...');
		if(retorno){		
			$.ajax({
				url: '/cadastro/?mod=' + encodeURIComponent('{$modulos}') + '&buy=' + encodeURIComponent('{$_REQUEST['buy']}'),
				dataType: 'jsonp',
				type: 'POST',
				cache: false,
				data: $("#formCadastro").serialize(),
				success: function(request) {
					if (request.code) {
						$('body').append('<div class="alert alert-success" role="alert" style="position: fixed; top:0; bottom: 0; left: 0; right: 0; margin: auto; width: 500px; height: 120px;"><button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>Conta criada com sucesso! <br/>Você receberá um e-mail de confirmação com os dados de acesso ao sistema.<br /><br /><span style="font-size: 10px">Redirecionando para o site Hardness Sistemas</span></div>');
						setTimeout(function(){
							window.location = '/';
						}, 10000);
					} else {
						$('.captchaImage').html('<img src="/captcha.php" />');
						$('#submit').removeAttr('disabled').html('Enviar');
						if(request.erro != ''){
							$('body').append('<div class="alert alert-danger" role="alert" style="position: fixed; top:0; bottom: 0; left: 0; right: 0; margin: auto; width: 500px; overflow: auto; display: table;"><button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>' + request.erro + '</div>');
						}
						$('.erroCampo').remove();
						$.each(request.data, function(index, value) {
							$('#' + index).parent().addClass('has-error');
							if(index == 'Dominio'){
								$('#erroDominio').remove();
								$('#' + index).parent().parent().append('<label style="float: left;" id="erroDominio" class="erroCampo" class="control-label" for="inputError">' + value + '</label>');
							} else {
								$('#' + index).parent().append('<label style="float: left; color: #b94a48;" class="erroCampo" class="control-label" for="inputError">' + value + '</label>');
							}
						});
					}
					return false;
				}
			});
		} else {
			$('#submit').removeAttr('disabled').html('Enviar');
			$('body').append('<div class="alert alert-danger" role="alert" style="position: fixed; top:0; bottom: 0; left: 0; right: 0; margin: auto; width: 500px; overflow: auto; display: table;"><button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>Existe(m) campo(s) do formulário com erro, por favor verifique!</div>');
			return false;
		}
		return false;
	});

	$('.captchaImage').html('<img src="/captcha.php" />');
});
EOT;
include('requisicoes/footer.php');