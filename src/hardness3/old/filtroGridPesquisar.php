<?php
/*
   Programador: Evantoni Fuchs
   Data: 04/12/2009
*/

$pathRaiz=$_REQUEST["pathRaiz"];

include $pathRaiz . 'bibliotecas/sessao.php';

include $pathRaiz . 'bibliotecas/confUsuario.php';

include $pathRaiz . 'bibliotecas/conexaoBanco.php';

$cNomeTelaFiltro=$_REQUEST["cConfigNomeTelaFiltro"];
$divId=$_REQUEST["divId"];
$r=$_REQUEST["r"];
$divIdRoot=$_REQUEST["divIdRoot"];

include $pathRaiz . "bibliotecas/filtroGridUsuario.php";

unset($mConteudo_Filtro);

$cConfigCampos=base64_decode($cConfigCampos);
$mCampos=explode(',,',$cConfigCampos);

$cConfigFormatacaoCampo=base64_decode($cConfigFormatacaoCampo);
$mFormatacaoCampo=explode(',,',$cConfigFormatacaoCampo);

$xPesquisaSimples = str_replace(array(" ","/",".",",","-"),"",$cPesquisaSimples);
$mCondicao=array();

if (!empty($cPesquisaSimples))
{
	for ($nI=0;$nI<count($mCampos);$nI++)
	{	
		if (($mFormatacaoCampo[$nI]=='DATA') or ($mFormatacaoCampo[$nI]=='DATAHORA')) 
		{
			$mCondicao[] = "date_format(".$mCampos[$nI].",'%d%m%Y') like '%".$xPesquisaSimples."%' ";		
		}	
		elseif (strpos($mCampos[$nI],'||')!==FALSE)
		{
			$xmCampos = explode('||',$mCampos[$nI]);
			for ($i=0;$i<count($xmCampos);$i++)
			{
				$mCondicao[] = "replace(replace(replace(replace(replace(".$xmCampos[$i].",' ',''),'/',''),'.',''),',',''),'-','') like '%".$xPesquisaSimples."%' ";				
			}		
		}
		else
		{
			$mCondicao[] = "replace(replace(replace(replace(replace(".$mCampos[$nI].",' ',''),'/',''),'.',''),',',''),'-','') like '%".$xPesquisaSimples."%' ";		
		}
	}
}

$Acumula_Filtro = implode(" or ",$mCondicao) ;

/////////////////////////////////////////////////////////
include $pathRaiz . "bibliotecas/pasta_usuario.php";

//-- DEFININDO ARQUIVO QUE GUARDAR� OS DADOS
//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.php
$cArquivo=$Diretorio_Usuario."/".$cConfigNomeTelaFiltro.".php";
	
$mDados[]='<?php';
$mDados[]='$xTimerLimpar="'.time().'";';
$mDados[]='$xConsulta_Conteudo_Filtro_Simples="'.$cPesquisaSimples.'";';
$mDados[]='$xConsulta_Clausula_Filtro="'.$Acumula_Filtro.'";';
$mDados[]='$xConsulta_Ordem="'.$xConsulta_Ordem.'";';
$mDados[]='$xConsulta_Ordem_Desc="'.$xConsulta_Ordem_Desc.'";';
$mDados[]='$xConsulta_Order_By="'.$xConsulta_Order_By.'";';
$mDados[]='?'.'>';

$cDados=implode("\n",$mDados);

// grava o arquivo
if (!file_put_contents($cArquivo,$cDados))
{
   msg_alerta("IMPOSSIVEL CRIAR O ARQUIVO: $cArquivo");
   exit;
}
else
{
	echo <<<EOT
		<script>
		 //parent.atualizaDiv("{$r}", "{$divId}", "{$divIdRoot}");
		 parent.divRefresh("{$divId}", true);
		</script>
EOT;
}
?>
