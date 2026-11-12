<?php
if (empty($cNomeTelaFiltro))
{
    echo "Não foi definida a variavel: $cNomeTelaFiltro";
}
else
{
	$pathRaiz = $confUsuario['pathRaiz'];
    include $pathRaiz . "bibliotecas/pasta_usuario.php";

    //-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
    $cArquivoTelaFiltro=$Diretorio_Usuario."/".$cNomeTelaFiltro.".php";
    if (file_exists($cArquivoTelaFiltro))
    {
        include "$cArquivoTelaFiltro";
	    if (!empty($xTimerLimpar))
	    {
	    	$nExpirou = time()-$xTimerLimpar;
	    	if ($nExpirou>1000)
	    	{
				$mDados[]='<?php';
                $mDados[]='$xConsulta_Conteudo_Filtro_Simples="";';
                $mDados[]='$xConsulta_Clausula_Filtro="";';
                $mDados[]='$xConsulta_Ordem="'.$xConsulta_Ordem.'";';
                $mDados[]='$xConsulta_Ordem_Desc="'.$xConsulta_Ordem_Desc.'";';
                $mDados[]='$xConsulta_Order_By="'.$xConsulta_Order_By.'";';
				$mDados[]='?'.'>';
				$cDados=implode("\n",$mDados);
				if (!file_put_contents($cArquivoTelaFiltro,$cDados))
				{
				   msg_alerta("IMPOSSIVEL CRIAR O ARQUIVO: $cArquivoTelaFiltro");
				   exit;
				}
				include "$cArquivoTelaFiltro";
	    	}		
	    	
	    }
         
        $xConsulta_Conteudo_Filtro = (isset($xConsulta_Conteudo_Filtro)) ? $xConsulta_Conteudo_Filtro : false;
        $xConsulta_Sinal_Filtro = (isset($xConsulta_Sinal_Filtro)) ? $xConsulta_Sinal_Filtro : false;
        $mConteudo_Filtro=explode(',,',$xConsulta_Conteudo_Filtro);
        $mSinal=explode(',,',$xConsulta_Sinal_Filtro);
    }

    $xConsulta_Clausula_Filtro = (isset($xConsulta_Clausula_Filtro)) ? $xConsulta_Clausula_Filtro : '';
    $xConsulta_Clausula_Filtro=str_replace("{","<",$xConsulta_Clausula_Filtro);
    
}
?>
