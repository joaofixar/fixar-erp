<?php
namespace hardness;

require_once("old/lib/funcoes.php");
require_once("bibliotecas/segurancaSenha.php");

function autorizaValidarUsuarioSenha($login, $senha)
{
    $login = mysql_real_escape_string((string) $login);
    $C007 = mysql_query("SELECT C007_Id, C007_Flag_Vendedor, C007_Senha FROM C007 WHERE C007_Login='{$login}' AND C007_Ativo='S' LIMIT 1");
    $usuario = $C007 ? mysql_fetch_assoc($C007) : null;
    return $usuario && SegurancaSenha::validarSenhaArmazenada((string) $senha, (string) $usuario['C007_Senha']) ? $usuario : false;
}

/**
* Sistema genérico de autorizações, recebe Tabela, Id, Usuário autorizador, Descrição e Valor autorizado
*/
switch($g['r'][3]){

	case 'validaAutorizacao':
        $resposta = array('code' => true, 'data' => array());

        $C035 = mysql_query("select C035_Valor from C035 where C035_Tabela='$r_tabela' and C035_Tabela_Id='$r_tabelaId' order by C035_Id desc");
        $mC035 = mysql_fetch_assoc($C035);
        if ($r_valor <= $mC035['C035_Valor']) {
            $resposta['code'] = true;
        } else {
            $resposta['code'] = false;
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

	break;

	case 'content':
        /*
        * Divisão da Area em Conteúdos
        */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/sistema/funcoes/autoriza/tab/', '&tabela=' . urlencode($r_tabela) . '&tabelaId=' . urlencode($r_tabelaId) . '&valor=' . urlencode($r_valor) . '&descricao=' . urlencode($r_descricao)),
        );
        echo gProcessaAcoes($actions);
    
	break;
	
	case 'tab':
   	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/sistema/funcoes/autoriza/form/', 'Autorização', '&tab=geral&tabela=' . urlencode($r_tabela) . '&tabelaId=' . urlencode($r_tabelaId) . '&valor=' . urlencode($r_valor) . '&descricao=' . urlencode($r_descricao));
	
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'form':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'C035';
		$form_campo_pk = 'C035_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'autorizaFormBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/autoriza/form/';
		
 		//form editor
		$form_editor = array(
				'geral' => '{"C035_Login":{"top":7,"left":79.5,"width":184,"hidden":0},"C035_Senha":{"top":30,"left":76.140625,"width":184,"hidden":0},"C035_Descricao":{"top":53,"left":57.71875,"width":184,"hidden":0},"C035_Valor":{"top":76,"left":54.3125,"width":184,"hidden":0},"C035_Data_Autorizacao":{"top":99,"left":23.453125,"width":184,"hidden":0},"alcada":{"top":122,"left":72.828125,"width":184,"hidden":0},"C035_Tabela":{"top":145,"left":74.015625,"width":137,"hidden":0},"C035_Tabela_Id":{"top":168,"left":97.40625,"width":137,"hidden":0},"C035_C007_Id":{"top":191,"left":66.75,"width":137,"hidden":0}}',
		);
		
		$agora = agora();

 		$percentualDesconto = gCorrigeNumeroInverte($r_valor);
		if($percentualDesconto <= $g['C031']['alcadaVendaSupervisor_descontoMaximoSupervisorAVista']){
			$alcada      = 'S';
			$textoAlcada = 'Supervisor';
		} else{
			$alcada      = 'D';
			$textoAlcada = 'Diretor';
		}
 
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'C035_Login' => array('name' => 'C035_Login', 'label' => 'Login', 'type' => 'texto', 'reject' => true, 'uppercase' => false),
					'C035_Senha' => array('name' => 'C035_Denha', 'label' => 'Senha', 'type' => 'password', 'reject' => true, 'uppercase' => false),
					'C035_Descricao' => array('name' => 'C035_Descricao', 'label' => 'Descrição', 'type' => 'texto', 'value' => "{$r_descricao}", 'uppercase' => false, 'readonly' => true),
					'C035_Valor' => array('name' => 'C035_Valor', 'label' => 'Percentual', 'type' => 'moeda', 'value' => "{$r_valor}", 'casasDecimais' => '2', 'readonly' => true),
					'C035_Data_Autorizacao' => array('name' => 'C035_Data_Autorizacao', 'label' => 'Data autorização', 'type' => 'texto', 'value' => "{$agora}", 'readonly' => true),
					'C035_Tabela' => array('name' => 'C035_Tabela', 'label' => 'Tabela', 'type' => 'texto', 'value' => "{$r_tabela}", 'readonly' => true, 'type' => 'hidden'),
					'C035_Tabela_Id' => array('name' => 'C035_Tabela_Id', 'label' => 'Id', 'type' => 'numero', 'value' => "{$r_tabelaId}", 'readonly' => true, 'type' => 'hidden'),
					'C035_C007_Id' => array('name' => 'C035_C007_Id', 'label' => 'C007_Id', 'type' => 'numero', 'value' => "", 'readonly' => true, 'type' => 'hidden'),
					'alcada' => array('name' => 'alcada', 'label' => 'Alçada', 'value' => "{$textoAlcada}",'readonly' => true, 'reject' => true),
				)
			)
		);
		$form = new Formulario('form-autorizaForm');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
                $login = $form->campoValorEnviado('C035_Login');
                $senha = $form->campoValorEnviado('C035_Senha');
                $mC007 = autorizaValidarUsuarioSenha($login, $senha);

                if (empty($mC007['C007_Id'])){
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            dialogConfirm("Erro", "Login ou senha inválidos");
						} </script></div>
