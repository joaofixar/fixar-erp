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
if(!empty($g['C031']['curvaABCprodutos'])){
    $parametrosCurvaABC = explode(",", str_replace(" ", "", trim($g['C031']['curvaABCprodutos'],",")));
    $parametrosCurvaABC[1] += $parametrosCurvaABC[2];
    $pCurvaA = ($parametrosCurvaABC[0]/100);
    $pCurvaB = ($parametrosCurvaABC[1]/100);
    $pCurvaC = ($parametrosCurvaABC[2]/100);
}

mysqli_query("update D049 set D049_Flag_Curva_ABC='D'");

//Atualiza ultima data de classificação para alterar a Flag Classe na D001
mysqli_query("update D001
				set D001_Data_Ultimo_Movimento=(select max(T007_Data_Emissao) 
				from T007 
					left join T008 on T008_T007_Id=T007_Id
					left join D006 on D006_Id=T008_D006_Id 
					left join D009 on D009_Id=T008_D009_Id
					left join D049 on D049_Id=D009_D049_Id 
				where D049_D001_Id=D001_Id
					and D006_Flag_Venda_Compra_Outros='V'
					and T007_Flag_Cancelada!='S')");

$produtos = array();

//calcula o valor da cruva ABC pelo valor
//----------------------------------------------------------------------------------------------------------
$T007=mysql_query("select D001_Codigo_Produto,
	                    D049_Id,
                        sum(T008_Valor_Preco_Unitario*T008_Quantidade) Total
                   from T007
              left join D006 on D006_Id=T007_D006_Id
              left join T008 on T008_T007_Id=T007_Id
              left join D009 on D009_Id=T008_D009_Id
              left join D049 on D049_Id=D009_D049_Id
              left join D001 on D001_Id=D049_D001_Id
              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                  where T007_Flag_Cancelada!='S'
                    and D006_Flag_Venda_Compra_Outros='V'
                    and T007_Data_Emissao >= '{$xLast12}'
               group by D049_Id
               order by sum(T008_Valor_Preco_Unitario*T008_Quantidade) desc");

$erro=mysql_error();
if (!empty($erro))
{
   msg_alerta("ERRO!");
   msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
   return false;
}

$T007Total=mysql_query("select sum(T008_Valor_Preco_Unitario*T008_Quantidade) Total
                   from T007
              left join D006 on D006_Id=T007_D006_Id
              left join T008 on T008_T007_Id=T007_Id
              left join D009 on D009_Id=T008_D009_Id
              left join D049 on D049_Id=D009_D049_Id
              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
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
	mysqli_query("update D049 set D049_Flag_Curva_ABC='{$Curva}' where D049_Id='{$mT007['D049_Id']}'");
}

//----------------------------------------------------------------------------------------------------------
//calcula o valor da cruva ABC pela quantidade
$T007Qtd=mysql_query("select D001_Codigo_Produto,
                        D049_Id,
                        sum(T008_Quantidade) QtdTotal
                   from T007
              left join D006 on D006_Id=T007_D006_Id
              left join T008 on T008_T007_Id=T007_Id
              left join D009 on D009_Id=T008_D009_Id
              left join D049 on D049_Id=D009_D049_Id
              left join D001 on D001_Id=D049_D001_Id
              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                  where T007_Flag_Cancelada!='S'
                    and D006_Flag_Venda_Compra_Outros='V'
                    and T007_Data_Emissao >= '{$xLast12}'
               group by D049_Id
               order by sum(T008_Quantidade) desc");

$erro=mysql_error();
if (!empty($erro))
{
   msg_alerta("ERRO!");
   msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
   return false;
}

$T007TotalQtd=mysql_query("select sum(T008_Quantidade) QtdTotal
                   from T007
              left join D006 on D006_Id=T007_D006_Id
              left join T008 on T008_T007_Id=T007_Id
              left join D009 on D009_Id=T008_D009_Id
              left join D049 on D049_Id=D009_D049_Id
              left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
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

$Curva_A_Qtd=$mT007TotalQtd['QtdTotal']*$pCurvaA;
$Curva_B_Qtd=$mT007TotalQtd['QtdTotal']*$pCurvaB;
$Curva_C_Qtd=$mT007TotalQtd['QtdTotal']*$pCurvaC;

while ($mT007Qtd=mysql_fetch_array($T007Qtd)) {
    if ($mT007TotalQtd['QtdTotal']>$Curva_B_Qtd){
        $CurvaQtd='A';
    } else if ($mT007TotalQtd['QtdTotal']>$Curva_C_Qtd){
        $CurvaQtd='B';
    } else if ($mT007TotalQtd['QtdTotal']>0){
        $CurvaQtd='C';
    }else{
        $CurvaQtd='D';
    }
    //echo $mT007['D001_Codigo_Produto'].' '.$Curva.' '.$mT007['Total'].' '.$mT007Total['Total']."\n";
    $mT007TotalQtd['QtdTotal'] -= $mT007Qtd['QtdTotal'];
    mysqli_query("update D049 set D049_Flag_Curva_ABC_Qtd='{$CurvaQtd}' where D049_Id='{$mT007Qtd['D049_Id']}'");
}
//----------------------------------------------------------------------------------------------------------
//Atualiza a Classe do produto
mysqli_query("update D001 set D001_Flag_Classe='F'");
mysqli_query("update D001 set D001_Flag_Classe='A' where D001_Data_Ultimo_Movimento>=curdate() - interval 3 month");
mysqli_query("update D001 set D001_Flag_Classe='B' where D001_Flag_Classe='F' and D001_Data_Ultimo_Movimento>=curdate() - interval 6 month");
mysqli_query("update D001 set D001_Flag_Classe='C' where D001_Flag_Classe='F' and D001_Data_Ultimo_Movimento>=curdate() - interval 9 month");
mysqli_query("update D001 set D001_Flag_Classe='D' where D001_Flag_Classe='F' and D001_Data_Ultimo_Movimento>=curdate() - interval 12 month");

echo "Carteira de produtos concluido!\n";

?>
