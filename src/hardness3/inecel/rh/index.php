<?php
namespace hardness;

header("X-Robots-Tag: noindex, nofollow", true);

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
require_once('../../bibliotecas/segurancaSenha.php');

$js = '';
$redirectDestino = './painel.php';

if (isset($_REQUEST['login'])) {
    if ($_SERVER['HTTP_HOST'] == 'hard.hardness.com.br' || $_SERVER['HTTP_HOST'] == 'cha.chasistemas.com.br') {
        $usarSenhaRootPadrao = false;
    } else {
        $usarSenhaRootPadrao = true;
    }

    if (!isset($_SESSION['bancoDados'])) {
        @session_destroy();
        $_SESSION = array();
    }
    session_start();

    $e = '1';
    $l = $_REQUEST['login'];
    $s = $_REQUEST['senha'];
    $Banco_Dados = $w = $sb = $us = $uc = $Empresa_Usuaria = $PaginaQueIniciou = $nWidth_Padrao = $nHeight_Padrao = '';
    $user = explode('/', $confUsuario['pathRaiz']);
    $user = isset($user[2]) ? $user[2] : '';
    $l_ = (substr($l, 0, 5) == 'hard_') ? 'root' : $l;

    $sqlLoginDuplicado = mysqli_query("SELECT C007_Login FROM C007 WHERE C007_Login='{$l_}' AND C007_Login!='root' AND C007_Ativo='S'");

    if (empty($l)) {
        $js .= "alert('Digite o login.');";
    } else if (empty($s)) {
        $js .= "alert('Digite a senha.');";
    } else if (empty($e)) {
        $js .= "alert('Selecione a empresa.');";
    } else if (mysql_num_rows($sqlLoginDuplicado) > 1) {
        $js .= "alert('Existe mais de um usuario cadastrado com seu login, comunique o administrador do seu sistema.');";
    } else {
        $ordenaEmpresaPerfil = mysqli_query("SELECT C019_Id
                                               FROM C007
                                          LEFT JOIN C019 ON C019_C007_Id=C007_Id
                                          LEFT JOIN C021 ON C021_Id=C019_C021_Id
                                          LEFT JOIN C004 ON C019_C004_Id=C004_Id
                                              WHERE C007_Login='{$l_}'
                                                AND C007_Ativo='S'
                                                AND C004_Flag_Ativo='S'
                                                AND C019_Ordem_Empresa > 0");

        $orderByEmpresas = "ORDER BY C004_Ordem_Empresa,C004_Id ";
        if (mysqli_num_rows($ordenaEmpresaPerfil) > 0) {
            $orderByEmpresas = "ORDER BY C019_Ordem_Empresa,C004_Id ";
        }

        $sqlEmpresaUsuario = mysqli_query("SELECT C019_C004_Id,
                                                  C007_Login,
                                                  C004_Ordem_Empresa
                                             FROM C007
                                        LEFT JOIN C019 ON C019_C007_Id=C007_Id
                                        LEFT JOIN C021 ON C021_Id=C019_C021_Id
                                        LEFT JOIN C004 ON C019_C004_Id=C004_Id
                                            WHERE C007_Login='{$l_}' AND C007_Ativo='S' AND C004_Flag_Ativo='S'
                                                  {$orderByEmpresas}
                                            LIMIT 1");

        $rowEmpresaUsuario = mysql_fetch_array($sqlEmpresaUsuario);
        if (mysql_num_rows($sqlEmpresaUsuario) == 1) {
            $e = $rowEmpresaUsuario['C019_C004_Id'];
        }

        $cC004 = mysqli_query("select * from C004 where C004_Id='$e'");
        $mC004 = mysql_fetch_array($cC004);
        if (empty($rowEmpresaUsuario['C007_Login'])) {
            $js .= "alert('Cadastro de usuario nao encontrado.');";
        } else if (empty($mC004['C004_Banco_Configuracao'])) {
            $js .= "alert('Banco de configuracao nao definido.');";
        } else {
            $cBd_Configuracao = $mC004['C004_Banco_Configuracao'];

            $usuariosSuporte = array(
                'bruno' => '93513b491b333c2c6201d7ae5d95bef3',
                'ericks' => 'f70fc7d8ad7b81747a10fe4ef6f37d01'
            );

            if ($l != 'root' && substr($l, 0, 5) != 'hard_') {
                $Sql = mysqli_query("select * from C007
                                       left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
                                       left join C021 on C021_Id=C019_C021_Id
                                      where C007_Login='$l'
                                        and C007_Ativo='S'
                                        and (C007_D024_Id=0 or C007_D024_Id is null)");
            } else {
                $Sql = mysqli_query("select * from C007
                                       left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
                                       left join C021 on C021_Id=C019_C021_Id
                                      where C007_Id='63'
                                        and (C007_D024_Id=0 or C007_D024_Id is null)");
            }

            if (mysql_num_rows($Sql) == 0) {
                $js .= "alert('Login ou Senha invalida (1).');";
            } else {
                $mSenha = mysql_fetch_array($Sql);
                if ($mSenha['C007_Id'] == 63 && $l_ != 'root') {
                    $js .= "alert('Pasta da empresa nao definida.');";
                } else {
                    $Senha = base64_encode($s);
                    $senhaValida = SegurancaSenha::validarSenhaArmazenada($s, $mSenha['C007_Senha']);
                    $IP_Origem = substr($_SERVER['REMOTE_ADDR'], 0, strrpos($_SERVER['REMOTE_ADDR'], '.'));

                    $validaHorarioAcesso = true;
                    if (($mSenha['C007_Acesso_Hora_Inicio'] != '00:00:00' && !empty($mSenha['C007_Acesso_Hora_Inicio'])) &&
                        ($mSenha['C007_Acesso_Hora_Fim'] != '00:00:00' && !empty($mSenha['C007_Acesso_Hora_Fim']))) {
                        $horaInicio = $mSenha['C007_Acesso_Hora_Inicio'];
                        $horaFim = $mSenha['C007_Acesso_Hora_Fim'];
                        $horaAtual = date('H:i:s');
                        $validaHorarioAcesso = ($horaAtual >= $horaInicio && $horaAtual <= $horaFim);
                    }

                    $acessoDiaNaoUtil = true;
                    if ($mSenha['C007_Acesso_Dias_Nao_Uteis'] == 'N') {
                        $mensagemFinalDeSemana = '';
                        $mensagemFeriado = '';
                        $diaSemana = date('N');
                        if ($diaSemana == 6 || $diaSemana == 7) {
                            $acessoDiaNaoUtil = false;
                            $mensagemFinalDeSemana = 'Final de semana';
                        } elseif ($mSenha['C007_D206_Id'] > 0) {
                            $feriado = mysqli_query("SELECT D207_Data,
                                                            D207_Descricao_Feriado
                                                       FROM D207
                                                  LEFT JOIN D206 ON D206_Id = D207_D206_Id
                                                      WHERE D206_Id = '{$mSenha['C007_D206_Id']}'
                                                        AND D206_Flag_Ativo = 'S'
                                                        AND CURDATE() = D207_Data
                                                      LIMIT 1");
                            if (mysqli_num_rows($feriado) > 0) {
                                $mFeriado = mysql_fetch_array($feriado);
                                $acessoDiaNaoUtil = false;
                                $mensagemFeriado = 'Feriado de ' . $mFeriado['D207_Descricao_Feriado'];
                            }
                        }
                    }

                    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $IP_Origem = substr($_SERVER['HTTP_X_FORWARDED_FOR'], 0, strrpos($_SERVER['HTTP_X_FORWARDED_FOR'], '.'));
                    }

                    if (!$usarSenhaRootPadrao && !$senhaValida) {
                        $js .= "alert('Login ou Senha invalida (2).');";
                    } else if ($mSenha['C007_Id'] == 63 &&
                        md5($s) == $sys['senhaRootSuporte'] &&
                        !empty($sys['IPRootSuporte']) &&
                        ($sys['IPRootSuporte'] != md5($IP_Origem) && $sys['IPRootSuporte2'] != md5($IP_Origem))) {
                    } else if ($usarSenhaRootPadrao && (
                        (!$senhaValida && $mSenha['C007_Id'] != 63) ||
                        ($mSenha['C007_Id'] == 63 && (
                            ($l == 'root' && md5($s) != $sys['senhaRootMaster'] && md5($s) != $sys['senhaRootSuporte'] && (!$senhaValida || $s == 'root') && $sys['dominio'] == 'hardness.com.br') ||
                            (substr($l, 0, 5) == 'hard_' && md5($s) != $usuariosSuporte[substr($l, 5)] && $sys['dominio'] == 'hardness.com.br') ||
                            ($l == 'root' && md5($s) != $sys['senhaRootSistema'] && md5($s) != $sys['senhaRootMaster'] && (!$senhaValida || $s == 'root') && $sys['dominio'] != 'hardness.com.br')
                        ))
                    )) {
                        $js .= "alert('Login ou Senha invalida (3).');";
                    } elseif ($mSenha['C007_Id'] != 63 && $acessoDiaNaoUtil === false) {
                        if (!empty($mensagemFinalDeSemana)) {
                            $js .= "alert('Acesso nao liberado em dias nao uteis ({$mensagemFinalDeSemana})');";
                        } else {
                            $js .= "alert('Acesso nao liberado em dias nao uteis ({$mensagemFeriado})');";
                        }
                    } elseif ($mSenha['C007_Id'] != 63 && $validaHorarioAcesso === false) {
                        $js .= "alert('Acesso nao liberado neste horario (Permitido : {$mSenha['C007_Acesso_Hora_Inicio']} as {$mSenha['C007_Acesso_Hora_Fim']})');";
                    } elseif ($mSenha['C007_Id'] != 63 && !empty($mSenha['C007_IP_Acesso']) && ($Senha != $mSenha['C007_Senha_Especial']) && strpos(' ' . $mSenha['C007_IP_Acesso'], $IP_Origem) <= 0) {
                        $js .= "alert('Desculpe, o acesso foi negado para este local. ({$IP_Origem})');";
                    } else if ($mSenha['C007_Flag_Ausente'] != 'N' && $l != 'root') {
                        $js .= "alert('Acesso nao permitido, o usuario esta ausente. Por favor procure a gerencia.');";
                    } else if ($mSenha['C007_Dispositivo_Acesso'] != '0' && $mSenha['C007_Dispositivo_Acesso'] != '1' && $l != 'root') {
                        $js .= "alert('Acesso nao permitido por este dispositivo. Por favor procure a gerencia.');";
                    } else {
                        if (!empty($mSenha['C007_Cargo'])) {
                            $mSenha['C007_Cargo'] = '  -   (  ' . $mSenha['C007_Cargo'] . '  )';
                        }

                        if (empty($mC004['C004_Caminho_Sistema'])) {
                            $js .= "alert('Pasta da empresa nao definida.');";
                        } else {
                            mysqli_query("UPDATE C007 SET C007_Ultimo_Acesso = NOW() WHERE C007_Id = '{$mSenha['C007_Id']}'");

                            setcookie('liberacaoCreditoFlag', 2, 0, '/');

                            $_SESSION['Modulo_NFe'] = 'S';
                            $_SESSION['Usuario_Atual'] = $mSenha['C007_Id'];
                            $_SESSION['Nome_Usuario'] = $mSenha['C007_Nome'];
                            $_SESSION['Primeiro_Nome_Usuario'] = $mSenha['C007_Primeiro_Nome'];
                            $_SESSION['Alcada_Usuario'] = $mSenha['C007_Flag_Vendedor'];
                            $_SESSION['Cargo_Usuario'] = $mSenha['C007_Cargo'];
                            $_SESSION['Perfil_Usuario'] = $mSenha['C021_Id'];
                            $_SESSION['Empresa_Atual'] = "$e";
                            $_SESSION['Empresa_Logada'] = "$e";
                            $_SESSION['Nome_Empresa_Atual'] = $mC004['C004_Nome_Empresa'];
                            $_SESSION['Banco_Dados'] = $confUsuario['dbDatabase'];
                            $_SESSION['sb'] = "$sb";
                            $_SESSION['nWidth_Padrao'] = "$nWidth_Padrao";
                            $_SESSION['nHeight_Padrao'] = "$nHeight_Padrao";
                            $_SESSION['Pasta_Empresa'] = $mC004['C004_Caminho_Sistema'];
                            $_SESSION['cBd_Configuracao'] = $cBd_Configuracao;
                            $_SESSION['Usuario_Suporte'] = $mSenha['C007_Usuario_Suporte'];
                            $_SESSION['User_Suport'] = "$us";
                            $_SESSION['Usuario_Contador'] = "$uc";
                            $_SESSION['Empresa_Usuaria'] = "$Empresa_Usuaria";
                            $_SESSION['Data_Hora_Login'] = time();
                            $_SESSION['Cliente_Atual'] = null;
                            $_SESSION['Cliente_Atual_Contato'] = null;
                            $_SESSION['Usuario_Alcada'] = $mSenha['C007_Flag_Vendedor'];
                            if (!empty($mSenha['C007_Acesso_Hora_Inicio']) && !empty($mSenha['C007_Acesso_Hora_Fim'])) {
                                $_SESSION['Acesso_Hora_Inicio'] = $mSenha['C007_Acesso_Hora_Inicio'];
                                $_SESSION['Acesso_Hora_Fim'] = $mSenha['C007_Acesso_Hora_Fim'];
                            } else {
                                $_SESSION['Acesso_Hora_Inicio'] = null;
                                $_SESSION['Acesso_Hora_Fim'] = null;
                            }

                            $_SESSION['URL_Historico'] = "";
                            $_SESSION['URL_Tipo_Historico'] = "";
                            $_SESSION['URL_Descricao_Historico'] = "";
                            $_SESSION['URL_Entidade_Principal'] = "";
                            $_SESSION['Pagina_Monta_Consulta'] = "";
                            $_SESSION['Chave_Pagina_Monta_Consulta'] = "";
                            $_SESSION['PaginaQueIniciou'] = $PaginaQueIniciou;

                            apc_delete("cacheC007-{$confUsuario['dbDatabase']}");
                            header("Location: {$redirectDestino}");
                            exit;
                        }
                    }
                }
            }
        }
    }
}

$faviconPath = '';
if (isset($sys['favicon'])) {
    $favicon = $sys['favicon'];
    $faviconPath = "<link rel='icon' type='image/x-icon' href='{$favicon}'>";
}

if ($confUsuario['dbDatabase'] == 'e212bkp') {
    $sql = "SELECT C004_Nome_Abreviado FROM C004 WHERE C004_Id=4";
} else {
    $sql = "SELECT C004_Nome_Abreviado FROM C004 ORDER BY C004_Id ASC LIMIT 1";
}

$res = mysqli_query($sql);
$row = mysql_fetch_assoc($res);
$rodape = 'Hardness Sistemas ' . date('Y');
$logo = !empty($sys['logoSistema']) ? $sys['logoSistema'] : '../coletor/logo.png';
$nomeEmpresa = !empty($row['C004_Nome_Abreviado']) ? substr($row['C004_Nome_Abreviado'], 0, 27) : 'RH';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>RH - Login</title>
    <?php echo $faviconPath; ?>
    <style>
        :root {
            --bg-start: #f6f0e7;
            --bg-end: #eadbc8;
            --card-bg: #ffffff;
            --primary: #9a5b1f;
            --primary-dark: #6e3f12;
            --accent: #f4b266;
            --text: #3d2a18;
            --muted: #7e6b57;
            --border: #e9dbc8;
            --shadow: 0 24px 56px rgba(86, 49, 16, 0.18);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.95), transparent 28%),
                linear-gradient(135deg, var(--bg-start), var(--bg-end));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-shell {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: minmax(300px, 430px) minmax(320px, 1fr);
            background: rgba(255, 255, 255, 0.26);
            border: 1px solid rgba(255, 255, 255, 0.44);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }

        .brand-panel {
            position: relative;
            padding: 46px 40px;
            background:
                linear-gradient(160deg, rgba(154, 91, 31, 0.98), rgba(110, 63, 18, 0.98)),
                #9a5b1f;
            color: #fff7ef;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 620px;
        }

        .brand-panel::before,
        .brand-panel::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
        }

        .brand-panel::before {
            width: 190px;
            height: 190px;
            top: -70px;
            left: -60px;
        }

        .brand-panel::after {
            width: 280px;
            height: 280px;
            right: -110px;
            bottom: -100px;
        }

        .brand-top,
        .brand-bottom {
            position: relative;
            z-index: 1;
        }

        .brand-tag {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.22);
            font-size: 13px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .brand-panel h1 {
            margin: 24px 0 16px;
            font-size: 40px;
            line-height: 1.08;
            max-width: 300px;
        }

        .brand-panel p {
            margin: 0;
            max-width: 336px;
            line-height: 1.7;
            color: rgba(255, 247, 239, 0.86);
        }

        .brand-points {
            display: grid;
            gap: 14px;
            margin-top: 30px;
        }

        .brand-point {
            padding: 15px 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .brand-point strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .brand-point span {
            font-size: 13px;
            color: rgba(255, 247, 239, 0.8);
        }

        .form-panel {
            background: var(--card-bg);
            padding: 52px 46px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-card {
            width: 100%;
            max-width: 360px;
        }

        .logo {
            display: block;
            width: 180px;
            max-width: 100%;
            height: 99px;
            object-fit: contain;
            margin: 0 auto 28px;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .form-card h2 {
            margin: 0 0 10px;
            font-size: 30px;
            line-height: 1.15;
        }

        .subtitle {
            margin: 0 0 10px;
            color: var(--muted);
            line-height: 1.6;
        }

        .empresa {
            margin: 0 0 28px;
            font-size: 14px;
            font-weight: 600;
            color: #6a573f;
        }

        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .field input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px 15px;
            font-size: 15px;
            color: var(--text);
            background: #fffdf9;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input:focus {
            outline: none;
            border-color: rgba(154, 91, 31, 0.65);
            box-shadow: 0 0 0 4px rgba(154, 91, 31, 0.12);
            background: #ffffff;
        }

        .row-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 10px 0 22px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
        }

        .remember input {
            width: 16px;
            height: 16px;
        }

        .link-button {
            border: 0;
            background: transparent;
            color: var(--primary);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
        }

        .submit {
            width: 100%;
            border: 0;
            border-radius: 16px;
            padding: 15px 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 16px 32px rgba(154, 91, 31, 0.25);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(154, 91, 31, 0.31);
        }

        .footer {
            margin-top: 22px;
            text-align: center;
            font-size: 13px;
            color: var(--muted);
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(31, 19, 10, 0.56);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .modal-backdrop.is-open {
            display: flex;
        }

        .modal-card {
            position: relative;
            width: 100%;
            max-width: 430px;
            background: #fff;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 24px 60px rgba(61, 42, 24, 0.22);
        }

        .modal-card h3 {
            margin: 0 0 12px;
            font-size: 24px;
        }

        .modal-card p {
            margin: 0 0 18px;
            color: var(--muted);
            line-height: 1.6;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 22px;
        }

        .secondary,
        .primary {
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .secondary {
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
        }

        .primary {
            border: 0;
            background: var(--primary);
            color: #fff;
        }

        .modal-note {
            display: none;
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 13px;
        }

        .modal-note.error {
            display: block;
            background: #fff1ef;
            color: #a13f30;
        }

        .modal-note.success {
            display: block;
            background: #eef8f1;
            color: #256844;
        }

        .loader {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.78);
            border-radius: 22px;
        }

        .loader.is-open {
            display: flex;
        }

        .spinner {
            width: 54px;
            height: 54px;
            border: 4px solid #eedfcd;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 860px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                min-height: auto;
                padding: 34px 28px;
            }

            .form-panel {
                padding: 34px 24px;
            }
        }

        @media (max-width: 520px) {
            body {
                padding: 12px;
            }

            .brand-panel h1 {
                font-size: 32px;
            }

            .row-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            .modal-actions {
                flex-direction: column-reverse;
            }

            .secondary,
            .primary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <section class="brand-panel">
            <div class="brand-top">
                <span class="brand-tag">Modulo RH</span>
                <h1>Gestao de pessoas com acesso claro, rapido e familiar.</h1>
                <p>
                    O novo projeto de RH nasce dentro da estrutura Inecel, mantendo a mesma base de autenticacao
                    do sistema e uma tela de entrada alinhada ao fonte padrao encontrado no repositório.
                </p>
            </div>

            <div class="brand-bottom">
                <div class="brand-points">
                    <div class="brand-point">
                        <strong>Acesso no mesmo padrao</strong>
                        <span>Login reaproveitando regras existentes de sessao, horario, IP e perfis.</span>
                    </div>
                    <div class="brand-point">
                        <strong>Visual dedicado ao RH</strong>
                        <span>Identidade propria com linguagem mais humana, sem perder a consistencia do sistema.</span>
                    </div>
                    <div class="brand-point">
                        <strong>Base pronta para crescer</strong>
                        <span>Estrutura inicial criada para voce continuar com menus, cadastros e rotinas do modulo.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="form-panel">
            <div class="form-card">
                <img class="logo" src="<?php echo htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo">
                <p class="eyebrow">Recursos Humanos</p>
                <h2>Entrar no sistema</h2>
                <p class="subtitle">Informe suas credenciais para acessar o ambiente de RH.</p>
                <p class="empresa"><?php echo htmlspecialchars($nomeEmpresa, ENT_QUOTES, 'UTF-8'); ?></p>

                <form method="post" action="./index.php">
                    <div class="field">
                        <label for="login">Usuario</label>
                        <input id="login" name="login" type="text" placeholder="Digite seu usuario" autocomplete="username">
                    </div>

                    <div class="field">
                        <label for="senha">Senha</label>
                        <input id="senha" name="senha" type="password" placeholder="Digite sua senha" autocomplete="current-password">
                    </div>

                    <div class="row-actions">
                        <label class="remember" for="lembrar">
                            <input id="lembrar" type="checkbox" name="lembrar">
                            <span>Lembrar acesso</span>
                        </label>

                        <button class="link-button" type="button" id="open-modal">Esqueci minha senha</button>
                    </div>

                    <button class="submit" type="submit">Entrar</button>
                </form>

                <div class="footer">&copy; <?php echo htmlspecialchars($rodape, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </section>
    </div>

    <div class="modal-backdrop" id="password-modal" aria-hidden="true">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="loader" id="modal-loader">
                <div class="spinner"></div>
            </div>

            <h3 id="modal-title">Recuperacao de senha</h3>
            <p>Informe seu e-mail de cadastro para recuperar sua senha.</p>

            <div class="field">
                <label for="recovery-email">E-mail</label>
                <input id="recovery-email" type="email" placeholder="nome@empresa.com.br" autocomplete="email">
            </div>

            <div class="modal-note" id="modal-note"></div>

            <div class="modal-actions">
                <button class="secondary" type="button" id="close-modal">Fechar</button>
                <button class="primary" type="button" id="send-recovery">Enviar</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var injectedJs = <?php echo json_encode($js); ?>;
            if (injectedJs) {
                window.eval(injectedJs);
            }

            var modal = document.getElementById('password-modal');
            var openButton = document.getElementById('open-modal');
            var closeButton = document.getElementById('close-modal');
            var sendButton = document.getElementById('send-recovery');
            var recoveryInput = document.getElementById('recovery-email');
            var loginInput = document.getElementById('login');
            var note = document.getElementById('modal-note');
            var loader = document.getElementById('modal-loader');

            function openModal() {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                note.className = 'modal-note';
                note.textContent = '';
                window.setTimeout(function () {
                    recoveryInput.focus();
                }, 40);
            }

            function closeModal() {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                openButton.focus();
            }

            function showNote(type, text) {
                note.className = 'modal-note ' + type;
                note.textContent = text;
            }

            function setLoading(isLoading) {
                if (isLoading) {
                    loader.classList.add('is-open');
                } else {
                    loader.classList.remove('is-open');
                }
            }

            openButton.addEventListener('click', openModal);
            closeButton.addEventListener('click', closeModal);

            sendButton.addEventListener('click', function () {
                var email = recoveryInput.value.trim();

                if (!email) {
                    showNote('error', 'Por favor informe o e-mail.');
                    recoveryInput.focus();
                    return;
                }

                setLoading(true);
                note.className = 'modal-note';
                note.textContent = '';

                fetch('./esqueci_senha.php?ajax=true&eMail=' + encodeURIComponent(email), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                }).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    if (data.code) {
                        showNote('success', data.data);
                    } else {
                        showNote('error', data.data);
                    }
                }).catch(function () {
                    showNote('error', 'Algo deu errado ao enviar a recuperacao de senha.');
                }).finally(function () {
                    setLoading(false);
                });
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });

            if (loginInput) {
                loginInput.focus();
            }
        })();
    </script>
</body>
</html>
