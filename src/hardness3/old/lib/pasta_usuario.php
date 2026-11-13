<?php

if (!function_exists("copiar_arquivo_hard"))
{
    function copiar_arquivo_hard($Origem,$Destino)
    {
       foreach (glob("$Origem/*") as $cPasta_Origem)
       {
            if (filetype($cPasta_Origem)=='dir')
            {
               continue;
            }

            $cArquivo_Destino = str_replace("$Origem","$Destino",$cPasta_Origem);

            //-- COPIA O ARQUIVO
            if (!@copy($cPasta_Origem, $cArquivo_Destino))
            {
               echo "Falha ao copiar: <b>$cPasta_Origem</b>  para: <b>$cArquivo_Destino</b> <BR>";
               exit;
            }
            else
            {
               echo "Arquivo copiado: $cArquivo_Destino...<hr>";
            }

       }
    }
}
if (!function_exists("AdicionarIndex"))
{

    function AdicionarIndex($cPastaIndexDestino)
    {
        if (!file_exists("$cPastaIndexDestino/index.html"))
        {
            if (!@copy("../../lib/index.html", "$cPastaIndexDestino/index.html"))
            {
               echo "Falha ao copiar: <b>../../lib/index.html</b>  para: <b>$cPastaIndexDestino/index.html</b> <BR>";
               exit;
            }
        }
    }



}
if (empty($Banco_Dados))
{
   echo "Banco de Dados nao definido";
   exit;
}


if (!is_dir("../../dados_usuarios"))
{
   mkdir ("../../dados_usuarios", 0777);
   chmod ("../../dados_usuarios", 0777);
}

$Diretorio_Empresa="../../dados_usuarios/$Banco_Dados";

if (!is_dir($Diretorio_Empresa))
{
   mkdir ($Diretorio_Empresa, 0777);
}

