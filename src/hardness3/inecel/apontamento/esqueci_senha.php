<?php
namespace hardness;

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');

$eMail = isset($_REQUEST['eMail']) ? urldecode($_REQUEST['eMail']) : '';
$resposta = array('code' => true, 'data' => 'E-Mail enviado com sucesso, verifique sua caixa de entrada.');

function responder($payload)
{
    if (!empty($_REQUEST['callback'])) {
        header('Content-Type: application/javascript; charset=utf-8');
        die($_REQUEST['callback'] . "(" . json_encode($payload) . ");");
    }

    header('Content-Type: application/json; charset=utf-8');
    die(json_encode($payload));
}

if (empty($eMail)) {
    $resposta['code'] = false;
    $resposta['data'] = 'Por favor informe o e-mail';
    responder($resposta);
}

$C007 = mysql_query("SELECT C007_Login, C007_Senha FROM C007 WHERE C007_Email = '{$eMail}'");
if (mysql_num_rows($C007) == 0) {
    $resposta['code'] = false;
    $resposta['data'] = 'E-Mail nao encontrado';
    responder($resposta);
}

if (empty($sys['email'])) {
    $resposta['code'] = false;
    $resposta['data'] = 'Erro. Entre em contato com o suporte tecnico.';
    responder($resposta);
}

$mC007 = mysql_fetch_array($C007);
$mC007['C007_Senha'] = base64_decode($mC007['C007_Senha']);

require_once("../../bibliotecas/phpmailer/class.phpmailer.php");

$emailConteudo = <<<EOT
    <div style="width: 600px; color:black; font-family: Lato,Helvetica,Arial; color: #444; font-size: 14px; margin-left: auto; margin-right: auto; border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 50px 30px #0000002d; margin-bottom: 100px;">
        <div style="padding: 7px; background-color: #0064de; background-image: linear-gradient(to left, #0064de, #0d4c99); color: white;">
            <img src="{$sys['urlEmpresa']}{$sys['logoMenu']}"><br />
        </div>
        <div style="padding: 12px; background: #f0f1f3;">
            <div style="font-size: 18px; font-weight: bold; margin-top: 10px;">Recuperacao de senha</div>
            <p>Dados de acesso ao sistema:<br>
                Login: <b>{$mC007['C007_Login']}</b><br>
                Senha: <b>{$mC007['C007_Senha']}</b>
            </p>
            <div style="text-align: justify; margin-top: 15px;">Caso necessite de auxilio com a autenticacao no sistema entre em contato conosco pelo telefone {$sys['telefone']}, estamos a sua disposicao.</div>
            <p><div style="text-align: center; font-size: 11px; margin-bottom: -15px;">&copy; Hardness Sistemas. Todos os direitos reservados.</div></p>
        </div>
    </div>
EOT;

$mail = new \phpmailer;
$mail->From = "Hardness Sistemas";
$mail->FromName = "Hardness Sistemas - Recuperacao de senha";
$mail->Host = "smtp.gmail.com";
$mail->Mailer = "smtp";
$mail->SMTPAuth = true;
$mail->Username = "nfe@hardness.com.br";
$mail->Password = "hgrw nvpe xliv cluz";
$mail->Subject = "Recuperacao de senha";
$mail->Body = $emailConteudo;
$mail->AltBody = "ALT BODY";
$mail->CharSet = 'utf-8';
$mail->SetLanguage("br");
$mail->IsHTML(false);
$mail->Port = "465";
$mail->SMTPSecure = "ssl";

$mPara = preg_split("/[;,]+/", $eMail);
for ($x = 0; $x < count($mPara); $x++) {
    $email = $mPara[$x];
    if (strpos($email, '<') !== false) {
        $email = explode('<', $email);
        $email = explode('>', $email[1]);
        $email = $email[0];
    }
    $mail->AddAddress($email);
}

if (!$mail->Send()) {
    $resposta['code'] = false;
    $resposta['data'] = 'Erro ao enviar o email com a recuperacao da senha, favor entrar em contato com o suporte tecnico.';
    responder($resposta);
}

responder($resposta);
