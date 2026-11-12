<?php
namespace hardness;
require_once('prog/etc/etc001/grid_func-callback.php');
require_once('prog/etc/etc001/grid_func-js.php');

switch($g['r'][3]) {
    case 'builder':
        // Recupera grid construido com o Fast Crud
        $projeto = json_decode(file_get_contents("{$g['pathDados']}/builder/{$r_builderProjeto}.json"), true);
        $data = $projeto['forms'][$r_builderForm];

        global $g;

        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($r_tab)) ? $r_tab : false;
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

        // A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
        $form_tabela = $data["tabela"];
        $form_campo_pk = "{$data["tabela"]}_Id";
        // ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = "{$data["id"]}BtnInsert";
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/sistema/builder/form/builder/';

        // Definição do Formulário
        $form = new Formulario("Form{$data["id"]}");
        $formArray = [
            "campos" => [
                "cadastro" => []
            ]
        ];

        foreach ($data["form"] as $campo) {
            $campoForm = [
                "name" => $campo['campo'],
                "label" => $campo["titulo"],
                "type" => "text"
            ];
            switch ($campo['tipo']) {
                case "Select":
                    $campoForm["name"] = "{$data["tabela"]}_{$campo["tabela"]}_Id";
                    $campoForm["type"] = 'select';
                    $campoForm["select"] = gGeraSelect($campo["tabela"], "{$campo["tabela"]}_Id", implode(",", $campo["campo"]), '', true);
                    break;
                case "Moeda":
                    $campoForm["type"] = 'moeda';
                    break;
                case "Data":
                    $campoForm["type"] = 'data';
                    break;
            }
            foreach ($campo["opcoes"] as $opcao) {
                if ($opcao["nome"] == "casasDecimais") {
                    $campoForm["casasDecimais"] = $opcao["valor"];
                }
            }
            $formArray["campos"]["cadastro"][$campoForm["name"]] = $campoForm;
        }
//        print_r($formArray);
//        die();
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
                // Insert ou Update?
                if (!empty($r_acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
                        // Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
                        //$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        // Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
                        $alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {	
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($r_acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
                // Mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
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
        $botoesDir .= '<button type="submit" id="' . $enviar_btnId . '">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
}