if (!is_dir("../../dados_usuarios/$Banco_Dados/usuarios"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/usuarios", 0777);
}


if (!empty($Usuario_Atual))
{
   // Busca os dados do usuario
   $resultado=mysql_query("select * from C007 left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$Empresa_Atual' left join C021 on C021_Id=C019_C021_Id where C007_Id='$Usuario_Atual'");
   $mUsuario=mysql_fetch_array($resultado);

    $Diretorio_Usuario="../../dados_usuarios/$Banco_Dados/usuarios/u_".str_repeat('0',6-strlen($Usuario_Atual)).$Usuario_Atual;

    if (!is_dir($Diretorio_Usuario))
    {
       mkdir ($Diretorio_Usuario, 0777);
       chmod ($Diretorio_Usuario, 0777);
    }

    if (!is_dir("$Diretorio_Usuario/arq_temp"))
    {
       mkdir ("$Diretorio_Usuario/arq_temp", 0777);
       chmod ("$Diretorio_Usuario/arq_temp", 0777);       
    }

}



$Diretorio_NFe="../../dados_usuarios/$Banco_Dados/nfe";
if (!is_dir($Diretorio_NFe))
{
   mkdir ($Diretorio_NFe, 0777);
}

if (!is_dir("../../dados_usuarios/$Banco_Dados/orcamentos"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/orcamentos", 0777);
}
if (!is_dir("../../dados_usuarios/$Banco_Dados/nfe/xml"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/nfe/xml", 0777);
}
if (!is_dir("../../dados_usuarios/$Banco_Dados/nfe/danfe"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/nfe/danfe", 0777);
}
if (!is_dir("../../dados_usuarios/$Banco_Dados/nfe/certs"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/nfe/certs", 0777);
   copiar_arquivo_hard("../empresas/E003/nfe/certs","../../dados_usuarios/$Banco_Dados/nfe/certs");
}
if (!is_dir("../../dados_usuarios/$Banco_Dados/nfe/xml/assinado"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/nfe/xml/assinado", 0777);
   copiar_arquivo_hard("../empresas/E003/nfe/xml/assinado","../../dados_usuarios/$Banco_Dados/nfe/xml/assinado");
}
if (!is_dir("../../dados_usuarios/$Banco_Dados/nfe/xml/autorizado"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/nfe/xml/autorizado", 0777);
   copiar_arquivo_hard("../empresas/E003/nfe/xml/autorizado","../../dados_usuarios/$Banco_Dados/nfe/xml/autorizado");
}
if (!is_dir("../../dados_usuarios/$Banco_Dados/nfe/xml/pre"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/nfe/xml/pre", 0777);
}
if (!is_dir("../../dados_usuarios/$Banco_Dados/nfe/xml/recebido"))
{
   mkdir ("../../dados_usuarios/$Banco_Dados/nfe/xml/recebido", 0777);
}

$Diretorio_Nota_Normal="../../dados_usuarios/$Banco_Dados/notas";
if (!is_dir("$Diretorio_Nota_Normal"))
{
   mkdir ("$Diretorio_Nota_Normal", 0777);
   copiar_arquivo_hard("../empresas/E003/notas","$Diretorio_Nota_Normal");
}

$Diretorio_NFSe="../../dados_usuarios/$Banco_Dados/nfse";
if (!is_dir($Diretorio_NFSe))
{
   mkdir ($Diretorio_NFSe, 0777);
}
if (!is_dir($Diretorio_NFSe."/xml"))
{
   mkdir ($Diretorio_NFSe."/xml", 0777);
}
if (!is_dir($Diretorio_NFSe."/xml/rps"))
{
   mkdir ($Diretorio_NFSe."/xml/rps", 0777);
}
if (!is_dir($Diretorio_NFSe."/xml/autorizado"))
{
   mkdir ($Diretorio_NFSe."/xml/autorizado", 0777);
}

$Diretorio_Imagens="../../dados_usuarios/$Banco_Dados/imagens";
if (!is_dir($Diretorio_Imagens))
{
   mkdir ($Diretorio_Imagens, 0777);
   copiar_arquivo_hard("../empresas/E003/imagens",$Diretorio_Imagens);
}


//-- CRIA UM ARQUIVO PARA O CABECALHO PADRAO SE O MESMO AINDA NAO EXISTIR

$cArquivo_Cabecalho_Relatorio=$Diretorio_Empresa."/cabecalho_relatorio.php";

if (!file_exists($cArquivo_Cabecalho_Relatorio))
{

    unset($mAgrega_Dados);

    $mAgrega_Dados[]="<?php";
    $mAgrega_Dados[]="namespace hardness;";
    $mAgrega_Dados[]="//-- Cabecalho Padrao de Relatorios e Documentos da Empresa Usuaria.";
    $mAgrega_Dados[]="";

    $mAgrega_Dados[]="//-- Busca os dados da empresa usuária";
    $mAgrega_Dados[]="$"."cSql=mysql_query(\"select * from C004 where C004_Id='$"."Empresa_Atual'\");";
    $mAgrega_Dados[]="$"."mEmpresa=mysql_fetch_array($"."cSql);";

    $mAgrega_Dados[]="";

    $mAgrega_Dados[]="echo \"<table cellspacing=0 cellpading=0 border='0' align=center width=100% height=70>\";";
    $mAgrega_Dados[]="\techo \"<tr>\";";
    $mAgrega_Dados[]="\t\t//-- $"."Diretorio_Imagens é definido no arquivo include/pasta_usuario.inc";
    $mAgrega_Dados[]="\t\t$"."Arquivo_Logo=\"$"."Diretorio_Imagens/$"."mEmpresa[C004_Logo_Marca]\";";

    $mAgrega_Dados[]="\t\techo \"<td bgcolor=white align=center valign=center width=220>\";";
    $mAgrega_Dados[]="\t\t\techo \"<img src='$"."Arquivo_Logo' align=center>\";";
    $mAgrega_Dados[]="\t\t\techo \"<BR>\";";
    $mAgrega_Dados[]="\t\t\techo \"<font class=fonte>$"."mEmpresa[C004_Observacao_Logo]</font>\";";
    $mAgrega_Dados[]="\t\techo \"</td>\";";

    $mAgrega_Dados[]="\t\techo \"<td align=center>\";";
    $mAgrega_Dados[]="\t\t\techo \"<table cellspacing=0 cellpading=0 border='0' align=center height=70>\";";
    $mAgrega_Dados[]="\t\t\t\techo \"<tr>\";";
    $mAgrega_Dados[]="\t\t\t\t\techo \"<td><font class=fonte style='font-size:12px'><b>$"."mEmpresa[C004_Nome_Empresa]</b></font></td>\";";
    $mAgrega_Dados[]="\t\t\t\techo \"</tr>\";";

    $mAgrega_Dados[]="\t\t\t\techo \"<tr>\";";
    $mAgrega_Dados[]="\t\t\t\t\techo \"<td><font class=fonte>$"."mEmpresa[C004_Logradouro], $"."mEmpresa[C004_Numero] $"."mEmpresa[C004_Complemento]</font></td>\";";
    $mAgrega_Dados[]="\t\t\t\techo \"</tr>\";";

    $mAgrega_Dados[]="\t\t\t\techo \"<tr>\";";
    $mAgrega_Dados[]="\t\t\t\t\techo \"<td><font class=fonte>$"."mEmpresa[C004_Cep] - $"."mEmpresa[C004_Cidade] - $"."mEmpresa[C004_Estado]</font></td>\";";
    $mAgrega_Dados[]="\t\t\t\techo \"</tr>\";";

    $mAgrega_Dados[]="\t\t\t\techo \"<tr>\";";
    $mAgrega_Dados[]="\t\t\t\t\techo \"<td><font class=fonte>Fone/Fax: $"."mEmpresa[C004_Telefone] Fax: $"."mEmpresa[C004_Fax]</font></td>\";";
    $mAgrega_Dados[]="\t\t\t\techo \"</tr>\";";

    $mAgrega_Dados[]="\t\t\t\techo \"<tr>\";";
    $mAgrega_Dados[]="\t\t\t\t\techo \"<td><font class=fonte>$"."mEmpresa[C004_Site]</font></td>\";";
    $mAgrega_Dados[]="\t\t\t\techo \"</tr>\";";

    $mAgrega_Dados[]="\t\t\techo \"</table>\";";
    $mAgrega_Dados[]="\t\techo \"</td>\";";
    $mAgrega_Dados[]="\techo \"</tr>\";";
    $mAgrega_Dados[]="echo \"</table>\";";
    $mAgrega_Dados[]="?".">";

    $cAgrega_Dados=implode("\n",$mAgrega_Dados);
    if (!file_put_contents($cArquivo_Cabecalho_Relatorio,$cAgrega_Dados))
    {
       echo "Arquivo não pode ser criado: $cArquivo_Cabecalho_Relatorio<BR>";
    }
}

$Diretorio_Documentos="../../dados_usuarios/$Banco_Dados/documentos";
if (!is_dir($Diretorio_Documentos))
{
   mkdir ($Diretorio_Documentos, 0777);
}
AdicionarIndex("$Diretorio_Documentos");

if ($Documento_Clientes_Id>0)
{
    $Diretorio_Documentos_Cliente=$Diretorio_Documentos."/D024_".str_repeat('0',5-strlen($Documento_Clientes_Id)).$Documento_Clientes_Id;
    if (!is_dir($Diretorio_Documentos_Cliente))
    {
       mkdir ($Diretorio_Documentos_Cliente, 0777);
    }
    AdicionarIndex("$Diretorio_Documentos_Cliente");
}

AdicionarIndex("../../dados_usuarios");
AdicionarIndex("$Diretorio_Empresa/usuarios");
AdicionarIndex("$Diretorio_Empresa");
AdicionarIndex("$Diretorio_Nota_Normal");
AdicionarIndex("$Diretorio_NFe");
AdicionarIndex("$Diretorio_NFe/danfe");
AdicionarIndex("$Diretorio_NFe/certs");
AdicionarIndex("$Diretorio_NFe/xml");
AdicionarIndex("$Diretorio_NFe/xml/assinado");
AdicionarIndex("$Diretorio_NFe/xml/autorizado");
AdicionarIndex("$Diretorio_NFe/xml/pre");
AdicionarIndex("$Diretorio_NFe/xml/recebido");
AdicionarIndex("$Diretorio_Usuario");

?>
