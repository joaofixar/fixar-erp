<?php
namespace hardness;
global $g, $confUsuario;
require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
include_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');
require_once('../../sistema/confSistema.php');
$g['conf'] = new confSistema();
include $confUsuario['pathRaiz']."/hardness/include/sessao.inc";

for ($xNF=$T007_Numero_Nota_Fiscal_Inicial;$xNF<=$T007_Numero_Nota_Fiscal_Final;$xNF++)
{
    $cSql_T007=mysql_query("select *
                              from T007
                         left join C004 on C004_Id=T007_C004_Id
                             where T007_Numero_Nota_Fiscal='$xNF'
                               and T007_Serie='$T007_Serie'
                               and T007_Flag_ACP={$T007_Flag_ACP}
                               and T007_C004_Id='$Empresa_Atual'
                               order by abs(T007_Numero_Nota_Fiscal)");

    $mT007=mysql_fetch_array($cSql_T007);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       mysql_query("ROLLBACK");
       echo "Erro 5: $cErro<BR> ";
       exit;
    }

    if (mysql_num_rows($cSql_T007)==0)
    {
    
        $cClausula  = " insert into T007 (";
        $cClausula .= " T007_Flag_ACP, ";
        $cClausula .= " T007_Data_Emissao, ";
        $cClausula .= " T007_Data_Saida, ";
        $cClausula .= " T007_C004_Id, ";
        $cClausula .= " T007_Numero_Nota_Fiscal, ";
        $cClausula .= " T007_Serie, ";
        $cClausula .= " T007_Flag_Inutilizado, ";
        $cClausula .= " T007_Empresa_Usuaria_Cnpj, ";
        $cClausula .= " T007_Empresa_Usuaria_Codigo_Estado_IBGE, ";
        $cClausula .= " T007_Ambiente_NFe, ";
        $cClausula .= " T007_Flag_Contingencia ";
        $cClausula .= " ) values ( ";
        $cClausula .= " '{$T007_Flag_ACP}', ";
        $cClausula .= " current_date(), ";
        $cClausula .= " current_date(), ";
        $cClausula .= " '$Empresa_Atual', ";
        $cClausula .= " '$xNF', ";
        $cClausula .= " '$T007_Serie', ";
        $cClausula .= " 'S', ";
        $cClausula .= " '$mC004[C004_Cnpj]', ";
        $cClausula .= " '$mC004[D018_Codigo_IBGE]', ";
        $cClausula .= " '$mC004[C004_Ambiente_NFe]', ";
        $cClausula .= " 'N' ";
        $cClausula .= " ) ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro))
        {
           mysql_query("ROLLBACK");
           echo "Erro 6: $cErro<BR>$cClausula ";
           exit;
        }
    }
  
}

?>
