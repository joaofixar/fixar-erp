<?php
namespace hardness;

function boletoProtegidoHabilitado($empresaAtual = false)
{
    global $g;

    if ($empresaAtual === false && isset($g['C031']['enviarBoletoProtegido'])) {
        return $g['C031']['enviarBoletoProtegido'] === 'S';
    }

    if ($empresaAtual === false) {
        $empresaAtual = isset($g['empresaAtual']) && !empty($g['empresaAtual']) ? $g['empresaAtual'] : 1;
    }

    $empresaAtual = mysql_real_escape_string($empresaAtual);
    $sql = mysql_query("SELECT C031_Valor
                          FROM C031
                         WHERE C031_C004_Id = '{$empresaAtual}'
                           AND C031_Campo = 'enviarBoletoProtegido'
                         LIMIT 1");

    if ($sql && mysql_num_rows($sql) > 0) {
        $row = mysql_fetch_assoc($sql);
        if ($empresaAtual == (isset($g['empresaAtual']) ? $g['empresaAtual'] : null)) {
            $g['C031']['enviarBoletoProtegido'] = $row['C031_Valor'];
        }
        return $row['C031_Valor'] === 'S';
    }

    if ($empresaAtual == (isset($g['empresaAtual']) ? $g['empresaAtual'] : null)) {
        $g['C031']['enviarBoletoProtegido'] = 'N';
    }
    return false;
}

function boletoDocumentoSomenteDigitos($valor)
{
    return preg_replace('/\D+/', '', (string) $valor);
}

function boletoBuscarRegistroPorCQuery($cQuery)
{
    $cQuery = mysql_real_escape_string($cQuery);
    $sql = "SELECT T117_Id,
                   T117_D024_Id,
                   T117_C004_Id,
                   D024_Nome_Empresa,
                   D024_Cnpj,
                   D024_Cpf
              FROM T117
         LEFT JOIN D024 ON D024_Id = T117_D024_Id
         LEFT JOIN T002 ON T002_T117_Id = T117_Id
             WHERE md5(concat(T117_Id,T117_D024_Id)) = '{$cQuery}'
                OR md5(concat(T117_D024_Id,T002_T007_Id)) = '{$cQuery}'
          GROUP BY T117_Id
             LIMIT 1";

    $result = mysql_query($sql);
    if (!$result || mysql_num_rows($result) <= 0) {
        return false;
    }

    return mysql_fetch_assoc($result);
}

function boletoDocumentoEsperado(array $registro)
{
    if (!empty($registro['D024_Cpf'])) {
        return boletoDocumentoSomenteDigitos($registro['D024_Cpf']);
    }

    return boletoDocumentoSomenteDigitos($registro['D024_Cnpj']);
}

function boletoDocumentoTipo(array $registro)
{
    return !empty($registro['D024_Cpf']) ? 'CPF' : 'CNPJ';
}

function boletoDocumentoPlaceholder(array $registro)
{
    return boletoDocumentoTipo($registro) === 'CPF' ? '000.000.000-00' : '00.000.000/0000-00';
}

function boletoMontarLinkProtegido($cQuery)
{
    global $confUsuario;

    return "{$confUsuario['urlRaiz']}/hardness3/prog/externo/cobranca/gerarBoletoEmpresaProtegido.php?cQuery={$cQuery}";
}

function boletoDocumentoConfere($informado, array $registro)
{
    $documentoInformado = boletoDocumentoSomenteDigitos($informado);
    $documentoEsperado = boletoDocumentoEsperado($registro);

    return !empty($documentoEsperado) && $documentoInformado === $documentoEsperado;
}

function boletoRenderizarValidacaoDocumento(array $registro, $action, $camposHidden = array(), $mensagemErro = '')
{
    $tipoDocumento = boletoDocumentoTipo($registro);
    $placeholder = boletoDocumentoPlaceholder($registro);
    $cliente = htmlspecialchars($registro['D024_Nome_Empresa'], ENT_QUOTES, 'UTF-8');
    $action = htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
    $mensagemErro = trim((string) $mensagemErro);
    $htmlErro = '';

    if ($mensagemErro !== '') {
        $mensagemErro = htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8');
        $htmlErro = "<div style=\"margin-bottom:16px;padding:12px 14px;border-radius:10px;background:#fff1f0;color:#b42318;border:1px solid #f4c7c3;\">{$mensagemErro}</div>";
    }

    $inputsHidden = '';
    foreach ($camposHidden as $chave => $valor) {
        if ($valor === null || $valor === '') {
            continue;
        }

        $chave = htmlspecialchars($chave, ENT_QUOTES, 'UTF-8');
        $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        $inputsHidden .= "<input type=\"hidden\" name=\"{$chave}\" value=\"{$valor}\">";
    }

    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validar acesso ao boleto</title>
</head>
<body style="margin:0;background:#f3f6fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;">
        <div style="width:100%;max-width:460px;background:#ffffff;border:1px solid #d8e0ea;border-radius:18px;box-shadow:0 20px 50px rgba(15,23,42,0.10);padding:28px;">
            <div style="font-size:12px;font-weight:bold;letter-spacing:.08em;text-transform:uppercase;color:#4b6b88;margin-bottom:10px;">Boleto protegido</div>
            <h1 style="margin:0 0 12px;font-size:26px;line-height:1.2;color:#102a43;">Confirme seu {$tipoDocumento}</h1>
            <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#486581;">
                Para visualizar o boleto de <strong>{$cliente}</strong>, informe o {$tipoDocumento} cadastrado no sistema.
            </p>
            {$htmlErro}
            <form method="post" action="{$action}">
                {$inputsHidden}
                <label for="documento" style="display:block;margin-bottom:8px;font-size:13px;font-weight:bold;color:#243b53;">{$tipoDocumento}</label>
                <input
                    id="documento"
                    name="documento"
                    type="text"
                    inputmode="numeric"
                    autocomplete="off"
                    placeholder="{$placeholder}"
                    style="box-sizing:border-box;width:100%;padding:14px 16px;border:1px solid #bcccdc;border-radius:12px;font-size:16px;color:#102a43;"
                >
                <button type="submit" style="margin-top:16px;width:100%;padding:14px 16px;border:0;border-radius:12px;background:#1f6feb;color:#fff;font-size:15px;font-weight:bold;cursor:pointer;">
                    Abrir boleto
                </button>
            </form>
        </div>
    </div>
</body>
</html>
HTML;
}
