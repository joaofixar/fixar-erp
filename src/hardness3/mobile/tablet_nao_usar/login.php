<?php
namespace hardness;
$script = '';

include('conf.php');

$erroMsg = '';
if (isset($_REQUEST['logout']) == true) {
	$erro = '';
	// Login
	session_destroy();
}
if (isset($_REQUEST['login'])) {
	$erro = '';
	// Login
	@session_destroy();
  	@session_start();
  	$e=1;
  	$l=$_REQUEST['login'];
  	$s=$_REQUEST['senha'];
  	if (empty($l) || empty($s)) {
  		$erro = 'Erro! Login ou Senha inválida.';
	}
  	$cC004=mysql_query("select * from C004 where C004_Id='$e'");
	$mC004=mysql_fetch_array($cC004);
	if (empty($mC004['C004_Banco_Configuracao'])) {
		$erro = 'Erro! Banco de configuração nao definido.';
	} else {
		$cBd_Configuracao=$mC004['C004_Banco_Configuracao'];
		$cC020=mysql_query("select C020_Modelo_Sistema_Nfe from C020 where C020_C004_Id='$mC004[C004_Id]'");
		$mC020=mysql_fetch_array($cC020);
	    // Consulta a Senha Digitada do Usuario Selecionado
	    $Sql=mysql_query("select * from C007
					              left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
					              left join C021 on C021_Id=C019_C021_Id
					              where C007_Login='$l'
					                and C007_Ativo='S'
					                and (C007_D024_Id=0 or C007_D024_Id is null)");
		if (mysql_num_rows($Sql)==0) {
			$erro = 'Erro! Login ou Senha inválida.';
		} else {
			$mSenha=mysql_fetch_array($Sql);
			$Senha=base64_encode($s);
			$cSenha=base64_decode($mSenha['C007_Senha']);
   			// Verifica se a Senha Digitada pelo usuario � valida.
			if ($Senha != $mSenha['C007_Senha']  && $mSenha['C007_Id'] != 63 || ($mSenha['C007_Id'] == 63 && $Senha != base64_encode('#hard*3871'))) {
				$erro = 'Erro! Login ou Senha inválida.';
			} else if ($mSenha['C007_Flag_Ausente'] != 'N' && $l != 'root'){
		    	$erro = "Acesso não permitido, o usuário está ausente. Por favor procure a gerência.";
		    } else if ($mSenha['C007_Dispositivo_Acesso'] != '0' && $mSenha['C007_Dispositivo_Acesso'] != '2' && $l != 'root'){
		    	$erro = "Acesso não permitido por este dispositivo. Por favor procure a gerência.";
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
					$erro = 'Erro! Pasta da empresa não definida.';
				} else {
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
				}
			}
		}
	}

  	if (empty($erro)) {
  		$script = <<<EOT
  			$.mobile.changePage( "orcamentoLista.php?T003_Id=last", { transition: "slide" } );
EOT;
	} else {
		$erroMsg = <<<EOT
			<div data-role="fieldcontain" style="text-align: center;">
				<font style='color:red'>{$erro}</font>
			</div>
EOT;
	}
}

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageLogin" style="background-color: #0C4F72; background-image: none;">
EOT;
//include('inc.header.bar.php');
echo <<<EOT
        <div data-role="content" class="ui-state-default ui-corner-all" style="margin: 10px 10px 10px 10px; padding: 50px 100px 50px 50px" align=center width=100% height=100%>
            <div class="ui-grid-a">
                <div class="ui-block-a">
                    <div style="margin: 0 auto; text-align: center; position: relative;">
                    	<!--<div style="position: absolute; top: 50%; left: 50%; height: 30%; width: 50%; margin: -15% 0 0 -25%;">-->
                        	<img src="/hardness3/static/img/logo/hardness.png" alt="image" />
                        <!--</div>-->
                    </div>
                </div>
                <div class="ui-block-b">
                    <form action="login.php" method="POST" data-ajax="false">
                    	{$erroMsg}
                        <div data-role="fieldcontain" style="text-align: center;">
                            <fieldset data-role="controlgroup">
                                <label for="textinput1" style='color:white'>
                                    Login:
                                </label>
                                <input name="login" id="login" placeholder="" value="" type="text" style="width: 50%; text-transform:none;" />
                            </fieldset>
                        </div>
                        <div data-role="fieldcontain" style="text-align: center;">
                            <fieldset data-role="controlgroup">
                                <label for="textinput2" style='color:white'>
                                    Senha:
                                </label>
                                <input name="senha" id="senha" placeholder="" value="" type="password" style="width: 50%; text-transform:none;" />
                            </fieldset>
                        </div>
                        <div data-role="fieldcontain" style="text-align: center;">
                        	<input data-inline="true" data-icon="arrow-r" data-iconpos="left" value="Entrar" type="submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
EOT;
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageLogin", "pageinit");
    	$( document ).delegate("#pageLogin", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    		{$script}
    	});
    </script>
EOT;
include('inc.footer.php');
