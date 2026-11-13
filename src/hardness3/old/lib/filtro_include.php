<?php
if (empty($cNomeTelaFiltro))
{
    echo "N�o foi definida a variavel: $cNomeTelaFiltro";
}
else
{
    include "bibliotecas/pasta_usuario.php";

    //-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
    $cArquivoTelaFiltro=$Diretorio_Usuario."/".$cNomeTelaFiltro.".php";
    if (file_exists($cArquivoTelaFiltro))
    {
        include "$cArquivoTelaFiltro";
	    if (!empty($xTimerLimpar))
	    {
	    	$nExpirou = time()-$xTimerLimpar;
	    	if ($nExpirou>10)
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
         
        $mConteudo_Filtro=explode(',,',$xConsulta_Conteudo_Filtro);
        $mSinal=explode(',,',$xConsulta_Sinal_Filtro);
    }

    $xConsulta_Clausula_Filtro=str_replace("{","<",$xConsulta_Clausula_Filtro);
    
}
?>
