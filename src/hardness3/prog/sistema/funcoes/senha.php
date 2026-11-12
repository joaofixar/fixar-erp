<?php
namespace hardness;
require_once('bibliotecas/segurancaSenha.php');

function senhaPoliticaConfigurada()
{
    global $g;
    return array(
        'dataMarco' => $g['conf']->ini('senhaDataMarcoExpiracao'),
        'prazoVencimentoDias' => (int) $g['conf']->ini('senhaPrazoVencimentoDias'),
        'prazoInicialMarcoDias' => (int) $g['conf']->ini('senhaPrazoInicialMarcoDias'),
        'diasAvisoVencimento' => (int) $g['conf']->ini('senhaDiasAvisoVencimento')
    );
}

function enviarEmailSenhaAlterada($email, $nome, $login, $senha)
{
    require_once('bibliotecas/phpmailer/class.phpmailer.php');
    $nomeSeguro = htmlspecialchars($nome ?: 'Usuário', ENT_QUOTES, 'UTF-8');
    $loginSeguro = htmlspecialchars($login, ENT_QUOTES, 'UTF-8');
    $senhaSegura = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');
    $corpo = '<div style="font-family:Arial,sans-serif;color:#243047;max-width:600px;margin:auto;border:1px solid #dbe4ee;border-radius:10px;overflow:hidden">'
        . '<div style="padding:16px 22px;background:#1769aa;color:#fff;font-weight:bold;font-size:18px">Hardness Sistemas</div>'
        . '<div style="padding:22px"><p>Olá, <strong>' . $nomeSeguro . '</strong>.</p>'
        . '<p>Sua senha foi alterada com sucesso.</p>'
        . '<p style="padding:14px;background:#eef6ff;border-radius:6px"><strong>Login:</strong> ' . $loginSeguro . '<br><strong>Nova senha:</strong> ' . $senhaSegura . '</p>'
        . '<p>Por segurança, não compartilhe esta mensagem.</p></div></div>';
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
        $mail->Subject = '=?UTF-8?B?' . base64_encode('Hardness Sistemas - Senha alterada com sucesso - Login ' . $login) . '?=';
        $mail->Body = $corpo;
        $mail->AltBody = 'Sua senha foi alterada com sucesso. Login: ' . $login . '. Nova senha: ' . $senha;
        $mail->AddAddress($email, $nome);
        return (bool) $mail->Send();
    } catch (\Exception $e) {
        return false;
    }
}

function senhaAlterarUsuario($C007_Id, $nova, $confirmacao, $novoEmail)
{
    $C007_Id = (int) $C007_Id;
    $sql = mysql_query("SELECT C007_Senha, C007_Senha_Anterior_1, C007_Senha_Anterior_2, C007_Senha_Anterior_3, C007_Email, C007_Login, C007_Primeiro_Nome, C007_Nome FROM C007 WHERE C007_Id = '{$C007_Id}' AND C007_Ativo = 'S' LIMIT 1");
    $C007 = mysql_fetch_assoc($sql);
    $erros = array();
    if (!$C007) {
        $erros[] = 'Usuário não encontrado ou inativo.';
    } else {
        // O e-mail da confirmação é apenas informativo nesta tela e não pode ser trocado por ela.
        $novoEmail = trim((string) $C007['C007_Email']);
        if ($nova !== $confirmacao) $erros[] = 'A confirmação da nova senha não confere.';
        $erros = array_merge($erros, SegurancaSenha::validarNovaSenha($nova));
        if (SegurancaSenha::senhaReutilizada($nova, array($C007['C007_Senha'], $C007['C007_Senha_Anterior_1'], $C007['C007_Senha_Anterior_2'], $C007['C007_Senha_Anterior_3']))) $erros[] = 'A nova senha não pode ser igual à atual ou a uma das três senhas anteriores.';
    }
    if ($erros) return array('code' => false, 'data' => $erros);

    $v = SegurancaSenha::prepararAtualizacaoSenha($nova, $C007);
    $set = array(); foreach (array('C007_Senha','C007_Senha_Anterior_1','C007_Senha_Anterior_2','C007_Senha_Anterior_3') as $campo) $set[] = $campo . ' = ' . ($v[$campo] === null ? 'NULL' : "'" . mysql_real_escape_string($v[$campo]) . "'");
    $set[] = "C007_Data_Alteracao_Senha = '" . mysql_real_escape_string($v['C007_Data_Alteracao_Senha']) . "'";
    if (!mysql_query('UPDATE C007 SET ' . implode(', ', $set) . " WHERE C007_Id = '{$C007_Id}'")) return array('code' => false, 'data' => 'Erro ao gravar a nova senha.');
    $emailEnviado = enviarEmailSenhaAlterada($novoEmail, !empty($C007['C007_Primeiro_Nome']) ? $C007['C007_Primeiro_Nome'] : $C007['C007_Nome'], $C007['C007_Login'], $nova);
    $politica = senhaPoliticaConfigurada();
    return array('code' => true, 'dataVencimento' => date('d/m/Y', strtotime($v['C007_Data_Alteracao_Senha'] . ' +' . $politica['prazoVencimentoDias'] . ' days')), 'email' => $novoEmail, 'emailEnviado' => $emailEnviado);
}

