<?php
namespace hardness;

global $g;

/*****************************************************************************
*   SCRIPT DE ATUALIZACAO BASE DE TODAS AS EMPRESAS EM DETERMINADA VERSAO
*****************************************************************************/
chdir('/var/www/html/hardness3/');

// Configurações da Entidade
carregarBibliotecaFramework('sistema/confSistema.php');
// Restrições
require_once('bibliotecas/restricoes.php');
// Funções Gerais
carregarBibliotecaFramework('bibliotecas/funcoesGerais.php');
carregarBibliotecaFramework('sistema/apps.php');
carregarBibliotecaFramework('sistema/personalizacoes.php');
// SysConfig
require_once("configuracoes/sysConfig.php");

// confSistema
$g['conf'] = new confSistema();
$g['conf']->carregaAltText();

ini_set('default_charset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
//$link = mysqli_connect('172.31.19.221', 'root', '=!H2r4n6s8!=');
//echo mysqli_connect_error() . '\n\n\n';
$empresas = scandir('/var/www/sites');
foreach ($empresas as $index => $empresa){
	//Verifica os arquivos que o nome seja um arquivo de configuracao
	$siteAtual = explode('-',$empresa);
	$siteAtual = trim($siteAtual[0]);

	if(strpos($empresa, '-confUsuario.php') !== false) {
		echo $siteAtual . "\n\n\n";

		include("bibliotecas/confUsuario.php");
	    include('bibliotecas/conexaoBanco.php');

        echo "ATUALIZANDO DB: " . $confUsuario['dbDatabase'] . " - " . $confUsuario['urlRaiz'] . "\n";
	    $g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
		
		include('scripts/atualizacaoBase.php');

		mysqli_close($con);
		session_destroy();
		unset($_SESSION);
		unset($confUsuario);

		echo "\n\n\n";
	}
}

function carregarBibliotecaFramework($caminho) {
    $caminhoFW = "framework/{$caminho}";
    if (file_exists($caminhoFW)) {
        require_once($caminhoFW);
    } else {
        require_once($caminho);
    }
}

