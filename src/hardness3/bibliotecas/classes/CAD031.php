<?php
namespace hardness;
definirClasse('CAD031');
class CAD031_
{
    /**
     * inserirAposC004
     *
     * @param Form $form
     * @return String/bool
     */
    public function inserirAposC004($C004_Id)
    {
        global $g;
        require_once("bibliotecas/classes/FIS003.php");
        $FIS003 = new FIS003();

        // Insere os produtos (D009) da empresa criada
        $cSql_D049=mysql_query("SELECT D049_Id FROM D049
                                LEFT JOIN D009 ON D009_D049_Id=D049_Id AND D009_C004_Id={$C004_Id}
                                WHERE D009_Id IS NULL");
        while ($mD049=mysql_fetch_array($cSql_D049)) {
            mysql_query("INSERT INTO D009 (D009_C004_Id, D009_D049_Id, D009_Data_Cadastro, D009_Flag_Ativo) VALUES ('{$C004_Id}', '{$mD049['D049_Id']}', current_date(), 'S')");
        }

        $C020  = mysql_query("SELECT C020_Id FROM C020 ORDER BY C020_Id ASC LIMIT 1");
        $mC020 = mysql_fetch_array($C020);

        $C020_Id = 1;
        if($mC020['C020_Id'] > 0){
            $C020_Id = $mC020['C020_Id'];
        }

        mysql_query("INSERT INTO D053 (D053_C004_Id, D053_D018_Id, D053_UF) SELECT '{$C004_Id}', D018_Id, D018_UF FROM D018 WHERE D018_Codigo_IBGE IS NOT NULL");
        mysql_query("INSERT INTO C020 (C020_C004_Id, C020_Versao_NFe) SELECT '{$C004_Id}', C020_Versao_NFe FROM C020 WHERE C020_Id = '{$C020_Id}'");

        // Insere as NCM (D149) da empresa criada
        $cSql_D005=mysql_query("SELECT D005_Id FROM D005");
        while ($mD005=mysql_fetch_array($cSql_D005)) {
            $FIS003->cadastrarNCM($mD005['D005_Id']);
        }
        return true;
    }
}