function senhaTokenAdministrativoValido($C007_Id, $token)
{
    $C007_Id = (int) $C007_Id;
    $registro = isset($_SESSION['senha_admin_troca'][$C007_Id]) ? $_SESSION['senha_admin_troca'][$C007_Id] : null;
    if (!$registro || empty($registro['token']) || empty($registro['criado_em']) || time() - (int) $registro['criado_em'] > 900) return false;
    return hash_equals($registro['token'], (string) $token);
}

header('Content-Type: application/json; charset=utf-8');
$C007_Id = isset($g['usuarioAtual']) ? (int) $g['usuarioAtual'] : 0;

if ($g['r'][3] === 'status') {
    $resposta = array('code' => true, 'mostrar' => false);
    if ($C007_Id > 0 && ($sql = mysql_query("SELECT C007_Data_Alteracao_Senha, C007_Flag_Senha_Nao_Expira, C007_Email, C007_Primeiro_Nome, C007_Nome FROM C007 WHERE C007_Id = '{$C007_Id}' AND C007_Ativo = 'S' LIMIT 1")) && ($C007 = mysql_fetch_assoc($sql))) {
        $resposta['email'] = (string) $C007['C007_Email'];
        $resposta['nome'] = trim(!empty($C007['C007_Primeiro_Nome']) ? $C007['C007_Primeiro_Nome'] : $C007['C007_Nome']);
        // O usuário ROOT e contas com exceção não recebem bloqueio ou lembrete de expiração.
        if ($C007_Id === 63 || (isset($C007['C007_Flag_Senha_Nao_Expira']) && $C007['C007_Flag_Senha_Nao_Expira'] === 'S')) { echo json_encode($resposta); return; }
        // ini() carrega também a personalização GLOBAL-ini.php específica do banco.
        $politica = senhaPoliticaConfigurada();
        if ($vencimento = SegurancaSenha::vencimento($C007['C007_Data_Alteracao_Senha'], $politica['dataMarco'], $politica['prazoVencimentoDias'], $politica['prazoInicialMarcoDias'])) {
            $dias = (int) (new \DateTime('today'))->diff($vencimento)->format('%r%a');
            if ($dias <= $politica['diasAvisoVencimento']) $resposta = array_merge($resposta, array('mostrar' => true, 'obrigatoria' => $dias <= 0, 'diasRestantes' => $dias, 'dataVencimento' => $vencimento->format('d/m/Y'), 'prazoVencimentoDias' => $politica['prazoVencimentoDias']));
        }
    }
    echo json_encode($resposta); return;
}
if ($g['r'][3] === 'token') {
    if ($C007_Id <= 0) { echo json_encode(array('code' => false, 'data' => 'Usuário não autenticado.')); return; }
    if (empty($_SESSION['senha_csrf'])) $_SESSION['senha_csrf'] = bin2hex(random_bytes(32));
    echo json_encode(array('code' => true, 'csrf' => $_SESSION['senha_csrf'])); return;
}
if ($g['r'][3] === 'statusAdmin') {
    $idAdministrativo = isset($_GET['C007_Id']) ? (int) $_GET['C007_Id'] : 0;
    $tokenAdministrativo = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!senhaTokenAdministrativoValido($idAdministrativo, $tokenAdministrativo)) { echo json_encode(array('code' => false, 'data' => 'A autorização para alterar esta senha expirou. Reabra o cadastro do usuário.')); return; }
    $sql = mysql_query("SELECT C007_Email, C007_Primeiro_Nome, C007_Nome FROM C007 WHERE C007_Id = '{$idAdministrativo}' AND C007_Ativo = 'S' LIMIT 1");
    $C007 = mysql_fetch_assoc($sql);
    if (!$C007) { echo json_encode(array('code' => false, 'data' => 'Usuário não encontrado ou inativo.')); return; }
    echo json_encode(array('code' => true, 'email' => (string) $C007['C007_Email'], 'nome' => trim(!empty($C007['C007_Primeiro_Nome']) ? $C007['C007_Primeiro_Nome'] : $C007['C007_Nome']))); return;
}
if ($g['r'][3] === 'alterarAdmin') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(array('code' => false, 'data' => 'Requisição inválida.')); return; }
    $idAdministrativo = isset($_POST['C007_Id']) ? (int) $_POST['C007_Id'] : 0;
    $tokenAdministrativo = isset($_POST['tokenAdministrativo']) ? (string) $_POST['tokenAdministrativo'] : '';
    if (!senhaTokenAdministrativoValido($idAdministrativo, $tokenAdministrativo)) { echo json_encode(array('code' => false, 'data' => 'A autorização para alterar esta senha expirou. Reabra o cadastro do usuário.')); return; }
    $retorno = senhaAlterarUsuario($idAdministrativo, isset($_POST['novaSenha']) ? (string) $_POST['novaSenha'] : '', isset($_POST['confirmacaoNovaSenha']) ? (string) $_POST['confirmacaoNovaSenha'] : '', trim(isset($_POST['novoEmail']) ? (string) $_POST['novoEmail'] : ''));
    if (!empty($retorno['code'])) unset($_SESSION['senha_admin_troca'][$idAdministrativo]);
    echo json_encode($retorno, JSON_UNESCAPED_UNICODE); return;
}
if ($g['r'][3] === 'alterarEmail') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $C007_Id <= 0) { echo json_encode(array('code' => false, 'data' => 'Requisição inválida.')); return; }
    $novoEmail = trim(isset($_POST['novoEmail']) ? (string) $_POST['novoEmail'] : '');
    $confirmacaoEmail = trim(isset($_POST['confirmacaoEmail']) ? (string) $_POST['confirmacaoEmail'] : '');
    $sql = mysql_query("SELECT C007_Senha FROM C007 WHERE C007_Id = '{$C007_Id}' AND C007_Ativo = 'S' LIMIT 1");
    $C007 = mysql_fetch_assoc($sql);
    $erros = array();
    if (!$C007 || !SegurancaSenha::validarSenhaArmazenada(isset($_POST['senhaAtual']) ? (string) $_POST['senhaAtual'] : '', $C007['C007_Senha'])) $erros[] = 'A senha atual informada não confere.';
    if ($novoEmail !== $confirmacaoEmail) $erros[] = 'A confirmação do e-mail não confere.';
    if (!filter_var($novoEmail, FILTER_VALIDATE_EMAIL)) $erros[] = 'Informe um e-mail válido.';
    if ($erros) { echo json_encode(array('code' => false, 'data' => $erros), JSON_UNESCAPED_UNICODE); return; }
    if (!mysql_query("UPDATE C007 SET C007_Email = '" . mysql_real_escape_string($novoEmail) . "' WHERE C007_Id = '{$C007_Id}'")) { echo json_encode(array('code' => false, 'data' => 'Erro ao gravar o e-mail.')); return; }
    echo json_encode(array('code' => true, 'email' => $novoEmail), JSON_UNESCAPED_UNICODE); return;
}
if ($g['r'][3] !== 'alterar' || $_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(array('code' => false, 'data' => 'Método de requisição inválido.')); return; }
if ($C007_Id <= 0) { echo json_encode(array('code' => false, 'data' => 'Usuário não autorizado para esta alteração.')); return; }
$retorno = senhaAlterarUsuario($C007_Id, isset($_POST['novaSenha']) ? (string) $_POST['novaSenha'] : '', isset($_POST['confirmacaoNovaSenha']) ? (string) $_POST['confirmacaoNovaSenha'] : '', trim(isset($_POST['novoEmail']) ? (string) $_POST['novoEmail'] : ''));
unset($_SESSION['senha_csrf']);
echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
