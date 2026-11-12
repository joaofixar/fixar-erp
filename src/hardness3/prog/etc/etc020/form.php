<?php
namespace hardness;

/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'etc020FormEnviarEmail':
        $editor = 'var' . uniqid();

        // Options: recebido via JS abrirJanelaEnviarEmail()
        $options = json_decode($r_options, true);
        if (!isset($r_emailEnviado)) {
            $r_emailDe = $options['de'];
            $r_emailPara = $options['para'];
            $r_emailAssunto = $options['assunto'];
            $r_emailMensagem = $options['corpo'];
        }
        if (!empty($options['anexos'])) {
            foreach ($options['anexos'] as $anexo) {
                if ($anexos) {
                    $anexos .= '<br style="clear: both;"/>';
                }
                $anexos .= '<span class="ui-icon ui-icon-document" style="float: left;"></span><span style="float: left; margin-top: 1px;font-size:12px"><a href="#" onclick="window.open(\'' . ($g['urlRaiz'] . ltrim($anexo['url'], '/') . '?ajax=true&pdf=true&nomeArquivo=' . urlencode($anexo['arquivo']) . $anexo['parametros']) . '\');">' . $anexo['arquivo'] .  '</a></span>';
            }
        }

        $emailDe_ = htmlentities($r_emailDe,ENT_COMPAT,'utf-8');
        $r_emailPara_ = htmlentities($r_emailPara,ENT_COMPAT,'utf-8');
        $r_emailParaCc_ = htmlentities($r_emailParaCc,ENT_COMPAT,'utf-8');
        $r_emailParaCco_ = htmlentities($r_emailParaCco,ENT_COMPAT,'utf-8');
        $r_emailAssunto_ = htmlentities($r_emailAssunto,ENT_COMPAT,'utf-8');
        $r_emailMensagem_ = htmlentities($r_emailMensagem,ENT_COMPAT,'utf-8');
        $r_options_ = htmlentities($r_options,ENT_COMPAT,'utf-8');

        echo <<<EOT
<form id="etc020EnviarEmailForm" class="formFormulario">
    <table>
        <tr>
            <td>De:</td>
            <td style="font-weight: bold;"><input id="emailDe" name="emailDe" type="text" value="{$emailDe_}" size="65" class='itemForm itemForm-De formInput ui-state-default ui-corner-all' style="text-transform: none;"></td>
        </tr>
        <tr>
            <td>Para:</td>
            <td style="font-weight: bold;"><input name="emailPara" id="emailPara" type="text" size="65" value="{$r_emailPara_}" class='itemForm formInput ui-state-default ui-corner-all' style="text-transform: none;"></td>
        </tr>
        <tr style="display: none;">
            <td>Cc:</td>
            <td style="font-weight: bold;"><input name="emailParaCc" id="emailParaCc" value="{$r_emailParaCc_}" type="text" size="65" class='itemForm formInput ui-state-default ui-corner-all' style="text-transform: none;"></td>
        </tr>
        <tr style="display: none;">
            <td>Cco:</td>
            <td style="font-weight: bold;"><input name="emailParaCco" id="emailParaCco" value="{$r_emailParaCco_}" type="text" size="65" class='itemForm formInput ui-state-default ui-corner-all' style="text-transform: none;"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><span><a href="#" id="addCc">Adicionar Cc</a>&nbsp;&nbsp;&nbsp;</span><span><a href="#" id="addCco">Adicionar Cco</a></span></td>
        </tr>
        <tr>
            <td>Assunto:</td>
            <td style="font-weight: bold;"><input id="emailAssunto" name="emailAssunto" type="text" size="90" value="{$r_emailAssunto_}" class='itemForm  itemForm-Assunto formInput ui-state-default ui-corner-all' style="text-transform: none;"></td>
        </tr>
        <tr>
            <td>Anexos:</td>
            <td>
                {$anexos}
            </td>
            <!--<td>
                <div id="file-uploader">
                    <noscript><p>Please enable JavaScript to use file uploader.</p></noscript>         
                </div>
            </td>-->
        </tr>
    </table>
    <input name="emailEnviado" type="hidden" value="true" />
    <input name="options" type="hidden" value="{$r_options_}" />
</form>
<div style="width: 100%; height: 2px; background-color: #A6C9E2;"></div>
<div id="emailEditorDiv" style="background-color: white;">
    <textarea id="{$editor}">{$r_emailMensagem_}</textarea>
