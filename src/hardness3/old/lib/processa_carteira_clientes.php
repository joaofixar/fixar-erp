<?php

// Cria ou inicia uma sessão
include "sessao.php";

//--  Conexão com o banco de dados
include "../../../hardness/include/conexao.inc"; // conexao.php

include "funcoes_genericas.inc";

include "super_pesquisa.class.php";

echo "<script src='ajax.js'></script>";
echo "<script src='funcoes_genericas.js'></script>";

set_time_limit(0);

$C020=mysql_query("select * from C020 where C020_C004_Id='$Empresa_Atual'");
$mC020=mysql_fetch_array($C020);

$mes = 12;
$xLast12=date('Y-m-d');
$xLast12 = new DateTime($xLast12);
$xLast12 -> modify('-'.$mes.' months');
$xLast12 = $xLast12->format('Y-m-d');

mysql_query("TRUNCATE T124;");

    mysql_query("insert into T124 (T124_D024_Id)
                           (select D024_Id
                              from D024)");

    $T007=mysql_query("select T007_D024_Id,
                              sum(T007_Valor_Total_Produtos) Total,
                              month(T007_Data_Emissao) Mes
                         from T007
                    left join D006 on D006_Id=T007_D006_Id
                        where T007_Flag_Cancelada!='S'
                          and D006_Flag_Venda_Compra_Outros='V'
                          and T007_Data_Emissao > '{$xLast12}'
                     group by T007_D024_Id,month(T007_Data_Emissao)");
                     
    $erro=mysql_error();
    
    if (!empty($erro))
    {
       msg_alerta("ERRO! (1)");
       msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
       exit;
    }

    while ($mT007=mysql_fetch_array($T007))
    {
           mysql_query("update T124 set T124_Vendas_".str_pad($mT007[Mes],2,'0',STR_PAD_LEFT)."='$mT007[Total]' where T124_D024_Id='$mT007[T007_D024_Id]' and T124_Ano='$Ano'");
    }
    
    $T003=mysql_query("select T003_D024_Id,
                              sum(T003_Valor_Total_Produtos) Total,
                              month(T003_Data_Emissao) Mes
                         from T003
                    left join D006 on D006_Id=T003_D006_Id
                        where D006_Flag_Venda_Compra_Outros='V'
                          and T003_Data_Emissao > '$xLast12'
                     group by T003_D024_Id,month(T003_Data_Emissao)");
                     
    $erro=mysql_error();
    
    if (!empty($erro))
    {
       msg_alerta("ERRO! (2)");
       msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
       exit;
    }
    
    while ($mT003=mysql_fetch_array($T003))
    {
           mysql_query("update T124 set T124_Orcamentos_".str_pad($mT003[Mes],2,'0',STR_PAD_LEFT)."='$mT003[Total]' where T124_D024_Id='$mT003[T003_D024_Id]' and T124_Ano='$Ano'");
    }
    
    $T005=mysql_query("select T005_D024_Id,
                              sum(T005_Valor_Total_Produtos) Total,
                              month(T005_Data_Emissao) Mes
                         from T005
                    left join D006 on D006_Id=T005_D006_Id
                        where T005_Data_Emissao > '$xLast12'
                          and D006_Flag_Venda_Compra_Outros='V'
                          and (T005_Flag_Status='7' or T005_Flag_Status='11')
                     group by T005_D024_Id,month(T005_Data_Emissao)");

    $erro=mysql_error();

    if (!empty($erro))
    {
       msg_alerta("ERRO! (3)");
       msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
       exit;
    }

    unset($total);
    while ($mT005=mysql_fetch_array($T005))
    {
           mysql_query("update T124 set T124_Vendas_".str_pad($mT005[Mes],2,'0',STR_PAD_LEFT)."=T124_Vendas_".str_pad($mT005[Mes],2,'0',STR_PAD_LEFT)."+$mT005[Total] where T124_D024_Id='$mT005[T005_D024_Id]' and T124_Ano='$Ano'");
    }

    $erro=mysql_error();

    if (!empty($erro))
    {
       msg_alerta("ERRO! (4)");
       msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
       exit;
    }

$Ano=date('Y');

$T124=mysql_query("select sum(T124_Vendas_01+
                                  T124_Vendas_02+
                                  T124_Vendas_03+
                                  T124_Vendas_04+
                                  T124_Vendas_05+
                                  T124_Vendas_06+
                                  T124_Vendas_07+
                                  T124_Vendas_08+
                                  T124_Vendas_09+
                                  T124_Vendas_10+
                                  T124_Vendas_11+
                                  T124_Vendas_12) Total
                         from T124");

$mTotal=mysql_fetch_array($T124);

$T124=mysql_query("select *,
                              (T124_Vendas_01+
                               T124_Vendas_02+
                               T124_Vendas_03+
                               T124_Vendas_04+
                               T124_Vendas_05+
                               T124_Vendas_06+
                               T124_Vendas_07+
                               T124_Vendas_08+
                               T124_Vendas_09+
                               T124_Vendas_10+
                               T124_Vendas_11+
                               T124_Vendas_12) Total,
                              (T124_Orcamentos_01+
                               T124_Orcamentos_02+
                               T124_Orcamentos_03+
                               T124_Orcamentos_04+
                               T124_Orcamentos_05+
                               T124_Orcamentos_06+
                               T124_Orcamentos_07+
                               T124_Orcamentos_08+
                               T124_Orcamentos_09+
                               T124_Orcamentos_10+
                               T124_Orcamentos_11+
                               T124_Orcamentos_12) Orcamentos_Total
                               from T124
                        where (T124_Vendas_01+
                               T124_Vendas_02+
                               T124_Vendas_03+
                               T124_Vendas_04+
                               T124_Vendas_05+
                               T124_Vendas_06+
                               T124_Vendas_07+
                               T124_Vendas_08+
                               T124_Vendas_09+
                               T124_Vendas_10+
                               T124_Vendas_11+
                               T124_Vendas_12+
                               T124_Orcamentos_01+
                               T124_Orcamentos_02+
                               T124_Orcamentos_03+
                               T124_Orcamentos_04+
                               T124_Orcamentos_05+
                               T124_Orcamentos_06+
                               T124_Orcamentos_07+
                               T124_Orcamentos_08+
                               T124_Orcamentos_09+
                               T124_Orcamentos_10+
                               T124_Orcamentos_11+
                               T124_Orcamentos_12)>0
                      order by (T124_Vendas_01+
                               T124_Vendas_02+
                               T124_Vendas_03+
                               T124_Vendas_04+
                               T124_Vendas_05+
                               T124_Vendas_06+
                               T124_Vendas_07+
                               T124_Vendas_08+
                               T124_Vendas_09+
                               T124_Vendas_10+
                               T124_Vendas_11+
                               T124_Vendas_12) desc");

$erro=mysql_error();

if (!empty($erro))
{
	msg_alerta("ERRO! (5)");
	msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
	exit;
}

$Curva_A=$mTotal[0]*.80;
$Curva_B=$mTotal[0]*.20;
$Curva_C=$mTotal[0]*.10;

mysql_query("update D024 set D024_Flag_Curva_ABC='D'");

$xMes=date('m')-1;
while ($mT124=mysql_fetch_array($T124))
{
	
	unset($mValores);
	if ($xMes>=1 ) $mValores[]=$mT124[T124_Vendas_01];
	if ($xMes>=2 ) $mValores[]=$mT124[T124_Vendas_02];
	if ($xMes>=3 ) $mValores[]=$mT124[T124_Vendas_03];
	if ($xMes>=4 ) $mValores[]=$mT124[T124_Vendas_04];
	if ($xMes>=5 ) $mValores[]=$mT124[T124_Vendas_05];
	if ($xMes>=6 ) $mValores[]=$mT124[T124_Vendas_06];
	if ($xMes>=7 ) $mValores[]=$mT124[T124_Vendas_07];
	if ($xMes>=8 ) $mValores[]=$mT124[T124_Vendas_08];
	if ($xMes>=9 ) $mValores[]=$mT124[T124_Vendas_09];
	if ($xMes>=10) $mValores[]=$mT124[T124_Vendas_10];
	if ($xMes>=11) $mValores[]=$mT124[T124_Vendas_11];
	if ($xMes>=12) $mValores[]=$mT124[T124_Vendas_12];
	
	sort($mValores);
	
	$Valor_Vendas_Pior_Mes=$mValores[0];
	
	rsort($mValores);
	
	$Valor_Vendas_Melhor_Mes=$mValores[0];
	
	$Frequencia=count(array_filter($mValores, function ($element) { return ($element > 0 ); } ));
	
	if ($mTotal[0]>$Curva_B)
	{
		$Curva='A';
	}
	else if ($mTotal[0]>$Curva_C)
	{
		$Curva='B';
	}
	else
	{
		if ($mT124[Total]>0)
		{
			$Curva='C';
		}
		else
		{
			$Curva='D';
		}
	}
	
	mysql_query("update T124 set T124_Vendas_Total='$mT124[Total]',
	                             T124_Orcamentos_Total='$mT124[Orcamentos_Total]',  
                                 T124_Curva_ABC='$Curva'
                           where T124_Id='$mT124[T124_Id]'");

	mysql_query("update D024 set D024_Flag_Curva_ABC='$Curva',
	                             D024_Valor_Vendas_Pior_Mes='$Valor_Vendas_Pior_Mes',
	                             D024_Valor_Vendas_Melhor_Mes='$Valor_Vendas_Melhor_Mes',
	                             D024_Flag_Frequencia='$Frequencia',
	                             D024_Data_Ultimo_Orcamento=(select T003_Data_Emissao from T003 where T003_D024_Id='$mT124[T124_D024_Id]' order by T003_Id desc limit 1)
                           where D024_Id='$mT124[T124_D024_Id]'");

	$mTotal[0]-=$mT124[Total];
}
mysql_query("update T007
          left join D024 on D024_Id=T007_D024_Id
          left join D006 on D006_Id=T007_D006_Id
                set T007_C007_Id_Devolucao=D024_C007_Vendedor_Interno
	          where D006_Flag_Venda_Compra_Outros='DV'
	            and ifnull(T007_C007_Id_Devolucao,0)<=0");
//T007_Data_Emissao like '$Mes_Ano%'
	
mysql_query("update T013
          left join D024 on D024_Id=T013_D024_Id
          left join D006 on D006_Id=T013_D006_Id
                set T013_C007_Id_Devolucao=D024_C007_Vendedor_Interno
	          where D006_Flag_Venda_Compra_Outros='DV'
	            and ifnull(T013_C007_Id_Devolucao,0)<=0");
// T013_Data_Entrada like '$Mes_Ano%'


mysql_query("update D024 set D024_D085_Id=(select max(D085_Id) from D085 where D085_D024_Id=D024_Id)");
mysql_query("update D024 set D024_Data_Ultima_Compra=ifnull((select max(T005_Data_Emissao) from T005 where T005_D024_Id=D024_Id),D024_Data_Ultima_Compra) where D024_Data_Ultima_Compra<ifnull((select max(T005_Data_Emissao) from T005 where T005_D024_Id=D024_Id),D024_Data_Ultima_Compra)");
mysql_query("update D024 set D024_Data_Ultima_Compra=ifnull((select max(T007_Data_Emissao) from T007 where T007_D024_Id=D024_Id),D024_Data_Ultima_Compra) where D024_Data_Ultima_Compra<ifnull((select max(T007_Data_Emissao) from T007 where T007_D024_Id=D024_Id),D024_Data_Ultima_Compra)");
mysql_query("update D024 set D024_Data_Ultimo=ifnull((select max(T007_Data_Emissao) from T007 where T007_D024_Id=D024_Id),D024_Data_Ultima_Compra) where D024_Data_Ultima_Compra<ifnull((select max(T007_Data_Emissao) from T007 where T007_D024_Id=D024_Id),D024_Data_Ultima_Compra)");
mysql_query("update D024 set D024_Data_Ultima_Compra='0000-00-00' where D024_Data_Ultima_Compra is null or D024_Data_Ultima_Compra=''");
mysql_query("update D024 set D024_Data_Ultimo_Orcamento='0000-00-00' where D024_Data_Ultimo_Orcamento is null or D024_Data_Ultimo_Orcamento=''");

/*
mysql_query("update D024
          left join D085 on D085_Id=D024_D085_Id  
                set D024_Flag_Comercial='I',
                    D024_Data_Inativo=curdate() 
              where (D024_Flag_Comercial!='I' or D024_Data_Inativo='0000-00-00') 
                and ifnull(D024_Data_Ativo,'0000-00-00')<curdate() - interval 3 month
                and ifnull(D024_Data_Ultima_Compra,'0000-00-00')<curdate() - interval 3 month
                and D024_Flag_Cliente='S'");
*/

mysql_query("update D024 set D024_Flag_Comercial='A'");

mysql_query("update D024
          left join C007 on C007_Id=D024_C007_Vendedor_Interno
                set D024_Flag_Comercial='I',
                    D024_Data_Inativo=curdate()
              where C007_Ativo = 'N'");

mysql_query("update D024
          left join D085 on D085_Id=D024_D085_Id
                set D024_Flag_Comercial='I',
                    D024_Data_Inativo=curdate()
              where (D024_Flag_Comercial!='I' or D024_Data_Inativo='0000-00-00')
                and ifnull(D024_Data_Ativo,'0000-00-00')<curdate() - interval 1 month
                and ifnull(D024_Data_Ultima_Compra,'0000-00-00')<curdate() - interval 1 month
                and ifnull(D024_Data_Ultimo_Orcamento,'0000-00-00')<curdate() - interval 1 month
                and ifnull(D085_Data,'0000-00-00')<curdate() - interval 1 month
                and (select T005_Id from T005 where T005_D024_Id=D024_Id and T005_Flag_Almoxarifado='S' limit 1) is null");

mysql_query("update D024 
                set D024_Classe = 'F'");

mysql_query("update D024 
                set D024_Classe = 'A' 
              where D024_Valor_Vendas_Pior_Mes>5000");

mysql_query("update D024 
                set D024_Classe = 'B' 
              where D024_Valor_Vendas_Pior_Mes>=2000  
                and D024_Valor_Vendas_Pior_Mes<5000");

mysql_query("update D024 
                set D024_Classe = 'C' 
              where D024_Valor_Vendas_Pior_Mes<2000
                and D024_Data_Ultima_Compra!='0000-00-00'");

mysql_query("update D024 
                set D024_Classe = 'D' 
              where D024_Data_Ultimo_Orcamento!='0000-00-00'
                and D024_Data_Ultima_Compra='0000-00-00'");

mysql_query("update D024 
                set D024_Classe = 'E' 
              where D024_Flag_Pre_Cadastro='S'
                and D024_D085_Id>0");

mysql_query("update D024 
                set D024_Classe = 'A' 
              where D024_Flag_Comercial='I'
                and D024_Data_Ultima_Compra!='0000-00-00'");

mysql_query("update D024 
                set D024_Classe = 'B' 
              where D024_Flag_Comercial='I'
                and D024_Data_Ultimo_Orcamento!='0000-00-00'
                and D024_Data_Ultima_Compra='0000-00-00'");

mysql_query("update D024 
                set D024_Classe = 'C' 
              where D024_Flag_Comercial='I'
                and D024_Data_Ultimo_Orcamento='0000-00-00'
                and D024_Data_Ultima_Compra='0000-00-00'");

mysql_query("update D024 
                set D024_Classe = 'D' 
              where D024_Flag_Comercial='I'
                and D024_Flag_Pre_Cadastro='S'
                and D024_Data_Ultimo_Orcamento='0000-00-00'
                and D024_Data_Ultima_Compra='0000-00-00'");

/*
if ($mC020[C020_Prazo_Dias_Inativar_Cliente]>0)
{
    $T007=mysql_query("update D024
                      set D024_Flag_Comercial='A'");

    $T007=mysql_query("update D024
                      set D024_Flag_Comercial='I'
                    where D024_Flag_Pre_Cadastro!='S'
                      and (select T007_Id
                             from T007
                        left join D006 on D006_Id=T007_D006_Id
                            where T007_D024_Id=D024_Id
                              and D006_Flag_Venda_Compra_Outros='V'
                              and datediff(now(),T007_Data_Emissao)<60 limit 1) is null");

    $T007=mysql_query("update D024
                      set D024_Flag_Comercial='I'
                    where D024_Flag_Pre_Cadastro='S'
                      and datediff(now(),D024_Data_Cadastro)>90");
}
else
{
    $T007=mysql_query("update D024
                      set D024_Flag_Comercial='A'
                    where (select T007_Id
                             from T007
                        left join D006 on D006_Id=T007_D006_Id
                            where T007_D024_Id=D024_Id
                              and D006_Flag_Venda_Compra_Outros='V'
                              and datediff(now(),T007_Data_Emissao)<60 limit 1)>0
                      and (select T005_Id
                             from T005
                        left join D006 on D006_Id=T005_D006_Id
                            where T005_D024_Id=D024_Id
                              and D006_Flag_Venda_Compra_Outros='V'
                              and (T005_Flag_Status='7' or T005_Flag_Status='11')
                              and datediff(now(),T005_Data_Emissao)<60 limit 1)>0");
}
  
                    */

$erro=mysql_error();

echo $erro;

if (!empty($erro))
{
   msg_alerta("ERRO! (6)");
   msg_alerta("ERRO:/p/p ".htmlspecialchars($erro,ENT_QUOTES));
   exit;
}

msg_alerta('Processo concluido!');

echo "<script>";
echo "top.MostrarMensagem('none');";
//echo "parent.location.replace(parent.location.href);";
echo "</script>";

?>
