<?php
namespace hardness;
header('X-Robots-Tag: noindex, nofollow', true);
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once('../../bibliotecas/funcoesGerais.php');
require_once('../../bibliotecas/segurancaSenha.php');

function lerTokenRedefinicao($token)
{
    $token = strtr($token, '-_', '+/');
    $token .= str_repeat('=', (4 - strlen($token) % 4) % 4);
    $dados = base64_decode($token, true);
    if ($dados === false) return false;
    $partes = explode('.', $dados);
    if (count($partes) !== 3 || !ctype_digit($partes[0]) || !ctype_digit($partes[1]) || !preg_match('/^[a-f0-9]{64}$/', $partes[2])) return false;
    return array('id' => (int) $partes[0], 'expiraEm' => (int) $partes[1], 'assinatura' => $partes[2], 'dados' => $partes[0] . '.' . $partes[1]);
}

function usuarioTokenRedefinicao($token)
{
    $token = lerTokenRedefinicao($token);
    if (!$token || $token['expiraEm'] < time()) return false;
    $sql = mysql_query("SELECT C007_Id, C007_Login, C007_Senha, C007_Senha_Anterior_1, C007_Senha_Anterior_2, C007_Senha_Anterior_3, C007_Email, C007_Primeiro_Nome, C007_Nome FROM C007 WHERE C007_Id = '{$token['id']}' AND C007_Ativo = 'S' LIMIT 1");
    $C007 = mysql_fetch_assoc($sql);
    if (!$C007) return false;
    $chave = hash('sha256', 'hardness-recuperacao-senha-v1|' . $C007['C007_Senha']);
    if (!hash_equals(hash_hmac('sha256', $token['dados'], $chave), $token['assinatura'])) return false;
    return $C007;
}

function enviarConfirmacaoRedefinicao($email, $nome, $login, $senha)
{
    require_once('../../bibliotecas/phpmailer/class.phpmailer.php');
    $nome = htmlspecialchars($nome ?: 'Usuário', ENT_QUOTES, 'UTF-8');
    $login = htmlspecialchars($login, ENT_QUOTES, 'UTF-8');
    $senhaHtml = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');
    try {
        $mail = new \phpmailer;
        $mail->From = 'nfe@hardness.com.br'; $mail->FromName = 'Hardness Sistemas';
        $mail->Host = 'smtp.gmail.com'; $mail->Mailer = 'smtp'; $mail->SMTPAuth = true;
        $mail->Username = 'nfe@hardness.com.br'; $mail->Password = 'hgrw nvpe xliv cluz';
        $mail->Port = 465; $mail->SMTPSecure = 'ssl'; $mail->CharSet = 'utf-8'; $mail->SetLanguage('br'); $mail->IsHTML(true);
        $mail->Subject = '=?UTF-8?B?' . base64_encode('Hardness Sistemas - Senha alterada com sucesso - Login ' . html_entity_decode($login, ENT_QUOTES, 'UTF-8')) . '?=';
        $mail->Body = '<div style="font-family:Arial,sans-serif;color:#243047;max-width:600px;margin:auto;border:1px solid #dbe4ee;border-radius:10px;overflow:hidden"><div style="padding:16px 22px;background:#1769aa;color:#fff;font-weight:bold;font-size:18px">Hardness Sistemas</div><div style="padding:22px"><p>Olá, <strong>' . $nome . '</strong>.</p><p>Sua senha foi alterada com sucesso.</p><p style="padding:14px;background:#eef6ff;border-radius:6px"><strong>Login:</strong> ' . $login . '<br><strong>Nova senha:</strong> ' . $senhaHtml . '</p><p>Por segurança, não compartilhe esta mensagem.</p></div></div>';
        $mail->AltBody = 'Senha alterada com sucesso. Login: ' . $login . '. Nova senha: ' . $senha;
        $mail->AddAddress($email, html_entity_decode($nome, ENT_QUOTES, 'UTF-8'));
        return (bool) $mail->Send();
    } catch (\Exception $e) { return false; }
}

