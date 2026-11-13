<?php
namespace hardness;
header("X-Robots-Tag: noindex, nofollow", true);
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
require_once('../../bibliotecas/segurancaSenha.php');

$js = '';
if (isset($_REQUEST['login'])) {
	if ($_SERVER['HTTP_HOST'] == 'hard.hardness.com.br' || $_SERVER['HTTP_HOST'] == 'cha.chasistemas.com.br') {
		$usarSenhaRootPadrao = false;
	} else {
		$usarSenhaRootPadrao = true;
	}

	// Login!
	if(!isset($_SESSION['bancoDados'])){
		@session_destroy();
		$_SESSION = array();	
	}
	session_start();
	$e='1';
	$l=$_REQUEST['login'];
	$s=$_REQUEST['senha'];
	$Banco_Dados = $w = $sb = $us = $uc = $Empresa_Usuaria = $PaginaQueIniciou = $nWidth_Padrao = $nHeight_Padrao = '';
	$user = explode('/', $confUsuario['pathRaiz']);
  	$user = $user[2];
  	$l_ = (substr($l, 0, 5) == 'hard_') ? 'root' : $l;

  	$sqlLoginDuplicado = mysqli_query("SELECT C007_Login	FROM C007 WHERE C007_Login='{$l_}' AND C007_Login!='root' AND C007_Ativo='S'");

  	if (empty($l)) {
	   $js .= "alert('Digite o login.');";
	} else if (empty($s)) {
		$js .= "alert('Digite a senha.');";
	} else if (empty($e)) {
		$js .= "alert('Selecione a empresa.');";
	} else if (mysql_num_rows($sqlLoginDuplicado) > 1){
		$js .= "alert('Existe mais de um usuário cadastrado com seu login, comunique o administrador do seu sistema.');";
	}else {

		$ordenaEmpresaPerfil = mysqli_query("SELECT C019_Id 
								  			   FROM C007
							 			  LEFT JOIN C019 ON C019_C007_Id=C007_Id
							 			  LEFT JOIN C021 ON C021_Id=C019_C021_Id
							 			  LEFT JOIN C004 ON C019_C004_Id=C004_Id
								 			  WHERE C007_Login='{$l_}' 
								   			    AND C007_Ativo='S'
								   			    AND C004_Flag_Ativo='S'
								   			    AND C019_Ordem_Empresa > 0");

		$orderByEmpresas = "ORDER BY C004_Ordem_Empresa,C004_Id ";
		if(mysqli_num_rows($ordenaEmpresaPerfil) > 0){
			$orderByEmpresas = "ORDER BY C019_Ordem_Empresa,C004_Id ";
		} 
						
		$sqlEmpresaUsuario = mysqli_query("SELECT C019_C004_Id, 
		                                          C007_Login,
												  C004_Ordem_Empresa
											 FROM C007
		                          	    LEFT JOIN C019 ON C019_C007_Id=C007_Id
		                                LEFT JOIN C021 ON C021_Id=C019_C021_Id
									    LEFT JOIN C004 ON C019_C004_Id=C004_Id
										    WHERE C007_Login='{$l_}' AND C007_Ativo='S' AND C004_Flag_Ativo='S'
										 		  {$orderByEmpresas}	
										 	LIMIT 1");
										
		$rowEmpresaUsuario = mysql_fetch_array($sqlEmpresaUsuario);
		if(mysql_num_rows($sqlEmpresaUsuario) == 1){
			$e = $rowEmpresaUsuario['C019_C004_Id'];
		}
		$cC004=mysqli_query("select * from C004 where C004_Id='$e'");
		$mC004=mysql_fetch_array($cC004);
		if (empty($rowEmpresaUsuario['C007_Login'])) {
			$js .= "alert('Cadastro de usuário não encontrado.');";
		} else if (empty($mC004['C004_Banco_Configuracao'])) {
			$js .= "alert('Banco de configuração nao definido.');";
		} else {
			$cBd_Configuracao=$mC004['C004_Banco_Configuracao'];

			$cC020=mysqli_query("select C020_Modelo_Sistema_Nfe from C020 where C020_C004_Id='$mC004[C004_Id]'");
			$mC020=mysql_fetch_array($cC020);

			$usuariosSuporte = array(
				'bruno' => '93513b491b333c2c6201d7ae5d95bef3',
				'ericks' => 'f70fc7d8ad7b81747a10fe4ef6f37d01'
			);

		    // Consulta a Senha Digitada do Usuario Selecionado
		    if ($l != 'root' && substr($l, 0, 5) != 'hard_') {
		    		$Sql=mysqli_query("select * from C007
		                           left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
		                           left join C021 on C021_Id=C019_C021_Id
		                           where C007_Login='$l'
		                           and C007_Ativo='S'
		                           and (C007_D024_Id=0 or C007_D024_Id is null)");
		    } else {
		    	   $Sql=mysqli_query("select * from C007
		                           left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
		                           left join C021 on C021_Id=C019_C021_Id
		                           where C007_Id='63'
		                           and (C007_D024_Id=0 or C007_D024_Id is null)");
		    }

		    if (mysql_num_rows($Sql)==0) {
		    	$js .= "alert('Login ou Senha inválida (1).');";
		    } else {
			    $mSenha=mysql_fetch_array($Sql);
			    // usuário 63 só com login root (caso alguém modificou o nome do usuário);
			    if ($mSenha['C007_Id'] == 63 && $l_ != 'root') {
			    	$js .= "alert('Pasta da empresa não definida.');";
			    } else {
				    $Senha=base64_encode($s);
				    $senhaValida=SegurancaSenha::validarSenhaArmazenada($s, $mSenha['C007_Senha']);
   				    // Verifica se a Senha Digitada pelo usuario é valida.
   				    $IP_Origem=substr($_SERVER['REMOTE_ADDR'],0,strrpos($_SERVER['REMOTE_ADDR'],'.'));

					$validaHorarioAcesso = true;
					if(($mSenha['C007_Acesso_Hora_Inicio'] != '00:00:00' && !empty($mSenha['C007_Acesso_Hora_Inicio'])) && ($mSenha['C007_Acesso_Hora_Fim'] != '00:00:00' && !empty($mSenha['C007_Acesso_Hora_Fim']))){	
						$horaInicio = $mSenha['C007_Acesso_Hora_Inicio'];
						$horaFim    = $mSenha['C007_Acesso_Hora_Fim'];
						$horaAtual  =  date('H:i:s');
						if ($horaAtual >= $horaInicio && $horaAtual <= $horaFim) {
							$validaHorarioAcesso = true; // Acesso permitido
						} else {
							$validaHorarioAcesso = false;
						}
					}

					$acessoDiaNaoUtil = true;
					if($mSenha['C007_Acesso_Dias_Nao_Uteis'] == 'N'){
						
						$mensagemFinalDeSemana = '';
						$mensagemFeriado       = '';
						$diaSemana 			   = date('N');
						// Verifica se é sábado (6) ou domingo (7)
						if($diaSemana == 6 || $diaSemana == 7){
							$acessoDiaNaoUtil = false;
							$mensagemFinalDeSemana = 'Final de semana';
						} elseif($mSenha['C007_D206_Id'] > 0 ){
							//Verifica tabela de feriado
							$feriado = mysqli_query("SELECT D207_Data,
															D207_Descricao_Feriado
												       FROM D207
						  					      LEFT JOIN D206 ON D206_Id = D207_D206_Id
												  	  WHERE D206_Id = '{$mSenha['C007_D206_Id']}'
													    AND D206_Flag_Ativo = 'S'
														AND CURDATE() = D207_Data
													  LIMIT 1");
							if(mysqli_num_rows($feriado) > 0 ){
								$mFeriado = mysql_fetch_array($feriado);
								$acessoDiaNaoUtil = false;
								$mensagemFeriado = 'Feriado de '.$mFeriado['D207_Descricao_Feriado'];
							}
						}
					}

 	 				if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
						$IP_Origem=substr($_SERVER['HTTP_X_FORWARDED_FOR'],0,strrpos($_SERVER['HTTP_X_FORWARDED_FOR'],'.'));
					}  
   				    
   				    if (!$usarSenhaRootPadrao && !$senhaValida) {
   				    	$js .= "alert('Login ou Senha inválida (2).');";
				    } else if ($mSenha['C007_Id'] == 63 && md5($s)==$sys['senhaRootSuporte']  && !empty($sys['IPRootSuporte']) && ($sys['IPRootSuporte'] != md5($IP_Origem) && $sys['IPRootSuporte2'] != md5($IP_Origem))) {
				    	//$js .= "alert('Desculpe, o acesso foi negado para este local. ({$sys['IPRootSuporte']}  ".md5($IP_Origem)."})');";				    
				    	// $js .= "location.replace('{$confUsuario['urlRaiz']}login');";
			    	} else if ($usarSenhaRootPadrao && (
				    			(!$senhaValida && $mSenha['C007_Id'] != 63) || //Senha usuario sistema
				    			($mSenha['C007_Id'] == 63 && (
					    				($l == 'root' && md5($s) != $sys['senhaRootMaster'] && md5($s) != $sys['senhaRootSuporte'] && ($Senha!=$mSenha['C007_Senha'] || $s=='root') && $sys['dominio'] == 'hardness.com.br') || //Acesso root hardness
					    				(substr($l, 0, 5) == 'hard_' && md5($s) != $usuariosSuporte[substr($l, 5)] && $sys['dominio'] == 'hardness.com.br') || //Acesso usuarios hardness
					    				($l == 'root' && md5($s) != $sys['senhaRootSistema'] && md5($s) != $sys['senhaRootMaster'] && ($Senha!=$mSenha['C007_Senha'] || $s=='root') && $sys['dominio'] != 'hardness.com.br')  //Acesso root representantes
				    				)
				    			)
				    		)
				    ) {
			   		 	$js .= "alert('Login ou Senha inválida (3).');";
					} elseif($mSenha['C007_Id'] != 63 && $acessoDiaNaoUtil === false){
						if(!empty($mensagemFinalDeSemana)){
							$js .= "alert('Acesso não liberado em dias não úteis ({$mensagemFinalDeSemana})');";						
						} else{
							$js .= "alert('Acesso não liberado em dias não úteis ({$mensagemFeriado})');";	
						}
				    } elseif($mSenha['C007_Id'] != 63 && $validaHorarioAcesso === false){
						$js .= "alert('Acesso não liberado neste horário (Permitido : {$mSenha['C007_Acesso_Hora_Inicio']} às {$mSenha['C007_Acesso_Hora_Fim']})');";							
					} elseif ($mSenha['C007_Id'] != 63 && !empty($mSenha['C007_IP_Acesso']) && ($Senha!=$mSenha['C007_Senha_Especial']) && strpos(' '.$mSenha['C007_IP_Acesso'],$IP_Origem)<=0) {
			   		 	$js .= "alert('Desculpe, o acesso foi negado para este local. ({$IP_Origem})');";							
				    } else if ($mSenha['C007_Flag_Ausente'] != 'N' && $l != 'root'){
				    	$js .= "alert('Acesso não permitido, o usuário está ausente. Por favor procure a gerência.');";
				    } else if ($mSenha['C007_Dispositivo_Acesso'] != '0' && $mSenha['C007_Dispositivo_Acesso'] != '1' && $l != 'root'){
						$js .= "alert('Acesso não permitido por este dispositivo. Por favor procure a gerência.');";
				    } else {
		   	   			if (!empty($mSenha['C007_Cargo'])) {
							$mSenha['C007_Cargo']='  -   (  '.$mSenha['C007_Cargo'].'  )';
			   			}
				   		// verifica se a senha esta vencida 30 DIAS
				   		$Sql=mysqli_query("select * from $cBd_Configuracao.C007 where C007_Login='$l' and C007_Ativo='S' and C007_Data_Alteracao_Senha<=date_sub(current_date(), interval 30 day)");
			   			echo mysql_error();
				  		if (empty($mC004['C004_Caminho_Sistema'])) {
				  			$js .= "alert('Pasta da empresa não definida.');";
						} else {
							mysqli_query("UPDATE C007 SET C007_Ultimo_Acesso = NOW() WHERE C007_Id = '{$mSenha['C007_Id']}'");

							// Regenera o ID de sessão após autenticação bem-sucedida (evita fixação de sessão)
							session_regenerate_id(true);

							// Habilita mensagens de liberação de crédito
							setcookie('liberacaoCreditoFlag', 2, 0, '/');

							// Atribui a Variavel de Sessao o Nome e o Codigo do Usuario
							$_SESSION['Modulo_NFe']='S';
							$_SESSION['Usuario_Atual']=$mSenha['C007_Id'];
							$_SESSION['Nome_Usuario']=$mSenha['C007_Nome'];
							$_SESSION['Primeiro_Nome_Usuario']=$mSenha['C007_Primeiro_Nome'];
							$_SESSION['Alcada_Usuario']=$mSenha['C007_Flag_Vendedor'];
							$_SESSION['Cargo_Usuario']=$mSenha['C007_Cargo'];
							$_SESSION['Perfil_Usuario']=$mSenha['C021_Id'];
							$_SESSION['Empresa_Atual']="$e";
							$_SESSION['Empresa_Logada']="$e";
							$_SESSION['Nome_Empresa_Atual']=$mC004['C004_Nome_Empresa'];
							$_SESSION['Banco_Dados']=$confUsuario['dbDatabase'];
							$_SESSION['sb']="$sb";
							$_SESSION['nWidth_Padrao']="$nWidth_Padrao";
							$_SESSION['nHeight_Padrao']="$nHeight_Padrao";
							$_SESSION['Pasta_Empresa']=$mC004['C004_Caminho_Sistema']; //-- pasta onde estao localizados os dados da empresa
							$_SESSION['cBd_Configuracao']=$cBd_Configuracao;
							$_SESSION['Usuario_Suporte']=$mSenha['C007_Usuario_Suporte'];
							$_SESSION['User_Suport']="$us";
							$_SESSION['Usuario_Contador']="$uc";
							$_SESSION['Empresa_Usuaria']="$Empresa_Usuaria";
							$_SESSION['Data_Hora_Login']=time();
							$_SESSION['Cliente_Atual']=NULL;
							$_SESSION['Cliente_Atual_Contato']=NULL;
							$_SESSION['Usuario_Alcada']=$mSenha['C007_Flag_Vendedor'];
							if(!empty($mSenha['C007_Acesso_Hora_Inicio']) && !empty($mSenha['C007_Acesso_Hora_Fim'])){
								$_SESSION['Acesso_Hora_Inicio']=$mSenha['C007_Acesso_Hora_Inicio'];
								$_SESSION['Acesso_Hora_Fim']=$mSenha['C007_Acesso_Hora_Fim'];
							} else {
								$_SESSION['Acesso_Hora_Inicio']=NULL;
								$_SESSION['Acesso_Hora_Fim']=NULL;	
							}

							// -> Registra um historico de Enderecos
							$_SESSION['URL_Historico']="";

							// -> Registra um historico de Tipos de Telas
							$_SESSION['URL_Tipo_Historico']="";

							// -> Registra um historico de nomes lógicos das páginas acessadas
							$_SESSION['URL_Descricao_Historico']="";

							// -> Registra um historico com as Entidades Principais
							$_SESSION['URL_Entidade_Principal']="";

							//
							// Guarda o numero pagina acessada no monta_consultas_fme
							// Utiliza como chave da matriz a Entidade+cTela+Entidade_Principal+Id_Entidade_Principal
							//
							$_SESSION['Pagina_Monta_Consulta']="";
							$_SESSION['Chave_Pagina_Monta_Consulta']="";

							$_SESSION['PaginaQueIniciou']=$PaginaQueIniciou;

                            apc_delete("cacheC007-{$confUsuario['dbDatabase']}");
							$js .= "location.replace('./menu.php');";
							$redirectJS = <<<EOT
								<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
								<script>
								  	$(document).ready(function() {
								  		{$js}
								  		$('input:first').focus();
								  	});
								</script>
EOT;
							die($redirectJS);
							//echo "<div class='divHidden'><script type='text/javascript'> if (typeof jQuery != 'undefined') { fecharJanela('{$g['divId']}'); } </script></div>";
						}
		   		   }
			   }
		   }
		}
	}
}

$faviconPath = '';
if (isset($sys['favicon'])) {
	$favicon 	 = $sys['favicon']; 
	$faviconPath =	"<link rel='icon' type='image/x-icon' href={$favicon}>";
}

// Caso for a sigma irá logar com a empresa 4 como padrão. Analisar ua maneira disso ser configurável por empresa.
if($confUsuario['dbDatabase']=='e212bkp'){
	$sql = "SELECT C004_Nome_Abreviado FROM C004 WHERE C004_Id=4";
}else{
	$sql = "SELECT C004_Nome_Abreviado FROM C004 ORDER BY C004_Id ASC LIMIT 1";
}
$res = mysqli_query($sql);
$row = mysql_fetch_assoc($res);

$backgroundColor = $sys['loginBackground'];
$rodape = 'Hardness Sistemas ' . date('Y');
$logo = $sys['logoSistema'];
$row['C004_Nome_Abreviado'] = substr($row['C004_Nome_Abreviado'], 0 , 27);

//Funções de esqueci a senha
$pswScript = <<< JS
	abrirPsw = $(".psw");
	fecharPsw = $("#close-psw");
	modalPsw = $("#modal-psw");

	abrirPsw.click(() => {
		handleAbreModal();
	});

	fecharPsw.click(() => {
		handleFechaModal();
	});

	function handleAbreModal() {
		modalPsw.slideDown(200);
	}

	function handleFechaModal() {
		modalPsw.slideUp(200);
	}

	//Função do botão de esqueci a senha ================================
	$('.loader-container').bind('ajaxStart', function(){
    	$(this).show();
	}).bind('ajaxStop', function(){
		$(this).hide();
	});
	$(".msg").slideUp();
    $("#psw-button").click(() => {
		$(".msg").hide();
        let pswEmail = $("#pswEmail").val()
        $.ajax({
            url: 'esqueci_senha.php',
            type: 'GET',
            dataType: 'jsonp',
            data: {
                'ajax': true,
                'eMail': encodeURIComponent(pswEmail),
            },
            success: function(request) {
                if (request.code) {
                    $(".msg").css("background", "#65cf81")
                    $(".msg-text").html(request.data)
                    $(".msg").slideDown(200, () => {
                        setTimeout(() => {
                            $(".msg").slideUp(200)
                        }, 5000)
                    })
                } else {
                    $(".msg").css("background", "#E82728")
                    $(".msg-text").html(request.data)
                    $(".msg").slideDown(200, () => {
                        setTimeout(() => {
                            $(".msg").slideUp(200)
                        }, 5000)
                    })
                }
            },
            error: function(erro) {
                $(".msg").css("background", "#E82728")
                $(".msg-text").html("Algo deu errado. ")
                $(".msg").slideDown(200, () => {
                    setTimeout(() => {
                        $(".msg").slideUp(200)
                    }, 5000)
                })
            }
        });
    })
JS;

echo <<<EOT
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width" />
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<title></title>
{$faviconPath}
<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="./bootstrap/js/bootstrap.js"></script>
<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<style>
  	html, body {
	    height: 100%;
	}
	body {
		background: #F0F1F3;
	}

	.container-narrow {
		margin: 0 auto;
		max-width: 300px;
		text-align: center;
		padding: 30px 0 20px 0;
	}

	.form-signin {
	  	max-width: 300px;
	  	padding: 50px 30px;
	  	margin: 0 auto 20px;
	  	background-color: #fff;
	  	-webkit-border-radius: 10px;
	    -moz-border-radius: 10px;
	    border-radius: 10px;
	  	-webkit-box-shadow: 0 1px 2px rgba(0, 27, 68, 0.1);
	    -moz-box-shadow: 0 1px 2px rgba(0, 27, 68, 0.1);
	    box-shadow: 0 10px 15px rgba(0, 27, 68, 0.1);
		position: relative;
	}
 	.form-signin .form-signin-heading, .form-signin .checkbox {
    	margin-bottom: 10px;
  	}
  	.form-signin input[type="text"], .form-signin input[type="password"] {
	    font-size: 16px;
	    height: auto;
	    margin-bottom: 15px;
	    padding: 7px 9px;
  	}
  	#form-login {
	   /*background: url("nfe_transp.png") no-repeat scroll -2px -13px white;*/
	   background-color: white;
  	}
	.footer p {
		color: #000;
	}
	.psw {
		position: absolute;
		top: 198px;
		cursor: pointer;
	}
	.psw-modal {
		max-width: 500px;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		box-shadow: 0 0 0 2000px #0000004d;
	}
	.psw-modal h5 {
		font-size: 20px;
	}
	.psw-modal p {
		font-size: 18px;
	}
	.psw-modal input[type="email"] {
		margin: 20px 0;
		padding: 20px 10px;
		font-size: 18px;
	}
	#close-psw {
		color: #e22424;
		top: 190px;
	}
	.msg {
		width: 100%;
		padding: 20px 0;
		background: #e22424;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 20px;
		color: #fff;
		margin-top: 40px;
	}
	.msg-text {
		margin: 0;
		padding: 0;
	}
	.loader-container {
		display: none;
	}
	.loader {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background: #ffffffb6;
		width: 100%;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 10px;
	}
	.lds-roller {
	display: inline-block;
	position: relative;
	width: 80px;
	height: 80px;
	}
	.lds-roller div {
	animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
	transform-origin: 40px 40px;
	}
	.lds-roller div:after {
	content: " ";
	display: block;
	position: absolute;
	width: 7px;
	height: 7px;
	border-radius: 50%;
	background: #0064de;
	margin: -4px 0 0 -4px;
	}
	.lds-roller div:nth-child(1) {
	animation-delay: -0.036s;
	}
	.lds-roller div:nth-child(1):after {
	top: 63px;
	left: 63px;
	}
	.lds-roller div:nth-child(2) {
	animation-delay: -0.072s;
	}
	.lds-roller div:nth-child(2):after {
	top: 68px;
	left: 56px;
	}
	.lds-roller div:nth-child(3) {
	animation-delay: -0.108s;
	}
	.lds-roller div:nth-child(3):after {
	top: 71px;
	left: 48px;
	}
	.lds-roller div:nth-child(4) {
	animation-delay: -0.144s;
	}
	.lds-roller div:nth-child(4):after {
	top: 72px;
	left: 40px;
	}
	.lds-roller div:nth-child(5) {
	animation-delay: -0.18s;
	}
	.lds-roller div:nth-child(5):after {
	top: 71px;
	left: 32px;
	}
	.lds-roller div:nth-child(6) {
	animation-delay: -0.216s;
	}
	.lds-roller div:nth-child(6):after {
	top: 68px;
	left: 24px;
	}
	.lds-roller div:nth-child(7) {
	animation-delay: -0.252s;
	}
	.lds-roller div:nth-child(7):after {
	top: 63px;
	left: 17px;
	}
	.lds-roller div:nth-child(8) {
	animation-delay: -0.288s;
	}
	.lds-roller div:nth-child(8):after {
	top: 56px;
	left: 12px;
	}
	@keyframes lds-roller {
	0% {
		transform: rotate(0deg);
	}
	100% {
		transform: rotate(360deg);
	}
	}
</style>
<script>
  	$(document).ready(function() {
  		{$js}
  		$('input:first').focus();
		{$pswScript}
  	});
</script>
</head>
<body>
<div class="container">
	<div style="text-align: center;">
    	<!--<h3 class="muted">Grupo Boticário</h3>-->
    	<img src="{$logo}" style="width: {$widthLogo}; height: 99px; margin: 40px 0 25px 0;" />
  	</div>
  	<form id="form-login" class="form-signin" action="./index.php" method="post">
	  	<h4 style="margin-bottom: 20px; text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);">{$row['C004_Nome_Abreviado']}</h4>
    	<!--<h2 class="form-signin-heading">Login</h2>-->
    	<div class="alert alert-error hide">
        	<strong>Erro!</strong> <span></span>
      	</div>
    	<input type="text" class="input-block-level" placeholder="Usuário" name="login">
    	<input type="password" class="input-block-level" placeholder="Senha" name="senha">
		<p class="psw">Esqueceu sua senha?</p>
    	<button class="btn btn-primary pull-right" type="submit">Entrar</button>
    	<div class="clearfix"></div>
	</form>
  	<div class="footer">
    	<p style="text-align: center;">&copy; {$rodape}</p>
    </div>
	<div id="modal-psw" class="psw-modal form-signin hide">
		<div class="loader-container">
			<div class="loader">
				<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
			</div>	
		</div>
		<h5>Recuperação de Senha</h5>
		<p>Informe seu email de cadastro para recuperar sua senha.</p>
		<input type="email" class="input-block-level" placeholder="Email" name="email" id="pswEmail">
		<p id="close-psw" class="psw">Fechar</p>
		<button id="psw-button" class="btn btn-primary pull-right" type="button">Enviar</button>
		<div class="msg"><p class="msg-text"></p></div>	
	</div>
</div> <!-- /container -->
</body>
</html>
EOT;


