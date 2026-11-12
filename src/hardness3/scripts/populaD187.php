<?php
namespace hardness;

$registros = [
    '99' => 'OUTROS',
    '10' => 'OUTROS INSUMOS',
    '09' => 'SERVICOS',
    '08' => 'ATIVO IMOBILIZADO',
    '07' => 'MATERIAL DE USO E CONSUMO',
    '06' => 'PRODUTO INTERMEDIARIO',
    '05' => 'SUBPRODUTO',
    '04' => 'PRODUTO ACABADO',
    '03' => 'PRODUTO EM PROCESSO',
    '02' => 'EMBALAGEM',
    '01' => 'MATERIA-PRIMA',
    '00' => 'MERCADORIA PARA REVENDA'
];


$D187 = mysql_query("select D187_Codigo FROM D187");
if(mysql_num_rows($D187) === 0)
{ 
    mysql_query("insert into `d187` (`D187_Codigo`, `D187_Descricao`) 
                values      
                    ('99','OUTROS'),
                    ('10','OUTROS INSUMOS'),
                    ('09','SERVICOS'),
                    ('08','ATIVO IMOBILIZADO'),
                    ('07','MATERIAL DE USO E CONSUMO'),
                    ('06','PRODUTO INTERMEDIARIO'),
                    ('05','SUBPRODUTO'),
                    ('04','PRODUTO ACABADO'),
                    ('03','PRODUTO EM PROCESSO'),
                    ('02','EMBALAGEM'),
                    ('01','MATERIA-PRIMA'),
                    ('00','MERCADORIA PARA REVENDA')"
    );

} else {

    $D187_values = [];

    while ($row = mysql_fetch_array($D187)) {
        $D187_values[] = $row['D187_Codigo'];
    }

    foreach($registros as $codigo => $descricao)  {    
        if(array_search($codigo, $D187_values) === false)  {            
            mysql_query("insert into `d187` (`D187_Codigo`, `D187_Descricao`) values('{$codigo}','{$descricao}')");
        }
    }
}