</div>
EOT;

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// Corrigir Editor
				$('#{$g['divId']}').closest('.ui-dialog-content').data('callFixEditor', 'true');
				setTimeout(function() {
					$('#emailEditorDiv').find('> div:eq(1)').css('border', 'none');
					$('#emailEditorDiv').find('.nicEdit-panelContain').css({'border': 'none', 'background': 'none'});
				}, 100);
				function fixEditorSize() {
					setTimeout(function() {
						var divId = $('#{$g['divId']}');
						var editor = $('#emailEditorDiv');
						var width = divId.width() - 2;
						editor.find('> div').width(width);
						divId.css('overflow', 'hidden')
						editor.find('.nicEdit-main').width(width-8).css('overflow', 'auto');
						var height = divId.height() - divId.find('form').height() - editor.find('> div:eq(0)').height() - 3;
						editor.find('> div:eq(1)').height(height);
						editor.find('.nicEdit-main').height(height-8);
					}, 100);
				}
				$('#addCc').click(function() {
					$(this).parent().remove();
					$('#emailParaCc').closest('tr').show();
					fixEditorSize();
					return false;
				});
				$('#addCco').click(function() {
					$(this).parent().remove();
					$('#emailParaCco').closest('tr').show();
					fixEditorSize();
					return false;
				});
				$(document).ready(function() {
					new nicEditor({
						iconsPath : '/hardness3/static/img/nicEditorIcons.gif', 
						buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
					}).panelInstance('{$editor}');
					fixEditorSize();
				    setTimeout(function() {
  						$('#{$g['divId']} #emailPara').focus();
					}, 100);
				});
			} </script></div>
EOT;

        $botoes = array(
            array('titulo' => 'Enviar', 'id' => 'etc020EnviarEmailBtn', 'posicao' => 'direita'),
            array('titulo' => 'Fechar', 'id' => 'etc020EnviarEmailFecharBtn', 'posicao' => 'direita'),
        );
        echo Botoes::processar($botoes, 'form', $g['divId']);

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#etc020EnviarEmailFecharBtn').unbind('click').bind('click', function(data) {
                   fecharJanela('{$g['divId']}'); 
                });
				$('#etc020EnviarEmailBtn').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					setTimeout(function(){
						$('.loading-indicator-overlay').css({'opacity': '1', 'background': 'background: rgba(255, 255, 255, 0.5)'}).append('<div style="top: 55%; position: relative; text-align: center; color: blue;">Enviando....</div>'); // Aguarde ou feche a janela, o e-mail continuará sendo enviado
					}, 8000);
					var nicE = new nicEditors.findEditor('{$editor}');
					$.ajax({
						url: '/etc/etc020/form/etc020FormEnviarEmail/',
						dataType: 'html',
						type: 'POST',
						cache: false,
						data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&divId={$g['divId']}&' + $("#etc020EnviarEmailForm").serialize() + '&emailMensagem=' + encodeURIComponent(nicE.getContent()),
						success: function(data) {
							$('#{$g['divId']}').hideLoading();
							$('#{$g['divId']}').html(data);
							reloadUI('{$g['divId']}');
							$('#{$g['divId']}').find('> .buttonsBottom').each( function() {
								if (typeof($(this).attr('id')) != 'undefined') {
									moveBtnsToContent('{$g['divId']}');
								}
							});
							return false;
						}
					});
				});
			} </script></div>
