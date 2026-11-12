<?php
set_time_limit(0);
require_once('../../ide/sysConfig.php');
require_once('../conf.php');
require_once('../funcoesCriacaoConta.php');
session_start();
$footerJS = <<<EOT
	$(document).ready(function() {
EOT;

ini_set('mysql.connect_timeout', 300);
ini_set('default_socket_timeout', 300);

$fastCrud = $_REQUEST['fastCrud'];
$tipoCadastro = !!$_REQUEST['tipoCadastro'] ? $_REQUEST['tipoCadastro'] : 'PJ';

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
			if ($tipoCadastro == 'PJ') {
                if (!empty($_REQUEST['D024_Inscricao_Estadual'])) {
                    $_REQUEST['D024_Inscricao_Estadual'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_Inscricao_Estadual']);
                } else {
                    $erros['D024_Inscricao_Estadual'] = $mensagemPadrao;
                }
            }
			if(!empty($_REQUEST['D024_Nome_Empresa'])) 		{	$_REQUEST['D024_Nome_Empresa'] = strtoupper(mysqli_real_escape_string($conERP, $_REQUEST['D024_Nome_Empresa'])); 	} else { $erros['D024_Nome_Empresa'] 		= $mensagemPadrao; }
            if ($tipoCadastro == 'PF') {
                if (!empty($_REQUEST['D024_CPF_Responsavel'])) {
                    $_REQUEST['D024_CPF_Responsavel'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_CPF_Responsavel']);
                } else {
                    $erros['D024_CPF_Responsavel'] = $mensagemPadrao;
                }
            }
			if(!empty($_REQUEST['D024_Endereco'])) 			{	$_REQUEST['D024_Endereco'] = strtoupper(mysqli_real_escape_string($conERP, $_REQUEST['D024_Endereco'])); 			} else { $erros['D024_Endereco'] 			= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_Numero'])) 			{	$_REQUEST['D024_Numero'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_Numero']); 							} else { $erros['D024_Numero'] 				= $mensagemPadrao; }
			if($_REQUEST['D024_D018_Id'] > 0)				{	$_REQUEST['D024_D018_Id'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_D018_Id']); 						} else { $erros['D024_D018_Id'] 			= $mensagemPadrao; }
			if($_REQUEST['D024_D020_Id'] > 0)				{	$_REQUEST['D024_D020_Id'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_D020_Id']); 						} else { $erros['D024_D020_Id'] 			= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_Bairro'])) 			{	$_REQUEST['D024_Bairro'] = strtoupper(mysqli_real_escape_string($conERP, $_REQUEST['D024_Bairro'])); 				} else { $erros['D024_Bairro'] 				= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_Cep'])) 				{	$_REQUEST['D024_Cep'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_Cep']); 								} else { $erros['D024_Cep'] 				= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_E_Mail'])) 			{	$_REQUEST['D024_E_Mail'] = strtoupper(mysqli_real_escape_string($conERP, $_REQUEST['D024_E_Mail'])); 				} else { $erros['D024_E_Mail'] 				= $mensagemPadrao; }
			if(!empty($_REQUEST['D024_DDD_Telefone'])) 		{	$_REQUEST['D024_DDD_Telefone'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_DDD_Telefone']); 				} else { $erros['D024_DDD_Telefone'] 		= "&nbsp;"; }
			if(!empty($_REQUEST['D024_Telefone'])) 			{	$_REQUEST['D024_Telefone'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_Telefone']); 						} else { $erros['D024_Telefone'] 			= $mensagemPadrao; }
			//if(!empty($_REQUEST['pacote'])) 				{	$_REQUEST['pacote'] = mysqli_real_escape_string($conERP, $_REQUEST['pacote']); 									} else { $erros['pacote'] 					= $mensagemPadrao; }
			//if(!empty($_REQUEST['D024_Dias_Vencimento'])) 	{	$_REQUEST['D024_Dias_Vencimento'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_Dias_Vencimento']); 		} else { $erros['D024_Dias_Vencimento'] 	= $mensagemPadrao; }
			if(!empty($_REQUEST['login'])) 					{	$_REQUEST['login'] = mysqli_real_escape_string($conERP, $_REQUEST['login']); 										} else { $erros['login'] 					= $mensagemPadrao; }
			//$_REQUEST['D024_Cargo_Responsavel'] = strtoupper($_REQUEST['D024_Cargo_Responsavel']);
			//$_REQUEST['D024_Contador'] = strtoupper($_REQUEST['D024_Contador']);
            if ($tipoCadastro == 'PJ') {
                if (!empty($_REQUEST['D024_Cnpj'])) {
                    $_REQUEST['D024_Cnpj'] = mysqli_real_escape_string($conERP, $_REQUEST['D024_Cnpj']);
                    $D024 = mysqli_query($conERP, "SELECT * FROM D024 WHERE D024_Cnpj = '{$_REQUEST['D024_Cnpj']}'");
                    if (mysqli_num_rows($D024) > 0) {
                        $resposta['code'] = false;
                        $resposta['erro'] = "Este CNPJ já esta cadastrado em nossa base.";
                        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
                        die();
                    }
                } else {
                    $erros['D024_Cnpj'] = $mensagemPadrao;
                }
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
$dominio_ = explode('.', $_SERVER['SERVER_NAME'], 2);
$dominio_ = $dominio_[1];

$_nome_empresa = $tipoCadastro == 'PJ' ? 'Razão Social' : 'Seu Nome';
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<!-- <link rel="shortcut icon" href="../../assets/ico/favicon.png"> -->
	<title>{$headerHeadTitle}</title>
	<!-- Bootstrap core CSS -->
	<!--<link href="/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="../static/css/site.css" rel="stylesheet">
	<link href="../static/css/lightbox.css" rel="stylesheet" />
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="/static/js/html5shiv.js"></script>
		<script src="/static/js/respond.min.js"></script>
	<![endif]-->
	<link href="/static/video-js/video-js.css" rel="stylesheet">
	
	<style>
	    .btnCadastrar {
	        text-align: center;
        }
	    @media only screen and (max-width: 1025px) {
            .input-group .formSmall {
                width: 250px;
            }
            .formSmall {
                width: 250px;
            }
            .input-group .formSmaller {
                width: 150x;
            }
            .formSmaller {
                width: 150px;
            }
            .input-group-addon, .input-group-btn {
                width: auto;
            }
            .btnCadastrar {
                text-align: left;
            }
        }
    </style>
</head>
<body style="background-color: #FFF;">
<div id="cadastro" class="container" style="padding: 0;">
<div class="col-xs-12">
	Os campos com * são obrigatórios.</p>
	<div id="erros"></div>
	<div class="col-md-8">
	<form role="form" id="formCadastro" />
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" for="Dominio" class="control-label">* Domínio</label>
			</div>
			<div class="col-md-7">
				<div class="input-group">
					<input class="form-control formSmaller" type="text" name="Dominio" id="Dominio" size="18" maxlength="18" placeholder="nome_domínio" onblur="validaDominio(this, '{$dominio_}');" autocorrect="off" autocapitalize="none" />
					<input type="hidden" name="Site" id="Site" size="18" maxlength="18" value="{$dominio_}" />
					<input type="hidden" name="fastCrud" id="fastCrud" value="{$fastCrud}" />
					<input type="hidden" name="tipoCadastro" id="fastCrud" value="{$tipoCadastro}" />
					<span class="input-group-addon">.{$dominio_}</span>
				</div>
			</div>
		</div>
		<!-- <div class="input-group">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" for="Dominio" class="control-label">* Endereço</label>
			</div>
			<div class="col-md-4">
				<span class="input-group-addon" style="width: 14%; height: 34px; padding-top: 8px;">www.</span>
				<input class="form-control" type="text" name="Dominio" id="Dominio" size="18" maxlength="18" placeholder="nome_empresa" style="width: 46%; float: left; border-radius: 0px;" onblur="validaDominio(this);" />
				<span class="input-group-addon" style="height: 34px;">.hardness.com.br</span>
			</div>
		</div> -->
EOT;
        if ($tipoCadastro == 'PF') {
            echo <<<EOT
        <div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_CPF_Responsavel">* CPF</label>
			</div>
			<div class="col-md-5" style="text-align: left;">
				<input class="form-control" type="text" name="D024_CPF_Responsavel" id="D024_CPF_Responsavel" size="14" maxlength="14"  style="text-transform: uppercase; width: 130px;" onblur="validaCPF(this);" onKeyPress="return(formatarCPF('8',this,event));" placeholder="000.000.000-00" />
			</div>
		</div>
EOT;
        } else {
            echo <<<EOT
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" for="D024_Cnpj" class="control-label">* CNPJ</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Cnpj" id="D024_Cnpj" size="18" maxlength="18" style="text-transform: uppercase; width: 160px;" onblur="validaCNPJ(this);" onKeyPress="return(formatarCNPJ('7',this,event));" placeholder="00.000.000/0000-00" />
			</div>
		</div>
        <div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_Inscricao_Estadual">* Insc.Estadual</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Inscricao_Estadual" id="D024_Inscricao_Estadual" size="18" maxlength="14" style="text-transform: uppercase; width: 160px;" />
			</div>
		</div>
EOT;
        }
echo <<<EOT
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label formSmall" for="D024_Nome_Empresa">* {$_nome_empresa}</label>
			</div>
			<div class="col-md-8">
				<input class="form-control formSmall" type="text" name="D024_Nome_Empresa" id="D024_Nome_Empresa" size="60" maxlength="60" style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_Endereco">* Endereço</label>
			</div>
			<div class="col-md-8">
				<input class="form-control formSmall" type="text" name="D024_Endereco" id="D024_Endereco" size="60" maxlength="60" style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_Numero">* Numero</label>
			</div>
			<div class="col-md-5">
				<input class="form-control formSmall" type="text" name="D024_Numero" id="D024_Numero" size="14" maxlength="14" style="text-transform: uppercase; width: 100px;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_Complemento">Complemento</label>
			</div>
			<div class="col-md-5">
				<input class="form-control formSmall" type="text" name="D024_Complemento" id="D024_Complemento" size="20" maxlength="20" style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_D018_Id">* UF</label>
			</div>
			<div class="col-md-5">
EOT;

$cClausula  = " select ";
$cClausula .= " D018_Id, ";
$cClausula .= " CONCAT(D018_UF, ' - ', D018_Estado) ";
$cClausula .= " from D018 ";
$cClausula .= " order by D018_UF,D018_Estado ";
$D018=mysqli_query($conERP, $cClausula);
echo mysqli_error($conERP);
echo "<select name='D024_D018_Id' id='D024_D018_Id' onChange=\"atualizarComboCidades(this);\"  class=\"form-control formSmall\">";
echo "<option value=''>Selecione o Estado</option>";
while ($mD018=mysqli_fetch_array($D018)) {
	echo "<option value='{$mD018['D018_Id']}'>{$mD018[1]}</option>";
}
echo "</select>";

$onSucess = '';
if ($fastCrud == 'true') {
    $onSucess = <<<EOT
setTimeout(function(){
    window.top.location.replace("http://" + $('#Dominio').val() + ".fastcrud.com.br/");
}, 2000);
EOT;
}

echo <<<EOT
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_D020_Id">* Cidade</label>
			</div>
			<div class="col-md-5">
				<select name="D024_D020_Id" id="D024_D020_Id" class="form-control formSmall">
					<option value= "">Selecione a Cidade</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_Bairro">* Bairro</label>
			</div>
			<div class="col-md-5">
				<input class="form-control formSmall" type="text" name="D024_Bairro" id="D024_Bairro" size="30" maxlength="30"  style="text-transform: uppercase;" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_Cep">* CEP</label>
			</div>
			<div class="col-md-5">
				<input class="form-control" type="text" name="D024_Cep" id="D024_Cep" size="9" maxlength="9"  style="text-transform: uppercase; width: 160px;" placeholder="00000-000" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_DDD_Telefone">* Telefone</label>
			</div>
			<div class="col-md-2">
				<input class="form-control formSmall" type="text" name="D024_DDD_Telefone" id="D024_DDD_Telefone" value='' size="2" maxlength="2"  style="text-transform: uppercase;" placeholder="DDD" />
			</div>
			<div class="col-md-5">
				<input class="form-control formSmall" type="text" name="D024_Telefone" id="D024_Telefone" value='' size="12" maxlength="12"  style="text-transform: uppercase; width: 160px;" placeholder="Telefone" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="D024_E_Mail">* E-mail</label>
			</div>
			<div class="col-md-7">
				<input class="form-control formSmall" type="text" name="D024_E_Mail" id="D024_E_Mail" size="60" maxlength="60"  style="text-transform: lowercase;"  placeholder="email@empresa.com.br" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="login">* Usuário</label>
			</div>
			<div class="col-md-5">
				<input class="form-control formSmall" type="text" name="login" id="login" size="50" maxlength="50" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="senha">* Senha</label>
			</div>
			<div class="col-md-5">
				<input class="form-control formSmall" type="password" name="senha" id="senha" size="30" maxlength="30" placeholder="Digite uma senha" onkeyup="validaSenha();" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-4" style="text-align: left;">
				<label style="text-align: left;" class="control-label" for="senha_confirma">* Confirmar Senha</label>
			</div>
			<div class="col-md-5">
				<input class="form-control formSmall" type="password" name="senha_confirma" id="senha_confirma" size="30" maxlength="30"  placeholder="Digite a senha novamente" onkeyup="validaSenha();" />
			</div>
		</div>
		<div class="row" style="text-align: left; margin-top: 10px;">
			<div class="col-md-4">
				<label style="text-align: left; margin-top: 20px;" class="control-label" for="senha_confirma">Código de verificação</label><br>				
			</div>
			<div class="col-md-6">
				<span style="float: left;">Digite o código no campo abaixo.</span><br>
				<input class="form-control" type="text" name="captcha" id="captcha" size="5" maxlength="5" style="width: 80px; float: left;" />
				<span style="float: left; margin: 10px 0 0 10px;" class="captchaImage"><br>
			</div>
			<div class="col-md-5"></div>
		</div>
		<div class="row btnCadastrar">
			<button type="submit" id="submit" class="btn btn-default btn-lg btn-primary" style="margin: 20px 40px;">Cadastrar</button><br />
		</div>
	</form>
	</div>
</div>
</div>
</div>
<script src="../static/jquery/jquery-1.10.2.min.js"></script>
<script src="../static/js/site.js"></script>
<script src="../static/bootstrap/js/scrollspy_mod.js"></script>
<script src="../static/bootstrap/js/bootstrap.js"></script>
<script src="../static/js/lightbox-2.6.min.js"></script>
<script src="../static/video-js/video.js"></script>
<script src="../static/js/md5.js"></script>
<script type="text/javascript">
	$("#formCadastro").submit(function() {
		var retorno = validaCamposFormulario();
		$('#submit').attr('disabled', 'disabled').html('Aguarde, processando...');
		if(retorno){
			$.ajax({
				url: 'cadastroDominio.php?mod=' + encodeURIComponent('{$modulos}') + '&buy=' + encodeURIComponent('{$_REQUEST['buy']}'),
				dataType: 'jsonp',
				type: 'POST',
				cache: false,
				data: $("#formCadastro").serialize(),
				success: function(request) {
					if (request.code) {
						$('body').append('<div class="alert alert-success" role="alert" style="position: fixed; top:0; bottom: 0; left: 0; right: 0; margin: auto; width: 250px; height: 50px;"><button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>Conta criada com sucesso!<br /></div>');
						{$onSucess}
					} else {
						$('.captchaImage').html('<img src="../captcha.php" />');
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

	$('.captchaImage').html('<img src="../captcha.php" />');

	function validaDominio(campo, server){
		$.getJSON('ajaxDominio.php?funcao=validaDominioEmpresa&Dominio=' + encodeURIComponent($(campo).val()) + '&Server=' + encodeURIComponent(server) + '&callback=?', function(request) {
			if(!request.code) {
				$(campo).parent().addClass('has-error');
				$('#erroDominio').remove();
				$(campo).parent().parent().append('<label id="erroDominio" class="control-label" for="inputError">' + request.data + '</label>');
				return false;
			} else {
				$(campo).parent().removeClass('has-error');
				$('#erroDominio').remove();
				return true;
			}
		});
	}

	function validaCNPJ(campo){
		$.getJSON('ajaxDominio.php?funcao=validaCNPJ&D024_Cnpj=' + encodeURIComponent($(campo).val()) + '&callback=?', function(request) {
			if(!request.code) {
				$(campo).parent().addClass('has-error');
				$('#erroCnpj').remove();
				if(request.data != 'invalido'){
					$(campo).parent().append('<label id="erroCnpj" class="control-label" for="inputError">Este CNPJ já está cadastrado.</label>');
				} else {
					$(campo).parent().append('<label id="erroCnpj" class="control-label" for="inputError">Este CNPJ não é válido.</label>');
				}
				return false;
			} else {
				$(campo).parent().removeClass('has-error');
				$('#erroCnpj').remove();
				return true;
			}
		});
	}

	function validaCPF(campo){
		$.getJSON('ajaxDominio.php?funcao=validaCPF&D024_CPF_Responsavel=' + encodeURIComponent($(campo).val()) + '&callback=?', function(request) {
			if(!request.code) {
				$(campo).parent().addClass('has-error');
				$('#erroCpf').remove();
				$(campo).parent().append('<label id="erroCpf" class="control-label" for="inputError">Este CPF não é válido.</label>');
				return false;
			} else {
				$(campo).parent().removeClass('has-error');
				$('#erroCpf').remove();
				return true;
			}
		});
	}

	function atualizarComboCidades(campo){
		$.getJSON('ajaxDominio.php?funcao=atualizarComboCidades&D024_D018_Id=' + encodeURIComponent($(campo).val()) + '&callback=?', function(request) {
			if (request.code) {
				$('#D024_D020_Id').html(request.data);
			}
		});
	}
</script>
</body>
</html>
EOT;