EOT;
				} else if ($mC007['C007_Flag_Vendedor'] == 'V' || $mC007['C007_Flag_Vendedor'] == 'C' || $mC007['C007_Flag_Vendedor'] == 'N'){
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							dialogConfirm("Erro", "Login não tem alçada para esta autorização");
						} </script></div>
EOT;
                } else if ($mC007['C007_Flag_Vendedor'] == 'S' && $alcada == 'D'){
                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            dialogConfirm("Erro", "Login não tem alçada para esta autorização");
                        } </script></div>
EOT;
                } else {
                    // Registra na tabela C035 Autorizações a autorização referente ao desconto
                    $form->campoSetarValor('C035_Data_Autorizacao', gCorrigeDataInverte($form->campoValorEnviado('C035_Data_Autorizacao')), true);
                    $form->campoSetarValor('C035_C007_Id', $mC007['C007_Id'], true);
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            fecharJanela('{$g['divId']}');
                            dialogConfirm("Autorizado", "Autorizado com sucesso.");
                        } </script></div>
EOT;
                    }
                }
			}
		} 

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="autorizaFormBtn">Autorizar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'contentAbrirGaveta':
        /*
        * Divisão da Area em Conteúdos
        */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/sistema/funcoes/autoriza/tabAbrirGaveta/', '&divForm='.$r_divForm),
        );
        echo gProcessaAcoes($actions);
    
	break;
	
	case 'tabAbrirGaveta':
   	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/sistema/funcoes/autoriza/formAbrirGaveta/', 'Autorização abrir gaveta', '&tab=geral');
	
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'formAbrirGaveta':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'formAbrirGavetaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/autoriza/formAbrirGaveta/';
		
 		//form editor
		$form_editor = array(
				'geral' => '{"login":{"top":7,"left":31.5,"width":137,"hidden":0},"senha":{"top":30,"left":28.140625,"width":137,"hidden":0},"descricao":{"top":53,"left":9.71875,"width":137,"hidden":0}}',
		);

		$agora = agora();
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'login' => array('name' => 'login', 'label' => 'Login', 'type' => 'texto',  'uppercase' => false,'reject' => false),
					'senha' => array('name' => 'senha', 'label' => 'Senha', 'type' => 'password', 'uppercase' => false,'reject' => false),
					'descricao' => array('name' => 'descricao', 'label' => 'Descrição', 'value' => 'Abertura de gaveta', 'uppercase' => false,'reject' => true, 'readonly' => true),
				)
			)
		);
		$form = new Formulario('form-autorizaForm');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
                $login = $form->campoValorEnviado('login');
                $senha = $form->campoValorEnviado('senha');
                $mC007 = autorizaValidarUsuarioSenha($login, $senha);

                if (empty($mC007['C007_Id'])){
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            dialogConfirm("Erro", "Login ou senha inválido");
						} </script></div>
EOT;
				} else if ($mC007['C007_Flag_Vendedor'] == 'V' || $mC007['C007_Flag_Vendedor'] == 'C' || $mC007['C007_Flag_Vendedor'] == 'N'){
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							dialogConfirm("Erro", "Login não tem permissão para abrir gaveta");
						} </script></div>
EOT;
                } else {
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							// Exemplo: abrirJanela
							$('#{$g['divId']}').showLoading();
							$.ajax({
								url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
								type: 'GET',
								dataType: 'jsonp',
								data: {
									'ajax': 'true',
									'acaoId': 'da681c8d16233fa8bdc9a645a506841b',
									'link': '/sistema/personalizacao/tela/da681c8d16233fa8bdc9a645a506841b/?ajax=true&C007_Id={$mC007['C007_Id']}',
								},
								success: function(resposta) {
									$('#{$g['divId']}').hideLoading();
									window.open(resposta.data, '_blank');
									fecharJanela('{$g['divId']}');
								}
							});
						} </script></div>
EOT;               
                }
			}
		} 

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="formAbrirGavetaBtn">Autorizar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}

function validaAutorizacao($tabela, $tabelaId, $valor) {
 
    $C035 = mysql_query("SELECT C035_Valor FROM C035 WHERE C035_Tabela='{$tabela}' AND C035_Tabela_Id='{$tabelaId}' ORDER BY C035_Id DESC LIMIT 1");
    $mC035 = mysql_fetch_assoc($C035);
    if (number_format($valor,2) <= number_format($mC035['C035_Valor'],2)) {
        $retorno = true;
    } else {
        $retorno = false;
    }

    return $retorno;
 
}


?>
