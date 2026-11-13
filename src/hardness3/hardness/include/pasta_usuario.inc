<?php
namespace hardness;
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
   echo "Banco de Dados nao definido";
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

$Diretorio_NFe=$Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe";
$URL_NFe=$Caminho_Hardness."hardness/dados_usuarios/$Banco_Dados/nfe";
if (!is_dir($Diretorio_NFe))
{
   mkdir ($Diretorio_NFe, 0777);
}

if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/orcamentos"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/orcamentos", 0777);
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml", 0777);
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/danfe"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/danfe", 0777);
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/certs"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/certs", 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/nfe/certs",$Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/certs");
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/assinado"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/assinado", 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/nfe/xml/assinado",$Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/assinado");
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/autorizado"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/autorizado", 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/nfe/xml/autorizado",$Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/autorizado");
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/pre"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/pre", 0777);
}
if (!is_dir($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/recebido"))
{
   mkdir ($Caminho_Hardness."dados_usuarios/$Banco_Dados/nfe/xml/recebido", 0777);
}

$Diretorio_Nota_Normal=$Caminho_Hardness."dados_usuarios/$Banco_Dados/notas";
if (!is_dir("$Diretorio_Nota_Normal"))
{
   mkdir ("$Diretorio_Nota_Normal", 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/notas","$Diretorio_Nota_Normal");
}

$Diretorio_NFSe=$Caminho_Hardness."dados_usuarios/$Banco_Dados/nfse";
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

$Diretorio_Imagens=$Caminho_Hardness."dados_usuarios/$Banco_Dados/imagens";
if (!is_dir($Diretorio_Imagens))
{
   mkdir ($Diretorio_Imagens, 0777);
   copiar_arquivo_hard($Caminho_Hardness."empresas/E003/imagens",$Diretorio_Imagens);
}

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
