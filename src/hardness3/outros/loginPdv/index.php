<?php

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco-5.php');
require_once('../../bibliotecas/funcoesGerais.php');
require_once('../../bibliotecas/segurancaSenha.php');

if (!isset($_REQUEST['empresa']) || empty($_REQUEST['empresa'])) {
	die("A empresa não foi selecionada! Por favor, contate o suporte técnico.");
}

$js = '';
if (isset($_REQUEST['login'])) {
	if ($_SERVER['HTTP_HOST'] == 'hardness.hardness.com.br') {
		$usarSenhaRootPadrao = false;
	} else {
		$usarSenhaRootPadrao = false;
	}
	// Login!
	@session_destroy();
	@session_start();
	$e=$_REQUEST['empresa'];
	$l=$_REQUEST['login'];
	$s=$_REQUEST['senha'];
	$Banco_Dados = $w = $sb = $us = $uc = $Empresa_Usuaria = $PaginaQueIniciou = $nWidth_Padrao = $nHeight_Padrao = '';
	$user = explode('/', $confUsuario['pathRaiz']);
  	$user = $user[2];
  	if (empty($l)) {
	   $js .= "alert('Digite o login.');";
	} else if (empty($s)) {
		$js .= "alert('Digite o senha.');";
	} else if (empty($e)) {
		$js .= "alert('Selecione a empresa.');";
	} else {
					$cC004=mysql_query("select * from C004 where C004_Id='$e'");
					$mC004=mysql_fetch_array($cC004);
					if (empty($mC004['C004_Banco_Configuracao'])) {
						$js .= "alert('Banco de configuração nao definido.');";
					} else {
						$cBd_Configuracao=$mC004['C004_Banco_Configuracao'];
						
						$cC020=mysql_query("select C020_Modelo_Sistema_Nfe from C020 where C020_C004_Id='$mC004[C004_Id]'");
						$mC020=mysql_fetch_array($cC020);

					   // Consulta a Senha Digitada do Usuario Selecionado
					   if ($l != 'root') { 
					   		$Sql=mysql_query("select * from C007
					                          left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
					                          left join C021 on C021_Id=C019_C021_Id
					                          where C007_Login='$l'
					                          and C007_Ativo='S'
					                          and (C007_D024_Id=0 or C007_D024_Id is null)");
					   } else {
					   	   $Sql=mysql_query("select * from C007
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
						   if ($mSenha['C007_Id'] == 63 && $l != 'root') {
						   	   $js .= "alert('Pasta da empresa não definida.');";
						   } else {
							$Senha=base64_encode($s);
							$senhaValida=\hardness\SegurancaSenha::validarSenhaArmazenada($s, $mSenha['C007_Senha']);
   							   // Verifica se a Senha Digitada pelo usuario é valida.
   							   $IP_Origem=substr($_SERVER['REMOTE_ADDR'],0,strrpos($_SERVER['REMOTE_ADDR'],'.'));
							if (!$usarSenhaRootPadrao && !$senhaValida) {
   							   	   $js .= "alert('Login ou Senha inválida (2).');";
							} else if ($usarSenhaRootPadrao && ((!$senhaValida && $mSenha['C007_Id'] != 63) || ($mSenha['C007_Id'] == 63 && $Senha != base64_encode('#hard*3871')))) {
						   			$js .= "alert('Login ou Senha inválida (2).');";
							   } else if ($mSenha['C007_Id'] != 63 && !empty($mSenha['C007_IP_Acesso']) && ($Senha!=$mSenha['C007_Senha_Especial']) && strpos(' '.$mSenha['C007_IP_Acesso'],$IP_Origem)<=0) {
						   			$js .= "alert('Desculpe, o acesso foi negado para este local. ({$IP_Origem})');";
							   } else {
					   	   			if (!empty($mSenha['C007_Cargo'])) {
										$mSenha['C007_Cargo']='  -   (  '.$mSenha['C007_Cargo'].'  )';
						   			}
							   		// verifica se a senha esta vencida 30 DIAS
							   		$Sql=mysql_query("select * from $cBd_Configuracao.C007 where C007_Login='$l'
								                                                 and C007_Ativo='S'
								                                                 and C007_Data_Alteracao_Senha<=date_sub(current_date(), interval 30 day)");
						   			echo mysql_error();
							  		if (empty($mC004['C004_Caminho_Sistema'])) {
							  			$js .= "alert('Pasta da empresa não definida.');";
									} else {
										mysql_query("UPDATE C007 SET C007_Ultimo_Acesso = NOW() WHERE C007_Id = '{$mSenha['C007_Id']}'");

										// Habilita mensagens de liberação de crédito
										setcookie('liberacaoCreditoFlag', 2, 0, '/');
										
										// Atribui a Variavel de Sessao o Nome e o Codigo do Usuario
										$_SESSION['Modulo_NFe']='S';
										$_SESSION['Usuario_Atual']=$mSenha['C007_Id'];
										$_SESSION['Nome_Usuario']=$mSenha['C007_Nome'];
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

										// -> Registra um historico de Enderecos
										$_SESSION['URL_Historico']="";

										// -> Registra um historico de Tipos de Telas
										$_SESSION['URL_Tipo_Historico']="";

										// -> Registra um historico de nomes l�gicos das p�ginas acessadas
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

										if (isset($confUsuario['urlRaiz'])) {
											$js .= "location.replace('{$confUsuario['urlRaiz']}hardness/pdv_novo/nota_principal.php?Operacao=I');";
										} else {
											$js .= "location.replace('http://{$user}/hardness/pdv_novo/nota_principal.php?Operacao=I');";
										}
										//echo "<div class='divHidden'><script type='text/javascript'> if (typeof jQuery != 'undefined') { fecharJanela('{$g['divId']}'); } </script></div>";
									}
					   		   }
						   }
					   }
					}
	}
}

$sql = "SELECT C004_Nome_Abreviado FROM C004 WHERE C004_Id = '{$_REQUEST['empresa']}'";
$res = mysql_query($sql);
if (mysql_num_rows($res) != 1) {
	die("A empresa selecionado não existe! Por favor, contate o suporte técnico.");
}
$row = mysql_fetch_assoc($res);
?>
<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
  <title></title>
  <script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
  <script type="text/javascript" src="./bootstrap/js/bootstrap.js"></script>
  <link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <style>
  body {
    padding-bottom: 40px;
    background: #0C4F72;
  }
  
  .container-narrow {
    margin: 0 auto;
    max-width: 300px;
    text-align: center;
    padding: 30px 0 20px 0;
  }
  
  .form-signin {
    max-width: 330px;
    padding: 19px 29px 29px;
    margin: 0 auto 20px;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
        -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
      box-shadow: 0 1px 2px rgba(0,0,0,.05);
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
  </style>
  <script>
  	$(document).ready(function() {
  		<?php echo $js; ?>
  		$('input:first').focus();
  	});
  </script>
  </head>
<body>
<div class="container">
  <div style="text-align: center;">
    <!--<h3 class="muted">Grupo Boticário</h3>-->
    <img src="./logo.png" style="width: 360px; height: 99px; margin: 40px 0 25px 0;" />
  </div>
  <form id="form-login" class="form-signin" action="./index.php?empresa=<?php echo $_REQUEST['empresa'] ?>" method="post">
  	<h4 style="margin-bottom: 20px; text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);"><?php echo substr($row['C004_Nome_Abreviado'], 0, 35);?></h4>
    <!--<h2 class="form-signin-heading">Login</h2>-->
    <div class="alert alert-error hide">
        <strong>Erro!</strong> <span></span>
      </div>
    <input type="text" class="input-block-level" placeholder="Usuário" name="login">
    <input type="password" class="input-block-level" placeholder="Senha" name="senha">
    <button class="btn btn-primary pull-right" type="submit">Entrar</button>
    <div class="clearfix"></div>
  </form>
  <div class="footer">
      <p style="text-align: center; color: white;">&copy; Hardness Sistemas <?php echo date('Y'); ?></p>
    </div>

</div> <!-- /container -->
</body>
</html>
