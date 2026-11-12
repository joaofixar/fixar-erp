<?php
namespace hardness;

function personalizarGlobal() {
    global $g;
    
    /*
    Este callback permite personalizar e criar novas variáveis globais

    Exemplo criando uma nova global com o valor de um campo da tabela C007 - Usuários
    ---------------------------------------------------------------------------------
        if (!isset($_SESSION['novaVariavel'])) {
            $C007 = mysql_query("select campo from C007 where C007_Id='{$g['usuarioAtual']}'");
            $mC007 = mysql_fetch_array($C007);
            $_SESSION['novaVariavel'] = $mC007['campo'];
        }
        $g['novaVariavel'] = $_SESSION['novaVariavel'];

    Exemplo criando uma nova global com o valor de um campo da tabela C004 - Empresa
    --------------------------------------------------------------------------------
        if (!isset($_SESSION['novaVariavel'])) {
            $C004 = mysql_query("select campo from C004 where C004_Id='{$g['empresaAtual']}'");
            $mC004 = mysql_fetch_array($C004);
            $_SESSION['novaVariavel'] = $mC004['campo'];
        }
        $g['novaVariavel'] = $_SESSION['novaVariavel'];
    */

}