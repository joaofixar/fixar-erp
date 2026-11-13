<?php
namespace hardness;
$script = '';
global $g;
include('conf.php');
$arquivo = $_REQUEST['arquivo'];
$case = $_REQUEST['case'];
$erroMsg = '';
if (isset($_REQUEST['scan'])) {
	$erro = '';
	// Login
	@session_destroy();
	@session_start();
	$e=1;
	$l=$_REQUEST['scan'];
	$s=false;
	
	if (empty($l)) {
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
					              where C007_Id='$l'
					                and C007_Ativo='S'
					                and (C007_D024_Id=0 or C007_D024_Id is null)");
		if (mysql_num_rows($Sql)==0) {
			$erro = 'Erro! Login ou Senha inválida.';
		} else {
			$mSenha=mysql_fetch_array($Sql);
			$Senha=base64_encode($s);
			$cSenha=base64_decode($mSenha['C007_Senha']);
			// Verifica se a Senha Digitada pelo usuario � valida.
			//if ($Senha!=$mSenha['C007_Senha']) {
			if (false) {
				$erro = 'Erro! Login ou Senha inválida.';
			} else {
				if (!empty($mSenha['C007_Cargo'])) {
					$mSenha['C007_Cargo']='  -   (  '.$mSenha['C007_Cargo'].'  )';
				}
				// verifica se a senha esta vencida 30 DIAS
				$Sql=mysql_query("select * from $cBd_Configuracao.C007 where C007_Id='$l'
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
		header('Location: ./menu.php?arquivo=' . urlencode($arquivo) . '&case=' . urlencode($case));
	} else {
		$erroMsg = <<<EOT
			<div style="text-align: center;"><h5>{$erro}</h5></div>
EOT;
	}
}
include('inc.header.php');

echo <<<EOT
	<div>
    <!--<img src="./imagens/crv.jpg" style="width: 100%; height: 70px;" />-->
    <img src="http://www.carsigma.com.br/images/logo.jpg" style="width: 100%; height: 70px;" />
    </div>
EOT;
$scan = $_REQUEST['scan'];

echo <<<EOT
	<div class="divHidden"> 
	<script language="JavaScript">
		var refreshMenu;
		function submitonEnter(e){
			var keycode;
			if (window.event) keycode = window.event.keyCode;
			else if (e) keycode = e.which;
			if(keycode == "13"){
				document.login.submit();
			}
		}
		// focus sempre no scan
    	$(document).unbind('click');
    	$(document).bind('click', function(e) {
    		if (e.target.id == 'senha') {
    			return true;
    		} else {
    			$('#scan').focus();
    			return false;
    		}
    	});
    	$(document).ready(function() {
    		$('#scan').focus();
    	});
	</script>
	</div>
<form name="login" action="verificaLogin.php?arquivo={$arquivo}&case={$case}" method="POST" data-ajax="false">
<strong style="font-size: 160%;">{$erroMsg}</strong>
<input name="scan" id="scan" type="text" onkeypress="submitonEnter(event);" style="width: 97%; position: absolute; top: -100px;" />
<div align="center" class="login" style="margin-top: 30%;"><br><strong style="font-size: 120%;">LEIA O CODIGO DE ACESSO<br> </br></div>
</form>
EOT;


include('inc.footer.php');