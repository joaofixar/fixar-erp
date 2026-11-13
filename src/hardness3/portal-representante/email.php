<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

function portalRepresentanteEmailConfiguracao($conf, $usuario)
{
    if ((int) $usuario < 1) throw new RepresentantePortalErro('O remetente do portal precisa ser definido pela empresa.', 503);
    $opcoes = portalRepresentanteConfig($conf);
    // Somente código de configuração do servidor pode substituir o transporte.
    if (isset($opcoes['emailTransporte']) && is_callable($opcoes['emailTransporte'])) return array('transporte' => $opcoes['emailTransporte'], 'usuario' => (int) $usuario);
    $conexao = portalRepresentanteConectar($conf);
    try { $cadastro = portalRepresentanteLerRemetente($conexao, (int) $usuario); }
    finally { portalRepresentanteFecharConexao($conexao); }
    return array('smtp' => portalRepresentanteEmailValidarRemetente($cadastro));
}

function portalRepresentanteEmailValidarRemetente($cadastro)
{
    $erro = 'Configure o e-mail de envio em Cadastros > Usuários para o usuário que disponibiliza o link.';
    if (!is_array($cadastro) || empty($cadastro['C007_Email_SMTP_Servidor'])) throw new RepresentantePortalErro($erro, 503);
    $de = portalRepresentanteEmailValido(isset($cadastro['C007_Email']) ? $cadastro['C007_Email'] : '');
    if ($de === '') $de = portalRepresentanteEmailValido(isset($cadastro['C007_Email_SMTP_Usuario']) ? $cadastro['C007_Email_SMTP_Usuario'] : '');
    // Campo texto legado: o envio de pedidos/orçamentos passa o valor do
    // cadastro diretamente ao PHPMailer. Não reinterpretar apenas no portal.
    $autenticacao = isset($cadastro['C007_Email_SMTP_Autenticacao']) ? $cadastro['C007_Email_SMTP_Autenticacao'] : false;
    $porta = !empty($cadastro['C007_Email_SMTP_Porta']) ? (int) $cadastro['C007_Email_SMTP_Porta'] : 25;
    if ($de === '' || $porta < 1 || $porta > 65535 || ($autenticacao && (empty($cadastro['C007_Email_SMTP_Usuario']) || empty($cadastro['C007_Email_SMTP_Senha'])))) throw new RepresentantePortalErro($erro, 503);
    $cadastro['C007_Email'] = $de;
    $cadastro['C007_Email_SMTP_Autenticacao'] = $autenticacao;
    $cadastro['C007_Email_SMTP_Porta'] = $porta;
    return $cadastro;
}

function portalRepresentanteEmailPrepararSmtp($cadastro)
{
    require_once dirname(__DIR__) . '/bibliotecas/phpmailer/class.phpmailer.php';
    $mail = new \PHPMailer;
    $mail->IsSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;
    $mail->Timeout = 60;
    $mail->Host = trim($cadastro['C007_Email_SMTP_Servidor']);
    $mail->Port = $cadastro['C007_Email_SMTP_Porta'];
    $mail->SMTPAuth = $cadastro['C007_Email_SMTP_Autenticacao'];
    $mail->Username = isset($cadastro['C007_Email_SMTP_Usuario']) ? $cadastro['C007_Email_SMTP_Usuario'] : '';
    $mail->Password = isset($cadastro['C007_Email_SMTP_Senha']) ? $cadastro['C007_Email_SMTP_Senha'] : '';
    $seguranca = isset($cadastro['C007_Email_SMTP_Seguranca']) ? strtolower(trim((string) $cadastro['C007_Email_SMTP_Seguranca'])) : '';
    if (in_array($seguranca, array('1', 'ssl'), true)) $mail->SMTPSecure = 'ssl';
    elseif (in_array($seguranca, array('2', 'tls'), true)) $mail->SMTPSecure = 'tls';
    elseif ($mail->Port === 465) $mail->SMTPSecure = 'ssl';
    elseif ($mail->Port === 587) $mail->SMTPSecure = 'tls';
    $mail->From = $cadastro['C007_Email'];
    $mail->FromName = !empty($cadastro['C007_Primeiro_Nome']) ? $cadastro['C007_Primeiro_Nome'] : 'Hardness';
    // Evita a expressão /e do PHPMailer legado ao codificar nomes acentuados.
    if (preg_match('/[\x80-\xFF]/', $mail->FromName)) $mail->FromName = '=?UTF-8?B?' . base64_encode($mail->FromName) . '?=';
    return $mail;
}

function portalRepresentanteEmailMensagemFalha($diagnostico)
{
    // Só devolve mensagens fixas. A resposta SMTP pode conter dados de acesso
    // ou o conteúdo do convite e não deve aparecer na tela nem nos logs.
    $mensagem = 'Não foi possível enviar o e-mail. ';
    if (preg_match('/authenticate|authentication|auth[^\r\n]*(?:failed|not accepted)|\b53[45]\b/i', $diagnostico))
        return $mensagem . 'O servidor recusou a autenticação do usuário que está enviando o link.';
    if (preg_match('/STARTTLS|TLS|SSL|certificate|crypto/i', $diagnostico))
        return $mensagem . 'Não foi possível estabelecer a conexão segura com o servidor de e-mail do usuário.';
    if (preg_match('/connect|timed?\s*out|timeout/i', $diagnostico))
        return $mensagem . 'Não foi possível conectar ao servidor de e-mail do usuário dentro do tempo limite.';
    if (preg_match('/from address failed|sender|MAIL FROM/i', $diagnostico))
        return $mensagem . 'O servidor recusou o endereço de e-mail do remetente.';
    if (preg_match('/recipients? failed|RCPT TO/i', $diagnostico))
        return $mensagem . 'O servidor recusou o endereço de e-mail do representante.';
    return $mensagem . 'Verifique a configuração de envio do usuário em Cadastros > Usuários e tente novamente.';
}

function portalRepresentanteEmail($remetente, $destino, $assunto, $texto)
{
    $destino = portalRepresentanteEmailValido($destino);
    if ($destino === '') throw new RepresentantePortalErro('O e-mail cadastrado para o representante é inválido.');
    try {
        if (isset($remetente['transporte'])) {
            $enviado = $remetente['transporte']($remetente['usuario'], $destino, $assunto, $texto);
        } else {
            $mail = portalRepresentanteEmailPrepararSmtp($remetente['smtp']);
            $mail->IsHTML(false);
            $mail->Subject = '=?UTF-8?B?' . base64_encode($assunto) . '?=';
            $mail->Body = $texto;
            $mail->AddAddress($destino);
            // O código não recebe cópias, anexos ou registro do corpo em logs.
            $enviado = $mail->Send();
        }
        if ($enviado !== true) throw new \RuntimeException(isset($mail) ? $mail->ErrorInfo : 'Envio recusado');
    } catch (\Throwable $erro) {
        throw new RepresentantePortalErro(portalRepresentanteEmailMensagemFalha($erro->getMessage()), 503);
    }
}
