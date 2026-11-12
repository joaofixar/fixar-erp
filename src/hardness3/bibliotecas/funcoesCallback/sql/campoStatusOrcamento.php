<?php
namespace hardness;
$status="";
if($row['status_orcamento'] == 'P'){
    $status="Pendente";
}
elseif($row['status_orcamento'] == 'F'){
    $status="Finalizado";
}
elseif($row['status_orcamento'] == 'C'){
    $status="Cancelado";
}
elseif($row['status_orcamento'] == 'V'){
    $status="Vencido";
}
elseif($row['status_orcamento'] == 'A'){
    $status="Aprovado";
}
elseif($row['status_orcamento'] == 'N'){
    $status="Negado";
}
elseif($row['status_orcamento'] == 'L'){
    $status="An?lise";
}
$row['status_orcamento'] = $status;