EOT;

        if (isset($r_emailEnviado)) {
            if (empty($r_emailDe)) {
                echo gGeraAlertMsg('Campos obrigatórios', 'É necessários preencher o campo "De:"');
            } else if (empty($r_emailPara)) {
                echo gGeraAlertMsg('Campos obrigatórios', 'É necessários preencher o campo "Para:"');
            } else if (empty($r_emailAssunto)) {
                echo gGeraAlertMsg('Campos obrigatórios', 'É necessários preencher o campo "Assunto:"');
            } else if (empty($r_emailMensagem)) {
                echo gGeraAlertMsg('Campos obrigatórios', 'É necessários preencher uma mensagem no texto do e-mail.');
            } else {
                // Envio de e-mail
                require("bibliotecas/phpmailer/class.phpmailer.php");
                $mail = new \phpmailer;

                // Configurações
                $mail->CharSet = "UTF-8";
                $mail->From = $r_emailDe;
                $mail->FromName = '';
                $mail->Host = $options['smtp']['host'];
                $mail->Mailer = "smtp";
                $mail->SMTPAuth = $options['smtp']['username'] ? true : false;
                $mail->Username = $options['smtp']['username'];
                $mail->Password = $options['smtp']['password'];
                $mail->Subject = $r_emailAssunto;
                $mail->Body = $r_emailMensagem;
                $mail->AltBody = "ALT BODY";
                $mail->SetLanguage("br");
                $mail->IsHTML(true);
                $mail->Timeout = 60;
                $mail->SMTPSecure = $options['smtp']['secure'];
                $mail->Port = $options['smtp']['port'];
                $mail->SMTPDebug = true;

                // Destinatários
                $todosEmailsEnviados = array();
                $mPara = preg_split("/[;,]+/", $r_emailPara);
                for ($x = 0; $x < count($mPara); $x++) {
                    $email = $mPara[$x];
                    if (strpos($email, '<') !== false) {
                        $email = explode('<', $email);
                        $email = explode('>', $email[1]);
                        $email = $email[0];
                    }
                    $todosEmailsEnviados[] = strtolower(trim($email));
                    $mail->AddAddress($email);
                }
                $mPara = preg_split("/[;,]+/", $r_emailParaCc);
                for ($x = 0; $x < count($mPara); $x++) {
                    $email = $mPara[$x];
                    if (strpos($email, '<') !== false) {
                        $email = explode('<', $email);
                        $email = explode('>', $email[1]);
                        $email = $email[0];
                    }
                    $todosEmailsEnviados[] = strtolower(trim($email));
                    $mail->AddCC($email);
                }
                $mPara = preg_split("/[;,]+/", $r_emailParaCco);
                for ($x = 0; $x < count($mPara); $x++) {
                    $email = $mPara[$x];
                    if (strpos($email, '<') !== false) {
                        $email = explode('<', $email);
                        $email = explode('>', $email[1]);
                        $email = $email[0];
                    }
                    $todosEmailsEnviados[] = strtolower(trim($email));
                    $mail->AddBCC($email);
                }

                // Anexos
                $dir = $g['pathDados'] . 'anexosEmail/';
                is_dir($dir) or mkdir($dir, 0777, true);
                foreach ($options['anexos'] as $key => $anexo) {
                    $dirArquivoSalvo = $dir . md5(uniqid());
                    $options['anexos'][$key]['arquivoSalvo'] = $dirArquivoSalvo;
                    // &pdf=true tem significado somente para a "/tela/"
                    file_get_contents(gGerarUrlBackend($anexo['url'], "&pdf=true&salvarNoArquivo={$dirArquivoSalvo}{$anexo['parametros']}"));
                    $mail->AddAttachment($dirArquivoSalvo, $anexo['arquivo']);
                }

                // Enviar
                if ($mail->Send()) {
                    // Salvar na tabela de log
                    $sql = "INSERT INTO C041 (C041_De, C041_Para, C041_Assunto, C041_Corpo, C041_Tabela, C041_Tabela_Valor) VALUES ('".mysql_real_escape_string($r_emailDe)."', '".mysql_real_escape_string($r_emailPara_)."', '".mysql_real_escape_string($r_emailAssunto)."', '".mysql_real_escape_string($r_emailMensagem)."', '".mysql_real_escape_string($options['tabela'])."', '".mysql_real_escape_string($options['tabelaValor'])."')";
                    mysql_query($sql);
                    $C041_Id = $g['mysqlLastId'];
                    foreach ($options['anexos'] as $anexo) {
                        $sql = "INSERT INTO C042 (C042_C041_Id, C042_Url, C042_Parametros, C042_Arquivo, C042_Arquivo_Salvo) VALUES ('".mysql_real_escape_string($C041_Id)."', '".mysql_real_escape_string($anexo['url'])."', '".mysql_real_escape_string($anexo['parametros'])."', '".mysql_real_escape_string($anexo['arquivo'])."', '".mysql_real_escape_string(basename($anexo['arquivoSalvo']))."')";
                        mysql_query($sql);
                    }

                    // Retorno
                    $alert = gGeraAlertMsg('E-mail enviado com sucesso!', "De: {$r_emailDe}<br />Para: {$r_emailPara}<br />SMTP: {$options['smtp']['host']}", false);
                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                setTimeout(function() {
                                    {$alert}
                                }, 150);
                            });
                            $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                            var divIdParent = '{$g['divIdParent']}';
                            if (!isempty(divIdParent)) {
                                divRefresh('{$g['divIdParent']}', true);
                            }
                        } </script></div>
    EOT;
                } else {
                    foreach ($options['anexos'] as $anexo) {
                        if (file_exists($anexo['arquivoSalvo'])) {
                            unlink($anexo['arquivoSalvo']);
                        }
                    }
                    $cErro = str_replace(array("'", '"', "\r", "\n"), "", $mail->ErrorInfo);
                    $cErro = str_replace(array("<p>", "</p>"), "<br /><br />", $cErro);
                    echo gGeraAlertMsg('Não foi possível enviar o e-mail.', "De: {$r_emailDe}<br />Para: {$r_emailPara}<br />SMTP: {$options['smtp']['host']}<br />SMTP Secure: {$mail->SMTPSecure}<br />PORT: {$mail->Port}<br />ERRO:<br />{$cErro}");
                }
            }
        }
	break;
}