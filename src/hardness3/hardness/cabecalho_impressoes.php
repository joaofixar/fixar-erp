<?php
namespace hardness;
if(isset($confUsuario['pathRaiz'])){
    $cArquivo_Cabecalho_Relatorio = $confUsuario['pathRaiz']."dados_usuarios/".$confUsuario['dbDatabase']."/cabecalho_relatorio.php";

    if(file_exists($cArquivo_Cabecalho_Relatorio)){
        $Diretorio_Imagens = "/hardness3/dados_usuarios/".$confUsuario['dbDatabase']."/imagens/logo/";
        include $cArquivo_Cabecalho_Relatorio;
    }else{
        unset($mAgrega_Dados);
        $mAgrega_Dados[]="<?php";
        $mAgrega_Dados[]="namespace hardness;";
        $mAgrega_Dados[]="//-- Cabecalho Padrao de Relatorios e Documentos da Empresa Usuaria.";
        $mAgrega_Dados[]="";

        $mAgrega_Dados[]="//-- Busca os dados da empresa usuária";
        $mAgrega_Dados[]="$"."cSql=mysqli_query(\"select 
                                                    *, 
                                                    D020_Nome_Cidade, 
                                                    D020_UF 
                                                from 
                                                    C004
                                                left join D020 on C004_D020_Id = D020_Id 
                                                where 
                                                    C004_Id='$"."Empresa_Atual'\");";
        $mAgrega_Dados[]="$"."mEmpresa=mysql_fetch_array($"."cSql);";

        $mAgrega_Dados[]="";
        $mAgrega_Dados[]="$"."pathInfo = pathinfo($"."mEmpresa[C004_Logo_Marca]);";
        $mAgrega_Dados[]="$"."logo = $"."mEmpresa[C004_Id].'.'.$"."pathInfo[extension];";

        $mAgrega_Dados[]="echo \"<table cellspacing=0 cellpading=0 border='0' align=center width=100% height=70>\";";
        $mAgrega_Dados[]="\techo \"<tr>\";";
        $mAgrega_Dados[]="\t\t$"."Arquivo_Logo=\"$"."Diretorio_Imagens$"."logo\";";

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
        $mAgrega_Dados[]="\t\t\t\t\techo \"<td><font class=fonte>$"."mEmpresa[C004_Cep] - $"."mEmpresa[D020_Nome_Cidade] - $"."mEmpresa[D020_UF]</font></td>\";";
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
        if (!file_put_contents($cArquivo_Cabecalho_Relatorio,$cAgrega_Dados)) {
            echo "Arquivo cabeçalho não pode ser criado: $cArquivo_Cabecalho_Relatorio<BR>";
        }
    }
}

?>