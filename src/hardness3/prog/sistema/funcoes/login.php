<?php
namespace hardness;
require_once('bibliotecas/segurancaSenha.php');
/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: fin001tab02
	*/
	case 'login':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'telaLoginFormBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/login/login/';
		// Form Editor
		$form_editor = array(
			'campos' => '{"C004_Id":{"top":8,"left":4,"width":342},
			                "Login":{"top":35,"left":20,"width":125},
			                "Senha":{"top":60,"left":15,"width":125}}'
		);

		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('telaLoginForm');  
		$formArray = array(
			'campos' => array(
				'login' => array(
					'C004_Id' => array('name' => 'C004_Id', 'label' => 'Empresa', 'type' => 'select', 'selectRefresh' => false, 'select' =>
						gGeraSelect('C004','C004_Id','C004_Nome_Empresa,D020_Nome_Cidade',"left join D020 on D020_Id=C004_D020_Id WHERE C004_Flag_Ativo != 'N' order by C004_Id"),
					),
					'Login' => array('name' => 'Login', 'label' => 'Login', 'type' => 'text', 'length' => array(20,255), 'uppercase' => false),
					'Senha' => array('name' => 'Senha', 'label' => 'Senha', 'type' => 'password', 'length' => array(20,255), 'uppercase' => false)
				)	
			)
		);
		$form->definirFormularioArray($formArray);
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Login!
  				@session_destroy();
  				@session_start();
  				$e=$form->campoValorEnviado('C004_Id');
  				$l=$form->campoValorEnviado('Login');
  				$s=$form->campoValorEnviado('Senha');
  				$Banco_Dados = $w = $sb = $us = $uc = $Empresa_Usuaria = $PaginaQueIniciou = $nWidth_Padrao = $nHeight_Padrao = '';
  				//$Banco_Dados="{$_REQUEST['b']}";
  				//$w="{$_REQUEST['w']}";
  				//$sb="{$_REQUEST['sb']}";
  				//$us="{$_REQUEST['us']}";
  				//$uc="{$_REQUEST['uc']}";
  				//$Empresa_Usuaria="{$_REQUEST['Empresa_Usuaria']}";
  				//$PaginaQueIniciou="{$_POST['PaginaQueIniciou']}";
  				
  				//require 'bibliotecas/confUsuario.php';
  				$user = explode('/', $confUsuario['pathRaiz']);
  				$user = $user[2];
  				//include 'bibliotecas/conexaoBanco.php';
  				
  				if (empty($l)) {
				   echo gGeraAlertMsg('Erro ao logar', 'Digite o login.');
   				} else if (empty($s)) {
   					echo gGeraAlertMsg('Erro ao logar', 'Digite a senha.');
				} else if (empty($e)) {
   					echo gGeraAlertMsg('Erro ao logar', 'Selecione uma empresa.');
				} else {
					$cC004=mysql_query("select * from C004 where C004_Id='$e'");
					$mC004=mysql_fetch_array($cC004);
					if (empty($mC004['C004_Banco_Configuracao'])) {
						echo gGeraAlertMsg('Erro ao logar', 'Banco de configuração nao definido.');
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
					   	   echo gGeraAlertMsg('Erro ao logar', 'Login ou Senha inválida (1).');
					   } else {
						   $mSenha=mysql_fetch_array($Sql);
						   // usuário 63 só com login root (caso alguém modificou o nome do usuário);
							if ($mSenha['C007_Id'] == 63 && $l != 'root') {
							   echo gGeraAlertMsg('Erro ao logar', 'Pasta da empresa não definida.');
							} else {
							   $Senha=base64_encode($s);
							   $senhaValida = SegurancaSenha::validarSenhaArmazenada($s, $mSenha['C007_Senha']);
							   // Verifica se a Senha Digitada pelo usuario é valida.
							   $IP_Origem=substr($_SERVER['REMOTE_ADDR'],0,strrpos($_SERVER['REMOTE_ADDR'],'.'));
							   if ($g['C031']['usarSenhaRootPadrao'] == 'N' && !$senhaValida) {
							       echo gGeraAlertMsg('Erro ao logar', 'Login ou Senha inválida (2).');
							   } else if ($g['C031']['usarSenhaRootPadrao'] == 'S' && ((!$senhaValida && $mSenha['C007_Id'] != 63) || ($mSenha['C007_Id'] == 63 && $Senha != base64_encode('#hard!3871')))) {
							       echo gGeraAlertMsg('Erro ao logar', 'Login ou Senha inválida (2).');
							   } else if ($mSenha['C007_Id'] != 63 && !empty($mSenha['C007_IP_Acesso']) && ($Senha!=$mSenha['C007_Senha_Especial']) && strpos(' '.$mSenha['C007_IP_Acesso'],$IP_Origem)<=0) {
							       echo gGeraAlertMsg('Erro ao logar', "Desculpe, o acesso foi negado para este local. ({$IP_Origem})");
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
							  			echo gGeraAlertMsg('Erro ao logar', 'Pasta da empresa não definida.');
									} else {
										mysql_query("UPDATE C007 SET C007_Ultimo_Acesso = NOW() WHERE C007_Id = '{$mSenha['C007_Id']}'");

										// Habilita mensagens de liberação de crédito
										setcookie('liberacaoCreditoFlag', 2, 0, '/');
										
										// Atribui a Variavel de Sessao o Nome e o Codigo do Usuario
										$_SESSION['Modulo_NFe']='S';
										$_SESSION['Usuario_Atual']=$mSenha['C007_Id'];
										$_SESSION['Nome_Usuario']=$mSenha['C007_Nome'];
										$_SESSION['Primeiro_Nome_Usuario']=$mSenha['C007_Primeiro_Nome'];
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
											echo "<div class='divHidden'><script type='text/javascript'> if (typeof jQuery != 'undefined') { location.replace('{$confUsuario['urlRaiz']}erp/'); } </script></div>";
										} else {
											echo "<div class='divHidden'><script type='text/javascript'> if (typeof jQuery != 'undefined') { location.replace('http://{$user}/erp/'); } </script></div>";
										}
										//echo "<div class='divHidden'><script type='text/javascript'> if (typeof jQuery != 'undefined') { fecharJanela('{$g['divId']}'); } </script></div>";
									}
					   		   }
						   }
					   }
					}
				}
			}
		}
		
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');		
//		echo  $g['smarty']->fetch('formEditor.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="telaLoginFormBtn">Login</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		// Eventos
		echo <<<EOT
		<div class='divHidden'><script type='text/javascript'> if (typeof jQuery != 'undefined') {
			$('#Login').bind('keypress', function(e) {
				if (e.keyCode == 13) {
					$('#telaLoginFormBtn').trigger('click');
				}
			});
			$('#Senha').bind('keypress', function(e) {
				if (e.keyCode == 13) {
					$('#telaLoginFormBtn').trigger('click');
				}
			});
		} </script></div>
EOT;
	break;
}
?>
