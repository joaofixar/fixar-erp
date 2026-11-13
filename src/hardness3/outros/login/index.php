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
				    $senhaValida = SegurancaSenha::validarSenhaArmazenada($s, $mSenha['C007_Senha']);
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
								($l == 'root' && md5($s) != $sys['senhaRootMaster'] && md5($s) != $sys['senhaRootSuporte'] && (!$senhaValida || $s=='root') && $sys['dominio'] == 'hardness.com.br') || //Acesso root hardness
					    				(substr($l, 0, 5) == 'hard_' && md5($s) != $usuariosSuporte[substr($l, 5)] && $sys['dominio'] == 'hardness.com.br') || //Acesso usuarios hardness
								($l == 'root' && md5($s) != $sys['senhaRootSistema'] && md5($s) != $sys['senhaRootMaster'] && (!$senhaValida || $s=='root') && $sys['dominio'] != 'hardness.com.br')  //Acesso root representantes
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
				    if (empty($mC004['C004_Caminho_Sistema'])) {
				        $js .= "alert('Pasta da empresa não definida.');";
						} else {
							mysqli_query("UPDATE C007 SET C007_Ultimo_Acesso = NOW() WHERE C007_Id = '{$mSenha['C007_Id']}'");

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

                            apc_delete("cacheC007-{$confUsuario['dbDatabase']}");
							require_once dirname(__DIR__, 2) . '/mcp/autoload.php';
							\HardnessMcp\AdminUi\Security\CsrfGuard::regenerate();
							if (isset($confUsuario['urlRaiz'])) {
								$oauthTx = (string) ($_POST['mcp_oauth_tx'] ?? '');
								$oauthCookie = '__Host-hrd_oauth_' . $oauthTx;
								$oauthResume = preg_match('/^[A-Za-z0-9_-]{16,48}$/', $oauthTx) === 1
									&& isset($_COOKIE[$oauthCookie])
									&& preg_match('/^[A-Za-z0-9_-]{32,64}$/', (string) $_COOKIE[$oauthCookie]) === 1;
								$oauthResumeLocation = '/gerencial/mcp/outro/McpOAuthAuthorizationResume/?ajax=true&tx=' . rawurlencode($oauthTx);
								$js .= $oauthResume
									? "location.replace('{$oauthResumeLocation}');"
									: "location.replace('{$confUsuario['urlRaiz']}erp/');";
							} else {
								$js .= "location.replace('http://{$user}/erp/');";
							}
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
if (!empty($sys['favicon'])) {
	$favicon = htmlspecialchars($sys['favicon'], ENT_QUOTES, 'UTF-8');
	$faviconPath = "<link rel='icon' type='image/x-icon' href='{$favicon}'>";
}

// Caso for a sigma irá logar com a empresa 4 como padrão. Analisar ua maneira disso ser configurável por empresa.
if($confUsuario['dbDatabase']=='e212bkp'){
	$sql = "SELECT C004_Id, C004_Nome_Abreviado, C004_Logo_Marca FROM C004 WHERE C004_Id=4";
}else{
	$sql = "SELECT C004_Id, C004_Nome_Abreviado, C004_Logo_Marca FROM C004 ORDER BY C004_Id ASC LIMIT 1";
}
$res = mysqli_query($sql);
$row = mysql_fetch_assoc($res);
$row['C004_Nome_Abreviado'] = substr($row['C004_Nome_Abreviado'], 0, 27);
$nomeEmpresa = htmlspecialchars($row['C004_Nome_Abreviado'], ENT_QUOTES, 'UTF-8');
$nomeEmpresaInicial = function_exists('mb_substr')
	? mb_strtoupper(mb_substr(trim($row['C004_Nome_Abreviado']), 0, 1, 'UTF-8'), 'UTF-8')
	: strtoupper(substr(trim($row['C004_Nome_Abreviado']), 0, 1));
$nomeEmpresaInicial = htmlspecialchars($nomeEmpresaInicial, ENT_QUOTES, 'UTF-8');

$logoClienteMarkup = "<span class=\"login-company-monogram\" aria-hidden=\"true\">{$nomeEmpresaInicial}</span>";
if (!empty($row['C004_Logo_Marca'])) {
	$logoClienteInfo = pathinfo($row['C004_Logo_Marca']);
	$logoClienteExtensao = !empty($logoClienteInfo['extension']) ? strtolower($logoClienteInfo['extension']) : '';
	if (in_array($logoClienteExtensao, array('gif', 'jpeg', 'jpg', 'png', 'svg', 'webp'))) {
		$logoCliente = '/hardness3/dados_usuarios/'
			. rawurlencode($confUsuario['dbDatabase'])
			. '/imagens/logo/'
			. rawurlencode($row['C004_Id'] . '.' . $logoClienteExtensao);
		$logoCliente = htmlspecialchars($logoCliente, ENT_QUOTES, 'UTF-8');
		$logoClienteMarkup = "<img class=\"login-company-logo\" src=\"{$logoCliente}\" alt=\"Logo de {$nomeEmpresa}\" onerror=\"this.hidden=true;this.nextElementSibling.hidden=false;\"><span class=\"login-company-monogram\" aria-hidden=\"true\" hidden>{$nomeEmpresaInicial}</span>";
	}
}

$backgroundColor = !empty($sys['loginBackground']) ? trim($sys['loginBackground']) : '';
$backgroundStyle = '';
if ($backgroundColor !== '' && !preg_match('/[{}<>]/', $backgroundColor) && stripos($backgroundColor, 'javascript:') === false) {
	$backgroundDeclaration = preg_match('/(?:^|;)\s*background(?:-[a-z-]+)?\s*:/i', $backgroundColor)
		? $backgroundColor
		: "background: {$backgroundColor};";
	$backgroundStyle = ' style="' . htmlspecialchars($backgroundDeclaration, ENT_QUOTES, 'UTF-8') . '"';
}
$nomeRepresentante = !empty($sys['nomeEmpresa']) ? $sys['nomeEmpresa'] : 'Hardness Sistemas';
$rodape = htmlspecialchars($nomeRepresentante, ENT_QUOTES, 'UTF-8') . ' ' . date('Y');
$fundacaoMarkup = '';
if (!empty($sys['anoFundacao']) && preg_match('/^\d{4}$/', (string) $sys['anoFundacao'])) {
	$anoFundacao = (int) $sys['anoFundacao'];
	if ($anoFundacao >= 1800 && $anoFundacao <= (int) date('Y')) {
		$fundacaoMarkup = "<span>Desde {$anoFundacao}</span>";
	}
}
$logo = !empty($sys['logoSistema']) ? $sys['logoSistema'] : '';
$logo = htmlspecialchars($logo, ENT_QUOTES, 'UTF-8');
$widthLogoConfigurada = '';
if (isset($widthLogo)) {
	$widthLogoConfigurada = trim($widthLogo);
} elseif (!empty($sys['widthLogo'])) {
	$widthLogoConfigurada = trim($sys['widthLogo']);
}
if ($widthLogoConfigurada !== '' && preg_match('/^\d+(?:\.\d+)?(?:px|%|rem|em|vw)?$/', $widthLogoConfigurada)) {
	if (is_numeric($widthLogoConfigurada)) {
		$widthLogoConfigurada .= 'px';
	}
	$logoStyle = " style=\"--login-logo-width: " . htmlspecialchars($widthLogoConfigurada, ENT_QUOTES, 'UTF-8') . ";\"";
} else {
	$logoStyle = '';
}
$logoMarkup = $logo !== ''
	? "<img class=\"login-logo\" src=\"{$logo}\" alt=\"Logo do sistema\"{$logoStyle}>"
	: '';
$impactoTitulo = !empty($sys['tituloImpactoLogin']) ? trim($sys['tituloImpactoLogin']) : '';
$textoCredibilidade = !empty($sys['textoCredibilidadeLogin']) ? trim($sys['textoCredibilidadeLogin']) : '';
$impactoMarkup = '';
$classePainelMarca = 'login-visual';
if ($impactoTitulo !== '' || $textoCredibilidade !== '') {
	$impactoTituloMarkup = '';
	$textoCredibilidadeMarkup = '';
	if ($impactoTitulo !== '') {
		$impactoTitulo = htmlspecialchars($impactoTitulo, ENT_QUOTES, 'UTF-8');
		$impactoTituloMarkup = "<h2>{$impactoTitulo}</h2>";
	}
	if ($textoCredibilidade !== '') {
		$textoCredibilidade = htmlspecialchars($textoCredibilidade, ENT_QUOTES, 'UTF-8');
		$textoCredibilidadeMarkup = "<p class=\"login-impact__credibility\">{$textoCredibilidade}</p>";
	}
	$impactoMarkup = <<<HTML
		<div class="login-impact">
			{$impactoTituloMarkup}
			{$textoCredibilidadeMarkup}
		</div>
HTML;
} else {
	$classePainelMarca .= ' login-visual--logo-only';
}
$brandPanelMarkup = <<<HTML
<aside class="{$classePainelMarca}" aria-label="Marca do sistema"{$backgroundStyle}>
	<div class="login-visual__content">
		{$logoMarkup}
		{$impactoMarkup}
	</div>
</aside>
HTML;

//Funções de esqueci a senha
$pswScript = <<< JS
	var abrirPsw = $("#open-psw");
	var fecharPsw = $("#close-psw");
	var modalPsw = $("#modal-psw");
	var formularioPsw = $("#form-psw");
	var mensagemPsw = $(".msg");
	var botaoPsw = $("#psw-button");
	var textoBotaoPsw = botaoPsw.text();

	function handleAbreModal() {
		mensagemPsw.stop(true, true).hide();
		formularioPsw[0].reset();
		abrirPsw.attr("aria-expanded", "true");
		modalPsw.attr("aria-hidden", "false").stop(true, true).fadeIn(160, function() {
			$("body").addClass("is-modal-open");
			$("#pswLogin").focus();
		});
	}

	function handleFechaModal() {
		mensagemPsw.stop(true, true).hide();
		formularioPsw[0].reset();
		modalPsw.attr("aria-hidden", "true").stop(true, true).fadeOut(140, function() {
			$("body").removeClass("is-modal-open");
			abrirPsw.attr("aria-expanded", "false").focus();
		});
	}

	function exibirMensagemPsw(tipo, texto) {
		mensagemPsw
			.removeClass("msg--success msg--error")
			.addClass(tipo === "success" ? "msg--success" : "msg--error")
			.attr("role", tipo === "success" ? "status" : "alert")
			.find(".msg-text").text(texto);
		mensagemPsw.stop(true, true).slideDown(180);
	}

	function definirCarregamentoPsw(carregando) {
		formularioPsw.attr("aria-busy", carregando ? "true" : "false");
		$(".loader-container").attr("aria-hidden", carregando ? "false" : "true").toggle(carregando);
		botaoPsw.prop("disabled", carregando).text(carregando ? "Enviando..." : textoBotaoPsw);
	}

	abrirPsw.click(handleAbreModal);
	fecharPsw.click(handleFechaModal);
	modalPsw.click(function(event) {
		if (event.target === this) {
			handleFechaModal();
		}
	});

	$(document).keydown(function(event) {
		if (!modalPsw.is(":visible")) {
			return;
		}
		if (event.keyCode === 27) {
			event.preventDefault();
			handleFechaModal();
			return;
		}
		if (event.keyCode === 9) {
			var focaveis = modalPsw.find("button:visible, input:visible");
			var primeiro = focaveis.first()[0];
			var ultimo = focaveis.last()[0];
			if (event.shiftKey && document.activeElement === primeiro) {
				event.preventDefault();
				ultimo.focus();
			} else if (!event.shiftKey && document.activeElement === ultimo) {
				event.preventDefault();
				primeiro.focus();
			}
		}
	});

	mensagemPsw.hide();
	formularioPsw.submit(function(event) {
		event.preventDefault();
		mensagemPsw.stop(true, true).hide();
		var pswLogin = $("#pswLogin").val();
		$.ajax({
			url: 'esqueci_senha.php',
			type: 'POST',
			dataType: 'json',
			data: {
				'login': pswLogin
			},
			beforeSend: function() {
				definirCarregamentoPsw(true);
			},
			success: function(request) {
				exibirMensagemPsw(request.code ? "success" : "error", request.data);
			},
			error: function() {
				exibirMensagemPsw("error", "Algo deu errado. ");
			},
			complete: function() {
				definirCarregamentoPsw(false);
			}
		});
	});

	$("#form-login").submit(function() {
		var botaoLogin = $(this).find("button[type='submit']");
		botaoLogin.prop("disabled", true).addClass("is-loading").text("Entrando...");
	});
JS;

$oauthLoginTx = (string) ($_GET['mcp_oauth_tx'] ?? '');
$oauthLoginHidden = preg_match('/^[A-Za-z0-9_-]{16,48}$/', $oauthLoginTx) === 1
	? '<input type="hidden" name="mcp_oauth_tx" value="' . htmlspecialchars($oauthLoginTx, ENT_QUOTES, 'UTF-8') . '">'
	: '';

echo <<<EOT
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<title>Acesso ao sistema</title>
{$faviconPath}
<link href="./login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
<script>
	$(document).ready(function() {
		{$js}
		$('input:first').focus();
		{$pswScript}
	});
</script>
</head>
<body>
<main class="login-shell login-shell--split">
	{$brandPanelMarkup}
	<section class="login-stage">
		<div class="login-access">
			<div class="login-content">
				<header class="login-brand">
					<h1>Seja bem-vindo</h1>
					<div class="login-company-heading">
						{$logoClienteMarkup}
						<div class="login-company-copy">
							<strong id="login-company">{$nomeEmpresa}</strong>
						</div>
					</div>
				</header>
				<form id="form-login" class="login-form" action="./index.php" method="post">
					{$oauthLoginHidden}
					<div class="alert alert-error hide">
						<strong>Erro!</strong> <span></span>
					</div>
					<div class="field-group">
						<label for="login-user">Usuário</label>
						<input id="login-user" type="text" placeholder="Digite seu usuário" name="login" autocomplete="username">
					</div>
					<div class="field-group">
						<label for="login-password">Senha</label>
						<input id="login-password" type="password" placeholder="Digite sua senha" name="senha" autocomplete="current-password">
					</div>
					<button class="button button--primary login-submit" type="submit">Entrar</button>
					<button id="open-psw" class="forgot-password" type="button" aria-haspopup="dialog" aria-controls="modal-psw" aria-expanded="false">Esqueceu a senha?</button>
				</form>
			</div>
			<footer class="footer">
				<p>&copy; {$rodape}</p>
				{$fundacaoMarkup}
			</footer>
		</div>
	</section>
</main>

<div id="modal-psw" class="password-dialog-backdrop hide" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="password-title" aria-describedby="password-description">
	<section class="password-dialog">
		<div class="loader-container" aria-hidden="true">
			<div class="loader" role="status" aria-label="Enviando solicitação">
				<span class="loader-spinner" aria-hidden="true"></span>
			</div>
		</div>
		<header class="password-dialog__header">
			<p class="login-eyebrow">Acesso ao sistema</p>
			<h2 id="password-title">Recuperação de Senha</h2>
			<p id="password-description">Informe seu login para receber as instruções de redefinição de senha.</p>
		</header>
		<form id="form-psw" class="password-form" novalidate>
			<div class="field-group">
				<label for="pswLogin">Login</label>
				<input type="text" placeholder="Login" name="login" id="pswLogin" autocomplete="username">
			</div>
			<div class="msg"><p class="msg-text"></p></div>
			<div class="password-dialog__actions">
				<button id="close-psw" class="button button--secondary" type="button">Fechar</button>
				<button id="psw-button" class="button button--primary" type="submit">Enviar</button>
			</div>
		</form>
	</section>
</div>
</body>
</html>
EOT;

