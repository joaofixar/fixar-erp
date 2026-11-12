<?php
namespace hardness;

global $g;

$pathRaizProjeto = realpath(__DIR__ . '/../../../');
if ($pathRaizProjeto === false) {
    die('Não foi possível localizar a raiz do projeto.');
}

require_once($pathRaizProjeto . '/bibliotecas/confUsuario.php');
require_once($pathRaizProjeto . '/bibliotecas/conexaoBanco.php');
require_once($pathRaizProjeto . '/bibliotecas/funcoesGerais.php');
require_once($pathRaizProjeto . '/bibliotecas/boletoProtegido.php');

$cQuery = isset($_REQUEST['cQuery']) ? $_REQUEST['cQuery'] : '';

$g['empresaAtual'] = 1;
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
$g['pathRaiz'] = $confUsuario['pathRaiz'];

$C004 = mysql_query("select C004_Nome_Empresa from C004 where C004_Id=1");
$mC004 = mysql_fetch_array($C004);

$registroBoleto = false;
if (!empty($cQuery)) {
    $registroBoleto = boletoBuscarRegistroPorCQuery($cQuery);

    if (!$registroBoleto) {
        die("{$mC004['C004_Nome_Empresa']} <br /><br />Não encontrados registros para gerar o(s) boleto(s)<BR><BR>Faça as seguintes verificações:<BR><BR>. Os titulos estão pendentes ?<BR>. Não esta cancelado ?<BR>. Esta vencido ?<BR>. É Nota Fiscal ?<BR>. Foi definido Conta e Banco ?");
    }

    if (!empty($registroBoleto['T117_C004_Id'])) {
        $g['empresaAtual'] = $registroBoleto['T117_C004_Id'];
    }

    if (boletoProtegidoHabilitado($g['empresaAtual'])) {
        $documentoInformado = isset($_POST['documento']) ? $_POST['documento'] : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !boletoDocumentoConfere($documentoInformado, $registroBoleto)) {
            $mensagemErro = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $mensagemErro = 'O CPF/CNPJ informado não confere com o cadastro do cliente.';
            }

            boletoRenderizarValidacaoDocumento(
                $registroBoleto,
                $_SERVER['PHP_SELF'],
                array('cQuery' => $cQuery),
                $mensagemErro
            );
            exit;
        }
    }
}

$nT117_Id = $registroBoleto ? $registroBoleto['T117_Id'] : '';

if (empty($nT117_Id)) {
    die("Boleto não encontrado");
}

$link = '/hardness3/hardness/boleto/gerar_boleto.php?T117_Id=' . $nT117_Id;
$filename = md5('boleto_' . $nT117_Id) . '.pdf';
$link = str_replace(' ', '+', $link);
$dir  = $g['pathDados'] . 'pdf/';
is_dir($dir) or mkdir($dir, 0777, true);
$file     = $dir . $filename;
$fileFull = $g['pathRaiz'] . $file;
$fileUrl  = trim("\hardness3\ ") . $file;

if (is_file($fileFull)) {
    unlink($fileFull);
}

$chmod = "xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --lowquality --margin-bottom 0 \"{$confUsuario['urlRaiz']}{$link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"";
$retorno = shell_exec($chmod);

echo "<head><meta http-equiv='refresh' content=1;url='$fileUrl'></head>";

//unlink($fileFull);
