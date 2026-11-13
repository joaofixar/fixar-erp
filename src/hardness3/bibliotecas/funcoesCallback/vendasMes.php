<?php
namespace hardness;

function vendasMes($array){
    global $g;

    $ultimaData = date("Y-m-d", strtotime("-0".$array['todosCampos']['D009_Meses_MMF']." months"));
    
    $T001=mysql_query("SELECT SUM(T001_Quantidade) as Quantidade, concat(month(T001_Data_Lancamento),'/',year(T001_Data_Lancamento)) as Mes FROM T001 WHERE T001_D009_Id='{$array['todosCampos']['D009_Id']}' AND T001_Flag_MMF='S' AND T001_Data_Lancamento >= '{$ultimaData}' group by year(T001_Data_Lancamento),month(T001_Data_Lancamento) order by  year(T001_Data_Lancamento),month(T001_Data_Lancamento)");
    $retorno="<table style='font-size:11px;' bgcolor='silver' align='right'><tr>";
    while ($mT001=mysql_fetch_array($T001)){
        $mostraIndice=' ';
        if ($qteAnterior>0){
            $indice=((($mT001['Quantidade']-$qteAnterior)/$qteAnterior)*100);
            if ($indice<0){
                $mostraIndice="<font style='color:red'>".number_format($indice,2,',','.')."%</font>";
            } else if ($indice>0){
                $mostraIndice="<font style='color:blue'>+".number_format($indice,2,',','.')."%</font>";
            } else {
                $mostraIndice="<font>".number_format($indice,2,',','.')."%</font>";
            }           
        }
        $qteAnterior=$mT001['Quantidade'];
        $retorno.='<td align="center" bgcolor="white">'.$mT001['Mes'].'<br>'.number_format($mT001['Quantidade'],2,',','.')."<br>$mostraIndice</td>";
        
    }
    $retorno.='</tr></table>';

    return $retorno;
}