$token = isset($_REQUEST['token']) ? (string) $_REQUEST['token'] : '';
$C007 = usuarioTokenRedefinicao($token);
$erro = ''; $sucesso = '';
if (!$C007) $erro = 'Este link é inválido ou expirou. Solicite uma nova recuperação de senha.';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $C007) {
    $nova = isset($_POST['novaSenha']) ? (string) $_POST['novaSenha'] : '';
    $confirmacao = isset($_POST['confirmacaoNovaSenha']) ? (string) $_POST['confirmacaoNovaSenha'] : '';
    $erros = array();
    if ($nova !== $confirmacao) $erros[] = 'A confirmação da nova senha não confere.';
    $erros = array_merge($erros, SegurancaSenha::validarNovaSenha($nova));
    if (SegurancaSenha::senhaReutilizada($nova, array($C007['C007_Senha'], $C007['C007_Senha_Anterior_1'], $C007['C007_Senha_Anterior_2'], $C007['C007_Senha_Anterior_3']))) $erros[] = 'A nova senha não pode ser igual à atual ou a uma das três senhas anteriores.';
    if ($erros) $erro = implode(' ', $erros);
    else {
        $v = SegurancaSenha::prepararAtualizacaoSenha($nova, $C007);
        $set = array(); foreach (array('C007_Senha','C007_Senha_Anterior_1','C007_Senha_Anterior_2','C007_Senha_Anterior_3') as $campo) $set[] = $campo . ' = ' . ($v[$campo] === null ? 'NULL' : "'" . mysql_real_escape_string($v[$campo]) . "'");
        $set[] = "C007_Data_Alteracao_Senha = '" . mysql_real_escape_string($v['C007_Data_Alteracao_Senha']) . "'";
        // A página pública não inicializa o contexto do ERP; grava diretamente na conexão.
        if (\mysqli_query($con, 'UPDATE C007 SET ' . implode(', ', $set) . " WHERE C007_Id = '" . (int) $C007['C007_Id'] . "'")) {
            $nome = !empty($C007['C007_Primeiro_Nome']) ? $C007['C007_Primeiro_Nome'] : $C007['C007_Nome'];
            $emailEnviado = enviarConfirmacaoRedefinicao($C007['C007_Email'], $nome, $C007['C007_Login'], $nova);
            $sucesso = 'Senha alterada com sucesso.' . ($emailEnviado ? ' Enviamos a confirmação para o e-mail cadastrado.' : ' Não foi possível enviar o e-mail de confirmação.');
        } else $erro = 'Não foi possível gravar a nova senha. Tente novamente.';
    }
}
if ($sucesso) echo '<script>window.setTimeout(function(){ window.location.replace("index.php"); }, 1800);</script>';
?><style>.ver-senha svg{display:block;width:18px;height:18px}.ver-senha{color:#1769aa!important}</style><script>document.addEventListener('DOMContentLoaded',function(){var aberto='<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="2.7" fill="currentColor"/></svg>',fechado='<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2"/><path d="M4 4l16 16" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>';document.querySelectorAll('.ver-senha').forEach(function(botao){botao.innerHTML=aberto;botao.addEventListener('click',function(){this.innerHTML=this.parentNode.querySelector('input').type==='text'?fechado:aberto;});});});</script><!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Definir nova senha</title><style>body{margin:0;background:#eef3f8;font:14px Arial,sans-serif;color:#243047}.caixa{width:calc(100% - 32px);max-width:460px;margin:8vh auto;background:#f8fafc;border-radius:18px;overflow:hidden;box-shadow:0 24px 70px rgba(15,23,42,.28)}header{padding:18px 24px;background:linear-gradient(125deg,#0f3d75,#1769aa);color:#fff;font-weight:bold}main{padding:28px 26px}h1{margin:0 0 10px;font-size:23px;color:#102a43}.regras{padding:12px 14px;background:#eef6ff;border-radius:8px;line-height:1.55}label{display:block;margin:17px 0 5px;font-weight:bold}input{box-sizing:border-box;width:100%;padding:11px;border:1px solid #b9c6d5;border-radius:6px;font-size:15px}.campo-senha{position:relative}.campo-senha input{padding-right:52px}.ver-senha{position:absolute;right:6px;top:6px;margin:0;padding:5px 8px;background:transparent;color:#1769aa;border:0;box-shadow:none;font-size:18px;line-height:1;cursor:pointer}.erro,.sucesso{padding:12px;margin:16px 0;border-radius:7px;line-height:1.45}.erro{color:#b42318;background:#fff1f2;border:1px solid #fecdd3}.sucesso{color:#067647;background:#ecfdf3;border-left:5px solid #12b76a}button{float:right;margin-top:22px;padding:11px 18px;border:0;border-radius:7px;background:#1463a5;color:#fff;font-weight:bold;cursor:pointer}.limpar{clear:both}</style></head><body><section class="caixa"><header>Segurança da senha</header><main><?php if ($sucesso): ?><h1>Senha alterada</h1><div class="sucesso"><?php echo htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?></div><?php elseif ($erro): ?><h1>Definir nova senha</h1><div class="erro"><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div><?php if ($C007): ?><p class="regras">Use de 8 a 20 caracteres, incluindo ao menos uma letra maiúscula, uma minúscula e um número. Não use a senha atual nem as três anteriores.</p><form method="post"><input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>"><label>Nova senha</label><div class="campo-senha"><input type="password" name="novaSenha" autocomplete="new-password" required><button class="ver-senha" type="button" aria-label="Mostrar senha">◉</button></div><label>Confirme a nova senha</label><div class="campo-senha"><input type="password" name="confirmacaoNovaSenha" autocomplete="new-password" required><button class="ver-senha" type="button" aria-label="Mostrar senha">◉</button></div><button type="submit">Salvar nova senha</button><div class="limpar"></div></form><?php endif; ?><?php else: ?><h1>Definir nova senha</h1><p>Escolha uma nova senha para continuar.</p><p class="regras">Use de 8 a 20 caracteres, incluindo ao menos uma letra maiúscula, uma minúscula e um número. Não use a senha atual nem as três anteriores.</p><form method="post"><input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>"><label>Nova senha</label><div class="campo-senha"><input type="password" name="novaSenha" autocomplete="new-password" required><button class="ver-senha" type="button" aria-label="Mostrar senha">◉</button></div><label>Confirme a nova senha</label><div class="campo-senha"><input type="password" name="confirmacaoNovaSenha" autocomplete="new-password" required><button class="ver-senha" type="button" aria-label="Mostrar senha">◉</button></div><button type="submit">Salvar nova senha</button><div class="limpar"></div></form><?php endif; ?></main></section><script>document.querySelectorAll('.ver-senha').forEach(function(botao){botao.addEventListener('click',function(){var campo=this.parentNode.querySelector('input');var mostrar=campo.type==='password';campo.type=mostrar?'text':'password';this.textContent=mostrar?'◉':'⊘';this.setAttribute('aria-label',mostrar?'Ocultar senha':'Mostrar senha');});});</script></body></html>
