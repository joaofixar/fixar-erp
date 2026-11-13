<?php
namespace hardness;
set_time_limit(0);

date_default_timezone_set('America/Sao_Paulo');
$mes = 12;
$xLast12=date('Y-m-d');
$xLast12 = new DateTime($xLast12);
$xLast12 -> modify('-'.$mes.' months');
$xLast12 = $xLast12->format('Y-m-d');

echo "Data inicial: ".$xLast12."\n";

//pega os valores dos parâmetros da curva
if(!empty($g['C031']['curvaABCclientes'])){
    $parametrosCurvaABC = explode(",", str_replace(" ", "", trim($g['C031']['curvaABCclientes'],",")));
    $pCurvaA = ($parametrosCurvaABC[0]/100);
    $pCurvaB = ($parametrosCurvaABC[1]/100);
    $pCurvaC = ($parametrosCurvaABC[2]/100);
}

mysqli_query("update D024 set D024_Flag_Curva_ABC='D'");

$produtos = array();

$T007=mysqli_query("select T007_D024_Id,
                        sum(T008_Valor_Preco_Unitario*T008_Quantidade) Total
                   from T007
              left join D006 on D006_Id=T007_D006_Id
              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
              left join T008 on T008_T007_Id=T007_Id
                  where T007_Flag_Cancelada!='S'
                    and D006_Flag_Venda_Compra_Outros='V'
                    and T007_Data_Emissao >= '{$xLast12}'
               group by T007_D024_Id
               order by sum(T008_Valor_Preco_Unitario*T008_Quantidade) desc");

$erro=mysql_error();
if (!empty($erro))
{
   msg_alerta("ERRO!");
   msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
   return false;
}

$T007Total=mysqli_query("select sum(T008_Valor_Preco_Unitario*T008_Quantidade) Total
                   from T007
              left join D006 on D006_Id=T007_D006_Id
              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
              left join T008 on T008_T007_Id=T007_Id
                  where T007_Flag_Cancelada!='S'
                    and D006_Flag_Venda_Compra_Outros='V'
                    and T007_Data_Emissao >= '{$xLast12}'");

$erro=mysql_error();
if (!empty($erro))
{
   msg_alerta("ERRO!");
   msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
   return false;
}

$mT007Total = mysql_fetch_array($T007Total);

$Curva_A=$mT007Total['Total']*$pCurvaA;
$Curva_B=$mT007Total['Total']*$pCurvaB;
$Curva_C=$mT007Total['Total']*$pCurvaC;

while ($mT007=mysql_fetch_array($T007)) {
    if ($mT007Total['Total']>$Curva_B){
        $Curva='A';
    } else if ($mT007Total['Total']>$Curva_C){
        $Curva='B';
    } else if ($mT007Total['Total']>0){
        $Curva='C';
    }else{
        $Curva='D';
    }
    //echo $mT007['D001_Codigo_Produto'].' '.$Curva.' '.$mT007['Total'].' '.$mT007Total['Total']."\n";
    $mT007Total['Total'] -= $mT007['Total'];
    mysqli_query("update D024 set D024_Flag_Curva_ABC='{$Curva}' where D024_Id='{$mT007['T007_D024_Id']}'");
}

//--------------------------------------------------------------------------------------------------------------
//calcula o valor da curva ABC pela quantidade
$T007Qtd=mysqli_query("select T007_D024_Id,
                        sum(T008_Quantidade) QtdTotal
                   from T007
              left join D006 on D006_Id=T007_D006_Id
              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
              left join T008 on T008_T007_Id=T007_Id
                  where T007_Flag_Cancelada!='S'
                    and D006_Flag_Venda_Compra_Outros='V'
                    and T007_Data_Emissao >= '{$xLast12}'
               group by T007_D024_Id
               order by sum(T008_Valor_Preco_Unitario*T008_Quantidade) desc");

$erro=mysql_error();
if (!empty($erro))
{
   msg_alerta("ERRO!");
   msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
   return false;
}

$T007TotalQtd=mysqli_query("select sum(T008_Quantidade) QtdTotal
                   from T007
              left join D006 on D006_Id=T007_D006_Id
              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
              left join T008 on T008_T007_Id=T007_Id
                  where T007_Flag_Cancelada!='S'
                    and D006_Flag_Venda_Compra_Outros='V'
                    and T007_Data_Emissao >= '{$xLast12}'");

$erro=mysql_error();
if (!empty($erro))
{
   msg_alerta("ERRO!");
   msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
   return false;
}

$mT007TotalQtd = mysql_fetch_array($T007TotalQtd);

$Curva_A=$mT007TotalQtd['QtdTotal']*$pCurvaA;
$Curva_B=$mT007TotalQtd['QtdTotal']*$pCurvaB;
$Curva_C=$mT007TotalQtd['QtdTotal']*$pCurvaC;

while ($mT007Qtd=mysql_fetch_array($T007Qtd)) {
    if ($mT007TotalQtd['QtdTotal']>$Curva_B){
        $Curva='A';
    } else if ($mT007TotalQtd['QtdTotal']>$Curva_C){
        $Curva='B';
    } else if ($mT007TotalQtd['QtdTotal']>0){
        $Curva='C';
    }else{
        $Curva='D';
    }
    //echo $mT007['D001_Codigo_Produto'].' '.$Curva.' '.$mT007['Total'].' '.$mT007Total['Total']."\n";
    $mT007TotalQtd['QtdTotal'] -= $mT007Qtd['QtdTotal'];
    mysqli_query("update D024 set D024_Flag_Curva_ABC_Qtd='{$Curva}' where D024_Id='{$mT007['T007_D024_Id']}'");
}

echo "Carteira de clientes concluido!\n";

?>
