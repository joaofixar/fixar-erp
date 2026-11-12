<?php

$resultado=mysql_query("select * from C020 where C020_C004_Id='$Empresa_Atual'");
$mC020=mysql_fetch_array($resultado);

$resultado=mysql_query("select * from C007 where C007_Id='$Usuario_Atual'");
$mC007=mysql_fetch_array($resultado);

$resultado=mysql_query("select * from C004 left join D020 on D020_Id=C004_D020_Id left join D018 on D018_Id=C004_D018_Id where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($resultado);

$C021=mysql_query("select C021_Nome_Perfil from C021 where C021_Id='$Perfil_Usuario'");
$mC021=mysql_fetch_array($C021);

?>

