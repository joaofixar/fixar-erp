<?php
namespace hardness;
set_time_limit(0);

$countReceber = 0;
$countPagar = 0;

///TITULOS A RECEBER
$sqlT002 = mysql_query("SELECT T002_Id, T002_D014_Id, T002_D032_Id, D006_D014_Id, D014_D032_Id 
                        FROM T002
                        LEFT JOIN T007 ON T007_Id=T002_T007_Id
                        LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                        LEFT JOIN D014 ON D014_Id=D006_D014_Id
                        WHERE (T002_D014_Id <= 0 OR T002_D014_Id IS NULL)
                        AND D006_D014_Id > 0
                        AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)
                        GROUP BY T002_Id");
while($resT002 = mysql_fetch_array($sqlT002)){
  mysqli_query("UPDATE T002 SET T002_D014_Id = '{$resT002['D006_D014_Id']}', T002_D032_Id = '{$resT002['D014_D032_Id']}' WHERE T002_Id='{$resT002['T002_Id']}'");
  $countReceber++;
}


//Agrupados/Reparcelados
$sqlT002 = mysql_query("SELECT ativo.T002_Id, ativo.T002_D014_Id, ativo.T002_D032_Id, D006_D014_Id, D014_D032_Id 
                        FROM T002 AS inativo
                        LEFT JOIN T002 AS ativo ON inativo.T002_T002_Id_Agrupado = ativo.T002_Id
                        LEFT JOIN T007 ON T007_Id=inativo.T002_T007_Id
                        LEFT JOIN D006 ON D006_Id=T007_D006_Id 
                        LEFT JOIN D014 ON D014_Id=D006_D014_Id
                        WHERE (ativo.T002_D014_Id <= 0 OR ativo.T002_D014_Id IS NULL)
                        AND D006_D014_Id > 0
                        AND inativo.T002_T002_Id_Agrupado > 0
                        GROUP BY ativo.T002_Id");

while($resT002 = mysql_fetch_array($sqlT002)){
  mysqli_query("UPDATE T002 SET T002_D014_Id = '{$resT002['D006_D014_Id']}', T002_D032_Id = '{$resT002['D014_D032_Id']}' WHERE T002_Id='{$resT002['T002_Id']}'");
  $countReceber++;
}

$arrayT015 = array();
///TITULOS A PAGAR - CONTA
$sqlT015 = mysql_query("SELECT T015_Id, T015_D014_Id, T015_D032_Id, D006_D014_Id, D014_D032_Id
                        FROM T015
                        LEFT JOIN T013 ON T013_Id=T015_T013_Id
                        LEFT JOIN D006 ON D006_Id=T013_D006_Id 
                        LEFT JOIN D014 ON D014_Id=D006_D014_Id
                        WHERE (T015_D014_Id <= 0 OR T015_D014_Id IS NULL)
                        AND D006_D014_Id > 0
                        AND (T015_T015_Id_Agrupado<=0 OR T015_T015_Id_Agrupado IS NULL)
                        GROUP BY T015_Id");
while($resT015 = mysql_fetch_array($sqlT015)){
  mysqli_query("UPDATE T015 SET T015_D014_Id = '{$resT015['D006_D014_Id']}', T015_D032_Id = '{$resT015['D014_D032_Id']}' WHERE T015_Id='{$resT015['T015_Id']}'");
  $arrayT015[] = $resT015['T015_Id'];
}

//Agrupados/Reparcelados - CONTA
$sqlT015 = mysql_query("SELECT ativo.T015_Id, ativo.T015_D014_Id, ativo.T015_D032_Id, D006_D014_Id, D014_D032_Id
                        FROM T015 AS inativo
                        LEFT JOIN T015 AS ativo ON inativo.T015_T015_Id_Agrupado = ativo.T015_Id
                        LEFT JOIN T013 ON T013_Id=inativo.T015_T013_Id
                        LEFT JOIN D006 ON D006_Id=T013_D006_Id 
                        LEFT JOIN D014 ON D014_Id=D006_D014_Id
                        WHERE (ativo.T015_D014_Id <= 0 OR ativo.T015_D014_Id IS NULL)
                        AND D006_D014_Id > 0
                        AND inativo.T015_T015_Id_Agrupado > 0
                        GROUP BY ativo.T015_Id");
while($resT015 = mysql_fetch_array($sqlT015)){
  mysqli_query("UPDATE T015 SET T015_D014_Id = '{$resT015['D006_D014_Id']}', T015_D032_Id = '{$resT015['D014_D032_Id']}' WHERE T015_Id='{$resT015['T015_Id']}'");
  $arrayT015[] = $resT015['T015_Id'];
}

//TITULOS A PAGAR - CENTRO DE CUSTOS
$sqlT015 = mysql_query("SELECT T015_Id, T015_D073_Id, D006_D073_Id
                        FROM T015
                        LEFT JOIN T013 ON T013_Id=T015_T013_Id
                        LEFT JOIN D006 ON D006_Id=T013_D006_Id 
                        WHERE (T015_D073_Id <= 0 OR T015_D073_Id IS NULL)
                        AND D006_D073_Id > 0
                        AND (T015_T015_Id_Agrupado<=0 OR T015_T015_Id_Agrupado IS NULL)
                        GROUP BY T015_Id");
while($resT015 = mysql_fetch_array($sqlT015)){
  mysqli_query("UPDATE T015 SET T015_D073_Id = '{$resT015['D006_D073_Id']}' WHERE T015_Id='{$resT015['T015_Id']}'");
  if(!in_array($resT015['T015_Id'], $arrayT015)) { $arrayT015[] = $resT015['T015_Id']; }
}

$sqlT015 = mysql_query("SELECT ativo.T015_Id, ativo.T015_D073_Id, D006_D073_Id
                        FROM T015 AS inativo
                        LEFT JOIN T015 AS ativo ON inativo.T015_T015_Id_Agrupado = ativo.T015_Id
                        LEFT JOIN T013 ON T013_Id=inativo.T015_T013_Id
                        LEFT JOIN D006 ON D006_Id=T013_D006_Id 
                        WHERE (ativo.T015_D073_Id <= 0 OR ativo.T015_D073_Id IS NULL)
                        AND D006_D073_Id > 0
                        AND inativo.T015_T015_Id_Agrupado > 0
                        GROUP BY ativo.T015_Id");
while($resT015 = mysql_fetch_array($sqlT015)){
  mysqli_query("UPDATE T015 SET T015_D073_Id = '{$resT015['D006_D073_Id']}' WHERE T015_Id='{$resT015['T015_Id']}'");
  if(!in_array($resT015['T015_Id'], $arrayT015)) { $arrayT015[] = $resT015['T015_Id']; }
}                 


echo "<center><br /><br />Títulos atualizados com sucesso!<br /><br />Títulos a Receber: {$countReceber}<br/ >Títulos a Pagar: {$countPagar}";