<?php
namespace hardness;
header('Content-Type: application/json; charset=utf-8');
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once('../../bibliotecas/funcoesGerais.php');

function respostaRecuperacao($code, $data)
{
    echo json_encode(array('code' => $code, 'data' => $data), JSON_UNESCAPED_UNICODE);
    exit;
}

function tokenRecuperacaoSenha($C007_Id, $senhaHash, $expiraEm)
{
    $dados = (int) $C007_Id . '.' . (int) $expiraEm;
    $chave = hash('sha256', 'hardness-recuperacao-senha-v1|' . $senhaHash);
    return rtrim(strtr(base64_encode($dados . '.' . hash_hmac('sha256', $dados, $chave)), '+/', '-_'), '=');
}

function mascararEmailRecuperacao($email)
{
    $partes = explode('@', $email, 2);
    if (count($partes) !== 2) return '***';
    $inicio = substr($partes[0], 0, min(2, strlen($partes[0])));
    $dominio = explode('.', $partes[1]);
    $provedor = substr($dominio[0], 0, 1) ?: '*';
    $extensao = count($dominio) > 1 ? '.' . implode('.', array_slice($dominio, 1)) : '';
    return ($inicio ?: '*') . '****@' . $provedor . '****' . $extensao;
}

function enviarLinkRecuperacaoSenha($email, $login, $nome, $link)
{
    require_once('../../bibliotecas/phpmailer/class.phpmailer.php');
    $loginSeguro = htmlspecialchars($login, ENT_QUOTES, 'UTF-8');
    $nomeSeguro = htmlspecialchars($nome ?: 'Usuário', ENT_QUOTES, 'UTF-8');
    $linkSeguro = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
    $corpo = '<div style="font-family:Arial,sans-serif;color:#243047;max-width:600px;margin:auto;border:1px solid #dbe4ee;border-radius:10px;overflow:hidden">'
        . '<div style="padding:16px 22px;background:#1769aa;color:#fff;font-weight:bold;font-size:18px">Hardness Sistemas</div>'
        . '<div style="padding:22px"><p>Olá, <strong>' . $nomeSeguro . '</strong>.</p>'
        . '<p>Recebemos uma solicitação para redefinir a senha do login <strong>' . $loginSeguro . '</strong>.</p>'
        . '<p style="margin:24px 0"><a href="' . $linkSeguro . '" style="display:inline-block;padding:12px 18px;background:#1769aa;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold">Definir nova senha</a></p>'
        . '<p>Este link é válido por 30 minutos. Se você não solicitou a alteração, ignore este e-mail.</p></div></div>';
    try {
        $mail = new \phpmailer;
        $mail->From = 'nfe@hardness.com.br';
        $mail->FromName = 'Hardness Sistemas';
        $mail->Host = 'smtp.gmail.com';
        $mail->Mailer = 'smtp';
        $mail->SMTPAuth = true;
        $mail->Username = 'nfe@hardness.com.br';
        $mail->Password = 'hgrw nvpe xliv cluz';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->CharSet = 'utf-8';
        $mail->SetLanguage('br');
        $mail->IsHTML(true);
        $mail->Subject = '=?UTF-8?B?' . base64_encode('Hardness Sistemas - Redefinição de senha - Login ' . $login) . '?=';
        $mail->Body = $corpo;
        $mail->AltBody = 'Login: ' . $login . '. Para definir uma nova senha, acesse: ' . $link;
        $mail->AddAddress($email, $nome);
        return (bool) $mail->Send();
    } catch (\Exception $e) { return false; }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respostaRecuperacao(false, 'Requisição inválida.');
$login = trim(isset($_POST['login']) ? (string) $_POST['login'] : '');
if ($login === '') respostaRecuperacao(false, 'Informe o login.');

$loginSql = mysql_real_escape_string($login);
$sql = mysql_query("SELECT C007_Id, C007_Login, C007_Senha, C007_Email, C007_Primeiro_Nome, C007_Nome FROM C007 WHERE C007_Login = '{$loginSql}' AND C007_Ativo = 'S' LIMIT 2");
if (!$sql || mysql_num_rows($sql) !== 1) respostaRecuperacao(false, 'Login inválido. Verifique o login informado e tente novamente.');
$C007 = mysql_fetch_assoc($sql);
if (empty($C007['C007_Email']) || !filter_var($C007['C007_Email'], FILTER_VALIDATE_EMAIL)) respostaRecuperacao(true, 'Se o login estiver correto e possuir e-mail cadastrado, enviaremos as instruções.');

$expiraEm = time() + (30 * 60);
$token = tokenRecuperacaoSenha($C007['C007_Id'], $C007['C007_Senha'], $expiraEm);
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$caminho = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$link = $protocolo . $_SERVER['HTTP_HOST'] . $caminho . '/redefinir_senha.php?token=' . rawurlencode($token);
$nome = !empty($C007['C007_Primeiro_Nome']) ? $C007['C007_Primeiro_Nome'] : $C007['C007_Nome'];
if (!enviarLinkRecuperacaoSenha($C007['C007_Email'], $C007['C007_Login'], $nome, $link)) respostaRecuperacao(false, 'Não foi possível enviar o e-mail. Entre em contato com o suporte técnico.');
respostaRecuperacao(true, 'Enviamos as instruções para ' . mascararEmailRecuperacao($C007['C007_Email']) . '.');
