<?php
namespace hardness;

global $g;

require_once("bibliotecas/classes/B2B001.php");
$B2B001 = new B2B001();

// --------------- Valores utilizados em todas as telas do B2B-------------------------
/**Trazer dados do cliente/contato*/
$sqlD024 = mysql_query("SELECT D024_Nome_Empresa,
							   D013_Nome_Contato,
							   D021_Nome_Empresa
						  FROM D024 
					 LEFT JOIN D013 ON D013_D024_Id=D024_Id AND D013_Id='{$g['clienteAtualContato']}'
					 LEFT JOIN D021 ON D024_D021_Id=D021_Id
						  WHERE D024_Id='{$g['clienteAtual']}'");
$resD024 = mysql_fetch_array($sqlD024);

//Caso o nome do cliente seja um email(tenha um @) o campo recebe tudo o que esta antes do @
if(strpos($resD024['D013_Nome_Contato'],"@")){
	$resD024['D013_Nome_Contato'] = substr($resD024['D013_Nome_Contato'], 0, strpos($resD024['D013_Nome_Contato'], "@"));
}else{
	//caso contrario pega o primeiro e ultimo nome da pessoa
	$temp = explode(" ",$resD024['D013_Nome_Contato']);
	$resD024['D013_Nome_Contato'] = $temp[0];
}

/*Trazer total de itens do carrinho*/
$T269 = mysql_query("SELECT COUNT(*) FROM T269 LEFT JOIN D001 ON D001_Id=T269_D001_Id LEFT JOIN D003 ON D003_Id=D001_D003_Id WHERE T269_D024_Id='{$g['clienteAtual']}'");
$T269_Itens = mysql_fetch_array($T269);

//Variaveis usadas em todas as telas
$BotaoPedidoOrcamento = $g['C031']['HabilitarPedidoOrcamento'];
$MostrarPreco = $g['C031']['HabilitarPreco'];
$MostrarPedidos=$g['C031']['MostrarPedidos'];
$MostrarOrcamentos=$g['C031']['MostrarOrcamentos'];
$MostrarBoletos=$g['C031']['MostrarBoletos'];
$MostrarNotasFiscais=$g['C031']['MostrarNotasFiscais'];
$tipoLoginUsuarioB2B=$g['tipoLoginUsuarioB2B'];
$PermitirVendedorEditarPrecoB2B=$g['C031']['PermitirVendedorEditarPrecoB2B'];
$PermitirVendedorExternoEditarPrecoB2B=$g['C031']['PermitirVendedorExternoEditarPrecoB2B'];
$PermitirRepresentanteEditarPrecoB2B=$g['C031']['PermitirRepresentanteEditarPrecoB2B'];
$logo = "http://".$_SERVER['HTTP_HOST']."/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/logo/{$g['c004']['C004_Logo_Marca_B2B']}";

switch ($g['r'][3]) {

	case 'b2b001FormSenha':
       	/** PARTE 1: Variavéis envidas para o formulário*/
		// Variável indica qual tab do form será exibida
		$tab = 'geral';
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/** PARTE 2: Definição do formulário*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D179';
		$form_campo_pk = 'D179_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'b2b001FormSenha_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/v2b2b/b2b001/form/b2b001FormSenha/?ajax=true';

		$dadosPerfil = array("cliente" => array("label"=>"Cliente:","valor"=>$resD024['D024_Nome_Empresa']),
							 "vendedor"=> array("label"=>"Vendedor:","valor"=>$g['C007']['C007_Primeiro_Nome']),
							 "representante" => array("label"=>"Representante:","valor"=>$resD024['D021_Nome_Empresa']),
							 "empresa" => array("label"=>"Loja:","valor"=>$g['C004']['C004_Nome_Empresa']));		

		//Adicionando campo contato 
		if($g['clienteAtualContato']!="99999"){
			$contato = array("contato" => array("label"=>"Usuário:","valor"=>$resD024['D013_Nome_Contato']));
			$dadosPerfil = array_merge($contato,$dadosPerfil);
		}

		// Array de definição do formulário		
		$formArray = array(
			'campos' => array(
				'geral' => array(
                    'D179_Senha1' => array('name' => 'D179_Senha1', 'label'=>'Senha atual', 'type' => 'password'),
                    'D179_Senha2' => array('name' => 'D179_Senha2', 'label'=>'Nova senha', 'type' => 'password'),
                    'D179_Senha' => array('name' => 'D179_Senha', 'label'=>'Repita nova senha', 'type' => 'password')
				)
			)
		);
        $form = new Formulario('b2b001FormSenha');
		$form->definirFormularioArray($formArray);

		/** PARTE 4: Gerar o formulário*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/** PARTE 5: Exibir o formulário*/
        //variaveis do menu
		$g['smarty']->assign('mostrarPedidos', $MostrarPedidos);
		$g['smarty']->assign('mostrarOrcamentos', $MostrarOrcamentos);
		$g['smarty']->assign('mostrarBoletos', $MostrarBoletos);
        $g['smarty']->assign('mostrarNotasFiscais', $MostrarNotasFiscais);
        $g['smarty']->assign('itensCarrinho', $T269_Itens[0]);
		$g['smarty']->assign('clienteAtualContatoNome', $resD024['D013_Nome_Contato']);
		$g['smarty']->assign('clienteAtualNome', substr($resD024['D024_Nome_Empresa'],0,20));
		$g['smarty']->assign('tipoLoginUsuarioB2B', $tipoLoginUsuarioB2B);
		$g['smarty']->assign('logo',$logo);
		$g['smarty']->assign('dadosPerfil',$dadosPerfil);
		
  		//Criando os botões do formulário
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
      	$g['smarty']->assign('href',$enviar_acaoHref);

		$botoesEsq = $botoesDir = '';
        $botoesDir = "<div class='btn btn-primary btn-user btn-block'><a class='button_Alterar_Senha' onClick='javascript:form.submit();'>Alterar</a></div>";
		$botoesEsq .= "<div class='text-cancel'><a href='/v2b2b/b2b001/grid/gridB2BPrincipal/?ajax=true'>Cancelar</a></div>";
        $g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);

        //chamando template de form do B2B
        $g['smarty']->display('v2formB2B.tpl');
        
        if(isset($r_operacao)){			
			$SenhaAtual = $r_D179_Senha1;
			$D179 = mysql_query("SELECT D013_Id,
										D013_Senha,
										D013_E_Mail,
										D179_Login,
										D179_senha 
								   FROM D013
								   LEFT JOIN D179 ON D179_Login=D013_E_Mail
								  WHERE D179_Senha = '{$SenhaAtual}' 
								    AND D013_Id='{$g['clienteAtualContato']}'");
			$rD179 = mysql_fetch_array($D179);

			if(mysql_num_rows($D179)>=1){
				$NovaSenha = $r_D179_Senha2;
				$SenhaRepeticao =$r_D179_Senha;
				$Email=$rD179["D179_Login"];

				if($NovaSenha==$SenhaRepeticao){
					if($SenhaRepeticao!=$rD179['D179_senha']){
						mysql_query("UPDATE D179 SET D179_Senha = '{$SenhaRepeticao}' WHERE ifnull(D179_Senha,'')!='' AND D179_Login='{$Email}'");
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
													window.location.href = "/v2b2b/b2b001/grid/gridB2BPrincipal?ajax=true";	
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
					window.location.href = "/v2b2b/b2b001/grid/gridB2BCarrinho?ajax=true";				
				});
			} </script></div>
EOT;

    break;

}