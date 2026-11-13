<?php
namespace hardness;
require_once($g['pathRaiz'].'/bibliotecas/confUsuario.php');
require_once($g['pathRaiz'].'/bibliotecas/conexaoBanco.php');
include_once($g['pathRaiz']."/configuracoes/sysConfig.php");

$js = '';
if (isset($_REQUEST['login'])) {
	if ($_SERVER['HTTP_HOST'] == 'hardness.hardness.com.br' || $_SERVER['HTTP_HOST'] == 'cha.chasistemas.com.br' || $_SERVER['HTTP_HOST'] == 'premier.premiersistemas.com.br') {
		$usarSenhaRootPadrao = true;
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

  	$sqlLoginDuplicado = mysql_query("SELECT C007_Login	FROM C007 WHERE C007_Login='{$l_}' AND C007_Login!='root' AND C007_Ativo='S'");

  	if (empty($l)) {
	   $js .= "Digite o login.";
	} else if (empty($s)) {
		$js .= "Digite a senha.";
	} else if (empty($e)) {
		$js .= "Selecione a empresa.";
	} else if (mysql_num_rows($sqlLoginDuplicado) > 1){
		$js .= "Existe mais de um usuário cadastrado com seu login, comunique o administrador do seu sistema.";
	}else {
		$sqlEmpresaUsuario = mysql_query("SELECT MIN(C019_C004_Id) as C019_C004_Id, C007_Login
											FROM C007
		                          	   LEFT JOIN C019 ON C019_C007_Id=C007_Id
		                               LEFT JOIN C021 ON C021_Id=C019_C021_Id
		                          		   WHERE C007_Login='{$l_}' AND C007_Ativo='S'");
		$rowEmpresaUsuario = mysql_fetch_array($sqlEmpresaUsuario);
		if(mysql_num_rows($sqlEmpresaUsuario) == 1){
			$e = $rowEmpresaUsuario['C019_C004_Id'];
		}
		$cC004=mysql_query("select * from C004 where C004_Id='$e'");
		$mC004=mysql_fetch_array($cC004);
		if (empty($rowEmpresaUsuario['C007_Login'])) {
			$js .= "Cadastro de usuário não encontrado.";
		} else if (empty($mC004['C004_Banco_Configuracao'])) {
			$js .= "Banco de configuração nao definido.";
		} else {
			$cBd_Configuracao=$mC004['C004_Banco_Configuracao'];

			$cC020=mysql_query("select C020_Modelo_Sistema_Nfe from C020 where C020_C004_Id='$mC004[C004_Id]'");
			$mC020=mysql_fetch_array($cC020);

			$usuariosSuporte = array(
				'bruno' => '93513b491b333c2c6201d7ae5d95bef3',
				'ericks' => 'f70fc7d8ad7b81747a10fe4ef6f37d01'
			);

		    // Consulta a Senha Digitada do Usuario Selecionado
		    if ($l != 'root' && substr($l, 0, 5) != 'hard_') {
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
		    	$js .= "Login ou Senha inválida (1).";
		    } else {
			    $mSenha=mysql_fetch_array($Sql);
			    // usuário 63 só com login root (caso alguém modificou o nome do usuário);
			    if ($mSenha['C007_Id'] == 63 && $l_ != 'root') {
			    	$js .= "Pasta da empresa não definida.";
			    } else {
				    $Senha=base64_encode($s);
				    $cSenha=base64_decode($mSenha['C007_Senha']);
   				    // Verifica se a Senha Digitada pelo usuario é valida.
   				    $IP_Origem=substr($_SERVER['REMOTE_ADDR'],0,strrpos($_SERVER['REMOTE_ADDR'],'.'));
   				    
   				    if (!$usarSenhaRootPadrao && $Senha!=$mSenha['C007_Senha']) {
   				    	$js .= "Login ou Senha inválida (2).";
				    } else if ($usarSenhaRootPadrao && (
				    			($Senha!=$mSenha['C007_Senha'] && $mSenha['C007_Id'] != 63) || //Senha usuario sistema
				    			($mSenha['C007_Id'] == 63 && (
					    				($l == 'root' && md5($s) != $sys['senhaRootMaster'] && md5($s) != $sys['senhaRootSuporte'] && ($Senha!=$mSenha['C007_Senha'] || $s=='root') && $sys['dominio'] == 'hardness.com.br') || //Acesso root hardness
					    				(substr($l, 0, 5) == 'hard_' && md5($s) != $usuariosSuporte[substr($l, 5)] && $sys['dominio'] == 'hardness.com.br') || //Acesso usuarios hardness
					    				($l == 'root' && md5($s) != $sys['senhaRootSistema'] && md5($s) != $sys['senhaRootMaster'] && ($Senha!=$mSenha['C007_Senha'] || $s=='root') && $sys['dominio'] != 'hardness.com.br')  //Acesso root representantes
				    				)
				    			)
				    		)
				    ) {
			   		 	$js .= "Login ou Senha inválida (3).";
				    } else

				    if ($mSenha['C007_Id'] != 63 && !empty($mSenha['C007_IP_Acesso']) && ($Senha!=$mSenha['C007_Senha_Especial']) && strpos(' '.$mSenha['C007_IP_Acesso'],$IP_Origem)<=0) {
			   		 	$js .= "Desculpe, o acesso foi negado para este local. ({$IP_Origem})";				    
				    } else if ($mSenha['C007_Flag_Ausente'] != 'N' && $l != 'root'){
				    	$js .= "Acesso não permitido, o usuário está ausente. Por favor procure a gerência.";
				    } else if ($mSenha['C007_Dispositivo_Acesso'] != '0' && $mSenha['C007_Dispositivo_Acesso'] != '1' && $l != 'root'){
						$js .= "Acesso não permitido por este dispositivo. Por favor procure a gerência.";
				    } else {
		   	   			if (!empty($mSenha['C007_Cargo'])) {
							$mSenha['C007_Cargo']='  -   (  '.$mSenha['C007_Cargo'].'  )';
			   			}
				   		// verifica se a senha esta vencida 30 DIAS
				   		$Sql=mysql_query("select * from $cBd_Configuracao.C007 where C007_Login='$l' and C007_Ativo='S' and C007_Data_Alteracao_Senha<=date_sub(current_date(), interval 30 day)");
			   			$js .= mysql_error();
				  		if (empty($mC004['C004_Caminho_Sistema'])) {
				  			$js .= "Pasta da empresa não definida.";
						} else {
							mysql_query("UPDATE C007 SET C007_Ultimo_Acesso = NOW() WHERE C007_Id = '{$mSenha['C007_Id']}'");

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
								$js .= "ConfUsuario não está com raiz setada";
							}

							if(!empty($js)){
								die($$js);
							}		  
						}
		   		   }
			   }
		   }
		}
	}
}
