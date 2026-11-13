<?php
namespace hardness;

global $g;

require_once("bibliotecas/classes/B2B001.php");
$B2B001 = new B2B001();

switch ($g['r'][3]) {

	case 'b2b001FormSenha':
		global $g;
       
        $MostrarPedidos=$g['C031']['MostrarPedidos'];
        $MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
        $MostrarBoletos=$g['C031']['MostrarBoletos'];
        $MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];

        /**
		 * Trazer total de itens do carrinho
		 */
		 $T269 = mysql_query("select count(*) from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
		 $T269_Itens = mysql_fetch_array($T269);
 
		/**
		 * Trazer dados do cliente
		 */
		 $sqlD024 = mysql_query("SELECT D024_Nome_Empresa,D013_Nome_Contato FROM D024 LEFT JOIN D013 ON D013_D024_Id=D024_Id WHERE D013_Id = '{$g['clienteAtualContato']}'");
		 $resD024 = mysql_fetch_array($sqlD024);
 


		//manipular nome do cliente
		//caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
		if(strpos($resD024['D013_Nome_Contato'],"@")){
			$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
		}else{
			//caso contrario pega o primeiro e ultimo nome da pessoa
			$temp = explode(" ",$resD024['D013_Nome_Contato']);
			$resD024['D013_Nome_Contato'] = $temp[0] . " " . $temp[count($temp)-1];
		}


		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = 'geral';
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
		$form_tabela = 'D013';
		$form_campo_pk = 'D013_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'b2b001FormSenha_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/b2b/b2b001/form/b2b001FormSenha/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
                    'D013_Senha1' => array('name' => 'D013_Senha1', 'label'=>'Senha atual', 'type' => 'password'),
                    'D013_Senha2' => array('name' => 'D013_Senha2', 'label'=>'Nova senha', 'type' => 'password'),
                    'D013_Senha' => array('name' => 'D013_Senha', 'label'=>'Repita nova senha', 'type' => 'password')  
				)
			)
		);
        $form = new Formulario('b2b001FormSenha');
		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/

		// echo "senha atual ".$r_D013_Senha1;
		// echo "<br>nova senha ".$r_D013_Senha2;
		// echo "<br>repetição ".$r_D013_Senha;

		
		/*
		* PARTE 4: Gerar o formulário
		*/
        $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        
		/**
		* PARTE 5: Exibir o formulário
        */
        //variaveis do menu
		$g['smarty']->assign('mostrarPedidos', $MostrarPedidos);
		$g['smarty']->assign('mostrarOrcamentos', $MostrarOrcamentos);
		$g['smarty']->assign('mostrarBoletos', $MostrarBoletos);
        $g['smarty']->assign('mostrarNotasFiscais', $MostrarNotasFiscais);
        $g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
        $g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('logo',"http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}");
		
  		//Criando os botões do formulário
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
      	$g['smarty']->assign('href',$enviar_acaoHref);
  		//if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }

        $botoesEsq = $botoesDir = '';
        $botoesDir .= '<a class="button_Alterar_Senha" onClick="javascript:form.submit();">Alterar</a>';
		$botoesEsq .= '<a href="/b2b/b2b001/grid/gridB2BPrincipal/?ajax=true">Cancelar</a>';
        $g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);

        //chamando template de form do B2B
        $g['smarty']->display('formB2B.tpl');
        

        if(isset($r_operacao)){			
                $SenhaAtual = $r_D013_Senha1;
                $D013 = mysql_query("Select D013_Id,D013_Senha,D013_E_Mail from D013 where D013_Senha = '{$SenhaAtual}' and D013_Id='{$g['clienteAtualContato']}'");
                $rD013 = mysql_fetch_array($D013);

                if(mysql_num_rows($D013)>=1){
                    $NovaSenha = $r_D013_Senha2;
                    $SenhaRepeticao =$r_D013_Senha;
                    $Email=$rD013["D013_E_Mail"];

                    if($NovaSenha==$SenhaRepeticao){
                    	if($SenhaRepeticao!=$rD013['D013_Senha']){
                    		mysql_query("update D013 set D013_Senha = '{$SenhaRepeticao}' where ifnull(D013_Senha,'')!='' and D013_E_Mail='{$Email}'");
                    		echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$(function () {
				                    		$( "#mensagemSenha" ).dialog({ 
													resizable: false,
												    height: "auto",
												    width: 300,
												    modal: true,
													buttons: {
												        Ok: function() {
												          $( this ).dialog( "close" );
												          window.location.href = "/b2b/b2b001/grid/gridB2BPrincipal?ajax=true";	
												        }
									      			}
										      	});	
									    });
								} </script></div>
								<div id="mensagemSenha" title="Alteração de senha" style="display:none;">
									<p>Senha alterada com sucesso.</p>
								</div>
EOT;
                    	}else{
                    		echo<<<EOT
                    		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	                    		$(function () {
		                    		$( "#mensagemSenha" ).dialog({ 
											resizable: false,
										    height: "auto",
										    width: 300,
										    modal: true,
											buttons: {
										        Ok: function() {
										          $( this ).dialog( "close" );
										        }
							      			}
								      	});	
							      	});
                    		}</script></div>
                    		<div id="mensagemSenha" title="Alteração de senha" style="display:none;">
								<p>A senha nova digitada é igual a senha em cadastro.</p>
							</div>
EOT;

                    	}	

                    }else{
                    	echo<<<EOT
                    		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    		$(function () {
	                    		$( "#mensagemSenha" ).dialog({ 
										resizable: false,
									    height: "auto",
									    width: 300,
									    modal: true,
										buttons: {
									        Ok: function() {
									          $( this ).dialog( "close" );
									        }
						      			}
						      		});	
						      	});
                    		}</script></div>
                    		<div id="mensagemSenha" title="Alteração de senha" style="display:none;">
								<p>A senha nova não confere em ambos os campos.</p>
							</div>
EOT;

                    }

                }else{
                	echo<<<EOT
                    		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    		$(function () {
	                    		$( "#mensagemSenha" ).dialog({ 
										resizable: false,
									    height: "auto",
									    width: 300,
									    modal: true,
										buttons: {
									        Ok: function() {
									          $( this ).dialog( "close" );
									        }
						      			}
						      		});	
						      	});
                    		}</script></div>
                    		<div id="mensagemSenha" title="Alteração de senha" style="display:none;">
								<p>A senha atual digitada não confere com a senha em cadastro.</p>
							</div>
EOT;



                }
        }            	
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#divCarrinho').click( function() {					
				window.location.href = "/b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
			});
	} </script></div>
EOT;
    break;

}