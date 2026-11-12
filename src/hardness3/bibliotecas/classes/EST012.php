<?php
namespace hardness;

definirClasse('EST012');
class EST012_
{
	/**
     * est012AprovarCotacao
     *
     * @param int $T173_Id
     */
    public function est012AprovarCotacao($T173_Id)
    {
        global $g;
        
        mysql_query("update T173 set T173_C007_Id_Liberou_Alcada='{$g['usuarioAtual']}',
                                      T173_Data_Liberou_Alcada=now(),
                                      T173_Flag_Alcada_Status='A',
                                      T173_Valor_Liberou_Alcada=T173_Valor_Pretendido_Alcada
                                where T173_Id='{$T173_Id}'");
        
        if (mysql_error())
            return mysql_error();
        else
            return true;
    }
    
    /**
     * reprovarCotacao
     *
     * @param int $T173_Id
     * @param string T173_Motivo_Nao_Liberado
     */
    public function reprovarCotacao($T173_Id, $T173_Motivo_Nao_Liberado)
    {
        global $g;
        
        mysql_query("update T173 set T173_C007_Id_Liberou_Alcada='{$g['usuarioAtual']}',
                                      T173_Data_Liberou_Alcada=now(),
                                      T173_Flag_Alcada_Status='N',
                                      T173_Valor_Liberou_Alcada=0,
                                      T173_Motivo_Nao_Liberado='{$T173_Motivo_Nao_Liberado}'
                                where T173_Id='{$T173_Id}'");
        
        if (mysql_error())
            return mysql_error();
        else
            return true;
    }
}
