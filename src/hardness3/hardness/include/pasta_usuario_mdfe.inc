<?php
$Banco_Dados = $confUsuario['dbDatabase'];
$Caminho_Hardness = str_replace("hardness3","hardness",$confUsuario['pathRaiz']);
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
            if (!@copy($Caminho_Hardness."include/index.html", "$cPastaIndexDestino/index.html"))
            {
               //echo "Falha ao copiar: <b>../include/index.html</b>  para: <b>$cPastaIndexDestino/index.html</b> <BR>";
               //exit;
            }
        }
    }
}
if (empty($Banco_Dados))
{
   echo "Banco de Dados não definido";
   exit;
}


if (!is_dir($Caminho_Hardness."dados_usuarios"))
{
   mkdir ($Caminho_Hardness."dados_usuarios", 0777);
   chmod ($Caminho_Hardness."dados_usuarios", 0777);
}

$Diretorio_Empresa=$Caminho_Hardness."dados_usuarios/$Banco_Dados";

if (!is_dir($Diretorio_Empresa))
{
   mkdir ($Diretorio_Empresa, 0777);
}

if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/usuarios"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/usuarios", 0777);
}


if (!empty($Usuario_Atual))
{
   // Busca os dados do usuario
   $resultado=mysql_query("select * from C007 left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$Empresa_Atual' left join C021 on C021_Id=C019_C021_Id where C007_Id='$Usuario_Atual'");
   $mUsuario=mysql_fetch_array($resultado);

    $Diretorio_Usuario=$Caminho_Hardness."dados_usuarios/$Banco_Dados/usuarios/u_".str_repeat('0',6-strlen($Usuario_Atual)).$Usuario_Atual;

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



$Diretorio_MDFe=$Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe";
if (!is_dir($Diretorio_MDFe))
{
   mkdir ($Diretorio_MDFe, 0777);
}

if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml", 0777);
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/damdfe"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/damdfe", 0777);
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/certs"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/certs", 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/mdfe/certs",$Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/certs");
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/assinado"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/assinado", 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/mdfe/xml/assinado",$Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/assinado");
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/autorizado"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/autorizado", 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/mdfe/xml/autorizado",$Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/autorizado");
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/pre"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/pre", 0777);
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/recebido"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/mdfe/xml/recebido", 0777);
}


$Diretorio_Imagens=$Caminho_Hardness."dados_usuarios/$Banco_Dados/imagens";
if (!is_dir($Diretorio_Imagens))
{
   mkdir ($Diretorio_Imagens, 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/imagens",$Diretorio_Imagens);
}


//-- CRIA UM ARQUIVO PARA O CABECALHO PADRAO SE O MESMO AINDA NAO EXISTIR
/*
$cArquivo_Cabecalho_Relatorio=$Diretorio_Empresa."/cabecalho_relatorio.php";

if (!file_exists($cArquivo_Cabecalho_Relatorio))
{

    unset($mAgrega_Dados);

    $mAgrega_Dados[]="<?php";
    $mAgrega_Dados[]="//-- Cabecalho Padrao de Relatorios e Documentos da Empresa Usuaria.";
    $mAgrega_Dados[]="";

    $mAgrega_Dados[]="//-- Busca os dados da empresa usu�ria";
    $mAgrega_Dados[]="$"."cSql=mysql_query(\"select * from C004 where C004_Id='$"."Empresa_Atual'\");";
    $mAgrega_Dados[]="$"."mEmpresa=mysql_fetch_array($"."cSql);";

    $mAgrega_Dados[]="";

    $mAgrega_Dados[]="echo \"<table cellspacing=0 cellpading=0 border='0' align=center width=100% height=70>\";";
    $mAgrega_Dados[]="\techo \"<tr>\";";
    $mAgrega_Dados[]="\t\t//-- $"."Diretorio_Imagens � definido no arquivo include/pasta_usuario.inc";
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
       echo "Arquivo n�o pode ser criado: $cArquivo_Cabecalho_Relatorio<BR>";
    }
}
*/
$Diretorio_Documentos=$Caminho_Hardness."dados_usuarios/$Banco_Dados/documentos";
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

AdicionarIndex($Caminho_Hardness."dados_usuarios");
AdicionarIndex("$Diretorio_Empresa/usuarios");
AdicionarIndex("$Diretorio_Empresa");
AdicionarIndex("$Diretorio_MDFe");
AdicionarIndex("$Diretorio_MDFe/damdfe");
AdicionarIndex("$Diretorio_MDFe/certs");
AdicionarIndex("$Diretorio_MDFe/xml");
AdicionarIndex("$Diretorio_MDFe/xml/assinado");
AdicionarIndex("$Diretorio_MDFe/xml/autorizado");
AdicionarIndex("$Diretorio_MDFe/xml/pre");
AdicionarIndex("$Diretorio_MDFe/xml/recebido");
AdicionarIndex("$Diretorio_Usuario");

?>
