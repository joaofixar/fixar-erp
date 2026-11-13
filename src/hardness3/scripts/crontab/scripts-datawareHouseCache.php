<?php

$sql = "UPDATE D182 SET D182_Grid_Salvo = NULL";
mysql_query($sql);

$sqlUsuario = "SELECT C007_Id, C007_Primeiro_Nome, C007_Login FROM C007 WHERE C007_Ativo='S'";
$resUsuario = mysql_query($sqlUsuario);
while($rowUsuario = mysql_fetch_assoc($resUsuario)) {
    echo "Atualizando usuário {$rowUsuario['C007_Login']}\n";
    $C007_Id = $rowUsuario['C007_Id'];
    $sql = "SELECT * FROM D132 ORDER BY D132_Descricao";
    $res = mysql_query($sql);
    while ($row = mysql_fetch_assoc($res)) {
        echo "...{$row['D132_Descricao']}\n";
        $tab = "etc011FormDash1{$row['D132_Grupo']}";
        $uniqid = uniqid();
        if ($row['D132_Tipo'] == 1) {
            $url = "{$confUsuario['urlRaiz']}etc/etc011/grid/etc011GridPivot/?ajax=true&bi=visu&D132_Id={$row['D132_Id']}&noEmp=true&p=1&u={$C007_Id}&divId={$uniqid}&divIdRoot=B.I.&tab=$tab";
        } else {
            $url = "{$confUsuario['urlRaiz']}etc/etc011/ajax/visualizarIndicador/?ajax=true&D132_Id={$row['D132_Id']}&noEmp=true&p=1&u={$C007_Id}&tab=$tab";
        }
        //echo $url . "\n";
        file_get_contents($url);
    }
}
